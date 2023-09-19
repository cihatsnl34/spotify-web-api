<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SpotifyService;
use Illuminate\Support\Facades\Mail;

class CheckSpotifyTrackCount extends Command
{
    protected $signature = 'spotify:check-track-count';
    protected $description = 'Check Spotify track count and send an email if it changes';

    protected $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        parent::__construct();
        $this->spotifyService = $spotifyService;
    }

    public function handle()
    {
        // Spotify API'ye istek yaparak şarkı sayısını alın
        $response = $this->spotifyService->getTopArtists(10, 5, 'BQAk2_oVKgxuQ9dxqXJuR8e4dVZnLjlWCeG1tjUYo4vZTag-TBHKKYcuvrIdfa76LSLjZDvyAoWm65mrX5zD0x5kivxZX707Q5FGHm8_K_CN_V6-8siRaXKetvauP5Km6slgtD57CeIEUe7AKu42j-ORW8-kCuMwiLJtNMs0HzqTKT-H0y1HACiKq2nQMOEUSKkWk84qwOSoaXWWwIji7lVIt4jI4muLX_rTyN2Yln1iOkZjYBAJLQfrWRxNXB-q8m2xaBHOlcYd8Lly78baebI_');
        $currentTrackCount  = json_decode(json_encode($response), true);


        if ($currentTrackCount['original']['savedNewCount'] != 0) {
            // Şarkı sayısında değişiklik var, e-posta gönder
            Mail::raw("Spotify track count has changed:" . $currentTrackCount['original']['savedNewCount'] . "  tracks", function ($message) {
                $message->to('cihatsenell@gmail.com')->subject('Spotify Track Count Change');
                // $message->to(auth()->user()->email)->subject('Spotify Track Count Change');
            });

        } else {
            // Şarkı sayısında değişiklik yok, log dosyasına yaz
            \Log::info("There is no new song.");
        }
    }
}
