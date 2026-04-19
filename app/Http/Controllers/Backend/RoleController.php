<?php

namespace App\Http\Controllers\Backend;


use DB;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    private $view_path = 'backend.roles.';
    private $route_path = 'roles.';
    private $role;

    function __construct()
    {
        $this->role = new Role;
    }


    public function index(Request $request)
    {
        if (!auth()->user()->can('role-list')) {
            abort(403);
        }

        $data['request'] = $request->all();
        $data['tableData'] = Role::orderBy('id', 'asc')->paginate(20);

        return view($this->view_path . 'index')->with($data);
    }


    public function create()
    {
        if (!auth()->user()->can('role-create')) {
            abort(403);
        }

        $permission = Permission::get();
        $modules = Module::with('permissions')->get();

        return view($this->view_path . 'create', compact('permission', 'modules'));
    }

    public function store(RoleRequest $request)
    {
        if (!auth()->user()->can('role-create')) {
            abort(403);
        }

        try {

            \DB::beginTransaction();

            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permission'));

            \DB::commit();
            // toast('Role created.','success');

            return redirect()->route('admin.roles.index')->with('success', ['Role created']);
        } catch (\Exception $e) {
            \DB::rollback();

            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');
            // alert()->error('Failed',trans('alert_message.error_message'));

            return redirect()->back()->with('errorMsg', ['Something went wrong. Please try again later.']);
        }
    }

    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();

        return view($this->view_path . 'show', compact('role', 'rolePermissions'));
    }


    public function edit($id)
    {
        if (!auth()->user()->can('role-edit')) {
            abort(403);
        }

        $modules = Module::with('permissions')->get();
        $editModeData = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view($this->view_path . 'edit', compact('editModeData', 'permission', 'rolePermissions', 'modules'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('role-edit')) {
            abort(403);
        }

        try {
            $this->validate($request, [
                'name' => 'required',
                'permission' => 'required',
            ]);

            \DB::beginTransaction();

            $role = Role::find($id);
            $role->name = $request->input('name');
            $role->save();

            $role->syncPermissions($request->input('permission'));
            \DB::commit();
            // toast('Role updated.','success');

            return redirect()->route('admin.roles.index')->with('success', ['Role updated']);
        } catch (\Exception $e) {
            \DB::rollback();

            $logMessage = formatCommonErrorLogMessage($e);
            writeToLog($logMessage, 'error');
            // alert()->error('Failed',trans('alert_message.error_message'));

            return redirect()->back()->with('fail', ['Something went wrong. Please try again later.']);
        }
    }

    public function destroy($id)
    {
        DB::table("roles")->where('id', $id)->delete();

        return redirect()->route('admin.roles.index')->with('success', ['Role deleted successfully']);
    }
}
