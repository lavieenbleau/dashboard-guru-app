<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serial;
use App\Models\Mapel;
use App\Models\Lesson;
use App\Models\Theme;
use App\Models\Subtheme;
use App\Models\Post;
use App\Models\Classroom;
use Illuminate\Support\Str;

class TugasController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get all mapels
        $mapels = Mapel::all();

        return view('guru.tugas.index', compact('serial', 'mapels'));
    }

    public function listByMapel($serial, $mapel)
    {
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        
        // Get all posts (tugas) for this mapel and serial
        $tugas = Post::where('serial_id', $serial->id)
            ->where('mapel_id', $mapel->id)
            ->where('is_task', 1)
            ->latest()
            ->get();

        return view('guru.tugas.list', compact('serial', 'mapel', 'tugas'));
    }

    public function create($serial, $mapel)
    {
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        $classrooms = Classroom::where('serial_id', $serial->id)->orderBy('name')->get();

        return view('guru.tugas.create', compact('serial', 'mapel', 'classrooms'));
    }

    public function store(Request $request, $serial, $mapel)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'link' => 'nullable|url',
            'deadline' => 'nullable|date',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png',
            'classrooms' => 'nullable|array',
            'classrooms.*' => 'exists:classrooms,id',
        ]);
        
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        
        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('tugas', $filename, 'public');
        }
        
        // Create tugas post
        Post::create([
            'serial_id' => $serial->id,
            'user_id' => auth()->id(),
            'mapel_id' => $mapel->id,
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title) . '-' . time(),
            'link' => $request->link,
            'attachment' => $attachmentPath,
            'deadline' => $request->deadline,
            'category' => null,
            'shared_to_classes' => $request->classrooms,
            'is_task' => 1,
        ]);

        return redirect()->route('guru.tugas.mapel', [$serial->id, $mapel->id])
            ->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function show($serial, $mapel, $id)
    {
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        $task = Post::findOrFail($id);

        return view('guru.tugas.show', compact('serial', 'mapel', 'task'));
    }

    public function edit($serial, $mapel, $id)
    {
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        $task = Post::findOrFail($id);
        $classrooms = Classroom::where('serial_id', $serial->id)->orderBy('name')->get();
        
        $sharedClasses = $task->shared_to_classes ?? [];

        return view('guru.tugas.edit', compact('serial', 'mapel', 'task', 'classrooms', 'sharedClasses'));
    }

    public function update(Request $request, $serial, $mapel, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'link' => 'nullable|url',
            'deadline' => 'nullable|date',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png',
            'remove_attachment' => 'nullable|boolean',
            'classrooms' => 'nullable|array',
            'classrooms.*' => 'exists:classrooms,id',
        ]);

        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        $task = Post::findOrFail($id);
        
        // Handle attachment
        $attachmentPath = $task->attachment;
        
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($attachmentPath && \Storage::disk('public')->exists($attachmentPath)) {
                \Storage::disk('public')->delete($attachmentPath);
            }
            
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('tugas', $filename, 'public');
        } elseif ($request->remove_attachment) {
            // Remove attachment if requested
            if ($attachmentPath && \Storage::disk('public')->exists($attachmentPath)) {
                \Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = null;
        }
        
        // Update task
        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'deadline' => $request->deadline,
            'attachment' => $attachmentPath,
            'shared_to_classes' => $request->classrooms,
        ]);

        return redirect()->route('guru.tugas.mapel', [$serial->id, $mapel->id])
            ->with('success', 'Tugas berhasil diupdate!');
    }

    public function destroy($serial, $mapel, $id)
    {
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        $task = Post::where('is_task', 1)->findOrFail($id);
        
        // Delete attachment file if exists
        if ($task->attachment && \Storage::disk('public')->exists($task->attachment)) {
            \Storage::disk('public')->delete($task->attachment);
        }
        
        $task->delete();

        return redirect()->route('guru.tugas.mapel', [$serial->id, $mapel->id])
            ->with('success', 'Tugas berhasil dihapus!');
    }
}