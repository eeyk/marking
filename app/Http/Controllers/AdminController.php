<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Player;
use App\Models\User;

class AdminController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreateActivity()
    {
        return view('test');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postCreateActivity(Request $request)
    {
        Activity::create([
            'name'=>$request->name,
            'details'=>$request->details,
            'usersNum'=>$request->usersNum,
            'playersNum'=>$request->playersNum,
        ]);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreateUser()
    {
        return view('test');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postCreateUser(Request $request,$id)
    {
    #   $activity_id=Activity::where('name','=',$name)->get('id');
        $activity_id=$id;
        $this->validate($request,[
            'name'=>'required|max:50',
            'account'=>'required|account|unique:users|max:255',
            'password'=>'required|confirmed|min:6',
            'weight'=>'required|between:0,1';
        ]);
        $user=User::create([
            'name'=>$request->name,
            'account'=>$request->email,
            'password'=>bcrypt($request->password),
            'weight'=>($request->weight)
        ]);
    }

    public function getCreatePlayer()
    {
        return view('test');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showActivity($id)
    {
        /**
        * @return id对应的活动，选手，评委
        */
    }

    public function getUpdateActivity($id)
    {
        $activity = Activity::findOrFail($id);
        return view('test',compact('activity'));
    }

    public function getUpdateUser($id)
    {
        $user = User::findOrFail($id);
        return view('test',compact('user'));
    }

    public function getUpdatePlayer($id)
    {
        $player = Player::findOrFail($id);
        return view('test',compact('player'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUser($id,Request $request)
    {
        $this->validate($request,[
            'name'=>'required'
            'account'=>'required|max:50',
            'password'=>'confirmed|min:6'
        ]);
        $user=User::findOrFail($id);
        $data=[];
        $data['name']=$request->name;
        $data['weight']=$request->weight;
        if($request->account){
            $data['account']=$request->account;
        }
        if($request->password){
            $data['password']=bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', '评委资料资料更新成功！');


    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePLayer($id,Request $request)
    {
        $this->validate($request,[
            'name'=>'required'
            'details'=>'required',
        ]);
        $player=Player::findOrFail($id);
        $data = array('name' =>$request->name ,
                      'details'=>$request->details );
        $player->update($data);
        session()->flash('success', '选手资料资料更新成功！');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateActivity($id,Request $request)
    {
        $this->validate($request,[
            'name'=>'required'
            'details'=>'required',
        ]);
        $activity=Activity::findOrFail($id);
        $data = array('name' =>$request->name ,
                      'details'=>$request->details,
                      'usersNum'=>$request->usersNum,
                      'playersNum'=>$request->playersNum,
                  );
        $activity->update($data);
        session()->flash('success', '活动资料资料更新成功！');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
