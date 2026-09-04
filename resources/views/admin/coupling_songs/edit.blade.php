@extends('layouts.admin')
@section('page_title', 'Edit Coupling: ' . $row->title)
@section('content')
<form method="POST" action="{{ route('admin.coupling-songs.update', $row) }}">
    @method('PUT')
    @include('admin.coupling_songs._form')
</form>
@endsection
