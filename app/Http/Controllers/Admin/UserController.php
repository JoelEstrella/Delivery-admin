<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

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


        if ($request->expectsJson()) {
            return Response::success(
                'Registros obtenidos correctamente.',
                $users
            );
        }

        return view('admin.users.index', [
            'title' => 'Usuarios',
            'subtitle' => 'Administración de accesos del sistema',
        ]);
    }

    public function create()
    {
        return Response::success(
            'Registros obtenidos correctamente.',
            [
                'title' => 'Usuarios',
                'subtitle' => 'Crear Usuario',
                'method' => 'POST',
                'submitLabel' => 'Guardar usuario',
                'roles' => Role::orderBy('name')->get(['id', 'name'])
            ]
        );
    }

    public function store(UserRequest $request)
    {
        
        $user = $this->users->create($request->validated());

        if ($request->expectsJson()) {
            return Response::success(
                'Registro creado correctamente.',
                $user,
                201
            );
        }
    }

    public function show(User $user)
    {
        $user->load(['role.permissions']);

        return Response::success(
            'Registros obtenidos correctamente.',
            $user
        );
    }

    public function edit(User $user)
    {
        $user->load(['role.permissions']);

        return Response::success(
            'Registros obtenidos correctamente.',
            [
                'title' => 'Usuarios',
                'subtitle' => 'Editar Usuario',
                'method' => 'PUT',
                'submitLabel' => 'Actualizar usuario',
                'user' => $user,
                'roles' => Role::orderBy('name')->get(['id', 'name'])
            ]
        );
    }

    public function update(UserRequest $request, User $user)
    {

        if (Auth::id() === $user->id && !$request->boolean('is_active')) {
            abort(403, 'No puedes desactivar tu propio usuario.');
        }

        $userUpdate = $this->users->update($user, $request->validated());

        return Response::success(
            'Registros actualizados correctamente.',
            $userUpdate
        );

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
