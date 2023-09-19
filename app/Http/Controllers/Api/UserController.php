<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    
    /** @SWG\Post(
     *     path="/api/storeProfilePhoto",
     *     tags={"Profil storeProfilePhoto"},
     *     summary="Profil storeProfilePhoto",
     *     description="Profil storeProfilePhoto",
     *     @SWG\Parameter(
     *          name="token",
     *          description="User token",
     *          required=true,
     *          type="string",
     *          in="header"
     *     ),
     * @SWG\Parameter(
     *          name="profile_photo",
     *          description="User profile_photo",
     *          required=true,
     *          type="file",
     *          in="query"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="profile data",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="Profil fotoğrafi başariyla yüklendi",
     *                  type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *          response=400,
     *          description="Yüklenecek bir dosya bulunamadi"
     *     )
     * )
     */

     /** @SWG\Post(
     *     path="/api/changeProfilePhoto",
     *     tags={"Profil changeProfilePhoto"},
     *     summary="Profil changeProfilePhoto",
     *     description="Profil changeProfilePhoto",
     *     @SWG\Parameter(
     *          name="token",
     *          description="User token",
     *          required=true,
     *          type="string",
     *          in="header"
     *     ),
     * @SWG\Parameter(
     *          name="profile_photo",
     *          description="User profile_photo",
     *          required=true,
     *          type="file",
     *          in="query"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="profile data",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="Profil fotoğrafi başariyla yüklendi",
     *                  type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *          response=400,
     *          description="Yüklenecek bir dosya bulunamadi"
     *     )
     * )
     */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadProfilePhoto(Request $request)
    {
        // Doğrulama işlemlerini burada ekleyin, örneğin kullanıcı oturum açmış mı kontrol edin.

        $user = $request->user(); // Oturum açık olan kullanıcıyı alın

        // Yüklenen dosyayı kontrol edin ve saklayın
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('public/profile-photos');

            // Eğer kullanıcının zaten bir profil fotoğrafı varsa, eski fotoğrafı silin
            if ($user->profile_photo) {
                Storage::delete($user->profile_photo);
            }

            // Veritabanında kullanıcıya ait profil fotoğrafını güncelleyin
            $user->profile_photo = $path;
            $user->save();

            return response()->json(['message' => 'Profil fotoğrafi başariyla yüklendi'], 200);
        }

        return response()->json(['message' => 'Yüklenecek bir dosya bulunamadi'], 400);
    }
    public function updateProfilePhoto(Request $request)
    {
        // Doğrulama işlemlerini burada ekleyin, örneğin kullanıcı oturum açmış mı kontrol edin.

        $user = $request->user(); // Oturum açık olan kullanıcıyı alın

        // Yüklenen dosyayı kontrol edin ve saklayın
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('public/profile-photos');

            // Eğer kullanıcının zaten bir profil fotoğrafı varsa, eski fotoğrafı silin
            if ($user->profile_photo) {
                Storage::delete($user->profile_photo);
            }

            // Veritabanında kullanıcıya ait profil fotoğrafını güncelleyin
            $user->profile_photo = $path;
            $user->save();

            return response()->json(['message' => 'Profil fotoğrafı başarıyla güncellendi'], 200);
        }

        return response()->json(['message' => 'Yüklenecek bir dosya bulunamadi'], 400);
    }
}
