@extends('layouts.sneat')
@section('title', 'Monitoring Kuis Siswa')
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
            <h3 class="mb-1"><i class='bx bx-desktop text-primary me-2'></i>Monitoring Aktivitas Siswa</h3>
            <p class="text-muted mb-0">Memantau aktivitas <strong>{{ $kelasName }}</strong> pada aplikasi <strong>{{ $productName }}</strong>.</p>
        </div>
        <div>
            <button class="btn btn-warning" id="btnBulkReminder" onclick="sendReminderBulk()">
                <i class='bx bx-bell me-1'></i>Ingatkan Semua yang Belum Mengerjakan
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-group"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $classroom->students->count() }}</h4>
                    </div>
                    <p class="mb-1">Total Siswa</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-double"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $finishedCount }}</h4>
                    </div>
                    <p class="mb-1">Selesai</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-time-five"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $activeCount }}</h4>
                    </div>
                    <p class="mb-1">Sedang Mengerjakan</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-secondary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-minus-circle"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $notStartedCount }}</h4>
                    </div>
                    <p class="mb-1">Belum Mengerjakan</p>
                </div>
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

    <!-- Alert Container for Reminders -->
    <div id="alertContainer"></div>

    <!-- Filters and Data Table -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Aktivitas Peserta</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('guru.monitoring-quiz.export-csv', [$kelasName, $serialModel->id]) }}{!! $lessonIdParam !!}" id="btnExportCsv" class="btn btn-success btn-sm">
                    <i class="bx bx-file me-1"></i> Export CSV
                </a>
                <a href="{{ route('guru.monitoring-quiz.export-pdf', [$kelasName, $serialModel->id]) }}{!! $lessonIdParam !!}" id="btnExportPdf" class="btn btn-danger btn-sm">
                    <i class="bx bxs-file-pdf me-1"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body mt-3">
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Filter Kuis</label>
                    <select id="filter_exercise" class="form-select">
                        <option value="">Semua Kuis</option>
                        @foreach($exercises as $ex)
                            <option value="{{ $ex->id }}">{{ $ex->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="monitoringTable">
                    <thead>
                        <tr>
                            <th>Peserta & Kuis</th>
                            <th>Status Siswa</th>
                            <th>Ringkasan Perilaku</th>
                            <th>Aktivitas Terakhir</th>
                            <th>Tingkat Risiko</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data populated by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Timeline -->
<div class="modal fade" id="modalTimeline" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTimelineTitle">Detail Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="timelineContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .timeline { list-style: none; padding: 0; margin: 0; }
    .timeline > li { position: relative; padding-bottom: 15px; border-left: 2px solid #e9ecef; padding-left: 15px; }
    .timeline > li::before { content: ''; position: absolute; left: -6px; top: 0; width: 10px; height: 10px; border-radius: 50%; background: #696cff; }
</style>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let dataTableInstance;

$(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';

    dataTableInstance = $('#monitoringTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('guru.monitoring-quiz.data', [$kelasName, $serialModel->id]) }}{!! $lessonIdParam !!}",
            data: function (d) {
                d.exercise_id = $('#filter_exercise').val();
            },
            dataSrc: function (json) {
                // Client side filtering for Status
                let statusFilter = $('#filter_status').val();
                if (statusFilter) {
                    return json.data.filter(function(item) {
                        return item.status_type === statusFilter;
                    });
                }
                return json.data;
            }
        },
        columns: [
            { 
                data: null,
                render: function(data, type, row) {
                    return `<strong>${row.student_name}</strong><br><small class="text-muted">${row.exercise_name}</small>`;
                }
            },
            { 
                data: 'status',
                render: function(data, type, row) {
                    let badge = 'bg-secondary';
                    if (row.status_type === 'Sedang Mengerjakan') { badge = 'bg-primary'; }
                    if (row.status_type === 'Selesai') { badge = 'bg-success'; }
                    
                    let submitBadge = row.submit_status.includes('Selesai') ? 'bg-success' : 'bg-secondary';
                    let html = `<span class="badge ${badge} mb-1">${data}</span>`;
                    
                    if (row.status_type !== 'Belum Mengerjakan') {
                        html += `<br><small class="text-muted">Pengumpulan: <span class="badge ${submitBadge}">${row.submit_status}</span></small>`;
                    }
                    return html;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    if (row.status_type === 'Belum Mengerjakan') return '-';
                    return `<div class="d-flex flex-column gap-1">
                                <small>Keluar Aplikasi: <strong class="${row.jml_background > 0 ? 'text-warning' : ''}">${row.jml_background}x</strong></small>
                                <small>Kembali ke Aplikasi: <strong class="text-success">${row.jml_resume}x</strong></small>
                                <small>Gangguan Koneksi: <strong>${row.jml_reconnected}x</strong></small>
                                <small>Klik Tombol Back: <strong class="${row.jml_blocked > 0 ? 'text-danger' : ''}">${row.jml_blocked}x</strong></small>
                                <small>Durasi Keluar: <strong class="text-danger">${row.total_away}</strong></small>
                            </div>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    if (row.status_type === 'Belum Mengerjakan') return '-';
                    return `<small><strong>${row.last_event}</strong></small><br>
                            <small class="text-muted"><i class="bx bx-time"></i> ${row.aktivitas_terakhir}</small>`;
                }
            },
            { 
                data: 'risk_level',
                render: function(data, type, row) {
                    if (row.status_type === 'Belum Mengerjakan') return '-';
                    let icon = data === 'Berisiko Tinggi' ? 'bx-error-circle' : (data === 'Perlu Perhatian' ? 'bx-error' : 'bx-check-circle');
                    return `<span class="badge bg-${row.risk_color}"><i class="bx ${icon}"></i> ${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    let btns = `<button class="btn btn-sm btn-info btn-detail mb-1 w-100" data-student="${row.student_id}" data-exercise="${row.exercise_id}"><i class="bx bx-list-ul"></i> Lihat Detail</button>`;
                    
                    if (row.status_type === 'Belum Mengerjakan') {
                        let isReminded = sessionStorage.getItem(`reminded_${row.student_id}_${row.exercise_id}`);
                        if (isReminded) {
                            btns += `<button class="btn btn-sm btn-secondary w-100" disabled><i class="bx bx-check"></i> Sudah Diingatkan</button>`;
                        } else {
                            btns += `<button class="btn btn-sm btn-warning btn-reminder w-100" data-student="${row.student_id}" data-student-name="${row.student_name}" data-exercise="${row.exercise_id}"><i class="bx bx-bell"></i> Kirim Reminder</button>`;
                        }
                    }
                    
                    return `<div class="d-flex flex-column">${btns}</div>`;
                }
            }
        ]
    });

    $('#btnFilter').click(function() {
        dataTableInstance.ajax.reload();
        
        let exId = $('#filter_exercise').val();
        let csvUrl = "{{ route('guru.monitoring-quiz.export-csv', [$kelasName, $serialModel->id]) }}{!! $lessonIdParam !!}";
        let pdfUrl = "{{ route('guru.monitoring-quiz.export-pdf', [$kelasName, $serialModel->id]) }}{!! $lessonIdParam !!}";
        
        if (exId) {
            let joinChar = csvUrl.includes('?') ? '&' : '?';
            csvUrl += joinChar + "exercise_id=" + exId;
            pdfUrl += joinChar + "exercise_id=" + exId;
        }
        
        $('#btnExportCsv').attr('href', csvUrl);
        $('#btnExportPdf').attr('href', pdfUrl);
    });

    setInterval(function() {
        dataTableInstance.ajax.reload(null, false);
    }, 10000);

    // Detail Modal
    $('#monitoringTable').on('click', '.btn-detail', function() {
        let studentId = $(this).data('student');
        let exerciseId = $(this).data('exercise');
        
        $('#timelineContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#modalTimeline').modal('show');

        $.ajax({
            url: `/monitoring-quiz/{{ $kelasName }}/{{ $serialModel->id }}/detail/${studentId}/${exerciseId}`,
            method: 'GET',
            success: function(res) {
                $('#timelineContent').html(res.html);
            },
            error: function() {
                $('#timelineContent').html('<p class="text-danger">Gagal memuat data.</p>');
            }
        });
    });

    // Individual Reminder
    $('#monitoringTable').on('click', '.btn-reminder', function() {
        let studentId = $(this).data('student');
        let exerciseId = $(this).data('exercise');
        let studentName = $(this).data('student-name');
        let btn = $(this);
        
        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('guru.monitoring-quiz.reminder', [$kelasName, $serialModel->id]) }}{!! $lessonIdParam !!}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                type: 'individual',
                student_id: studentId,
                exercise_id: exerciseId,
                student_name: studentName
            },
            success: function(res) {
                showAlert(res.message, 'success');
                sessionStorage.setItem(`reminded_${studentId}_${exerciseId}`, '1');
                dataTableInstance.ajax.reload(null, false);
            },
            error: function() {
                showAlert('Gagal mengirim reminder.', 'danger');
                btn.html('<i class="bx bx-bell"></i> Kirim Reminder').prop('disabled', false);
            }
        });
    });
});

