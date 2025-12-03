<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serial;
use App\Models\OnlineMeeting;
use App\Models\Classroom;
use Illuminate\Support\Facades\DB;

class OnlineClassController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get upcoming and past meetings
        $upcomingMeetings = OnlineMeeting::where('serial_id', $serial->id)
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->with('classroom')
            ->get();
            
        $pastMeetings = OnlineMeeting::where('serial_id', $serial->id)
            ->where('end_time', '<', now())
            ->orderBy('start_time', 'desc')
            ->with('classroom')
            ->limit(10)
            ->get();
        
        return view('guru.online-class.index', compact('serial', 'upcomingMeetings', 'pastMeetings'));
    }
    
    public function create($serial)
    {
        $serial = Serial::findOrFail($serial);
        $classrooms = Classroom::where('serial_id', $serial->id)->get();
        
        return view('guru.online-class.create', compact('serial', 'classrooms'));
    }
    
    public function store(Request $request, $serial)
    {
        $request->validate([
            'title' => 'required|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'description' => 'nullable|string',
            'meeting_link' => 'required|url',
            'meeting_code' => 'nullable|string|max:50',
            'platform' => 'required|in:zoom,google-meet,teams,other',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);
        
        $serial = Serial::findOrFail($serial);
        
        OnlineMeeting::create([
            'serial_id' => $serial->id,
            'classroom_id' => $request->classroom_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'meeting_link' => $request->meeting_link,
            'meeting_code' => $request->meeting_code,
            'platform' => $request->platform,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'upcoming',
        ]);
        
        return redirect()->route('guru.onlineclass', $serial->id)
            ->with('success', 'Jadwal online class berhasil ditambahkan!');
    }
    
    public function edit($serial, $id)
    {
        $serial = Serial::findOrFail($serial);
        $meeting = OnlineMeeting::findOrFail($id);
        $classrooms = Classroom::where('serial_id', $serial->id)->get();
        
        return view('guru.online-class.edit', compact('serial', 'meeting', 'classrooms'));
    }
    
    public function update(Request $request, $serial, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'description' => 'nullable|string',
            'meeting_link' => 'required|url',
            'meeting_code' => 'nullable|string|max:50',
            'platform' => 'required|in:zoom,google-meet,teams,other',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);
        
        $meeting = OnlineMeeting::findOrFail($id);
        $meeting->update([
            'classroom_id' => $request->classroom_id,
            'title' => $request->title,
            'description' => $request->description,
            'meeting_link' => $request->meeting_link,
            'meeting_code' => $request->meeting_code,
            'platform' => $request->platform,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);
        
        return redirect()->route('guru.onlineclass', $serial)
            ->with('success', 'Jadwal online class berhasil diperbarui!');
    }
    
    public function destroy($serial, $id)
    {
        $meeting = OnlineMeeting::findOrFail($id);
        $meeting->delete();
        
        return redirect()->route('guru.onlineclass', $serial)
            ->with('success', 'Jadwal online class berhasil dihapus!');
    }
}
