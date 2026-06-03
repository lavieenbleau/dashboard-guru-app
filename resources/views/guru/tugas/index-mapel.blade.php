@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item active">Pilih Mata Pelajaran</li>
        </ol>
    </nav>
    <h4 class="fw-bold py-3 mb-4">
        Tugas / Pilih Mata Pelajaran
    </h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        @forelse($mapels as $mapel)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class='bx bx-folder-open fs-4'></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $mapel->name }}</h5>
                                <small class="text-muted">Jumlah Modul: {{ $mapel->lessons_count }}</small>
                            </div>
                        </div>
                        <a href="{{ route('guru.tugas.mapel', [$serial->id, $mapel->id]) }}" class="btn btn-warning btn-sm w-100">
                            <i class='bx bx-right-arrow-alt me-1'></i>Lihat Daftar Modul
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Belum ada mata pelajaran yang tersedia. Hubungi administrator untuk mendapatkan akses paket pembelajaran.
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
@endsection
