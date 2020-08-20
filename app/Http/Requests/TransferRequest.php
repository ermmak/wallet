<?php

namespace App\Http\Requests;

use App\Transfer;
use App\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Class TransferRequest
 * @package App\Http\Requests
 */
class TransferRequest extends FormRequest
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
            'from' => [ 'required', 'integer', Rule::exists('wallets', 'id') ],
            'to' => [ 'required', 'integer', 'different:from', Rule::exists('wallets', 'id') ],
            'amount' => [
                'required',
                'regex:/^\d{1,12}(\.\d{1,2})?$/',
                fn (string $attribute, $value, $fail) => $this->checkAmount($fail)
            ],
            'recipient_currency' => 'boolean'
        ];
    }

    /**
     * @param $fail
     */
    protected function checkAmount($fail): void
    {
        $wallet = $this->wallet('from');
        $currencyCode = Str::upper($wallet->currency->code);
        $transferAmount = $this->currencyAmount($wallet, $this->wallet('to'));

        $wallet->amount < $transferAmount && $fail("You can't transfer more than {$wallet->amount} $currencyCode");
    }

    /**
     * Save overall transfer data
     * @return bool
     */
    public function save(): bool
    {
        $from = $this->wallet('from');
        $to = $this->wallet('to');

        return DB::transaction(
            fn () => $this->transfer($from, $to)
        );
    }

    /**
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    protected function wallet(string $type)
    {
        return Wallet::with('currency')->find(
            $this->input($type)
        );
    }

    /**
     * @param $from
     * @param $to
     */
    protected function transfer($from, $to): bool
    {
        $debited = $this->setWalletAmount(
            $from, $this->currencyAmount($from, $to)
        );
        $credited = $this->setWalletAmount(
            $to, $this->currencyAmount($from, $to, false), false
        );

        return
            $debited &&
            $credited &&
            $this->saveTransfer($from, $to);
    }

    /**
     * @param Wallet $wallet
     * @param $amount
     * @param bool $debit
     * @return bool
     */
    protected function setWalletAmount(Wallet $wallet, $amount, $debit = true): bool
    {
        $wallet->amount = $debit
            ? $wallet->amount - $amount
            : $wallet->amount + $amount;

        return $wallet->save();
    }

    /**
     * @param $from
     * @param $to
     * @param bool $debit
     * @return float|int|mixed
     */
    protected function currencyAmount($from, $to, $debit = true)
    {
        $amount = $this->input('amount');
        $recipientCurrency = $this->input('recipient_currency', false);

        if ($debit) {
            return round(
                $recipientCurrency
                    ? $to->currency->rate * $amount / $from->currency->rate
                    : $amount,
                2
            );
        }

        return round(
            $recipientCurrency
                ? $amount
                : $from->currency->rate * $amount / $to->currency->rate,
            2
        );
    }

    /**
     * Save transfer data
     * @param $from
     * @param $to
     * @return bool
     */
    protected function saveTransfer($from, $to): bool
    {
        $transfer = new Transfer;

        $transfer->fromWallet()->associate($from);
        $transfer->fromCurrency()->associate($from->currency);
        $transfer->from_currency_rate = $from->currency->rate;
        $transfer->amount = $this->input('amount');
        $transfer->recipient_currency = $this->input('recipient_currency') ?? false;

        $transfer->toWallet()->associate($to);
        $transfer->toCurrency()->associate($to->currency);
        $transfer->to_currency_rate = $to->currency->rate;

        return $transfer->save();
    }
}
