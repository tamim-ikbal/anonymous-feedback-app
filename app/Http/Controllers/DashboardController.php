<?php

namespace App\Http\Controllers;

use App\Enums\DBTables;
use App\Services\FeedbackLinkService;
use App\Services\FeedbackService;
use Core\Auth;
use Core\DB;

class DashboardController extends Controller{

    public function index(){
        $feedbackLink = FeedbackLinkService::getByUser();
        $feedbacks = DB::table(DBTables::FEEDBACK)
                        ->where('user_id','=',Auth::id())
                        ->where('uuid','=',$feedbackLink['uuid'])
                        ->get();
                        
        return view('dashbaord',[
            'feedbackLink' => FeedbackLinkService::feedback_link($feedbackLink['uuid']),
            'feedbacks' => $feedbacks,
        ]);
    }

}