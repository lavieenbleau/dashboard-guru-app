@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.onlineclass', $serial->id) }}">Online Class</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.onlineclass.tema', [$serial->id, $mapel->id]) }}">{{ $mapel->name }}</a></li>
            <li class="breadcrumb-item active">{{ $tema->name }}</li>
        </ol>
    </nav>

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ $tema->name }} /</span> Pilih Sub Tema
    </h4>

    <div class="row">
        @forelse($subthemes as $subtheme)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class='bx bx-book-content fs-4'></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0 text-muted small">Sub Tema {{ $subtheme->subtheme }}</p>
                                <h5 class="card-title mb-0">{{ $subtheme->name }}</h5>
                            </div>
                        </div>
                        <a href="{{ route('guru.onlineclass.list', [$serial->id, $mapel->id, $tema->id, $subtheme->id]) }}" class="btn btn-success btn-sm w-100">
                            <i class='bx bx-laptop me-1'></i>Lihat Online Class
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Belum ada sub tema untuk tema ini.
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
