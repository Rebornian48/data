@extends('layouts.admin')
@section('page_title', 'Tambah Album')
@section('content')
<form method="POST" action="{{ route('admin.albums.store') }}">
    @include('admin.albums._form')
</form>
@endsection
