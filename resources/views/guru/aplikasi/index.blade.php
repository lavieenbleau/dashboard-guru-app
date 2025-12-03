@extends('layouts.sneat')

@section('content')

<div class="text-center mb-4">
    <!-- Profile Guru -->
    <img src="{{ asset('images/default-user.png') }}"
        style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">

    <h4 class="mt-3">Halo, {{ auth()->user()->name }}</h4>
    <p class="text-muted">Silakan pilih aplikasi kurikulum yang ingin digunakan</p>
</div>

<!-- Daftar Aplikasi / Serial -->
<div class="row justify-content-center">

    @forelse ($serials as $item)
    <div class="col-md-4 col-lg-3 mb-4">
        <a href="{{ url('/guru/aplikasi/'.$item->id) }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm hover-card" style="border-radius: 12px;">
                <div class="card-body text-center">

                    <div class="mb-3">
                        <i class='bx bx-book-open' style="font-size: 48px; color: #696cff;"></i>
                    </div>

                    <h5 class="fw-bold text-dark">
                        {{ $item->product->name }}
                    </h5>

                    <p class="text-muted small mt-1">
                        Grade: {{ $item->product->grade }} <br>
                        Semester: {{ $item->product->semester }}
                    </p>

                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="text-center">
        <p class="text-muted">Anda belum memiliki aplikasi kurikulum.</p>
    </div>
    @endforelse

</div>

@endsection