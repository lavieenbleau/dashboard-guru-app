@extends('layouts.sneat')

@section('content')
<div class="container-xxl py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('guru.soal', $serial->id) }}">Bank Soal</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.soal.list-direct', [$serial->id, 'tambahan']) }}">Soal Tambahan</a></li>
            <li class="breadcrumb-item active">Generate Soal dengan AI</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <!-- Info Card -->
            <div class="card bg-label-primary mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-primary p-3 me-3">
                            <i class='bx bx-brain bx-md'></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Smart Question Generator</h5>
                            <p class="mb-0 text-muted">Gunakan AI (OpenRouter) untuk membuat soal secara otomatis berdasarkan materi yang Anda berikan. AI akan menghasilkan soal berkualitas yang siap digunakan atau diedit sesuai kebutuhan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class='bx bx-edit-alt me-2'></i>
                    <h5 class="mb-0">Form Generate Soal</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            
                            @if(str_contains(session('error'), 'rate limit') || str_contains(session('error'), 'Rate limit'))
                                <hr class="my-2">
                                <small class="d-block mt-2">
                                    <strong><i class='bx bx-info-circle me-1'></i>Apa itu Rate Limit?</strong><br>
                                    OpenAI membatasi jumlah request per menit untuk setiap akun. Sistem sudah otomatis mencoba ulang 3 kali dengan jeda waktu.
                                </small>
                                <small class="d-block mt-2">
                                    <strong>Solusi:</strong><br>
                                    • Tunggu 1-2 menit lalu coba lagi<br>
                                    • Kurangi jumlah soal (misal: 3-5 soal saja)<br>
                                    • Upgrade akun OpenAI untuk limit lebih tinggi
                                </small>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('guru.soal.ai-generate', $serial->id) }}" method="POST" id="aiGeneratorForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Materi Yang Sudah Di-upload -->
                                <div class="mb-4">
                                    <label for="uploaded_material_id" class="form-label fw-bold">
                                        <i class='bx bx-folder-open me-1'></i>Pilih Materi
                                    </label>
                                    <div class="input-group">
                                        <select class="form-select" id="uploaded_material_id" name="uploaded_material_id">
                                            <option value="">-- Pilih Materi (Opsional) --</option>
                                            <optgroup label="Materi Guru">
                                                @foreach($materials->where('source_type', 'post') as $material)
                                                    <option value="post:{{ $material->id }}" data-mapel-id="{{ $material->mapel_id }}" {{ old('uploaded_material_id') == 'post:' . $material->id ? 'selected' : '' }}>
                                                        {{ $material->title }} @if($material->mapel) - {{ $material->mapel->name }} @endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Materi Admin">
                                                @foreach($materials->where('source_type', 'lesson') as $material)
                                                    <option value="lesson:{{ $material->id }}" data-mapel-id="{{ $material->mapel_id }}" {{ old('uploaded_material_id') == 'lesson:' . $material->id ? 'selected' : '' }}>
                                                        {{ $material->name }} @if($material->mapel) - {{ $material->mapel->name }} @endif
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <button class="btn btn-outline-primary" type="button" id="readMaterialBtn">
                                            <i class='bx bx-search-alt me-1'></i>Baca Materi
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        Pilih materi guru atau materi admin untuk otomatis mengisi deskripsi materi dari konten yang tersedia.
                                    </small>
                                    <div id="materialReadStatus" class="small mt-2 text-muted" style="display:none;"></div>
                                </div>

                                <!-- Ilustrasi Materi -->
                                <div class="mb-4">
                                    <label for="illustration" class="form-label fw-bold">
                                        <i class='bx bx-book-content me-1'></i>Ilustrasi / Deskripsi Materi <span class="text-danger">*</span>
                                    </label>
                                    <textarea 
                                        class="form-control @error('illustration') is-invalid @enderror" 
                                        id="illustration" 
                                        name="illustration" 
                                        rows="6" 
                                        placeholder="Contoh: Jelaskan tentang materi perkalian untuk siswa kelas 3 SD. Fokus pada konsep dasar perkalian 1-10, dengan pendekatan penjumlahan berulang. Siswa sudah memahami penjumlahan dasar."
                                        required>{{ old('illustration') }}</textarea>
                                    @error('illustration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class='bx bx-info-circle me-1'></i>Tulis deskripsi materi yang ingin Anda buatkan soalnya. Semakin detail dan jelas, semakin baik kualitas soal yang dihasilkan. Minimal 20 karakter.
                                    </small>
                                </div>

                                <div class="row">
                                    <!-- Jenis Soal -->
                                    <div class="col-md-6 mb-4">
                                        <label for="question_type" class="form-label fw-bold">
                                            <i class='bx bx-list-check me-1'></i>Jenis Soal <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('question_type') is-invalid @enderror" id="question_type" name="question_type" required>
                                            <option value="">-- Pilih Jenis Soal --</option>
                                            <option value="pilihan_ganda" {{ old('question_type') == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                                            <option value="essai" {{ old('question_type') == 'essai' ? 'selected' : '' }}>Essay</option>
                                        </select>
                                        @error('question_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Pilihan Ganda: 4 opsi jawaban | Essay: soal terbuka</small>
                                    </div>

                                    <!-- Tingkat Kesulitan -->
                                    <div class="col-md-6 mb-4">
                                        <label for="difficulty" class="form-label fw-bold">
                                            <i class='bx bx-slider-alt me-1'></i>Tingkat Kesulitan <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                                            <option value="">-- Pilih Tingkat --</option>
                                            <option value="mudah" {{ old('difficulty') == 'mudah' ? 'selected' : '' }}>Mudah</option>
                                            <option value="sedang" {{ old('difficulty') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                            <option value="sulit" {{ old('difficulty') == 'sulit' ? 'selected' : '' }}>Sulit</option>
                                        </select>
                                        @error('difficulty')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Sesuaikan dengan kemampuan siswa</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Jumlah Soal -->
                                    <div class="col-md-6 mb-4">
                                        <label for="count" class="form-label fw-bold">
                                            <i class='bx bx-hash me-1'></i>Jumlah Soal <span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            class="form-control @error('count') is-invalid @enderror" 
                                            id="count" 
                                            name="count" 
                                            min="1" 
                                            max="10" 
                                            value="{{ old('count', 3) }}" 
                                            required>
                                        @error('count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Maksimal 10 soal per generate. Rekomendasi: 3-5 soal</small>
                                    </div>

                                    <!-- Mata Pelajaran -->
                                    <div class="col-md-6 mb-4">
                                        <label for="mapel_id" class="form-label fw-bold">
                                            <i class='bx bx-book me-1'></i>Mata Pelajaran <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('mapel_id') is-invalid @enderror" id="mapel_id" name="mapel_id" required>
                                            <option value="">-- Pilih Mapel --</option>
                                            @foreach($mapels as $mapel)
                                                <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>
                                                    {{ $mapel->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('mapel_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Tipe Soal -->
                                    <div class="col-md-6 mb-4">
                                        <label for="exercise_type_id" class="form-label fw-bold">
                                            <i class='bx bx-category me-1'></i>Tipe Soal <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('exercise_type_id') is-invalid @enderror" id="exercise_type_id" name="exercise_type_id" required>
                                            <option value="">-- Pilih Tipe Soal --</option>
                                            @foreach($exerciseTypes as $type)
                                                <option value="{{ $type->id }}" {{ old('exercise_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('exercise_type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Waktu Pengerjaan -->
                                    <div class="col-md-6 mb-4">
                                        <label for="time_limit" class="form-label fw-bold">
                                            <i class='bx bx-time me-1'></i>Waktu Pengerjaan (Menit) <span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            class="form-control @error('time_limit') is-invalid @enderror" 
                                            id="time_limit" 
                                            name="time_limit" 
                                            min="1" 
                                            max="480" 
                                            value="{{ old('time_limit') }}" 
                                            placeholder="Contoh: 45"
                                            required>
                                        @error('time_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Masukkan durasi pengerjaan soal dalam menit (1-480 menit / 1 menit hingga 8 jam)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Tips Card -->
                                <div class="card bg-label-success mb-3">
                                    <div class="card-body">
                                        <h6 class="mb-3"><i class='bx bx-bulb me-2'></i>Tips Generate Soal</h6>
                                        <ul class="mb-0 ps-3">
                                            <li class="mb-2">Berikan deskripsi materi yang jelas dan spesifik</li>
                                            <li class="mb-2">Sebutkan tingkat kelas atau kemampuan siswa</li>
                                            <li class="mb-2">Jelaskan fokus pembelajaran yang diinginkan</li>
                                            <li class="mb-2">Sebutkan prasyarat pengetahuan siswa</li>
                                            <li class="mb-2">Generate beberapa kali jika perlu hasil berbeda</li>
                                            <li class="mb-2"><strong>Mulai dengan 3-5 soal</strong> untuk hasil optimal</li>
                                            <li>Tunggu 1-2 menit antar generate untuk hindari rate limit</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Bagikan ke Kelas -->
                                <div class="card bg-label-info">
                                    <div class="card-body">
                                        <h6 class="mb-3"><i class='bx bx-share-alt me-2'></i>Bagikan ke Kelas (Opsional)</h6>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="selectAllClasses">
                                            <label class="form-check-label fw-bold" for="selectAllClasses">
                                                Pilih Semua Kelas
                                            </label>
                                        </div>

                                        <hr>

                                        <div style="max-height: 300px; overflow-y: auto;">
                                            @forelse($classrooms as $classroom)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input classroom-checkbox" type="checkbox" name="classrooms[]" value="{{ $classroom->id }}" id="class{{ $classroom->id }}">
                                                    <label class="form-check-label" for="class{{ $classroom->id }}">
                                                        {{ $classroom->name }}
                                                    </label>
                                                </div>
                                            @empty
                                                <p class="text-muted mb-0">Tidak ada kelas tersedia</p>
                                            @endforelse
                                        </div>

                                        <small class="text-muted d-block mt-3">
                                            <i class='bx bx-info-circle me-1'></i>Anda bisa membagikan soal nanti setelah review
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="generateBtn">
                                <i class='bx bx-brain me-1'></i>Generate Soal dengan AI
                            </button>
                            <a href="{{ route('guru.soal.list-direct', [$serial->id, 'tambahan']) }}" class="btn btn-label-secondary">
                                <i class='bx bx-x me-1'></i>Batal
                            </a>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="loadingIndicator" class="alert alert-info mt-3" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span>Sedang menghasilkan soal dengan AI, mohon tunggu...</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('aiGeneratorForm');
    const generateBtn = document.getElementById('generateBtn');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const selectAllCheckbox = document.getElementById('selectAllClasses');
    const classroomCheckboxes = document.querySelectorAll('.classroom-checkbox');
    const materialSelect = document.getElementById('uploaded_material_id');
    const readMaterialBtn = document.getElementById('readMaterialBtn');
    const illustrationField = document.getElementById('illustration');
    const mapelField = document.getElementById('mapel_id');
    const materialReadStatus = document.getElementById('materialReadStatus');
    const readMaterialUrlTemplate = "{{ route('guru.soal.ai-material.read', ['serial' => $serial->id, 'materialId' => '__MATERIAL_ID__']) }}";

    const setMaterialStatus = (message, isError = false) => {
        materialReadStatus.textContent = message;
        materialReadStatus.style.display = 'block';
        materialReadStatus.classList.toggle('text-danger', isError);
        materialReadStatus.classList.toggle('text-success', !isError);
    };

    const readSelectedMaterial = async () => {
        if (!materialSelect || !materialSelect.value) {
            setMaterialStatus('Pilih materi terlebih dahulu.', true);
            return;
        }

        try {
            readMaterialBtn.disabled = true;
            setMaterialStatus('Membaca materi terpilih...');

            const url = readMaterialUrlTemplate.replace('__MATERIAL_ID__', encodeURIComponent(materialSelect.value));
            const response = await fetch(url, { headers: { 'Accept': 'application/json' } });

            if (!response.ok) {
                throw new Error('Gagal mengambil materi.');
            }

            const data = await response.json();
            illustrationField.value = data.illustration || '';

            if (data.mapel_id && !mapelField.value) {
                mapelField.value = String(data.mapel_id);
            }

            setMaterialStatus('Materi berhasil dimuat ke kolom deskripsi.');
        } catch (error) {
            setMaterialStatus(error.message || 'Terjadi kesalahan saat membaca materi.', true);
        } finally {
            readMaterialBtn.disabled = false;
        }
    };

    if (readMaterialBtn) {
        readMaterialBtn.addEventListener('click', readSelectedMaterial);
    }

    if (materialSelect) {
        materialSelect.addEventListener('change', function() {
            materialReadStatus.style.display = 'none';
        });
    }

    // Select All Classrooms
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            classroomCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        classroomCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(classroomCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });
    }

    // Show loading on submit
    form.addEventListener('submit', function() {
        generateBtn.disabled = true;
        loadingIndicator.style.display = 'block';
    });
});
</script>
@endsection
