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

class MateriController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get unique themes (mata pelajaran) as main menu
        $themes = Theme::select('id', 'name')
            ->distinct()
            ->get()
            ->unique('name');

        return view('guru.materi.index', compact('serial', 'themes'));
    }

    public function subtema($serial, $tema)
    {
        $serial = Serial::findOrFail($serial);
        $tema = Theme::findOrFail($tema);
        
        // Get subthemes for this theme
        $subthemes = Subtheme::where('theme_id', $tema->id)->get();

        return view('guru.materi.subtema', compact('serial', 'tema', 'subthemes'));
    }

    public function list($serial, $tema, $subtema)
    {
        $serial   = Serial::findOrFail($serial);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);

        // Get posts for this combination - decode JSON category
        $posts = Post::where('serial_id', $serial->id)
            ->where('is_task', 0)
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function($post) use ($tema, $subtema) {
                $category = json_decode($post->category, true);
                return isset($category['theme_id']) && 
                       isset($category['subtheme_id']) &&
                       $category['theme_id'] == $tema->id && 
                       $category['subtheme_id'] == $subtema->id;
            });

        return view('guru.materi.list', compact('serial', 'tema', 'subtema', 'posts'));
    }

    public function create($serial, $tema, $subtema)
    {
        $serial   = Serial::findOrFail($serial);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);

        return view('guru.materi.create', compact('serial', 'tema', 'subtema'));
    }

    public function store(Request $request, $serial, $tema, $subtema)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'link' => 'nullable|url',
            'attachment' => 'nullable|file|max:10240',
            'embed' => 'nullable',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('materi', 'public');
        }

        $category = json_encode([
            'theme_id' => $tema,
            'subtheme_id' => $subtema,
        ]);

        Post::create([
            'serial_id' => $serial,
            'user_id' => auth()->id(),
            'mapel_id' => null, // Not using mapel anymore
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title) . '-' . time(),
            'link' => $request->link,
            'attachment' => $attachmentPath,
            'embed' => $request->embed,
            'category' => $category,
            'is_task' => 0,
        ]);

        return redirect()->route('guru.materi.list', [$serial, $tema, $subtema])
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    public function edit($serial, $tema, $subtema, $id)
    {
        $serial   = Serial::findOrFail($serial);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);
        $post     = Post::findOrFail($id);

        return view('guru.materi.edit', compact('serial', 'tema', 'subtema', 'post'));
    }

    public function update(Request $request, $serial, $tema, $subtema, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'link' => 'nullable|url',
            'attachment' => 'nullable|file|max:10240',
            'embed' => 'nullable',
        ]);

        $post = Post::findOrFail($id);

        $attachmentPath = $post->attachment;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('materi', 'public');
        }

        $post->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title) . '-' . time(),
            'link' => $request->link,
            'attachment' => $attachmentPath,
            'embed' => $request->embed,
        ]);

        return redirect()->route('guru.materi.list', [$serial, $tema, $subtema])
            ->with('success', 'Materi berhasil diperbarui!');
    }

    public function destroy($serial, $tema, $subtema, $id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('guru.materi.list', [$serial, $tema, $subtema])
            ->with('success', 'Materi berhasil dihapus!');
    }
}