@extends('layouts.admin')
@section('page_title', 'Tambah Lokasi MV')
@section('content')
<form method="POST" action="{{ route('admin.mv-locations.store') }}">
    @include('admin.mv_locations._form')
</form>
@endsection
