<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai {{ $classroom->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        h3, h4, h5 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .bg-success { color: green; }
        .bg-primary { color: blue; }
        .bg-warning { color: orange; }
        .bg-danger { color: red; }
    </style>
</head>
<body>
    @php
        if (!function_exists('formatScorePdf')) {
            function formatScorePdf($val) {
                if (is_null($val)) return '-';
                $val = (float)$val;
                if ($val >= 90) return '<span class="bg-success fw-bold">'.$val.'</span>';
                if ($val >= 80) return '<span class="bg-primary fw-bold">'.$val.'</span>';
                if ($val >= 70) return '<span class="bg-warning fw-bold">'.$val.'</span>';
                return '<span class="bg-danger fw-bold">'.$val.'</span>';
            }
        }
    @endphp

    <div class="header">
        <h3>REKAPITULASI NILAI SISWA</h3>
        <h4>Mata Pelajaran: {{ $selectedLesson->name }}</h4>
        <h4>Kelas: {{ $classroom->name }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="min-width: 150px;">NAMA SISWA</th>
                <th>TUGAS</th>
                <th>AKM</th>
                <th>UH</th>
                <th>PTS</th>
                <th>PAS</th>
                <th>NILAI AKHIR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapData as $index => $data)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $data['student']->name }}</td>
                <td class="text-center">{!! formatScorePdf($data['tugas']['avg']) !!}</td>
                <td class="text-center">{!! formatScorePdf($data['akm']['avg']) !!}</td>
                <td class="text-center">{!! formatScorePdf($data['uh']['avg']) !!}</td>
                <td class="text-center">{!! formatScorePdf($data['pts']['avg']) !!}</td>
                <td class="text-center">{!! formatScorePdf($data['pas']['avg']) !!}</td>
                <td class="text-center">{!! formatScorePdf($data['nilai_akhir']) !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="font-size: 10px; color: #555;">
        <p><strong>Keterangan Warna:</strong></p>
        <p>
            <span style="color: green;">Hijau</span> : 90 - 100 <br>
            <span style="color: blue;">Biru</span> : 80 - 89 <br>
            <span style="color: orange;">Kuning</span> : 70 - 79 <br>
            <span style="color: red;">Merah</span> : < 70
        </p>
    </div>
</body>
</html>