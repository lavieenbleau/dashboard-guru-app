<?php
$bladePath = __DIR__ . '/resources/views/guru/rekap-nilai/show-class.blade.php';

$content = <<<'EOD'
@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">{{ $serial->name }} / Rekap Nilai /</span> {{ $classroom->name }}
            </h4>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Rekap Nilai - {{ $classroom->name }}</h5>
                        <p class="text-muted mb-0">Ringkasan nilai mata pelajaran {{ $selectedLesson->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('guru.rekapnilai.kelas.pdf', ['serial' => $serial->id, 'classroom' => $classroom->id, 'lesson_id' => $selectedLesson->id]) }}" 
                           class="btn btn-success me-2">
                            <i class="bx bxs-file-pdf me-1"></i>
                            Download PDF
                        </a>
                        <a href="{{ route('guru.rekapnilai.kelas', ['serial' => $serial->id, 'classroom' => $classroom->id]) }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($students->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            Belum ada siswa di kelas ini.
                        </div>
                    @else
                        @php
                            if (!function_exists('getBadgeRekap')) {
                                function getBadgeRekap($val) {
                                    if (is_null($val)) return '<span class="text-muted">-</span>';
                                    $val = (float)$val;
                                    if ($val >= 90) return '<span class="badge bg-success">'.$val.'</span>';
                                    if ($val >= 80) return '<span class="badge bg-primary">'.$val.'</span>';
                                    if ($val >= 70) return '<span class="badge bg-warning">'.$val.'</span>';
                                    return '<span class="badge bg-danger">'.$val.'</span>';
                                }
                            }
                            if (!function_exists('formatScoreRekap')) {
                                function formatScoreRekap($data) {
                                    if (is_null($data['avg'])) return '<span class="text-muted">-</span>';
                                    return getBadgeRekap($data['avg']) . ' <small class="text-muted">('.$data['count'].')</small>';
                                }
                            }
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="card h-100 bg-lighter border">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary"><i class='bx bx-user me-2'></i>Statistik Siswa</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2 d-flex justify-content-between"><span>Total Siswa:</span> <strong>{{ $stats['total_siswa'] }}</strong></li>
                                            <li class="mb-2 d-flex justify-content-between"><span>Sudah Dinilai:</span> <strong class="text-success">{{ $stats['sudah_dinilai'] }}</strong></li>
                                            <li class="d-flex justify-content-between"><span>Belum Dinilai:</span> <strong class="text-danger">{{ $stats['belum_dinilai'] }}</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 bg-lighter border">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary"><i class='bx bx-line-chart me-2'></i>Statistik Nilai</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2 d-flex justify-content-between align-items-center"><span>Rata-rata Kelas:</span> <span>{!! getBadgeRekap($stats['rata_kelas']) !!}</span></li>
                                            <li class="mb-2 d-flex justify-content-between align-items-center"><span>Nilai Tertinggi:</span> <span>{!! getBadgeRekap($stats['tertinggi']) !!}</span></li>
                                            <li class="d-flex justify-content-between align-items-center"><span>Nilai Terendah:</span> <span>{!! getBadgeRekap($stats['terendah']) !!}</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center align-middle" style="width: 40px;">NO</th>
                                        <th class="align-middle">NAMA SISWA</th>
                                        <th class="text-center align-middle">TUGAS</th>
                                        <th class="text-center align-middle">AKM</th>
                                        <th class="text-center align-middle">UH</th>
                                        <th class="text-center align-middle">PTS</th>
                                        <th class="text-center align-middle">PAS</th>
                                        <th class="text-center align-middle">NILAI AKHIR</th>
                                        <th class="text-center align-middle">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapData as $index => $data)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td><strong>{{ $data['student']->name }}</strong></td>
                                            <td class="text-center">{!! formatScoreRekap($data['tugas']) !!}</td>
                                            <td class="text-center">{!! formatScoreRekap($data['akm']) !!}</td>
                                            <td class="text-center">{!! formatScoreRekap($data['uh']) !!}</td>
                                            <td class="text-center">{!! formatScoreRekap($data['pts']) !!}</td>
                                            <td class="text-center">{!! formatScoreRekap($data['pas']) !!}</td>
                                            <td class="text-center">{!! getBadgeRekap($data['nilai_akhir']) !!}</td>
                                            <td class="text-center">
                                                <a href="{{ route('guru.rekapnilai.siswa', ['serial' => $serial->id, 'classroom' => $classroom->id, 'student' => $data['student']->id]) }}" 
                                                   class="btn btn-sm btn-info">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
EOD;

file_put_contents($bladePath, $content);
echo "show-class.blade.php updated.\n";
