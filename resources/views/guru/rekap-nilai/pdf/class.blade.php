<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai - {{ $classroom->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .header h1 {
            margin: 5px 0;
            font-size: 14px;
        }
        .header h2 {
            margin: 3px 0;
            font-size: 11px;
            font-weight: normal;
        }
        .info {
            margin-bottom: 10px;
            font-size: 9px;
        }
        .info table {
            width: 40%;
        }
        .info td {
            padding: 2px 5px;
        }
        .mapel-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .mapel-title {
            font-size: 11px;
            font-weight: bold;
            margin: 10px 0 5px 0;
            padding: 3px 5px;
            background-color: #f0f0f0;
            border-left: 4px solid #333;
        }
        table.grades {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.grades th,
        table.grades td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 8px;
        }
        table.grades th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        table.grades td.name {
            text-align: left;
        }
        .footer {
            margin-top: 20px;
            font-size: 8px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP NILAI SISWA</h1>
        <h2>{{ $serial->name }}</h2>
        <h2>{{ $classroom->name }}</h2>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>Kelas</strong></td>
                <td>: {{ $classroom->name }}</td>
            </tr>
            <tr>
                <td><strong>Jumlah Siswa</strong></td>
                <td>: {{ $students->count() }} orang</td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: {{ now()->format('d F Y') }}</td>
            </tr>
        </table>
    </div>

    @if($students->isEmpty())
        <p>Belum ada data siswa.</p>
    @else
        @foreach($mapels as $mapel)
            @php
                $taskCount = isset($allTasks[$mapel->id]) ? count($allTasks[$mapel->id]) : 0;
                $exTypes = isset($allExercises[$mapel->id]) ? array_keys($allExercises[$mapel->id]) : [];
                $totalExCols = 0;
                foreach($exTypes as $type) {
                    $totalExCols += count($allExercises[$mapel->id][$type]);
                }
            @endphp
            
            <div class="mapel-section">
                <div class="mapel-title">{{ strtoupper($mapel->name) }}</div>
                
                @if($taskCount > 0 || $totalExCols > 0)
                    <table class="grades">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 20px;">No</th>
                                <th rowspan="2" style="min-width: 100px;">Nama Siswa</th>
                                @if($taskCount > 0)
                                    <th colspan="{{ $taskCount }}">Tugas</th>
                                @endif
                                @foreach($exTypes as $type)
                                    @php $exCount = count($allExercises[$mapel->id][$type]); @endphp
                                    <th colspan="{{ $exCount }}">
                                        {{ $type == 'UH' ? 'Ulangan Harian' : ($type == 'Tambahan' ? 'Soal Tambahan' : $type) }}
                                    </th>
                                @endforeach
                                <th rowspan="2">Rata-rata</th>
                            </tr>
                            <tr>
                                @if(isset($allTasks[$mapel->id]))
                                    @foreach($allTasks[$mapel->id] as $task)
                                        <th style="width: 25px;">{{ $task['number'] }}</th>
                                    @endforeach
                                @endif
                                @if(isset($allExercises[$mapel->id]))
                                    @foreach($allExercises[$mapel->id] as $type => $exercises)
                                        @foreach($exercises as $ex)
                                            <th style="width: 25px;">{{ $ex['number'] }}</th>
                                        @endforeach
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekapData as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="name">{{ $data['student']->name }}</td>
                                    @php
                                        $mapelData = $data['mapels'][$mapel->id];
                                        $allPoints = [];
                                    @endphp
                                    
                                    {{-- Display tasks --}}
                                    @foreach($mapelData['tasks'] as $task)
                                        <td>
                                            @if($task['point'] !== null)
                                                @php $allPoints[] = $task['point']; @endphp
                                                {{ $task['point'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endforeach
                                    
                                    {{-- Display exercises by type --}}
                                    @foreach(['UH', 'PTS', 'PAS', 'Tambahan'] as $type)
                                        @if(isset($mapelData['exercises'][$type]))
                                            @foreach($mapelData['exercises'][$type] as $ex)
                                                <td>
                                                    @if($ex['point'] !== null)
                                                        @php $allPoints[] = $ex['point']; @endphp
                                                        {{ $ex['point'] }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endforeach
                                        @endif
                                    @endforeach
                                    
                                    {{-- Average --}}
                                    <td>
                                        @if(count($allPoints) > 0)
                                            <strong>{{ round(array_sum($allPoints) / count($allPoints), 1) }}</strong>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="font-size: 9px; color: #666; margin: 5px 0;">Belum ada data untuk mata pelajaran ini.</p>
                @endif
            </div>
        @endforeach
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
        <p style="margin-top: 3px;"><em>Angka pada kolom header menunjukkan nomor urut tugas/soal per mata pelajaran.</em></p>
    </div>
</body>
</html>
