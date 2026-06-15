document.body.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-detail-siswa');
    if (!btn) return;

    const modalBody = document.getElementById('studentDetailContentNew');
    if (!modalBody) return;

    // Tahap 7: Render nama & nilai akhir (Tahap Dasar)
    // Tampilkan Loading (kalau ada DOM yang belum siap, tapi tidak disarankan pakai delay spinner, cukup skeleton cepat)
    modalBody.innerHTML = '<div class="p-5 text-center text-muted">Memuat data...</div>';

    // Parse JSON murni dari HTML DOM secara aman
    const dataElement = document.getElementById('student-data');
    if (!dataElement) {
        modalBody.innerHTML = '<div class="alert alert-danger m-4">Gagal memuat struktur data.</div>';
        return;
    }

    let students;
    try {
        students = JSON.parse(dataElement.textContent);
    } catch (err) {
        modalBody.innerHTML = '<div class="alert alert-danger m-4">Format data rusak.</div>';
        return;
    }

    const studentId = parseInt(btn.getAttribute('data-student-id'));
    const student = students.find(s => parseInt(s.id) === studentId);

    // Tahap 9: Data tidak ditemukan
    if (!student) {
        modalBody.innerHTML = '<div class="alert alert-danger m-4">Data siswa tidak ditemukan</div>';
        return;
    }

    if (student.nilai_akhir === null) {
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-12">
                    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Siswa /</span> ${student.name}</h4>
                    <div class="text-center py-5">
                        <i class='bx bx-info-circle text-muted fs-1 mb-2'></i>
                        <p class="text-muted mb-0">Belum terdapat data penilaian untuk siswa ini.</p>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    // Tahap 7 & 8: Render Komplit tapi bertahap.
    
    function renderCategory(catId, catName, icon, color) {
        const catData = student[catId] || {};
        const entries = Object.values(catData);
        
        let html = `
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h6 class="m-0 fw-bold text-${color}"><i class='bx ${icon} me-2 fs-5 align-middle'></i>Detail ${catName}</h6>
                    </div>
                    <div class="card-body p-0">
        `;
        
        if (entries.length > 0) {
            html += '<ul class="list-group list-group-flush">';
            entries.forEach((point, idx) => {
                let p = parseFloat(point);
                let bgClass = 'bg-danger';
                if (p >= 90) bgClass = 'bg-success';
                else if (p >= 80) bgClass = 'bg-primary';
                else if (p >= 70) bgClass = 'bg-warning';
                
                let badge = (point === null || point === undefined) 
                    ? '<span class="badge bg-label-secondary">Belum Dinilai</span>' 
                    : `<span class="badge ${bgClass}">${p}</span>`;
                    
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-light">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3 bg-label-${color} text-${color} d-flex align-items-center justify-content-center rounded-circle fw-bold">
                                ${idx + 1}
                            </div>
                            <h6 class="mb-0 text-dark fw-semibold">Penilaian ${idx + 1}</h6>
                        </div>
                        <div class="ms-3">${badge}</div>
                    </li>
                `;
            });
            html += '</ul>';
        } else {
            html += `
                <div class="text-center py-5">
                    <i class='bx ${icon} text-muted fs-1 mb-2'></i>
                    <p class="text-muted mb-0">Belum ada aktivitas ${catName}.</p>
                </div>
            `;
        }
        
        html += `
                    </div>
                </div>
            </div>
        `;
        return html;
    }

    let finalScoreP = parseFloat(student.nilai_akhir);
    let finalScoreBg = 'bg-danger';
    if (finalScoreP >= 90) finalScoreBg = 'bg-success';
    else if (finalScoreP >= 80) finalScoreBg = 'bg-primary';
    else if (finalScoreP >= 70) finalScoreBg = 'bg-warning';

    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-12">
                <h4 class="fw-bold pb-2 mb-4">
                    <span class="text-muted fw-light">Siswa /</span> ${student.name}
                </h4>

                <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-4 bg-primary text-white d-flex flex-column justify-content-center align-items-center p-4">
                            <div class="avatar avatar-xl mb-3">
                                <span class="avatar-initial rounded-circle bg-white text-primary fs-2 fw-bold">
                                    ${(student.name || 'NN').substring(0, 2).toUpperCase()}
                                </span>
                            </div>
                            <h4 class="text-white fw-bold mb-1 text-center">${student.name}</h4>
                            <p class="text-white-50 mb-0"><i class="bx bx-id-card me-1"></i>${student.nis || '-'}</p>
                        </div>
                        <div class="col-md-8">
                            <div class="card-body h-100 d-flex flex-column justify-content-center p-5">
                                <div class="text-center">
                                    <h6 class="text-muted text-uppercase fw-bold letter-spacing-1 mb-2">Nilai Akhir Keseluruhan</h6>
                                    <span class="badge ${finalScoreBg} fs-1 px-4 py-2">${student.nilai_akhir}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h5 class="fw-bold mb-3 mt-4 text-secondary">Rincian Penilaian</h5>
                <div class="row g-3">
                    ${renderCategory('tugas', 'Tugas', 'bx-task', 'secondary')}
                    ${renderCategory('akm', 'AKM', 'bx-brain', 'primary')}
                    ${renderCategory('uh', 'Ulangan Harian', 'bx-check-shield', 'success')}
                    ${renderCategory('pts', 'PTS', 'bx-file', 'warning')}
                    ${renderCategory('pas', 'PAS', 'bx-medal', 'danger')}
                </div>
            </div>
        </div>
    `;
});
