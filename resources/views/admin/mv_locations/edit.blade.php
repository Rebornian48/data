@extends('layouts.admin')
@section('page_title', 'Edit Lokasi MV')
@section('content')
<form method="POST" action="{{ route('admin.mv-locations.update', $row) }}">
    @method('PUT')
    @include('admin.mv_locations._form')
</form>
@endsection
