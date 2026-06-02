@extends('layouts.sneat')

@section('title', 'Belum Ada Paket Pembelajaran')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-8 col-lg-6 text-center">
        <!-- Icon -->
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 120px; height: 120px; background-color: rgba(105, 108, 255, 0.1);">
                <i class="bx bx-package" style="font-size: 60px; color: #696cff;"></i>
            </div>
        </div>

        <!-- Text -->
        <h3 class="fw-bold mb-3" style="color: #0F172A;">Belum Ada Paket Pembelajaran</h3>
        <p class="text-muted mb-4" style="font-size: 1.1rem; line-height: 1.6;">
            Akun Anda berhasil dibuat namun belum memiliki paket pembelajaran aktif.
            <br>Silakan hubungi Administrator SCI Media untuk mendapatkan akses Serial atau Paket Pembelajaran.
        </p>

        <!-- Action Button -->
        <a href="https://tak-scimediaonline.my.id/layanan-pelanggan-pelapor" target="_blank" class="btn btn-primary btn-lg" style="border-radius: 12px; padding: 0.8rem 2rem; font-weight: 600; box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);">
            <i class="bx bxs-contact me-2"></i> Hubungi Admin
        </a>
    </div>
</div>
@endsection
