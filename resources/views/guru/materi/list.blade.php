@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.tema', [$serial->id, $tema->id]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item active">{{ $subtema->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Materi - {{ $subtema->name }}</h4>
                <p class="text-muted mb-0">{{ $tema->name }}</p>
            </div>
            <a href="{{ route('guru.materi.create', [$serial->id, $tema->id, $subtema->id]) }}" class="btn btn-primary">
                <i class='bx bx-plus me-1'></i>Tambah Materi
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Materi List -->
    <div class="row g-3">
        @forelse ($posts as $post)
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h5 class="mb-2">{{ $post->title }}</h5>
                            
                            @if($post->description)
                            <p class="text-muted mb-3">{{ Str::limit($post->description, 200) }}</p>
                            @endif

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($post->link)
                                <span class="badge bg-label-primary">
                                    <i class='bx bx-link-alt'></i> Link
                                </span>
                                @endif
                                
                                @if($post->attachment)
                                <span class="badge bg-label-success">
                                    <i class='bx bx-file'></i> File
                                </span>
                                @endif
                                
                                @if($post->embed)
                                <span class="badge bg-label-info">
                                    <i class='bx bx-video'></i> Embed
                                </span>
                                @endif
                            </div>

                            <small class="text-muted">
                                <i class='bx bx-user'></i> {{ $post->user->name ?? 'Unknown' }} • 
                                <i class='bx bx-time'></i> {{ $post->created_at->diffForHumans() }}
                            </small>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('guru.materi.edit', [$serial->id, $tema->id, $subtema->id, $post->id]) }}">
                                        <i class='bx bx-edit me-2'></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('guru.materi.destroy', [$serial->id, $tema->id, $subtema->id, $post->id]) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class='bx bx-trash me-2'></i>Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Content Details -->
                    @if($post->link || $post->attachment || $post->embed)
                    <div class="mt-3 pt-3 border-top">
                        @if($post->link)
                        <div class="mb-2">
                            <a href="{{ $post->link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-link-external'></i> Buka Link
                            </a>
                        </div>
                        @endif

                        @if($post->attachment)
                        <div class="mb-2">
                            <a href="{{ Storage::url($post->attachment) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class='bx bx-download'></i> Download File
                            </a>
                        </div>
                        @endif

                        @if($post->embed)
                        <div class="mt-3">
                            <div class="ratio ratio-16x9">
                                {!! $post->embed !!}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class='bx bx-book-open' style="font-size: 48px; opacity: 0.3;"></i>
                    <h5 class="mt-3">Belum Ada Materi</h5>
                    <p class="text-muted mb-3">Tambahkan materi pertama untuk sub tema ini</p>
                    <a href="{{ route('guru.materi.create', [$serial->id, $tema->id, $subtema->id]) }}" class="btn btn-primary">
                        <i class='bx bx-plus me-1'></i>Tambah Materi
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
