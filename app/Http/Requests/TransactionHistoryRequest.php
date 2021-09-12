<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'txn_type' => 'string',
            'purpose' => 'required|string',
            'value' => 'required|integer', 
            'account_id' => 'integer',
            'balance_before' => 'integer',
            'balance_after' => 'integer',
        ];
    }
}
