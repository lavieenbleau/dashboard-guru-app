<?php
$content = file_get_contents('resources/views/guru/rekap-nilai/show-class.blade.php');

// Rename old modal
$content = str_replace('<div class="modal fade" id="studentDetailModal" tabindex="-1" aria-hidden="true">', '<div class="modal fade" id="student-detail-modal-old" tabindex="-1" aria-hidden="true">', $content);

// Rename old script section
$content = str_replace("@section('scripts')", "@section('old-detail-script')", $content);

// Insert new modal right before old script section
$newModal = <<<HTML
<!-- NEW Student Detail Modal -->
<div class="modal fade" id="studentDetailModalNew" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-primary">Detail Penilaian Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="studentDetailContentNew" class="p-4">
                    <!-- Dynamic content will be injected here by rekap-nilai-detail.js -->
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script id="student-data" type="application/json">
{!! json_encode(\$cleanStudentDetails) !!}
</script>
<script src="{{ asset('js/guru/rekap-nilai-detail.js') }}"></script>
@endsection

HTML;

$content = str_replace("@section('old-detail-script')", $newModal . "\n@section('old-detail-script')", $content);

// Update button targets
$content = str_replace('data-bs-target="#studentDetailModal"', 'data-bs-target="#studentDetailModalNew"', $content);

file_put_contents('resources/views/guru/rekap-nilai/show-class.blade.php', $content);
echo "View patched for Tahap 1, 2, 5.\n";
