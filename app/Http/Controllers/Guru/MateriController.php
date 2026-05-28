<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Serial;
use App\Models\Mapel;
use App\Models\Theme;
use App\Models\Subtheme;
use App\Models\Post;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Classroom;
use App\Models\PostComment;
use App\Models\PostChildComment;

class MateriController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::findOrFail($serial);

        return view('guru.materi.index', compact('serial'));
    }

    // Materi dari Admin
    public function admin($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get all mapels that have admin materials (category = 1)
        $mapels = Mapel::whereHas('lessons', function($query) {
            $query->where('category', Lesson::CATEGORY_MATERI);
        })->get();

        return view('guru.materi.admin', compact('serial', 'mapels'));
    }

    // Show admin lessons for a mapel
    public function adminLessons($serial, $mapel)
    {
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        
        // Get all admin lessons for this mapel with classrooms relationship
        $lessons = Lesson::where('mapel_id', $mapel->id)
            ->where('category', Lesson::CATEGORY_MATERI)
            ->with('classrooms')
            ->get();

        return view('guru.materi.admin-lessons', compact('serial', 'mapel', 'lessons'));
    }

    // Share admin lesson to classrooms
    public function shareAdminLesson(Request $request, $serial, $lessonId)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lessonId);
        
        $classrooms = $request->classrooms ?? [];
        
        // If share as task is enabled, create posts for each classroom
        if ($request->has('as_task') && $request->as_task == 1 && count($classrooms) > 0) {
            $deadline = $request->deadline ? \Carbon\Carbon::parse($request->deadline) : null;
            
            foreach ($classrooms as $classroomId) {
                // Check if post already exists for this lesson and classroom
                $existingPost = Post::where('serial_id', $serial->id)
                    ->where('mapel_id', $lesson->mapel_id)
                    ->where('title', $lesson->name)
                    ->first();
                
                if (!$existingPost) {
                    // Create new post as task
                    Post::create([
                        'serial_id' => $serial->id,
                        'user_id' => auth()->id(),
                        'mapel_id' => $lesson->mapel_id,
                        'title' => $lesson->name,
                        'description' => 'Tugas dari materi: ' . $lesson->name,
                        'slug' => \Str::slug($lesson->name) . '-' . time(),
                        'category' => json_encode(['lesson_id' => $lesson->id]),
                        'due_date' => $deadline,
                        'is_task' => 1,
                    ]);
                }
            }
            
            return back()->with('success', 'Materi berhasil dibagikan sebagai tugas ke ' . count($classrooms) . ' kelas!');
        }
        
        return back()->with('success', 'Materi berhasil dibagikan ke ' . count($classrooms) . ' kelas!');
    }

    // Share custom materi to classrooms
    public function shareCustomMateri(Request $request, $serial, $postId)
    {
        $serial = Serial::findOrFail($serial);
        $post = Post::findOrFail($postId);
        
        // Update task settings
        $post->is_task = $request->has('as_task') && $request->as_task == 1 ? 1 : 0;
        $post->due_date = $request->deadline ? \Carbon\Carbon::parse($request->deadline) : null;
        
        $post->save();
        
        $message = $post->is_task 
            ? 'Materi berhasil dibagikan sebagai tugas!' 
            : 'Materi berhasil dibagikan!';
            
        return back()->with('success', $message);
    }

    // Materi Tambahan (Custom)
    public function custom($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get all mapels
        $mapels = Mapel::all();

        return view('guru.materi.custom', compact('serial', 'mapels'));
    }

    // List materi by mapel
    public function listByMapel($serial, $mapel)
    {
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        
        // Get all posts (materi) for this mapel and serial
        $materis = Post::where('serial_id', $serial->id)
            ->where('mapel_id', $mapel->id)
            ->where('is_task', 0)
            ->latest()
            ->get();

        return view('guru.materi.list-simple', compact('serial', 'mapel', 'materis'));
    }

    // Create new materi
    public function createMateri($serial, $mapel)
    {
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        
        return view('guru.materi.create-simple', compact('serial', 'mapel'));
    }

    // Store new materi
    public function storeMateri(Request $request, $serial, $mapel)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'link' => 'nullable|url',
            'attachment' => 'nullable|file|max:10240',
        ]);
        
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('materi', 'public');
        }
        
        Post::create([
            'serial_id' => $serial->id,
            'user_id' => auth()->id(),
            'mapel_id' => $mapel->id,
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title) . '-' . time(),
            'link' => $request->link,
            'attachment' => $attachmentPath,
            'embed' => $request->embed,
            'category' => null,
            'is_task' => 0,
        ]);
        
        return redirect()->route('guru.materi.mapel', [$serial->id, $mapel->id])
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    // Edit materi
    public function editMateri($serial, $mapel, $id)
    {
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        $materi = Post::findOrFail($id);
        
        return view('guru.materi.edit-simple', compact('serial', 'mapel', 'materi'));
    }

    // Update materi
    public function updateMateri(Request $request, $serial, $mapel, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'link' => 'nullable|url',
            'attachment' => 'nullable|file|max:10240',
        ]);
        
        $materi = Post::findOrFail($id);
        
        $attachmentPath = $materi->attachment;
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($materi->attachment && \Storage::disk('public')->exists($materi->attachment)) {
                \Storage::disk('public')->delete($materi->attachment);
            }
            $attachmentPath = $request->file('attachment')->store('materi', 'public');
        }
        
        $materi->update([
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'attachment' => $attachmentPath,
            'embed' => $request->embed,
        ]);
        
        return redirect()->route('guru.materi.mapel', [$serial, $mapel])
            ->with('success', 'Materi berhasil diupdate!');
    }

    // Show detail materi
    public function showDetail($serial, $mapel, $id)
    {
        $serial = Serial::findOrFail($serial);
        $mapel = Mapel::findOrFail($mapel);
        $materi = Post::with(['comments.user', 'comments.student', 'comments.replies.user', 'comments.replies.student'])->findOrFail($id);
        
        $sharedClassrooms = Classroom::where('serial_id', $serial->id)->get();
        
        return view('guru.materi.detail', compact('serial', 'mapel', 'materi', 'sharedClassrooms'));
    }

    // Store comment
    public function storeComment(Request $request, $serial, $mapel, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $materi = Post::findOrFail($id);
        
        PostComment::create([
            'post_id' => $materi->id,
            'user_id' => auth()->id(),
            'student_id' => null,
            'message' => $request->message,
            'code' => Str::random(10),
            'is_user' => 1, // Guru
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    // Store reply to comment
    public function storeReply(Request $request, $serial, $mapel, $id, $commentId)
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
    public function deleteComment($serial, $mapel, $id, $commentId)
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
    public function deleteReply($serial, $mapel, $id, $replyId)
    {
        $reply = PostChildComment::where('id', $replyId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        $reply->delete();

        return back()->with('success', 'Balasan berhasil dihapus!');
    }

    // Delete materi
    public function destroyMateri($serial, $mapel, $id)
    {
        $materi = Post::findOrFail($id);
        
        // Delete attachment if exists
        if ($materi->attachment && \Storage::disk('public')->exists($materi->attachment)) {
            \Storage::disk('public')->delete($materi->attachment);
        }
        
        $materi->delete();
        
        return redirect()->route('guru.materi.mapel', [$serial, $mapel])
            ->with('success', 'Materi berhasil dihapus!');
    }
    
    // Create new Theme (Mapel) for custom materials
    public function createTheme($serial)
    {
        $serial = Serial::findOrFail($serial);
        return view('guru.materi.create-theme', compact('serial'));
    }
    
    // Store new Theme (Mapel)
    public function storeTheme(Request $request, $serial)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);
        
        $serial = Serial::findOrFail($serial);
        
        // Get the max theme number to increment
        $maxTheme = Theme::max('theme') ?? 0;
        
        $theme = Theme::create([
            'lesson_id' => null, // Custom theme, not linked to lesson
            'theme' => $maxTheme + 1,
            'name' => $request->name,
        ]);
        
        return redirect()->route('guru.materi.custom', $serial->id)
            ->with('success', 'Mapel berhasil ditambahkan!');
    }
    
    // Create new Subtheme for a Theme
    public function createSubtheme($serial, $tema)
    {
        $serial = Serial::findOrFail($serial);
        $tema = Theme::findOrFail($tema);
        return view('guru.materi.create-subtheme', compact('serial', 'tema'));
    }
    
    // Store new Subtheme
    public function storeSubtheme(Request $request, $serial, $tema)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);
        
        $serial = Serial::findOrFail($serial);
        $tema = Theme::findOrFail($tema);
        
        // Get the max subtheme number for this theme
        $maxSubtheme = Subtheme::where('theme_id', $tema->id)->max('subtheme') ?? 0;
        
        Subtheme::create([
            'lesson_id' => null,
            'theme_id' => $tema->id,
            'subtheme' => $maxSubtheme + 1,
            'name' => $request->name,
        ]);
        
        return redirect()->route('guru.materi.tema', [$serial->id, $tema->id, 'custom'])
            ->with('success', 'Subtema berhasil ditambahkan!');
    }

    public function subtema($serial, $tema, $type = 'admin')
    {
        $serial = Serial::findOrFail($serial);
        $tema = Theme::findOrFail($tema);
        
        // Get subthemes for this theme
        $subthemes = Subtheme::where('theme_id', $tema->id)->get();

        return view('guru.materi.subtema', compact('serial', 'tema', 'subthemes', 'type'));
    }

    public function list($serial, $tema, $subtema, $type = 'custom')
    {
        $serial   = Serial::findOrFail($serial);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);

        // Get materials based on type
        if ($type === 'admin') {
            // Admin materials from lesson_items
            $materials = LessonItem::where('theme_id', $tema->id)
                ->where('subtheme_id', $subtema->id)
                ->where('is_admin', true)
                ->with(['lesson.mapel'])
                ->orderBy('number')
                ->get();
        } else {
            // Custom materials from posts (backward compatibility)
            $materials = Post::where('serial_id', $serial->id)
                ->where('is_task', 0)
                ->orderBy('created_at', 'desc')
                ->get()
                ->filter(function($post) use ($tema, $subtema) {
                    $category = json_decode($post->category, true);
                    $isMatch = isset($category['theme_id']) && 
                           isset($category['subtheme_id']) &&
                           $category['theme_id'] == $tema->id && 
                           $category['subtheme_id'] == $subtema->id;
                           
                    return $isMatch && (!isset($category['is_admin']) || $category['is_admin'] === false);
                });
        }

        // For admin type, convert to posts-like structure for view compatibility
        if ($type === 'admin') {
            $posts = $materials;
        } else {
            $posts = $materials;
        }

        return view('guru.materi.list', compact('serial', 'tema', 'subtema', 'posts', 'type', 'materials'));
    }

    public function create($serial, $tema, $subtema, $type = 'custom')
    {
        $serial   = Serial::findOrFail($serial);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);

        return view('guru.materi.create', compact('serial', 'tema', 'subtema', 'type'));
    }

    public function store(Request $request, $serial, $tema, $subtema)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'link' => 'nullable|url',
            'attachment' => 'nullable|file|max:10240',
            'embed' => 'nullable',
            'mapel_id' => 'nullable|exists:mapels,id',
            'kategori_kelas' => 'nullable|string|max:255',
        ]);

        $serial = Serial::findOrFail($serial);
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('materi', 'public');
        }

        // Find or create base lesson for this mapel
        $lesson = Lesson::firstOrCreate([
            'mapel_id' => $request->mapel_id,
            'category' => Lesson::CATEGORY_MATERI,
            'name' => 'Base Lesson',
        ], [
            'grade' => '1',
            'semester' => 1,
        ]);

        // Get next number for this lesson
        $nextNumber = LessonItem::where('lesson_id', $lesson->id)
            ->where('theme_id', $tema)
            ->where('subtheme_id', $subtema)
            ->max('number') + 1;

        // Create lesson item (custom from guru)
        LessonItem::create([
            'lesson_id' => $lesson->id,
            'theme_id' => $tema,
            'subtheme_id' => $subtema,
            'number' => $nextNumber ?? 1,
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'embed' => $request->embed,
            'attachment' => $attachmentPath,
            'is_admin' => false, // Custom from guru
        ]);

        return redirect()->route('guru.materi.list', [$serial->id, $tema, $subtema, 'custom'])
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    public function edit($serial, $tema, $subtema, $id, $type = 'custom')
    {
        $serial   = Serial::findOrFail($serial);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);
        
        // Try to find in lesson_items first (new structure)
        $lessonItem = LessonItem::find($id);
        
        if ($lessonItem) {
            // New structure - lesson_items
            return view('guru.materi.edit', compact('serial', 'tema', 'subtema', 'lessonItem', 'type'))
                ->with('post', null);
        } else {
            // Old structure - posts (backward compatibility)
            $post = Post::findOrFail($id);
            return view('guru.materi.edit', compact('serial', 'tema', 'subtema', 'post', 'type'))
                ->with('lessonItem', null);
        }
    }

    public function update(Request $request, $serial, $tema, $subtema, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'link' => 'nullable|url',
            'attachment' => 'nullable|file|max:10240',
            'embed' => 'nullable',
            'mapel_id' => 'nullable|exists:mapels,id',
        ]);

        // Check if it's lesson_item or post
        $lessonItem = LessonItem::find($id);
        
        if ($lessonItem) {
            // Update lesson_item
            $attachmentPath = $lessonItem->attachment;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('materi', 'public');
            }

            $lessonItem->update([
                'title' => $request->title,
                'description' => $request->description,
                'link' => $request->link,
                'attachment' => $attachmentPath,
                'embed' => $request->embed,
            ]);
        } else {
            // Update post (old structure)
            $post = Post::findOrFail($id);
            
            $attachmentPath = $post->attachment;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('materi', 'public');
            }

            $category = json_decode($post->category, true);

            $post->update([
                'title' => $request->title,
                'description' => $request->description,
                'slug' => Str::slug($request->title) . '-' . time(),
                'link' => $request->link,
                'attachment' => $attachmentPath,
                'embed' => $request->embed,
                'mapel_id' => $request->mapel_id,
                'category' => json_encode($category),
            ]);
        }

        return redirect()->route('guru.materi.list', [$serial, $tema, $subtema, 'custom'])
            ->with('success', 'Materi berhasil diperbarui!');
    }

    public function destroy($serial, $tema, $subtema, $id)
    {
        // Try lesson_item first
        $lessonItem = LessonItem::find($id);
        
        if ($lessonItem) {
            $lessonItem->delete();
        } else {
            // Fall back to post
            $post = Post::findOrFail($id);
            $post->delete();
        }

        return redirect()->route('guru.materi.list', [$serial, $tema, $subtema, 'custom'])
            ->with('success', 'Materi berhasil dihapus!');
    }

    public function share(Request $request, $serial, $tema, $subtema, $id)
    {
        // Get the admin material
        $post = Post::findOrFail($id);
        
        // Verify it's an admin material
        $category = json_decode($post->category, true);
        if (!isset($category['is_admin']) || $category['is_admin'] !== true) {
            return back()->with('error', 'Hanya materi admin yang bisa di-share!');
        }

        // Get classroom IDs from request (can be empty array to unshare from all)
        $classroomIds = $request->input('classroom_ids', []);
        
        // Validate classroom IDs belong to this serial
        if (!empty($classroomIds)) {
            $validClassrooms = \App\Models\Classroom::where('serial_id', $serial)
                ->whereIn('id', $classroomIds)
                ->pluck('id')
                ->toArray();
            
            if (count($validClassrooms) !== count($classroomIds)) {
                return back()->with('error', 'Beberapa kelas tidak valid!');
            }
        }

        $message = empty($classroomIds) 
            ? 'Materi dibatalkan dari semua kelas' 
            : 'Materi berhasil di-share ke ' . count($classroomIds) . ' kelas!';

        return back()->with('success', $message);
    }

    public function shareSingle($serial, $tema, $subtema, $id)
    {
        // Try lesson_item first (new structure)
        $lessonItem = LessonItem::find($id);
        
        if ($lessonItem) {
            // Verify it's an admin material
            if (!$lessonItem->is_admin) {
                return back()->with('error', 'Hanya materi admin yang bisa di-share!');
            }

            // Toggle share status
            $classrooms = Classroom::where('serial_id', $serial)->pluck('id')->toArray();

            if (empty($classrooms)) {
                return back()->with('error', 'Belum ada kelas yang dibuat!');
            }

            return back()->with('success', 'Materi berhasil diproses untuk serial ini!');
        } else {
            // Old structure - posts
            $post = Post::findOrFail($id);
            
            // Verify it's an admin material
            $category = json_decode($post->category, true);
            if (!isset($category['is_admin']) || $category['is_admin'] !== true) {
                return back()->with('error', 'Hanya materi admin yang bisa di-share!');
            }

            $classrooms = Classroom::where('serial_id', $serial)->pluck('id')->toArray();

            if (empty($classrooms)) {
                return back()->with('error', 'Belum ada kelas yang dibuat!');
            }

            return back()->with('success', 'Materi berhasil diproses untuk serial ini!');
        }
    }

    public function bulkShare(Request $request, $serial, $tema, $subtema)
    {
        $postIds = json_decode($request->input('post_ids', '[]'), true);
        
        if (empty($postIds)) {
            return back()->with('error', 'Tidak ada materi yang dipilih!');
        }

        // Get all classrooms for this serial
        $classrooms = Classroom::where('serial_id', $serial)->pluck('id')->toArray();
        
        if (empty($classrooms)) {
            return back()->with('error', 'Belum ada kelas yang dibuat!');
        }

        // Update all selected items (both lesson_items and posts)
        $updated = 0;
        foreach ($postIds as $postId) {
            // Try lesson_item first
            $lessonItem = LessonItem::find($postId);
            
            if ($lessonItem && $lessonItem->is_admin) {
                $updated++;
            } else {
                // Try post
                $post = Post::find($postId);
                
                if ($post) {
                    $category = json_decode($post->category, true);
                    if (isset($category['is_admin']) && $category['is_admin'] === true) {
                        $updated++;
                    }
                }
            }
        }

        return back()->with('success', "$updated materi berhasil di-share ke semua kelas (" . count($classrooms) . " kelas)!");
    }
}
