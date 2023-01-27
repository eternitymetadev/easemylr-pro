<?php

    namespace App\Http\Middleware;

    use Closure;
    use JWTAuth;
    use Exception;
    use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;
    use Helper;
    use Route;

    class JwtMiddleware extends BaseMiddleware
    {

        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle($request, Closure $next)
        {
            
            $data = [];
            $message = "";
            $status = false;
            $errorCode = 400;
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (Exception $e) {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    $message = "Token is Invalid";
                    $status = false;
                    $errorCode = 401;
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    $message = "Token is Expired";
                    $status = false;
                    $errorCode = 401;
                }else{
                    $message = "Authorization Token not found";
                    $status = false;
                    $errorCode = 401;
                }
                return Helper::apiResponseSend($message,$data,$status,$errorCode);
            }
            return $next($request);
        }
    }