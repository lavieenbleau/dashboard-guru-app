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
                            
                            @if($materi->description)
                            <p class="text-muted mb-3">{{ Str::limit($materi->description, 200) }}</p>
                            @endif

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($materi->link)
                                <span class="badge bg-label-primary">
                                    <i class='bx bx-link-alt'></i> Link
                                </span>
                                @endif
                                
                                @if($attachment)
                                <span class="badge bg-label-success">
                                    <i class='bx bx-file'></i> File
                                </span>
                                @endif
                                
                                @if($embed)
                                <span class="badge bg-label-info">
                                    <i class='bx bx-video'></i> Embed
                                </span>
                                @endif
                                
                                @if($type === 'admin' && $sharedToClasses)
                                @php
                                    $sharedCount = is_array($sharedToClasses) ? count($sharedToClasses) : count(json_decode($sharedToClasses, true) ?? []);
                                @endphp
                                @if($sharedCount > 0)
                                <span class="badge bg-label-primary">
                                    <i class='bx bx-share-alt'></i> Shared ke {{ $sharedCount }} kelas
                                </span>
                                @endif
                                @endif
                            </div>

                            <small class="text-muted">
                                <i class='bx bx-user'></i> {{ $userName }} • 
                                <i class='bx bx-time'></i> {{ $createdAt->diffForHumans() }}
                            </small>
                            </div>
                        </div>

                        @if($type === 'admin')
                        <!-- Tombol Share Individual untuk Materi Admin -->
                        <form action="{{ route('guru.materi.share-single', [$serial->id, $tema->id, $subtema->id, $itemId]) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $sharedToClasses ? 'btn-success' : 'btn-primary' }}" 
                                    onclick="return confirm('{{ $sharedToClasses ? 'Batalkan share materi ini?' : 'Share materi ke semua kelas?' }}')">
                                <i class='bx {{ $sharedToClasses ? 'bx-check-circle' : 'bx-share-alt' }} me-1'></i>
                                {{ $sharedToClasses ? 'Shared' : 'Share' }}
                            </button>
                        </form>
                        @else
                        <!-- Edit/Delete untuk Materi Custom Guru -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('guru.materi.edit', [$serial->id, $tema->id, $subtema->id, $itemId, $type]) }}">
                                        <i class='bx bx-edit me-2'></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('guru.materi.destroy', [$serial->id, $tema->id, $subtema->id, $itemId]) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class='bx bx-trash me-2'></i>Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </div>

                    <!-- Content Details -->
                    @if($link || $attachment || $embed)
                    <div class="mt-3 pt-3 border-top">
                        @if($link)
                        <div class="mb-2">
                            <a href="{{ $link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-link-external'></i> Buka Link
                            </a>
                        </div>
                        @endif

                        @if($attachment)
                        <div class="mb-2">
                            <a href="{{ Storage::url($attachment) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class='bx bx-download'></i> Download File
                            </a>
                        </div>
                        @endif

                        @if($embed)
                        <div class="mt-3">
                            <div class="ratio ratio-16x9">
                                {!! $embed !!}
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
            alert('Pilih minimal satu materi!');
            return;
        }

        if (confirm(`Share ${selected.length} materi ke semua kelas?`)) {
            postIdsInput.value = JSON.stringify(selected);
            bulkShareForm.submit();
        }
    });
});
</script>
@endif

@endsection
