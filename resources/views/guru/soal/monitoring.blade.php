@extends('layouts.sneat')
@section('title', 'Monitoring Kuis')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Soal /</span> Monitoring Kuis</h4>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-user-check"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $activeCount }}</h4>
                    </div>
                    <p class="mb-1">Peserta Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-double"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $finishedCount }}</h4>
                    </div>
                    <p class="mb-1">Total Submit</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-exit"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $totalAppBackground }}</h4>
                    </div>
                    <p class="mb-1">Pindah Tab / Keluar</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-log-in-circle"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $totalReconnected }}</h4>
                    </div>
                    <p class="mb-1">Masuk Kembali</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-danger"><i class="bx bx-error"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $totalSuspicious }}</h4>
                    </div>
                    <p class="mb-1">Tanda Mencurigakan</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="card card-border-shadow-secondary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-block"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $totalBlocked }}</h4>
                    </div>
                    <p class="mb-1">Klik Tombol Back</p>
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

    <!-- Filters and Data Table -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Aktivitas Peserta</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('guru.monitoring-quiz.export-csv', $serialModel->id) }}" id="btnExportCsv" class="btn btn-success btn-sm">
                    <i class="bx bx-file me-1"></i> Export CSV
                </a>
                <a href="{{ route('guru.monitoring-quiz.export-pdf', $serialModel->id) }}" id="btnExportPdf" class="btn btn-danger btn-sm">
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
                    <label class="form-label">Filter Tanggal</label>
                    <input type="date" id="filter_date" class="form-control">
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
                            <th>Mencurigakan</th>
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
$(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none'; // Mencegah alert default DataTables

    let table = $('#monitoringTable').DataTable({
        processing: true,
        serverSide: false, // Using client-side pagination with ajax data to allow 10s reload without resetting page easily, but we can do serverSide true if needed
        ajax: {
            url: "{{ route('guru.monitoring-quiz.data', $serialModel->id) }}",
            data: function (d) {
                d.exercise_id = $('#filter_exercise').val();
                d.date = $('#filter_date').val();
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
                    if (data === 'Sedang Mengerjakan') { badge = 'bg-primary'; }
                    if (data === 'Di Luar Aplikasi') { badge = 'bg-warning'; }
                    if (data === 'Selesai') { badge = 'bg-success'; }
                    return `<span class="badge ${badge} mb-1">${data}</span><br>
                            <small class="text-muted">Pengumpulan: <span class="badge ${row.submit_status === 'Selesai' ? 'bg-success' : 'bg-secondary'}">${row.submit_status}</span></small>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    return `<small>Keluar Aplikasi: <strong>${row.jml_background} Kali</strong></small><br>
                            <small>Masuk Kembali: <strong>${row.jml_reconnected} Kali</strong></small><br>
                            <small>Total Durasi Keluar: <strong class="text-danger">${row.total_away}</strong></small>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    return `<small><strong>${row.last_event}</strong></small><br>
                            <small class="text-muted"><i class="bx bx-time"></i> ${row.aktivitas_terakhir}</small>`;
                }
            },
            { 
                data: 'suspicious',
                render: function(data, type, row) {
                    let badge = data === 'Ya' ? 'bg-danger' : 'bg-success';
                    return `<span class="badge ${badge}">${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    return `<button class="btn btn-sm btn-info btn-detail" data-student="${row.student_id}" data-exercise="${row.exercise_id}"><i class="bx bx-list-ul"></i> Lihat Detail</button>`;
                }
            }
        ]
    });

    $('#btnFilter').click(function() {
        table.ajax.reload();
        
        // Update export links
        let exId = $('#filter_exercise').val();
        let csvUrl = "{{ route('guru.monitoring-quiz.export-csv', $serialModel->id) }}?exercise_id=" + exId;
        let pdfUrl = "{{ route('guru.monitoring-quiz.export-pdf', $serialModel->id) }}?exercise_id=" + exId;
        $('#btnExportCsv').attr('href', csvUrl);
        $('#btnExportPdf').attr('href', pdfUrl);
    });

    // Polling every 10 seconds
    setInterval(function() {
        table.ajax.reload(null, false); // false = keep current paging
    }, 10000);

    // Detail Modal
    $('#monitoringTable').on('click', '.btn-detail', function() {
        let studentId = $(this).data('student');
        let exerciseId = $(this).data('exercise');
        
        $('#timelineContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#modalTimeline').modal('show');

        $.ajax({
            url: `/aplikasi/{{ $serialModel->id }}/monitoring-quiz/detail/${studentId}/${exerciseId}`,
            method: 'GET',
            success: function(res) {
                $('#timelineContent').html(res.html);
            },
            error: function() {
                $('#timelineContent').html('<p class="text-danger">Gagal memuat data.</p>');
            }
        });
    });
});
</script>
@endsection
