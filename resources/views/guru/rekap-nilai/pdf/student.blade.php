<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai - {{ $student->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 5px 0;
            font-size: 16px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 50%;
        }
        .info td {
            padding: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            font-size: 13px;
            margin-bottom: 10px;
            padding: 5px;
            background-color: #f0f0f0;
            border-left: 4px solid #666;
        }
        table.grades {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.grades th,
        table.grades td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        table.grades th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        table.grades td.center {
            text-align: center;
        }
        table.grades tfoot td {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            background-color: #e0e0e0;
            border-radius: 3px;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP NILAI SISWA</h1>
        <h2>{{ $serial->name }}</h2>
    </div>

    <div class="info">
        <table>
            <tr>
                <td style="width: 150px;"><strong>Nama Siswa</strong></td>
                <td>: {{ $student->name }}</td>
            </tr>
            <tr>
                <td><strong>Kelas</strong></td>
                <td>: {{ $classroom->name }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: {{ now()->format('d F Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Tasks Section -->
    <div class="section">
        <h3>Nilai Tugas</h3>
        @if($tasks->isEmpty())
            <p style="padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd;">Belum ada nilai tugas.</p>
        @else
            <table class="grades">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Mata Pelajaran</th>
                        <th>Judul Tugas</th>
                        <th style="width: 60px;">Nilai</th>
                        <th style="width: 100px;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $index => $task)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ $task->post->mapel->name ?? '-' }}</td>
                            <td>{{ $task->post->title ?? '-' }}</td>
                            <td class="center">
                                @if($task->point)
                                    {{ $task->point }}
                                @else
                                    Belum dinilai
                                @endif
                            </td>
                            <td class="center">{{ $task->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;">Rata-rata:</td>
                        <td class="center">
                            @php
                                $avg = $tasks->where('point', '!=', null)->avg('point');
                            @endphp
                            @if($avg)
                                {{ round($avg, 1) }}
                            @else
                                -
                            @endif
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    <!-- Exercise Points Section -->
    <div class="section">
        <h3>Nilai Soal/Ujian</h3>
        @if($exercisePoints->isEmpty())
            <p style="padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd;">Belum ada nilai soal/ujian.</p>
        @else
            <table class="grades">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Mata Pelajaran</th>
                        <th style="width: 80px;">Kategori</th>
                        <th>Judul Soal</th>
                        <th style="width: 60px;">Nilai</th>
                        <th style="width: 100px;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exercisePoints as $index => $point)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>{{ $point->exercise->lesson->mapel->name ?? '-' }}</td>
                            <td class="center">
                                @if($point->exercise->exerciseType)
                                    {{ $point->exercise->exerciseType->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $point->exercise->title }}</td>
                            <td class="center">
                                @if($point->exercise_point)
                                    {{ $point->exercise_point }}
                                @else
                                    Belum dinilai
                                @endif
                            </td>
                            <td class="center">{{ $point->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;">Rata-rata:</td>
                        <td class="center">
                            @php
                                $avg = $exercisePoints->where('exercise_point', '!=', null)->avg('exercise_point');
                            @endphp
                            @if($avg)
                                {{ round($avg, 1) }}
                            @else
                                -
                            @endif
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>
