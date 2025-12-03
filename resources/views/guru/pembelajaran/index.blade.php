@extends('layouts.app')

@section('title', 'Pembelajaran')

@section('content')
<div class="container py-4">
    <h3>Pembelajaran — Aplikasi: {{ $serial->product->name ?? '—' }}</h3>
    <p class="text-muted">Serial ID: {{ $serial->id }}</p>

    <div class="card">
        <div class="card-body">
            <p>This is a placeholder page for the pembelajaran list. Implement the real UI in resources/views/guru/pembelajaran/index.blade.php.</p>
        </div>
    </div>
</div>
@endsection
