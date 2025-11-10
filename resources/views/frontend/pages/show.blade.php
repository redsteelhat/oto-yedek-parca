@extends('layouts.app')

@section('title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">{{ $page->title }}</h1>
        
        <div class="prose max-w-none bg-white rounded-lg shadow-lg p-6">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection

