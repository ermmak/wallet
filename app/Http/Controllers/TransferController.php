<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use Illuminate\Http\Request;

/**
 * Class TransferController
 * @package App\Http\Controllers
 */
class TransferController extends Controller
{
    /**
     * Get transfers list
     * @param Request $request
     */
    public function index(Request $request)
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
