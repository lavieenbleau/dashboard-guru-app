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
    .embed-preview-container {
        border-radius: 12px;
        overflow: hidden;
        margin-top: 10px;
        display: none;
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
    }
    .embed-preview-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
    .youtube-preview {
        border-radius: 12px;
        overflow: hidden;
        margin-top: 10px;
        display: none;
        width: 100%;
    }
    .youtube-preview img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }
    .list-group-item.cursor-pointer:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.materi', $serial->id) }}">Materi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.custom', $serial->id) }}">Materi Tambahan</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.materi.lesson', [$serial->id, $lesson->id]) }}">{{ $lesson->name }}</a></li>
            <li class="breadcrumb-item active">Edit Materi</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold text-dark"><i class='bx bx-book-open text-primary me-2'></i>Edit Materi - {{ $lesson->name }}</h4>
    </div>

    <form action="{{ route('guru.materi.update', [$serial->id, $lesson->id, $materi->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Kolom Kiri: Form Utama (70%) -->
            <div class="col-lg-8 mb-4">
                <div class="card card-modern h-100">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Judul Materi <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" value="{{ old('title', $materi->title) }}" placeholder="Contoh: Mengenal Sistem Tata Surya" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Deskripsi Materi</label>
                            <textarea name="description" id="editor" class="form-control summernote @error('description') is-invalid @enderror">{{ old('description', $materi->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-2 d-block">Jelaskan isi materi secara detail. Anda dapat menyisipkan gambar langsung ke dalam editor.</small>
                        </div>
                        
                        <div class="mb-2">
                            @if($materi->attachment)
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">File Saat Ini</label>
                                <div class="alert alert-info mb-2 p-2 d-flex align-items-center">
                                    <i class='bx bx-file me-2 fs-4'></i>
                                    <a href="{{ Storage::url($materi->attachment) }}" target="_blank" class="text-truncate">{{ basename($materi->attachment) }}</a>
                                </div>
                            </div>
                            @endif

                            <label class="form-label fw-bold text-dark">{{ $materi->attachment ? 'Ganti File Lampiran' : 'Upload Lampiran' }}</label>
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

            <!-- Kolom Kanan: Pengaturan & Media (30%) -->
            <div class="col-lg-4 mb-4">
                
                <!-- Card Media Pembelajaran -->
                <div class="card card-modern mb-4">
                    <div class="card-header border-bottom bg-transparent pb-3 pt-4 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class='bx bx-play-circle me-2 text-primary'></i>Media Pembelajaran</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Link Referensi (Opsional)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-link"></i></span>
                                <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $materi->link) }}" placeholder="https://...">
                            </div>
                            @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark">Video YouTube / Embed</label>
                            <textarea name="embed" id="embedInput" rows="3" class="form-control @error('embed') is-invalid @enderror" placeholder="<iframe src='...'></iframe>">{{ old('embed', $materi->embed) }}</textarea>
                            @error('embed')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Tempel kode iframe YouTube atau Vimeo.</small>
                            
                            <!-- Preview YouTube -->
                            <div class="youtube-preview" id="youtubePreview">
                                <img src="" alt="YouTube Thumbnail" id="youtubeThumbnail">
                            </div>
                            <!-- Preview Embed Asli -->
                            <div class="embed-preview-container" id="embedPreview">
                                <!-- iframe will be inserted here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Pengaturan Materi -->
                <div class="card card-modern mb-4">
                    <div class="card-header border-bottom bg-transparent pb-3 pt-4 px-4">
                        <h5 class="mb-0 fw-bold text-dark"><i class='bx bx-cog me-2 text-primary'></i>Pengaturan Materi</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Pilih Kelas</label>
                            <div class="list-group rounded-3 overflow-hidden" style="max-height: 200px; overflow-y: auto;">
                                @php
                                    $classrooms = \App\Models\Classroom::where('serial_id', $serial->id)->get();
                                    // Normally you would load shared classrooms, but we're mimicking visual design
                                @endphp
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
                            <small class="text-muted d-block mt-2">Pilih kelas yang dapat mengakses materi ini.</small>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark">Status Publikasi</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="publish" {{ old('status') == 'publish' ? 'selected' : '' }}>Publikasikan Sekarang</option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi di Kanan -->
                <div class="d-flex flex-column gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold w-100">
                        <i class='bx bx-save me-2'></i>Update Materi
                    </button>
                    <a href="{{ route('guru.materi.lesson', [$serial->id, $lesson->id]) }}" class="btn btn-label-secondary btn-lg fw-bold w-100">
                        Batal
                    </a>
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
            ]
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

        if (changeFileBtn) {
            changeFileBtn.addEventListener('click', () => {
                fileInput.click();
            });
        }

        fileInput.addEventListener('change', handleFiles);

        if (removeFileBtn) {
            removeFileBtn.addEventListener('click', () => {
                fileInput.value = '';
                filePreview.style.display = 'none';
                dropZone.style.display = 'block';
            });
        }

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

    // Embed Preview Logic
    const embedInput = document.getElementById('embedInput');
    const youtubePreview = document.getElementById('youtubePreview');
    const youtubeThumbnail = document.getElementById('youtubeThumbnail');
    const embedPreview = document.getElementById('embedPreview');

    if (embedInput) {
        embedInput.addEventListener('input', function() {
            const val = this.value.trim();
            youtubePreview.style.display = 'none';
            embedPreview.style.display = 'none';
            embedPreview.innerHTML = '';

            if (!val) return;

            // Try extracting youtube ID from iframe src
            let youtubeId = null;
            const srcMatch = val.match(/src=["']([^"']+)["']/i);
            if (srcMatch && srcMatch[1]) {
                const url = srcMatch[1];
                const ytMatch = url.match(/(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i);
                if (ytMatch && ytMatch[1]) {
                    youtubeId = ytMatch[1];
                }
            }

            if (youtubeId) {
                youtubeThumbnail.src = 'https://img.youtube.com/vi/' + youtubeId + '/maxresdefault.jpg';
                // Fallback to hqdefault if maxres doesn't exist
                youtubeThumbnail.onerror = function() {
                    this.onerror = null;
                    this.src = 'https://img.youtube.com/vi/' + youtubeId + '/hqdefault.jpg';
                };
                youtubePreview.style.display = 'block';
            } else if (val.includes('<iframe')) {
                // Regular iframe preview
                embedPreview.innerHTML = val;
                embedPreview.style.display = 'block';
            }
        });

        // Trigger on load if there's old value
        if(embedInput.value.trim()) {
            embedInput.dispatchEvent(new Event('input'));
        }
    }
});
</script>
@endsection
