<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use App\Track;
use App\Grade;
use App\City;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function statics()
    {
        $p_users = User::where('code','!=',null)->where('grade_id','!=',null)->where('city_id','!=',null)->orderBy('id', 'DESC')->limit(50)->get();

        $today_user = User::where('code','!=',null)->where('grade_id','!=',null)->where('city_id','!=',null)->whereDate('created_at', Carbon::today())->count();

        $yesterday_user = User::where('code','!=',null)->where('grade_id','!=',null)->where('city_id','!=',null)->whereDate('created_at', Carbon::yesterday())->count();

        $total_user = User::where('code','!=',null)->where('grade_id','!=',null)->where('city_id','!=',null)->count();

        $m_city_id=User::select('city_id')
            ->groupBy('city_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(1)
            ->get();

        $m_grade_id=User::select('grade_id')
            ->groupBy('grade_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(1)
            ->get();

        $added_cities= User::select('city_id')
            ->groupBy('city_id')
            ->get();

        foreach ($added_cities as $city){
            if ($city->city_id!==null) {
                $city_names[] = City::find($city->city_id)->name;
                $city_counts[] = User::where('city_id',$city->city_id)->count();

            }

        }


        @$m_city=City::find($m_city_id[0]->city_id)->name;
        @$m_grade=Grade::find($m_grade_id[0]->grade_id)->title;

        $tot = 0;
        $tot_today = 0;
        $tot_yesterday = 0;
        $i = 0;
        foreach ($p_users as $p_user) {
            @$p_users[$i]->grade = Grade::find($p_user->grade_id)->title;
            @$p_users[$i]->city = City::find($p_user->city_id)->name;
            $date=$p_user->created_at->format('Y-m-d');
            $date = explode("-", $date);
            @$p_users[$i]->shamsi_created_at=gregorian_to_jalali($date[0], $date[1], $date[2], "/");
            $i++;
        }

        $pays = Track::where('is_success', 1)->get();
        $pays_today = Track::where('is_success', 1)->whereDate('created_at', Carbon::today())->get();

        $pays_yesterday = Track::where('is_success', 1)->whereDate('created_at', Carbon::yesterday())->get();

        foreach ($pays as $pay) {
            $tot = $tot + $pay->amount;
        }
        foreach ($pays_today as $pay) {
            $tot_today = $tot_today + $pay->amount;
        }
        foreach ($pays_yesterday as $pay) {
            $tot_yesterday = $tot_yesterday + $pay->amount;
        }
        $start = Carbon::now()->subDays(30);

        for ($i = 0 ; $i <= 30; $i++) {
            $date=$start->copy()->addDays($i)->format('Y-m-d');
            $date = explode("-", $date);
            $dates[]=gregorian_to_jalali($date[0], $date[1], $date[2], "/");
            $datas[]=User::where('code','!=',null)->where('grade_id','!=',null)->where('city_id','!=',null)->whereDate('created_at', $start->copy()->addDays($i))->count();
            $money=Track::whereDate('created_at', $start->copy()->addDays($i))->where('is_success', 1)->get();
            $m_tot=0;
            foreach ($money as $m) {
                $m_tot+= $m->amount;
            }
            $money_datas[]=$m_tot/10000;
        }


        $max_data=max($money_datas );


        return response()->json(["success" => ["added_cities" => $city_names,"city_counts" => $city_counts,"dates" => $dates,"max_datas" => $max_data,"money_datas" => $money_datas,"datas" => $datas,"m_grade" => $m_grade,"m_city" => $m_city,"total_user" => $total_user, "today_users" => $today_user, "yesterday_users" => $yesterday_user, "tot" => number_format($tot/10), "tot_today" => number_format($tot_today/10), "tot_yesterday" => number_format($tot_yesterday/10), "users" => $p_users]], 200);


    }
}
