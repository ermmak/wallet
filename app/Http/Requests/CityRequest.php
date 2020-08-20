<?php

namespace App\Http\Requests;

use App\City;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
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
            'name' => [ 'required', Rule::unique('cities')->ignore($this->route('city')) ],
            'city' => [ 'required', 'integer', Rule::exists('cities', 'id') ],
        ];
    }

    /**
     * @param City|null $city
     * @return bool
     */
    public function save(City $city = null): bool
    {
        return $this->saveCity($city ?? new City);
    }

    /**
     * @param City $city
     * @return bool
     */
    protected function saveCity(City $city): bool
    {
        $city->name = $this->input('name');
        $city->country()->associate($this->input('country'));

        return $city->save();
    }
}
