@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <x-content-header title="Edit Inward General" />

    <div id="edit-inward-general" data-inward="{{ json_encode($module) }}">

    </div>

</div>
@endsection
