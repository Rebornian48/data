@extends('layouts.admin')
@section('page_title', 'Edit Sub-unit: ' . $subUnit->name)
@section('content')
<form method="POST" action="{{ route('admin.sub-units.update', $subUnit) }}">
    @method('PUT')
    @include('admin.sub_units._form')
</form>
@endsection
