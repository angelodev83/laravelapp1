<?php

namespace App\Repositories\API\MgmtApp;

use Illuminate\Http\Request;

use App\Models\API\AppUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuthRepository
{

    public function fetchUser(int $id)
    {   
        $user = User::findOrFail($id);

        return $user;
    }

    public function updateData($request, $user)
    {
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->location = $request->location;
        $user->description = $request->description;

        $user->save();  
    }

    public function store($userData)
    {
        $auth = User::create($this->prepareRegData($userData));
        return $auth;
    }

    public function prepareRegData($userData)
    {
        $firstname = $userData['first_name'];
        $lastname = $userData['last_name'];
        $authData['name'] = $firstname. ' ' .$lastname;
        $authData['email'] = $userData['email'];
        $authData['password'] = Hash::make($userData['password']);
        
        $authData['role_id'] = 1;

        return $authData;
    }

    public function fetchUserWhereEmail($userData)
    {
        $user = User::where('email', '=', $userData['email'])->firstOrFail();

        return $user;
    }

    public function authenticateUser($userData)
    {   
        // if (Auth::guard('drivers-api')->attempt(['email' => $userData['email'], 'password' => $userData['password']])) {
        //     $user = Auth::guard('drivers-api')->user();
        //     $token = $user->createToken('user_token')->plainTextToken;
        //     return response()->json(['user' => $user, 'token' => $token], 200);
        // }
        // else {
        //     return response()->json(['error' => 'Invalid credentials or account not found.'], 401);
        // }
        if(Auth::attempt(['email'=>$userData['email'], 'password'=>$userData['password']]))
        {
            $token = Auth::user()->createToken('user_token')->plainTextToken;
            return response()->json([ 'user' => Auth::user(), 'token' => $token ], 200);
        }
        else{
            return response()->json([
                'errors' => 'Invalid credentials or account not found.'
            ], 400);
        }
    }

    public function logoutUser($id)
    {
        $user = $this->fetchUser($id);

        $user->tokens()->delete();

        return response()->json('User logged out!', 200);
    }
}