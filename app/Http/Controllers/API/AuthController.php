<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller; 

use Auth; 
use Hash;
use Mail;
use Str;

use Illuminate\Http\Request; 

use App\Models\User;
use App\Models\ModuleRole;
use App\Models\Module;
use App\Mail\SendMail;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        $email = request()->email;
        $password = request()->password;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => 0, 'message' => $email.' is an invalid email address'],500);
        }

        if (empty($password)) {
            return response()->json(['success' => 0, 'message' => 'Please enter your password'],500);
        }

        $user = User::where('email','=',$email)->first();

        if(!$user || !Hash::check($password, $user->password)){
            return response()->json(['success' => 0, 'message' => 'You have entered an invalid email or password'],500);
        }

        if ($user->status != 'verified') {
            return response()->json(['success' => 0, 'message' => 'Please verify your account to login'],500);
        }

        $token = $user->createToken('authToken')->accessToken;
        return response(['success' => 1, 'user' => $user, 'token' => $token, 'message' => 'Authenticated!']);
    }

    public function check()
    {
        $user = Auth::user();

        $route = request()->route;
        $route = ucwords($route);

        $module = Module::where('name','=',$route)->first();
        
        if ($module) {
            $module_role = ModuleRole::where('module_id','=',$module->id)->where('role_id','=',$user->role_id)->where('R','=',true)->first();

            if (!$module_role) {
                return response()->json(['success' => 0, 'user' => $user, 'authenticated' => true], 403);
            }
        }
        
        // if () {}
        //$avatar = UserFile::find($user->file_avatar_id);
        //$avatar = ($avatar) ? asset('storage/avatars/'.$avatar->name) : null;
        //$user->avatar = $avatar;

        return response()->json(['success' => 1, 'user' => $user, 'authenticated' => true], 200);
    }


    public function logout()
    {
        $user = Auth::user();
        $user->token()->revoke(); 
        return response()->json(['success' => 1, 'authenticated' => true], 200);
    }

    public function signup()
    {
        $first_name = request()->first_name;
        $last_name = request()->last_name;
        $email = request()->email;
        $password = request()->password;
        $password2 = request()->password2;
        $phone = request()->phone;

        if (empty($first_name) || empty($last_name)  || empty($email) || empty($password) || empty($phone)) {
            return response()->json(['success' => 0, 'message' => 'Please check all the required fields'],500);   
        }

        if (strlen($password) < 8) {
            return response()->json(['success' => 0, 'message' => 'The password must be at least 8 characeters long'],500); 
        }

        if ($password != $password2) {
            return response()->json(['success' => 0, 'message' => 'Password not matched!'],500); 
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => 0, 'message' => 'You have entered an invalid email address'],500); 
        } 

        $exists = User::where('email','=',$email)->withTrashed()->first();

        if ($exists && $exists->email = $email && !$exists->trashed()) {
            return response()->json(['success' => 0, 'message' => 'The email address has already been taken'],500); 
        }

        $user = new User();
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->email = $email;
        $user->phone = $phone;
        $user->password = app('hash')->make($password);
        $user->verification_code = Str::uuid()->toString();
        $user->save();


        $data = [ 'APP_URL' => getenv("APP_URL"), 'APP_NAME' => getenv("APP_NAME"), 'subject' => 'Please Confirm Your Email - Action Required',  'user' => $user, 'template' => 'confirmation'];
        Mail::to($email)->queue(new SendMail($data));


        // generate jwt-token
        //$token = $user->createToken('authToken')->accessToken;
        unset($user->verifcation_code);

        return response(['success' => 1, 'message' => 'You have successfully created an account...']);
    }

    public function verify()
    {
        $token = request()->token;

        if (empty($token) || $token == 'INVALID') {
            return response()->json(['success' => 0, 'message' => 'Invalid Token!'],500); 
        }

        $user = User::where('verification_code','=',$token)->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid Token!'],500);
        }

        $user->verification_code = 'INVALID';
        $user->status = 'verified';
        $user->save();


        return response(['success' => 1, 'message' => 'User Verified!']);
    }


    public function forgotPassword()
    {
        $email = request()->email;

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => 0, 'message' => 'You have entered an invalid email address'],500); 
        } 

        $exists = User::where('email','=',$email)->first();

        if (!$exists) {
            return response()->json(['success' => 0, 'message' => 'Could\'nt find the email address'],500); 
        }

        $exists->forgot_password_code = Str::uuid()->toString();
        $exists->save();


        $data = [
            'APP_URL' => getenv("APP_URL"), 'APP_NAME' => getenv("APP_NAME"), 'subject' => getenv("APP_NAME").' - Retrieve Account',  
            'user' => $exists, 'template' => 'forgot-password'
        ];

        Mail::to($email)->queue(new SendMail($data));
        return response(['success' => 1, 'message' => 'We sent you an email to reset your password, please check your inbox.']);
    }

    public function verifyResetPasswordToken()
    {
        $token = request()->token;

        if (empty($token) || is_null($token) || $token == 'INVALID') {
            return response()->json(['success' => 0, 'message' => 'Invalid Token!'],500); 
        }

        $user = User::where('forgot_password_code','=',$token)->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid Token!'],500);
        }

        return response(['success' => 1, 'message' => 'User Verified!']);
    }

    public function resetPassword()
    {
        $token = request()->token;
        $password = request()->password;
        $password2 = request()->password2;

        if (strlen($password) < 8) {
            return response()->json(['success' => 0, 'message' => 'The password must be at least 8 characeters long'],500); 
        }

        if ($password != $password2) {
            return response()->json(['success' => 0, 'message' => 'Password not matched!'],500); 
        }

        if (empty($token) || is_null($token) || $token == 'INVALID') {
            return response()->json(['success' => 0, 'message' => 'Invalid Token!'],500); 
        }
        
        $user = User::where('forgot_password_code','=',$token)->first();

        if (!$user) {
            return response()->json(['success' => 0, 'message' => 'Invalid Token!'],500);
        }
        
        $user->forgot_password_code = 'INVALID';
        $user->password = app('hash')->make($password);
        $user->save();

        return response(['success' => 1, 'message' => 'Password has been reset!']);
    }

    public function roles()
    {
        return 'ROLES ACCESS';
    }
}
