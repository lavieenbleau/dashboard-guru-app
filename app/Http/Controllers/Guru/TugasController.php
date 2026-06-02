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
use App\Models\PostComment;
use App\Models\PostChildComment;
use Illuminate\Support\Str;

class TugasController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::with('product')->findOrFail($serial);
        $lessonIds = json_decode($serial->product->lesson_id ?? '[]', true) ?? [];
        
        // Get lessons that belong to this guru's serial product and have category MATERI (1)
        $lessons = Lesson::whereIn('id', $lessonIds)
            ->where('category', Lesson::CATEGORY_MATERI)
            ->with('mapel')
            ->get();

        return view('guru.tugas.index', compact('serial', 'lessons'));
    }

    public function listByLesson($serial, $lesson)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        
        // Get all posts (tugas) for this lesson and serial
        $tugas = Post::where('serial_id', $serial->id)
            ->where('category', 'like', '%"lesson_id":' . $lesson->id . '%')
            ->where('is_task', 1)
            ->latest()
            ->get();

        return view('guru.tugas.list', compact('serial', 'lesson', 'tugas'));
    }

    public function create($serial, $lesson)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $classrooms = Classroom::where('serial_id', $serial->id)->orderBy('name')->get();

        return view('guru.tugas.create', compact('serial', 'lesson', 'classrooms'));
    }

    public function store(Request $request, $serial, $lesson)
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
        $lesson = Lesson::findOrFail($lesson);
        
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
            'mapel_id' => $lesson->mapel_id,
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title) . '-' . time(),
            'link' => $request->link,
            'attachment' => $attachmentPath,
            'due_date' => $request->deadline,
            'category' => ['lesson_id' => $lesson->id],
            'is_task' => 1,
        ]);

        return redirect()->route('guru.tugas.mapel', [$serial->id, $lesson->id])
            ->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function show($serial, $lesson, $id)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $task = Post::with(['comments.user', 'comments.student', 'comments.replies.user', 'comments.replies.student'])->findOrFail($id);

        return view('guru.tugas.show', compact('serial', 'lesson', 'task'));
    }

    public function edit($serial, $lesson, $id)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $task = Post::findOrFail($id);
        $classrooms = Classroom::where('serial_id', $serial->id)->orderBy('name')->get();
        
        $sharedClasses = $classrooms->pluck('id')->toArray();

        return view('guru.tugas.edit', compact('serial', 'lesson', 'task', 'classrooms', 'sharedClasses'));
    }

    public function update(Request $request, $serial, $lesson, $id)
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
        $lesson = Lesson::findOrFail($lesson);
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
            'due_date' => $request->deadline,
            'attachment' => $attachmentPath,
        ]);

        return redirect()->route('guru.tugas.mapel', [$serial->id, $lesson->id])
            ->with('success', 'Tugas berhasil diupdate!');
    }

    public function destroy($serial, $lesson, $id)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $task = Post::where('is_task', 1)->findOrFail($id);
        
        // Delete attachment file if exists
        if ($task->attachment && \Storage::disk('public')->exists($task->attachment)) {
            \Storage::disk('public')->delete($task->attachment);
        }
        
        $task->forceDelete();

        return redirect()->route('guru.tugas.mapel', [$serial->id, $lesson->id])
            ->with('success', 'Tugas berhasil dihapus!');
    }

    // Store comment
    public function storeComment(Request $request, $serial, $lesson, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $task = Post::findOrFail($id);
        
        PostComment::create([
            'post_id' => $task->id,
            'user_id' => auth()->id(),
            'student_id' => null,
            'message' => $request->message,
            'code' => Str::random(10),
            'is_user' => 1, // Guru
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    // Store reply to comment
    public function storeReply(Request $request, $serial, $lesson, $id, $commentId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $comment = PostComment::findOrFail($commentId);
        
        PostChildComment::create([
            'post_comment_id' => $comment->id,
            'user_id' => auth()->id(),
            'student_id' => null,
            'message' => $request->message,
            'is_user' => 1, // Guru
        ]);

        return back()->with('success', 'Balasan berhasil ditambahkan!');
    }

    // Delete comment
    public function deleteComment($serial, $lesson, $id, $commentId)
    {
        $comment = PostComment::where('id', $commentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        // Delete all replies first
        $comment->replies()->delete();
        
        // Delete comment
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus!');
    }

    // Delete reply
    public function deleteReply($serial, $lesson, $id, $replyId)
    {
        $reply = PostChildComment::where('id', $replyId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        $reply->delete();

        return back()->with('success', 'Balasan berhasil dihapus!');
    }
}