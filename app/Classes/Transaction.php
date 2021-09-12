<?php

namespace App\Classes;

use App\Http\Controllers\AuthController;
use App\Http\Requests\CreditRequest;
use App\Http\Requests\DebitRequest;
use App\Http\Requests\TransactionHistoryRequest;
use App\Models\Account;
use App\Models\TransactionHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Transaction {


    private function checkPin(Request $request){
        $ame = (new AuthController)->accountme();
        if (Hash::check($request->pin, $ame->pin)) {
            return "Working";
        }

        else{
          return response()->json(['error' => 'Unauthorized. Pin Incorrect'], 401);
        }

    }

    private function saveTransactionHistory(CreditRequest $request, TransactionHistoryRequest $request3){

        $me  = (new AuthController)->me();
        $ame = (new AuthController)->accountme();

        $receiver = Account::where('account_number',$request->receiver_account_number)->first();

        $save_transaction_history_sender = $request3->validated();
        $save_transaction_history_receiver = $save_transaction_history_sender;

        $save_transaction_history_sender['txn_type'] = 'Debit';
        $save_transaction_history_sender['account_id'] = $ame->id;
        $save_transaction_history_sender['balance_before'] = $ame->account_balance;
        $save_transaction_history_sender['balance_after'] = $ame->account_balance - $request3->value;

        $save_transaction_history_receiver['txn_type'] = 'Credit';
        $save_transaction_history_receiver['account_id'] = $receiver->id;
        $save_transaction_history_receiver['balance_before'] = $receiver->account_balance;
        $save_transaction_history_receiver['balance_after'] = $receiver->account_balance + $request3->value;
        
        $sender_transaction =TransactionHistory::create($save_transaction_history_sender);
        $receiver_transaction = TransactionHistory::create($save_transaction_history_receiver);

    }

    private function saveWithdrawalHistory(DebitRequest $request, TransactionHistoryRequest $request3){

        $ame = (new AuthController)->accountme();


        $save_transaction_history_receiver = $request3->validated();

        $save_transaction_history_receiver['txn_type'] = 'Withdrawal';
        $save_transaction_history_receiver['account_id'] = $ame->id;
        $save_transaction_history_receiver['balance_before'] = $ame->account_balance;
        $save_transaction_history_receiver['balance_after'] = $ame->account_balance - $request3->value;
        

        $receiver_transaction = TransactionHistory::create($save_transaction_history_receiver);

    }

    private function creditAccount(CreditRequest $request){
        $test = $this->checkPin($request);
        if ($test === 'Working'){
            $me  = (new AuthController)->me();
            $ame = (new AuthController)->accountme();

            $receiver = Account::where('account_number',$request->receiver_account_number)->first();
            $receiver_user = User::where('id',$receiver->user_id)->first();

                if ($request->value <= 0){
                    return response()->json(['error' => '"Please enter an amount greater than 0"'], 401);
                }    
        
                else if($request->value <= $receiver->account_balance){

                $receiver->account_balance += $request->value;
                $receiver->save(); 

                return response()->json(["success"=>"Transaction of {$request->value} to {$receiver_user->first_name} successful."],200);
                }
        
                else{ 
                    return response()->json(['error' => 'You dont have enough funds to make this transaction.'], 401);
                    }
        }

        else{
                return response()->json(['error' => 'Incorrect Pin.'], 401);
        }
    }
        


    private function debitAccount(DebitRequest $request){

        $ame = (new AuthController)->accountme();
        $sender = Account::where('account_number',$ame->account_number)->first();

        if ($request->value <= 0){
            return response()->json(['error' => 'Please enter an amount greater than 0.'], 401);
        } 

        else if($request->value <= $sender->account_balance){

            $sender->account_balance -= $request->value;
            $sender->save(); 
            return response()->json(["success" => "Transaction successful. {$sender->first_name}, your new balance is {$sender->account_balance}"],200);    
        }
        
        else{
               return response()->json(['error' => 'You dont have enough funds to make this transaction.'], 401);

        }

    }



    public function transfer(CreditRequest $request,DebitRequest $request2, TransactionHistoryRequest $request3){
        $me  = (new AuthController)->me();
        $ame = (new AuthController)->accountme();

        $test = $this->checkPin($request);
        $receiver = Account::where('account_number',$request->receiver_account_number)->first();

        if ($test === 'Working'){

            $this->creditAccount($request);
            $this->debitAccount($request2);
            $this->saveTransactionHistory($request,$request3);

            $sender = Account::where('account_number',$ame->account_number)->first();

            return TransactionHistory::where('account_id',$ame->id)->latest('id')->first();
        }

        else{
            return response()->json(['error' => 'Incorrect Pin.'], 401);
        }
    
    }

    public function withdrawal(DebitRequest $request,TransactionHistoryRequest $request3){

        $test = $this->checkPin($request);
        if ($test === 'Working'){
                $ame = (new AuthController)->accountme();
                $sender = Account::where('account_number',$ame->account_number)->first();

                if ($request->value <= 0){
                    return response()->json(['error' => 'Please enter an amount greater than 0.'], 401);
                } 

                else if($request->value <= $sender->account_balance){

                    $sender->account_balance -= $request->value;
                    $sender->save(); 
                    $this->saveWithdrawalHistory($request,$request3);
                    return TransactionHistory::where('account_id',$ame->id)->latest('id')->first();    
                }
                
                else{
                    return response()->json(['error' => 'You dont have enough funds to make this transaction.'], 401);

                }
        }
        else{
            return response()->json(['error' => 'Incorrect Pin.'], 401);
        }
    
    }

}