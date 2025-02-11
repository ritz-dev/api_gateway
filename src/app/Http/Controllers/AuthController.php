<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    public function register(Request $request){

        $registeredData = $request->validate([
            'name' => 'required|string',
            'email' => 'email|required|string|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role_id" => $request->role_id
        ]);

        $token = $user->createToken('SMS')->accessToken;

        return response()->json([
            "status" => true,
            "message" => "Successfully",
            "token" => $token,
            "data" => []
        ]);
    }

    public function login(Request $request)
    {
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password
        ];

        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('SMS')->accessToken;


            $role = Role::where('id',$user->role_id)->pluck('name')->first();
            $role_permissions = RolePermission::where('role_id',$user->role_id)->get();

            $permission = [];

            foreach($role_permissions as $role_permission){
                $permission [] = Permission::where('id',$role_permission->permission_id)->pluck('name')->first();
            }
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'token_type' => 'bearer',
                'admin' => $user,
                'permissions' => $permission,
                'role' => $role
            ]);
        }

        // If no user or admin matches, return an error
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    public function logout(){
        $user = auth()->guard('api')->user();

        if ($user) {
            // Revoke the user's token if Passport is used
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                'message' => "Logout Successfully",
            ]);
        }

        return response()->json([
            'message' => "User not logged in",
        ]);
    }

    public function me (Request $request) {

        $user = auth()->guard('api')->user();

        $name = $user->name;

        $role = Role::where('id',$user->role_id)->pluck('name')->first();

        $role_permissions = RolePermission::where('role_id',$user->role_id)->get();

        $permissionIds = [];

        foreach($role_permissions as $role_permission){
            $permissionIds[] = $role_permission->permission_id;
        }

        $permission = [];

        foreach($permissionIds as $permissionId){
            $permission[] = Permission::where('id',$permissionId)->pluck('name')->first();
        }

        $data = [
            'id' => $user->id,
            'name' => $name,
            'email' => $user->email,
            'role' => $role,
            'permissions' => $permission,
        ];

        if(!$data) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($data);
    }
}
