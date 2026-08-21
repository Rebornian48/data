@extends('layouts.admin')
@section('page_title', 'Tambah Generasi')
@section('content')
<form method="POST" action="{{ route('admin.generations.store') }}">
    @include('admin.generations._form')
</form>
@endsection
