@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.custom', $serial->id) }}">Materi Tambahan</a></li>
            <li class="breadcrumb-item active">{{ $lesson->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Materi Tambahan - {{ $lesson->name }}</h4>
                <p class="text-muted mb-0">Kelola materi untuk mata pelajaran {{ $lesson->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('guru.materi.create', [$serial->id, $lesson->id]) }}" class="btn btn-primary">
                    <i class='bx bx-plus me-1'></i>Tambah Materi
                </a>
                <a href="{{ route('guru.materi.custom', $serial->id) }}" class="btn btn-outline-secondary">
                    <i class='bx bx-arrow-back me-1'></i> Kembali
                </a>
            </div>
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
    <div class="row g-4">
        @forelse ($materis as $materi)
        <div class="col-12">
            <div class="card" style="border-radius: 12px; margin-bottom: 20px; border: 1px solid #E5E7EB; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex flex-column flex-md-row w-100">
                        @php
                            $youtubeId = null;
                            $embedUrl = null;
                            if($materi->embed) {
                                $embedUrl = $materi->embed;
                                if (preg_match('/src="([^"]+)"/i', $materi->embed, $matches)) {
                                    $embedUrl = $matches[1];
                                } elseif (preg_match("/src='([^']+)'/i", $materi->embed, $matches)) {
                                    $embedUrl = $matches[1];
                                }
                                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $embedUrl, $match)) {
                                    $youtubeId = $match[1];
                                }
                            }
                        @endphp

                        @if($youtubeId)
                            <div class="flex-shrink-0 mb-3 mb-md-0 me-md-3" style="width: 180px;">
                                <div class="position-relative" style="width: 100%; height: 100px; border-radius: 8px; overflow: hidden; background-color: #000;">
                                    <img src="https://img.youtube.com/vi/{{ $youtubeId }}/maxresdefault.jpg" onerror="this.src='https://img.youtube.com/vi/{{ $youtubeId }}/hqdefault.jpg'" alt="Thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                                    <a href="{{ route('guru.materi.detail', [$serial->id, $lesson->id, $materi->id]) }}" class="position-absolute top-50 start-50 translate-middle text-white text-decoration-none">
                                        <i class='bx bx-play-circle' style="font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5);"></i>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="flex-shrink-0 mb-3 mb-md-0 me-md-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 8px; background-color: #EEF2FF; color: #4F46E5;">
                                <i class='bx bx-file fs-4'></i>
                            </div>
                        @endif

                        <div class="flex-grow-1 d-flex flex-column justify-content-between">
                            <div class="pe-0">
                                <h6 class="mb-1 text-dark fw-bold" style="font-size: 1.05rem;">
                                    {{ $materi->title }}
                                </h6>
                                
                                @php
                                    $categoryData = is_string($materi->category) ? json_decode($materi->category, true) : ($materi->category ?? []);
                                    $isShared = isset($categoryData['is_shared']) && $categoryData['is_shared'] === true;
                                @endphp
                                
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2 text-muted" style="font-size: 0.8rem;">
                                    @if($isShared)
                                        <span class="badge bg-label-success px-2 py-1"><i class='bx bx-check-circle me-1'></i>Shared</span>
                                    @endif
                                    <span><i class='bx bx-user'></i> {{ $materi->user->name ?? 'Unknown' }}</span>
                                    <span>•</span>
                                    <span><i class='bx bx-time'></i> {{ $materi->created_at->format('d M Y') }}</span>
                                </div>
                                
                                @if($materi->description)
                                <p class="text-muted mb-0" style="font-size: 0.85rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ strip_tags($materi->description) }}
                                </p>
                                @endif
                            </div>
                            
                            <div class="d-flex justify-content-end mt-3 mt-md-0 align-items-center" style="gap: 12px;">
                                <a href="{{ route('guru.materi.detail', [$serial->id, $lesson->id, $materi->id]) }}" class="btn btn-sm btn-primary px-4" style="border-radius: 6px;">
                                    Lihat Detail
                                </a>
                                <x-action-dropdown>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.materi.detail', [$serial->id, $lesson->id, $materi->id]) }}">
                                            <i class='bx bx-show me-2'></i>Lihat Detail
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('guru.materi.share', [$serial->id, $materi->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class='bx bx-share-alt me-2'></i>Bagikan
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.materi.edit', [$serial->id, $lesson->id, $materi->id]) }}">
                                            <i class='bx bx-edit me-2'></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('guru.materi.destroy', [$serial->id, $lesson->id, $materi->id]) }}" method="POST" onsubmit="confirmSubmit(event, 'Konfirmasi Hapus', 'Hapus materi ini?', 'Ya, Hapus', true)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class='bx bx-trash me-2'></i>Hapus
                                            </button>
                                        </form>
                                    </li>
                                </x-action-dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class='bx bx-book-open' style="font-size: 48px; opacity: 0.3;"></i>
                    <h5 class="mt-3">Belum Ada Materi</h5>
                    <p class="text-muted mb-3">Tambahkan materi pertama untuk mata pelajaran {{ $lesson->name }}</p>
                    <a href="{{ route('guru.materi.create', [$serial->id, $lesson->id]) }}" class="btn btn-primary">
                        <i class='bx bx-plus me-1'></i>Tambah Materi
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

@endsection
