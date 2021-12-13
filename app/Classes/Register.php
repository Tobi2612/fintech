<?php

namespace App\Classes;

use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;

class Register
{

    private function generate_account_number($new_user)
    {
        $dat['account_number'] = 1000000000 + $new_user->id;
        $dat['account_balance'] = 5000000;
        $dat['user_id'] = $new_user->id;
        $dat['first_name'] = $new_user->first_name;
        $dat['pin'] = $new_user->pin;
        $dat['username'] = $new_user->username;
        $dat['email'] = $new_user->email;

        if ($new_user->account_type === 'user') {
            $dat['account_type'] = 'user';
        } else {
            $dat['account_type'] = 'admin';
        }

        return Account::create($dat);
    }



    public function create(RegisterRequest $request)
    {

        $data = $request->validated();
        $check_username = $request->username;
        $user = json_decode(User::all(), true);
        $filtered = array_filter($user, function ($u) use ($check_username) {
            return strtolower($u['username']) === strtolower($check_username);
        });


        //check for white spaces in username

        if (!preg_match('/^\S*$/', $check_username)) {
            return response()->json([
                'success' => False,
                'error' => 'Incorrect Username format'
            ], 400);
        }

        // Must start with letter
        // 1-20 characters
        // Letters and numbers only
        if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{0,20}$/', $check_username)) {
            return response()->json([
                'success' => False,
                'error' => 'Incorrect Username format'
            ], 400);
        }

        if (count($filtered) > 0) {
            return "Username already exists";
        } else {
            $data['password'] = bcrypt($data['password']);
            $data['account_type'] = 'user';
            $data['email'] = strtolower($data['email']);
            $data['pin'] = bcrypt($data['pin']);
            $new_user = User::create($data);
            $this->generate_account_number($new_user);
            return $new_user;
        }
    }


    public function createAdmin(CreateAdminRequest $request)
    {
        $data = $request->validated();
        $check_username = $request->username;
        $user = json_decode(User::all(), true);
        $filtered = array_filter($user, function ($u) use ($check_username) {
            return strtolower($u['username']) === strtolower($check_username);
        });

        //check for white spaces in username

        if (!preg_match('/^\S*$/', $check_username)) {
            return response()->json([
                'success' => False,
                'error' => 'Incorrect Username format'
            ], 400);
        }

        // Must start with letter
        // 1-20 characters
        // Letters and numbers only
        if (!preg_match('/^[A-Za-z]{1}[A-Za-z0-9]{0,20}$/', $check_username)) {
            return response()->json([
                'success' => False,
                'error' => 'Incorrect Username format'
            ], 400);
        }



        if (count($filtered) > 0) {
            return "Username already exists";
        } else {
            $data['password'] = bcrypt($data['password']);
            $data['email'] = strtolower($data['email']);
            $data['account_type'] = 'admin';
            $data['pin'] = bcrypt($data['pin']);
            $new_user = User::create($data);
            $this->generate_account_number($new_user);
            return $new_user;
        }
    }
}
