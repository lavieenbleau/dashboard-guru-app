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
                @if($isOverCapacity)
                    <span class="badge bg-danger ms-2" style="font-size: 0.6em; vertical-align: middle;">Melebihi Kapasitas</span>
                @elseif($isFull)
                    <span class="badge bg-warning ms-2" style="font-size: 0.6em; vertical-align: middle;">Penuh</span>
                @endif
            </h4>
            <p class="text-muted mb-0">
                @if($classroom->grade)
                    Tingkat {{ $classroom->grade }} • 
                @endif
                <span class="{{ $isOverCapacity ? 'text-danger fw-semibold' : ($isFull ? 'text-warning fw-semibold' : '') }}">
                    {{ $studentCount }} / {{ $maxStudents }} Siswa
                </span>
            </p>
        </div>
        <div>
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalImportSiswa" {{ $isFull ? 'disabled' : '' }}>
                <i class='bx bx-upload me-1'></i>Import CSV
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa" {{ $isFull ? 'disabled' : '' }}>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <i class='bx bx-error-circle me-1'></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($isOverCapacity)
        <div class="alert alert-danger" role="alert">
            <i class='bx bx-error-circle me-1'></i>
            <strong>Perhatian:</strong> Kelas ini memiliki {{ $studentCount }} siswa, melebihi batas maksimum {{ $maxStudents }} siswa.
            Penambahan siswa baru tidak diperbolehkan sampai jumlah siswa kembali ke batas normal.
        </div>
    @elseif($isFull)
        <div class="alert alert-warning" role="alert">
            <i class='bx bx-info-circle me-1'></i>
            Kelas sudah mencapai kapasitas maksimum {{ $maxStudents }} siswa.
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
                                        <x-action-dropdown>
                                                <li>
                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditSiswa{{ $student->id }}">
                                                        <i class='bx bx-edit me-1'></i> Edit
                                                    </button>
                                                </li>

                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('guru.kelas.siswa.destroy', [$serial->id, $classroom->id, $student->id]) }}" method="POST" onsubmit="confirmSubmit(event, 'Konfirmasi Hapus', 'Hapus siswa ini?', 'Ya, Hapus', true)">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class='bx bx-trash me-1'></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </x-action-dropdown>
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
                            <li>Kapasitas tersisa: <strong>{{ $maxStudents - $studentCount }}</strong> siswa (maks {{ $maxStudents }} per kelas)</li>
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

