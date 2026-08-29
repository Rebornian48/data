@extends('layouts.admin')
@section('page_title', 'Tambah Peta')
@section('content')
<form method="POST" action="{{ route('admin.maps.store') }}">
    @include('admin.maps._form')
</form>
@endsection
