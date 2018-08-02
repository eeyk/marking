<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Player;
use App\Models\Activity;
use App\Models\Score;
use App\Models\Weight;
use Auth;
use Response;
use DB;
class UsersController extends Controller
{

    public function activityOfUser($id)
    {
        $activity_id = $id;
        $activity = Activity::where('id',$activity_id)->first(array('id','name','deleted_at','url','details'));
        if(!$activity){return response()->json(array('result'=>'failure'));}
        $activity->actType = $activity->deleted_at ? 'finish':'onGoing';
        return response()->json(array('result'=>'success','activity'=>$activity));
    }

    public function searchPlayer(Request $request)
    {
        $name = $request->name;
        $activity_id = Auth::user()->activity_id;
        if(!Player::withTrashed()->where('activity_id',$activity_id)->where('name','like','%'.$name.'%')->exists())
            {
                return response()->json(array('result'=>'non-existent'));
            }

        $players = Player::withTrashed()->where('activity_id',$activity_id)->where('name','like','%'.$name.'%')->get(array('id','name','score'));

        foreach ($players as $value) {
            if($this->isMarking($value->id))
            {
                $value->isMarking = 1;
            }else{
                $value->isMarking = 0;
            }
            $value->rank = $this->getRank($value->id);

        }


        return response()->json(array('result'=>'success','players'=>$players));

    }

    //返回当前评委是否已经对该选手评分
    public function isMarking($id)
    {
        $player_id = $id;
        $user_id = Auth::id();
        $player = Player::withTrashed()->where('id','$player_id');
           if($marking = Score::withTrashed()->where('player_id','=',$player_id)
                            ->where('user_id','=',$user_id)
                            ->exists())
        {return 'true';}else{return "0";}
    }

    public function getRank($id)
    {
        $activity_id = Player::withTrashed()->where('id',$id)->first();
        $activity_id = $activity_id->activity_id;
        $players = Player::withTrashed()->where('activity_id',$activity_id)->orderBy('score','desc')->get(array('id','score'));
        $rank = 0;
        foreach ($players as $value) {
            $rank++;
            if($value->id == $id)
            {
                return $rank;
            }
        }
        return $rank;

    }

    public function getAllPlayers(Request $request)
    {   
        //$activity_id = Auth::user()->activity_id;
        $activity_id = $request->id;
        if(!Activity::withTrashed()->where('id',$activity_id)->exists())
            {
                return response()->json(array('result'=>'failure'));
            }
        $players = Player::withTrashed()->where('activity_id',$activity_id)->orderBy('score','desc')->get(array('id','name','score'));

        $rank = 0;
        foreach ($players as $value) 
        {   
            if($this->isMarking($value->id))
            {
                $value->isMarking = 1;
            }else{
                $value->isMarking = 0;
            }
            $rank++;
            $value->rank = $rank;
        }
        return response()->json(array('result'=>'success','players'=>$players));
    }


    public function playerDetail($id)
    {   
        $activity_id = Auth::user()->activity_id;
        if(!Player::withTrashed()->where('id',$id)->exists())
            {
                return response()->json(array('result'=>'failure'));
            }
        $player = Player::withTrashed()->where('id',$id)->first(array('id','name','details','url'));
        $score = Score::withTrashed()->where('user_id',Auth::user()->id)->where('player_id',$id)->first();
        if($score){$player->score = $score->score;}else{$player->score = 0;}
        //$player->score = $score;
        $nextPlayer = Player::withTrashed()->where('activity_id',$activity_id)->where('id','>',$id)->first(array('id','name'));
        $prevPlayer = Player::withTrashed()->where('activity_id',$activity_id)->where('id','<',$id)->orderBy('id','desc')->first(array('id','name'));
        return response()->json(array('result'=>'success','targetPlayer'=>$player,'nextPlayer'=>$nextPlayer,'prevPlayer'=>$prevPlayer));
    }


    public function postScore(Request $request)
    {   
       
        $player_id = $request->id;
        $user = Auth::user();
        $player = Player::where('id',$player_id)->first();
        if($player->activity_id!=$user->activity_id || !$request->score)
        {
            return response()->json(array('result'=>'failure'));
        } 
        DB::beginTransaction();
        try {  
            if (!Score::where('user_id',$user->id)->where('player_id',$player->id)->exists()) 
            {
                $score = Score::create([
                    'player_id' => $player->id,
                    'user_id' => $user->id,
                    'score' => $request->score,
                    'activity_id'=>$user->activity_id,
                ]);
            }else{
                $score = Score::where('user_id',$user->id)->where('player_id',$player->id)->first();
                $data['score'] = $request->score;
                $score->update($data);
            }

            $totalScore = $this->getTotalScore($player_id);
            $player->update(['score'=>$totalScore]);
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            return response()->json(array('result'=>'failure','ex'=>$ex));
        }
        return response()->json(array('result'=>'success'));
            
    }

    public function getTotalScore($id)
    {
        $scores = Score::withTrashed()->where('player_id',$id)->get();
        $totalScore = 0;
        foreach ($scores as  $value) {

            $user = User::withTrashed()->where('id',$value->user_id)->first();
            $weight = Weight::withTrashed()->where('activity_id',$value->activity_id)->where('level',$user->level)->first();
            $levelNums = $weight->levelNums;
            $weight = $weight->weight;
            $totalScore = $totalScore + $value->score*$weight/$levelNums;
        }
        return $totalScore;
    }

    //返回该选手是否已经被所有评委评分
    public function isTotallyMarked($id)
    {   
        $player = Player::withTrashed()->where('id',$id)->first();
        $activity_id = $player->activity_id;
        $count = Score::withTrashed()->where('player_id',$id)->count();
        $userNums = User::withTrashed()->where('activity_id',$activity_id)->count();
        if($count<$userNums){
            return false;
        }else
        {
            return true;
        }
    }




/*
    public function groupRank($id,$group)
    {
        $activity_id = $id;
        $activity = Activity::where('id',$activity_id)->get(array('name','img'))->first();
        $playerRank = Player::where('activity_id',$activity_id)
                            ->where('group',$group)
                            ->orderBy('score','desc')
                            ->get(array('name','score'));
        
        return response()->json(array(
                                'playerRank'=>$playerRank,
                                'url'=>route('groupRank',$id).'/'.$group,
                                'status'=>true,
                            ));
        
    }

    public function rankAll($id)
    {
        $activity_id = $id;
        $activity = Activity::where('id','=',$activity_id)->get(array('name','img'))->first();
        $playerRank = Player::where('activity_id','=',$activity_id)->orderBy('score','desc')->get(array('name','score'));
        
        return response()->json(array(
                                'playerRank'=>$playerRank,
                                'url'=>route('rankAll',$id),
                                'status'=>true,
                            ));

     }

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
