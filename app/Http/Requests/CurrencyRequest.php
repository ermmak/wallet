<?php

namespace App\Http\Requests;

use App\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
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
            'name' => $this->nameCodeRule(),
            'code' => $this->nameCodeRule(),
            'rate' => [ 'required', 'regex:/^\d{1,10}(\.\d{1,8})?$/' ]
        ];
    }

    /**
     * @return array
     */
    protected function nameCodeRule()
    {
        $id = $this->route('currency');

        return [
            Rule::requiredIf(!empty($id)), Rule::unique('currencies')->ignore($id)
        ];
    }

    /**
     * @param Currency|null $currency
     * @return bool
     */
    public function save(Currency $currency = null): bool
    {
        return empty($currency) ? $this->createCurrency() : $this->updateCurrency($currency);
    }

    /**
     * @return bool
     */
    protected function createCurrency(): bool
    {
        return (new Currency($this->validated()))->save();
    }

    /**
     * @param Currency $currency
     * @return bool
     */
    protected function updateCurrency(Currency $currency): bool
    {
        $currency->rate = $this->input('rate');

        return $currency->save();
    }
}
