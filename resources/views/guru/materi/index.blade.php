@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4 edu-wrapper">
    <div class="edu-page-bg">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3" style="border-bottom: 1px solid #E5E7EB;">
            <div>
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard', $serial->id) }}" class="text-sub text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active text-main fw-medium">Materi</li>
                    </ol>
                </nav>
                <h4 class="mb-1 text-main fw-bold">Materi – {{ $serial->product->name }}</h4>
                <p class="text-sub mb-0" style="font-size: 0.9rem;">Pilih kategori materi yang ingin diakses dan kelola untuk kelas Anda.</p>
            </div>
            <a href="{{ route('guru.dashboard', $serial->id) }}" class="btn-edu btn-edu-outline text-decoration-none">
                <i class='bx bx-home me-2'></i> Dashboard
            </a>
        </div>

        <!-- Category Cards -->
        <div class="row g-4 mb-2">
            <!-- Materi dari Admin -->
            <div class="col-md-6">
                <a href="{{ route('guru.materi.admin', $serial->id) }}" class="text-decoration-none">
                    <div class="edu-card h-100 item-card-hover" style="border-bottom: 4px solid #4F46E5 !important;">
                        <div class="edu-card-body text-center py-5">
                            <div class="mb-4 mx-auto" style="width: 80px; height: 80px; background-color: #EEF2FF; border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1);">
                                <i class='bx bx-library text-indigo' style="font-size: 40px;"></i>
                            </div>
                            <h4 class="mb-2 text-main fw-bold">Materi Modul Utama</h4>
                            <p class="text-sub mb-3">Materi kurikulum yang disediakan oleh administrator pusat</p>
                            <span class="edu-badge badge-indigo border border-light">Dari Admin</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Materi Tambahan (Buatan Guru) -->
            <div class="col-md-6">
                <a href="{{ route('guru.materi.custom', $serial->id) }}" class="text-decoration-none">
                    <div class="edu-card h-100 item-card-hover" style="border-bottom: 4px solid #06B6D4 !important;">
                        <div class="edu-card-body text-center py-5">
                            <div class="mb-4 mx-auto" style="width: 80px; height: 80px; background-color: #ECFEFF; border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(6, 182, 212, 0.1);">
                                <i class='bx bx-book-add text-cyan' style="font-size: 40px;"></i>
                            </div>
                            <h4 class="mb-2 text-main fw-bold">Materi Tambahan</h4>
                            <p class="text-sub mb-3">Materi pendukung atau tugas yang Anda buat secara mandiri</p>
                            <span class="edu-badge badge-cyan border border-light">Buatan Guru</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.item-card-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.item-card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
}
</style>
@endsection