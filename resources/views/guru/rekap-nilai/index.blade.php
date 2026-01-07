@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">{{ $serial->name }} /</span> Rekap Nilai
            </h4>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pilih Kelas</h5>
                    <p class="text-muted mb-0">Pilih kelas untuk melihat rekap nilai siswa</p>
                </div>
                <div class="card-body">
                    @if($classrooms->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            Belum ada kelas yang dibuat. Silakan buat kelas terlebih dahulu.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($classrooms as $classroom)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border shadow-sm">
                                        <div class="card-body d-flex flex-column">
                                            <div class="mb-3">
                                                <h5 class="card-title mb-1">{{ $classroom->name }}</h5>
                                                <p class="text-muted mb-0">
                                                    <i class="bx bx-user me-1"></i>
                                                    {{ $classroom->students()->count() }} Siswa
                                                </p>
                                            </div>
                                            <div class="mt-auto">
                                                <a href="{{ route('guru.rekapnilai.kelas', ['serial' => $serial->id, 'classroom' => $classroom->id]) }}" 
                                                   class="btn btn-primary w-100">
                                                    <i class="bx bx-file me-1"></i>
                                                    Lihat Rekap Nilai
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
