@extends('layouts.admin')

@section('title', 'Edit Member')
@section('page_title', 'Edit: ' . $member->name)

@section('content')
<form method="POST" action="{{ route('admin.members.update', $member) }}">
    @method('PUT')
    @include('admin.members._form')
</form>
@endsection
