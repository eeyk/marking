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
use Auth;

use Excel;
use App\jobs\CreatePlayer;
use App\jobs\CreateUser;


class AdminController extends Controller
{

    public function admin()
    {
        $name = Auth::user()->name;
        $activities = Activity::get(array('id','name','details','img'));
        $oldActivities = Activity::onlyTrashed()->get(array('name','id','details','img'));
    //    return view('admin',compact('activities','oldActivities'));
        return view('admin')->with('activities',$activities)
                            ->with('oldActivities',$oldActivities)
                            ->with('name',$name);

    }

    public function getCreateActivity()
    {
        $name = Auth::user()->name;
        return view('createactivity')->with('name',$name);
    }

    public function showActivity($id)
    {
        if(!Activity::where('id',$id)->exists()) {session()->flash('waring','活动不存在或已经结束'); return redirect()->back();}
        if(!User::where('activity_id',$id)->exists()) {session()->flash('waring','还未创建评委'); return redirect()->back();}
        if(!Player::where('activity_id',$id)->exists()) {session()->flash('waring','还未创建选手'); return redirect()->back();}
        $activity = Activity::where('id',$id)->first(array('id','name','details','img'));
        $users = User::where('activity_id','=',$id)->get(array('id','name','weight'));
        $players = Player::where('activity_id','=',$id)->get(array('id','name','score'));
        //return view('activity',compact('activity','users','players'));
        /*
        return response()->json(array(
                            'status'=>true,
                            'url'=>route('showActivity',$id),
                            'activity'=>$activity,
                            'users'=>$users,
                            'players'=>$players,
                          ));
        */
        return view('activity')->with('activity',$activity)
                               ->with('users',$users)
                               ->with('players',$players);

    }

    public function postCreateActivity(Request $request)
    {
        $data = array('name' =>$request->name,
                      'details'=>$request->details,
                  );

        if($request->file('file'))
        {
            $file = $request->file('file');
            $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
            $file = $file->move('img/',$newFileName);
            $file = 'img/'.$newFileName;
            $data['img'] = $file;
        }else {
            $data['img'] = $activity->img;
        }
        Activity::create($data);
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
        $activity_id = $request->id;
        $file = $request->file('file');
        $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
        $file = $file->move('xls/',$newFileName);
        $file = 'xls/'.$newFileName;

        $data['playersNum'] = 0;
        $inputPlayers = Excel::selectSheetsByIndex(0)->load($file,function($reader){})->ignoreEmpty()->get();
        foreach ($inputPlayers as $inputPlayer) {
            $player = Player::create([
                'name'=>$inputPlayer->name,
                'details'=>$inputPlayer->details,
                'activity_id'=>$activity_id,
                'group' => $inputPlayer->group,
                'groupName'=>$inputPlayer->groupName,
            ]);
            $data['playersNum'] = $data['playersNum']+1;
        }
        $activity = Activity::findOrFail($activity_id);
        $data['playersNum'] = $activity->playersNum+$data['playersNum'];
        $activity->update($data);

        return response()->json(array('url'=>route('showActivity',$request->id)));

    }

    public function createUser(Request $request)
    {
        $activity_id = $request->id;
        $file = $request->file('file');
        $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
        $file = $file->move('xls/',$newFileName);
        $file = 'xls/'.$newFileName;

        $data['levelA']=0;$data['levelB']=0;$data['levelC']=0;
        $inputUsers = Excel::selectSheetsByIndex(0)->load($file,function($reader){})->ignoreEmpty()->get();
            foreach ($inputUsers as $inputUser) {
                $user = User::create([
                    'name'=>$inputUser->name,
                    'details'=>$inputUser->details,
                    'account'=>$inputUser->account,
                    'password'=>bcrypt($inputUser->password),
                    'weight'=>$inputUser->weight,
                    'activity_id'=>$activity_id,
                    'level'=>$inputUser->level,
                ]);
                switch ($inputUser->level) {
                    case 'A':
                        $data['levelA']=$data['levelA']+1;
                        break;
                    case 'B':
                        $data['levelB']=$data['levelB']+1;
                        break;
                    case 'C':
                        $data['levelC']=$data['levelC']+1;
                        break;
                    default:
                        break;
                }
            }
            $activity = Activity::findOrFail($activity_id);
            $data['levelA'] = $activity->levelA+$data['levelA'];
            $data['levelB'] = $activity->levelB+$data['levelB'];
            $data['levelC'] = $activity->levelC+$data['levelC'];
            $activity->update($data);

        return response()->json(array('url'=>route('showActivity',$request->id)));

    }

