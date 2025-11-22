<?php

namespace App\Http\Controllers\Api\Hotel;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelResource;
use App\Http\Traits\ApiResponseTrait as TraitsApiResponseTrait;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{

    use TraitsApiResponseTrait;


    public function getAllHotel(){

    try{

       $hotels = Hotel::with('location')->paginate(8);

        if ($hotels->isEmpty()) {
            return $this->errorResponse('No hotels found', 404);
        }

        return $this->successResponse(HotelResource::collection( $hotels));

    }catch(\Exception $e){
        return $this->errorResponse($e->getMessage() , 500);
    }
    } // end getAllHotel


   public function show($id)
    {
        try{

             $hotel = Hotel::with('location')->find($id);

            if (!$hotel) {
                return $this->errorResponse('Hotel not found', 404);
            }

            return $this->successResponse(new HotelResource($hotel));

        }catch(\Exception $e){
            return $this->errorResponse($e->getMessage() , 500);
        }
    } // end show




}
