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
          session()->flash('success','欢迎登录！');
          return redirect()->route('index');
        }else {
          session()->flash('danger','很抱歉，您的帐号和密码不匹配');
          return redirect()->back();
        }
    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success','您已经成功退出');
        return redirect('login');
    }
}
