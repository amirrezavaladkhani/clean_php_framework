<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="{{asset()}}" data-template="vertical-menu-template">

@include('layouts.head')
<body>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        @include('layouts.sidebar')

        <div class="layout-page">
            @include('layouts.navbar')
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    @yield('content')
                </div>
                @include('layouts.footer')
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

</div>

@include('layouts.scripts')

</body>
</html>