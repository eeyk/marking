<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Player;
use Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreatePlayer extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $activity_id;
    protected $site;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($activity_id,$site)
    {
        $this->activity_id=$activity_id;
        $this->site=$site;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $inputPlayers = Excel::selectSheetsByIndex(0)->load($this->site,function($reader){})->ignoreEmpty()->get();
        foreach ($inputPlayers as $inputPlayer) {
            $player=Player::create([
                'name'=>$inputPlayer->name,
                'details'=>$inputPlayer->details,
                'activity_id'=>$this->activity_id,
            ]);
        }
    }
}
