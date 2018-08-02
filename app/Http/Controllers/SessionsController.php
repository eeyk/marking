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
            return view('LoginPage');
        }elseif (!(Auth::user()->isAdmin)) {
            return view('UserPage');
        }
        return view('AdministratorPage');
    }

    public function identity()
    {
        $name = Auth::user()->name;
        $id = Auth::user()->id;
        $actId = Auth::user()->activity_id;
        return response()->json(array('name'=>$name,'id'=>$id,'actId'=>$actId));

    }

    public function create()
    {
        return view('LoginPage');
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
          return response()->json(array('result'=>'success','url'=>route('index')));
        }else {
            return response()->json(array('result'=>'pswError','url'=>route('login')));
        }
    }

    public function destroy()
    {
        Auth::logout();
        return redirect('login');
    }
}
