@extends('layouts.guru')

@section('content')

<h3>{{ $serial->product->name }}</h3>
<p class="text-muted">{{ $serial->classrooms->count() }} Kelas</p>

<div class="row mt-4">

    <div class="col-md-4 mb-3">
        <a href="/materi" class="menu-card shadow">
            <div class="menu-icon">📘</div>
            <h5>Materi / Soal</h5>
        </a>
    </div>

    <div class="col-md-4 mb-3">
        <a href="/kelas" class="menu-card shadow">
            <div class="menu-icon">👥</div>
            <h5>Kelas</h5>
        </a>
    </div>

    <div class="col-md-4 mb-3">
        <a href="/pembelajaran" class="menu-card shadow">
            <div class="menu-icon">🎒</div>
            <h5>Pembelajaran</h5>
        </a>
    </div>

    <div class="col-md-4 mb-3">
        <a href="/online-class" class="menu-card shadow">
            <div class="menu-icon">💻</div>
            <h5>Online Class</h5>
        </a>
    </div>

    <div class="col-md-4 mb-3">
        <a href="/laporan-harian" class="menu-card shadow">
            <div class="menu-icon">📝</div>
            <h5>Laporan Harian</h5>
        </a>
    </div>

</div>

@endsection