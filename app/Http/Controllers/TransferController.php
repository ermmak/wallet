<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferFilterRequest;
use App\Http\Requests\TransferRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class TransferController
 * @package App\Http\Controllers
 */
class TransferController extends Controller
{
    /**
     * Get transfers list
     * @param TransferFilterRequest $request
     * @return \Illuminate\Support\LazyCollection
     */
    public function index(TransferFilterRequest $request)
    {
        return $this
            ->walletsJoin($this->filteredTransfersQuery($request), $request)
            ->join('wallets as from_wallets', 'transfers.from', '=', 'from_wallets.id')
            ->join('currencies as from_currencies', 'from_wallets.currency_id', '=', 'from_currencies.id')
            ->join('users as from_users', 'from_wallets.user_id', '=', 'from_users.id')
            ->join('wallets as to_wallets', 'transfers.to', '=', 'to_wallets.id')
            ->join('currencies as to_currencies', 'to_wallets.currency_id', '=', 'to_currencies.id')
            ->join('users as to_users', 'to_wallets.user_id', '=', 'to_users.id')
            ->addSelect($this->selects())
            ->cursor()
            ->map(fn ($data) => $this->formatData($data));
    }

    /**
     * @param TransferFilterRequest $request
     * @return Builder
     */
    protected function filteredTransfersQuery(TransferFilterRequest $request): Builder
    {
        $query = DB::table('transfers');

        if ($request->has('period_start')) {
            $periodStart = $request->input('period_start') . ' 00:00:00';
            $query->whereRaw("transfers.created_at >= timestamp '$periodStart'");
        }

        if ($request->has('period_end')) {
            $periodEnd = $request->input('period_end') . ' 00:00:00';
            $query->whereRaw("transfers.created_at <= timestamp '$periodEnd'");
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param TransferFilterRequest $request
     * @return Builder
     */
    protected function walletsJoin(Builder $query, TransferFilterRequest $request)
    {
        return $query
            ->joinSub(
                DB::table('wallets')->where('user_id', $request->input('user')),
                'user_wallets',
                fn ($join) => $join
                    ->on('transfers.from', '=', 'user_wallets.id')
                    ->orOn('transfers.to', '=', 'user_wallets.id')
            );
    }

    /**
     * @return string[]
     */
    protected function selects(): array
    {
        return [
            'transfers.amount', 'transfers.from', 'transfers.to', 'transfers.recipient_currency', 'transfers.created_at',
            'from_wallets.id as from_wallet_id', 'to_wallets.id as to_wallet_id',
            'from_currencies.rate as from_rate', 'to_currencies.rate as to_rate',
            'user_wallets.user_id as user_id',
            'from_users.id as from_user_id', 'to_users.id as to_user_id',
            'from_users.name as from_name', 'to_users.name as to_name',
        ];
    }

    /**
     * @param $data
     * @return object
     */
    protected function formatData($data): object
    {
        $debit = $data->user_id === $data->from_user_id;
        $oppositeUserKey = $debit ? 'recipient' : 'sender';
        $oppositeUserValue = $debit ? $data->to_name : $data->from_name;

        return (object) [
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

    /**
     * @param $data
     * @return object
     */
    protected function creditData($data): object
    {
        $usdAmount = $data->amount * $data->{ $data->recipient_currency ? 'from_rate' : 'to_rate' };

        return (object) [
            'user' => $data->to_name,
            'currency_amount' => "+{$data->amount}",
            'usd_amount' => "+$usdAmount",
            'sender' => $data->from_name,
        ];
    }

    /**
     * Make money transfer between wallets
     * @param TransferRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function make(TransferRequest $request)
    {
        return response()->json($request->save());
    }
}
