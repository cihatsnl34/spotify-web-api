<?php

namespace App\Services;

use App\Models\Genre;
use App\Models\Singer;
use App\Models\Song;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class SpotifyService
{

    public function getTopArtists($limit = 20, $offset = 0, $accessToken)
    {
        $newSong = 0;
        if (!$accessToken) {
            return response()->json([
                'message' => 'AccessToken bos gelemez.'
            ], 401);
        }

        $artists = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://api.spotify.com/v1/me/top/artists", [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        $artistInfo = [];
        if (isset($artists['error'])) {
            return response()->json([
                'data' => $artists,
            ], 401);
        }
        // Burada elde edilen verileri işleyebilirsiniz.
        foreach ($artists['items'] as $key => $artist) {
            $artistInfo[$key]['id'] = $artist['id'];
            foreach ($artist['genres'] as $genresValue) {
                $artistInfo[$key]['genres'][] = $genresValue;
            }
            $artistInfo[$key]['name'] = $artist['name'];
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://api.spotify.com/v1/artists/" . $artist['id'] . "/top-tracks?market=TR");
            foreach ($response->json() as $value) {
                foreach ($value as $childvalue) {
                    $artistInfo[$key]['song'][] = $childvalue['name'];
                }
            }
        }
        try {
            foreach ($artistInfo as $artistInfoValue) {
                $oldSinger = Singer::where('name', $artistInfoValue['name'])->first();
                if (!$oldSinger) {
                    $singer = new Singer();
                    $singer->id = Str::uuid();
                    $singer->name = $artistInfoValue['name'];
                    $singer->save();
                }
                foreach ($artistInfoValue['genres'] as $genressValue) {
                    $oldGenre = Genre::where('name', $genressValue)->first();
                    if (!$oldGenre) {
                        $genres = new Genre();
                        $genres->id = Str::uuid();
                        $genres->name = $genressValue;
                        $genres->save();
                    }
                }
                foreach ($artistInfoValue['song'] as $songValue) {
                    if (!$oldSinger) {
                        $newSong++;
                        $song = new Song();
                        $song->id = Str::uuid();
                        $song->name = $songValue;
                        $song->singer_id = $singer->id;
                        $song->genre = $genres->name;
                        $song->save();
                    }
                }
            }
            return response()->json([
                'savedNewCount' => $newSong,
            ], 200);
        } catch (\Exception $e) {
            return $e;
        }
    }
}
