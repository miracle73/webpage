<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorVerification;
use Carbon\Carbon;
use App\User;

class LoginController extends Controller
{
     /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }
    public function expireTime() {
        $myTTL = 120960; //minutes
        return $this->jwt->factory()->setTTL($myTTL);
    }

    public function authenticate(Request $request)
    {     
        $this->expireTime();
        $this->validateRequest($request);

        $user = $request->only('password','auth_permit');

        // try {
            // if (!$token = $this->jwt->attempt($credentials)) {
        //         return response()->json(['message' => 'invalid credentials'], 400);
        //     }
        // } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        //     return response()->json(['token_expired'], 401);
        // } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        //     return response()->json(['token_invalid'], 401);
        // } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        //     return response()->json(['token_absent' => $e->getMessage()], 401);
        // }

        // $user = Auth::guard('api')->user();
    
        $image_link = env('CLOUDINARY_IMAGE_LINK');
        $image_format = 'w_200,c_thumb,ar_4:4,g_face/';

        if ($user != null) {
            // if($user->two_factor_enabled == 'yes'){
            //     $user->email_verified_at = null;
            //     $user->verifycode = mt_rand(100000,999999);
            //     $user->save();

            //     //Send a mail form account verification
            //     Mail::to($user->email)->send(new TwoFactorVerification($user));

            //     $msg['success'] = true;
            //     $msg['two_factor'] = true;
            //     $msg['message'] = 'Two factor authentication set, please verify you account with otp sent to your email!';
            //     return response()->json($msg, 200);
            // }

            $msg['success'] = true;
            $msg['two_factor'] = false;
            $msg['message'] = 'Login Successful!';
            $msg['user'] = $user;
            $msg['image_link'] = $image_link;
            $msg['image_small_view_format'] = $image_format;
            // $msg['token'] = 'Bearer '. $token;
            // $msg['token_type'] = 'bearer';
            // $msg['expires_in(minutes)'] = auth()->factory()->getTTL();
            return response()->json($msg, 200);
        } else {
            $msg['success'] = false;
            $msg['two_factor'] = false;
            $msg['message'] = 'Login Unsuccessful: account has not been confirmed yet!';
            return response()->json($msg, 401);
        }
    }

    
    public function refresh()
    {   
        return response()->json([
            'access_token' => 'Bearer '. auth()->refresh(),
            'token_type'   => 'bearer',
            'expires_in(minutes)'   => (int)auth()->factory()->getTTL()
        ], 200);
    }

    

    public function validateRequest(Request $request){
            $rules = [
                'auth_permit' => 'required',
                'password' => 'required',
            ];
            $messages = [
                'required' => ':attribute is required'
            ];
        $this->validate($request, $rules, $messages);
    }
}
