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

    public function admin()
    {
        $activities=Activity::all();
        $oldActivities = Activity::onlyTrashed()->get();
    //    return view('admin',compact('activities','oldActivities'));
        $data = array();
        $data['url'] = route('admin');
        $i = 0;
        foreach($activities as $activity)
        {
          $i = $i+1;
          $data[$i] = array('name'=>$activity->name,
                           'id'=>$activity->id,
          );
        }
        $data['activityNum'] = $i;
        foreach($oldActivities as $oldActivity)
        {
          $i = $i+1;
          $data[$i] = array('name'=>$oldActivity->name,
                           'id'=>$oldActivity->id,
          );
        }
        $data['olaActivityNum'] = $i-$data['activityNum'];
        return response()->json($data);
    }

    public function showActivity($id)
    {
        $activity = Activity::findOrFail($id);
        $users = User::where('activity_id','=',$id)->get(array('id','name'));
        $players = Player::where('activity_id','=',$id)->get(array('id','name','score'));
    //    return view('activity',compact('activity','users','players'));
        return response()->json(array(
                            'status'=>true,
                            'url'=>route('showActivity',$id),
                            'activity'=>$activity,
                            'users'=>$users,
                            'players'=>$players,
                          ));
    }

    public function postCreateActivity(Request $request)
    {
        Activity::create([
            'name'=>$request->name,
            'details'=>$request->details,
        ]);
        //  return redirect()->route('index')->with('status','true');
        return response()->json(array('status'=>true,'url'=>route('admin')));
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
        $job = new CreatePlayer($request);
        $this->dispatch($job);
    //    return redirect()->route('showActivity',$request->id);
        return response()->json(array('url'=>route('showActivity',$request->id)));

    }

    public function createUser(Request $request)
    {
        $job = new CreateUser($request);
        $this->dispatch($job);
    //    return redirect()->route('showActivity',$request->id);
        return response()->json(array('url'=>route('showActivity',$request->id)));

    }

    public function getUpdateActivity($id)
    {
        $activity = Activity::findOrFail($id);
        //  return view('updateActivity',compact('activity'));
         return response()->json(array('activity'=>$activity,'url'=>route('updateActivity',$id)));
    }

    public function getUpdateUser($id)
    {
        $user = User::findOrFail($id);
        //  return view('updateuser',compact('user'));
        return response()->json(array('user'=>$user,'url'=>route('updateUser',$id)));
    }

    public function getUpdatePlayer($id)
    {
        $player = Player::findOrFail($id);
        //  return view('updateplayer',compact('player'));
        return response()->json(array('player'=>$player,'url'=>route('updatePlayer',$id)));
    }

    public function updateUser($id,Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'password'=>'confirmed|min:6',
            //'weight'=>'required|between:0,1'
        ]);
        $user = User::findOrFail($id);
        $data = [];
        $data['name'] = $request->name;
        $data['weight'] = $request->weight;
        $data['details'] = $request->details;
        if($request->account){
            $data['account'] = $request->account;
        }
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        //    return redirect()->route('showActivity',$user->activity_id)->with('status','true');
        return response()->json(array('status'=>true,'url'=>route('showActivity',$user->activity_id)));
    }


    public function updatePlayer($id,Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'details'=>'required',
            'url'=>'required',
            'group'=>'required',
        ]);
        $player = Player::findOrFail($id);
        $data = array('name' =>$request->name ,
                      'details'=>$request->details,
                      'url'=>$request->url,
                      'group'=>$request->group,
                  );
        $player->update($data);
        //    return redirect()->route('showActivity',$player->activity_id)->with('status','true');
        return response()->json(array('status'=>true,'url'=>route('showActivity',$player->activity_id)));
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
        //  return redirect()->route('showActivity',$id)->with('status','true');
        return response()->json(array('status'=>true,'url'=>route('showActivity',$id)));
    }


    public function destroy(Request $request)
    {
        $id = $request->id;
        User::where('activity_id','=',$id)->delete();
        Player::where('activity_id','=',$id)->delete();
        Activity::where('id','=',$id)->delete();
        Score::where('activity_id','=',$id)->delete();
        return redirect()->route('index');
    }

    public function restore($id)
    {
        User::withTrashed()->where('activity_id','=',$id)->restore();
        Player::withTrashed()->where('activity_id','=',$id)->restore();
        Activity::withTrashed()->where('id','=',$id)->restore();
        Score::withTrashed()->where('activity_id','=',$id)->restore();
        return redirect()->route('index');
    }

    public function markedPlayer($id)
    {
      $activity_id = $id;
      $players = Player::where('activity_id',$activity_id)
                       ->where('isMarking','1')
                       ->exists();
      if($players)
       {
         $players = Player::where('activity_id',$activity_id)
                          ->where('isMarking','1')
                          ->get();
      //    return view('markedPlayer',compact('players'))->with('status','true');
      //  }else{
      //    return view('markedPlayer')->with('status','false');
      //  }
          return response()->json(array('players'=>$players,'status'=>true,'url'=>route('markedPlayer',$id)));
        }else{
          return response()->json(array('status'=>false,'url'=>route('markedPlayer',$id)));
        }
    }

    public function unMarkedPlayer($id)
    {
      $activity_id = $id;
      $players = Player::where('activity_id',$activity_id)
                       ->where('isMarking','<','1')
                       ->exists();
      if($players)
       {
         $players = Player::where('activity_id',$activity_id)
                          ->where('isMarking','<','1')
                          ->get();
      //    return view('unMarkedPlayer',compact('players'))->with('status','success');
      //  }else{
      //    return view('unMarkedPlayer')->with('status','false');
      //  }
          return response()->json(array('players'=>$players,'status'=>true,'url'=>route('unMarkedPlayer',$id)));
        }else{
          return response()->json(array('status'=>false,'url'=>route('unMarkedPlayer',$id)));
        }
    }

    public function markedPlayerDetail($id)
    {
        $player = Player::findorFail($id);
        if($player->isMarking < '1'){return response()->json(array('status'=>false,'url'=>route('markedPlayerDetail',$id)));}
        $score = Score::where('player_id',$id)->get();
    //    return view('markedPlayerDetail',compact('player','scores'));
        return response()->json(array('player'=>$player,'score'=>$score,'status'=>true,'url'=>route('markedPlayerDetail',$id)));
    }

    public function isMarking($player_id,$user_id)
    {
        if($marking = Score::where('Player_id',$player_id)
                           ->where('user_id',$user_id)
                           ->exists())
        {return 'true';}else{return "0";}
    }

    public function unMarkedPlayerDetail($id)
    {
      $player = Player::findorFail($id);
      if($player->isMarking >= '1'){return response()->json(array('status'=>false,'url'=>route('markedPlayerDetail',$id)));}
      $activity_id = $player->activity_id;
      $users = User::where('activity_id',$activity_id)->get(array('id','name','weight'));
      foreach ($users as $user)
      {
        if($this->isMarking($id,$user->id))
        {
          $user->score = Score::where('player_id',$id)
                              ->where('user_id',$user->id)
                              ->first()
                              ->score;
        }else{
          $user->score = 0;
        }
      }
  //  return view('unMarkedPlayerDetail',compact('player','users'));
      return response()->json(array('player'=>$player,'users'=>$users,'status'=>true,'url'=>route('unMarkedPlayerDetail',$id)));

    }

}
