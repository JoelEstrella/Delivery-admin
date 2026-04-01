@extends('layouts.auth')

@section('title', 'Recuperar acceso | ' . config('app.name', 'Sistema administrativo'))

@section('content')
    <div class="auth-brand">
        <div class="admin-brand admin-brand--auth">
            <img src="{{ asset('images/segey.png') }}" alt="Logo institucional" class="admin-brand__logo">
        </div>
        <h1 class="ui-page-title auth-page-title mb-1">Recuperar acceso</h1>
        <p class="ui-page-subtitle mb-0">La recuperación automática aún no está habilitada.</p>
    </div>

    <div class="ui-alert ui-alert--info mt-3 mb-0">
        Solicita apoyo al área administrativa para restablecer tu acceso.
    </div>

    <div class="mt-4 d-grid">
        <a href="{{ route('login') }}" class="ui-btn ui-btn--ghost text-center">Volver al inicio de sesión</a>
    </div>
@endsection
