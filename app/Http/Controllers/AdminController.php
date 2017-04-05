<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Player;
use App\Models\User;
use App\Models\Score;

class AdminController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreateActivity()
    {
        return view('createActivity');
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
            #'usersNum'=>$request->usersNum,
            #'playersNum'=>$request->playersNum,
        ]);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function getCreateUser()
    // {
    //     return view('createuser');
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postCreateUser(Request $request)
    {
    #   $activity_id=Activity::where('name','=',$name)->get('id');
        $activity_id=$request->id;
        $this->validate($request,[
            'name'=>'required|max:50',
            'account'=>'required|unique:users|max:255',
            'password'=>'required|confirmed|min:6',
            'weight'=>'required|between:0,1',
            'details'=>'required|max:50',
        ]);
        $user=User::create([
            'name'=>$request->name,
            'account'=>$request->account,
            'password'=>bcrypt($request->password),
            'weight'=>$request->weight,
            'details'=>$request->details,
            'activity_id'=>$activity_id,
        ]);
        $activity=Activity::findOrFail($activity_id);
        $data['usersNum']=$activity->usersNum+1;
        $activity->update($data);


        session()->flash('success','裁判已经成功创建');
        return redirect()->back();
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
        $activity=Activity::findOrFail($id);
        $users=User::where('activity_id','=',$id)->get();
        $players=Player::where('activity_id','=',$id)->get();
        return view('activity',compact('activity','users','players'));
    }



    public function getUpdateActivity($id)
    {
        $activity = Activity::findOrFail($id);
        return view('updateActivity',compact('activity'));
    }

    public function getUpdateUser($id)
    {
        $user = User::findOrFail($id);
        return view('updateuser',compact('user'));
    }

    public function getUpdatePlayer($id)
    {
        $player = Player::findOrFail($id);
        return view('updateplayer',compact('player'));
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
            'name'=>'required',
            'password'=>'confirmed|min:6',
            'weight'=>'required|between:0,1'
        ]);
        $user=User::findOrFail($id);
        $data=[];
        $data['name']=$request->name;
        $data['weight']=$request->weight;
        $date['details']=$request->details;
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
    public function updatePlayer($id,Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
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
            'name'=>'required',
            'details'=>'required',
        ]);
        $activity=Activity::findOrFail($id);
        $data = array('name' =>$request->name ,
                      'details'=>$request->details,
                      #'usersNum'=>$request->usersNum,
                      #'playersNum'=>$request->playersNum,
                  );
        $activity->update($data);
        session()->flash('success', '活动资料资料更新成功！');
    }

    public function rank($id)
    {
        $playerRank = Player::where('id','=',$id)->get(array('name','score'));
    }


    public function rankAll($id)
    {
        $activity_id=$id;
        $activity=Activity::findOrFail($activity_id)->get(array('name','details'))->first();
        #$activity = Activity::where('id','=',$activity_id)->get(array('name','details'))->first();
        $playerRank = Player::where('activity_id','=',$activity_id)->orderBy('score','desc')->get(array('name','score'));
        return view('rankall',compact('playerRank','activity'));
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('activity_id','=',$id)->delete();
        Player::where('activity_id','=',$id)->delete();
        Activity::where('id','=',$id)->delete();
        Score::where('activity_id','=',$id)->delete();
    }

    public function restore($id)
    {
        User::withTrashed()->where('activity_id','=',$id)->restore();
        Player::withTrashed()->where('activity_id','=',$id)->restore();
        Activity::withTrashed()->where('id','=',$id)->restore();
        Score::withTrashed()->where('activity_id','=',$id)->restore();
    }
}
