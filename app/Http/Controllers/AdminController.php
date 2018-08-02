<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;

use App\Models\Activity;
use App\Models\Player;
use App\Models\User;
use App\Models\Score;
use App\Models\Weight;
use Auth;
use DB;
use Storage;
use Excel;
use App\jobs\CreatePlayer;
use App\jobs\CreateUser;


class AdminController extends Controller
{


    public function getActivityList($listType)
    {
        if($listType == 'onGoing'){return $this->getOnGoingActivityList();}
        if($listType == 'finish'){return $this->getFinishActivityList();}
    }

    public function getOnGoingActivityList()
    {
        $activities = Activity::get(array('id','name','url'));
        foreach ($activities as $value) {
            $value->actType = 'onGoing';

        }
        return response()->json(array('activities'=>$activities));
    }

    public function getFinishActivityList()
    {
        $activities = Activity::onlyTrashed()->get(array('id','name','url'));
        foreach ($activities as $value) {
            $value->actType = 'finish';
        }
        return response()->json(array('activities'=>$activities));
    }



    public function showActivity($id)
    {   

        if(!Activity::withTrashed()->where('id',$id)->exists()){

            return response()->json(['result'=>'failure']);
        }
        $activity = Activity::withTrashed()->where('id',$id)->first(array('id','name','url','deleted_at','details'));
        if($activity->deleted_at != null)
        {
            $activity->actType = 'finish';
        }else{
            $activity->actType = 'onGoing';
        }
        $nextActivity = Activity::withTrashed()->where('id','>',$id)->first(array('id','name'));
        $prevActivity = Activity::withTrashed()->where('id','<',$id)->orderBy('id','desc')->first(array('id','name'));
        return response()->json(array('result'=>'success','targetActivity'=>$activity,'nextActivity'=>$nextActivity,'prevActivity'=>$prevActivity));

    }

    public function postCreateActivity(Request $request)
    {
        if(Activity::where('name',$request->name)->exists()){
            return response()->json(array('result'=>'nameExists'));
        }
        $data = array('name' =>$request->name,
                      'details'=>$request->details,
                  );

        if($request->file('actImg'))
        {
            $file = $request->file('actImg');
            $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
            $file = $file->move('img/',$newFileName);
            $file = 'img/'.$newFileName;
            $data['url'] = $file;
        }else {
            return response()->json(array('result'=>'failure'));
        }

        DB::beginTransaction();
        try{
            Activity::create($data);
            $id = Activity::where('name',$request->name)->first()->id;
            $request->id = $id;
            $this->createPlayer($request);
            $this->createUser($request);
            DB::commit();
        }catch(QueryException $ex){
            DB::rollback();
            unlink($file);
            return response()->json(['result'=>'failure','ex'=>$ex]);
        }
        return response()->json(array('result'=>'success','id'=>$id));

    }



    public function createPlayer(Request $request)
    {   

            $activity_id = $request->id;
            $file = $request->file('playerFile');
            $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
            $file = $file->move('xls/',$newFileName);
            $file = 'xls/'.$newFileName;


            $inputPlayers = Excel::selectSheetsByIndex(0)->load($file,function($reader){})->ignoreEmpty()->get();
            unlink($file);
            foreach ($inputPlayers as $inputPlayer) {
                $player = Player::create([
                    'name'=>$inputPlayer->name,
                    'details'=>$inputPlayer->details,
                    'activity_id'=>$activity_id,
                //  'group' => $inputPlayer->group,
                //  'groupName'=>$inputPlayer->groupName,
                ]);
            }


        return true;

    }

