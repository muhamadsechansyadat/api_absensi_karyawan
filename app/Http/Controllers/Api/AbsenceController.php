<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Absence;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Stevebauman\Location\Facades\Location;

class AbsenceController extends Controller
{
    /**
    * @OA\Post(
    * path="/api/absence/clock-in",
    * tags={"Absence"},
    * security={{"bearer_token":{}}},
    *      @OA\Response(
    *          response=201,
    *          description="Clock In Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Clock In Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function clockIn(Request $request){
        try{
            /* $ip = $request->ip(); Dynamic IP address */
            // $ip = '48.188.144.248'; /* Static IP address */
            $ip = '110.33.122.75'; /* Static IP address */
            $location = Location::get($ip);
            $user_id = Auth::user()->id;
            $absence = Absence::create([
                'user_id' => $user_id,
                'clock_in' => Carbon::now(),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude
            ]);

            return response()->json([
                'res' => true,
                'message' => 'User successfully clock in',
                'data' => $absence
            ]);
        }catch(QueryException $e){
            return response()->json([
                'res' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
    * @OA\Post(
    * path="/api/absence/clock-out",
    * tags={"Absence"},
    * security={{"bearer_token":{}}},
    *      @OA\Response(
    *          response=201,
    *          description="Clock Out Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Clock Out Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function clockOut(Request $request){
        try{
            /* $ip = $request->ip(); Dynamic IP address */
            // $ip = '48.188.144.248'; /* Static IP address */
            $ip = '110.33.122.75'; /* Static IP address */
            $location = Location::get($ip);
            $user_id = Auth::user()->id;
            $now = Carbon::now();
            $dataClockIn = Absence::where('user_id', $user_id)->whereDate('clock_in', '=', $now)->first();
            
            if(empty($dataClockIn)){
                return response()->json([
                    'res' => false,
                    'message' => 'Please clock in first'
                ], 422);
            }
            $absence = Absence::create([
                'user_id' => $user_id,
                'clock_out' => Carbon::now(),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude
            ]);

            return response()->json([
                'res' => true,
                'message' => 'User successfully clock out',
                'data' => $absence
            ]);
        }catch(QueryException $e){
            return response()->json([
                'res' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
    * @OA\Get(
    * path="/api/absence/clock-in",
    * tags={"Absence"},
    * security={{"bearer_token":{}}},
    * @OA\Parameter(
    *    description="Date format d/m/Y example: 7/8/2023",
    *    in="query",
    *    name="date",
    *    @OA\Schema(
    *       type="string",
    *       format ="date",
    *    )
    * ),
    *      @OA\Response(
    *          response=201,
    *          description="Successfully get clock in",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Successfully get clock in",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function getClockIn(Request $request){
        try{
            $date = (isset($request->date)) ? Carbon::createFromFormat('d/m/Y',  $request->date) : '';
            $userId = Auth::user()->id;
            if($date == ''){
                // untuk mengambil semua data absen clock in
                $dataClockIn = Absence::where('user_id', $userId)->whereNotNull('clock_in')->paginate();
            }else{
                // untuk mengambil data absen clock in tanggal berapapun 
                $dataClockIn = Absence::where('user_id', $userId)->whereDate('clock_in', '=', $date)->orderBy('clock_in', 'ASC')->limit(1)->first();
            }

            return response()->json([
                'res' => true,
                'message' => 'Successfully get clock in',
                'data' => $dataClockIn
            ]);
        }catch(QueryException $e){
            return response()->json([
                'res' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
    * @OA\Get(
    * path="/api/absence/clock-out",
    * tags={"Absence"},
    * security={{"bearer_token":{}}},
    * @OA\Parameter(
    *    description="Date format d/m/Y example: 7/8/2023",
    *    in="query",
    *    name="date",
    *    @OA\Schema(
    *       type="string",
    *       format ="date",
    *    )
    * ),
    *      @OA\Response(
    *          response=201,
    *          description="Successfully get clock out",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Successfully get clock out",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function getClockOut(Request $request){
        try{
            $date = (isset($request->date)) ? Carbon::createFromFormat('d/m/Y',  $request->date) : '';
            $userId = Auth::user()->id;
            if($date == ''){
                // untuk mengambil semua data absen clock out
                $dataClockOut = Absence::where('user_id', $userId)->whereNotNull('clock_out')->paginate();
            }else{
                // untuk mengambil data absen clock out tanggal berapapun 
                $dataClockOut = Absence::where('user_id', $userId)->whereDate('clock_out', '=', $date)->orderBy('clock_out', 'DESC')->limit(1)->first();
            }

            return response()->json([
                'res' => true,
                'message' => 'Successfully get clock out',
                'data' => $dataClockOut
            ]);
        }catch(QueryException $e){
            return response()->json([
                'res' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
