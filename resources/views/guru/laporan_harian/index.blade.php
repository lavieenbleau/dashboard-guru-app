@extends('layouts.app')

@section('title', 'Laporan Harian')

@section('content')
<div class="container py-4">
    <h3>Laporan Harian — Aplikasi: {{ $serial->product->name ?? '—' }}</h3>
    <p class="text-muted">Serial ID: {{ $serial->id }}</p>

    <div class="card">
        <div class="card-body">
            <p>This is a placeholder page for the laporan harian list. Implement the real UI in resources/views/guru/laporan_harian/index.blade.php.</p>
        </div>
    </div>
</div>
@endsection
