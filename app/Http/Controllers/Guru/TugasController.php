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
        
        $mapels = Mapel::whereHas('lessons', function($query) use ($lessonIds) {
            $query->whereIn('id', $lessonIds)
                  ->where('category', Lesson::CATEGORY_MATERI);
        })->withCount(['lessons' => function($query) use ($lessonIds) {
            $query->whereIn('id', $lessonIds)
                  ->where('category', Lesson::CATEGORY_MATERI);
        }])->orderBy('name')->get();

        return view('guru.tugas.index-mapel', compact('serial', 'mapels'));
    }
    
    public function mapel($serial, $mapel_id)
    {
        $serial = Serial::with('product')->findOrFail($serial);
        $lessonIds = json_decode($serial->product->lesson_id ?? '[]', true) ?? [];
        $mapel = Mapel::findOrFail($mapel_id);
        
        $lessons = Lesson::whereIn('id', $lessonIds)
            ->where('mapel_id', $mapel_id)
            ->where('category', Lesson::CATEGORY_MATERI)
            ->with('mapel')
            ->orderBy('name')
            ->get();

        return view('guru.tugas.index', compact('serial', 'mapel', 'lessons'));
    }

    public function listByLesson($serial, $lesson)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        
        // Get all posts (tugas) for this lesson and serial
        $tugas = Post::where('serial_id', $serial->id)
            ->where('category', 'like', '%"lesson_id":' . $lesson->id . '%')
            ->where('is_task', 1)
            ->with('classroom')
            ->latest()
            ->get();
            
        // Group tasks by group_id or title
        $groupedTugas = $tugas->groupBy(function($post) {
            $cat = is_string($post->category) ? json_decode($post->category, true) : ($post->category ?? []);
            return $cat['group_id'] ?? $post->title;
        })->map(function($group) {
            $master = $group->first();
            $master->shared_classrooms = $group->pluck('classroom')->filter();
            $master->all_ids = $group->pluck('id')->toArray();
            return $master;
        });
        
        $tugas = $groupedTugas->values();

        $classrooms = Classroom::where('serial_id', $serial->id)->orderBy('name')->get();

        return view('guru.tugas.list', compact('serial', 'lesson', 'tugas', 'classrooms'));
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
            'classroom_ids' => 'required|array|min:1',
            'classroom_ids.*' => [
                'required',
                \Illuminate\Validation\Rule::exists('classrooms', 'id')->where(function ($query) use ($serial) {
                    $query->where('serial_id', $serial);
                }),
            ],
            'title' => 'required|max:255',
            'description' => 'nullable',
            'link' => 'nullable|url',
            'deadline' => 'nullable|date',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png',
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
        $groupId = uniqid('task_');
        
        foreach ($request->classroom_ids as $classroomId) {
            Post::create([
                'serial_id' => $serial->id,
                'classroom_id' => $classroomId,
                'user_id' => auth()->id(),
                'mapel_id' => $lesson->mapel_id,
                'title' => $request->title,
                'description' => $request->description,
                'slug' => Str::slug($request->title) . '-' . time() . '-' . $classroomId,
                'link' => $request->link,
                'attachment' => $attachmentPath,
                'due_date' => $request->deadline,
                'category' => ['lesson_id' => $lesson->id, 'group_id' => $groupId],
                'is_task' => 1,
            ]);
        }

        return redirect()->route('guru.tugas.mapel', [$serial->id, $lesson->id])
            ->with('success', 'Tugas berhasil dibuat dan didistribusikan ke ' . count($request->classroom_ids) . ' kelas');
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
        
        $cat = is_string($task->category) ? json_decode($task->category, true) : ($task->category ?? []);
        $groupId = $cat['group_id'] ?? null;
        if ($groupId) {
            $sharedClasses = Post::where('serial_id', $serial->id)->where('category', 'like', '%"group_id":"' . $groupId . '"%')->where('is_task', 1)->pluck('classroom_id')->toArray();
        } else {
            $sharedClasses = Post::where('serial_id', $serial->id)->where('title', $task->title)->where('is_task', 1)->pluck('classroom_id')->toArray();
        }

        return view('guru.tugas.edit', compact('serial', 'lesson', 'task', 'classrooms', 'sharedClasses'));
    }

    public function update(Request $request, $serial, $lesson, $id)
    {
        $request->validate([
            'classroom_ids' => 'required|array|min:1',
            'classroom_ids.*' => [
                'required',
                \Illuminate\Validation\Rule::exists('classrooms', 'id')->where(function ($query) use ($serial) {
                    $query->where('serial_id', $serial);
                }),
            ],
            'title' => 'required|max:255',
            'description' => 'nullable',
            'link' => 'nullable|url',
            'deadline' => 'nullable|date',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png',
            'remove_attachment' => 'nullable|boolean',
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
        
        // Get all related tasks
        $cat = is_string($task->category) ? json_decode($task->category, true) : ($task->category ?? []);
        $groupId = $cat['group_id'] ?? null;
        
        if ($groupId) {
            $allTasks = Post::where('serial_id', $serial->id)->where('category', 'like', '%"group_id":"' . $groupId . '"%')->where('is_task', 1)->get();
        } else {
            $allTasks = Post::where('serial_id', $serial->id)->where('title', $task->title)->where('is_task', 1)->get();
            $groupId = uniqid('task_');
        }

        $existingClassroomIds = $allTasks->pluck('classroom_id')->toArray();
        $newClassroomIds = $request->classroom_ids;

        // Update existing tasks
        foreach ($allTasks as $t) {
            if (in_array($t->classroom_id, $newClassroomIds)) {
                $tCategory = is_string($t->category) ? json_decode($t->category, true) : ($t->category ?? []);
                $tCategory['group_id'] = $groupId;
                $t->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'link' => $request->link,
                    'due_date' => $request->deadline,
                    'attachment' => $attachmentPath,
                    'category' => $tCategory,
                ]);
            } else {
                // Remove if classroom no longer selected
                $t->forceDelete();
            }
        }

        // Create for new classrooms
        $classroomsToAdd = array_diff($newClassroomIds, $existingClassroomIds);
        foreach ($classroomsToAdd as $classroomId) {
            Post::create([
                'serial_id' => $serial->id,
                'classroom_id' => $classroomId,
                'user_id' => auth()->id(),
                'mapel_id' => $lesson->mapel_id,
                'title' => $request->title,
                'description' => $request->description,
                'slug' => Str::slug($request->title) . '-' . time() . '-' . $classroomId,
                'link' => $request->link,
                'attachment' => $attachmentPath,
                'due_date' => $request->deadline,
                'category' => ['lesson_id' => $lesson->id, 'group_id' => $groupId],
                'is_task' => 1,
            ]);
        }

        return redirect()->route('guru.tugas.mapel', [$serial->id, $lesson->id])
            ->with('success', 'Tugas berhasil diperbarui untuk ' . count($request->classroom_ids) . ' kelas');
    }

    public function destroy($serial, $lesson, $id)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $task = Post::where('is_task', 1)->findOrFail($id);
        
        $cat = is_string($task->category) ? json_decode($task->category, true) : ($task->category ?? []);
        $groupId = $cat['group_id'] ?? null;
        
        if ($groupId) {
            $allTasks = Post::where('serial_id', $serial->id)->where('category', 'like', '%"group_id":"' . $groupId . '"%')->where('is_task', 1)->get();
        } else {
            $allTasks = Post::where('serial_id', $serial->id)->where('title', $task->title)->where('is_task', 1)->get();
        }

        foreach ($allTasks as $t) {
            // Delete attachment file if exists
            if ($t->attachment && \Storage::disk('public')->exists($t->attachment)) {
                \Storage::disk('public')->delete($t->attachment);
            }
            $t->forceDelete();
        }

        return redirect()->route('guru.tugas.mapel', [$serial->id, $lesson->id])
            ->with('success', 'Tugas berhasil dihapus!');
    }

    public function updateClassroom(Request $request, $serial, $lesson, $id)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $task = Post::where('is_task', 1)->findOrFail($id);
        
        $request->validate([
            'classroom_ids' => 'required|array|min:1',
            'classroom_ids.*' => [
                'required',
                \Illuminate\Validation\Rule::exists('classrooms', 'id')->where(function ($query) use ($serial) {
                    $query->where('serial_id', $serial->id);
                }),
            ],
        ]);

        $cat = is_string($task->category) ? json_decode($task->category, true) : ($task->category ?? []);
        $groupId = $cat['group_id'] ?? null;
        
        if ($groupId) {
            $allTasks = Post::where('serial_id', $serial->id)->where('category', 'like', '%"group_id":"' . $groupId . '"%')->where('is_task', 1)->get();
        } else {
            $allTasks = Post::where('serial_id', $serial->id)->where('title', $task->title)->where('is_task', 1)->get();
            $groupId = uniqid('task_');
        }

        $existingClassroomIds = $allTasks->pluck('classroom_id')->toArray();
        $newClassroomIds = $request->classroom_ids;

        foreach ($allTasks as $t) {
            if (in_array($t->classroom_id, $newClassroomIds)) {
                $tCategory = is_string($t->category) ? json_decode($t->category, true) : ($t->category ?? []);
                $tCategory['group_id'] = $groupId;
                $t->update(['category' => $tCategory]);
            } else {
                $t->forceDelete();
            }
        }

        $classroomsToAdd = array_diff($newClassroomIds, $existingClassroomIds);
        foreach ($classroomsToAdd as $classroomId) {
            Post::create([
                'serial_id' => $task->serial_id,
                'classroom_id' => $classroomId,
                'user_id' => $task->user_id,
                'mapel_id' => $task->mapel_id,
                'title' => $task->title,
                'description' => $task->description,
                'slug' => Str::slug($task->title) . '-' . time() . '-' . $classroomId,
                'link' => $task->link,
                'attachment' => $task->attachment,
                'due_date' => $task->due_date,
                'category' => ['lesson_id' => $lesson->id, 'group_id' => $groupId],
                'is_task' => 1,
            ]);
        }

        return back()->with('success', 'Distribusi tugas berhasil diperbarui ke ' . count($request->classroom_ids) . ' kelas');
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
        $comment = PostComment::where('id', $commentId)->firstOrFail();
        
        // Delete all replies first
        $comment->replies()->delete();
        
        // Delete comment
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus!');
    }

    // Delete reply
    public function deleteReply($serial, $lesson, $id, $replyId)
    {
        $reply = PostChildComment::where('id', $replyId)->firstOrFail();
        
        $reply->delete();

        return back()->with('success', 'Balasan berhasil dihapus!');
    }
}