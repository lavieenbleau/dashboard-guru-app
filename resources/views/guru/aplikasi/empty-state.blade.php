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
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalAktivasiSerial" style="border-radius: 12px; padding: 0.8rem 2rem; font-weight: 600;">
                <i class="bx bx-key me-2"></i> Aktivasi Serial
            </button>
            <a href="https://tak-scimediaonline.my.id/layanan-pelanggan-pelapor" target="_blank" class="btn btn-primary btn-lg" style="border-radius: 12px; padding: 0.8rem 2rem; font-weight: 600; box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);">
                <i class="bx bxs-contact me-2"></i> Hubungi Admin
            </a>
        </div>
    </div>
</div>

<!-- Modal Aktivasi Serial -->
<div class="modal fade" id="modalAktivasiSerial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('guru.aplikasi.activate') }}" method="POST" id="formAktivasiSerial">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Aktivasi Serial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Masukkan kode serial yang diberikan admin atau dari hasil pembelian untuk menambahkan aplikasi ke akun Anda.</p>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Kode Serial <span class="text-danger">*</span></label>
                        <input type="text" name="serial_code" class="form-control form-control-lg text-center fw-bold" placeholder="Contoh: SCI-XXXX-XXXX-XXXX" required style="letter-spacing: 2px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-check-circle me-1'></i> Aktivasi Serial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            if (typeof showSuccess === 'function') {
                showSuccess("{{ session('success') }}");
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}"
                });
            }
        @endif

        @if(session('error'))
            if (typeof showError === 'function') {
                showError("{{ session('error') }}");
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "{{ session('error') }}"
                });
            }
        @endif

        const activationForm = document.getElementById('formAktivasiSerial');
        if (activationForm) {
            activationForm.addEventListener('submit', function(e) {
                const btnSubmit = this.querySelector('button[type="submit"]');
                const originalText = btnSubmit.innerHTML;
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';
            });
        }
    });
</script>
@endsection