    public function createUser(Request $request)
    {
            
            $activity_id = $request->id;
            $file = $request->file('userFile');
            $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
            $file = $file->move('xls/',$newFileName);
            $file = 'xls/'.$newFileName;

            $inputUsers = Excel::selectSheetsByIndex(0)->load($file,function($reader){})->ignoreEmpty()->get();
            unlink($file);

            $a = 0;
            $b = 0;
            $c = 0;
            foreach ($inputUsers as $inputUser) {

                //防止某些情况下excel读取到空白行造成错误
                if(!$inputUser->name){break;}
                
                $user = User::create([
                    'name'=>$inputUser->name,
                    'details'=>$inputUser->details,
                    'account'=>$inputUser->account,
                    'password'=>bcrypt($inputUser->password),
                    'activity_id'=>$activity_id,
                    'level'=>$inputUser->level,
                ]);
                
                switch ($inputUser->level) {
                    case 'A':
                        $a++;
                        break;
                    case 'B':
                        $b++;
                        break;
                    case 'C':
                        $c++;
                        break;
                    default:
                        break;
                }
                //return response()->json(array('a'=>$a,'b'=>$b,'c'=>$c));

                if(!Weight::where('activity_id',$activity_id)->where('level',$inputUser->level)->exists())
                    {
                        $weight = Weight::create([
                            'activity_id'=>$activity_id,
                            'level'=>$inputUser->level,
                            'weight'=>$inputUser->weight,
                        ]);
                    }
            }

            Weight::where('activity_id',$activity_id)->where('level','A')->update(['levelNums'=>$a]);
            Weight::where('activity_id',$activity_id)->where('level','B')->update(['levelNums'=>$b]);
            Weight::where('activity_id',$activity_id)->where('level','C')->update(['levelNums'=>$c]);

            return true;

    
}

    public function updateActivity(Request $request)
    {
        try{
            if($request->id == null || $request->name == null || $request->details == null)
            {
                return response()->json(array('result'=>'failure'));
            }

            $activity = Activity::findOrFail($request->id);
            $data = array('name' =>$request->name,
                          'details'=>$request->details,
                      );

            if($request->file('actImg'))
            {
                $file = $request->file('actImg');
                $oldFileName = $activity->url;
                $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
                $file = $file->move('img/',$newFileName);
                $file = 'img/'.$newFileName;
                $data['url'] = $file;
            }else {
                $data['url'] = $activity->url;
            }
            $activity->update($data);

            if($request->file('actImg'))
            {   
                if($oldFileName!=null)
                {
                    unlink($oldFileName);
                }
            }

        }catch(QueryException $ex){
            return response()->json(array('result'=>'failure'));
        }
        return response()->json(array('result'=>'success'));
    }


    public function destroy(Request $request)
    {   
        DB::beginTransaction();
        try {

            $id = $request->id;
            User::where('activity_id','=',$id)->delete();
            Player::where('activity_id','=',$id)->delete();
            Activity::where('id','=',$id)->delete();
            Score::where('activity_id','=',$id)->delete();
            Weight::where('activity_id','$id')->delete();
            DB::commit();

        } catch (QueryException $ex) {
            DB::rollback();
            return response()->json(array('result'=>'failure'));
        }
        return response()->json(array('result'=>'success'));
    }

    public function restore(Request $request)
    {
        DB::beginTransaction();
        try {

            $id = $request->id;
            User::withTrashed()->where('activity_id','=',$id)->restore();
            Player::withTrashed()->where('activity_id','=',$id)->restore();
            Activity::withTrashed()->where('id','=',$id)->restore();
            Score::withTrashed()->where('activity_id','=',$id)->restore();
            Weight::withTrashed()->where('activity_id','$id')->restore();
            DB::commit();

        } catch (QueryException $ex) {
            DB::rollback();
            return response()->json(array('result'=>'failure'));
        }
        return response()->json(array('result'=>'success'));
    }

