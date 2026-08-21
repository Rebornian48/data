@extends('layouts.admin')
@section('page_title', 'Edit Single: ' . $single->title)
@section('content')
<form method="POST" action="{{ route('admin.singles.update', $single) }}">
    @method('PUT')
    @include('admin.singles._form')
</form>
@endsection
