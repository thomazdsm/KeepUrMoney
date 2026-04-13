<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/scss/theme/app.scss', 'resources/js/app.js'])
    </head>
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <div class="app-wrapper">

            <!--begin::Header-->
            @include('layouts.navigation')
            <!--end::Header-->

            <!--begin::Sidebar-->
{{--            @include('layouts.sidebar')--}}
            <x-adminlte-sidebar-menu></x-adminlte-sidebar-menu>
            <!--end::Sidebar-->

            <!--begin::App Main-->
            <main class="app-main">
                <!--begin::App Content Header-->
                @isset($header)
                    <div class="app-content-header">
                        <!--begin::Container-->
                        <div class="container">
                            <!--begin::Row-->

                            <div class="d-flex justify-content-between align-items-center mb-4">
{{--                            <div class="row">--}}
                                <div id="header">
                                    <h3 class="mb-0">{{ $header }}</h3>
                                </div>
                                <div id="actionButton">
                                    @isset($actionButton)
                                        {{ $actionButton }}
                                    @endisset
                                </div>
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Container-->
                    </div>
                @endisset

                <!-- Page Content -->
                <div class="app-content">
                    {{ $slot }}
                </div>
            </main>

            <!--begin::Footer-->
            <footer class="app-footer">
                <!--begin::To the end-->
                <div class="float-end d-none d-sm-inline">Anything you want</div>
                <!--end::To the end-->
                <!--begin::Copyright-->
                <strong>
                    Copyright &copy; 2014-2026&nbsp;
                    <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
                </strong>
                All rights reserved.
                <!--end::Copyright-->
            </footer>
            <!--end::Footer-->

            @isset($scripts)
                {{ $scripts }}
            @endisset
        </div>
    </body>
</html>
