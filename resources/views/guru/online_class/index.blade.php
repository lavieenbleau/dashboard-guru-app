@extends('layouts.app')

@section('title', 'Online Class')

@section('content')
<div class="container py-4">
    <h3>Online Class — Aplikasi: {{ $serial->product->name ?? '—' }}</h3>
    <p class="text-muted">Serial ID: {{ $serial->id }}</p>

    <div class="card">
        <div class="card-body">
            <p>This is a placeholder page for the online class list. Implement the real UI in resources/views/guru/online_class/index.blade.php.</p>
        </div>
    </div>
</div>
@endsection
