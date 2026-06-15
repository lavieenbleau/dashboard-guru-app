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
                                                    <button type="button" class="btn btn-sm btn-info rounded-pill px-3 shadow-sm btn-detail-siswa" data-student-id="{{ $data['student']->id }}" data-bs-toggle="modal" data-bs-target="#studentDetailModal"><i class="bx bx-detail me-1"></i>Detail</button>
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


<!-- Student Detail Modal -->
<div class="modal fade" id="studentDetailModal" tabindex="-1" aria-labelledby="studentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-primary" id="studentDetailModalLabel">Detail Penilaian Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="studentDetailModalBody">

                <!-- Content goes here -->
                <div id="studentDetailContent"></div>
            </div>
        </div>
    </div>
</div>

@section('page-script')
<script>
    // Tahap 2 - Verifikasi JSON
    window.studentDetails = @json($rekapData);
    window.uniqueColumns = @json($detailColumns);
    console.log('Student Details Loaded:', window.studentDetails);

    document.addEventListener('DOMContentLoaded', function() {
        const detailButtons = document.querySelectorAll('.btn-detail-siswa');
        const modalBody = document.getElementById('studentDetailContent');

        function getBadgeDetail(val, hero = false) {
            if (val === null || val === undefined) return '<span class="badge bg-label-secondary' + (hero ? ' fs-5' : '') + '">Belum Dinilai</span>';
            val = parseFloat(val);
            let bgClass = '';
            if (val >= 90) bgClass = 'bg-success';
            else if (val >= 80) bgClass = 'bg-primary';
            else if (val >= 70) bgClass = 'bg-warning';
            else bgClass = 'bg-danger';
            
            return '<span class="badge ' + bgClass + (hero ? ' fs-1 px-4 py-2' : '') + '">' + val + '</span>';
        }

        detailButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Tahap 4 - Debug Tombol
                const studentId = parseInt(this.getAttribute('data-student-id'));
                console.log('Clicked Student ID:', studentId);

                // Ensure it's an array
                let studentArray = Array.isArray(window.studentDetails) ? window.studentDetails : Object.values(window.studentDetails);

                // Tahap 5 - Verifikasi Pencarian Data
                const studentData = studentArray.find(s => s.student && String(s.student.id) === String(studentId));
                console.log('Found Student:', studentData);

                // Tahap 6 - Fallback Error
                if (!studentData) {
                    modalBody.innerHTML = `
                        <div class="alert alert-danger m-4">
                            Data siswa tidak ditemukan.
                        </div>
                    `;
                    return;
                }

                // Empty State
                if (studentData.nilai_akhir === null) {
                    modalBody.innerHTML = `
                        <div class="row"><div class="col-md-12">
                            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Siswa /</span> ${(studentData.student.name || 'Siswa')}</h4>
                            <div class="text-center py-5">
                                <i class='bx bx-info-circle text-muted fs-1 mb-2'></i>
                                <p class="text-muted mb-0">Belum terdapat data penilaian untuk siswa ini.</p>
                            </div>
                        </div></div>
                    `;
                    return;
                }

                // Render HTML Sederhana ke Kompleks
                let html = `
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="fw-bold py-3 mb-4">
                                <span class="text-muted fw-light">Siswa /</span> ${studentData.student.name}
                            </h4>

                            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                                <div class="row g-0">
                                    <div class="col-md-4 bg-primary text-white d-flex flex-column justify-content-center align-items-center p-4">
                                        <div class="avatar avatar-xl mb-3">
                                            <span class="avatar-initial rounded-circle bg-white text-primary fs-2 fw-bold">
                                                ${(studentData.student.name || 'NN').substring(0, 2).toUpperCase()}
                                            </span>
                                        </div>
                                        <h4 class="text-white fw-bold mb-1 text-center">${studentData.student.name}</h4>
                                        <p class="text-white-50 mb-0"><i class="bx bx-id-card me-1"></i>${studentData.student.nis || '-'}</p>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body h-100 d-flex flex-column justify-content-center p-5">
                                            <div class="text-center">
                                                <h6 class="text-muted text-uppercase fw-bold letter-spacing-1 mb-2">Nilai Akhir Keseluruhan</h6>
                                                ${getBadgeDetail(studentData.nilai_akhir, true)}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h5 class="fw-bold mb-3 mt-4 text-secondary">Rincian Penilaian</h5>
                            <div class="row g-3">
                `;

                const categories = [
                    { id: 'tasks', name: 'Tugas', icon: 'bx-task', color: 'secondary' },
                    { id: 'akm', name: 'AKM', icon: 'bx-brain', color: 'primary' },
                    { id: 'uh', name: 'Ulangan Harian', icon: 'bx-check-shield', color: 'success' },
                    { id: 'pts', name: 'PTS', icon: 'bx-file', color: 'warning' },
                    { id: 'pas', name: 'PAS', icon: 'bx-medal', color: 'danger' }
                ];

                categories.forEach(cat => {
                    let cols = window.uniqueColumns[cat.id];
                    let avgKey = cat.id === 'tasks' ? 'tugas' : cat.id;
                    let avgData = studentData[avgKey];

                    html += `
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                                    <h6 class="m-0 fw-bold text-${cat.color}"><i class='bx ${cat.icon} me-2 fs-5 align-middle'></i>Detail ${cat.name}</h6>
                                    <span class="badge bg-${cat.color} rounded-pill px-3 py-1 fw-normal">Rata-rata: ${avgData && avgData.avg !== null ? avgData.avg : '-'}</span>
                                </div>
                                <div class="card-body p-0">
                    `;

                    let colsArray = cols ? Object.values(cols) : [];
                    if (colsArray.length > 0) {
                        html += '<ul class="list-group list-group-flush">';
                        colsArray.forEach((col, idx) => {
                            let point = studentData.detail && studentData.detail[cat.id] ? studentData.detail[cat.id][col.id] : null;
                            html += `
                                <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-bottom-0 border-light">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-${cat.color} text-${cat.color} d-flex align-items-center justify-content-center rounded-circle fw-bold">
                                            ${idx + 1}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-semibold">${col.title}</h6>
                                        </div>
                                    </div>
                                    <div class="ms-3">${getBadgeDetail(point)}</div>
                                </li>
                            `;
                        });
                        html += '</ul>';
                    } else {
                        html += `
                            <div class="text-center py-5">
                                <i class='bx ${cat.icon} text-muted fs-1 mb-2'></i>
                                <p class="text-muted mb-0">Belum ada aktivitas ${cat.name}.</p>
                            </div>
                        `;
                    }

                    html += `
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += `
                            </div>
                        </div>
                    </div>
                `;

                modalBody.innerHTML = html;
            });
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection