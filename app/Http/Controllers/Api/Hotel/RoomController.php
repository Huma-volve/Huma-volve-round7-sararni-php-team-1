<?php

namespace App\Http\Controllers\Api\Hotel;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use App\Http\Traits\ApiResponseTrait as TraitsApiResponseTrait;
use App\Models\Hotel;
use App\Models\Room;

use Illuminate\Http\Request;

class RoomController extends Controller
{
    use  TraitsApiResponseTrait  ;

    public function index($hotelId)
      {
            try{
                $hotel = Hotel::find($hotelId);

            if (!$hotel) {
                return $this->errorResponse('Hotel Not Found', 404);
            }

            $rooms = Room::where('hotel_id', $hotelId)->get();

        if ($rooms->isEmpty()) {
                return response()->json([
                    'status'  => true,
                    'message' => 'No rooms available for this hotel',
                    'data'    => []
                ], 200);

            // return $this->successResponse(RoomResource::collection($rooms));
            return response()->json([
                'status'  => true,
                'message' => 'No rooms available for this hotel',
            
            ]);

            }

            }catch(\Exception $e){
                return $this->errorResponse($e->getMessage() , 500);
            }
    } // end of index


    public function show($hotelId , $roomId)  {

        try{
           $hotel = Hotel::find($hotelId);

        if(!$hotel){
            return $this->errorResponse('Hotel Not Found' , 404);
        }

        $room = Room::with('ratePlans')->where('hotel_id' , $hotelId)->find($roomId);

        if(!$room){
            return $this->errorResponse('Room Not Found' , 404);
        }

        return  $this->successResponse( new RoomResource($room));

        }catch(\Exception $e){
            return $this->errorResponse($e->getMessage() , 500);
        }
    } // end of show
}
