<!DOCTYPE html>
<html>
<head>
    <title>Laporan Monitoring Kuis</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
        .text-danger { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Laporan Monitoring Kuis</h2>
    <p>Dicetak pada: {{ now()->format('d M Y H:i:s') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Nama Kuis</th>
                <th>Event</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $log->student->name ?? 'Unknown' }}</td>
                <td>{{ $log->exercise->title ?? 'Unknown' }}</td>
                <td>{{ $log->event_type }}</td>
                <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                <td class="{{ $log->suspicious_flag ? 'text-danger' : '' }}">
                    {{ $log->suspicious_flag ? 'Mencurigakan' : 'Normal' }}
                </td>
                <td>{{ $log->ip_address }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
