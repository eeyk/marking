<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Player;
use App\Models\Activity;
use App\Models\Score;
use Auth;
use Response;

class UsersController extends Controller
{

    public function getAllPlayers()
    {
        $id = Auth::user()->activity_id;
        if(!Activity::where('id',$id)->exists()) //return response()->json(array('status'=>false,'url'=>route('getAllPlayers'),'msg'=>'活动不存在'));
        {
            session()->flash('warning','活动不存在或已经结束');
            return redirect()->back();
        }
        $activity = Activity::where('id',$id)->first(array('id','name','details','img'));
        if(!PLayer::where('activity_id',$id)->exists()) //return response()->json(array('status'=>false,'url'=>route('getAllPlayers'),'msg'=>'还没有选手'));
        {
            session()->flash('warning','目前还没有选手');
            return redirect()->back();
        }
        $temp = Player::where('activity_id',$id)->orderBy('group','desc')->get(array('group'));
        $groupNums = $temp->first()->group;
        for($i=1;$i<=$groupNums;$i++)
            {
                $data['group'.$i] = Player::where('activity_id','=',$id)->where('group',$i)->get(array('id','name','details','score','isMarking','group','img','groupName'));
                foreach ($data['group'.$i] as $player) {
                    $player->isMarking = $this->isMarking($player->id);
                }
            }
        return view('getAllPlayers')->with('players',$data)
                                    ->with('activity',$activity);
        /*
        return response()->json(array(
                                'players'=>$data,
                                'activity'=>$activity,
                                'url'=>route('getAllPlayers'),
                                'status'=>true,
                            ));
        */
    }

    public function show($id)
    {
        if(!Player::where('id',$id)->exists())// return response()->json(array('status'=>false,'msg'=>'该选手不存在'));
        {
            session()->flash('warning','选手不存在');
            return redirect()->back();
        }
        $player = Player::findOrFail($id);
        $isMarking = $this->isMarking($id);
        $player_id = $id;
        $user_id = Auth::id();
        $data = array('details' => $player->details ,
                           'id' => $player->id,
                          'name'=>$player->name,
                     'isMarking'=>$isMarking,
                           'img'=>$player->img,
                 );
        if($this->isMarking($id))
        {$data['score']=Score::where('player_id','=',$player_id)
                             ->where('user_id','=',$user_id)
                             ->first()->score;
        }else{$data['score'] = 0;}
    //    return view('player',compact('data'));
        return view('player')->with('player',$data);
        //return response()->json(array('player'=>$data,'status'=>true,'url'=>route('playerDetail',$id)));

    }

    public function postScore(Request $request,$id)
    {
        $player = Player::findOrFail($id);
        $user = Auth::user();
        $activity = Activity::findOrFail($player->activity_id);
        $level = 'level'.$user->level;
        $levelNums = $activity->$level;
        if(($player->activity_id!=$user->activity_id) or ($this->isMarking($id)))
            {
                return response()->json(array('status'=>false,'url'=>route('playerDetail',$id),'msg'=>'您无法对其他活动的选手评分'));
            }else
            {
                $this->validate($request,[
                  'score' => 'required',
                ]);
                $score = Score::create([
                    'player_id' => $player->id,
                    'user_id' => $user->id,
                    'score' => ($request->score),
                    'activity_id'=>$activity->id,
                    'weight'=>$user->weight,
                    'levelNums'=>$levelNums,
                ]);
                $data['score'] = $player->score+($request->score)*($user->weight)/($levelNums);
                $data['isMarking'] = $player->isMarking+($user->weight)/($levelNums);
                $player->update($data);
            //    return redirect()->route('index');
                return response()->json(array('status'=>true,'url'=>route('index')));

            }
    }

    public function isMarking($id)
    {
        $player_id = $id;
        $user_id = Auth::id();
        $player = Player::findOrFail($player_id);
           if($marking = Score::where('player_id','=',$player_id)
                            ->where('user_id','=',$user_id)
                            ->exists())
        {return 'true';}else{return "0";}
    }

    // public function rank($id)
    // {
    //     $playerRank = Player::where('id','=',$id)->get(array('name','score'));
    // }


    public function groupRank($id,$group)
    {
        $activity_id = $id;
        $activity = Activity::where('id',$activity_id)->get(array('name','img'))->first();
        $playerRank = Player::where('activity_id',$activity_id)
                            ->where('group',$group)
                            ->orderBy('score','desc')
                            ->get(array('name','score'));
        /*
        return response()->json(array(
                                'playerRank'=>$playerRank,
                                'url'=>route('groupRank',$id).'/'.$group,
                                'status'=>true,
                            ));
        */
        return view('groupRank')->with('playerRank',$playerRank);
    }

    public function rankAll($id)
    {
        $activity_id = $id;
        $activity = Activity::where('id','=',$activity_id)->get(array('name','img'))->first();
        $playerRank = Player::where('activity_id','=',$activity_id)->orderBy('score','desc')->get(array('name','score'));
        /*
        return response()->json(array(
                                'playerRank'=>$playerRank,
                                'url'=>route('rankAll',$id),
                                'status'=>true,
                            ));
        */
        return view('rankall')->with('playerRank',$playerRank);
     }

/*
     public function resetPassword($id,Request $request)
     {
         $this->validate($request,[
             'name'=>'required',
             'password'=>'confirmed|min:6',
         ]);
         $user = User::findOrFail($id);
         $data = [];
         if($request->account){
             $data['account'] = $request->account;
         }
         if($request->password){
             $data['password'] = bcrypt($request->password);
         }
         $user->update($data);
         return response()->json(array('status'=>true,'url'=>route('index')));
     }
*/

}
