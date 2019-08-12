<?php

use Illuminate\Http\Request;
use App\Http\Resources\Participant as ParticipantResource;

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('participants/all', function(){
    $participants = App\Participant::orderBy('score', 'desc')->limit(20)->get();
    return ParticipantResource::collection($participants);
});

Route::get('participant', function(){
    $participants = App\Participant::inRandomOrder()->limit(2)->get();
    return ParticipantResource::collection($participants);
});

Route::post('participant/submit', function(Request $request){
    $validatedData = $request->validate([
        'first' => 'required',
        'second' => 'required',
        'selected' => 'required',
    ]);

    function probability($rating1, $rating2){
        return 1.0 * 1.0 / (1 + 1.0 * pow(10, 1.0 * ($rating1 - $rating2) / 400));
    }
    
    function calculateScore($win_rank, $lost_rank){
      //finding probability of players
    
      $win_prob = probability($win_rank, $lost_rank);
      $lost_prob = probability($lost_rank, $win_rank);
    
      //echo $win_prob, $lost_prob;
    
      //const 
      $const00 = 30;
    
      //updating ratings
      $win_rank = round($win_rank + $const00 * (1 - $win_prob));
      $lost_rank = round($lost_rank + $const00 * (0 - $lost_prob));
    
      return [ $win_rank, $lost_rank ];
    }

    // return [ $request->first, $request->second, $request->selected ];

    $participant1 = App\Participant::find((int)$request->first);
    $participant2 = App\Participant::find((int)$request->second);

    switch((int)$request->selected){
        case 1:
            $calculatedScores = calculateScore($participant1->score, $participant2->score);
            DB::table('participants')->where('id', $participant1->id)->update(['score' => $calculatedScores[0]]);
            DB::table('participants')->where('id', $participant2->id)->update(['score' => $calculatedScores[1]]);
            break;
        case 2:
            $calculatedScores = calculateScore($participant1->score, $participant2->score);
            DB::table('participants')->where('id', $participant2->id)->update(['score' => $calculatedScores[0]]);
            DB::table('participants')->where('id', $participant1->id)->update(['score' => $calculatedScores[1]]);
            break;
    }

    $participant = App\Participant::inRandomOrder()->limit(2)->get();
});