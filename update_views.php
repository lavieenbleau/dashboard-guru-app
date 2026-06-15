<?php

// 1. Update classes.blade.php
$classesFile = __DIR__ . '/resources/views/guru/monitoring-quiz/classes.blade.php';
$classesContent = file_get_contents($classesFile);
$classesSearch = <<<'EOD'
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center bg-label-success p-2 rounded">
                                <span class="fw-semibold text-success"><i class='bx bx-check-circle me-1'></i>Selesai</span>
                                <span class="badge bg-success">{{ $stat['finished'] }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center bg-label-primary p-2 rounded">
                                <span class="fw-semibold text-primary"><i class='bx bx-time-five me-1'></i>Sedang Mengerjakan</span>
                                <span class="badge bg-primary">{{ $stat['in_progress'] }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center bg-label-secondary p-2 rounded">
                                <span class="fw-semibold text-secondary"><i class='bx bx-minus-circle me-1'></i>Belum Mengerjakan</span>
                                <span class="badge bg-secondary">{{ $stat['not_started'] }}</span>
                            </div>
                        </div>
                    </div>
EOD;
$classesContent = str_replace($classesSearch, '', $classesContent);
file_put_contents($classesFile, $classesContent);

// 2. Update products.blade.php
$productsFile = __DIR__ . '/resources/views/guru/monitoring-quiz/products.blade.php';
$productsContent = file_get_contents($productsFile);

$productsSearch = <<<'EOD'
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center bg-label-success p-2 rounded">
                                <span class="fw-semibold text-success"><i class='bx bx-check-circle me-1'></i>Selesai</span>
                                <span class="badge bg-success">{{ $stat['finished'] }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center bg-label-primary p-2 rounded">
                                <span class="fw-semibold text-primary"><i class='bx bx-time-five me-1'></i>Sedang Mengerjakan</span>
                                <span class="badge bg-primary">{{ $stat['in_progress'] }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center bg-label-secondary p-2 rounded">
                                <span class="fw-semibold text-secondary"><i class='bx bx-minus-circle me-1'></i>Belum Mengerjakan</span>
                                <span class="badge bg-secondary">{{ $stat['not_started'] }}</span>
                            </div>
                        </div>
                    </div>
EOD;

$productsReplace = <<<'EOD'
                    <div class="mb-4 text-center">
                        <h4 class="mb-1 text-primary">{{ $stat['total_quiz'] ?? 0 }}</h4>
                        <p class="mb-0 text-muted small">Kuis Tersedia</p>
                        @if(isset($stat['last_update']) && $stat['last_update'])
                            <p class="mb-0 mt-2 text-muted small"><i class='bx bx-calendar-edit me-1'></i>Terakhir Update: {{ \Carbon\Carbon::parse($stat['last_update'])->format('d M Y') }}</p>
                        @endif
                    </div>
EOD;
$productsContent = str_replace($productsSearch, $productsReplace, $productsContent);
file_put_contents($productsFile, $productsContent);


// 3. Update monitoring_kuis_list.blade.php
$kuisListFile = __DIR__ . '/resources/views/guru/soal/monitoring_kuis_list.blade.php';
$kuisListContent = file_get_contents($kuisListFile);

// Hapus bagian Summary Statistics
$kuisListContent = preg_replace('/<!-- Summary Statistics -->.*?<\/div>\s*<\/div>/s', '', $kuisListContent);
// Hapus div Filter Status Kuis
$kuisListContent = preg_replace('/<div class="col-md-6">\s*<label class="form-label d-block fw-bold"><i class=\'bx bx-pulse\'>.*?<\/div>\s*<\/div>/s', '', $kuisListContent);
// Perbaiki grid column untuk filter category dari col-md-6 jadi col-12
$kuisListContent = str_replace('<div class="col-md-6 mb-3 mb-md-0">', '<div class="col-12">', $kuisListContent);
$kuisListContent = str_replace("let showStatus = (currentStatus === 'all' || card.getAttribute('data-status') === currentStatus);", "let showStatus = true;", $kuisListContent);

// Update isi Card Kuis
$cardSearch = <<<'EOD'
                        @if($ex->shared_classes->count() > 0)
                            <div class="mb-3 text-muted small">
                                <i class='bx bx-share-alt me-1'></i> Shared ke {{ $ex->shared_classes->count() }} Kelas: 
                                <span class="fst-italic">{{ Str::limit($ex->shared_classes->implode(', '), 30) }}</span>
                            </div>
                        @endif

                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="fw-bold">Progress Selesai</small>
                                <small>{{ $ex->stat_finished }} / {{ $ex->stat_total_students }} Siswa</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent }}%;" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">{{ $progressPercent }}%</small>
                                <small class="text-muted">Nilai Rata-rata: <strong class="text-primary">{{ $ex->stat_avg_score }}</strong></small>
                            </div>
                        </div>

                        <div class="row text-center mt-3 border-top pt-3">
                            <div class="col-4">
                                <span class="d-block text-success fw-bold">{{ $ex->stat_finished }}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Selesai</small>
                            </div>
                            <div class="col-4 border-start border-end">
                                <span class="d-block text-warning fw-bold">{{ $ex->stat_active }}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Mengerjakan</small>
                            </div>
                            <div class="col-4">
                                <span class="d-block text-secondary fw-bold">{{ $ex->stat_not_started }}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Belum</small>
                            </div>
                        </div>
EOD;

$cardReplace = <<<'EOD'
                        @if($ex->shared_classes_count > 0)
                            <div class="mb-2 text-muted small">
                                <i class='bx bx-share-alt me-1'></i> Dibagikan ke: {{ $ex->shared_classes_count }} Kelas
                            </div>
                        @endif
                        
                        <div class="mb-2 text-muted small">
                            <i class='bx bx-calendar me-1'></i> Dibuat: {{ $ex->created_at->format('d M Y') }}
                        </div>
EOD;

$kuisListContent = str_replace($cardSearch, $cardReplace, $kuisListContent);
// Remove status logic
$statusLogicSearch = <<<'EOD'
            @php
                $statusType = 'aktif';
                if ($ex->stat_finished == $ex->stat_total_students && $ex->stat_total_students > 0) {
                    $statusType = 'selesai';
                } elseif ($ex->stat_finished == 0 && $ex->stat_active == 0) {
                    $statusType = 'belum';
                }
                
                $progressPercent = $ex->stat_total_students > 0 ? round(($ex->stat_finished / $ex->stat_total_students) * 100) : 0;
            @endphp
EOD;
$kuisListContent = str_replace($statusLogicSearch, '', $kuisListContent);
$kuisListContent = str_replace('data-status="{{ $statusType }}"', '', $kuisListContent);

file_put_contents($kuisListFile, $kuisListContent);


// 4. Update monitoring.blade.php
$monitoringFile = __DIR__ . '/resources/views/guru/soal/monitoring.blade.php';
$monitoringContent = file_get_contents($monitoringFile);

// Hapus filter dan tambahkan summary
$filterSearch = <<<'EOD'
            <div class="row mb-4">
                <div class="col-md-8">
                    <label class="form-label">Filter Status</label>
                    <select id="filter_status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Sedang Mengerjakan">Sedang Mengerjakan</option>
                        <option value="Belum Mengerjakan">Belum Mengerjakan</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button id="btnFilter" class="btn btn-primary w-100">Terapkan Filter</button>
                </div>
            </div>
EOD;

$summaryStats = <<<'EOD'
            @php
                $progressPercent = $totalStudents > 0 ? round(($finishedCount / $totalStudents) * 100) : 0;
            @endphp
            
            <div class="row mb-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="card h-100 bg-label-primary shadow-none border border-primary">
                        <div class="card-body">
                            <h6 class="mb-3 text-primary">Progress Kuis</h6>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="fw-bold">{{ $finishedCount }} / {{ $totalStudents }} siswa selesai</small>
                            </div>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progressPercent }}%;" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            
                            <div class="row text-center mt-3 border-top border-primary pt-3">
                                <div class="col-4">
                                    <span class="d-block text-success fw-bold">{{ $finishedCount }}</span>
                                    <small class="text-primary" style="font-size: 0.75rem;">Selesai</small>
                                </div>
                                <div class="col-4 border-start border-end border-primary">
                                    <span class="d-block text-warning fw-bold">{{ $activeCount }}</span>
                                    <small class="text-primary" style="font-size: 0.75rem;">Mengerjakan</small>
                                </div>
                                <div class="col-4">
                                    <span class="d-block text-secondary fw-bold">{{ $notStartedCount }}</span>
                                    <small class="text-primary" style="font-size: 0.75rem;">Belum</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card h-100 bg-label-info shadow-none border border-info">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <h6 class="mb-3 text-info text-center border-bottom border-info pb-2">Statistik Nilai</h6>
                            <div class="row text-center mt-2">
                                <div class="col-4">
                                    <span class="d-block text-info fw-bold fs-4">{{ round($highestScore) }}</span>
                                    <small class="text-info" style="font-size: 0.75rem;">Tertinggi</small>
                                </div>
                                <div class="col-4 border-start border-end border-info">
                                    <span class="d-block text-primary fw-bold fs-4">{{ round($averageScore) }}</span>
                                    <small class="text-info" style="font-size: 0.75rem;">Rata-rata</small>
                                </div>
                                <div class="col-4">
                                    <span class="d-block text-danger fw-bold fs-4">{{ round($lowestScore) }}</span>
                                    <small class="text-info" style="font-size: 0.75rem;">Terendah</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-8">
                    <label class="form-label">Filter Status</label>
                    <select id="filter_status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Sedang Mengerjakan">Sedang Mengerjakan</option>
                        <option value="Belum Mengerjakan">Belum Mengerjakan</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button id="btnFilter" class="btn btn-primary w-100">Terapkan Filter</button>
                </div>
            </div>
EOD;

$monitoringContent = str_replace($filterSearch, $summaryStats, $monitoringContent);
file_put_contents($monitoringFile, $monitoringContent);

echo "All views updated.";
