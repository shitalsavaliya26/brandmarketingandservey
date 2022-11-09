<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Currency;
use App\CurrencyUpdateTime;
use Illuminate\Console\Command;

class CurrencyUpdateCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencyupdate:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Currency rate Update every 24 hours';

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
        echo "------ Cron is Running... -----\n";
        $this->callapi();
        echo "------Cron Runs Successfully!!-----\n";
    }

    public function callapi()
    {
        $today = Carbon::now()->format('Y-m-d');
        $url = "https://api.apilayer.com/currency_data/change?start_date=".$today."&end_date=".$today;
        $headers = array(
            "Content-Type: text/plain",
            "apikey: XaQ4OsopwMKNlG78t8a9yEpeWgmpBBjE",
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response,true);


        if(empty($response)){
            echo "Error! Something went wrong. \n";
            return;
        }
        if(array_key_exists("quotes",$response)){
            foreach ($response['quotes'] as $key => $value) {
                $find = Currency::where('code', substr($key,3))->first();
                if($find){
                    $find->rate = $value['end_rate'];
                    $find->save();
                }            
            }                
            echo "Currency rate updated successfully. \n";
            return; 
        }else{
            echo "Error! Something went wrong. \n";
            return;
        }
        return;
    }
}
