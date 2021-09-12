<?php

namespace App\Http\Controllers;

use App\Classes\AdminTransaction;
use App\Classes\Transaction;
use App\Http\Requests\CreditRequest;
use App\Http\Requests\DebitRequest;
use App\Http\Requests\TransactionHistoryRequest;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;


class AccountController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register']]);
    }

    public function checkAdmin(){
        $me = (new AuthController) -> accountme();
        if ($me->account_type === 'admin'){
            return 'admin';
        }

        else{

            return 'user';
        }
    }
 
    public function getAccountNumber(){
        $test = (new AuthController)->accountme();
        return $test;
    }


    public function withdrawal(DebitRequest $request, TransactionHistoryRequest $request3){

        $debit = (new Transaction)->withdrawal($request,$request3);
        return $debit;
    }
    
    public function transfer(CreditRequest $request,DebitRequest $request2,TransactionHistoryRequest $request3){

        $transfer = (new Transaction)->transfer($request,$request2, $request3);
        return $transfer;
    }

    public function transactionHistoryM(){

        $me = (new AuthController)->accountme();
        $history = TransactionHistory::where('account_id',$me->id)->orderBy('id','desc')->get();
        return $history;
    }

    public function adminCredit(CreditRequest $request, TransactionHistoryRequest $request3){
   
        $check = $this->checkAdmin();

        if($check === 'admin'){
        $credit = (new AdminTransaction)->credit($request,$request3);
        return $credit;
        }

        else{
            return response()->json(["Error" => "Unauthorised. Not an Admin"],401);
        }
    }


    public function adminDebit(CreditRequest $request, TransactionHistoryRequest $request3){

        $check = $this->checkAdmin();

        if($check === 'admin'){
            $credit = (new AdminTransaction)->debit($request,$request3);
            return $credit;
        }

        else{
            return response()->json(["Error" => "Unauthorised. Not an Admin"],401);
        }
    }

    public function adminViewUser(){

        $check = $this->checkAdmin();

        if($check === 'admin'){
        $users = (new AdminTransaction)->adminViewUser();
        return $users;
        }

        else{
            return response()->json(["Error" => "Unauthorised. Not an Admin"],401);
        }
    }

    public function adminViewAdmin(){

        $check = $this->checkAdmin();

        if($check === 'admin'){
            $user = (new AdminTransaction)->adminViewAdmin();
            return $user;
        }

        else{
            return response()->json(["Error" => "Unauthorised. Not an Admin"],401);
        }
    }


    public function adminSearch(Request $request){

        $check = $this->checkAdmin();

        if($check === 'admin'){
            $user = (new AdminTransaction)->adminSearch($request);
            return $user;
        }

        else{
            return response()->json(["Error" => "Unauthorised. Not an Admin"],401);
        }
    }


    public function getbyId($id){

        $check = $this->checkAdmin();

        if($check === 'admin'){
            $user = (new AdminTransaction)->getbyId($id);
            return $user;
        }

        else{
            return response()->json(["Error" => "Unauthorised. Not an Admin"],401);
        }
    }




}