function sendReminderBulk() {
    $('#btnBulkReminder').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...').prop('disabled', true);
    
    $.ajax({
        url: "{{ route('guru.monitoring-quiz.reminder', [$kelasName, $serialModel->id]) }}{!! $lessonIdParam !!}",
        method: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            type: 'bulk'
        },
        success: function(res) {
            showAlert(res.message, 'success');
            
            // Mark all "Belum Mengerjakan" as reminded in UI
            let data = dataTableInstance.rows().data().toArray();
            data.forEach(function(row) {
                if (row.status_type === 'Belum Mengerjakan') {
                    sessionStorage.setItem(`reminded_${row.student_id}_${row.exercise_id}`, '1');
                }
            });
            dataTableInstance.ajax.reload(null, false);
            
            $('#btnBulkReminder').html('<i class="bx bx-bell me-1"></i>Ingatkan Semua yang Belum Mengerjakan').prop('disabled', false);
        },
        error: function() {
            showAlert('Gagal mengirim reminder massal.', 'danger');
            $('#btnBulkReminder').html('<i class="bx bx-bell me-1"></i>Ingatkan Semua yang Belum Mengerjakan').prop('disabled', false);
        }
    });
}

function showAlert(message, type) {
    let alertHtml = `
    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
    $('#alertContainer').html(alertHtml);
    
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}
</script>
@endsection
