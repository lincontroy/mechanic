<?php

namespace App\Http\Controllers\auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
        // Validate request data
   
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_type' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            
            'mobile' => 'required|max:255',
            'password' => 'required|min:9',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 400);
        }
        // Check if validation pass then create user and auth token. Return the auth token
        if ($validator->passes()) {
            $user = User::create([
                'username' => $request->name,
                'user_type' => $request->user_type,
                'email' => $request->email,
                'balance' => 0,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
        
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }


    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    public function me(Request $request)
    {
        return $request->user();
    }
    public function update_loc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'long' => 'required',        
        ]);


        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 400);
        }

        $update=User::where('id',Auth::user()->id)->update(['lat'=>$request->lat,'long'=>$request->long]);

        if($update){
            return response()->json([
                'Success'=>'Location updated'
            ]);
        }else{
            return response()->json([
                'Error'=>'Error updating location'
            ]);
        }



        //this function is called anytime the app is put on

    }

    public function get_mechanic(Request $request)
    {

        //this is the driver lat and long
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'long' => 'required',        
        ]);

        $lat=$request->lat;
        $long=$request->long;


        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'error' => $errors
            ], 400);
        }

        $datas = DB::table("users")
		         ->select("users.id",DB::raw("6371 * acos(cos(radians(" . $lat . "))* cos(radians(users.lat))* cos(radians(users.long) - radians(" . $long . ")) 
		        + sin(radians(" .$lat. ")) 
		        * sin(radians(users.lat))) AS distance"))
                ->where('users.user_type',2)
		        ->groupBy("users.id")
		        ->get();

        if($datas !=null){
            
            foreach($datas as $data){

               //get the distance of the nearest mechanic
                
                $driver=User::select('username','mobile','lat','long')->where('id',$data->id)->get();
               
                return response()->json([$driver]);


            }

        }else{

                return response()->json(['Message'=>"No mechanic found within your location"]);

        }
        

        //this function is called anytime the app is put on

    }

}
