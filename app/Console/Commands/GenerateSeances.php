<?php

namespace App\Console\Commands;

use App\Models\Hall;
use App\Models\Movie;
use App\Models\Premiere;
use App\Models\Seance;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateSeances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seance:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate seances for available movies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $priceList = [
            2000000, 3000000, 4000000, 4500000, 5000000, 5500000, 6000000
        ];

        $i = 1;

        while ($i <= 7) {
            $dayStart = now()->addDays($i)->startOfDay();

            foreach (Premiere::with(['movie'])->cursor() as $premiere) {
                $increment = 0;
                $startDateTime = Carbon::parse($dayStart)->addHours(6);


                while ($increment < 10) {
                    if (Carbon::parse($startDateTime)->addMinutes($premiere->movie->duration) > Carbon::parse($dayStart)->addDay()->startOfDay()) {
                        break;
                    }

                    $hall = Hall::with(['seats'])->where('cinema_id', '=', $premiere->cinema_id)->inRandomOrder()->first();
                    $standardPrice = $priceList[mt_rand(0, count($priceList) - 1)];
                    $prices = $hall->seats->reduce(function ($result, $item) use ($priceList, $standardPrice) {
                        if (!array_key_exists('standard', $result) && !$item->is_vip) {
                            $result['standard'] = $standardPrice;
                        }

                        if (!array_key_exists('vip', $result) && $item->is_vip) {
                            $result['vip'] = $standardPrice + 1000000;
                        }

                        return $result;
                    }, []);
                    $prices['standard'] ??= null;
                    $prices['vip'] ??= null;


                    $endDateTime = Carbon::parse($startDateTime)->addMinutes($premiere->movie->duration);
                    $seance = Seance::create([
                        'cinema_id' => $hall->cinema_id,
                        'hall_id' => $hall->id,
                        'premiere_id' => $premiere->id,
                        'format_id' => $hall->formats->first()->id,
                        'prices' => $prices,
                        'start_date' => $startDateTime,
                        'start_date_time' => $startDateTime,
                        'end_date_time' => $endDateTime
                    ]);

                    $seats = $hall->seats->reduce(function($result, $seat) use ($prices) {
                        $price = $seat->is_vip ? $prices['vip'] : $prices['standard'];

                        $result[$seat->id] = compact('price');

                        return $result;
                    }, []);

                    $seance->seats()->attach($seats);

                    $increment++;
                    $startDateTime->addMinutes($premiere->movie->duration);
                }
            }
            $i++;
        }

        return true;

    }
}
