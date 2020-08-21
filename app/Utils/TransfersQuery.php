<?php


namespace App\Utils;


use App\Http\Requests\TransferFilterRequest;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

trait TransfersQuery
{
    /**
     * @param TransferFilterRequest $request
     * @return Builder
     */
    protected function transfersQuery(TransferFilterRequest $request)
    {
        return $this
            ->walletsJoin($this->filteredTransfersQuery($request), $request)
            ->join('wallets as from_wallets', 'transfers.from', '=', 'from_wallets.id')
            ->join('currencies as from_currencies', 'from_wallets.currency_id', '=', 'from_currencies.id')
            ->join('users as from_users', 'from_wallets.user_id', '=', 'from_users.id')
            ->join('wallets as to_wallets', 'transfers.to', '=', 'to_wallets.id')
            ->join('currencies as to_currencies', 'to_wallets.currency_id', '=', 'to_currencies.id')
            ->join('users as to_users', 'to_wallets.user_id', '=', 'to_users.id')
            ->addSelect($this->selects());
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
}
