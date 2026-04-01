<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $users;

    public function __construct(UserService $users)
    {
        $this->users = $users;
        $this->middleware('permission:users.view')->only(['index', 'show']);
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.update')->only(['edit', 'update']);
        $this->middleware('permission:users.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $users = User::with('role')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get(['id', 'name']);

        if ($request->expectsJson()) {
            return response()->json([
                'users' => $users,
                'roles' => $roles,
            ]);
        }

        return view('admin.users.index', [
            'title' => 'Usuarios',
            'subtitle' => 'Administración de accesos del sistema',
        ]);
    }

    public function create()
    {
        return view('admin.shared.form', [
            'title' => 'Nuevo usuario',
            'subtitle' => 'Captura la información del usuario',
            'route' => route('admin.users.store'),
            'method' => 'POST',
            'backRoute' => route('admin.users.index'),
            'submitLabel' => 'Guardar usuario',
            'entity' => new User(),
            'sections' => $this->formSections(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $user = $this->users->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Usuario creado correctamente.',
                'user' => $user,
            ], 201);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $user)
    {
        $user->load(['role.permissions']);

        return view('admin.shared.show', [
            'title' => 'Detalle de usuario',
            'subtitle' => 'Consulta la información del usuario',
            'backRoute' => route('admin.users.index'),
            'editRoute' => route('admin.users.edit', $user),
            'entity' => $user,
            'sections' => $this->detailSections(),
        ]);
    }

    public function edit(User $user)
    {
        $user->load(['role.permissions']);

        return view('admin.shared.form', [
            'title' => 'Editar usuario',
            'subtitle' => 'Actualiza la información del usuario',
            'route' => route('admin.users.update', $user),
            'method' => 'PUT',
            'backRoute' => route('admin.users.index'),
            'submitLabel' => 'Actualizar usuario',
            'entity' => $user,
            'sections' => $this->formSections($user),
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        if (Auth::id() === $user->id && !$request->boolean('is_active')) {
            return back()->withInput()->withErrors(['is_active' => 'No puedes desactivar tu propio usuario.']);
        }

        $this->users->update($user, $request->validated());

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->withErrors(['user' => 'No puedes eliminar tu propio usuario.']);
        }

        $this->users->delete($user);

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    protected function indexColumns()
    {
        return [
            ['label' => 'Nombre', 'field' => 'name', 'type' => 'text'],
            ['label' => 'Usuario', 'field' => 'username', 'type' => 'text'],
            ['label' => 'Correo', 'field' => 'email', 'type' => 'text'],
            ['label' => 'Rol', 'field' => 'role.name', 'type' => 'text'],
            ['label' => 'Activo', 'field' => 'is_active', 'type' => 'badge', 'map' => [1 => ['label' => 'Sí', 'class' => 'success'], 0 => ['label' => 'No', 'class' => 'secondary']]],
            ['label' => 'Último acceso', 'field' => 'last_login_at', 'type' => 'datetime'],
        ];
    }

    protected function formSections($user = null)
    {
        $roles = Role::orderBy('name')->get();
        $roleOptions = [];

        foreach ($roles as $role) {
            $roleOptions[$role->id] = $role->name;
        }

        return [
            [
                'title' => 'Datos generales',
                'fields' => [
                    ['name' => 'name', 'label' => 'Nombre completo', 'type' => 'text', 'col' => 6],
                    ['name' => 'email', 'label' => 'Correo electrónico', 'type' => 'email', 'col' => 6],
                    ['name' => 'username', 'label' => 'Usuario', 'type' => 'text', 'col' => 6],
                    ['name' => 'role_id', 'label' => 'Rol', 'type' => 'select', 'options' => $roleOptions, 'col' => 6],
                    ['name' => 'password', 'label' => $user ? 'Nueva contraseña' : 'Contraseña', 'type' => 'password', 'col' => 6],
                    ['name' => 'is_active', 'label' => 'Usuario activo', 'type' => 'checkbox', 'col' => 6, 'value' => 1],
                ],
            ],
        ];
    }

    protected function detailSections()
    {
        return [
            [
                'title' => 'Información del usuario',
                'fields' => [
                    ['name' => 'name', 'label' => 'Nombre completo', 'type' => 'text', 'col' => 6],
                    ['name' => 'email', 'label' => 'Correo electrónico', 'type' => 'text', 'col' => 6],
                    ['name' => 'username', 'label' => 'Usuario', 'type' => 'text', 'col' => 6],
                    ['name' => 'role.name', 'label' => 'Rol', 'type' => 'text', 'col' => 6],
                    ['name' => 'is_active', 'label' => 'Activo', 'type' => 'badge', 'map' => [1 => ['label' => 'Sí', 'class' => 'success'], 0 => ['label' => 'No', 'class' => 'secondary']], 'col' => 6],
                    ['name' => 'last_login_at', 'label' => 'Último acceso', 'type' => 'datetime', 'col' => 6],
                    ['name' => 'created_at', 'label' => 'Creado', 'type' => 'datetime', 'col' => 6],
                ],
            ],
        ];
    }
}
