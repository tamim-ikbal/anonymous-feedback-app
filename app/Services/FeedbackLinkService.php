<?php

namespace App\Services;

use App\Enums\DBTables;
use Core\Auth;
use Core\DB;

class FeedbackLinkService{
    public static function getByUser()
    {
        $data = DB::table(DBTables::FEEDBACK_LINKS)->where('user_id','=',Auth::id())->first();
        if($data){
            return $data;
        }
        static::createLink(Auth::id());
        return DB::table(DBTables::FEEDBACK_LINKS)->where('user_id','=',Auth::id())->first();
    }

    public static function createLink($user_id)
    {
        $key = self::generateKey();
        $schema = [
            'user_id' => $user_id,
            'uuid' => $key
        ];

        DB::table('feedback_links')->insert($schema);
    }

    public static function generateKey()
    {
        $key = uniqid();
        if(DB::table('feedback_links')->where('uuid','=',$key)->first()){
            $key = static::generateKey();
        }
        return $key;
    }

    public static function feedback_link($code)
    {
        return uri('/'.$code);
    }
}