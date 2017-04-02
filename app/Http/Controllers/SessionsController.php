<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\User;

class SessionsController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('test');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
          return redirect()->intended(route('index'));
        }else {
          session()->flash('danger','很抱歉，您的帐号和密码不匹配')；
          return redirect()->back();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Auth::logout();
        session()->flash('success','您已经成功退出');
        return redirect('login');
    }
}
