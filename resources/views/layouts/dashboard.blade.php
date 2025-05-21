@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('components.dashboard.header')

    <div class="container">
        <div class="content">
            @include('components.dashboard.sidebar')

            <div class="main">
                @yield('dashboard-content')
            </div>
        </div>
    </div>
@endsection
