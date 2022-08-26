<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Seance;
use App\Models\SeanceSeat;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $seance->load('premiere:id,movie_id');

            collect($validatedData['seat_ids'])->each(function ($id) use ($seance, $user) {

                if ($user->tickets()->where('seance_seat_id', '=', $id)->first()) {
                    throw new BusinessException('Seance seat with ID: ' . $id . ' is not available.');
                }

                $seance->seats()->updateExistingPivot($id, [
                    'is_available' => false
                ]);

                $user->tickets()->create([
                    'seance_id' => $seance->id,
                    'movie_id' => $seance->premiere->movie_id,
                    'seance_seat_id' => $id,
                ]);
            });

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return false;
        }
        DB::commit();

        // TODO Broadcast a new event!

        return true;
    }
}
