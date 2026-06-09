@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.custom', $serial->id) }}">Materi Tambahan</a></li>
            <li class="breadcrumb-item active">{{ $mapel->name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Materi Tambahan - {{ $mapel->name }}</h4>
                <p class="text-muted mb-0">Kelola materi untuk mata pelajaran {{ $mapel->name }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('guru.materi.create', [$serial->id, $mapel->id]) }}" class="btn btn-primary">
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
                            if($embed) {
                                $embedUrl = $embed;
                                if (preg_match('/src="([^"]+)"/i', $embed, $matches)) {
                                    $embedUrl = $matches[1];
                                } elseif (preg_match("/src='([^']+)'/i", $embed, $matches)) {
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
                                    <a href="{{ $embedUrl }}" target="_blank" class="position-absolute top-50 start-50 translate-middle text-white text-decoration-none">
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
                                
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2 text-muted" style="font-size: 0.8rem;">
                                    @if($type === 'admin' && $sharedToClasses)
                                        @php
                                            $sharedCount = is_array($sharedToClasses) ? count($sharedToClasses) : count(json_decode($sharedToClasses, true) ?? []);
                                        @endphp
                                        @if($sharedCount > 0)
                                            <span class="badge bg-label-primary px-2 py-1"><i class='bx bx-share-alt me-1'></i>Shared ke {{ $sharedCount }} kelas</span>
                                        @endif
                                    @endif
                                    <span><i class='bx bx-user'></i> {{ $userName }}</span>
                                    <span>•</span>
                                    <span><i class='bx bx-time'></i> {{ $createdAt->format('d M Y') }}</span>
                                </div>
                                
                                @if($materi->description)
                                <p class="text-muted mb-0" style="font-size: 0.85rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ strip_tags($materi->description) }}
                                </p>
                                @endif
                            </div>
                            
                            <div class="d-flex justify-content-end mt-3 mt-md-0 align-items-center" style="gap: 12px;">
                                @if($embed)
                                    <a href="{{ $embedUrl }}" target="_blank" class="btn btn-sm btn-outline-primary px-3" style="border-radius: 6px;">
                                        <i class='bx bx-play-circle me-1'></i> Video
                                    </a>
                                @endif
                                @if($link)
                                    <a href="{{ $link }}" target="_blank" class="btn btn-sm btn-outline-primary px-3" style="border-radius: 6px;">
                                        <i class='bx bx-link-external me-1'></i> Buka Link
                                    </a>
                                @endif
                                @if($attachment)
                                    <a href="{{ Storage::url($attachment) }}" target="_blank" class="btn btn-sm btn-primary px-3" style="border-radius: 6px;">
                                        <i class='bx bx-download me-1'></i> Download
                                    </a>
                                @endif
                                
                                <x-action-dropdown>
                                    @if($type === 'admin')
                                        <li>
                                            <form action="{{ route('guru.materi.share-single', [$serial->id, $tema->id, $subtema->id, $itemId]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="button" class="dropdown-item" onclick="confirmClick(event, 'Konfirmasi', '{{ $sharedToClasses ? 'Batalkan share materi ini?' : 'Share materi ke semua kelas?' }}', 'Ya, Lanjutkan')">
                                                    <i class='bx {{ $sharedToClasses ? 'bx-check-circle' : 'bx-share-alt' }} me-2'></i>{{ $sharedToClasses ? 'Batalkan Share' : 'Bagikan' }}
                                                </button>
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item" href="{{ route('guru.materi.edit', [$serial->id, $tema->id, $subtema->id, $itemId, $type]) }}">
                                                <i class='bx bx-edit me-2'></i>Edit
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('guru.materi.destroy', [$serial->id, $tema->id, $subtema->id, $itemId]) }}" method="POST" onsubmit="confirmSubmit(event, 'Konfirmasi Hapus', 'Hapus materi ini?', 'Ya, Hapus', true)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class='bx bx-trash me-2'></i>Hapus
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </x-action-dropdown>
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
                    @if($type === 'admin')
                    <p class="text-muted mb-3">Belum ada materi dari admin untuk subtema ini</p>
                    @else
                    <p class="text-muted mb-3">Tambahkan materi pertama untuk sub tema ini</p>
                    <a href="{{ route('guru.materi.create', [$serial->id, $tema->id, $subtema->id, $type]) }}" class="btn btn-primary">
                        <i class='bx bx-plus me-1'></i>Tambah Materi
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

@if($type === 'admin')
<!-- Form untuk Bulk Share -->
<form id="bulkShareForm" action="{{ route('guru.materi.bulk-share', [$serial->id, $tema->id, $subtema->id]) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="post_ids" id="postIdsInput">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleSelectBtn');
    const bulkShareBtn = document.getElementById('bulkShareBtn');
    const checkboxes = document.querySelectorAll('.materi-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkShareForm = document.getElementById('bulkShareForm');
    const postIdsInput = document.getElementById('postIdsInput');
    let isSelectMode = false;

    // Toggle select mode
    toggleBtn.addEventListener('click', function() {
        isSelectMode = !isSelectMode;
        
        if (isSelectMode) {
            // Show checkboxes
            checkboxes.forEach(cb => cb.style.display = 'block');
            toggleBtn.innerHTML = '<i class="bx bx-x me-1"></i>Batal';
            toggleBtn.classList.remove('btn-label-secondary');
            toggleBtn.classList.add('btn-label-danger');
        } else {
            // Hide checkboxes and uncheck all
            checkboxes.forEach(cb => {
                cb.style.display = 'none';
                cb.querySelector('input').checked = false;
            });
            toggleBtn.innerHTML = '<i class="bx bx-checkbox me-1"></i>Pilih Materi';
            toggleBtn.classList.remove('btn-label-danger');
            toggleBtn.classList.add('btn-label-secondary');
            bulkShareBtn.style.display = 'none';
            updateSelectedCount();
        }
    });

    // Update selected count
    function updateSelectedCount() {
        const selected = document.querySelectorAll('.materi-select:checked');
        const count = selected.length;
        selectedCountSpan.textContent = count;
        
        if (count > 0 && isSelectMode) {
            bulkShareBtn.style.display = 'inline-block';
        } else {
            bulkShareBtn.style.display = 'none';
        }
    }

    // Listen to checkbox changes
    document.querySelectorAll('.materi-select').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Bulk share action
    bulkShareBtn.addEventListener('click', function() {
        const selected = Array.from(document.querySelectorAll('.materi-select:checked'))
            .map(cb => cb.value);
        
        if (selected.length === 0) {
            showError('Pilih minimal satu materi!');
            return;
        }

        showConfirm('Konfirmasi Share', `Share ${selected.length} materi ke semua kelas?`, 'Ya, Lanjutkan').then((result) => {
            if (result.isConfirmed) {
                postIdsInput.value = JSON.stringify(selected);
                bulkShareForm.submit();
            }
        });
    });
});
</script>
@endif

@endsection
