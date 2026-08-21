@extends('layouts.admin')
@section('page_title', 'Edit: ' . $generation->name)
@section('content')
<form method="POST" action="{{ route('admin.generations.update', $generation) }}">
    @method('PUT')
    @include('admin.generations._form')
</form>
@endsection
