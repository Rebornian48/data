@extends('layouts.admin')
@section('page_title', 'Tambah Sub-unit')
@section('content')
<form method="POST" action="{{ route('admin.sub-units.store') }}">
    @include('admin.sub_units._form')
</form>
@endsection
