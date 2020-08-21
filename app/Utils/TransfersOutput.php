<?php


namespace App\Utils;


trait TransfersOutput
{
    /**
     * @param $data
     * @return array
     */
    protected function formatData($data): array
    {
        $debit = $data->user_id === $data->from_user_id;
        $oppositeUserKey = $debit ? 'recipient' : 'sender';
        $oppositeUserValue = $debit ? $data->to_name : $data->from_name;

        return [
            'usd_amount' => round($this->usdAmount($data), 2),
            'currency_amount' => round($this->currencyAmount($data, $debit), 2),
            'recipient_currency' => $data->recipient_currency,
            'user' => $debit ? $data->from_name : $data->to_name,
            'type' => $debit ? 'debit' : 'credit',
            $oppositeUserKey => $oppositeUserValue,
            'datetime' => $data->created_at,
        ];
    }

    /**
     * @param $data
     * @return float|int
     */
    protected function usdAmount($data)
    {
        return $data->amount * $data->{ $data->recipient_currency ? 'to_rate' : 'from_rate' };
    }

    /**
     * @param $data
     * @param false $debit
     * @return float|int
     */
    protected function currencyAmount($data, $debit = false)
    {
        return $this->usdAmount($data) / $data->{ $debit ? 'from_rate' : 'to_rate' };
    }
}
