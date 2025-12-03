@extends('layouts.sneat')

@section('content')
<div class="container-xxl">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.tema', [$serial->id, $tema->id]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.list', [$serial->id, $tema->id, $subtema->id]) }}">{{ $subtema->name }}</a></li>
            <li class="breadcrumb-item active">Tambah Materi</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Form Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tambah Materi Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.materi.store', [$serial->id, $tema->id, $subtema->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label">Judul Materi <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jelaskan materi secara singkat</small>
                        </div>

                        <!-- Link -->
                        <div class="mb-3">
                            <label class="form-label">Link Eksternal</label>
                            <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link') }}" placeholder="https://example.com">
                            @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">URL ke sumber materi eksternal (opsional)</small>
                        </div>

                        <!-- Attachment -->
                        <div class="mb-3">
                            <label class="form-label">File Lampiran</label>
                            <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
                            @error('attachment')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Max 10MB (PDF, DOC, PPT, atau gambar)</small>
                        </div>

                        <!-- Embed -->
                        <div class="mb-3">
                            <label class="form-label">Embed Code (Video/Iframe)</label>
                            <textarea name="embed" rows="3" class="form-control @error('embed') is-invalid @enderror" placeholder="<iframe src='...'></iframe>">{{ old('embed') }}</textarea>
                            @error('embed')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kode embed dari YouTube, Vimeo, atau platform lainnya</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save me-1'></i>Simpan Materi
                            </button>
                            <a href="{{ route('guru.materi.list', [$serial->id, $tema->id, $subtema->id]) }}" class="btn btn-label-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Info Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Informasi</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 mb-2">Tema:</dt>
                        <dd class="col-sm-7 mb-2">{{ $tema->name }}</dd>

                        <dt class="col-sm-5 mb-0">Sub Tema:</dt>
                        <dd class="col-sm-7 mb-0">{{ $subtema->name }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection