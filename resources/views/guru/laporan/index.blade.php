@extends('layouts.guru')

@section('content')

<h3>Laporan Harian</h3>
<p>Hari ini: {{ date('d F Y') }}</p>

<a href="#" class="btn btn-primary btn-lg mt-3">Isi Laporan Hari Ini</a>

@endsection