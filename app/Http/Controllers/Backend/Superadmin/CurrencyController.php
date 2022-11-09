<?php

namespace App\Http\Controllers\Backend\Superadmin;
use Session;
use Carbon\Carbon;
use App\Models\Currency;
use App\CurrencyUpdateTime;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    public function __construct(Request $request)
    {
        $this->limit = $request->limit ? $request->limit : 10;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $currency = new Currency();

        if ($request->has('name') && $request->name != '' && $request->name != null) {
            $currency = $currency->where('name', 'LIKE', "%{$request->name}%")->orWhere('code', 'LIKE', "%{$request->name}%");
        }

        $currency = $currency->orderBy('id', 'asc')->get();

        return view('backend.superadmin.currency.index', compact('currency'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $id = CustomHelper::getDecrypted($id);

        $currency = Currency::find($id);

        if (!$currency) {
            return redirect()->back()->with('error', 'No recored found.');
        }

        return view('backend.superadmin.currency.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = CustomHelper::getDecrypted($id);
        $currency = Currency::find($id);
        if (!$currency) {
            return redirect()->back()->with('error', 'No recored found.');
        }
        $request->validate([
            'amount' => 'required',
        ], [
            'amount.required' => 'Please enter currency rate.',
        ]);
        $currency->rate = $request->amount;
        $currency->save();
        return redirect()->route('currency.index')->with('success', 'Currency updated successfully.');
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

    public function currencyRateEdit(Request $request)
    {
        if ($request->isMethod('POST')) {
            $data = $request->except(['_token']);
            $message = "";
            foreach ($data as $key => $value) {
                if ($key != "") {
                    $currencyupdate = Currency::firstOrCreate(['code' => $key]);
                    $currencyupdate->rate = $value;
                    $currencyupdate->save();
                }
            }
            $message = "Currency rate update successfully.";
            return redirect()->back()->with('success', $message);
        } else {
            $currencys = Currency::all();
            $currency = [];
            foreach ($currencys as $key => $value) {
                $currency[$value['code']] = $value['rate'];
            }
        }
        return view('backend.superadmin.currency.currencyrateedit', compact('currency'));
    }


    public function currencyRateApi(Request $request)
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
                Session::flash('error', 'Something went wrong.');
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
                $time = new CurrencyUpdateTime();
                $time->created_at = Carbon::now();
                $time->save();
                               
                Session::flash('success', 'Currency rate updated successfully.');
                return; 
            }else{
                Session::flash('error', 'Something went wrong.');
                return;
            }
    }
}
