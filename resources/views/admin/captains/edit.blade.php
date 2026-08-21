@extends('layouts.admin')
@section('page_title', 'Edit Data Kapten')
@section('content')
<form method="POST" action="{{ route('admin.captains.update', $captain) }}">
    @method('PUT')
    @include('admin.captains._form')
</form>
@endsection
