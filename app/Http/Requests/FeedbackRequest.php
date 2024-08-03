<?php

namespace App\Http\Requests;

use Core\Request;

class FeedbackRequest extends Request{

    protected function rules():array
    {
        return [
            'feedback' => ['required','string','escape'],
        ];
    }
}