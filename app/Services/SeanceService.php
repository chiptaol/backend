<?php

namespace App\Services;

use App\Enums\SeanceSeatStatus;
use App\Enums\TicketStatus;
use App\Jobs\BookSeatJob;
use App\Models\Seance;
use App\Models\SeanceSeat;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use WebSocket\Client;

class SeanceService
{
    public function book(array $validatedData, Seance $seance)
    {
        DB::beginTransaction();
        try {
            $user = User::query()
                ->firstOrCreate([
                    'email' => $validatedData['email']
                ], [
                    'phone' => $validatedData['phone'] ?? null
                ]);
            $seance->load(['premiere:id,movie_id', 'cinema:id,title', 'hall:id,title']);

            if ($seance->bookedSeats()->whereIn('seat_id', $validatedData['seat_ids'])->get()->isNotEmpty()) {
                return false;
            }

            foreach ($validatedData['seat_ids'] as $id) {
                if (Cache::has('booked-seat-id:' . $id)) {
                    return false;
                }
            }

            foreach ($validatedData['seat_ids'] as $id) {
                Cache::put('booked-seat-id:' . $id, true, 120);
            }

            $prices = $seance->seats()
                ->whereIn('seat_id', $validatedData['seat_ids'])
                ->get()
                ->pluck('pivot.price', 'id');

            SeanceSeat::query()
                ->where('seance_id', '=', $seance->id)
                ->whereIn('seat_id', $validatedData['seat_ids'])
                ->update([
                    'status' => SeanceSeatStatus::PENDING
                ]);

            $ticket = $user->tickets()->create([
                'seance_id' => $seance->id,
                'movie_id' => $seance->premiere->movie_id,
                'cinema_title' => $seance->cinema->title,
                'hall_title' => $seance->hall->title,
                'total_price' => $prices->sum(),
                'status' => TicketStatus::PREPARED
            ]);

            $ticket->seats()->attach($validatedData['seat_ids']);
            $ticket->load('movie');
            $ticket->seats->each(function ($item) use ($prices) {
                $item->price = $prices[$item->id];
            });

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return false;
        }
        DB::commit();

        $websocket = new Client(config('app.ws_url') . '/seances/' . $seance->id);
        $seats = SeanceSeat::select('id', 'status', 'seat_id')
            ->where('seance_id', '=', $seance->id)
            ->whereIn('seat_id', $validatedData['seat_ids'])->get();

        foreach ($seats as $seat) {
            $data = [
                'id' => $seat->seat_id,
                'status' => $seat->status
            ];

            $websocket->send(json_encode($data));

            BookSeatJob::dispatch($seat, $websocket)->delay(now()->addSeconds(120));
        }

        $websocket->close();

        return $ticket;
    }
}
