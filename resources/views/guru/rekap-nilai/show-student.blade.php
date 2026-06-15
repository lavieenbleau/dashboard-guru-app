@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">{{ $serial->name }} / Rekap Nilai / {{ $classroom->name }} /</span> {{ $student->name }}
            </h4>

            @php
                if (!function_exists('getBadgeDetail')) {
                    function getBadgeDetail($val, $hero = false) {
                        if (is_null($val)) return '<span class="badge bg-label-secondary' . ($hero ? ' fs-5' : '') . '">Belum Dinilai</span>';
                        $val = (float)$val;
                        $class = '';
                        if ($val >= 90) $class = 'bg-success';
                        elseif ($val >= 80) $class = 'bg-primary';
                        elseif ($val >= 70) $class = 'bg-warning';
                        else $class = 'bg-danger';
                        
                        return '<span class="badge ' . $class . ($hero ? ' fs-1 px-4 py-2' : '') . '">'.$val.'</span>';
                    }
                }
            @endphp

            <div class="d-flex justify-content-between mb-4">
                <a href="{{ route('guru.rekapnilai.kelas', ['serial' => $serial->id, 'classroom' => $classroom->id]) }}" class="btn btn-secondary shadow-sm">
                    <i class="bx bx-arrow-back me-1"></i> Kembali ke Kelas
                </a>
                <a href="{{ route('guru.rekapnilai.siswa.pdf', ['serial' => $serial->id, 'classroom' => $classroom->id, 'student' => $student->id]) }}" class="btn btn-success shadow-sm">
                    <i class="bx bxs-file-pdf me-1"></i> Download PDF
                </a>
            </div>

            <!-- Header Siswa -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body d-flex flex-column flex-md-row align-items-center">
                    <div class="avatar avatar-xl me-0 me-md-4 mb-3 mb-md-0 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle fw-bold shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($student->name, 0, 2)) }}
                    </div>
                    <div class="text-center text-md-start">
                        <h4 class="mb-1 fw-bold text-dark">{{ $student->name }}</h4>
                        <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3 text-muted mt-2">
                            <div><i class="bx bx-id-card me-1 text-primary"></i> {{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</div>
                            <div><i class="bx bx-building-house me-1 text-primary"></i> {{ $classroom->name }}</div>
                            <div><i class="bx bx-book-bookmark me-1 text-primary"></i> {{ $lessonsForTasks->implode(', ') ?: 'Semua' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Metric: Nilai Akhir & 5 Kategori -->
            <div class="row mb-4">
                <!-- Hero Card: Nilai Akhir -->
                <div class="col-12 col-lg-4 mb-4 mb-lg-0">
                    <div class="card h-100 bg-primary text-white shadow border-0 rounded-3 p-4 d-flex flex-column justify-content-center align-items-center text-center" style="background: linear-gradient(135deg, #696cff 0%, #5154cc 100%);">
                        <h5 class="text-white mb-4 fw-semibold"><i class='bx bx-bar-chart-alt-2 me-2'></i>Nilai Akhir Keseluruhan</h5>
                        <div class="mb-3">
                            {!! getBadgeDetail($rekapDetail['nilai_akhir'], true) !!}
                        </div>
                        <p class="mb-0 text-white-50 mt-3"><small>Diakumulasi dari seluruh aktivitas yang telah dinilai.</small></p>
                    </div>
                </div>
                
                <!-- 5 Mini Cards -->
                <div class="col-12 col-lg-8">
                    <div class="row g-3">
                        <!-- Tugas -->
                        <div class="col-6 col-md-4">
                            <div class="card shadow-sm border-0 border-start border-secondary border-4 h-100">
                                <div class="card-body px-3 py-4 text-center d-flex flex-column justify-content-center">
                                    <div class="text-secondary mb-2"><i class='bx bx-task fs-2'></i></div>
                                    <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.8rem;">Tugas</h6>
                                    <div class="fs-4">{!! getBadgeDetail($rekapDetail['tugas']['avg']) !!}</div>
                                </div>
                            </div>
                        </div>
                        <!-- AKM -->
                        <div class="col-6 col-md-4">
                            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                                <div class="card-body px-3 py-4 text-center d-flex flex-column justify-content-center">
                                    <div class="text-primary mb-2"><i class='bx bx-brain fs-2'></i></div>
                                    <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.8rem;">AKM</h6>
                                    <div class="fs-4">{!! getBadgeDetail($rekapDetail['akm']['avg']) !!}</div>
                                </div>
                            </div>
                        </div>
                        <!-- UH -->
                        <div class="col-6 col-md-4">
                            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                                <div class="card-body px-3 py-4 text-center d-flex flex-column justify-content-center">
                                    <div class="text-success mb-2"><i class='bx bx-check-shield fs-2'></i></div>
                                    <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.8rem;">Ulangan Harian</h6>
                                    <div class="fs-4">{!! getBadgeDetail($rekapDetail['uh']['avg']) !!}</div>
                                </div>
                            </div>
                        </div>
                        <!-- PTS -->
                        <div class="col-6 col-md-6">
                            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                                <div class="card-body px-3 py-4 text-center d-flex flex-column justify-content-center">
                                    <div class="text-warning mb-2"><i class='bx bx-file fs-2'></i></div>
                                    <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.8rem;">PTS</h6>
                                    <div class="fs-4">{!! getBadgeDetail($rekapDetail['pts']['avg']) !!}</div>
                                </div>
                            </div>
                        </div>
                        <!-- PAS -->
                        <div class="col-6 col-md-6">
                            <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                                <div class="card-body px-3 py-4 text-center d-flex flex-column justify-content-center">
                                    <div class="text-danger mb-2"><i class='bx bx-medal fs-2'></i></div>
                                    <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.8rem;">PAS</h6>
                                    <div class="fs-4">{!! getBadgeDetail($rekapDetail['pas']['avg']) !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Cards -->
            <div class="row">
                <!-- TUGAS -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 fw-bold text-secondary"><i class='bx bx-task me-2 fs-5 align-middle'></i>Detail Tugas</h6>
                            <span class="badge bg-secondary rounded-pill px-3 py-1 fw-normal">Rata-rata: {{ round($rekapDetail['tugas']['avg'], 1) ?? '-' }}</span>
                        </div>
                        <div class="card-body p-0">
                            @if(count($rekapDetail['tugas']['list']) > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($rekapDetail['tugas']['list'] as $idx => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-bottom-0 border-light">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-secondary text-secondary d-flex align-items-center justify-content-center rounded-circle fw-bold">
                                            {{ $idx + 1 }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-semibold">{{ $item['title'] }}</h6>
                                            <small class="text-muted"><i class="bx bx-book-open me-1"></i>{{ $item['lesson'] }}</small>
                                        </div>
                                    </div>
                                    <div class="ms-3">{!! getBadgeDetail($item['point']) !!}</div>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <div class="text-center py-5">
                                <i class='bx bx-folder-open text-muted fs-1 mb-2'></i>
                                <p class="text-muted mb-0">Belum ada tugas yang dikerjakan.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- AKM -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 fw-bold text-primary"><i class='bx bx-brain me-2 fs-5 align-middle'></i>Detail AKM</h6>
                            <span class="badge bg-primary rounded-pill px-3 py-1 fw-normal">Rata-rata: {{ round($rekapDetail['akm']['avg'], 1) ?? '-' }}</span>
                        </div>
                        <div class="card-body p-0">
                            @if(count($rekapDetail['akm']['list']) > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($rekapDetail['akm']['list'] as $idx => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-bottom-0 border-light">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-primary text-primary d-flex align-items-center justify-content-center rounded-circle fw-bold">
                                            {{ $idx + 1 }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-semibold">{{ $item['title'] }}</h6>
                                        </div>
                                    </div>
                                    <div class="ms-3">{!! getBadgeDetail($item['point']) !!}</div>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <div class="text-center py-5">
                                <i class='bx bx-brain text-muted fs-1 mb-2'></i>
                                <p class="text-muted mb-0">Belum ada nilai AKM.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- UH -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 fw-bold text-success"><i class='bx bx-check-shield me-2 fs-5 align-middle'></i>Detail Ulangan Harian</h6>
                            <span class="badge bg-success rounded-pill px-3 py-1 fw-normal">Rata-rata: {{ round($rekapDetail['uh']['avg'], 1) ?? '-' }}</span>
                        </div>
                        <div class="card-body p-0">
                            @if(count($rekapDetail['uh']['list']) > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($rekapDetail['uh']['list'] as $idx => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-bottom-0 border-light">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-success text-success d-flex align-items-center justify-content-center rounded-circle fw-bold">
                                            {{ $idx + 1 }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-semibold">{{ $item['title'] }}</h6>
                                        </div>
                                    </div>
                                    <div class="ms-3">{!! getBadgeDetail($item['point']) !!}</div>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <div class="text-center py-5">
                                <i class='bx bx-check-shield text-muted fs-1 mb-2'></i>
                                <p class="text-muted mb-0">Belum ada nilai Ulangan Harian.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- PTS -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 fw-bold text-warning"><i class='bx bx-file me-2 fs-5 align-middle'></i>Detail PTS</h6>
                            <span class="badge bg-warning rounded-pill px-3 py-1 fw-normal text-dark">Rata-rata: {{ round($rekapDetail['pts']['avg'], 1) ?? '-' }}</span>
                        </div>
                        <div class="card-body p-0">
                            @if(count($rekapDetail['pts']['list']) > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($rekapDetail['pts']['list'] as $idx => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-bottom-0 border-light">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-warning text-warning d-flex align-items-center justify-content-center rounded-circle fw-bold">
                                            {{ $idx + 1 }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-semibold">{{ $item['title'] }}</h6>
                                        </div>
                                    </div>
                                    <div class="ms-3">{!! getBadgeDetail($item['point']) !!}</div>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <div class="text-center py-5">
                                <i class='bx bx-file text-muted fs-1 mb-2'></i>
                                <p class="text-muted mb-0">Belum ada nilai PTS.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- PAS -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 fw-bold text-danger"><i class='bx bx-medal me-2 fs-5 align-middle'></i>Detail PAS</h6>
                            <span class="badge bg-danger rounded-pill px-3 py-1 fw-normal">Rata-rata: {{ round($rekapDetail['pas']['avg'], 1) ?? '-' }}</span>
                        </div>
                        <div class="card-body p-0">
                            @if(count($rekapDetail['pas']['list']) > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($rekapDetail['pas']['list'] as $idx => $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-bottom-0 border-light">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-danger text-danger d-flex align-items-center justify-content-center rounded-circle fw-bold">
                                            {{ $idx + 1 }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-semibold">{{ $item['title'] }}</h6>
                                        </div>
                                    </div>
                                    <div class="ms-3">{!! getBadgeDetail($item['point']) !!}</div>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <div class="text-center py-5">
                                <i class='bx bx-medal text-muted fs-1 mb-2'></i>
                                <p class="text-muted mb-0">Belum ada nilai PAS.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection