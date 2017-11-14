<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\User;

class SessionsController extends Controller
{

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->guest('login');
        }elseif (!(Auth::user()->isAdmin)) {
            return redirect('getAllPlayers');
        }
        return redirect('admin');
    }

    public function create()
    {
        return view('login');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
          'account' => 'required',
          'password' => 'required'
        ]);

        $credentials=[
          'account' => $request->account,
          'password' => $request->password,
        ];

        if (Auth::attempt($credentials,$request->has('remember')))
        {
          return response()->json(array('status'=>true,'url'=>route('index')));
        }else {
            return response()->json(array('status'=>false,'url'=>route('login')));
        }
    }

    public function destroy()
    {
        Auth::logout();
        return response()->json(array('status'=>true,'url'=>route('login')));
    }
}
