@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.custom', $serial->id) }}">Materi Tambahan</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.mapel', [$serial->id, $mapel->id]) }}">{{ $mapel->name }}</a></li>
            <li class="breadcrumb-item active">Edit Materi</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <!-- Form Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Materi - {{ $mapel->name }}</h5>
                    <a href="{{ route('guru.materi.mapel', [$serial->id, $mapel->id]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class='bx bx-arrow-back'></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.materi.update', [$serial->id, $mapel->id, $materi->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Judul -->
                        <div class="mb-3">
                            <label class="form-label">Judul Materi <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $materi->title) }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description', $materi->description) }}</textarea>
                            <small class="text-muted">Jelaskan materi secara detail</small>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Link -->
                        <div class="mb-3">
                            <label class="form-label">Link Materi <small class="text-muted">(Optional)</small></label>
                            <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $materi->link) }}" placeholder="https://example.com">
                            <small class="text-muted">Link ke video, artikel, atau sumber belajar online</small>
                            @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Attachment -->
                        @if($materi->attachment)
                        <div class="mb-3">
                            <label class="form-label">File Lampiran Saat Ini</label>
                            <div class="alert alert-info">
                                <i class='bx bx-file'></i> 
                                <a href="{{ Storage::url($materi->attachment) }}" target="_blank">{{ basename($materi->attachment) }}</a>
                            </div>
                        </div>
                        @endif

                        <!-- New Attachment -->
                        <div class="mb-3">
                            <label class="form-label">{{ $materi->attachment ? 'Ganti' : 'Tambah' }} File Lampiran <small class="text-muted">(Optional)</small></label>
                            <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip">
                            <small class="text-muted">Format: PDF, DOC, PPT, XLS, ZIP. Maksimal 10MB</small>
                            @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Embed Code -->
                        <div class="mb-3">
                            <label class="form-label">Embed Code <small class="text-muted">(Optional)</small></label>
                            <textarea name="embed" class="form-control @error('embed') is-invalid @enderror" rows="3" placeholder="<iframe src='...'></iframe>">{{ old('embed', $materi->embed) }}</textarea>
                            <small class="text-muted">Kode embed untuk video YouTube, Google Docs, dll</small>
                            @error('embed')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('guru.materi.mapel', [$serial->id, $mapel->id]) }}" class="btn btn-outline-secondary">
                                <i class='bx bx-x'></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save'></i> Update Materi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
