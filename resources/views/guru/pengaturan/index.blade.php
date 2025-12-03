@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('guru.dashboard', $serial->id) }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pengaturan</li>
        </ol>
    </nav>

    <h4 class="fw-bold mb-4"><i class='bx bx-cog me-2'></i>Pengaturan Akun</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class='bx bx-user me-2'></i>Informasi Profile</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.pengaturan.profile', $serial->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4">
                            <img src="{{ $user->avatar ? asset('storage/avatars/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=7367f0&color=fff&size=100' }}" 
                                 alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar">
                            <div class="button-wrapper">
                                <label for="avatar" class="btn btn-primary me-2 mb-2" tabindex="0">
                                    <span class="d-none d-sm-block">Upload Foto Baru</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" id="avatar" name="avatar" class="account-file-input" hidden accept="image/png, image/jpeg, image/jpg">
                                </label>
                                <button type="button" class="btn btn-outline-secondary account-image-reset mb-2">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                                <p class="text-muted mb-0">Format: JPG, JPEG, PNG. Max 2MB</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class='bx bx-save me-1'></i>Simpan Perubahan
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class='bx bx-reset me-1'></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class='bx bx-lock me-2'></i>Ubah Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.pengaturan.password', $serial->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-lock-alt me-1'></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class='bx bx-info-circle me-2'></i>Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-calendar me-2'></i>
                                <div>
                                    <small class="text-muted d-block">Bergabung Sejak</small>
                                    <strong>{{ $user->created_at->format('d M Y') }}</strong>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-time me-2'></i>
                                <div>
                                    <small class="text-muted d-block">Login Terakhir</small>
                                    <strong>{{ $user->updated_at->format('d M Y H:i') }}</strong>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex align-items-center">
                                <i class='bx bx-shield-alt me-2'></i>
                                <div>
                                    <small class="text-muted d-block">Role</small>
                                    <strong class="text-capitalize">{{ $user->role ?? 'Guru' }}</strong>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Image upload preview
    document.getElementById('avatar').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('uploadedAvatar').src = event.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    });

    // Reset image
    document.querySelector('.account-image-reset').addEventListener('click', function() {
        document.getElementById('uploadedAvatar').src = '{{ $user->avatar ? asset("storage/avatars/" . $user->avatar) : "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&background=7367f0&color=fff&size=100" }}';
        document.getElementById('avatar').value = '';
    });
</script>
@endsection
