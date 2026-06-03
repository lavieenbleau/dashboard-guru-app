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
    <div class="row g-3">
        @forelse ($materis as $materi)
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <h5 class="mb-0">{{ $materi->title }}</h5>
                            </div>
                            
                            <div class="mb-2">
                                @php
                                    $categoryData = is_string($materi->category) ? json_decode($materi->category, true) : ($materi->category ?? []);
                                    $isShared = isset($categoryData['is_shared']) && $categoryData['is_shared'] === true;
                                @endphp
                                @if($isShared)
                                    <span class="badge bg-label-success"><i class='bx bx-check-circle me-1'></i>Dibagikan ke Semua Kelas</span>
                                @else
                                    <span class="badge bg-label-secondary"><i class='bx bx-info-circle me-1'></i>Belum Dibagikan</span>
                                @endif
                            </div>
                            
                            @if($materi->description)
                            <p class="text-muted mb-3">{{ Str::limit($materi->description, 200) }}</p>
                            @endif

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($materi->link)
                                <span class="badge bg-label-primary">
                                    <i class='bx bx-link-alt'></i> Link
                                </span>
                                @endif
                                
                                @if($materi->attachment)
                                <span class="badge bg-label-success">
                                    <i class='bx bx-file'></i> File
                                </span>
                                @endif
                                
                                @if($materi->embed)
                                <span class="badge bg-label-info">
                                    <i class='bx bx-video'></i> Embed
                                </span>
                                @endif
                            </div>

                            <small class="text-muted">
                                <i class='bx bx-user'></i> {{ $materi->user->name ?? 'Unknown' }} • 
                                <i class='bx bx-time'></i> {{ $materi->created_at->diffForHumans() }}
                            </small>
                        </div>

                        <!-- Edit/Delete Actions -->
                        <x-action-dropdown>
                                <li>
                                    <a class="dropdown-item" href="{{ route('guru.materi.detail', [$serial->id, $lesson->id, $materi->id]) }}">
                                        <i class='bx bx-show me-2'></i>Lihat Detail
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    @php
                                        $categoryData = is_string($materi->category) ? json_decode($materi->category, true) : ($materi->category ?? []);
                                        $isShared = isset($categoryData['is_shared']) && $categoryData['is_shared'] === true;
                                    @endphp
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
                                    <form action="{{ route('guru.materi.destroy', [$serial->id, $lesson->id, $materi->id]) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class='bx bx-trash me-2'></i>Hapus
                                        </button>
                                    </form>
                                </li>
                            </x-action-dropdown>
                    </div>

                    <!-- Content Details -->
                    @if($materi->link || $materi->attachment || $materi->embed)
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('guru.materi.detail', [$serial->id, $lesson->id, $materi->id]) }}" class="btn btn-sm btn-primary">
                                <i class='bx bx-show'></i> Lihat Detail
                            </a>
                            
                            @if($materi->link)
                            <a href="{{ $materi->link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-link-external'></i> Buka Link
                            </a>
                            @endif

                            @if($materi->attachment)
                            <a href="{{ Storage::url($materi->attachment) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class='bx bx-download'></i> Download File
                            </a>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="mt-3 pt-3 border-top">
                        <a href="{{ route('guru.materi.detail', [$serial->id, $lesson->id, $materi->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class='bx bx-show'></i> Lihat Detail
                        </a>
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
