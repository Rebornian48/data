@extends('layouts.admin')
@section('page_title', 'Edit Peta: ' . $map->title)
@section('content')
<form method="POST" action="{{ route('admin.maps.update', $map) }}">
    @method('PUT')
    @include('admin.maps._form')
</form>
@endsection
