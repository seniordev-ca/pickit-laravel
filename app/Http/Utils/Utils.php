<?php


namespace App\Http\Utils;


class Utils
{
    public static function makeResponse($data = [], $message = '', $status = true)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function genOTP($digits = 3)
    {
        return '' . rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }
}
