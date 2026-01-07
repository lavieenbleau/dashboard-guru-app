@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item active">{{ $categoryInfo['name'] }}</li>
        </ol>
    </nav>

    <h4 class="mb-4">{{ $categoryInfo['name'] }}</h4>

    <!-- Admin/Custom Selection Cards -->
    <div class="row g-4">
        <div class="col-md-6">
            <a href="{{ route('guru.soal.list-by-category', [$serial->id, $category, 'admin']) }}" class="text-decoration-none">
                <div class="card shadow-sm hover-shadow-lg transition h-100 border-{{ $categoryInfo['color'] }}">
                    <div class="card-body text-center py-5">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <span class="avatar-initial rounded bg-label-{{ $categoryInfo['color'] }}">
                                <i class='bx bx-shield-alt-2 bx-lg'></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Bank Soal dari Admin</h5>
                        <p class="text-muted mb-0">Soal yang disediakan oleh admin<br>(hanya bisa di-share ke kelas)</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-6">
            <a href="{{ route('guru.soal.list-by-category', [$serial->id, $category, 'custom']) }}" class="text-decoration-none">
                <div class="card shadow-sm hover-shadow-lg transition h-100 border-success">
                    <div class="card-body text-center py-5">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class='bx bx-edit-alt bx-lg'></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Soal Saya</h5>
                        <p class="text-muted mb-0">Soal yang dibuat sendiri<br>(dapat diedit & dihapus)</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.hover-shadow-lg {
    transition: all 0.3s ease;
}
.hover-shadow-lg:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
