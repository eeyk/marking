<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Player;
use Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllPlayers()
    {
        $players = Player::all();
        return view('test',compact('players'));
    }


    public function rank($id)
    {
        $playerRank = Player::where('id','=',$id)->get(array('name','score'));
    }


    public function rankAll()
    {
        $playerRank = Player::all()->get(array('id','name','score'))->orderBy('score');
        return view('test',compact('playerRank'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $player = Player::findOrFail($id);
        $data = array('details' => $player->details ,
                           'id' => $id);
        return view('test',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postScore(Request $request,$id)
    {
        $player_id = $id;
        $user_id = Auth::id();
        if($this->isMarking($id))
            {
                session()->flash('danger','你已经对该选手评分了')；
                return redirect()->route('index');
            }else
            {
                $score = Score::create([
                    'player_id' = $player_id,
                    'user_id' = $user_id,
                    'score' = $request->score
                ]);
                session()->falsh('success','评分成功');
                return redirect()->route('index');
            }
    }

    public function isMarking($id)
    {
        $player_id = $id;
        $user_id = Auth::id();
        if($check=Score::where('player_id' => $player_id)
                            ->where('user_id' => $user_id))
        {return 'true'; }else{return 'false';}
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
