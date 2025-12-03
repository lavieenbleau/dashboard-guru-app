@extends('layouts.sneat')

@section('content')

<h2 class="fw-bold">Kelas {{ $classroom->name }}</h2>

<div class="row mt-4">

    <div class="col-md-3 text-center">
        <a href="/materi" class="text-decoration-none">
            <img src="/icons/book.png" width="80">
            <div class="mt-2 fw-bold">Materi</div>
        </a>
    </div>

    <div class="col-md-3 text-center">
        <a href="/pembelajaran" class="text-decoration-none">
            <img src="/icons/backpack.png" width="80">
            <div class="mt-2 fw-bold">Pembelajaran</div>
        </a>
    </div>

    <div class="col-md-3 text-center">
        <a href="/online-class" class="text-decoration-none">
            <img src="/icons/laptop.png" width="80">
            <div class="mt-2 fw-bold">Online Class</div>
        </a>
    </div>

    <div class="col-md-3 text-center">
        <a href="/laporan-harian" class="text-decoration-none">
            <img src="/icons/report.png" width="80">
            <div class="mt-2 fw-bold">Laporan Harian</div>
        </a>
    </div>

</div>

@endsection