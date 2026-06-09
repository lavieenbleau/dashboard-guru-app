@extends('layouts.sneat')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .card-modern {
        border-radius: 16px;
        box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
        border: none;
    }
    .note-editor .note-editing-area { min-height: 250px; }
    .note-editor .note-dropzone { opacity: 0 !important; }
    
    .drop-zone {
        border: 2px dashed #d9dee3;
        border-radius: 16px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }
    .drop-zone:hover, .drop-zone.dragover {
        border-color: #696cff;
        background-color: rgba(105, 108, 255, 0.05);
    }
    .drop-zone-icon {
        font-size: 48px;
        color: #696cff;
        margin-bottom: 15px;
    }
    .file-preview {
        border: 1px solid #d9dee3;
        border-radius: 16px;
        padding: 20px;
        display: none;
        background-color: #fff;
    }
    .file-icon {
        font-size: 32px;
        color: #696cff;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas', $serial->id) }}">Tugas</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.tugas.mapel', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item active">Tambah Tugas</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold text-dark"><i class='bx bx-task text-primary me-2'></i>Tambah Tugas Baru</h4>
    </div>

    <form action="{{ route('guru.tugas.store', [$serial->id, $lesson->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Kolom Kiri: Form Utama -->
            <div class="col-lg-8 mb-4">
                <div class="card card-modern h-100">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Judul Tugas <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="Contoh: Laporan Praktikum Perubahan Wujud Benda" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Deskripsi Tugas</label>
                            <textarea name="description" class="form-control summernote @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-2 d-block">Jelaskan instruksi pengerjaan tugas secara detail. Guru dapat menambahkan gambar langsung ke deskripsi.</small>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark">Lampiran Tugas (Opsional)</label>
                            
                            <!-- Input File Hidden -->
                            <input type="file" name="attachment" id="attachmentInput" class="d-none @error('attachment') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png,.webp">
                            
                            <!-- Area Drop Zone -->
                            <div class="drop-zone" id="dropZone">
                                <i class='bx bx-cloud-upload drop-zone-icon'></i>
                                <h5 class="mb-2 text-dark">Klik untuk upload file atau drag & drop di sini</h5>
                                <p class="text-muted mb-0">PDF, DOCX, XLSX, PPTX, ZIP, JPG, PNG<br>Maksimal 10 MB</p>
                            </div>

                            <!-- Area Preview File -->
                            <div class="file-preview mt-3" id="filePreview">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center">
                                        <i class='bx bxs-file-blank file-icon me-3'></i>
                                        <div>
                                            <h6 class="mb-0 text-dark" id="fileName">nama_file.pdf</h6>
                                            <small class="text-muted" id="fileSize">2.3 MB</small>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="changeFileBtn">Ganti File</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeFileBtn">Hapus File</button>
                                    </div>
                                </div>
                            </div>
                            
                            @error('attachment')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Pengaturan -->
            <div class="col-lg-4 mb-4">
                <div class="card card-modern h-100">
                    <div class="card-header border-bottom bg-transparent pb-3 pt-4 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class='bx bx-cog me-2 text-primary'></i>Pengaturan Tugas</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold text-dark mb-0">Pilih Kelas <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline mb-0 me-0">
                                    <input class="form-check-input" type="checkbox" id="selectAllClasses">
                                    <label class="form-check-label small text-dark cursor-pointer" for="selectAllClasses">Pilih Semua</label>
                                </div>
                            </div>
                            <div class="list-group @error('classroom_ids') is-invalid border-danger @enderror" style="max-height: 250px; overflow-y: auto; border-radius: 8px;">
                                @forelse($classrooms as $classroom)
                                    <label class="list-group-item d-flex gap-2 px-3 py-2 cursor-pointer border-1">
                                        <input class="form-check-input classroom-checkbox mt-0" type="checkbox" name="classroom_ids[]" value="{{ $classroom->id }}" {{ (is_array(old('classroom_ids')) && in_array($classroom->id, old('classroom_ids'))) ? 'checked' : '' }}>
                                        <span class="text-dark">{{ $classroom->name }}</span>
                                    </label>
                                @empty
                                    <div class="alert alert-warning mb-0 rounded-0 border-0">
                                        <i class='bx bx-info-circle'></i> Belum ada kelas.
                                    </div>
                                @endforelse
                            </div>
                            @error('classroom_ids')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Deadline Pengumpulan</label>
                            <input type="datetime-local" name="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline') }}">
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Kosongkan jika tidak ada batas waktu.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Link Referensi (Opsional)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-link"></i></span>
                                <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link') }}" placeholder="https://...">
                            </div>
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Google Drive, YouTube, Website, Canva, dll.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Pengaturan Pengumpulan</label>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="require_file" id="requireFile" value="1" {{ old('require_file') ? 'checked' : '' }}>
                                <label class="form-check-label text-dark" for="requireFile">
                                    Siswa wajib mengunggah file jawaban
                                </label>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark">Pengaturan Nilai</label>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" disabled>
                                <label class="form-check-label text-muted d-flex align-items-center gap-2">
                                    Tampilkan nilai kepada siswa setelah dinilai
                                    <span class="badge bg-label-secondary" style="font-size: 10px;">Segera Hadir</span>
                                </label>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Bottom Action Buttons -->
            <div class="col-12 mt-2">
                <div class="card card-modern bg-transparent border-0 shadow-none">
                    <div class="card-body p-0 d-flex justify-content-end gap-3 flex-wrap">
                        <a href="{{ route('guru.tugas.mapel', [$serial->id, $lesson->id]) }}" class="btn btn-label-secondary btn-lg px-4 fw-bold">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm fw-bold">
                            <i class='bx bx-save me-2'></i>Simpan Tugas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Summernote
    if (typeof $ !== 'undefined' && $.fn.summernote) {
        $('.summernote').summernote({
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['picture', 'link']],
                ['view', ['fullscreen', 'codeview']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    if (typeof uploadImage === 'function') {
                        uploadImage(files[0], 'tugas', this);
                    }
                }
            }
        });
    }

    // Select All Classes Logic
    const selectAllBtn = document.getElementById('selectAllClasses');
    const classCheckboxes = document.querySelectorAll('.classroom-checkbox');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('change', function() {
            classCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });

        classCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = Array.from(classCheckboxes).every(c => c.checked);
                const someChecked = Array.from(classCheckboxes).some(c => c.checked);
                selectAllBtn.checked = allChecked;
                selectAllBtn.indeterminate = someChecked && !allChecked;
            });
        });
    }

    // Drag & Drop File Upload Logic
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('attachmentInput');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFileBtn = document.getElementById('removeFileBtn');
    const changeFileBtn = document.getElementById('changeFileBtn');

    if (dropZone && fileInput) {
        const preventDefaults = (e) => {
            e.preventDefault();
            e.stopPropagation();
        };

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('dragover');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFiles();
            }
        });

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        changeFileBtn.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', handleFiles);

        removeFileBtn.addEventListener('click', () => {
            fileInput.value = '';
            filePreview.style.display = 'none';
            dropZone.style.display = 'block';
        });

        function handleFiles() {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                
                // Validate size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    showError('Ukuran file terlalu besar. Maksimal 10 MB.');
                    fileInput.value = '';
                    return;
                }

                fileName.textContent = file.name;
                fileSize.textContent = formatBytes(file.size);
                
                dropZone.style.display = 'none';
                filePreview.style.display = 'block';
            }
        }

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
    }
});
</script>
@endsection
