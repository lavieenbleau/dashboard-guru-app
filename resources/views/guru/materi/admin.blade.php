@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4 edu-wrapper">
    <div class="edu-page-bg">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3" style="border-bottom: 1px solid #E5E7EB;">
            <div>
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}" class="text-sub text-decoration-none">Materi</a></li>
                        <li class="breadcrumb-item active text-main fw-medium">Materi Admin</li>
                    </ol>
                </nav>
                <h4 class="mb-1 text-main fw-bold">Materi Pusat (Admin)</h4>
                <p class="text-sub mb-0" style="font-size: 0.9rem;">Telusuri dan kelola materi yang disediakan oleh administrator pusat.</p>
            </div>
            <a href="{{ route('guru.materi', $serial->id) }}" class="btn-edu btn-edu-outline text-decoration-none">
                <i class='bx bx-arrow-back me-2'></i> Kembali
            </a>
        </div>

        <!-- Lesson (Paket Pembelajaran) Cards -->
        <div class="row g-3">
            @forelse ($lessons as $lesson)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('guru.materi.admin.lessons', [$serial->id, $lesson->id]) }}" class="text-decoration-none">
                    <div class="edu-card h-100 item-card-hover">
                        <div class="edu-card-body d-flex flex-column h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="item-icon-box" style="width: 40px; height: 40px; font-size: 1.25rem;">
                                    <i class='bx bx-book-bookmark'></i>
                                </div>
                                <h5 class="mb-0 text-main fw-semibold" style="font-size: 1.05rem;">{{ $lesson->name }}</h5>
                            </div>
                            <div class="mb-3 text-sub" style="font-size: 0.85rem;">
                                Mata Pelajaran: <strong>{{ $lesson->mapel->name ?? '-' }}</strong>
                            </div>
                            <div class="mt-auto pt-2 d-flex justify-content-between align-items-center border-top" style="border-color: #F3F4F6 !important;">
                                <span class="text-sub" style="font-size: 0.85rem;">Lihat Modul & Materi</span>
                                <i class='bx bx-chevron-right text-indigo' style="font-size: 1.25rem;"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12">
                <div class="edu-card text-center py-5">
                    <div class="mx-auto mb-3" style="width: 48px; height: 48px; background-color: #F3F4F6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class='bx bx-info-circle text-sub fs-3'></i>
                    </div>
                    <h6 class="text-main fw-semibold mb-1">Modul Kosong</h6>
                    <p class="mb-0 text-sub" style="font-size: 0.9rem;">
                        Belum ada materi dari administrator pusat di jenjang ini.
                    </p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.item-card-hover {
    transition: all 0.2s ease;
}
.item-card-hover:hover {
    border-color: #C7D2FE;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transform: translateY(-2px);
}
</style>
@endsection
