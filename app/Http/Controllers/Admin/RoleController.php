<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roles;

    public function __construct(RoleService $roles)
    {
        $this->roles = $roles;
        $this->middleware('permission:roles.view')->only(['index', 'show']);
        $this->middleware('permission:roles.create')->only(['store']);
        $this->middleware('permission:roles.update')->only(['update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            $search = trim((string) $request->get('search'));

            $roles = Role::with(['permissions:id,name,module', 'users:id'])
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('slug', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name')
                ->get();

            $roles->transform(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'is_active' => (bool) $role->is_active,
                    'permissions_count' => $role->permissions->count(),
                    'users_count' => $role->users->count(),
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'module' => $permission->module,
                            'label' => $permission->module . ' / ' . $permission->name,
                        ];
                    })->values(),
                ];
            });

            $permissions = Permission::orderBy('module')
                ->orderBy('name')
                ->get()
                ->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'module' => $permission->module,
                        'label' => $permission->module . ' / ' . $permission->name,
                    ];
                })
                ->values();

            return response()->json([
                'roles' => $roles,
                'permissions' => $permissions,
            ]);
        }

        return view('admin.roles.index', [
            'title' => 'Roles',
            'subtitle' => 'Administración de perfiles y permisos',
        ]);
    }

    public function store(RoleRequest $request)
    {
        $role = $this->roles->create($request->validated());
        $role->load(['permissions', 'users']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Rol creado correctamente.',
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'is_active' => (bool) $role->is_active,
                    'permissions_count' => $role->permissions->count(),
                    'users_count' => $role->users->count(),
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'module' => $permission->module,
                            'label' => $permission->module . ' / ' . $permission->name,
                        ];
                    })->values(),
                ]
            ], 201);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function show(Request $request, Role $role)
    {
        $role->load(['permissions:id,name,module', 'users:id,name']);

        $data = [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'description' => $role->description,
            'is_active' => (bool) $role->is_active,
            'permissions_count' => $role->permissions->count(),
            'users_count' => $role->users->count(),
            'permissions' => $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'module' => $permission->module,
                    'label' => $permission->module . ' / ' . $permission->name,
                ];
            })->values(),
            'permission_ids' => $role->permissions->pluck('id')->values(),
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'role' => $data
            ]);
        }

        return redirect()->route('admin.roles.index');
    }

    public function update(RoleRequest $request, Role $role)
    {
        $data = $request->validated();

        if ($role->slug === 'super-admin') {
            $data['is_active'] = true;
        }

        $this->roles->update($role, $data);

        $role->load(['permissions', 'users']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $role->slug === 'super-admin'
                    ? 'El rol Super Admin siempre permanece activo.'
                    : 'Rol actualizado correctamente.',
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'is_active' => (bool) $role->is_active,
                    'permissions_count' => $role->permissions->count(),
                    'users_count' => $role->users->count(),
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'module' => $permission->module,
                            'label' => $permission->module . ' / ' . $permission->name,
                        ];
                    })->values(),
                ]
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Request $request, Role $role)
    {
        if ($role->slug === 'super-admin') {
            $message = 'No se puede desactivar el rol Super Admin.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $message
                ], 422);
            }

            return back()->withErrors(['role' => $message]);
        }

        $this->roles->deactivate($role);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Rol desactivado correctamente.'
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Rol desactivado correctamente.');
    }
}