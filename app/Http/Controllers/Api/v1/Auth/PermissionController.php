<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Api\v1\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends BaseController
{
    public function createRole(Request $request){
        $validator = Validator::make($request->all(),[
            'name'  => 'required|string|max:255|unique:'.config('permission.table_names.roles','roles'),
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $role = Role::create(['name' => $request['name']]);

        return $this->sendResponse($role->toArray(), 'Role created successfully.');
    }

    public function createPermission(Request $request){

        $validator = Validator::make($request->all(),[
            'name'  => 'required|string|max:255|unique:'.config('permission.table_names.permissions','permissions'),
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $permission = Permission::create(['name' => $request['name']]);

        return $this->sendResponse($permission->toArray(), 'Permission created successfully.');

    }

    public function givePermissionToRole(Request $request){
        $validator = Validator::make($request->all(),[
            'role'          => 'required|string|max:255',
            'permission'    => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $role = Role::where('name', $request['role'])->first();

        if($role){
            $permission = Permission::where('name', $request['permission'])->first();
            if($permission){
                $role->givePermissionTo($request['permission']);
            }else{
                return $this->sendError('Permission Not Found.');
            }
        }else{
            return $this->sendError('Role Not Found.');
        }

        return $this->sendResponse([],'Permission successfully add to Role.');

    }

    public function giveRoleToUser(Request $request){
        $validator = Validator::make($request->all(),[
            'role'      => 'required|string|max:255',
            'user_id'   => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::where('id', $request['user_id'])->first();

        if($user){
            $role = Role::where('name', $request['role'])->first();
            if($role){
                $user->assignRole($request['role']);
            }else{
                return $this->sendError('Role Not Found.');
            }
        }else{
            return $this->sendError('User Not Found.');
        }

        return $this->sendResponse([],'Role successfully add to User.');
    }

    public function givePermissionToUser(Request $request){
        $validator = Validator::make($request->all(),[
            'permission'    => 'required|string|max:255',
            'user_id'       => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::where('id', $request['user_id'])->first();

        if($user){
            $permission = Permission::where('name', $request['permission'])->first();
            if($permission){
                $user->givePermissionTo($request['permission']);
            }else{
                return $this->sendError('Permission Not Found.');
            }
        }else{
            return $this->sendError('User Not Found.');
        }

        return $this->sendResponse([],'Permission successfully add to User.');
    }

    public function getPermissionByUser(Request $request,$user_id){
        $user = User::where('id', $user_id)->first();
        if($user){
            $data = [
                'user'                => $user->toArray(),
                'success'             => false,
                'roles'               => $user->getRoleNames(),
                'directPermissions'   => $user->getDirectPermissions(),
                'permissionsViaRoles' => $user->getPermissionsViaRoles(),
            ];
            return $this->sendResponse($data);
        }else{
            return $this->sendError('User Not Found.');
        }
    }
}
