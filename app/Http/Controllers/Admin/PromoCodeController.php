<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPromoCodeRequest;
use App\Http\Resources\PromocodeAdmin;
use App\Models\Promocode;
use App\Traits\apiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    //Trait apiResponse
    use apiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Make the collection of all promo codes
        $promoCodes = PromocodeAdmin::collection(Promocode::all());
        return $this->jsonResponse(200,'Promo code are here',$promoCodes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    //Function to add new promo code, AddPromoCodeRequest applied
    public function store(AddPromoCodeRequest $request): JsonResponse
    {
        $promoCode = Promocode::create($request->only([
            'promocode',
            'started_at',
            'expired_at',
            'discount'
        ]));
        if($promoCode)
            return $this->jsonResponse(201,'Promo code added successfully',$promoCode);
        else
            return $this->jsonResponse(500,'An error has occurred');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = Promocode::destroy($id);
        if($deleted != 0)
            return $this->jsonResponse(200,'The promo code has been deleted successfully');
        else
            return  $this->jsonResponse(500,'Error has been occurred');
    }
}
