@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route($type === 'admin' ? 'guru.materi.admin' : 'guru.materi.custom', $serial->id) }}">{{ $type === 'admin' ? 'Materi dari Admin' : 'Materi Tambahan' }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.tema', [$serial->id, $tema->id, $type]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.list', [$serial->id, $tema->id, $subtema->id, $type]) }}">{{ $subtema->name }}</a></li>
            <li class="breadcrumb-item active">Edit Materi</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <!-- Form Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Materi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.materi.update', [$serial->id, $tema->id, $subtema->id, $post->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Pilih Materi Pembelajaran atau Tugas -->
                        <div class="mb-3">
                            <label class="form-label">Pilih materi pembelajaran atau tugas</label>
                            <select name="is_task" class="form-select @error('is_task') is-invalid @enderror">
                                <option value="0" {{ old('is_task', $post->is_task) == 0 ? 'selected' : '' }}>Materi Pembelajaran</option>
                                <option value="1" {{ old('is_task', $post->is_task) == 1 ? 'selected' : '' }}>Tugas</option>
                            </select>
                            @error('is_task')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mata Pelajaran -->
                        <div class="mb-3">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-select @error('mapel_id') is-invalid @enderror">
                                <option value="">-- Pilih Mata Pelajaran (Opsional) --</option>
                                @foreach(\App\Models\Mapel::all() as $mapel)
                                @php
                                    $selectedMapelId = isset($lessonItem) ? ($lessonItem->lesson->mapel_id ?? null) : (isset($post) ? $post->mapel_id : null);
                                @endphp
                                <option value="{{ $mapel->id }}" {{ old('mapel_id', $selectedMapelId) == $mapel->id ? 'selected' : '' }}>{{ $mapel->name }}</option>
                                @endforeach
                            </select>
                            @error('mapel_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Judul -->
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            @php
                                $title = isset($lessonItem) ? $lessonItem->title : (isset($post) ? $post->title : '');
                            @endphp
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $title) }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description with CKEditor -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            @php
                                $description = isset($lessonItem) ? $lessonItem->description : (isset($post) ? $post->description : '');
                            @endphp
                            <textarea name="description" id="editor" class="form-control @error('description') is-invalid @enderror">{{ old('description', $description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Link -->
                        <div class="mb-3">
                            <label class="form-label">Link <small class="text-muted">(Optional)</small></label>
                            @php
                                $link = isset($lessonItem) ? $lessonItem->link : (isset($post) ? $post->link : '');
                            @endphp
                            <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $link) }}" placeholder="https://example.com">
                            @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori Kelas -->
                        <div class="mb-3">
                            <label class="form-label">Kategori kelas</label>
                            @php
                                $category = is_string($post->category) ? json_decode($post->category, true) : ($post->category ?? []);
                                $kategoriKelas = $category['kategori_kelas'] ?? '';
                            @endphp
                            <input type="text" name="kategori_kelas" class="form-control @error('kategori_kelas') is-invalid @enderror" value="{{ old('kategori_kelas', $kategoriKelas) }}" placeholder="Contoh: Kelas 4">
                            @error('kategori_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Attachment -->
                        @php
                            $currentAttachment = isset($lessonItem) ? $lessonItem->attachment : (isset($post) ? $post->attachment : null);
                        @endphp
                        @if($currentAttachment)
                        <div class="mb-3">
                            <label class="form-label">File Saat Ini</label>
                            <div class="alert alert-info mb-2">
                                <i class='bx bx-file me-2'></i>
                                <a href="{{ Storage::url($currentAttachment) }}" target="_blank">{{ basename($currentAttachment) }}</a>
                            </div>
                        </div>
                        @endif

                        <!-- New Attachment -->
                        <div class="mb-3">
                            <label class="form-label">{{ $currentAttachment ? 'Ganti File' : 'File Lampiran' }}</label>
                            <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                            @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Max 10MB (PDF, DOC, PPT, atau gambar)</small>
                        </div>

                        <!-- Embed -->
                        <div class="mb-3">
                            <label class="form-label">Embed Code (Video/Iframe)</label>
                            @php
                                $embed = isset($lessonItem) ? $lessonItem->embed : (isset($post) ? $post->embed : '');
                            @endphp
                            <textarea name="embed" rows="3" class="form-control @error('embed') is-invalid @enderror" placeholder="<iframe src='...'></iframe>">{{ old('embed', $embed) }}</textarea>
                            @error('embed')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kode embed dari YouTube, Vimeo, atau platform lainnya</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save me-1'></i>Update Materi
                            </button>
                            <a href="{{ route('guru.materi.list', [$serial->id, $tema->id, $subtema->id, $type]) }}" class="btn btn-label-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CKEditor 5 -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: ['undo', 'redo', '|', 'bold', 'italic', '|', 
                     'alignment', 'outdent', 'indent', '|',
                     'bulletedList', 'numberedList', '|',
                     'imageUpload', 'link'],
            language: 'id'
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endsection