    public function deleteActivity($id)
    {
        DB::beginTransaction();
        try{
            $activity = Activity::withTrashed()->where('id',$id)->first();
            $actImg = $activity->url;
            $users = User::withTrashed()->where('activity_id',$id)->get();
            User::withTrashed()->where('activity_id','=',$id)->forceDelete();
            Player::withTrashed()->where('activity_id','=',$id)->forceDelete();
            Activity::withTrashed()->where('id','=',$id)->forceDelete();
            Score::withTrashed()->where('activity_id','=',$id)->forceDelete();
            Weight::withTrashed()->where('activity_id',$id)->forceDelete();
            DB::commit();
        }catch(QueryException $ex){
            DB::rollback();
            return response()->json(array('result'=>'failure'));
        }
        if($actImg!=null){unlink($actImg);}
        foreach ($users as $value) {
            if($value->url != null){unlink($value->url);}
        }
        return response()->json(array('result'=>'success'));
    }


    //返回该评委是否对该选手评分
    public function isMarking($player_id,$user_id)
    {
        if($marking = Score::withTrashed()->where('player_id',$player_id)
                           ->where('user_id',$user_id)
                           ->exists())
        {return true;}else{return false;}
    }


    public function searchActivity(Request $request)
    {
        $name = $request->name;
        if(!Activity::withTrashed()->where('name','like','%'.$name.'%')->exists())
            {
                return response()->json(array('result'=>'non-existent'));
            }

        $onGoingActivities = Activity::where('name','like','%'.$name.'%')->get(array('id','name','deleted_at','url'));
        foreach ($onGoingActivities as $value) {
            $value->actType = 'onGoing';

        }

        $finishActivities = Activity::onlyTrashed()->where('name','like','%'.$name.'%')->get(array('id','name','deleted_at','url'));
        foreach ($finishActivities as $value) {
            $value->actType = 'finish';

        }

        return response()->json(array('result'=>'success','onGoingActivities'=>$onGoingActivities,'finishActivities'=>$finishActivities));

    }

    public function getUserTable(Request $request)
    {
        $activity_id = $request->id;
        if(!User::withTrashed()->where('activity_id',$activity_id)->exists())
        {
            return response()->json(array('result'=>'failure'));
        }
        $users = User::withTrashed()->where('activity_id',$activity_id)->get(array('id','name','level'));

        foreach ($users as $value) {

            $weight = Weight::withTrashed()->where('activity_id',$activity_id)->where('level',$value->level)->first();
            if($weight){$value->weight = $weight->weight; } 

        }

        return response()->json(array('result'=>'success','users'=>$users));
    }

    public function getUser($id)
    {   
        if(!User::withTrashed()->where('id',$id)->exists()){

            return response()->json(['result'=>'failure']);
        }
        $user = User::withTrashed()->where('id',$id)->first(array('id','name','account','activity_id','level'));
        $weight = Weight::withTrashed()->where('activity_id',$user->activity_id)->where('level',$user->level)->first();
        if($weight){$user->weight = $weight->weight; }    
        return response()->json(array('result'=>'success','user'=>$user));

    }

