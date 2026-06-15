<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');

// 1. Remove spinner HTML
$spinnerHtml = <<<HTML
                <!-- Spinner Loading State -->
                <div class="d-flex justify-content-center align-items-center py-5 my-5" id="studentDetailLoading">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
HTML;
$content = str_replace($spinnerHtml, '', $content);

// 2. Remove display: none from modalBody HTML
$content = str_replace('<div id="studentDetailContent" style="display: none;"></div>', '<div id="studentDetailContent"></div>', $content);

// 3. Fix JavaScript logic
$oldJs = <<<'JS'
    document.addEventListener('DOMContentLoaded', function() {
        const detailButtons = document.querySelectorAll('.btn-detail-siswa');
        const modalBody = document.getElementById('studentDetailContent');
        const loadingSpinner = document.getElementById('studentDetailLoading');

        function getBadgeDetail(val, hero = false) {
JS;

$newJs = <<<'JS'
    document.addEventListener('DOMContentLoaded', function() {
        const detailButtons = document.querySelectorAll('.btn-detail-siswa');
        const modalBody = document.getElementById('studentDetailContent');

        // Debug log
        console.log("Loaded studentDetails:", window.studentDetails);
        console.log("Loaded uniqueColumns:", window.uniqueColumns);

        function getBadgeDetail(val, hero = false) {
JS;
$content = str_replace($oldJs, $newJs, $content);

$oldJs2 = <<<'JS'
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
                            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Siswa /</span> ${studentData.student.name}</h4>
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
JS;

$newJs2 = <<<'JS'
        detailButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = parseInt(this.getAttribute('data-student-id'));
                
                console.log("Clicked student ID:", studentId);

                const studentData = window.studentDetails.find(s => s.student.id === studentId);
                
                console.log("Found student data:", studentData);

                if (!studentData) {
                    modalBody.innerHTML = '<div class="alert alert-danger m-4">Data siswa tidak ditemukan.</div>';
                    return;
                }

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
JS;
$content = str_replace($oldJs2, $newJs2, $content);

$oldJs3 = <<<'JS'
                                            <span class="avatar-initial rounded-circle bg-white text-primary fs-2 fw-bold">
                                                ${studentData.student.name.substring(0, 2).toUpperCase()}
                                            </span>
JS;
$newJs3 = <<<'JS'
                                            <span class="avatar-initial rounded-circle bg-white text-primary fs-2 fw-bold">
                                                ${(studentData.student.name || 'NN').substring(0, 2).toUpperCase()}
                                            </span>
JS;
$content = str_replace($oldJs3, $newJs3, $content);

$oldJs4 = <<<'JS'
                modalBody.innerHTML = html;
                loadingSpinner.style.display = 'none';
                modalBody.style.display = 'block';
            });
        });
JS;
$newJs4 = <<<'JS'
                modalBody.innerHTML = html;
            });
        });
JS;
$content = str_replace($oldJs4, $newJs4, $content);

file_put_contents('resources/views/guru/rekap-nilai/show-class.blade.php', $content);
echo "View script patched.\n";
