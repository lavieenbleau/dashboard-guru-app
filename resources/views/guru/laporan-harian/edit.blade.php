@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item"><a href="{{ route('guru.dashboard', $serial->id) }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.laporanharian', $serial->id) }}">Laporan Harian</a></li>
            <li class="breadcrumb-item active">Edit Aktivitas</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class='bx bx-edit text-warning me-2'></i>Edit Aktivitas Siswa</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('guru.laporanharian.update', [$serial->id, $post->id]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Pilih Siswa <span class="text-danger">*</span></label>
                    <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Siswa --</option>
                        @php
                            $currentClassroom = null;
                        @endphp
                        @foreach($students as $student)
                            @if($currentClassroom != $student->classroom_id)
                                @if($currentClassroom !== null)
                                    </optgroup>
                                @endif
                                <optgroup label="{{ $student->classroom->name ?? 'Tanpa Kelas' }}">
                                @php
                                    $currentClassroom = $student->classroom_id;
                                @endphp
                            @endif
                            <option value="{{ $student->id }}" {{ (old('student_id', $studentId) == $student->id) ? 'selected' : '' }}>
                                {{ $student->name }} @if($student->absen)({{ $student->absen }})@endif
                            </option>
                        @endforeach
                        @if($currentClassroom !== null)
                            </optgroup>
                        @endif
                    </select>
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Aktivitas <span class="text-danger">*</span></label>
                    <textarea name="activity" class="form-control @error('activity') is-invalid @enderror" 
                              rows="4" required placeholder="Deskripsikan aktivitas siswa...">{{ old('activity', $post->description) }}</textarea>
                    @error('activity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                           value="{{ old('date', \Carbon\Carbon::parse($post->created_at)->format('Y-m-d')) }}">
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class='bx bx-save me-1'></i>Update Aktivitas
                    </button>
                    <a href="{{ route('guru.laporanharian', $serial->id) }}" class="btn btn-secondary">
                        <i class='bx bx-x me-1'></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
