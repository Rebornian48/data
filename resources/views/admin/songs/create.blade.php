@extends('layouts.admin')
@section('page_title', 'Tambah Lagu')
@section('content')
<form method="POST" action="{{ route('admin.songs.store') }}">
    @include('admin.songs._form')
</form>
@endsection
