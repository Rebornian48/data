@extends('layouts.admin')
@section('page_title', 'Tambah Setlist')
@section('content')
<form method="POST" action="{{ route('admin.setlists.store') }}">
    @include('admin.setlists._form')
</form>
@endsection
