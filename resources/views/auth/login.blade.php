@extends('layouts.auth')

@section('title', 'Iniciar sesión | ' . config('app.name', 'Sistema administrativo'))

@section('content')
    <div class="auth-brand">
        <div class="admin-brand admin-brand--auth">
            <img src="{{ asset('images/segey.png') }}" alt="Logo institucional" class="admin-brand__logo">
        </div>
        <h1 class="ui-page-title auth-page-title mb-1">Acceso al sistema</h1>
        <p class="ui-page-subtitle mb-0">Ingresa con tu usuario o correo institucional.</p>
    </div>

    @if(session('success'))
        <div class="ui-alert ui-alert--success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="ui-alert ui-alert--error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.submit') }}" method="POST" class="mt-3">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Usuario o correo</label>
            <input type="text" name="login" value="{{ old('login') }}" class="form-control" placeholder="">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Contraseña</label>
            <input type="password" name="password" class="form-control" placeholder="">
        </div>

        <button type="submit" class="ui-btn ui-btn--primary w-100 py-3">Ingresar</button>
    </form>

    <div class="mt-4 text-center small text-muted-soft">
        Sistema institucional preparado para operaciones administrativas.
    </div>
@endsection
