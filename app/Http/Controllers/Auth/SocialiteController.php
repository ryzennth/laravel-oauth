<?php
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        $user = User::updateOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName(),
                'profile_photo_path' => $this->storeAvatar($socialUser->getAvatar()),
            ]
        );

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

    private function storeAvatar($avatarUrl)
    {
        if (!$avatarUrl) {
            return null;
        }

        $contents = file_get_contents($avatarUrl);
        $filename = 'profile_photos/' . uniqid() . '.jpg';
        Storage::disk('public')->put($filename, $contents);

        return $filename;
    }
}