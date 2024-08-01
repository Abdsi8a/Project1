<?php

namespace App\Http\Controllers;

use App\Models\NormalUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class AdminController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            'phone'=>'required|numeric|digits:10',
            'password'=>'required|min:6',
        ]);

        $normalUser=NormalUser::where('phone',$request->phone)->first();
        if(!$normalUser){
            return response([
                'message'=>'the phone is wrong',
            ],422);
        }

        $user_id=$normalUser->user_id;
        $user = User::findOrFail($user_id);

        if(!$normalUser|| !Hash::check($request->password,$normalUser->password)){
            return response([
                'message'=>'The provided credentials are incorrect'
            ],422);
        }
        if($normalUser->role=='admin'){
            $token=$user->createToken('auth_token')->accessToken;
                return response([
                    'token'=>$token
                ],200);
        }
        return response([
            'message'=>'no access',
        ],422);

    }

    // Update admin credentials function
    public function updateAdmin(Request $request)
    {
        $attr=$request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $userAdmin = Auth::user()->update([
            'name'=>$request->name,
        ]);
        $normalAdmin=NormalUser::where('user_id',Auth::user()->id)->update(['phone'=>$request->phone,]);

        if($userAdmin && $normalAdmin){
            return response()->json([
                'message'=>'updated successfully',
            ]);
        }
        return response()->json([
            'message'=>'something wronge',
        ]);


    }
    public function updateAdminPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6',
            'NewPassword'=>'required|min:6|confirmed',
        ]);
        $user_id=auth()->user()->id;
        $normalUser = NormalUser::where('user_id',$user_id)->first();


        if(Hash::check($request->password,$normalUser->password) ){
            $normalUser->update(['password' => Hash::make($request['NewPassword'])]);
            return response()->json([
                'message'=> 'the password is updated',
                ],200);
        }
        return response()->json([
        'message'=> 'the old password is wrong',
        ],422);

    }

    public function logoutAdmin(){
        User::find(Auth::id())->tokens()->delete();
        return response([
            'message'=>'Logged out sucesfully'
        ],200);
    }

    public function adminInfo(){
        return response()->json([
        'theAdmin:'=>NormalUser::where('role','admin')->select('user_id','phone')->with('user:id,name')->get(),
        ]);
    }

    public function addToWallet(Request $request)
    {
        // Define custom validation rules for 'EmailOrPhone'
    Validator::extend('email_or_phone', function ($attribute, $value, $parameters, $validator) {
        // Check if the value is a valid email or a valid phone number
        return filter_var($value, FILTER_VALIDATE_EMAIL) || preg_match('/^[0-9]{10,15}$/', $value);
    });
    $attr = $request->validate([
        'EmailOrPhone' => 'required|email_or_phone',
        'amount' => 'required|numeric|min:0'
    ], [
        'email_or_phone' => 'The :attribute must be a valid email address or phone number.'
    ]);

        $user = User::whereHas('googleUser', function ($query) use ($attr) {
            $query->where('email', $attr['EmailOrPhone']);
        })->orWhereHas('normalUser', function ($query) use ($attr) {
            $query->where('phone', $attr['EmailOrPhone']);
        })->first();

        if(!$user){
        return response()->json([
            'message'=>'email or phone not found...'
        ]);}

        $user->wallet=$attr['amount'];
        $user->save();

        return response()->json([
            'message'=>'amount added successful',
            'amount'=>$attr['amount']
        ]);

    }
}
