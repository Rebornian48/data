@extends('layouts.admin')
@section('page_title', 'Edit Setlist: ' . $setlist->name)
@section('content')
<form method="POST" action="{{ route('admin.setlists.update', $setlist) }}">
    @method('PUT')
    @include('admin.setlists._form')
</form>
@endsection
