<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $userManagementServiceUrl = config('services.user_management.url') . '/login';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($userManagementServiceUrl, [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        return response()->json(json_decode($response->getBody(), true), $response->getStatusCode());
    }

    // public function register(Request $request){

    //     $registeredData = $request->validate([
    //         'name' => 'required|string',
    //         'email' => 'email|required|string|unique:employees',
    //         'password' => 'required|confirmed'
    //     ]);

    //     $user = Employee::create([
    //         "name" => $request->name,
    //         "email" => $request->email,
    //         "password" => Hash::make($request->password),
    //         "role_id" => $request->role_id
    //     ]);

    //     $token = $user->createToken('passportToken')->accessToken;

    //     return response()->json([
    //         "status" => true,
    //         "message" => "Successfully",
    //         "token" => $token,
    //         "data" => []
    //     ]);
    // }

    public function logout(Request $request){
        $token = $request->header('Authorization');
        $userManagementServiceUrl = config('services.user_management.url') . '/logout';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $token,
        ])->post($userManagementServiceUrl, []);

        return response()->json(json_decode($response->getBody(), true), $response->getStatusCode());
    }

    public function me (Request $request) {
        $token = $request->header('Authorization');
        $userManagementServiceUrl = config('services.user_management.url') . '/me';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => $token,
        ])->get($userManagementServiceUrl);
        return response()->json(json_decode($response->getBody(), true), $response->getStatusCode());
    }

    public function validateToken(Request $request) {
        try{
            return response()->json(['message' => 'Token is Validate'],200);
        } catch (\Exception $e) { 
            return response()->json(['error' => 'Something Wrong..'], 503); 
        } 
    }
}
