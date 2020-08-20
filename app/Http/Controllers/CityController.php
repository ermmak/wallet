<?php

namespace App\Http\Controllers;

use App\City;
use App\Http\Requests\CityRequest;
use Illuminate\Http\Request;

/**
 * Class CityController
 * @package App\Http\Controllers
 */
class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return City[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function index()
    {
        return City::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CityRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CityRequest $request)
    {
        return response()->json($request->save());
    }

    /**
     * Display the specified resource.
     *
     * @param City $city
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(City $city)
    {
        return response()->json($city);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CityRequest $request
     * @param City $city
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CityRequest $request, City $city)
    {
        return response()->json($request->save($city));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param City $city
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(City $city)
    {
        $city->has('users') && abort(403, 'Has active users');

        return response()->json($city->delete());
    }
}
