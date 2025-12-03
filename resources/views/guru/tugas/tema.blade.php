@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item active">{{ $mapel->name }}</li>
        </ol>
    </nav>

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ $mapel->name }} /</span> Pilih Tema
    </h4>

    <div class="row">
        @forelse($themes as $theme)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class='bx bx-folder fs-4'></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0 text-muted small">Tema {{ $theme->theme }}</p>
                                <h5 class="card-title mb-0">{{ $theme->name }}</h5>
                            </div>
                        </div>
                        <a href="{{ route('guru.tugas.subtema', [$serial->id, $mapel->id, $theme->id]) }}" class="btn btn-warning btn-sm w-100">
                            <i class='bx bx-right-arrow-alt me-1'></i>Lihat Sub Tema
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Belum ada tema untuk mata pelajaran ini.
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
