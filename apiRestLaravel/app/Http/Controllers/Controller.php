<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function response($status,$message=null,$data=null)
    {	$status=ucfirst($status);
    	$response=array();
    	$response['status']=$status;
    	$statusCode=($status!='Error' && !is_null($status)) ? $code=200 : $code=400 ;
    	$response['code']=$statusCode;
    	if (!is_null($data)) {
    		$response['data']=$data;
    	}

    	if(!is_null($message)){
    		$response['message']=$message;
    	}
    	return response()->json($response,$response['code']);
    }
}
