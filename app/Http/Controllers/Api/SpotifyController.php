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

    /** @SWG\Post(
     *     path="/api/top-artists",
     *     tags={"top-artists"},
     *     summary="top-artists",
     *     description="top-artiststop-artists",
     *     @SWG\Parameter(
     *          name="token",
     *          description="User token",
     *          required=true,
     *          type="string",
     *          in="header"
     *     ),
     * @SWG\Parameter(
     *          name="token",
     *          description="Spotify Token",
     *          required=true,
     *          type="string",
     *          in="query"
     *     ),
     *   @SWG\Parameter(
     *          name="limit",
     *          description="Limit",
     *          required=true,
     *          type="integer",
     *          in="query"
     *     ),
     *   @SWG\Parameter(
     *          name="offset",
     *          description="offset",
     *          required=true,
     *          type="integer",
     *          in="query"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="profile data",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="response",
     *                  type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *     )
     * )
     */
   /** @SWG\Post(
     *     path="/api/artist-all-track",
     *     tags={"artist-all-track"},
     *     summary="artist-all-track",
     *     description="artist-all-track",
     *     @SWG\Parameter(
     *          name="token",
     *          description="User token",
     *          required=true,
     *          type="string",
     *          in="header"
     *     ),
     * @SWG\Parameter(
     *          name="name",
     *          description="Singer Name",
     *          required=true,
     *          type="string",
     *          in="query"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="profile data",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="data",
     *                  type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *     )
     * )
     */

        /** @SWG\Post(
     *     path="/api/genres-all-track",
     *     tags={"genres-all-track"},
     *     summary="genres-all-track",
     *     description="genres-all-track",
     *     @SWG\Parameter(
     *          name="token",
     *          description="User token",
     *          required=true,
     *          type="string",
     *          in="header"
     *     ),
     * @SWG\Parameter(
     *          name="genre",
     *          description="Genre",
     *          required=true,
     *          type="string",
     *          in="query"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="profile data",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="data",
     *                  type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *     )
     * )
     */


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
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();

            return response()->json(array('errors' => $errors));
        }

        $response = $this->spotifyService->getTopArtists($request->limit, $request->offset, $request->token);
        return response()->json([
            'data' => $response,
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
