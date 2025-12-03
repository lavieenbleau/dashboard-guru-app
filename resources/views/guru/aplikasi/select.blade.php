@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2>Pilih Aplikasi Kurikulum</h2>
    <p class="text-muted">Silakan pilih aplikasi yang ingin Anda kelola.</p>

    <div class="row mt-4">

        @foreach ($aplikasi as $item)
        <div class="col-md-4 mb-3">
            <a href="/aplikasi/{{ $item->id }}" class="text-decoration-none">
                <div class="card p-3 shadow-sm">
                    <h5 class="fw-bold">{{ $item->product->name }}</h5>
                    <p class="text-muted mb-0">
                        Grade: {{ $item->product->grade }} <br>
                        Semester: {{ $item->product->semester }}
                    </p>
                </div>
            </a>
        </div>
        @endforeach

    </div>
</div>
@endsection