<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SpotifyService
{

    public function getTopArtists($limit = 20, $offset = 0, $accessToken)
    {

        if (!$accessToken) {
            return response()->json([
                'message' => 'AccessToken bos gelemez.'
            ], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://api.spotify.com/v1/me/top/artists", [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        return $response->json();
    }
}
