@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">{{ $serial->name }} / Rekap Nilai / {{ $classroom->name }} /</span> {{ $student->name }}
            </h4>

            <div class="mb-3">
                <a href="{{ route('guru.rekapnilai.kelas', ['serial' => $serial->id, 'classroom' => $classroom->id]) }}" 
                   class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i>
                    Kembali ke {{ $classroom->name }}
                </a>
                <a href="{{ route('guru.rekapnilai.siswa.pdf', ['serial' => $serial->id, 'classroom' => $classroom->id, 'student' => $student->id]) }}" 
                   class="btn btn-success">
                    <i class="bx bxs-file-pdf me-1"></i>
                    Download PDF
                </a>
            </div>

            <!-- Student Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg bg-label-primary me-3">
                            <span class="avatar-initial rounded-circle">{{ substr($student->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $student->name }}</h5>
                            <p class="text-muted mb-0">{{ $classroom->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-task me-2"></i>
                        Nilai Tugas
                    </h5>
                </div>
                <div class="card-body">
                    @if($tasks->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            Belum ada nilai tugas.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Paket Pembelajaran</th>
                                        <th>Judul Tugas</th>
                                        <th class="text-center" style="width: 100px;">Nilai</th>
                                        <th style="width: 150px;">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $index => $task)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @php
                                                    $cat = is_string($task->post->category) ? json_decode($task->post->category, true) : $task->post->category;
                                                    $lessonId = $cat['lesson_id'] ?? null;
                                                @endphp
                                                {{ $lessonId && isset($lessonsForTasks[$lessonId]) ? $lessonsForTasks[$lessonId] : ($task->post->mapel->name ?? '-') }}
                                            </td>
                                            <td>{{ $task->post->title ?? '-' }}</td>
                                            <td class="text-center">
                                                @if($task->point)
                                                    <span class="badge bg-primary">{{ $task->point }}</span>
                                                @else
                                                    <span class="text-muted">Belum dinilai</span>
                                                @endif
                                            </td>
                                            <td>{{ $task->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Rata-rata:</strong></td>
                                        <td class="text-center">
                                            <strong>
                                                @php
                                                    $avg = $tasks->where('point', '!=', null)->avg('point');
                                                @endphp
                                                @if($avg)
                                                    <span class="badge bg-primary">{{ round($avg, 1) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Exercise Points Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-edit me-2"></i>
                        Nilai Soal/Ujian
                    </h5>
                </div>
                <div class="card-body">
                    @if($exercisePoints->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            Belum ada nilai soal/ujian.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Paket Pembelajaran</th>
                                        <th>Kategori</th>
                                        <th>Judul Soal</th>
                                        <th class="text-center" style="width: 100px;">Nilai</th>
                                        <th style="width: 150px;">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exercisePoints as $index => $point)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $point->exercise->lesson->name ?? ($point->exercise->lesson->mapel->name ?? '-') }}</td>
                                            <td>
                                                @if($point->exercise->exerciseType)
                                                    <span class="badge bg-info">{{ $point->exercise->exerciseType->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $point->exercise->title }}</td>
                                            <td class="text-center">
                                                @if($point->exercise_point)
                                                    <span class="badge bg-success">{{ $point->exercise_point }}</span>
                                                @else
                                                    <span class="text-muted">Belum dinilai</span>
                                                @endif
                                            </td>
                                            <td>{{ $point->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Rata-rata:</strong></td>
                                        <td class="text-center">
                                            <strong>
                                                @php
                                                    $avg = $exercisePoints->where('exercise_point', '!=', null)->avg('exercise_point');
                                                @endphp
                                                @if($avg)
                                                    <span class="badge bg-success">{{ round($avg, 1) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
