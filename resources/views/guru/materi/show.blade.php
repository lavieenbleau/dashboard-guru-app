@extends('layouts.sneat')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h3 class="fw-bold">{{ $lesson->name }}</h3>

    <a href="{{ route('guru.materi.create', [$serial->id, $lesson->id]) }}" class="btn btn-primary">
        <i class="bx bx-plus"></i> Tambah Materi
    </a>
</div>

<div class="card p-4 shadow-sm">

    @foreach ($lesson->items as $item)
    <div class="border-bottom mb-3 pb-3">
        <h5 class="fw-bold">{{ $item->number }}. {{ $item->title }}</h5>

        @if($item->embed)
        <div class="mt-2">{!! $item->embed !!}</div>
        @endif

        <div class="mt-2">
            <a href="{{ route('guru.materi.edit', [$serial->id, $lesson->id, $item->id]) }}"
                class="btn btn-sm btn-warning">
                Edit
            </a>

            <form action="{{ route('guru.materi.delete', [$serial->id, $lesson->id, $item->id]) }}" method="POST"
                class="d-inline">
                @csrf @method('DELETE')
                <button onclick="return confirm('Hapus materi ini?')" class="btn btn-sm btn-danger">
                    Hapus
                </button>
            </form>
        </div>
    </div>
    @endforeach

</div>

@endsection