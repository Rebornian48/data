@extends('layouts.admin')
@section('page_title', 'Edit Album: ' . $album->title)
@section('content')
<form method="POST" action="{{ route('admin.albums.update', $album) }}">
    @method('PUT')
    @include('admin.albums._form')
</form>
@endsection
