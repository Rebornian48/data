@extends('layouts.admin')

@section('page_title', 'Tambah Tim')

@section('content')
<form method="POST" action="{{ route('admin.teams.store') }}">
    @include('admin.teams._form')
</form>
@endsection
