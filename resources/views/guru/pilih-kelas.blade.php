@extends('layouts.guru')

@section('content')
    <h1>Pilih Kelas</h1>
    <p>Pilih kelas untuk melanjutkan (placeholder).</p>
@endsection
@extends('layouts.sneat')

@section('title', 'Pilih Kelas')

@section('content')

<div class="d-flex justify-content-center mb-4">
    <div class="card shadow-sm text-center" style="width:360px; border-radius:12px;">
        <div class="card-body py-3">
            <div class="avatar avatar-xl mb-2 d-flex justify-content-center">
                <span class="avatar-initial rounded-circle bg-primary text-white fs-2 mx-auto">👩‍🏫</span>
            </div>
            <h5 class="mb-0">{{ auth()->user()->name }}</h5>
            <small class="text-muted">
                {{ optional(auth()->user())->instansi ?? optional(auth()->user())->institution ?? optional(auth()->user())->school ?? 'Instansi belum diatur' }}
            </small>
        </div>
    </div>
</div>

<h4 class="fw-bold pb-3 text-center">Pilih Kelas</h4>
<p class="text-muted text-center">Silakan pilih kelas yang ingin Anda kelola.</p>

<div class="row mt-4 justify-content-center">

    @forelse($classrooms as $c)
    <div class="col-md-4 mb-4 d-flex">
        <div class="card h-100 shadow-sm w-100">
            <div class="card-body text-center">

                <div class="avatar avatar-xl mb-3 d-flex justify-content-center">
                    <span class="avatar-initial rounded-circle bg-primary text-white fs-2 mx-auto">👥</span>
                </div>

                <h5 class="card-title mb-1">{{ $c->name }}</h5>
                <p class="text-muted mb-3">Kelas {{ $c->grade }}</p>

                <a href="/kelas/{{ $c->id }}" class="btn btn-primary w-100">
                    Masuk Kelas
                </a>

            </div>
        </div>
    </div>
    @empty
    <p class="text-muted text-center">Tidak ada kelas tersedia untuk akun Anda.</p>
    @endforelse

</div>

@endsection