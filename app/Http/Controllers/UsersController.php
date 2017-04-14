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
        return view('getAllPlayers',compact('players','activity'));
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
        return view('player',compact('data'));
    }

    public function postScore(Request $request,$id)
    {
        $player_id = $id;
        $user_id = Auth::id();
        $weight=Auth::user()->weight;
        $activity_id=Auth::user()->activity_id;
        $user_activity_id=Auth::user()->activity_id;
        $player = Player::findOrFail($player_id);
        $player_activity_id=$player->activity_id;
        $player_score=$player->score;
        if(($player_activity_id!=$user_activity_id) or ($this->isMarking($id)))
            {
                session()->flash('danger','无法修改评分');
                return redirect()->route('index');
            }else
            {
                $score = Score::create([
                    'player_id' => $player_id,
                    'user_id' => $user_id,
                    'score' => ($request->score),
                    'activity_id'=>$activity_id,
                    'weight'=>$weight,
                ]);
                $data['score']=$player_score+($request->score)*$weight;
                $player->update($data);
                session()->flash('success','评分成功');
                return redirect()->route('index');
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

    public function rank($id)
    {
        $playerRank = Player::where('id','=',$id)->get(array('name','score'));
    }


    public function rankAll($id)
    {
        $activity_id=$id;
        $activity=Activity::findOrFail($activity_id)->get(array('name','details'))->first();
        $playerRank = Player::where('activity_id','=',$activity_id)->orderBy('score','desc')->get(array('name','score'));
        return view('rankall',compact('playerRank','activity'));
    }

    public function destroy($id)
    {
        //
    }
}
