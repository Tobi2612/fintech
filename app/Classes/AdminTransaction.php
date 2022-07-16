<?php

namespace App\Classes;

use App\Http\Controllers\AuthController;
use App\Http\Requests\CreditRequest;
use App\Http\Requests\TransactionHistoryRequest;
use App\Models\Account;  
use App\Models\TransactionHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminTransaction {


    public function adminViewUser(){

        return Account::where('account_type','user')->get();
    }


    public function adminViewAdmin(){

        return Account::where('account_type','admin')->get();
    }

    public function adminSearch(Request $request){

        $searchby= $request->searchby;
        $search = $request->search;  
        
        $columns = ['account_number',"username"];
  
        switch ($searchby) {
          case 'General Search':
            $query = Account::query();
            foreach($columns as $column){
                $query->orWhere($column, $search);
            }
            $user = $query->get();
            // $user->appends(['searchby'=> 'General Search','search' =>$search]);
            break;
          case 'Account Number':
            $user = Account::query()->where("account_number", $search)->get();
            // $user->appends(['searchby'=> 'Account Number','search' => $search]);
            break;
          case 'Username':
            $user = Account::query()->where("username", $search)->get();
            // $user->appends(['searchby'=> 'Username','search' =>$search]);
            break;
          default:
            return response()->json(["error"=>"Error in search function"],401);
        }
  
  
    return $user;
  
    }

    public function getbyId($id){
        if(!Account::find($id)){
            return response()->json(["Error" => "User not Found"],401);
        }
        $user = Account ::findOrFail($id);
       return $user;
    }
  
    
    private function checkPin(CreditRequest $request){

        $ame = (new AuthController) ->accountme();
   
        if (Hash::check($request->pin, $ame->pin)) {
             return 'Working';
        }
  
        else{
            return response()->json(['error' => 'Unauthorized. Pin Incorrect'], 401);
        }
  
    }
  

    private function saveCreditTransactionHistory(CreditRequest $request, TransactionHistoryRequest $request3){
  

        $ame = (new AuthController)->accountme();
  
        $receiver = Account::where('account_number',$request->receiver_account_number)->first();
  
        $save_transaction_history_sender = $request3->validated();
        $save_transaction_history_receiver = $save_transaction_history_sender;

        $save_transaction_history_sender['txn_type'] = 'Admin Credit to User';
        $save_transaction_history_sender['account_id'] = $ame->id;
        $save_transaction_history_sender['balance_before'] = $ame->account_balance;
        $save_transaction_history_sender['balance_after'] = $ame->account_balance;

        $save_transaction_history_receiver['txn_type'] = 'Credit from Admin';
        $save_transaction_history_receiver['account_id'] = $receiver->id;
        $save_transaction_history_receiver['balance_before'] = $receiver->account_balance;
        $save_transaction_history_receiver['balance_after'] = $receiver->account_balance + $request3->value;
        
        $sender_transaction = TransactionHistory::create($save_transaction_history_sender);
        $receiver_transaction = TransactionHistory::create($save_transaction_history_receiver);
  
    }
  

    private function saveDebitTransactionHistory(CreditRequest $request, TransactionHistoryRequest $request3){
  

        $ame = (new AuthController)->accountme();
  
        $receiver = Account::where('account_number',$request->receiver_account_number)->first();
  
        $save_transaction_history_sender = $request3->validated();
        $save_transaction_history_receiver = $save_transaction_history_sender;

        $save_transaction_history_sender['txn_type'] = 'Admin Debit from User';
        $save_transaction_history_sender['account_id'] = $ame->id;
        $save_transaction_history_sender['balance_before'] = $ame->account_balance;
        $save_transaction_history_sender['balance_after'] = $ame->account_balance;

        $save_transaction_history_receiver['txn_type'] = 'Debit by Admin';
        $save_transaction_history_receiver['account_id'] = $receiver->id;
        $save_transaction_history_receiver['balance_before'] = $receiver->account_balance;
        $save_transaction_history_receiver['balance_after'] = $receiver->account_balance + $request3->value;
        
        $sender_transaction =TransactionHistory::create($save_transaction_history_sender);
        $receiver_transaction = TransactionHistory::create($save_transaction_history_receiver);
  
    }
  
  
    public function credit(CreditRequest $request, TransactionHistoryRequest $request3){
 
        $test = $this->checkPin($request);
        if ($test === 'Working'){

            $me  = (new AuthController)->me();
            $ame = (new AuthController)->accountme();

            $receiver = Account::where('account_number',$request->receiver_account_number)->first();
            $receiver_user = User::where('id',$receiver->user_id)->first();
        
                if ($request->value <= 0){
                    return response()->json(['error' => '"Please enter an amount greater than 0"'], 401);
                }    
          
                else{
        
                $receiver->account_balance += $request->value;
                $receiver->save(); 

                $save_history = $this->saveCreditTransactionHistory($request,$request3);
                return TransactionHistory::where('account_id',$ame->id)->latest('id')->first();
                }
        }
  
        else{
            return response()->json(['error' => 'Incorrect Pin.'], 401);
        }
    }


    public function debit(CreditRequest $request, TransactionHistoryRequest $request3){
 
        $test = $this->checkPin($request);
        if ($test === 'Working'){

            $me  = (new AuthController)->me();
            $ame = (new AuthController)->accountme();
    
            $receiver = Account::where('account_number',$request->receiver_account_number)->first();
            $receiver_user = User::where('id',$receiver->user_id)->first();
  
                if ($request->value <= 0){
                    return response()->json(['error' => '"Please enter an amount greater than 0"'], 401);
                }    
          
                else{
        
                $receiver->account_balance -= $request->value;
                $receiver->save(); 
                

                $save_history = $this->saveDebitTransactionHistory($request,$request3);
                return TransactionHistory::where('account_id',$ame->id)->latest('id')->first();
                }
        }
  
        else{
            return response()->json(['error' => 'Incorrect Pin.'], 401);
        }
    }
}