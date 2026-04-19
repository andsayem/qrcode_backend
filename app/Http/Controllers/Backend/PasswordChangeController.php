<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function passwordUpdate(Request $request)
    {
        if (Hash::check($request->old_password, auth()->user()->password)) {
            auth()->user()->password = bcrypt($request->new_password);
            auth()->user()->save();

            return redirect()->back()->with('success', ['Password changed']);
        } else {

            return redirect()->back()->with('fail', ['Invalid old password.']);;
        }
    }
}
