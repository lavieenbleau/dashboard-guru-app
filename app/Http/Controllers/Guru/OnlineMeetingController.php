<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serial;
use App\Models\Mapel;
use App\Models\Classroom;
use App\Models\OnlineMeeting;

class OnlineMeetingController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get all meetings for this serial
        $meetings = OnlineMeeting::where('serial_id', $serial->id)
            ->where('user_id', auth()->id())
            ->with(['classroom'])
            ->orderBy('start_time', 'desc')
            ->get();
        
        // Separate by status
        $upcomingMeetings = $meetings->where('status', 'upcoming')->sortBy('start_time');
        $ongoingMeetings = $meetings->where('status', 'live');
        $endedMeetings = $meetings->whereIn('status', ['ended', 'cancelled']);
        
        // Get classrooms and mapels for quick create
        $classrooms = Classroom::where('serial_id', $serial->id)->get();
        $mapels = Mapel::all();
        
        return view('guru.meeting.index', compact('serial', 'upcomingMeetings', 'ongoingMeetings', 'endedMeetings', 'classrooms', 'mapels'));
    }

    public function quickStart(Request $request, $serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get first classroom from serial if not provided
        $classroomId = $request->classroom_id;
        if (!$classroomId) {
            $firstClassroom = Classroom::where('serial_id', $serial->id)->first();
            if (!$firstClassroom) {
                return back()->with('error', 'Belum ada kelas untuk membuat meeting!');
            }
            $classroomId = $firstClassroom->id;
        }
        
        $request->validate([
            'title' => 'required|max:255',
            'mapel_id' => 'nullable|exists:mapels,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'duration' => 'nullable|integer|min:15|max:480', // 15 min - 8 hours
        ]);

        $duration = (int)($request->duration ?? 60); // Default 60 minutes, cast to int
        $now = now();
        
        // Generate meeting code
        $meetingCode = OnlineMeeting::generateMeetingCode();
        
        // Generate Jitsi meeting link
        $meetingLink = 'https://meet.jit.si/' . $meetingCode;
        
        // Create instant meeting
        $meeting = OnlineMeeting::create([
            'serial_id' => $serial->id,
            'user_id' => auth()->id(),
            'classroom_id' => $classroomId,
            'mapel_id' => $request->mapel_id,
            'title' => $request->title,
            'description' => 'Instant meeting - ' . $now->format('d M Y H:i'),
            'meeting_code' => $meetingCode,
            'meeting_link' => $meetingLink,
            'platform' => 'jitsi',
            'start_time' => $now,
            'end_time' => $now->copy()->addMinutes($duration),
            'status' => 'live', // Langsung live
            'room_id' => $meetingCode,
            'is_internal' => true,
        ]);

        // Redirect langsung ke meeting room
        return redirect()->route('guru.meeting.join', [$serial->id, $meeting->id]);
    }

    public function create($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get classrooms and mapels
        $classrooms = Classroom::where('serial_id', $serial->id)->get();
        $mapels = Mapel::all();
        
        return view('guru.meeting.create', compact('serial', 'classrooms', 'mapels'));
    }

    public function store(Request $request, $serial)
    {
        $serial = Serial::findOrFail($serial);
        
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'mapel_id' => 'nullable|exists:mapels,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'platform' => 'required|in:jitsi,zoom,gmeet,other',
            'meeting_link' => 'nullable|url',
        ]);

        // Generate meeting code for Jitsi
        $meetingCode = OnlineMeeting::generateMeetingCode();
        
        // For Jitsi, room_id is the meeting code
        $roomId = $request->platform === 'jitsi' ? $meetingCode : null;

        OnlineMeeting::create([
            'serial_id' => $serial->id,
            'user_id' => auth()->id(),
            'classroom_id' => $request->classroom_id,
            'mapel_id' => $request->mapel_id,
            'title' => $request->title,
            'description' => $request->description,
            'meeting_code' => $meetingCode,
            'meeting_link' => $request->meeting_link,
            'platform' => $request->platform,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'upcoming',
            'room_id' => $roomId,
            'is_internal' => $request->platform === 'jitsi',
        ]);

        return redirect()->route('guru.meeting', $serial->id)
            ->with('success', 'Meeting berhasil dijadwalkan!');
    }

    public function show($serial, $id)
    {
        $serial = Serial::findOrFail($serial);
        $meeting = OnlineMeeting::with(['classroom', 'user'])->findOrFail($id);
        
        // Check authorization
        if ($meeting->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        return view('guru.meeting.show', compact('serial', 'meeting'));
    }

    public function join($serial, $id)
    {
        $serial = Serial::findOrFail($serial);
        $meeting = OnlineMeeting::findOrFail($id);
        
        // Check if meeting is active
        if (!$meeting->isActive()) {
            return redirect()->route('guru.meeting.show', [$serial->id, $meeting->id])
                ->with('error', 'Meeting belum dimulai atau sudah berakhir!');
        }
        
        // Update status to live if upcoming
        if ($meeting->status === 'upcoming') {
            $meeting->update(['status' => 'live']);
        }
        
        // Get user name for Jitsi
        $userName = auth()->user()->name;
        $userEmail = auth()->user()->email;
        
        return view('guru.meeting.join', compact('serial', 'meeting', 'userName', 'userEmail'));
    }

    public function edit($serial, $id)
    {
        $serial = Serial::findOrFail($serial);
        $meeting = OnlineMeeting::findOrFail($id);
        
        // Check authorization
        if ($meeting->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $classrooms = Classroom::where('serial_id', $serial->id)->get();
        $mapels = Mapel::all();
        
        return view('guru.meeting.edit', compact('serial', 'meeting', 'classrooms', 'mapels'));
    }

    public function update(Request $request, $serial, $id)
    {
        $meeting = OnlineMeeting::findOrFail($id);
        
        // Check authorization
        if ($meeting->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'mapel_id' => 'nullable|exists:mapels,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'meeting_link' => 'nullable|url',
        ]);

        $meeting->update([
            'classroom_id' => $request->classroom_id,
            'mapel_id' => $request->mapel_id,
            'title' => $request->title,
            'description' => $request->description,
            'meeting_link' => $request->meeting_link,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('guru.meeting.show', [$serial, $meeting->id])
            ->with('success', 'Meeting berhasil diupdate!');
    }

    public function destroy($serial, $id)
    {
        $meeting = OnlineMeeting::findOrFail($id);
        
        // Check authorization
        if ($meeting->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $meeting->delete();

        return redirect()->route('guru.meeting', $serial)
            ->with('success', 'Meeting berhasil dihapus!');
    }

    public function end($serial, $id)
    {
        $meeting = OnlineMeeting::findOrFail($id);
        
        // Check authorization
        if ($meeting->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $meeting->update(['status' => 'ended']);

        return redirect()->route('guru.meeting.show', [$serial, $meeting->id])
            ->with('success', 'Meeting telah diakhiri!');
    }
}
