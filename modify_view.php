<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');

// 1. Remove Tabs Nav
$content = preg_replace('/<!-- Nav Tabs -->.*?<\/ul>/s', '', $content);

// 2. Remove Tab Content Wrappers
$content = str_replace('<div class="tab-content" id="rekapTabsContent">', '', $content);
$content = str_replace('<div class="tab-pane fade show active" id="ringkasan" role="tabpanel" aria-labelledby="ringkasan-tab">', '', $content);

// 3. Remove Detail Penilaian Tab Content completely
$content = preg_replace('/<!-- Tab Detail Penilaian -->.*?<\/div>\s*<\/div>\s*<\/div>/s', '', $content);

// 4. Update Detail Button
$detailBtnOrig = '<a href="{{ route(\'guru.rekapnilai.siswa\', [\'serial\' => $serial->id, \'classroom\' => $classroom->id, \'student\' => $data[\'student\']->id]) }}" class="btn btn-sm btn-info rounded-pill px-3 shadow-sm">';
$detailBtnNew = '<button type="button" class="btn btn-sm btn-info rounded-pill px-3 shadow-sm btn-detail-siswa" data-student-id="{{ $data[\'student\']->id }}" data-bs-toggle="modal" data-bs-target="#studentDetailModal">';
$content = str_replace($detailBtnOrig, $detailBtnNew, $content);

$content = str_replace('Detail</a>', 'Detail</button>', $content);

// 5. Add Modal and JS script before @endsection
$modalAndJs = <<<HTML

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

@push('scripts')
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

            const url = `/aplikasi/\${serialId}/rekap-nilai/kelas/\${classroomId}/lesson/\${lessonId}/student/\${studentId}/ajax`;

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
@endpush
HTML;

// Clean up trailing divs from removed tab panes (since we removed 2 opening div wrappers)
// Originally it had:
//        </div> <!-- end tab pane 1 -->
//        <div id="detail">...</div> <!-- end tab pane 2 -->
//    </div> <!-- end tab content -->
// </div> <!-- end container -->

// We'll just replace the end of the file
$content = preg_replace('/<\/div>\s*<\/div>\s*@endsection/', "</div>\n" . $modalAndJs . "\n@endsection", $content);

file_put_contents('resources/views/guru/rekap-nilai/show-class.blade.php', $content);
echo "View refactored.\n";
