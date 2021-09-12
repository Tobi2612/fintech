<?php

namespace App\Classes;

use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;

class Register {
 
    private function generate_account_number($userr){
        $dat['account_number'] = 1000000000 + $userr->id;
        $dat['account_balance'] = 5000000;
        $dat['user_id'] = $userr->id;
        $dat['first_name'] = $userr->first_name;
        $dat['pin'] = $userr->pin;
        $dat['username'] = $userr->username;
        $dat['email'] = $userr->email;

        if($userr->account_type === 'user'){
            $dat['account_type'] = 'user';
        }

        else{
            $dat['account_type'] = 'admin';
        }
    
        return Account::create($dat);
    }



    public function create(RegisterRequest $request){
        
        $data = $request -> validated();
        $no = $request->username;
        $user = json_decode(User::all(),true);
        $filtered = array_filter($user , function($u) use($no) {
            return $u['username'] === $no;
        });

        if(count($filtered)>0){
            return "Username already exists";
        }

        else{
        $data['password'] = bcrypt($data['password']);
        $data['account_type'] = 'user';
        $data['pin'] = bcrypt($data['pin']);
        $userr = User::create($data);
        $this->generate_account_number($userr);
        return $userr;
        }
    }


    public function createAdmin(CreateAdminRequest $request){
        $data = $request -> validated();
        $no = $request->username;
        $user = json_decode(User::all(),true);
        $filtered = array_filter($user , function($u) use($no) {
            return $u['username'] === $no;
        });

        if(count($filtered)>0){
            return "Username already exists";
        }

        else{
        $data['password'] = bcrypt($data['password']);
        $data['account_type'] = 'admin';
        $data['pin'] = bcrypt($data['pin']);
        $userr = User::create($data);
        $this->generate_account_number($userr);
        return $userr;
        }
    } 

}