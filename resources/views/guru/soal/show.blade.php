@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.tema', [$serial->id, $category]) }}">{{ $categoryInfo['name'] }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list', [$serial->id, $category, $tema->id]) }}">{{ $tema->name }}</a></li>
            <li class="breadcrumb-item active">{{ $lesson->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-file-blank text-{{ $categoryInfo['color'] }} me-2'></i>{{ $lesson->name }}
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('guru.soal.edit', [$serial->id, $category, $tema->id, $lesson->id]) }}" class="btn btn-{{ $categoryInfo['color'] }}">
                <i class='bx bx-edit me-1'></i>Edit Soal
            </a>
            <a href="{{ route('guru.soal.list', [$serial->id, $category, $tema->id]) }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back me-1'></i>Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informasi Soal -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Informasi Soal</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Kategori</strong>
                            <span class="badge bg-label-{{ $categoryInfo['color'] }}">{{ $categoryInfo['name'] }}</span>
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Mata Pelajaran</strong>
                            {{ $tema->name }}
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted small">Dibuat</strong>
                            {{ $lesson->created_at->format('d M Y H:i') }}
                        </li>
                        <li class="mb-0">
                            <strong class="d-block text-muted small">Terakhir Diupdate</strong>
                            {{ $lesson->updated_at->format('d M Y H:i') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Statistik -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Statistik</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Total Siswa Mengerjakan</span>
                        <strong>{{ $totalSubmissions }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Rata-rata Nilai</span>
                        <strong>{{ $averageScore ? number_format($averageScore, 1) : '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Nilai Tertinggi</span>
                        <strong>{{ $highestScore ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Konten Soal -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Deskripsi Soal</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Belum ada deskripsi untuk soal ini. Klik tombol "Edit Soal" untuk menambahkan deskripsi.
                    </p>
                </div>
            </div>

            <!-- Daftar Pertanyaan/Soal -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Jawaban Siswa</h5>
                    <span class="badge bg-label-{{ $categoryInfo['color'] }}">{{ $totalSubmissions }} Siswa</span>
                </div>
                <div class="card-body">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Jawaban</th>
                                        <th>Waktu Pengumpulan</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $submission)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($submission->student->name ?? 'Student') }}&background=random&size=32" 
                                                         class="rounded-circle me-2" width="32" height="32">
                                                    <div>
                                                        <div class="fw-semibold">{{ $submission->student->name ?? 'Unknown' }}</div>
                                                        <small class="text-muted">{{ $submission->student->classroom->name ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;">
                                                    {{ Str::limit($submission->description, 100) }}
                                                </div>
                                                @if($submission->attachment)
                                                    <small class="text-muted">
                                                        <i class='bx bx-paperclip'></i> Ada lampiran
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $submission->created_at->format('d M Y') }}</small><br>
                                                <small class="text-muted">{{ $submission->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($submission->point)
                                                    <span class="badge bg-success">{{ $submission->point }}</span>
                                                @else
                                                    <span class="badge bg-warning">Belum Dinilai</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal{{ $submission->id }}">
                                                    <i class='bx bx-show'></i> Lihat
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Detail Modal -->
                                        <div class="modal fade" id="detailModal{{ $submission->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Jawaban {{ $submission->student->name ?? 'Student' }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Siswa:</label>
                                                            <p>{{ $submission->student->name ?? '-' }} ({{ $submission->student->classroom->name ?? '-' }})</p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Jawaban:</label>
                                                            <div class="border rounded p-3" style="background: #f8f9fa; white-space: pre-wrap;">{{ $submission->description }}</div>
                                                        </div>
                                                        @if($submission->attachment)
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Lampiran:</label>
                                                                <p><a href="{{ $submission->attachment }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    <i class='bx bx-download'></i> Lihat Lampiran
                                                                </a></p>
                                                            </div>
                                                        @endif
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Waktu Pengumpulan:</label>
                                                            <p>{{ $submission->created_at->format('d M Y H:i') }}</p>
                                                        </div>
                                                        <hr>
                                                        <form action="{{ route('guru.soal.grade', [$serial->id, $submission->id]) }}" method="POST">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="point{{ $submission->id }}" class="form-label fw-bold">Nilai:</label>
                                                                <input type="number" class="form-control" id="point{{ $submission->id }}" 
                                                                       name="point" value="{{ $submission->point }}" min="0" max="100" required>
                                                            </div>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class='bx bx-save'></i> Simpan Nilai
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class='bx bx-clipboard display-1 text-muted'></i>
                            <p class="text-muted mt-3">Belum ada siswa yang mengerjakan soal ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
