<?php
namespace App\Services;

use App\Models\Booking;
use App\Http\Resources\Api\V1\BookingResource;

use App\Http\Resources\Api\V1\FlightDetailResource;

use App\Models\Flight;
use App\Models\FlightLeg;
use App\Models\FlightSeat;
use App\Models\BookingDetail;
use App\Models\BookingParticipant;
use App\Models\BookingFlight;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FlightBookingService
{
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
          
            $this->validateSeatsAvailability($data['segments']);

           
            $mainFlight = Flight::with('flightLegs')->find($data['flight_id']);
            $firstLeg = $mainFlight->flightLegs->first();

            $booking = Booking::create([
                'user_id' => $data['user_id'],
                'booking_reference' => $this->generateBookingReference(),
                'category' => 'flight',
                'item_id' => $data['flight_id'],
                'trip_type' => $data['trip_type'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_price' => 0,
                'currency' => 'USD',
                'booking_date' => now(),
                'booking_time' => now(),
                'departure_time' => $firstLeg->departure_time ?? null,
                'arrival_time' => $firstLeg->arrival_time ?? null,
                'outbound_flight_id' => $data['flight_id'],
                'return_flight_id' => $this->getReturnFlightId($data),
            ]);

            
            $participants = $this->createParticipants($booking->id, $data['participants']);

            $total = 0;
            $bookingDetails = [];

          
            foreach ($data['segments'] as $segment) {
                $seat = FlightSeat::with('flight.flightLegs')->findOrFail($segment['seat_id']);
                $flightLeg = $seat->flight->flightLegs->first();

               
                $participantIndex = $segment['participant_index'];
                $participant = $participants[$participantIndex] ?? $participants[0];


                $total += $seat->price;

                
                $bookingFlight = BookingFlight::create([
                    'booking_id' => $booking->id,
                    'flight_id' => $segment['flight_id'],
                    'flight_leg_id' => $flightLeg->id,
                    'flight_seat_id' => $seat->id,
                    'participant_id' => $participant->id,
                    'class_id' => $seat->class_id,
                    'direction' => $segment['direction'],
                    'price' => $seat->price,
                ]);

              
                $bookingDetails[] = [
                    'flight_leg_id' => $flightLeg->id,
                    'seat_id' => $seat->id,
                    'class_id' => $seat->class_id,
                    'direction' => $segment['direction'],
                    'price' => $seat->price,
                    'participant_id' => $participant->id,
                    'booking_flight_id' => $bookingFlight->id,
                ];

                
                $seat->update(['status' => 'reserved']);


                $participant->update(['seat_number' => $seat->seat_number]);
            }


            BookingDetail::create([
                'booking_id' => $booking->id,
                'meta' => [
                    'segments_details' => $bookingDetails,
                    'participants_count' => count($participants),
                    'booking_summary' => [
                        'total_segments' => count($data['segments']),
                        'adults_count' => collect($data['participants'])->where('type', 'adult')->count(),
                        'children_count' => collect($data['participants'])->where('type', 'child')->count(),
                        'infants_count' => collect($data['participants'])->where('type', 'infant')->count(),
                    ]
                ]
            ]);


            $booking->update(['total_price' => $total]);

            DB::commit();

            return [
                'booking' => $this->loadBookingRelations($booking),
                'flight' => Flight::find($data['flight_id']),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    
    private function validateSeatsAvailability(array $segments)
    {
        $seatIds = array_column($segments, 'seat_id');

        
        $seats = FlightSeat::whereIn('id', $seatIds)->get();

        foreach ($seats as $seat) {
            if ($seat->status !== 'available') {
                throw new \Exception("this seat ( {$seat->seat_number} is unavailable");
            }
        }

       
        if (count($seatIds) !== count(array_unique($seatIds))) {
            throw new \Exception('seat has already reserved');
        }
    }

   
    public function confirmBooking($bookingId)
    {
        DB::beginTransaction();

        try {
            $booking = Booking::where('category', 'flight')->findOrFail($bookingId);

            if ($booking->status !== 'pending') {
                throw new \Exception('can not confirm booking');
            }

            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid'
            ]);

            
            $booking->bookingFlights->each(function ($bookingFlight) {
                if ($bookingFlight->flightSeat) {
                    $bookingFlight->flightSeat->update(['status' => 'confirmed']);
                }
            });

            DB::commit();
            return $this->loadBookingRelations($booking);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    
    public function cancelBooking($bookingId)
    {
        DB::beginTransaction();

        try {
            $booking = Booking::where('category', 'flight')->findOrFail($bookingId);

            if (!in_array($booking->status, ['pending', 'confirmed'])) {
                throw new \Exception('can not cancel booking');
            }

            $booking->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded',
                'cancelled_at' => now(),
                'cancelled_by' => 'user'
            ]);

           
            $this->restoreSeats($booking);

            DB::commit();
            return $this->loadBookingRelations($booking);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

   
    public function updatePaymentStatus($bookingId, array $data)
    {
        DB::beginTransaction();

        try {
            $booking = Booking::where('category', 'flight')->findOrFail($bookingId);

            $booking->update($data);

          
            if ($data['payment_status'] === 'paid' && $booking->status === 'pending') {
                $booking->update(['status' => 'confirmed']);
                $this->confirmSeats($booking);
            }

            DB::commit();
            return $this->loadBookingRelations($booking);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    
    public function findBooking($bookingId)
    {
        return $this->loadBookingRelations(
            Booking::where('category', 'flight')->findOrFail($bookingId)
        );
    }

    
    public function getUserBookings($userId)
    {
        return Booking::with([
            'outboundFlight.carrier',
            'returnFlight.carrier',
            'participants',
            'bookingFlights.flight.carrier'
        ])->where('user_id', $userId)
            ->where('category', 'flight')
            ->latest()
            ->get();
    }

    
    public function searchBookings(array $filters)
    {
        $query = Booking::with([
            'user',
            'outboundFlight.carrier',
            'participants'
        ])->where('category', 'flight');

        if (isset($filters['booking_reference'])) {
            $query->where('booking_reference', 'like', "%{$filters['booking_reference']}%");
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->latest()->get();
    }

    
    private function createParticipants($bookingId, array $participantsData)
    {
        $participants = [];
        foreach ($participantsData as $participantData) {
            $participant = BookingParticipant::create([
                'booking_id' => $bookingId,
                'title' => $participantData['title'],
                'first_name' => $participantData['first_name'],
                'last_name' => $participantData['last_name'],
                'date_of_birth' => $participantData['date_of_birth'],
                'passport_number' => $participantData['passport_number'],
                'passport_expiry' => $participantData['passport_expiry'],
                'nationality' => $participantData['nationality'],
                'email' => $participantData['email'],
                'phone' => $participantData['phone'],
                'type' => $participantData['type'],
                'special_requests' => $participantData['special_requests'] ?? null,
            ]);
            $participants[] = $participant;
        }
        return $participants;
    }

    
    private function restoreSeats(Booking $booking)
    {
        $booking->bookingFlights->each(function ($bookingFlight) {
            if ($bookingFlight->flightSeat) {
                $bookingFlight->flightSeat->update(['status' => 'available']);
            }
        });
    }

    
    private function confirmSeats(Booking $booking)
    {
        $booking->bookingFlights->each(function ($bookingFlight) {
            if ($bookingFlight->flightSeat) {
                $bookingFlight->flightSeat->update(['status' => 'confirmed']);
            }
        });
    }

    
    private function generateBookingReference(): string
    {
        do {
            $reference = 'FLT' . Str::upper(Str::random(6));
        } while (Booking::where('booking_reference', $reference)->exists());

        return $reference;
    }

    
    private function getReturnFlightId(array $data)
    {
        if ($data['trip_type'] === 'round_trip') {
            foreach ($data['segments'] as $segment) {
                if ($segment['direction'] === 'return') {
                    return $segment['flight_id'];
                }
            }
        }
        return null;
    }

    
    private function loadBookingRelations(Booking $booking)
    {
        return $booking->load([
            'user',
            'outboundFlight.carrier',
            'outboundFlight.aircraft',
            'outboundFlight.flightLegs.originAirport',
            'outboundFlight.flightLegs.destinationAirport',
            'returnFlight.carrier',
            'returnFlight.aircraft',
            'returnFlight.flightLegs.originAirport',
            'returnFlight.flightLegs.destinationAirport',
            'participants',
            'bookingFlights.flight.carrier',
            'bookingFlights.flightSeat',
            'bookingFlights.class',
            'bookingFlights.participant',
            'bookingFlights.flightLeg.originAirport',
            'bookingFlights.flightLeg.destinationAirport',
            'bookingDetails',
            'participants.bookingFlights',
            'participants.bookingFlights.flightSeat.class',
            'participants.bookingFlights.flightLeg',
            'participants.bookingFlights.flight'
        ]);
    }
}
