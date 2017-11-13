<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Player;
use App\Models\Activity;
use Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use App\Http\Requests;

class CreatePlayer extends Job implements SelfHandling, ShouldQueue
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
        $data['playersNum'] = 0;
        $inputPlayers = Excel::selectSheetsByIndex(0)->load($this->file,function($reader){})->ignoreEmpty()->get();
        foreach ($inputPlayers as $inputPlayer) {
            $player = Player::create([
                'name'=>$inputPlayer->name,
                'details'=>$inputPlayer->details,
                'activity_id'=>$this->activity_id,
                'group' => $inputPlayer->group,
            ]);
            $data['playersNum'] = $data['playersNum']+1;
        }
        $activity = Activity::findOrFail($this->activity_id);
        $data['playersNum'] = $activity->playersNum+$data['playersNum'];
        $activity->update($data);
    }
}
