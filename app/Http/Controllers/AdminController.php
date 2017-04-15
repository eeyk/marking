<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Player;
use App\Models\User;
use App\Models\Score;
use Excel;
use App\jobs\CreatePlayer;
use App\jobs\CreateUser;


class AdminController extends Controller
{
    public function getCreateActivity()
    {
        return view('createActivity');
    }

    public function postCreateActivity(Request $request)
    {
        Activity::create([
            'name'=>$request->name,
            'details'=>$request->details,
        ]);
        session()->flash('success','成功创建活动');
        return redirect()->route('index');
    }

/*
    public function postCreateUser(Request $request)
    {
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
*/
    public function createPlayer(Request $request)
    {
        $job=new CreatePlayer($request);
        $this->dispatch($job);
        return redirect()->route('showActivity',$request->id);
    }

    public function createUser(Request $request)
    {
        $job=new CreateUser($request);
        $this->dispatch($job);
        return redirect()->route('showActivity',$request->id);
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
        $data['details']=$request->details;
        if($request->account){
            $data['account']=$request->account;
        }
        if($request->password){
            $data['password']=bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', '评委资料更新成功！');
        return redirect()->route('showActivity',$user->activity_id);
    }


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
        session()->flash('success', '选手资料更新成功！');
        return redirect()->route('showActivity',$player->activity_id);
    }


    public function updateActivity($id,Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'details'=>'required',
        ]);
        $activity=Activity::findOrFail($id);
        $data = array('name' =>$request->name,
                      'details'=>$request->details,
                  );
        $activity->update($data);
        session()->flash('success', '活动资料更新成功！');
        return redirect()->route('showActivity',$id);
    }

    public function admin()
    {
        $activities=Activity::all();
        return view('admin',compact('activities'));
    }

    public function showActivity($id)
    {
        $activity=Activity::findOrFail($id);
        $users=User::where('activity_id','=',$id)->get();
        $players=Player::where('activity_id','=',$id)->get();
        return view('activity',compact('activity','users','players'));
    }

    public function oldActivities()
    {
        $oldActivities=Activity::onlyTrashed()->get();
        return view('oldActivities',compact('oldActivities'));
    }

    public function destroy(Request $request)
    {
        $id=$request->id;
        User::where('activity_id','=',$id)->delete();
        Player::where('activity_id','=',$id)->delete();
        Activity::where('id','=',$id)->delete();
        Score::where('activity_id','=',$id)->delete();
        session()->flash('success','活动结束');
        return redirect()->route('index');
    }

    public function restore($id)
    {
        User::withTrashed()->where('activity_id','=',$id)->restore();
        Player::withTrashed()->where('activity_id','=',$id)->restore();
        Activity::withTrashed()->where('id','=',$id)->restore();
        Score::withTrashed()->where('activity_id','=',$id)->restore();
        session()->flash('success','活动已成功恢复');
        return redirect()->route('index');
    }
}
