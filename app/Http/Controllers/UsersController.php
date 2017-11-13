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
        $id=Auth::user()->activity_id;
        $activity=Activity::findOrFail($id);
        $players = Player::where('activity_id','=',$id)->get();
        foreach ($players as $player) {
            $player->isMarking=$this->isMarking($player->id);
        }
        //  return view('getAllPlayers',compact('players','activity'));
        //  返回json数据
        $data = array();
        $data['status'] = true;
        $data['activity'] = array('name'=>$activity->name,'img'=>$activity->img);
        $data['url'] = route('getAllPlayers');
        $i = 0;
        foreach ($players as $player)
        {
          $i = $i+1;
          $data[$i] = array('name'=> $player->name,
                        'isMarking'=> $player->isMarking,
                        'details'=> $player->details,
                        'group' => $player->group,

          );
        }
        $data['num'] = $i;
        //return response()->json(compact('data'));
        return response()->json(array(
                                'players'=>$players,
                                'activity'=>$activity,
                                'url'=>route('getAllPlayers'),
                                'status'=>true,
                            ));
    }

    public function show($id)
    {
        $player = Player::findOrFail($id);
        $isMarking=$this->isMarking($id);
        $player_id=$id;
        $user_id=Auth::id();
        $data = array('details' => $player->details ,
                           'id' => $player->id,
                          'name'=>$player->name,
                     'isMarking'=>$isMarking,
                 );
        if($this->isMarking($id))
        {$data['score']=Score::where('player_id','=',$player_id)
                             ->where('user_id','=',$user_id)
                             ->first()->score;
        }else{$data['score']=0;}
    //    return view('player',compact('data'));
        return response()->json(array('player'=>$data));

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
                return redirect()->back()->with('status',false);
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
        $activity = Activity::where('id',$activity_id)->get(array('name','image'))->first();
        $playerRank = Player::where('activity_id',$activity_id)
                            ->where('group',$group)
                            ->orderBy('score','desc')
                            ->get(array('name','score'));

        $data = array();
        $i = 0;
        foreach ($playerRank as $player)
        {
          $i = $i+1;
          $data[$i] = array('name'=> $player->name,
                            'score'=> $player->score,

          );
        }
        $data['num'] = $i;
        $data['activity'] = array('name'=>$activity->name,'image'=>$activity->image);
        $data['url'] = route('groupRank',$id,$group);
        $data['status'] = true;
        return response()->json($data);
    }

    public function rankAll($id)
    {
        $activity_id = $id;
        $activity = Activity::where('id','=',$activity_id)->get(array('name','image'))->first();
        $playerRank = Player::where('activity_id','=',$activity_id)->orderBy('score','desc')->get(array('name','score'));
        //return view('rankall',compact('playerRank','activity'));
        $data = array();
        $i = 0;
        foreach ($playerRank as $player)
        {
            $i = $i+1;
            $data[$i] = array('name'=> $player->name,
                            'score'=> $player->score,

                        );
        }
         $data['num'] = $i;
         $data['activity'] = array('name'=>$activity->name,'image'=>$activity->image);
         $data['url'] = route('rankAll',$activity_id);
         $data['status'] = true;
         return response()->json($data);
     }


}
