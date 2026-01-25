@extends('layouts.guru')

@section('content')
<h1>Dashboard Aplikasi</h1>

@if ($serials->isEmpty())
<div class="alert alert-info">Tidak ada aplikasi / serial terdaftar untuk akun ini.</div>
@else
<div class="row">
    @foreach ($serials as $serial)
    <div class="col-md-4 mb-3">
        <div class="menu-card">
            <h5>{{ $serial->product->name ?? 'Produk' }}</h5>
            <p>Serial: {{ $serial->serial }}</p>
            <a class="btn btn-sm btn-primary" href="#">Buka</a>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
@extends('layouts.guru')

@section('content')

<h3 class="mb-4">Daftar Aplikasi</h3>

<div class="row">

    @forelse($serials as $s)
    <div class="col-md-6 mb-3">
        <a href="/app/{{ $s->id }}" class="card shadow-sm p-3"
            style="text-decoration:none; color:#333; border-radius:16px;">

            <h5>{{ $s->product->name ?? 'Produk Tanpa Nama' }}</h5>
            <p>{{ $s->classrooms->count() }} Kelas</p>
            <p class="text-muted">
                {{ $s->created_at ? $s->created_at->format('d M Y') : '-' }}
            </p>
        </a>
    </div>
    @empty
    <p class="text-muted">Belum ada aplikasi yang aktif untuk akun Anda.</p>
    @endforelse

</div>

@endsection