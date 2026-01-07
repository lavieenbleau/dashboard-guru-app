@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item active">{{ $type === 'admin' ? 'Soal dari Admin' : 'Soal Saya' }}</li>
        </ol>
    </nav>

    <h4 class="mb-4">{{ $type === 'admin' ? 'Bank Soal dari Admin' : 'Soal Saya' }}</h4>

    <!-- Exercise Types Cards -->
    <div class="row g-4">
        @foreach($exerciseTypes as $exType)
        <div class="col-md-4">
            <a href="{{ route('guru.soal.list-by-type', [$serial->id, $type, $exType->id]) }}" class="text-decoration-none">
                <div class="card shadow-sm hover-shadow-lg transition h-100">
                    <div class="card-body text-center py-4">
                        <div class="avatar avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded 
                                {{ $exType->kode === 'UH' ? 'bg-label-primary' : '' }}
                                {{ $exType->kode === 'PTS' ? 'bg-label-warning' : '' }}
                                {{ $exType->kode === 'PAS' ? 'bg-label-danger' : '' }}">
                                <i class='bx {{ $exType->kode === 'UH' ? 'bx-edit' : ($exType->kode === 'PTS' ? 'bx-file' : 'bx-book') }} bx-lg'></i>
                            </span>
                        </div>
                        <h5 class="mb-2">{{ $exType->name }}</h5>
                        <span class="badge bg-label-secondary">{{ $exType->kode }}</span>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

<style>
.hover-shadow-lg {
    transition: all 0.3s ease;
}
.hover-shadow-lg:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
