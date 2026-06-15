@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">{{ $serial->name }} / Rekap Nilai / {{ $classroom->name }} /</span> {{ $student->name }}
            </h4>

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
            @endphp

            <div class="d-flex justify-content-between mb-4">
                <a href="{{ route('guru.rekapnilai.kelas', ['serial' => $serial->id, 'classroom' => $classroom->id]) }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali ke Kelas
                </a>
                <a href="{{ route('guru.rekapnilai.siswa.pdf', ['serial' => $serial->id, 'classroom' => $classroom->id, 'student' => $student->id]) }}" class="btn btn-success">
                    <i class="bx bxs-file-pdf me-1"></i> Download PDF
                </a>
            </div>

            <!-- Header Siswa -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3 mb-2 mb-sm-0">
                            <span class="text-muted d-block">Nama Siswa</span>
                            <strong class="fs-5">{{ $student->name }}</strong>
                        </div>
                        <div class="col-sm-3 mb-2 mb-sm-0">
                            <span class="text-muted d-block">NIS / NISN</span>
                            <strong>{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</strong>
                        </div>
                        <div class="col-sm-3 mb-2 mb-sm-0">
                            <span class="text-muted d-block">Kelas</span>
                            <strong>{{ $classroom->name }}</strong>
                        </div>
                        <div class="col-sm-3">
                            <span class="text-muted d-block">Mata Pelajaran Terkait</span>
                            <strong>{{ $lessonsForTasks->implode(', ') ?: 'Semua' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Nilai Akhir -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 text-white"><i class='bx bx-bar-chart-alt-2 me-2'></i>Ringkasan Nilai Akhir</h5>
                </div>
                <div class="card-body mt-3">
                    <div class="row text-center g-4">
                        <div class="col-6 col-md-2">
                            <h6 class="text-muted mb-2">Tugas</h6>
                            <div class="fs-4">{!! getBadgeRekap($rekapDetail['tugas']['avg']) !!}</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <h6 class="text-muted mb-2">AKM</h6>
                            <div class="fs-4">{!! getBadgeRekap($rekapDetail['akm']['avg']) !!}</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <h6 class="text-muted mb-2">UH</h6>
                            <div class="fs-4">{!! getBadgeRekap($rekapDetail['uh']['avg']) !!}</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <h6 class="text-muted mb-2">PTS</h6>
                            <div class="fs-4">{!! getBadgeRekap($rekapDetail['pts']['avg']) !!}</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <h6 class="text-primary fw-bold mb-2">NILAI AKHIR KESELURUHAN</h6>
                            <div class="fs-3">{!! getBadgeRekap($rekapDetail['nilai_akhir']) !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- TUGAS -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="m-0 text-primary">Detail Tugas</h6>
                            <span>Rata-rata: <strong>{!! getBadgeRekap($rekapDetail['tugas']['avg']) !!}</strong></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <tbody>
                                        @forelse($rekapDetail['tugas']['list'] as $idx => $item)
                                        <tr>
                                            <td class="px-3 py-2 text-muted" style="width: 30px;">{{ $idx + 1 }}</td>
                                            <td class="py-2">
                                                {{ $item['title'] }}
                                                <small class="d-block text-muted">{{ $item['lesson'] }}</small>
                                            </td>
                                            <td class="px-3 py-2 text-end fw-bold">{!! getBadgeRekap($item['point']) !!}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-3 py-3 text-center text-muted">Belum ada nilai tugas</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AKM -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="m-0 text-primary">Detail AKM</h6>
                            <span>Rata-rata: <strong>{!! getBadgeRekap($rekapDetail['akm']['avg']) !!}</strong></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <tbody>
                                        @forelse($rekapDetail['akm']['list'] as $idx => $item)
                                        <tr>
                                            <td class="px-3 py-2 text-muted" style="width: 30px;">{{ $idx + 1 }}</td>
                                            <td class="py-2">
                                                {{ $item['title'] }}
                                            </td>
                                            <td class="px-3 py-2 text-end fw-bold">{!! getBadgeRekap($item['point']) !!}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-3 py-3 text-center text-muted">Belum ada nilai AKM</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- UH -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="m-0 text-primary">Detail Ulangan Harian</h6>
                            <span>Rata-rata: <strong>{!! getBadgeRekap($rekapDetail['uh']['avg']) !!}</strong></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <tbody>
                                        @forelse($rekapDetail['uh']['list'] as $idx => $item)
                                        <tr>
                                            <td class="px-3 py-2 text-muted" style="width: 30px;">{{ $idx + 1 }}</td>
                                            <td class="py-2">
                                                {{ $item['title'] }}
                                            </td>
                                            <td class="px-3 py-2 text-end fw-bold">{!! getBadgeRekap($item['point']) !!}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-3 py-3 text-center text-muted">Belum ada nilai UH</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PTS -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="m-0 text-primary">Detail PTS</h6>
                            <span>Rata-rata: <strong>{!! getBadgeRekap($rekapDetail['pts']['avg']) !!}</strong></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <tbody>
                                        @forelse($rekapDetail['pts']['list'] as $idx => $item)
                                        <tr>
                                            <td class="px-3 py-2 text-muted" style="width: 30px;">{{ $idx + 1 }}</td>
                                            <td class="py-2">
                                                {{ $item['title'] }}
                                            </td>
                                            <td class="px-3 py-2 text-end fw-bold">{!! getBadgeRekap($item['point']) !!}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-3 py-3 text-center text-muted">Belum ada nilai PTS</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PAS -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="m-0 text-primary">Detail PAS</h6>
                            <span>Rata-rata: <strong>{!! getBadgeRekap($rekapDetail['pas']['avg']) !!}</strong></span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <tbody>
                                        @forelse($rekapDetail['pas']['list'] as $idx => $item)
                                        <tr>
                                            <td class="px-3 py-2 text-muted" style="width: 30px;">{{ $idx + 1 }}</td>
                                            <td class="py-2">
                                                {{ $item['title'] }}
                                            </td>
                                            <td class="px-3 py-2 text-end fw-bold">{!! getBadgeRekap($item['point']) !!}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-3 py-3 text-center text-muted">Belum ada nilai PAS</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection