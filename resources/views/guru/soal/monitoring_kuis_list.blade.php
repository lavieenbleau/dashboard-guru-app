@extends('layouts.sneat')
@section('title', 'Dashboard Monitoring Kuis')
@section('content')

<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.monitoring-quiz') }}">Monitoring Kuis</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.monitoring-quiz.products', $kelasName) }}">{{ $kelasName }}</a></li>
            <li class="breadcrumb-item active">{{ $productName }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1"><i class='bx bx-desktop text-primary me-2'></i>Dashboard Monitoring Kuis</h3>
            <p class="text-muted mb-0">Memantau seluruh kuis pada mata pelajaran <strong>{{ $productName }}</strong> di kelas <strong>{{ $kelasName }}</strong>.</p>
        </div>
    </div>

    
    </div>

    @if(isset($dbError) && $dbError)
    <div class="alert alert-danger alert-dismissible mb-4" role="alert">
        <h6 class="alert-heading mb-1"><i class="bx bx-error-circle"></i> Koneksi Database Log Bermasalah</h6>
        <span>{{ $dbError }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <label class="form-label d-block fw-bold"><i class='bx bx-filter-alt'></i> Filter Kategori</label>
                    <div class="d-flex flex-wrap gap-2" id="filterCategoryContainer">
                        <button class="btn btn-primary btn-sm filter-cat" data-cat="all">Semua</button>
                        @foreach($groupedExercises->keys() as $cat)
                            <button class="btn btn-outline-primary btn-sm filter-cat" data-cat="{{ Str::slug($cat) }}">{{ $cat }}</button>
                        @endforeach
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Quiz Cards List -->
    <div class="row" id="quizContainer">
        @forelse($exercises as $ex)

            <div class="col-md-6 col-xl-4 mb-4 quiz-card" data-cat="{{ Str::slug($ex->category_name) }}" >
                <div class="card h-100 border border-{{ $ex->badge_color }}">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center pb-3">
                        <span class="badge bg-{{ $ex->badge_color }}">{{ $ex->category_name }}</span>
                        <small class="text-muted" title="{{ $ex->created_at }}">{{ $ex->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="card-body pt-3 pb-2">
                        <h5 class="card-title text-truncate" title="{{ $ex->title }}">{{ $ex->title }}</h5>
                        
                        @if($ex->kd_list->count() > 0)
                            <div class="mb-2">
                                <small class="text-muted d-block">KD:</small>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @foreach($ex->kd_list as $kd)
                                        <span class="badge bg-label-dark" style="font-size: 0.7rem;">{{ $kd }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <div class="mb-2 text-muted small">
                            <i class='bx bx-list-ol me-1'></i> {{ $ex->total_soal }} Soal
                        </div>
                        
                        @if($ex->shared_classes_count > 0)
                            <div class="mb-2 text-muted small">
                                <i class='bx bx-share-alt me-1'></i> Dibagikan ke: {{ $ex->shared_classes_count }} Kelas
                            </div>
                        @endif
                        
                        <div class="mb-2 text-muted small">
                            <i class='bx bx-calendar me-1'></i> Dibuat: {{ $ex->created_at->format('d M Y') }}
                        </div>
                    </div>
                    <div class="card-footer p-0">
                        <a href="{{ route('guru.monitoring-quiz.kuis-detail', [$kelasName, $serialModel->id, $ex->id]) }}{!! $lessonIdParam !!}" class="btn btn-primary w-100 rounded-top-0">
                            <i class='bx bx-search-alt me-1'></i> Monitoring Siswa
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class='bx bx-folder-open text-muted' style="font-size: 3rem;"></i>
                <p class="mt-2 text-muted">Belum ada kuis pada mata pelajaran ini.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const catButtons = document.querySelectorAll('.filter-cat');
        const statusButtons = document.querySelectorAll('.filter-status');
        const cards = document.querySelectorAll('.quiz-card');
        
        let currentCat = 'all';
        let currentStatus = 'all';
        
        function applyFilters() {
            cards.forEach(card => {
                let showCat = (currentCat === 'all' || card.getAttribute('data-cat') === currentCat);
                let showStatus = true;
                
                if (showCat && showStatus) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        catButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                catButtons.forEach(b => { b.classList.remove('btn-primary'); b.classList.add('btn-outline-primary'); });
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                currentCat = this.getAttribute('data-cat');
                applyFilters();
            });
        });
        
        statusButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                statusButtons.forEach(b => { b.classList.remove('btn-secondary'); b.classList.add('btn-outline-secondary'); });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-secondary');
                currentStatus = this.getAttribute('data-status');
                applyFilters();
            });
        });
    });
</script>
@endsection
