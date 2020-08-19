<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;

/**
 * Class TransferController
 * @package App\Http\Controllers
 */
class TransferController extends Controller
{
    /**
     * Get transfers list
     */
    public function index()
    {
    }

    /**
     * Make money transfer between wallets
     * @param TransferRequest $request
     */
    public function make(TransferRequest $request)
    {
        return response()->json($request->save());
    }
}
