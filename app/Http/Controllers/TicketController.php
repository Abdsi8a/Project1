<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Trip;
use Illuminate\Http\Request;
use Faker\Factory as Faker;

class TicketController extends Controller
{
    public function searchForTicket(Request $request, $trip_id)
    {
        $trip = Trip::find($trip_id);
        if (!$trip) {
            return response()->json(['message' => 'Trip not found'], 404);
        }

        $attr = $request->validate([
            'airport_id1' => 'required|integer|exists:airports,id',
            'airport_id2' => 'required|integer|exists:airports,id',
            'typeOfTicket' => 'required|string',
            'roundOrOne_trip' => 'required|string|in:RoundTrip,OneWay',
        ]);

        $alreadyTickets = Ticket::where([
            ['airport_id1', $attr['airport_id1']],
            ['airport_id2', $attr['airport_id2']],
            ['typeOfTicket', $attr['typeOfTicket']],
            ['roundOrOne_trip', $attr['roundOrOne_trip']],
        ])->with('airLine')->get();
        
        if ($alreadyTickets->isNotEmpty()) {
            
            $numOfFlights=0;
            foreach ($alreadyTickets as $alreadyTicket) {
            $numOfFlights += 1;
            }
            return response()->json([
                'message' => 'There are already tickets',
                'numberOfFlights'=>$numOfFlights,
                'tickets' => $alreadyTickets,
            ], 200);
        }

        $count = mt_rand(1, 5);
        $tickets = Ticket::factory()->count($count)->create([
            'airport_id1' => $attr['airport_id1'],
            'airport_id2' => $attr['airport_id2'],
            'typeOfTicket' => $attr['typeOfTicket'],
            'roundOrOne_trip' => $attr['roundOrOne_trip'],
            'dateOfTicket' => $trip->dateOfTrip,
            'dateEndOfTicket' => $trip->dateEndOfTrip,
        ]);
        $numOfFlights=0;
        foreach ($tickets as $ticket) {
            $numOfFlights += 1;
        }

        if ($attr['roundOrOne_trip'] == 'RoundTrip') {
            foreach ($tickets as $ticket) {
                $ticket->price += $ticket->price * 0.5;
                $ticket->save();
            }
        }

        return response()->json([
            'message' => 'The ticket(s) created successfully',
            'numberOfFlights'=>$numOfFlights,
            'tickets' => Ticket::where([
                ['airport_id1', $attr['airport_id1']],
                ['airport_id2', $attr['airport_id2']],
                ['typeOfTicket', $attr['typeOfTicket']],
                ['roundOrOne_trip', $attr['roundOrOne_trip']],
            ])->with('cc')->get(),
        ], 200);
    }
}
