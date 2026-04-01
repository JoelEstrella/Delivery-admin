@extends('layouts.auth')

@section('title', 'Iniciar sesión | ' . config('app.name', 'Sistema administrativo'))

@section('content')
    <div class="auth-brand">
        <div class="admin-brand admin-brand--auth">
            <img src="{{ asset('images/segey.png') }}" alt="Logo institucional" class="admin-brand__logo">
        </div>
        <h1 class="h4 fw-bold mb-1">Acceso al sistema</h1>
        <p class="text-muted mb-0">Ingresa con tu usuario o correo institucional.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.submit') }}" method="POST" class="mt-3">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Usuario o correo</label>
            <input type="text" name="login" value="{{ old('login') }}" class="form-control form-control-lg" placeholder="">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Contraseña</label>
            <input type="password" name="password" class="form-control form-control-lg" placeholder="">
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">Ingresar</button>
    </form>

    <div class="mt-4 text-center small text-muted">
        Sistema institucional preparado para operaciones administrativas.
    </div>
@endsection
