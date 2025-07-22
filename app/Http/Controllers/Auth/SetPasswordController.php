<?php
// app/Http/Controllers/Auth/SetPasswordController.php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class SetPasswordController extends Controller
{
    public function create()
    {
        return inertia('Auth/SetPassword');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('dashboard')->with('status', 'Password berhasil diset!');
    }
}
