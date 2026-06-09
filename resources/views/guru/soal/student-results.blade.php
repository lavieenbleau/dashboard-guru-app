@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.lesson', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list-direct', [$serial->id, $lesson->id, 'tambahan']) }}">Soal Tambahan</a></li>
            <li class="breadcrumb-item active">Lihat Soal</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <!-- Exercise Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="mb-2">{{ $exercise->title }}</h4>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($exercise->lesson && $exercise->lesson->mapel)
                                    <span class="badge bg-label-info">
                                        <i class='bx bx-book me-1'></i>{{ $exercise->lesson->mapel->name }}
                                    </span>
                                @endif
                                
                                @if($exercise->exerciseType)
                                    <span class="badge bg-label-secondary">
                                        {{ $exercise->exerciseType->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nav Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('guru.soal.view-exercise', [$serial->id, $lesson->id, $exercise->id]) }}">
                        <i class="bx bx-list-ol me-1"></i> Detail Soal
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('guru.soal.student-results', [$serial->id, $lesson->id, $exercise->id]) }}">
                        <i class="bx bx-check-shield me-1"></i> Hasil Pengerjaan Siswa
                    </a>
                </li>
            </ul>

            <!-- Table Daftar Hasil Siswa -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Hasil Siswa</h5>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Jumlah Soal Dikerjakan</th>
                                <th>Sudah Dinilai</th>
                                <th>Total Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exercisePoints as $index => $point)
                                @php
                                    $answers = json_decode($point->answer, true) ?? [];
                                    $graded = json_decode($point->competence_point, true) ?? [];
                                    $totalAnswers = count($answers);
                                    $totalGraded = count($graded);
                                @endphp
                                <tr>
                                    <td>{{ $exercisePoints->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $point->student->name ?? 'Unknown Student' }}</strong>
                                        @if(isset($point->student->email))
                                            <br><small class="text-muted">{{ $point->student->email }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $totalAnswers }} Soal</td>
                                    <td>
                                        <span class="badge {{ $totalGraded >= $exercise->exerciseItems->count() ? 'bg-success' : 'bg-warning' }}">
                                            {{ $totalGraded }} / {{ $exercise->exerciseItems->count() }} Dinilai
                                        </span>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-primary">{{ $point->exercise_point ?? 0 }}</h6>
                                    </td>
                                    <td>
                                        <a href="{{ route('guru.soal.student-answer-detail', [$serial->id, $lesson->id, $exercise->id, $point->student_id]) }}" class="btn btn-sm btn-info">
                                            <i class="bx bx-detail me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="alert alert-warning mb-0">
                                            <i class='bx bx-info-circle me-2'></i>Belum ada siswa yang mengerjakan soal ini.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($exercisePoints->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $exercisePoints->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
