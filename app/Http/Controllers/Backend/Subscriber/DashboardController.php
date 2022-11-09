<?php

namespace App\Http\Controllers\Backend\Subscriber;
use DB;
use Auth;
use Session;
use Carbon\Carbon;
use App\BrandGroup;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Country;
use App\Models\Currency;
use App\Models\PriceSetting;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Models\TransactionHistory;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('is_information_filled')->except('showCurrencyForm', 'termsandconditionsForm','subscriberwelcome');
    }



    public function subscriberwelcome(Request $request)
    {
        $user = Admin::find(Auth::user()->id);
        $data = $this->ip_info();
        $cdata = isset($data['country']) ? $data['country'] : "";
        $countryData = Country::where('name',$cdata)->first();
        
        if(empty($user->first_time_login)){
            $user->first_time_login = 1;
            $user->save();
            if($countryData){
                $currencyData = Currency::where('code', $countryData['currency'])->first();
                return redirect()->route('subscriber.profile')->with('success', 'Welcome to Example!')->with('country', $countryData->id)->with('calling_code', $countryData->calling_code)->with('currency', $currencyData->id);
            }
            else{
                return redirect()->route('subscriber.profile')->with('success', 'Welcome to Example!');
            }    
        }
        else{
            return redirect()->route('subscriber.profile')->with('success','Complete your profile to start.');
        }
             
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCurrencyForm(Request $request, $id)
    {

        abort(404);
        if ($request->isMethod('POST')) {
            $id = CustomHelper::getDecrypted($id);
            $user = Admin::find($id);

            if ($user) {
                $request->validate([
                    'currency_id' => 'required',
                ], [
                    'currency_id.required' => 'Please select currency',
                ]);
                $user->currency_id = $request->currency_id;
                $user->save();
                return redirect()->route('subscriber.profile');
            } else {
                Auth::logout();
                return redirect()->route('subscriber.login')
                    ->with('error', 'Account not found!');
            }
        } else {

            if (!empty(Auth::user()->currency_id)) {
                return redirect()->route('subscriber.dashboard');
            }
            $currencies = Currency::get();
            return view('layouts.backend.subscriber.currency', compact('currencies', 'id'));
        }
    }

    public function termsandconditionsForm(Request $request)
    {

        if ($request->isMethod('POST')) {

            $request->validate([
                'terms_and_conditions' => 'required|array|min:2',
            ], [
                'terms_and_conditions.required' => 'Please select terms and conditions.',
                'terms_and_conditions.min' => 'Please select terms and conditions.',
            ]);
            try {
                $user = Admin::find(Auth::user()->id);
                if ($user) {
                    $user->is_terms_and_conditions_accepted = "1";
                    $user->save();
                    $data = $this->ip_info();

                
                    $cdata = isset($data['country']) ? $data['country'] : "";
                    $countryData = Country::where('name',$cdata)->first();
                    
                    if($countryData){

                        $currencyData = Currency::where('code', $countryData['currency'])->first();
                        return redirect()->route('subscriber.profile')->with('success', 'Welcome to Example!')->with('country', $countryData->id)->with('calling_code', $countryData->calling_code)->with('currency', $currencyData->id);
                    }
                    else{
                        return redirect()->route('subscriber.profile')->with('success', 'Welcome to Example!');
                    }
                 

                } else {
                    Auth::logout();
                    return redirect()->route('subscriber.login')
                        ->with('error', 'Account not found!');
                }
            } catch (Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            if (!empty(Auth::user()->is_terms_and_conditions_accepted)) {
                return redirect()->route('subscriber.dashboard');
            }
            return view('layouts.backend.subscriber.terms_and_conditions');
        }
    }

    public function ip_info($ip = null, $purpose = "location", $deep_detect = true)
    {
        $output = null;
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }

                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                }

            }
        }
        $purpose = str_replace(array("name", "\n", "\t", " ", "-", "_"), null, strtolower(trim($purpose)));
        $support = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America",
        );
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city" => @$ipdat->geoplugin_city,
                            "state" => @$ipdat->geoplugin_regionName,
                            "country" => @$ipdat->geoplugin_countryName,
                            "country_code" => @$ipdat->geoplugin_countryCode,
                            "continent" => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode,
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1) {
                            $address[] = $ipdat->geoplugin_regionName;
                        }

                        if (@strlen($ipdat->geoplugin_city) >= 1) {
                            $address[] = $ipdat->geoplugin_city;
                        }

                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }

    public function index(Request $request)
    {

        $start = new Carbon('first day of this month');
        $end = new Carbon('last day of this month');

        $adminId = Auth::user()->id;
        $brands = Brand::orderBy('id', 'desc')->where('subscriber_id', $adminId)->get();
        $brandgroups = BrandGroup::orderBy('id', 'desc')->where('subscriber_id', $adminId)->get();
        $countries = Country::get();
        $priceSetting = PriceSetting::get();

        

        $messageHistories = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'message_sent')->where('admin_id', $adminId);
    
        $infoHistories = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'notification_topic_add')->where('admin_id', $adminId);

        $buyHistories = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'buy')->where('admin_id', $adminId);
        $winHistories = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'win')->where('admin_id', $adminId);
        $wesiteLinkHistories = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'wesite_link')->where('admin_id', $adminId);
        $pollHistories = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'poll')->where('admin_id', $adminId);
        $distributionHistories = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'notification_distribution')->where('admin_id', $adminId);

        // dd($pollHistories);
        // if ($request->brand_id != null && $request->brand_id != '' && $request->brand_id != 'null') {
        //     $messageHistories->where('brand_id', $request->brand_id);
        //     $infoHistories->where('brand_id', $request->brand_id);
        //     $buyHistories->where('brand_id', $request->brand_id);
        //     $winHistories->where('brand_id', $request->brand_id);
        //     $wesiteLinkHistories->where('brand_id', $request->brand_id);
        //     $pollHistories->where('brand_id', $request->brand_id);
        //     $distributionHistories->where('brand_id', $request->brand_id);
        // }

        if ($request->group_id != null && $request->group_id != '' && $request->group_id != 'null') {
            $messageHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $infoHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $buyHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $winHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $wesiteLinkHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $pollHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $distributionHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
        }

        if ($request->country_id != null && $request->country_id != '' && $request->country_id != 'null') {
            $messageHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $infoHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $buyHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $winHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $wesiteLinkHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $pollHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $distributionHistories->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
        }

        if ($request->city_id != null && $request->city_id != '' && $request->city_id != 'null') {
            $messageHistories->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $infoHistories->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $buyHistories->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $winHistories->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $wesiteLinkHistories->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $pollHistories->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $distributionHistories->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
        }

        if ($request->startDate != null && $request->startDate != '' && $request->startDate != 'null' && $request->endDate != null && $request->endDate != '' && $request->endDate != 'null') {
            $dateS = date('Y-m-d 00:00:00', strtotime($request->startDate));
            $dateE = date('Y-m-d 00:00:00', strtotime($request->endDate));

            $messageHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $infoHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $buyHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $winHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $wesiteLinkHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $pollHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $distributionHistories->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
        }else{
            $messageHistories->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $infoHistories->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $buyHistories->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $winHistories->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $wesiteLinkHistories->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $pollHistories->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $distributionHistories->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
        }

      



        $messageHistories = $messageHistories->get();
        $infoHistories = $infoHistories->get();
        $buyHistories = $buyHistories->get();
        $winHistories = $winHistories->get();
        $wesiteLinkHistories = $wesiteLinkHistories->get();
        $pollHistories = $pollHistories->get();
        $distributionHistories = $distributionHistories->get();

        // dd($pollHistories );

        // dd($infoHistories);

        // $count = [];
        // $count['messageHistoriesCount'] = $messageHistoriesCount = count($messageHistories);
        // $count['infoHistoriesCount'] = $infoHistoriesCount = count($infoHistories);
        // $count['buyHistoriesCount'] = $buyHistoriesCount = count($buyHistories);
        // $count['winHistoriesCount'] = $winHistoriesCount = count($winHistories);
        // $count['wesiteLinkHistoriesCount'] = $wesiteLinkHistoriesCount = count($wesiteLinkHistories);
        // $count['pollHistoriesCount'] = $pollHistoriesCount = count($pollHistories);
        // $count['distributionHistoriesCount'] = $distributionHistoriesCount = count($distributionHistories);
        // $countHistory = array_sum($count);


        $count = [];
        $count['messageHistoriesCount'] = $messageHistoriesCount = $messageHistories->sum('is_counting');
        $count['infoHistoriesCount'] = $infoHistoriesCount =  $infoHistories->sum('is_counting');;
        $count['buyHistoriesCount'] = $buyHistoriesCount = $buyHistories->sum('is_counting');
        $count['winHistoriesCount'] = $winHistoriesCount = $winHistories->sum('is_counting');
        $count['wesiteLinkHistoriesCount'] = $wesiteLinkHistoriesCount = $wesiteLinkHistories->sum('is_counting');
        $count['pollHistoriesCount'] = $pollHistoriesCount = $pollHistories->sum('is_counting');
        $count['distributionHistoriesCount'] = $distributionHistoriesCount = $distributionHistories->sum('is_counting');
        $countHistory = array_sum($count);


        $sum = [];
        $sum['messageHistoriesSum'] = $messageHistoriesSum = $messageHistories->sum('amount');
        $sum['infoHistoriesSum'] = $infoHistoriesSum = $infoHistories->sum('amount');
        $sum['buyHistoriesSum'] = $buyHistoriesSum = $buyHistories->sum('amount');
        $sum['winHistoriesSum'] = $winHistoriesSum = $winHistories->sum('amount');
        $sum['wesiteLinkHistoriesSum'] = $wesiteLinkHistoriesSum = $wesiteLinkHistories->sum('amount');
        $sum['pollHistoriesSum'] = $pollHistoriesSum = $pollHistories->sum('amount');
        $sum['distributionHistoriesSum'] = $distributionHistoriesSum = $distributionHistories->sum('amount');
        $totalHistory = array_sum($sum);

        $messaging = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'message_sent')->where('admin_id', $adminId)->groupby(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))->selectRaw('*, DATE_FORMAT(created_at, "%d-%m-%Y") as date, ROUND(sum(amount),2) as sum');
        $info = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'notification_topic_add')->where('admin_id', $adminId)->groupby(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))->selectRaw('*, DATE_FORMAT(created_at, "%d-%m-%Y") as date, ROUND(sum(amount),2) as sum');
        $infoDistribute = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'notification_distribution')->where('admin_id', $adminId)->groupby(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))->selectRaw('*, DATE_FORMAT(created_at, "%d-%m-%Y") as date, ROUND(sum(amount),2) as sum');
        $poll = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'poll')->where('admin_id', $adminId)->groupby(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))->selectRaw('*, DATE_FORMAT(created_at, "%d-%m-%Y") as date, ROUND(sum(amount),2) as sum');
        $shop = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'buy')->where('admin_id', $adminId)->groupby(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))->selectRaw('*, DATE_FORMAT(created_at, "%d-%m-%Y") as date, ROUND(sum(amount),2) as sum');
        $win = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'win')->where('admin_id', $adminId)->groupby(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))->selectRaw('*, DATE_FORMAT(created_at, "%d-%m-%Y") as date, ROUND(sum(amount),2) as sum');
        $weblink = TransactionHistory::with(['user:id,country_id,city_id','brand'])->where('pay_for', 'wesite_link')->where('admin_id', $adminId)->groupby(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y')"))->selectRaw('*, DATE_FORMAT(created_at, "%d-%m-%Y") as date, ROUND(sum(amount),2) as sum');

        // if ($request->brand_id != null && $request->brand_id != '' && $request->brand_id != 'null') {
        //     $messaging = $messaging->where('brand_id', $request->brand_id);
        //     $info = $info->where('brand_id', $request->brand_id);
        //     $infoDistribute = $infoDistribute->where('brand_id', $request->brand_id);
        //     $poll = $poll->where('brand_id', $request->brand_id);
        //     $shop = $shop->where('brand_id', $request->brand_id);
        //     $win = $win->where('brand_id', $request->brand_id);
        //     $weblink = $weblink->where('brand_id', $request->brand_id);
        // }

        if ($request->group_id != null && $request->group_id != '' && $request->group_id != 'null') {
            $messaging->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $info->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $infoDistribute->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $poll->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $shop->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $win->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
            $weblink->whereHas('brand', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
        }

        if ($request->country_id != null && $request->country_id != '' && $request->country_id != 'null') {
            $messaging->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $info->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $infoDistribute->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $poll->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $shop->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $win->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
            $weblink->whereHas('brand', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })->whereHas('user', function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            });
        }

        if ($request->city_id != null && $request->city_id != '' && $request->city_id != 'null') {
            $messaging->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $info->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $infoDistribute->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $poll->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $shop->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $win->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
            $weblink->whereHas('user', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
        }

        

        if ($request->startDate != null && $request->startDate != '' && $request->startDate != 'null' && $request->endDate != null && $request->endDate != '' && $request->endDate != 'null') {
            $dateS = date('Y-m-d 00:00:00', strtotime($request->startDate));
            $dateE = date('Y-m-d 00:00:00', strtotime($request->endDate));

            $messaging->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $info->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $infoDistribute->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $poll->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $shop->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $win->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
            $weblink->whereDate('created_at', '>=', $dateS)->whereDate('created_at', '<=', $dateE);
        }else{

            $messaging->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $info->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $infoDistribute->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $poll->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $shop->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $win->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
            $weblink->whereDate('created_at','>=',$start) ->whereDate('created_at','<=',$end);
        
            
        }
        

       


        $messaging = $messaging->orderBy('created_at', 'asc')->get()->toArray();
        $info = $info->orderBy('created_at', 'asc')->get()->toArray();
        $infoDistribute = $infoDistribute->orderBy('created_at', 'asc')->get()->toArray();
        $poll = $poll->orderBy('created_at', 'asc')->get()->toArray();
        $shop = $shop->orderBy('created_at', 'asc')->get()->toArray();
        $win = $win->orderBy('created_at', 'asc')->get()->toArray();
        $weblink = $weblink->orderBy('created_at', 'asc')->get()->toArray();

        $messagingGraph = $infoGraph = $infoDistributeGraph = $pollGraph = $shopGraph = $winGraph = $weblinkGraph = [];

        $dates = array_unique(array_column(array_merge($messaging, $info, $infoDistribute, $poll, $shop, $win, $weblink), 'date'));
        usort($dates, ['App\Http\Controllers\Backend\Subscriber\DashboardController', 'compareDates']);

        foreach ($messaging as $row) {
            $messagingGraph[] = [$row['date'], $row['sum']];
        }

        foreach ($info as $row) {
            $infoGraph[] = [$row['date'], $row['sum']];
        }

        foreach ($infoDistribute as $row) {
            $infoDistributeGraph[] = [$row['date'], $row['sum']];
        }

        foreach ($poll as $row) {
            $pollGraph[] = [$row['date'], $row['sum']];
        }

        foreach ($shop as $row) {
            $shopGraph[] = [$row['date'], $row['sum']];
        }

        foreach ($win as $row) {
            $winGraph[] = [$row['date'], $row['sum']];
        }

        foreach ($weblink as $row) {
            $weblinkGraph[] = [$row['date'], $row['sum']];
        }

        foreach ($dates as $date) {
            if (!in_array($date, array_column($messagingGraph, 0))) {
                $messagingGraph[] = [$date, 0];
            }
            if (!in_array($date, array_column($infoGraph, 0))) {
                $infoGraph[] = [$date, 0];
            }
            if (!in_array($date, array_column($infoDistributeGraph, 0))) {
                $infoDistributeGraph[] = [$date, 0];
            }
            if (!in_array($date, array_column($pollGraph, 0))) {
                $pollGraph[] = [$date, 0];
            }
            if (!in_array($date, array_column($shopGraph, 0))) {
                $shopGraph[] = [$date, 0];
            }
            if (!in_array($date, array_column($winGraph, 0))) {
                $winGraph[] = [$date, 0];
            }
            if (!in_array($date, array_column($weblinkGraph, 0))) {
                $weblinkGraph[] = [$date, 0];
            }
        }
        usort($messagingGraph, ['App\Http\Controllers\Backend\Subscriber\DashboardController', 'compareDatesOfArray']);
        usort($infoGraph, ['App\Http\Controllers\Backend\Subscriber\DashboardController', 'compareDatesOfArray']);
        usort($infoDistributeGraph, ['App\Http\Controllers\Backend\Subscriber\DashboardController', 'compareDatesOfArray']);
        usort($pollGraph, ['App\Http\Controllers\Backend\Subscriber\DashboardController', 'compareDatesOfArray']);
        usort($shopGraph, ['App\Http\Controllers\Backend\Subscriber\DashboardController', 'compareDatesOfArray']);
        usort($winGraph, ['App\Http\Controllers\Backend\Subscriber\DashboardController', 'compareDatesOfArray']);
        usort($weblinkGraph, ['App\Http\Controllers\Backend\Subscriber\DashboardController', 'compareDatesOfArray']);
        return view('backend.subscriber.dashboard.dashboard', compact('brands', 'countries', 'priceSetting', 'totalHistory', 'countHistory', 'messagingGraph', 'infoGraph', 'infoDistributeGraph', 'pollGraph', 'shopGraph', 'winGraph', 'weblinkGraph', 'dates', 'messageHistories', 'infoHistories', 'distributionHistories', 'pollHistories', 'buyHistories', 'winHistories', 'wesiteLinkHistories','count','brandgroups'));
    }

    public static function compareDates($date1, $date2)
    {
        return strtotime($date1) - strtotime($date2);
    }

    public static function compareDatesOfArray($date1, $date2)
    {
        $datetime1 = strtotime($date1[0]);
        $datetime2 = strtotime($date2[0]);
        return $datetime1 - $datetime2;
    }

    public function currencyget(Request $request, $id)
    {
        $country = Country::find($id);
        $currencyData = Currency::where('code', $country['currency'])->first();
        if (!empty($currencyData)) {
            return response()->json(['success' => true, 'data' => $currencyData['id']]);
        } else {
            return response()->json(['success' => false, 'data' => ""]);
        }
    }
}