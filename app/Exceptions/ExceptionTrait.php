<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

trait ExceptionTrait 
{
    public function apiException($request,$e){
      

        if($this->isModel($e)){
           
            return $this->modelResponse();
        }
        if($this->isHttp($e)){
            

            return $this->httpResponse();
        }
        if($this->isToken($e)){

            return $this->tokenResponse();
        }
        if($this->isMethod($e)){

            return $this->methodResponse();
        }
        if($this->isTokenBlacklisted($e)){

            return $this->tokenBlacklistedResponse();
        }
    }


    public function isModel($e){

        return $e instanceof ModelNotFoundException;
    }


    public function isHttp($e){

        return $e instanceof NotFoundHttpException;
    }


    public function isToken($e){

        return $e instanceof AuthenticationException;
    }

    public function isMethod($e){

        return $e instanceof MethodNotAllowedHttpException;
    }


    public function isTokenBlacklisted($e){

        return $e instanceof TokenBlacklistedException;
    }


    public function modelResponse(){

        return response()->json([
            'success'=>false,
            'message'=>'Model not found!'
        ],Response::HTTP_NOT_FOUND);
    }

    
    public function httpResponse(){

        return response()->json([
            'success'=>false,
            'message'=>'Incorect route!'
        ],Response::HTTP_NOT_FOUND);
    }


    public function tokenResponse(){

        return response()->json([
            'success'=>false,
            'message'=>'Unauthorized!'
        ],Response::HTTP_UNAUTHORIZED);
    }

    
    public function methodResponse(){

        return response()->json([
            'success'=>false,
            'message'=> 'The GET method is not supported for this route!'
        ],Response::HTTP_METHOD_NOT_ALLOWED);
    }


    public function tokenBlacklistedResponse(){

         return response()->json([
            'success'=>false,
            'message'=> 'The token has been blacklisted!'
        ],Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
