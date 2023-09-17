<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Singer;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\SpotifyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SpotifyController extends Controller
{
    protected $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    public function getTopArtists(Request $request)
    {
        $rules = [
            'limit' => 'required|integer',
            'offset' => 'required|integer',
            'token' => 'required|string'
        ];
        $artistInfo = [];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();

            return response()->json(array('errors' => $errors));
        }

        $artists = $this->spotifyService->getTopArtists($request->limit, $request->offset, $request->token);

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
                'Authorization' => 'Bearer ' . $request->token,
            ])->get("https://api.spotify.com/v1/artists/" . $artist['id'] . "/top-tracks?market=TR");
            foreach ($response->json() as $value) {
                foreach ($value as $childvalue) {
                    $artistInfo[$key]['song'][] = $childvalue['name'];
                }
            }
        }

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
                $song = new Song();
                $song->id = Str::uuid();
                $song->name = $songValue;
                $song->singer_id = $singer->id;
                $song->genre = $genres->name;
                $song->save();
            }
        }
        return response()->json([
            'data' => 'saved',
        ], 200);
    }
    public function getArtistTrack(Request $request)
    {
        $rules = [
            'name' => 'required|string'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();

            return response()->json(array('errors' => $errors));
        }
        $singer = Singer::where('name', $request->name)->first();
        $data = $singer->songs()->get('name');
        return response()->json([
            'data' => $data,
        ], 200);
    }
    public function getGenresTrack(Request $request)
    {
        $rules = [
            'genre' => 'required|string'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();

            return response()->json(array('errors' => $errors));
        }
        $data = Song::where('genre', $request->genre)->get('name');
        return response()->json([
            'data' => $data,
        ], 200);
    }
}
