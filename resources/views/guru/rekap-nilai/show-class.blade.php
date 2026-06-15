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

                        <ul class="nav nav-tabs mb-4" id="rekapTab" role="tablist">
                          <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="ringkasan-tab" data-bs-toggle="tab" data-bs-target="#ringkasan" type="button" role="tab" aria-controls="ringkasan" aria-selected="true">
                                <i class="bx bx-pie-chart-alt-2 me-1"></i> Ringkasan Nilai
                            </button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab" aria-controls="detail" aria-selected="false">
                                <i class="bx bx-table me-1"></i> Detail Penilaian
                            </button>
                          </li>
                        </ul>

                        <div class="tab-content p-0 shadow-none border-0" id="rekapTabContent">
                          <!-- TAB RINGKASAN -->
                          
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
                                                    <button type="button" class="btn btn-sm btn-info rounded-pill px-3 shadow-sm btn-detail-siswa" data-student-id="{{ $data['student']->id }}" data-bs-toggle="modal" data-bs-target="#studentDetailModal"><i class="bx bx-detail me-1"></i>Detail</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                          </div>

                          <!-- TAB DETAIL -->
                          <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                            <div class="mb-3 d-flex gap-2 flex-wrap">
                                <button class="btn btn-sm btn-dark filter-btn active" data-filter="all">Semua</button>
                                @if($detailColumns['tasks']->count() > 0)
                                <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="tasks">Tugas</button>
                                @endif
                                @if($detailColumns['akm']->count() > 0)
                                <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="akm">AKM</button>
                                @endif
                                @if($detailColumns['uh']->count() > 0)
                                <button class="btn btn-sm btn-outline-success filter-btn" data-filter="uh">UH</button>
                                @endif
                                @if($detailColumns['pts']->count() > 0)
                                <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="pts">PTS</button>
                                @endif
                                @if($detailColumns['pas']->count() > 0)
                                <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="pas">PAS</button>
                                @endif
                            </div>

                            <div class="table-responsive text-nowrap" style="overflow-x: auto; max-height: 600px;">
                                <table class="table table-bordered table-hover" style="min-width: 100%;">
                                    <thead class="table-light" style="position: sticky; top: 0; z-index: 20;">
                                        <tr>
                                            <th rowspan="2" class="text-center align-middle" style="width: 40px; position: sticky; left: 0; background-color: #f9f9f9; z-index: 30; border-right: 2px solid #ddd;">NO</th>
                                            <th rowspan="2" class="align-middle" style="min-width: 220px; position: sticky; left: 40px; background-color: #f9f9f9; z-index: 30; border-right: 2px solid #ddd;">NAMA SISWA</th>
                                            
                                            @if($detailColumns['tasks']->count() > 0)
                                                <th colspan="{{ $detailColumns['tasks']->count() }}" class="text-center category-tasks"><span class="badge bg-secondary">TUGAS</span></th>
                                            @endif
                                            @if($detailColumns['akm']->count() > 0)
                                                <th colspan="{{ $detailColumns['akm']->count() }}" class="text-center category-akm"><span class="badge bg-primary">AKM</span></th>
                                            @endif
                                            @if($detailColumns['uh']->count() > 0)
                                                <th colspan="{{ $detailColumns['uh']->count() }}" class="text-center category-uh"><span class="badge bg-success">UH</span></th>
                                            @endif
                                            @if($detailColumns['pts']->count() > 0)
                                                <th colspan="{{ $detailColumns['pts']->count() }}" class="text-center category-pts"><span class="badge bg-warning text-dark">PTS</span></th>
                                            @endif
                                            @if($detailColumns['pas']->count() > 0)
                                                <th colspan="{{ $detailColumns['pas']->count() }}" class="text-center category-pas"><span class="badge bg-danger">PAS</span></th>
                                            @endif
                                        </tr>
                                        <tr>
                                            @foreach(['tasks', 'akm', 'uh', 'pts', 'pas'] as $cat)
                                                @foreach($detailColumns[$cat] as $col)
                                                    <th class="text-center category-{{ $cat }}" title="{{ $col['title'] }}" data-bs-toggle="tooltip">
                                                        {{ \Illuminate\Support\Str::limit($col['title'], 15) }}
                                                    </th>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rekapData as $index => $data)
                                            <tr>
                                                <td class="text-center fw-bold" style="position: sticky; left: 0; background-color: #fff; z-index: 10; border-right: 2px solid #ddd;">{{ $index + 1 }}</td>
                                                <td style="position: sticky; left: 40px; background-color: #fff; z-index: 10; border-right: 2px solid #ddd;"><strong>{{ $data['student']->name }}</strong></td>
                                                
                                                @foreach(['tasks', 'akm', 'uh', 'pts', 'pas'] as $cat)
                                                    @foreach($detailColumns[$cat] as $col)
                                                        <td class="text-center category-{{ $cat }}">
                                                            @if(isset($data['detail'][$cat][$col['id']]))
                                                                {{ $data['detail'][$cat][$col['id']] }}
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light" style="position: sticky; bottom: 0; z-index: 20;">
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold" style="position: sticky; left: 0; background-color: #f9f9f9; z-index: 30; border-right: 2px solid #ddd;">RATA-RATA KELAS</td>
                                            
                                            @foreach(['tasks', 'akm', 'uh', 'pts', 'pas'] as $cat)
                                                @foreach($detailColumns[$cat] as $col)
                                                    <td class="text-center fw-bold text-primary category-{{ $cat }}">
                                                        @if(isset($detailAverages[$cat][$col['id']]))
                                                            {{ $detailAverages[$cat][$col['id']] }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                          </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Student Detail Modal -->
<div class="modal fade" id="studentDetailModal" tabindex="-1" aria-labelledby="studentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-primary" id="studentDetailModalLabel">Detail Penilaian Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="studentDetailModalBody">
                <!-- Spinner Loading State -->
                <div class="d-flex justify-content-center align-items-center py-5 my-5" id="studentDetailLoading">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <!-- Content goes here -->
                <div id="studentDetailContent" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailButtons = document.querySelectorAll('.btn-detail-siswa');
    const modalBody = document.getElementById('studentDetailContent');
    const loadingSpinner = document.getElementById('studentDetailLoading');
    const serialId = '{{ $serial->id }}';
    const classroomId = '{{ $classroom->id }}';
    const lessonId = '{{ $selectedLesson->id }}';

    detailButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            
            // Show loading, hide content
            loadingSpinner.style.display = 'flex';
            modalBody.style.display = 'none';
            modalBody.innerHTML = '';

            const url = `/aplikasi/${serialId}/rekap-nilai/kelas/${classroomId}/lesson/${lessonId}/student/${studentId}/ajax`;

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.text();
                })
                .then(html => {
                    modalBody.innerHTML = html;
                    loadingSpinner.style.display = 'none';
                    modalBody.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching student details:', error);
                    modalBody.innerHTML = `
                        <div class="alert alert-danger m-4" role="alert">
                            Gagal memuat detail siswa. Silakan coba lagi.
                        </div>
                    `;
                    loadingSpinner.style.display = 'none';
                    modalBody.style.display = 'block';
                });
        });
    });
});
</script>
@endsection
@endsection