@foreach($students as $index => $student)

                                
                                <!-- Modal Edit Data Siswa -->
                                <div class="modal fade" id="modalEditSiswa{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content">
                                            
                                            <!-- Header Profile Section -->
                                            <div class="modal-header d-flex flex-column align-items-center position-relative pb-0 border-bottom-0">
                                                <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
                                                
                                                <div class="mb-3 mt-4">
                                                    @if($student->avatar)
                                                        <img src="{{ Storage::url($student->avatar) }}" alt="Avatar" class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                                    @else
                                                        <div class="avatar avatar-xl">
                                                            <span class="avatar-initial rounded-circle bg-primary">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <h4 class="fw-bold mb-1 text-center">{{ $student->name }}</h4>
                                                <p class="text-muted mb-1 text-center">
                                                    NIS: {{ $student->nis ?? '-' }} &bull; Username: {{ $student->username }} &bull; Kelas: {{ $classroom->name }}
                                                </p>
                                                <p class="text-muted mb-4 text-center">{{ $student->email ?? '-' }}</p>
                                            </div>

                                            <div class="modal-body pt-0">
                                                <form id="formSiswaModern{{ $student->id }}" action="{{ route('guru.kelas.siswa.update', [$serial->id, $classroom->id, $student->id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="row g-4">
                                                        <!-- Kolom Kiri -->
                                                        <div class="col-lg-6">
                                                            <div class="card h-100 mb-4">
                                                                <div class="card-header d-flex align-items-center justify-content-between">
                                                                    <h5 class="mb-0">Informasi Siswa</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                                        <input type="text" name="name" class="form-control" value="{{ $student->name }}" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">NIS</label>
                                                                        <input type="text" name="nis" class="form-control" value="{{ $student->nis }}">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Email</label>
                                                                        <input type="email" name="email" class="form-control" value="{{ $student->email }}">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">No. Telepon</label>
                                                                        <input type="text" name="phone" class="form-control" value="{{ $student->phone }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Kolom Kanan -->
                                                        <div class="col-lg-6">
                                                            <div class="card mb-4">
                                                                <div class="card-header d-flex align-items-center justify-content-between">
                                                                    <h5 class="mb-0">Akun Siswa</h5>
                                                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetPasswordSiswa{{ $student->id }}()">
                                                                        <i class='bx bx-refresh me-1'></i> Reset Password
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Username</label>
                                                                        <input type="text" class="form-control" value="{{ $student->username }}" readonly disabled>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Password Baru</label>
                                                                        <input type="password" id="newPassword{{ $student->id }}" class="form-control" placeholder="Kosongkan jika tidak diubah">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Konfirmasi Password</label>
                                                                        <input type="password" id="confirmPassword{{ $student->id }}" class="form-control" placeholder="Ketik ulang password baru">
                                                                        <small class="text-danger mt-1 d-none" id="errorPassword{{ $student->id }}">Password tidak cocok!</small>
                                                                    </div>
                                                                    <div class="form-check mt-2">
                                                                        <input class="form-check-input" type="checkbox" id="showPassword{{ $student->id }}" onclick="togglePassword{{ $student->id }}()">
                                                                        <label class="form-check-label" for="showPassword{{ $student->id }}">
                                                                            Tampilkan Password
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Informasi Sistem -->
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5 class="mb-0">Informasi Sistem</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <ul class="list-unstyled mb-0">
                                                                        <li class="d-flex justify-content-between mb-2">
                                                                            <span class="text-muted">ID Siswa</span>
                                                                            <span class="fw-semibold">#{{ $student->id }}</span>
                                                                        </li>
                                                                        <li class="d-flex justify-content-between mb-2">
                                                                            <span class="text-muted">Dibuat</span>
                                                                            <span class="fw-semibold">{{ $student->created_at->translatedFormat('d F Y') }}</span>
                                                                        </li>
                                                                        <li class="d-flex justify-content-between mb-2">
                                                                            <span class="text-muted">Terakhir Diubah</span>
                                                                            <span class="fw-semibold">{{ $student->updated_at->translatedFormat('d F Y') }}</span>
                                                                        </li>
                                                                        <li class="d-flex justify-content-between">
                                                                            <span class="text-muted">Status Akun</span>
                                                                            <span class="badge bg-label-success">Aktif</span>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                                <!-- Hidden Form for Password -->
                                                <form id="formPassword{{ $student->id }}" action="{{ route('guru.kelas.siswa.update-password', [$serial->id, $classroom->id, $student->id]) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="password" id="hiddenPasswordField{{ $student->id }}">
                                                </form>
                                            </div>
                                            
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="button" class="btn btn-primary" onclick="submitSiswaModern{{ $student->id }}()">Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    // Scripts scoped per student modal to handle logic
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const modalEl = document.getElementById('modalEditSiswa{{ $student->id }}');
                                        const modalContent = document.getElementById('modalContent{{ $student->id }}');
                                        
                                        // Animation on show
                                        modalEl.addEventListener('show.bs.modal', function () {
                                            setTimeout(() => {
                                                modalContent.style.opacity = '1';
                                                modalContent.style.transform = 'scale(1)';
                                            }, 10);
                                        });
                                        
                                        // Animation on hide
                                        modalEl.addEventListener('hide.bs.modal', function () {
                                            modalContent.style.opacity = '0';
                                            modalContent.style.transform = 'scale(0.95)';
                                        });
                                    });

                                    function togglePassword{{ $student->id }}() {
                                        const pwd1 = document.getElementById('newPassword{{ $student->id }}');
                                        const pwd2 = document.getElementById('confirmPassword{{ $student->id }}');
                                        const type = document.getElementById('showPassword{{ $student->id }}').checked ? 'text' : 'password';
                                        pwd1.type = type;
                                        pwd2.type = type;
                                    }

                                    function resetPasswordSiswa{{ $student->id }}() {
                                        showConfirm('Konfirmasi Tindakan', 'Reset password siswa ini? Password akan diubah ke standar: siswa1234.', 'Ya, Reset Password').then((result) => {
                                            if(result.isConfirmed) {
                                                const defaultPwd = 'siswa1234';
                                                document.getElementById('hiddenPasswordField{{ $student->id }}').value = defaultPwd;
                                                showSuccess('Password baru: ' + defaultPwd + '<br>Menyimpan perubahan...').then(() => {
                                                    document.getElementById('formPassword{{ $student->id }}').submit();
                                                });
                                            }
                                        });
                                    }

                                    function submitSiswaModern{{ $student->id }}() {
                                        const pwd1 = document.getElementById('newPassword{{ $student->id }}').value;
                                        const pwd2 = document.getElementById('confirmPassword{{ $student->id }}').value;
                                        const errorMsg = document.getElementById('errorPassword{{ $student->id }}');
                                        
                                        if (pwd1 !== '') {
                                            if (pwd1 !== pwd2) {
                                                errorMsg.classList.remove('d-none');
                                                return;
                                            }
                                            if (pwd1.length < 6) {
                                                errorMsg.textContent = 'Password minimal 6 karakter!';
                                                errorMsg.classList.remove('d-none');
                                                return;
                                            }
                                            
                                            errorMsg.classList.add('d-none');
                                            
                                            // Submitting password first via Fetch, then submitting the main form
                                            // This is to avoid changing backend logic while fulfilling UI requirements
                                            const pwdForm = document.getElementById('formPassword{{ $student->id }}');
                                            document.getElementById('hiddenPasswordField{{ $student->id }}').value = pwd1;
                                            
                                            fetch(pwdForm.action, {
                                                method: 'POST',
                                                body: new FormData(pwdForm)
                                            }).then(() => {
                                                document.getElementById('formSiswaModern{{ $student->id }}').submit();
                                            }).catch(err => {
                                                console.error(err);
                                                showError('Gagal menyimpan password. Menyimpan data utama...').then(() => {
                                                    document.getElementById('formSiswaModern{{ $student->id }}').submit();
                                                });
                                            });
                                            
                                        } else {
                                            // No password change, just submit main form
                                            document.getElementById('formSiswaModern{{ $student->id }}').submit();
                                        }
                                    }
                                </script>
@endforeach
@endsection
