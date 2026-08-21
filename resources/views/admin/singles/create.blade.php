@extends('layouts.admin')
@section('page_title', 'Tambah Single')
@section('content')
<form method="POST" action="{{ route('admin.singles.store') }}">
    @include('admin.singles._form')
</form>
@endsection
