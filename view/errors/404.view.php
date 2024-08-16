@extends('errors/layouts/app')
@section('title') 404 @endsection
@section('content')
    <div class="flex flex-col items-center justify-center gap-5 py-[80px]">
        <h1 class="text-4xl font-bold md:text-6xl">404</h1>
        <a href="<?php echo uri('/'); ?>" class="rounded border-[1px] border-solid border-blue-600 bg-blue-600 px-8 py-3 text-white transition hover:bg-transparent hover:text-blue-600">Back to home</a>
    </div>
@endsection