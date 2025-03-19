@extends('layouts.master')

@section('title', 'Page Not Found!')

@section('content')
    <div class="container-xxl container-p-y text-center">
        <div class="misc-wrapper">
            <h1 class="mb-2 mx-2">Page Not Found :(</h1>
            <p class="mb-4 mx-2">We couldn't find the page you are looking for</p>
            <a href="/" class="btn btn-primary">Back to home</a>
            <div class="mt-3">
                <img
                        src="../../assets/img/illustrations/page-misc-error-light.png"
                        alt="page-misc-error-light"
                        width="500"
                        class="img-fluid"
                        data-app-light-img="illustrations/page-misc-error-light.png"
                        data-app-dark-img="illustrations/page-misc-error-dark.png" />
            </div>
        </div>
    </div>
@endsection
