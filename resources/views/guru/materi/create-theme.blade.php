@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.custom', $serial->id) }}">Materi Tambahan</a></li>
            <li class="breadcrumb-item active">Tambah Mapel</li>
        </ol>
    </nav>

    <!-- Form Card -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tambah Mata Pelajaran Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.materi.theme.store', $serial->id) }}" method="POST">
                        @csrf

                        <!-- Nama Mapel -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Contoh: Matematika Lanjut"
                                   required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Masukkan nama mata pelajaran yang akan Anda ajarkan</div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('guru.materi.custom', $serial->id) }}" class="btn btn-outline-secondary">
                                <i class='bx bx-arrow-back me-1'></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class='bx bx-check me-1'></i>Simpan Mapel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex">
                        <i class='bx bx-info-circle text-info me-2' style="font-size: 24px;"></i>
                        <div>
                            <h6 class="mb-1">Informasi</h6>
                            <p class="text-muted mb-0 small">
                                Setelah membuat mata pelajaran, Anda dapat menambahkan subtema dan materi di dalamnya.
                                Mata pelajaran yang Anda buat hanya akan terlihat di "Materi Tambahan" Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
