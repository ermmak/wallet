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
     */
    public function index(TransferFilterRequest $request)
    {
        return $this
            ->walletsJoin($this->filteredTransfersQuery($request), $request)
            ->join('currencies', 'user_wallets.currency_id', '=', 'currencies.id')
            ->join('users', 'user_wallets.user_id', '=', 'users.id')
            ->addSelect($this->selects())
            ->chunk()
            ->paginate($request->input('perPage') ?? 30);
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

    /**
     * @param TransferFilterRequest $request
     * @return Builder
     */
    protected function filteredTransfersQuery(TransferFilterRequest $request): Builder
    {
        $query = DB::table('transfers');

        $request->has('period_start') &&
        $query->where('created_at', '>=', $request->input('period_start'));

        $request->has('period_end') &&
        $query->where('created_at', '<=', $request->input('period_end'));

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
            'user_wallets.id as wallet_id', 'users.name', 'currencies.code', 'currencies.rate'
        ];
    }
}