    public function getUpdateActivity($id)
    {
        if(!Activity::where('id',$id)->exists())
        {
            session()->flash('waring','活动不存在或已经结束');
            return redirect()->back();
        }
        $activity = Activity::where('id',$id)->first(array('id','name','details','img'));
        //  return view('updateActivity',compact('activity'));
        // return response()->json(array('activity'=>$activity,'url'=>route('updateActivity',$id),'status'=>true));
         return view('updateActivity')->with('activity',$activity);
    }

    public function getUpdateUser($id)
    {
        if(!User::where('id',$id)->exists())
        {
            session()->flash('waring','评委不存在或活动已经结束');
            return redirect()->back();
        }
        $user = User::where('id',$id)->first(array('id','activity_id','name','details','weight','level'));
        //  return view('updateuser',compact('user'));
        //return response()->json(array('user'=>$user,'url'=>route('updateUser',$id),'status'=>true));
        return view('updateUser')->with('user',$user);
    }

    public function getUpdatePlayer($id)
    {
        if(!Player::where('id',$id)->exists())
        {
            session()->flash('waring','选手不存在或活动已经结束');
            return redirect()->back();
        }
        $player = Player::where('id',$id)->first(array('id','activity_id','name','details','score','isMarking','group','img'));
        //return view('updateplayer',compact('player'));
        //return response()->json(array('player'=>$player,'url'=>route('updatePlayer',$id),'status'=>true));
        return view('updatePlayer')->with('player',$player);
    }

    public function updateUser($id,Request $request)
    {
        $this->validate($request,[
            'name'=>'required',
            'password'=>'confirmed|min:6',
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
            'img'=>'required',
            'group'=>'required',
            'groupName'=>'required',
        ]);

        $player = Player::findOrFail($id);
        $data = array('name' =>$request->name ,
                      'details'=>$request->details,
                      'img'=>$request->img,
                      'group'=>$request->group,
                      'groupName'=>$request->groupName,
                  );

        if($request->file('file'))
        {
            $file = $request->file('file');
            $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
            $file = $file->move('img/',$newFileName);
            $file = 'img/'.$newFileName;
            $data['img'] = $file;
        }else
            {
                $data['img'] = $player->img;
            }
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

        $activity = Activity::findOrFail($id);
        $data = array('name' =>$request->name,
                      'details'=>$request->details,
                  );

        if($request->file('file'))
        {
            $file = $request->file('file');
            $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
            $file = $file->move('img/',$newFileName);
            $file = 'img/'.$newFileName;
            $data['img'] = $file;
        }else {
            $data['img'] = $activity->img;
        }

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
        return response()->json(array('status'=>true,'url'=>route('index')));
    }

    public function restore($id)
    {
        User::withTrashed()->where('activity_id','=',$id)->restore();
        Player::withTrashed()->where('activity_id','=',$id)->restore();
        Activity::withTrashed()->where('id','=',$id)->restore();
        Score::withTrashed()->where('activity_id','=',$id)->restore();
        return response()->json(array('status'=>true,'url'=>route('index')));
    }

