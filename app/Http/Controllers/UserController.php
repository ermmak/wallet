<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Transfer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return User::with('wallet', 'city.country')->paginate($request->input('perPage') ?? 20);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
        return response()->json($request->save());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        return response()->json(
            User::with('wallet', 'city.country')->find($id)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, User $user)
    {
        return response()->json($request->save($user));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        return response()->json(
            DB::transaction(function () use ($user) {
                Transfer::where('from', $user->wallet->id)->delete();
                Transfer::where('to', $user->wallet->id)->delete();
                $user->wallet()->delete();

                return $user->delete();
            })
        );
    }
}
