@extends('layouts.admin')
@section('page_title', 'Edit Lagu: ' . $song->title)
@section('content')
<form method="POST" action="{{ route('admin.songs.update', $song) }}">
    @method('PUT')
    @include('admin.songs._form')
</form>
@endsection
