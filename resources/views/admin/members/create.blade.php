@extends('layouts.admin')

@section('title', 'Tambah Member')
@section('page_title', 'Tambah Member Baru')

@section('content')
<form method="POST" action="{{ route('admin.members.store') }}">
    @include('admin.members._form')
</form>
@endsection
