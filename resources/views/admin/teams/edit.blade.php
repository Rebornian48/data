@extends('layouts.admin')

@section('page_title', 'Edit Tim')

@section('content')
<form method="POST" action="{{ route('admin.teams.update', $team) }}">
    @method('PUT')
    @include('admin.teams._form')
</form>
@endsection
