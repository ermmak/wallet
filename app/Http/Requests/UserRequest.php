<?php

namespace App\Http\Requests;

use App\User;
use App\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Class UserRequest
 * @package App\Http\Requests
 */
class UserRequest extends FormRequest
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
            'name' => 'required',
            'email' => [ 'required', 'email', Rule::unique('users')->ignore($this->route('user')) ],
            'city' => [ 'required', Rule::exists('cities', 'id') ],
            'currency' => [ 'required', Rule::exists('currencies', 'id') ],
            'amount' => [ 'required', 'regex:/^\d{1,12}(\.\d{1,2})?$/' ]
        ];
    }

    /**
     * @param User|null $user
     * @return bool
     */
    public function save(User $user = null): bool
    {
        return DB::transaction(
            fn () => empty($user) ? $this->createUser() : $this->updateUser($user)
        );
    }

    /**
     * @return bool
     */
    public function createUser(): bool
    {
        $user = new User;

        return
            $this->saveUser($user) &&
            $this->makeWallet($user)->save();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function updateUser(User $user): bool
    {
        $this->saveUser($user);

        return $user->wasChanged();
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function saveUser(User $user): bool
    {
        $user->name = $this->input('name');
        $user->email = $this->input('email');
        $user->password = Hash::make($this->input('password'));
        $user->city()->associate($this->input('city'));

        return $user->save();
    }

    /**
     * @param User $user
     * @return Wallet
     */
    protected function makeWallet(User $user): Wallet
    {
        $wallet = new Wallet;
        $wallet->amount = $this->input('amount');
        $wallet->user()->associate($user);
        $wallet->currency()->associate($this->input('currency'));

        return $wallet;
    }
}
