@extends('layouts.sneat')

@section('title', 'Pilih Kelas')

@section('content')
<div class="container-xxl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class='bx bx-group text-info me-2'></i>Kelola Kelas
        </h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
            <i class='bx bx-plus me-1'></i>Tambah Kelas
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        @forelse($classrooms as $classroom)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 hover-shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class='bx bx-door-open fs-4'></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $classroom->name }}</h5>
                                @if($classroom->grade)
                                    <small class="text-muted">Tingkat {{ $classroom->grade }}</small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            @php
                                $maxStudents = \App\Models\Classroom::MAX_STUDENTS;
                                $count = $classroom->students_count;
                                $isFull = $count >= $maxStudents;
                                $isOver = $count > $maxStudents;
                            @endphp
                            <span class="badge {{ $isOver ? 'bg-label-danger' : ($isFull ? 'bg-label-warning' : 'bg-label-primary') }}">
                                <i class='bx bx-user me-1'></i>{{ $count }} / {{ $maxStudents }} Siswa
                                @if($isOver) (Melebihi Kapasitas) @elseif($isFull) (Penuh) @endif
                            </span>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('guru.kelas.dashboard', [$serial->id, $classroom->id]) }}" class="btn btn-sm btn-info flex-grow-1">
                                <i class='bx bx-user-circle me-1'></i>Kelola Siswa
                            </a>
                            <form method="POST" action="{{ route('guru.kelas.destroy', [$serial->id, $classroom->id]) }}" onsubmit="return confirm('Hapus kelas ini?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class='bx bx-group display-1 text-muted'></i>
                        <h5 class="mt-3">Belum Ada Kelas</h5>
                        <p class="text-muted mb-3">Tambahkan kelas untuk mulai mengelola siswa</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                            <i class='bx bx-plus me-1'></i>Tambah Kelas Pertama
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modalTambahKelas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('guru.kelas.store', $serial->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Kelas 4A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tingkat / Grade</label>
                        <input type="text" name="grade" class="form-control" placeholder="Contoh: 4">
                        <small class="text-muted">Opsional</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-save me-1'></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
}
</style>
@endsection
