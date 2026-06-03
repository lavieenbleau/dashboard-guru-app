@extends('layouts.sneat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">{{ $serial->name }} / Rekap Nilai /</span> {{ $classroom->name }}
            </h4>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Rekap Nilai - {{ $classroom->name }}</h5>
                        <p class="text-muted mb-0">Ringkasan nilai per mata pelajaran</p>
                    </div>
                    <div>
                        <a href="{{ route('guru.rekapnilai.kelas.pdf', ['serial' => $serial->id, 'classroom' => $classroom->id, 'lesson_id' => $lessons->first()->id]) }}" 
                           class="btn btn-success me-2">
                            <i class="bx bxs-file-pdf me-1"></i>
                            Download PDF
                        </a>
                        <a href="{{ route('guru.rekapnilai', $serial->id) }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($students->isEmpty())
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            Belum ada siswa di kelas ini.
                        </div>
                    @else
                        @foreach($lessons as $lesson)
                            @php
                                $taskCount = isset($allTasks[$lesson->id]) ? count($allTasks[$lesson->id]) : 0;
                                $exTypes = isset($allExercises[$lesson->id]) ? array_keys($allExercises[$lesson->id]) : [];
                                $totalExCols = 0;
                                foreach($exTypes as $type) {
                                    $totalExCols += count($allExercises[$lesson->id][$type]);
                                }
                            @endphp
                            
                            <div class="mb-4">
                                <h5 class="card-title text-primary mb-3">
                                    <i class='bx bx-book-content me-2'></i>{{ strtoupper($lesson->name) }}
                                </h5>
                                @if($taskCount > 0 || $totalExCols > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th rowspan="2" class="text-center align-middle" style="width: 40px;">NO</th>
                                                    <th rowspan="2" class="align-middle" style="min-width: 180px;">NAMA SISWA</th>
                                                    @if($taskCount > 0)
                                                        <th colspan="{{ $taskCount }}" class="text-center">TUGAS</th>
                                                    @endif
                                                    @foreach($exTypes as $type)
                                                        @php
                                                            $exCount = count($allExercises[$lesson->id][$type]);
                                                        @endphp
                                                        <th colspan="{{ $exCount }}" class="text-center">
                                                            {{ $type == 'UH' ? 'ULANGAN HARIAN' : ($type == 'Tambahan' ? 'SOAL TAMBAHAN' : $type) }}
                                                        </th>
                                                    @endforeach
                                                    <th rowspan="2" class="text-center align-middle">RATA-RATA</th>
                                                </tr>
                                                <tr>
                                                    @if(isset($allTasks[$lesson->id]))
                                                        @foreach($allTasks[$lesson->id] as $task)
                                                            <th class="text-center" style="width: 55px;" title="{{ $task['title'] }}">{{ $task['number'] }}</th>
                                                        @endforeach
                                                    @endif
                                                    @if(isset($allExercises[$lesson->id]))
                                                        @foreach($allExercises[$lesson->id] as $type => $exercises)
                                                            @foreach($exercises as $ex)
                                                                <th class="text-center" style="width: 55px;" title="{{ $ex['title'] }}">
                                                                    {{ $ex['number'] }}
                                                                </th>
                                                            @endforeach
                                                        @endforeach
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($rekapData as $index => $data)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $data['student']->name }}</td>
                                                        @php
                                                            $lessonData = $data['lessons'][$lesson->id];
                                                            $allPoints = [];
                                                        @endphp
                                                        
                                                        {{-- Display tasks --}}
                                                        @foreach($lessonData['tasks'] as $task)
                                                            <td class="text-center">
                                                                @if($task['point'] !== null)
                                                                    @php $allPoints[] = $task['point']; @endphp
                                                                    {{ $task['point'] }}
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                        
                                                        {{-- Display exercises by type --}}
                                                        @foreach(['UH', 'PTS', 'PAS', 'Tambahan'] as $type)
                                                            @if(isset($lessonData['exercises'][$type]))
                                                                @foreach($lessonData['exercises'][$type] as $ex)
                                                                    <td class="text-center">
                                                                        @if($ex['point'] !== null)
                                                                            @php $allPoints[] = $ex['point']; @endphp
                                                                            {{ $ex['point'] }}
                                                                        @else
                                                                            <span class="text-muted">-</span>
                                                                        @endif
                                                                    </td>
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                        
                                                        {{-- Calculate and display average --}}
                                                        <td class="text-center">
                                                            @if(count($allPoints) > 0)
                                                                @php $avg = round(array_sum($allPoints) / count($allPoints), 1); @endphp
                                                                <strong>{{ $avg }}</strong>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="bx bx-info-circle me-2"></i>
                                        Belum ada tugas atau soal untuk mata pelajaran ini.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        
                        <div class="mt-4">
                            <h6 class="mb-3">Detail Nilai Per Siswa</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 40px;">NO</th>
                                            <th>NAMA SISWA</th>
                                            <th class="text-center">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rekapData as $index => $data)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td><strong>{{ $data['student']->name }}</strong></td>
                                                <td class="text-center">
                                                    <a href="{{ route('guru.rekapnilai.siswa', ['serial' => $serial->id, 'classroom' => $classroom->id, 'student' => $data['student']->id]) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="bx bx-show me-1"></i>Lihat Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bx bx-info-circle me-1"></i>
                                Angka pada kolom header menunjukkan nomor urut tugas/soal. Hover pada nomor untuk melihat judul lengkap.
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