    public function updateUser(Request $request)
    {   
                
        if($request->id == null || $request->name == null || $request->account == null || $request->password == null)
        {
            return response()->json(array('result'=>'failure'));
        }
        DB::beginTransaction();
        try{
            $user = User::findOrFail($request->id);
            $data = [];
            $data['name'] = $request->name;
            //为空时，前端传的是字符串'null'   orz
            //这里前端用weight来表示level
            if($request->weight !='null'){
                $data['level'] = $request->weight;

                $oldLevel = Weight::withTrashed()->where('activity_id',$user->activity_id)->where('level',$user->level)->first();
                $oldLevel->levelNums--;
                $oldLevel->save();

                $newLevel = Weight::withTrashed()->where('activity_id',$user->activity_id)->where('level',$request->weight)->first();
                $newLevel->levelNums++;
                $newLevel->save();
            }
            if($request->account){
                $data['account'] = $request->account;
            }
            if($request->password && $request->password!='undefined'){
                $data['password'] = bcrypt($request->password);
            }
            $user->update($data);
            //在user的level指向改变后和levelNums改变后，再重新计算player的加权分数
            if($request->weight != 'null'){
                $players = Player::withTrashed()->where('activity_id',$user->activity_id)->get();
                foreach ($players as $value) {
                    $totalScore = $this->getTotalScore($value->id);
                    $value->update(['score'=>$totalScore]);
                }
            }
            DB::commit();

        }catch(QueryException $ex){
            return response()->json(array('result'=>'failure','ex'=>$ex));
            DB::rollback();
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

    public function getPlayerTable(Request $request)
    {
        $activity_id = $request->id;
        if(!Player::withTrashed()->where('activity_id',$activity_id)->exists())
            {
                return response()->json(array('result'=>'failure'));
            }
        $players = Player::withTrashed()->where('activity_id',$activity_id)->orderBy('score','desc')->get(array('id','name','score'));
        $i = 0;
        foreach ($players as $value) {
            $i++;
            $value->rank = $i;
            if($this->isTotallyMarked($value->id)){
                $value->isMarking = 1;
            }else{
                $value->isMarking = 0;
            }
        }

        return response()->json(array('result'=>'success','players'=>$players));
    }

    public function getPlayer($id)
    {   
        if(!Player::withTrashed()->where('id',$id)->exists()){

            return response()->json(['result'=>'failure']);
        }
        $player = Player::withTrashed()->where('id',$id)->first(array('id','name','url','details'));
        return response()->json(array('result'=>'success','player'=>$player));

    }

    public function updatePlayer(Request $request)
    {
        try{
            if($request->id == null || $request->name == null || $request->details == null)
            {
                return response()->json(array('result'=>'failure'));
            }

            $player = Player::findOrFail($request->id);
            $data = array('name' =>$request->name,
                          'details'=>$request->details,
                      );

            if($request->file('playerImg'))
            {
                $file = $request->file('playerImg');
                $oldFileName = $player->url;
                $newFileName = md5(time().rand(0,10000)).'.'.$file->getClientOriginalExtension();
                $file = $file->move('img/',$newFileName);
                $file = 'img/'.$newFileName;
                $data['url'] = $file;
            }else {
                $data['url'] = $player->url;
            }
            $player->update($data);

            if($request->file('playerImg'))
            {   
                if($oldFileName!=null)
                {
                    unlink($oldFileName);
                }
            }

        }catch(QueryException $ex){
            return response()->json(array('result'=>'failure'));
        }
        return response()->json(array('result'=>'success'));
    }

    public function playerDetail($id)
    {
        if(!Player::withTrashed()->where('id',$id)->exists()) 
        {
            return response()->json(array('result'=>'failure'));
        }

        $player = Player::withTrashed()->where('id',$id)->first(array('score','activity_id','id'));
        $players = Player::withTrashed()->where('activity_id',$player->activity_id)->orderBy('score','desc')->get();
        $rank = 0;
        foreach ($players as $value) {
            $rank++;
            if($value->id == $player->id)
            {
                $player->rank = $rank;
                break;
            }
        }

        $scores = Score::withTrashed()->where('player_id',$id)->get(array('user_id','score'));
        foreach ($scores as  $value) {
            $user = User::withTrashed()->where('id',$value->user_id)->first();
            if($user)
            {
                $value->name = $user->name;
                $weight = Weight::withTrashed()->where('activity_id',$user->activity_id)->where('level',$user->level)->first();
                if($weight){$value->weight = $weight->weight; }   
            } 
        }

        $datas = User::withTrashed()->where('activity_id',$player->activity_id)->get(array('id','name','level','activity_id'));
        $users = new User();
        $i = 0;
        foreach ($datas as $value) {
            $weight = Weight::withTrashed()->where('activity_id',$value->activity_id)->where('level',$value->level)->first();
            if($weight){$value->weight = $weight->weight; }    

            if(!$this->isMarking($id,$value->id))
            {
                $i++;
                $users[$i] = $value;
            }      
        }

        return response()->json(array('result'=>'success','player'=>$player,'scores'=>$scores,'users'=>$users));

    }


}
