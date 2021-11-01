<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Entering the system.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $username = $request->input('id');
        $password = $request->input('password');

        $user = User::where('id', $username)->first();
     
        if ($user && (md5($password) == $user->password)) {
            $apiToken = base64_encode(Str::random(40));

            $user->update([
                'api_token' => $apiToken
            ]);

            $response['success'] = true;
            $response['message'] = "Login Success";
            $response['data'] = [
                'user' => $user,
                'api_token' => $apiToken
            ];
        }else {
            $response['success'] = false;
            $response['message'] = "User not found or Wrong Password";
            $response['data'] = null;
        }
        return response()->json($response, 200);
    }

    /**
     * Checking out from the system.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout($id)
    {
        $user = User::find($id);
        if ($user){
            $user->update([
                'api_token' => null
            ]);
        }
        $response['success'] = true;
        $response['message'] = "Logout Success";
        $response['data'] = null;
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //$dosen = User::where('role', $id)->get();
        $response['success'] = true;
        $response['message'] = "List Dosen";
        //$response['data']    = $dosen;
        $response['data']    = DB::select('SELECT id, nama FROM users WHERE role = :id', ['id' => $id]);
        return response()->json($response, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = \Validator::make($request->all(),[
            'password' => 'required'
        ]);

        if ($validator->fails()){
            $response['success'] = false;
            $response['message'] = $validator->messages();
            $response['data'] = null;
        }else{
            $user = User::find($id);
            $user->password = md5($request->password);
            $user->update();

            $response['success'] = true;
            $response['message'] = "Password berhasil diubah";
            $response['data'] = $user;
        }
        return response()->json($response, 201);
    }

}
