<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Sistema administrativo'))</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icoSegey.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/icoSegey.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="admin-app">
@php
    $currentUser = auth()->user();
    if ($currentUser) {
        $currentUser->loadMissing('role.permissions');
    }
@endphp
<div class="admin-layout admin-shell">
    @include('layouts.partials.sidebar')
    <div class="admin-main">
        @include('layouts.partials.topbar')
        <main class="admin-content">
            <div class="container-fluid">
                @include('layouts.partials.flash')
                @yield('content')
            </div>
        </main>
        @include('layouts.partials.footer')
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('js/angular-1.8.2/angular.min.js') }}"></script>
<script src="{{ asset('js/app/app.module.js') }}"></script>
<script src="{{ asset('js/app/services/cct.service.js') }}"></script>
<script src="{{ asset('js/app/services/user.service.js') }}"></script>
<script src="{{ asset('js/app/services/role.service.js') }}"></script>
<script src="{{ asset('js/app/controllers/cct.controller.js') }}"></script>
<script src="{{ asset('js/app/controllers/user.controller.js') }}"></script>
<script src="{{ asset('js/app/controllers/role.controller.js') }}"></script>
<script src="{{ asset('js/app/controllers/delivery.controller.js') }}"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>
@stack('scripts')
</body>
</html>
