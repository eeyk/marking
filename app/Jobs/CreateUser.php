<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\User;
use App\Models\Activity;
use Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use App\Http\Requests;

class CreateUser extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $activity_id;
    protected $file;
    /**
     * Create a new job instance.
     *
     * @return void
     */
     public function __construct(Request $request)
     {
         $this->activity_id=$request->id;
         $this->file=$request->file('file');
         $newFileName = md5(time().rand(0,10000)).'.'.$this->file->getClientOriginalExtension();
         $this->file=$this->file->move('xls/',$newFileName);
         $this->file='xls/'.$newFileName;
     }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data['groupA']=0;$data['groupB']=0;$data['groupC']=0;
        $inputUsers = Excel::selectSheetsByIndex(0)->load($this->file,function($reader){})->ignoreEmpty()->get();
            foreach ($inputUsers as $inputUser) {
                $user=User::create([
                    'name'=>$inputUser->name,
                    'details'=>$inputUser->details,
                    'account'=>$inputUser->account,
                    'password'=>bcrypt($inputUser->password),
                    'weight'=>$inputUser->weight,
                    'activity_id'=>$this->activity_id,
                    'group'=>$inputUser->group,
                ]);
                switch ($inputUser->group) {
                    case 'A':
                        $data['groupA']=$data['groupA']+1;
                        break;
                    case 'B':
                        $data['groupB']=$data['groupB']+1;
                        break;
                    case 'C':
                        $data['groupC']=$data['groupC']+1;
                        break;
                    default:
                        break;
                }
            }
            $activity=Activity::findOrFail($this->activity_id);
            $data['groupA']=$activity->groupA+$data['groupA'];
            $data['groupB']=$activity->groupB+$data['groupB'];
            $data['groupC']=$activity->groupC+$data['groupC'];
            $activity->update($data);
    }
}
