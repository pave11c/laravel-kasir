<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TransaksiSementaraController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});




// bagian lupa pas
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);

})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');


Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:5|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [($status)]]);
})->middleware('guest')->name('password.update');
// end bagian lupa pass





Route::get('/daftar', [AuthController::class, 'index']);
Route::post('/user/daftar', [AuthController::class, 'store'])->name('store');


Route::post('/postlogin', [AuthController::class, 'postlogin']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/login', function () {
    return Auth::check() ? redirect('/dashboard') : view('auth.login');
})->middleware('guest')->name('login');

Route::get('/forgot/password', [AuthController::class, 'forgotPw']);

Route::group(['middleware' => ['auth', 'ceklevel:admin']], function(){
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);

    Route::get('/admin/kategori', [KategoriController::class, 'index']);
    Route::post('/admin/kategori/store', [KategoriController::class, 'store']);
    Route::get('/admin/kategori/{id}/edit', [KategoriController::class, 'edit']);
    Route::put('/admin/kategori/{id}', [KategoriController::class, 'update']);
    Route::get('/admin/kategori/{id}', [KategoriController::class, 'destroy']);

    Route::get('/admin/satuan', [SatuanController::class, 'index']);
    Route::post('/admin/satuan/store', [SatuanController::class, 'store']);
    Route::get('/admin/satuan/{id}/edit', [SatuanController::class, 'edit']);
    Route::put('/admin/satuan/{id}', [SatuanController::class, 'update']);
    Route::get('/admin/satuan/{id}', [SatuanController::class, 'destroy']);

    Route::get('/admin/barang', [BarangController::class, 'index']);
    Route::post('/admin/barang/store', [BarangController::class, 'store']);
    Route::get('/admin/barang/{id}/edit', [BarangController::class, 'edit']);
    Route::get('/admin/barang/{id}/show', [BarangController::class, 'show']);
    Route::put('/admin/barang/{id}', [BarangController::class, 'update']);
    Route::get('/admin/barang/{id}', [BarangController::class, 'destroy']);
});

