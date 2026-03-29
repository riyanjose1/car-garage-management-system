@extends('layouts.app')

@section('content')

<h2 style="margin-bottom:32px;">Discover</h2>

<div class="masonry">
    @for ($i = 1; $i <= 12; $i++)
        <div class="pin">
            <div class="pin-image"></div>
            <h4>Inspiration #{{ $i }}</h4>
            <p>Curated design ideas and visuals</p>
        </div>
    @endfor
</div>

@endsection
