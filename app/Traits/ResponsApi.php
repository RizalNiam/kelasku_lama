<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ResponsApi{
    public static function requestSuccessData($message, $data = [], $status = 200)
    {
        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $data,
        ]);
    }
    public static function requestSuccessWithLog($log, $message = 'Success!')
    {
        return response()->json([
            "status" => Response::HTTP_OK,
            "message" => $message,
            "log" => $log
        ], Response::HTTP_OK);
    }
    public static function requestSuccess($message = 'Success!', $code = 200)
    {
        return response()->json([
            "status" => $code,
            "message" => $message,
        ], $code);
    }
    public static function loginSuccess($dataUser, $token)
    {
        return response()->json([
            "status" => Response::HTTP_OK,
            "message" => "Login Success!",
            "token" => $token,
            "data" => $dataUser
        ]);
    }
    public static function requestRefreshToken($token)
    {
        return response()->json([
            "status" => Response::HTTP_OK,
            "message" => "Success!",
            "token" => $token,
        ]);
    }
    public static function badRequest($message = 'Failed!', $error = 'bad_request')
    {
        return response()->json([
            "status" => Response::HTTP_BAD_REQUEST,
            "message" => $message,
            "errors" => $error
        ], Response::HTTP_BAD_REQUEST);
    }
    public static function requestUnauthorized($message, $errors = 'Unauthorized')
    {
        return response()->json([
            "status" =>  Response::HTTP_UNAUTHORIZED,
            "message" => $message,
            "errors" => $errors,
        ], Response::HTTP_UNAUTHORIZED);
    }
    public static function requestNotFound($message)
    {
        return response()->json([
            "status" => Response::HTTP_NOT_FOUND,
            "message" => $message,
        ], Response::HTTP_NOT_FOUND);
    }
    public static function responseValidation($errors = [], $message = 'Failed!')
    {
        return response()->json([
            "status" => Response::HTTP_BAD_REQUEST,
            "message" => $message,
            "errors" => $errors
        ], Response::HTTP_BAD_REQUEST);
    }
}