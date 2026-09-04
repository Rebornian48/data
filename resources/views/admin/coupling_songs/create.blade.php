@extends('layouts.admin')
@section('page_title', 'Tambah Coupling Song')
@section('content')
<form method="POST" action="{{ route('admin.coupling-songs.store') }}">
    @include('admin.coupling_songs._form')
</form>
@endsection
