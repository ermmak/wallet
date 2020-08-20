<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferFilterRequest extends FormRequest
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
            'user' => [ 'required', 'integer', Rule::exists('users', 'id') ],
            'period_start' => [ 'date_format:Y-m-d' ],
            'period_end' => [ 'date_format:Y-m-d' ],
        ];
    }
}
