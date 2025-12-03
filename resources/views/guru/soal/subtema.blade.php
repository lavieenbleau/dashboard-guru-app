@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Soal</a></li>
            <li class="breadcrumb-item active">{{ $categoryInfo['name'] }}</li>
        </ol>
    </nav>

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ $categoryInfo['name'] }} /</span> Pilih Mata Pelajaran
    </h4>

    <div class="row">
        @forelse($themes as $theme)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 hover-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-{{ $categoryInfo['color'] }}">
                                    <i class='bx bx-book-open fs-4'></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $theme->name }}</h5>
                            </div>
                        </div>
                        <a href="{{ route('guru.soal.list', [$serial->id, $category, $theme->id]) }}" class="btn btn-{{ $categoryInfo['color'] }} btn-sm w-100">
                            <i class='bx bx-file-blank me-1'></i>Lihat Soal
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Belum ada mata pelajaran.
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
