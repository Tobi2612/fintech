<?php

namespace App\Http\Controllers;

use App\Classes\Register;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Account;
use App\Models\TransactionHistory;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register','login','all','adminLogin']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function register(RegisterRequest $request){

            $register_user = (new Register)->create( $request);
            return $register_user;
        
    }

    public function registerAdmin(CreateAdminRequest $request){

        $test = $this->accountme();
        if($test->account_type === 'admin'){

            $register_user = (new Register)->createAdmin($request);
            return $register_user;
        }

        else{
            return response()->json(["Error"=>"Unauthorised Action. Not an admin"],401);
    
        }
    }

    public function all(){
   
        return Account::latest()->get();
    }



    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    
    public function adminLogin()
    {
        $credentials = request(['email', 'password']);
        $user = User::where('email',$credentials['email'])->first();

        if($user){
           if($user->account_type ==='admin'){
                if (! $token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                return $this->respondWithToken($token);
            }

            else{
                return response()->json(['error' => 'Not an Admin, Return to regular login'], 401);
            }
        }

        else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function me()
    {
        return response()->json(auth()->user())->getData();
    }


    public function accountme()
    {
        $mee =  response()->json(auth()->user())->getData();
        return Account::where('user_id',$mee->id)->first();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 1000
        ]);
    }
}