<?php

namespace App\Http\Controllers;

use App\Exports\TransfersExport;
use App\Http\Requests\TransferFilterRequest;
use App\Http\Requests\TransferRequest;
use App\Utils\TransfersOutput;
use App\Utils\TransfersQuery;
use Illuminate\Support\LazyCollection;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class TransferController
 * @package App\Http\Controllers
 */
class TransferController extends Controller
{
    use TransfersQuery, TransfersOutput;

    /**
     * Get transfers list
     * @param TransferFilterRequest $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|LazyCollection
     */
    public function index(TransferFilterRequest $request)
    {
        $query = $this->transfersQuery($request);

        return $request->input('csv', false)
            ? Excel::download(new TransfersExport($query), 'transfers.xlsx')
            : $query->cursor()->map(fn ($data) => $this->formatData($data));
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
