<?php

namespace App\Http\Controllers;

use App\Enums\DBTables;
use App\Http\Requests\FeedbackRequest;
use App\Services\FeedbackService;
use Core\Auth;
use Core\DB;

class FeedbackController extends Controller{
    public function index($uuid)
    {
        $feedbackLinkData = DB::table(DBTables::FEEDBACK_LINKS)
                                ->relations(DBTables::USER,'user_id','id','user')
                                ->where('uuid','=',$uuid)
                                ->firstOrFail();
        return view('feedback',[
            'feedbackLinkData' => $feedbackLinkData,
        ]);
    }

    public function store(FeedbackRequest $request, $uuid)
    {
        $feedbackLinkData = DB::table(DBTables::FEEDBACK_LINKS)->where('uuid','=',$uuid)->firstOrFail();

        $data = $request->validated();

        //DTO
        $feedbackDTO = [
            'uuid'      => $feedbackLinkData['uuid'],
            'user_id'   => $feedbackLinkData['user_id'],
            'feedback'  => $data['feedback'] ?? '',
            'created_at' => time()
        ];

        //Save the feedback

        $insert = DB::table(DBTables::FEEDBACK)->insert($feedbackDTO);
         
        if(!$insert){
            return back();
        };

        return redirect('/feedback/success');
    }

    public function success()
    {
        return view('feedback-success');
    }
}