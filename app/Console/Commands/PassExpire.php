<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PassOrderItems;
use Carbon\Carbon;
use App\BankAccount;

class PassExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pass:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire or make pass as used';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*$upcomings = PassOrderItems::where('last_valid_date', '<=', Carbon::now())->get();
        dd($upcomings);*/
        $upcomings = PassOrderItems::where('last_valid_date', '<=', Carbon::now())->update(['is_used' => 1,'is_expire' => 1]);
        $this->info('Pass made expired or used');
    }
}
