<?php

namespace App\Http\Controllers\Backend;

use DB;
use Hash;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        return view('auth.login');
    }

    public function loginPost(LoginRequest $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'email' => 'required|email|exists:users,email',
        //     'password' => 'required|min:8'
        // ]);
        // if ($validator->fails()) {
        //     return back()->withInput()->withError('Invalid Credentials!')->with('fail', $validator->errors()->all());
        // }

        $userdata = [
            'email' => $request->email,
            'password' => $request->password
        ];
        if (Auth::attempt($userdata)) {
            return redirect(Route('admin.dashboard'));
        } else {
            return back()->withInput()->withError('Invalid Credentials!')->with('fail', ['Invalid Credentials!']);
        }
    }


}