    public function showOldActivity($id)
    {
        if(!Activity::onlyTrashed()->where('id',$id)->exists()) //{return response()->json(array('status'=>false));}
        {
            session()->flash('warning','该活动不存在或没有结束');

        }
        $oldActivity = Activity::onlyTrashed()->where('id',$id)->first(array('name'));
        $users = User::onlyTrashed()->where('activity_id','=',$id)->get(array('id','name'));
        $players = Player::onlyTrashed()->where('activity_id','=',$id)->orderBy('score','desc')->get(array('id','name','score'));
//      return view('activity',compact('activity','users','players'));
        return view('oldActivity')->with('oldActivity',$oldActivity)
                       ->with('users',$users)
                       ->with('players',$players);
        /*
        return response()->json(array(
                            'status'=>true,
                            'url'=>route('showOldActivity',$id),
                            'oldActivity'=>$oldActivity,
                            'users'=>$users,
                            'players'=>$players,
                          ));
        */
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
                          ->orderBy('score','desc')
                          ->get(array('id','name','score'));
         return view('markedPlayer')->with('players',$players);
        }else{
          session()->falsh('warning','没有发现完成评分的选手');
          return redirect()->back();
        }
        /*
          return response()->json(array('players'=>$players,'status'=>true,'url'=>route('markedPlayer',$id)));
        }else{
          return response()->json(array('status'=>false,'url'=>route('markedPlayer',$id)));
        }
        */
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
                          ->get(array('id','name'));
         $users = User::where('activity_id',$activity_id)->get(array('id'));

         $data = [];
         $i = 0;
         foreach ($users as $user)
         {
             $i = $i+1;
             $data[$i] = $user->id;
         }
         $count = $i;
        foreach ($players as $player)
        {
            for ($i=1; $i <= $count ; $i++)
            {
                if(!$this->isMarking($player->id,$data[$i]))
                {
                    $player->num = $player->num + 1;
                }
            }
        }
        return view('unMarkedPlayer')->with('players',$players);
    }else{
        session()->falsh('warning','没有发现未完成评分的选手');
        return redirect()->back();
    }
          /*
          return response()->json(array('players'=>$players,'status'=>true,'url'=>route('unMarkedPlayer',$id)));
        }else{
          return response()->json(array('status'=>false,'url'=>route('unMarkedPlayer',$id)));
        }
        */
    }

    public function markedPlayerDetail($id)
    {
        if(!Player::where('id',$id)->exists()) //{return response()->json(array('status'=>false));}
        {
            session()->flash('warning','选手不存在');
            return redirect()->back();
        }
        $player = Player::findOrFail($id);
        if($player->isMarking < '1')//{return response()->json(array('status'=>false,'url'=>route('markedPlayerDetail',$id)));}
        {
            session()->falsh('warning','该选手评分未完成');
            return redirect()->back();
        }
        $player = Player::where('id',$id)->first(array('id','name','score'));
        $score = Score::where('player_id',$id)->get(array('user_id','score','weight'));
        foreach ($score as  $value) {
            $user = User::where('id',$value->user_id)->first();
            $value->name = $user->name;
        }
    //    return view('markedPlayerDetail',compact('player','scores'));
    //    return response()->json(array('player'=>$player,'score'=>$score,'status'=>true,'url'=>route('markedPlayerDetail',$id)));
          return view('markedPlayerDetail')->with('player',$player)
                                          ->with('score',$score);
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
        if(!Player::where('id',$id)->exists()) //{return response()->json(array('status'=>false));}
        {
            session()->flash('warning','选手不存在');
            return redirect()->back();
        }
        $player = Player::findorFail($id);
        if($player->isMarking >= '1')//{return response()->json(array('status'=>false,'url'=>route('markedPlayerDetail',$id)));}
        {
            session()->falsh('warning','该选手评分已完成');
            return redirect()->back();
        }
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
                $user->isMarking = true;
            }else{
                $user->isMarking = false;
                $user->score = 0;
                }
        }
        $player = Player::where('id',$id)->first(array('id','name','score'));
        //return view('unMarkedPlayerDetail',compact('player','users'));
        //return response()->json(array('player'=>$player,'users'=>$users,'status'=>true,'url'=>route('unMarkedPlayerDetail',$id)));
        return view('unMarkedPlayerDetail')->with('player',$player)
                                           ->with('users',$users);
    }

}
