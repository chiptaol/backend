<?php

namespace App\Console\Commands;

use App\Models\Hall;
use App\Models\Movie;
use App\Models\Premiere;
use App\Models\Seance;
use Illuminate\Console\Command;

class GenerateSeances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seances:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate seances for available movies';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dayStart = now()->startOfDay();
        $dayEnd = now()->endOfDay();

        $priceList = [
            2000000, 3000000, 4000000, 4500000, 5000000, 5500000, 6000000
        ] ;

        foreach (Hall::all() as $hall) {
            $premiere = Premiere::inRandomOrder()->first();

            while($dayStart < $dayEnd) {
                $data = [
                    'cinema_id' => $hall->cinema_id,
                    'hall_id' => $hall->id,
                    'format_id' => $hall->formats->first('id'),
                    'start_date' => $dayStart,
                    'start_date_time' => $dayStart,
                    'end_date_time' => $dayStart->addHours(2),
                ];

                $standardSeatPrice = $priceList[mt_rand(0, count($priceList) - 1)];
                $vipSeatPrice = $standardSeatPrice + 10000;

                $seanceSeats = $hall->seats->reduce(function ($result, $seat) use ($standardSeatPrice, $vipSeatPrice) {
                    $price = $seat->is_vip ? $vipSeatPrice : $standardSeatPrice;
                    $result[$seat->id] = compact('price');

                    return $result;
                }, []);

                dd($seanceSeats);
//                $seance = $premiere->seances()->create($data);






            }

        }
    }
}
