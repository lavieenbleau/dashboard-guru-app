@extends('layouts.sneat')

@section('content')
<h3>{{ $subtema->name }}</h3>

<a href="#" class="btn btn-primary mb-3">+ Tambah Materi</a>

@foreach ($materials as $m)
<div class="card p-3 my-2 shadow-sm">
    <h5>{{ $m->title }}</h5>
    @if($m->youtube_url)
    <iframe width="100%" height="300" src="https://www.youtube.com/embed/{{ $m->youtube_url }}">
    </iframe>
    @endif
</div>
@endforeach
@endsection