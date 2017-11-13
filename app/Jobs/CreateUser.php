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
         $this->activity_id = $request->id;
         $this->file = $request->file('file');
         $newFileName = md5(time().rand(0,10000)).'.'.$this->file->getClientOriginalExtension();
         $this->file = $this->file->move('xls/',$newFileName);
         $this->file = 'xls/'.$newFileName;
     }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data['levelA']=0;$data['levelB']=0;$data['levelC']=0;
        $inputUsers = Excel::selectSheetsByIndex(0)->load($this->file,function($reader){})->ignoreEmpty()->get();
            foreach ($inputUsers as $inputUser) {
                $user = User::create([
                    'name'=>$inputUser->name,
                    'details'=>$inputUser->details,
                    'account'=>$inputUser->account,
                    'password'=>bcrypt($inputUser->password),
                    'weight'=>$inputUser->weight,
                    'activity_id'=>$this->activity_id,
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
            $activity = Activity::findOrFail($this->activity_id);
            $data['levelA'] = $activity->levelA+$data['levelA'];
            $data['levelB'] = $activity->levelB+$data['levelB'];
            $data['levelC'] = $activity->levelC+$data['levelC'];
            $activity->update($data);
    }
}
