<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private function resourceResponse($resource = null, $profile = null, string $message = 'نجح', int $code = 200)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $resource && $profile ? new $resource($profile) : null,
        ], $code);
    }

    private function errorResponse(string $message = 'خطأ', int $code = 403)
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
        ], $code);
    }



    private function uploadFile(Request $request, string $field, string $folder): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        return $request->file($field)->store($folder, 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    // ----------------- Controller Actions --------------------

    public function show(Request $request, ?User $user = null)
    {
        $targetUserId = $user?->id ?? auth()->id();

        $profile = Profile::with('user')
            ->where('user_id', $targetUserId)
            ->first();

        if (! $profile) {

            return $this->resourceResponse(
                ProfileResource::class,
                new Profile(['user_id' => $targetUserId]),
                'لم يتم إنشاء الملف الشخصي بعد',
                200
            );
        }

        return $this->resourceResponse(
            ProfileResource::class,
            $profile,
            'تم جلب الملف الشخصي بنجاح',
            200
        );
    }

    public function update(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();

        $profile = Profile::where('user_id', $user->id)->first();


        if ($request->hasFile('cv')) {
            $this->deleteFile($profile?->cv);
            $data['cv'] = $this->uploadFile($request, 'cv', 'cvs');
        }


        if ($request->hasFile('avatar')) {
            $this->deleteFile($user->avatar);
            $data['avatar'] = $this->uploadFile($request, 'avatar', 'avatars');
        }


        $user->update([
            'user_name' => $data['user_name'] ?? $user->user_name,
            'avatar'    => $data['avatar']    ?? $user->avatar,
        ]);

        // تحديث/إنشاء بيانات البروفيل (جدول profiles)
        $profile = Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'bio'      => $data['bio']      ?? $profile?->bio,
                'phone'    => $data['phone']    ?? $profile?->phone,
                'location' => $data['location'] ?? $profile?->location,
                'skill'    => $data['skill']    ?? $profile?->skill,
                'github'   => $data['github']   ?? $profile?->github,
                'gmail'    => $data['gmail']    ?? $profile?->gmail,
                'domain'   => $data['domain']   ?? $profile?->domain,
                'cv'       => $data['cv']       ?? $profile?->cv,
                'linkedin' => $data['linkedin'] ?? $profile?->linkedin,
            ]
        );

        $profile->load('user');

        return $this->resourceResponse(
            ProfileResource::class,
            $profile,
            'تم تحديث الملف الشخصي بنجاح',
            200
        );
    }
}
