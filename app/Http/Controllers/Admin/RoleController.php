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
        $this->middleware('permission:roles.create')->only(['create', 'store']);
        $this->middleware('permission:roles.update')->only(['edit', 'update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $roles = $this->roles->paginate($request->get('search'));

        return view('admin.shared.index', [
            'title' => 'Roles',
            'subtitle' => 'Administración de perfiles y permisos',
            'createRoute' => route('admin.roles.create'),
            'search' => $request->get('search'),
            'records' => $roles,
            'columns' => $this->indexColumns(),
            'resource' => 'admin.roles',
            'actions' => ['show', 'edit', 'delete'],
        ]);
    }

    public function create()
    {
        return view('admin.shared.form', [
            'title' => 'Nuevo rol',
            'subtitle' => 'Define un nuevo perfil de acceso',
            'route' => route('admin.roles.store'),
            'method' => 'POST',
            'backRoute' => route('admin.roles.index'),
            'submitLabel' => 'Guardar rol',
            'entity' => new Role(),
            'sections' => $this->formSections(),
        ]);
    }

    public function store(RoleRequest $request)
    {
        $this->roles->create($request->validated());

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        $role->users_count = $role->users->count();
        $permissionsHtml = '<ul class="mb-0">';

        foreach ($role->permissions as $permission) {
            $permissionsHtml .= '<li>' . e($permission->module . ' / ' . $permission->name) . '</li>';
        }

        if ($role->permissions->isEmpty()) {
            $permissionsHtml .= '<li class="text-muted">Sin permisos asignados</li>';
        }

        $permissionsHtml .= '</ul>';
        $role->permissions_list = $permissionsHtml;

        return view('admin.shared.show', [
            'title' => 'Detalle de rol',
            'subtitle' => 'Consulta los permisos asignados',
            'backRoute' => route('admin.roles.index'),
            'editRoute' => route('admin.roles.edit', $role),
            'entity' => $role,
            'sections' => $this->detailSections(),
        ]);
    }

    public function edit(Role $role)
    {
        $role->load(['permissions']);

        return view('admin.shared.form', [
            'title' => 'Editar rol',
            'subtitle' => 'Actualiza el perfil de acceso',
            'route' => route('admin.roles.update', $role),
            'method' => 'PUT',
            'backRoute' => route('admin.roles.index'),
            'submitLabel' => 'Actualizar rol',
            'entity' => $role,
            'sections' => $this->formSections($role),
        ]);
    }

    public function update(RoleRequest $request, Role $role)
    {
        if ($role->slug === 'super-admin') {
            $data = $request->validated();
            $data['is_active'] = true;
            $this->roles->update($role, $data);

            return redirect()->route('admin.roles.index')->with('success', 'El rol Super Admin siempre permanece activo.');
        }

        $this->roles->update($role, $request->validated());

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'super-admin') {
            return back()->withErrors(['role' => 'No se puede desactivar el rol Super Admin.']);
        }

        $this->roles->deactivate($role);

        return redirect()->route('admin.roles.index')->with('success', 'Rol desactivado correctamente.');
    }

    protected function indexColumns()
    {
        return [
            ['label' => 'Nombre', 'field' => 'name', 'type' => 'text'],
            ['label' => 'Slug', 'field' => 'slug', 'type' => 'text'],
            ['label' => 'Permisos', 'field' => 'permissions_count', 'type' => 'text'],
            ['label' => 'Usuarios', 'field' => 'users_count', 'type' => 'text'],
            ['label' => 'Activo', 'field' => 'is_active', 'type' => 'badge', 'map' => [1 => ['label' => 'Sí', 'class' => 'success'], 0 => ['label' => 'No', 'class' => 'secondary']]],
        ];
    }

    protected function formSections($role = null)
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $permissionOptions = [];

        foreach ($permissions as $permission) {
            $permissionOptions[$permission->id] = $permission->module . ' / ' . $permission->name;
        }

        $selectedPermissions = $role && $role->relationLoaded('permissions') ? $role->permissions->pluck('id')->all() : [];

        return [
            [
                'title' => 'Datos del rol',
                'fields' => [
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'col' => 6],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'col' => 6],
                    ['name' => 'description', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 4, 'col' => 12],
                    ['name' => 'is_active', 'label' => 'Rol activo', 'type' => 'checkbox', 'col' => 12, 'value' => 1],
                    ['name' => 'permissions', 'label' => 'Permisos', 'type' => 'checkbox_group', 'options' => $permissionOptions, 'selected' => $selectedPermissions, 'col' => 12],
                ],
            ],
        ];
    }

    protected function detailSections()
    {
        return [
            [
                'title' => 'Información del rol',
                'fields' => [
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'col' => 6],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'col' => 6],
                    ['name' => 'description', 'label' => 'Descripción', 'type' => 'text', 'col' => 12],
                    ['name' => 'is_active', 'label' => 'Activo', 'type' => 'badge', 'map' => [1 => ['label' => 'Sí', 'class' => 'success'], 0 => ['label' => 'No', 'class' => 'secondary']], 'col' => 6],
                    ['name' => 'users_count', 'label' => 'Usuarios asignados', 'type' => 'text', 'col' => 6],
                    ['name' => 'permissions_list', 'label' => 'Permisos', 'type' => 'html', 'col' => 12],
                ],
            ],
        ];
    }
}
