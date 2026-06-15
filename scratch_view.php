<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');

// Remove AJAX script block and replace it
$ajaxScriptRegex = '/@push\(\'scripts\'\).*?<\/script>.*?@endpush/s';
if (!preg_match($ajaxScriptRegex, $content)) {
    // try section('page-script')
    $ajaxScriptRegex = '/@section\(\'page-script\'\).*?<\/script>.*?@endsection/s';
}

$newScript = <<<HTML
@section('page-script')
<script>
    window.studentDetails = @json(\$rekapData);
    window.uniqueColumns = @json(\$detailColumns);

    document.addEventListener('DOMContentLoaded', function() {
        const detailButtons = document.querySelectorAll('.btn-detail-siswa');
        const modalBody = document.getElementById('studentDetailContent');
        const loadingSpinner = document.getElementById('studentDetailLoading');

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
                const studentId = parseInt(this.getAttribute('data-student-id'));
                
                loadingSpinner.style.display = 'flex';
                modalBody.style.display = 'none';

                const studentData = window.studentDetails.find(s => s.student.id === studentId);
                
                if (!studentData) {
                    modalBody.innerHTML = '<div class="alert alert-danger m-4">Data siswa tidak ditemukan.</div>';
                    loadingSpinner.style.display = 'none';
                    modalBody.style.display = 'block';
                    return;
                }

                if (studentData.nilai_akhir === null) {
                    modalBody.innerHTML = `
                        <div class="row"><div class="col-md-12">
                            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Siswa /</span> \${studentData.student.name}</h4>
                            <div class="text-center py-5">
                                <i class='bx bx-ghost text-muted fs-1 mb-2'></i>
                                <p class="text-muted mb-0">Belum terdapat data penilaian untuk siswa ini.</p>
                            </div>
                        </div></div>
                    `;
                    loadingSpinner.style.display = 'none';
                    modalBody.style.display = 'block';
                    return;
                }

                // Build HTML
                let html = `
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="fw-bold py-3 mb-4">
                                <span class="text-muted fw-light">Siswa /</span> \${studentData.student.name}
                            </h4>

                            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                                <div class="row g-0">
                                    <div class="col-md-4 bg-primary text-white d-flex flex-column justify-content-center align-items-center p-4">
                                        <div class="avatar avatar-xl mb-3">
                                            <span class="avatar-initial rounded-circle bg-white text-primary fs-2 fw-bold">
                                                \${studentData.student.name.substring(0, 2).toUpperCase()}
                                            </span>
                                        </div>
                                        <h4 class="text-white fw-bold mb-1 text-center">\${studentData.student.name}</h4>
                                        <p class="text-white-50 mb-0"><i class="bx bx-id-card me-1"></i>\${studentData.student.nis || '-'}</p>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body h-100 d-flex flex-column justify-content-center p-5">
                                            <div class="text-center">
                                                <h6 class="text-muted text-uppercase fw-bold letter-spacing-1 mb-2">Nilai Akhir Keseluruhan</h6>
                                                \${getBadgeDetail(studentData.nilai_akhir, true)}
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
                                    <h6 class="m-0 fw-bold text-\${cat.color}"><i class='bx \${cat.icon} me-2 fs-5 align-middle'></i>Detail \${cat.name}</h6>
                                    <span class="badge bg-\${cat.color} rounded-pill px-3 py-1 fw-normal">Rata-rata: \${avgData && avgData.avg !== null ? avgData.avg : '-'}</span>
                                </div>
                                <div class="card-body p-0">
                    `;

                    if (cols && cols.length > 0) {
                        html += '<ul class="list-group list-group-flush">';
                        cols.forEach((col, idx) => {
                            let point = studentData.detail && studentData.detail[cat.id] ? studentData.detail[cat.id][col.id] : null;
                            html += `
                                <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-bottom-0 border-light">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-label-\${cat.color} text-\${cat.color} d-flex align-items-center justify-content-center rounded-circle fw-bold">
                                            \${idx + 1}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark fw-semibold">\${col.title}</h6>
                                        </div>
                                    </div>
                                    <div class="ms-3">\${getBadgeDetail(point)}</div>
                                </li>
                            `;
                        });
                        html += '</ul>';
                    } else {
                        html += `
                            <div class="text-center py-5">
                                <i class='bx \${cat.icon} text-muted fs-1 mb-2'></i>
                                <p class="text-muted mb-0">Belum ada aktivitas \${cat.name}.</p>
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
                loadingSpinner.style.display = 'none';
                modalBody.style.display = 'block';
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
HTML;

$content = preg_replace($ajaxScriptRegex, $newScript, $content);

// Ensure Detail Button does not have old AJAX style trigger
$oldBtn = '<button type="button" class="btn btn-sm btn-info rounded-pill px-3 shadow-sm btn-detail-siswa" data-student-id="{{ $data[\'student\']->id }}" data-bs-toggle="modal" data-bs-target="#studentDetailModal"><i class="bx bx-detail me-1"></i>Detail</button>';
if (strpos($content, $oldBtn) === false) {
    // Find generic a tag or button
    $content = preg_replace('/<a href="\{\{ route\(\'guru\.rekapnilai\.siswa\'.*?<\/a>/s', $oldBtn, $content);
}

file_put_contents('resources/views/guru/rekap-nilai/show-class.blade.php', $content);
echo "View refactored.\n";
