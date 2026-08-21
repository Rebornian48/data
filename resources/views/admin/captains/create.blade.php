@extends('layouts.admin')
@section('page_title', 'Tambah Kapten')
@section('content')
<form method="POST" action="{{ route('admin.captains.store') }}">
    @include('admin.captains._form')
</form>
@endsection
