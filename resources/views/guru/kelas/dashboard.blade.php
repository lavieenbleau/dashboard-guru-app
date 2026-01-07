@extends('layouts.sneat')

@section('title', 'Dashboard Kelas')

@section('content')
<div class="container-xxl py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.kelas.pilih', $serial->id) }}">Kelas</a></li>
            <li class="breadcrumb-item active">{{ $classroom->name }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class='bx bx-door-open text-info me-2'></i>{{ $classroom->name }}
            </h4>
            <p class="text-muted mb-0">
                @if($classroom->grade)
                    Tingkat {{ $classroom->grade }} • 
                @endif
                {{ $students->count() }} Siswa
            </p>
        </div>
        <div>
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalImportSiswa">
                <i class='bx bx-upload me-1'></i>Import CSV
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                <i class='bx bx-user-plus me-1'></i>Tambah Siswa
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="alert alert-warning alert-dismissible" role="alert">
            <h6 class="alert-heading mb-2">
                <i class='bx bx-error-circle me-1'></i>
                Peringatan Import
            </h6>
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Siswa</h5>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->nis ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-info">
                                                    {{ substr($student->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <strong>{{ $student->name }}</strong>
                                        </div>
                                    </td>
                                    <td><code>{{ $student->username }}</code></td>
                                    <td>{{ $student->email ?? '-' }}</td>
                                    <td>
                                        <code>{{ $student->password_text ?? '********' }}</code>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                <i class='bx bx-dots-vertical-rounded'></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="{{ route('guru.kelas.siswa.destroy', [$serial->id, $classroom->id, $student->id]) }}" method="POST" onsubmit="return confirm('Hapus siswa ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class='bx bx-trash me-1'></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class='bx bx-user-x display-1 text-muted'></i>
                    <h5 class="mt-3">Belum Ada Siswa</h5>
                    <p class="text-muted mb-3">Tambahkan siswa untuk kelas ini</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                        <i class='bx bx-user-plus me-1'></i>Tambah Siswa Pertama
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="modalTambahSiswa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Siswa Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('guru.kelas.siswa.store', [$serial->id, $classroom->id]) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror">
                        @error('nis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Akan digunakan sebagai username jika diisi</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="siswa@email.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="08123456789">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class='bx bx-info-circle me-1'></i>
                        Password default: <strong>12345678</strong>
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

<!-- Modal Import CSV -->
<div class="modal fade" id="modalImportSiswa" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Siswa dari CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('guru.kelas.siswa.import', [$serial->id, $classroom->id]) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading mb-2">
                            <i class='bx bx-info-circle me-1'></i>
                            Petunjuk Import CSV
                        </h6>
                        <ol class="mb-2">
                            <li>Download template CSV terlebih dahulu</li>
                            <li>Isi data siswa pada file CSV</li>
                            <li>Kolom yang tersedia: <code>nama</code>, <code>nis</code>, <code>email</code>, <code>telepon</code></li>
                            <li>Kolom <strong>nama</strong> wajib diisi</li>
                            <li>Upload file CSV yang sudah diisi</li>
                        </ol>
                        <a href="{{ route('guru.kelas.siswa.template', $serial->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class='bx bx-download me-1'></i>Download Template CSV
                        </a>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih File CSV <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control @error('csv_file') is-invalid @enderror" accept=".csv" required>
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: CSV (maksimal 2MB)</small>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <strong>Catatan:</strong>
                        <ul class="mb-0 mt-1">
                            <li>Password default untuk semua siswa: <code>12345678</code></li>
                            <li>Username akan dibuat otomatis dari NIS (jika ada) atau nama siswa</li>
                            <li>Siswa dengan username/NIS yang sudah ada akan dilewati</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class='bx bx-upload me-1'></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
