<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PixController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\AffiliateManagerController;

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
    return redirect()->route('game.index');
});

// Rotas do jogo (públicas - qualquer um pode ver, mas só logados podem jogar)
Route::get('/game', [GameController::class, 'index'])->name('game.index');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    // Jogo
    Route::post('/game/play', [GameController::class, 'play'])->name('game.play');
    Route::post('/game/finish', [GameController::class, 'finish'])->name('game.finish'); // NOVA ROTA
    Route::get('/game/history', [GameController::class, 'history'])->name('game.history');

    // Perfil do usuário (se precisar)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas para gateway
Route::middleware('auth')->prefix('pix')->group(function () {
    Route::post('/deposit', [PixController::class, 'deposit'])->name('pix.deposit');
    Route::post('/withdrawal', [PixController::class, 'withdrawal'])->name('pix.withdrawal');
});
Route::post('/pix/callback', [PixController::class, 'callback'])->name('pix.callback');
Route::post('/withdraw/callback', [App\Http\Controllers\PixController::class, 'withdrawCallback'])
    ->name('withdraw.callback');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas para o gerenciador de afiliados
Route::middleware('auth')->group(function () {
    Route::get('/afiliate_manager/UmFzcGFkaW5oYQ==', [AffiliateManagerController::class, 'index'])->name('affiliate.manager');
    Route::post('/affiliate_manager/{id}/commission', [AffiliateManagerController::class, 'updateCommissionRate'])->name('affiliate.update.commission');
    Route::post('/affiliate_manager/{id}/status', [AffiliateManagerController::class, 'updateStatus'])->name('affiliate.update.status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/ref/{affiliateCode}', [AffiliateController::class, 'track'])
    ->name('affiliate.track');

// Rotas protegidas para afiliados
Route::middleware('auth')->group(function () {
    Route::get('/affiliate/dashboard', [AffiliateController::class, 'dashboard'])
        ->name('affiliate.dashboard');
});

// Para admin - pagar comissões
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/admin/affiliate/pay-commissions', [AffiliateController::class, 'payCommissions'])
        ->name('admin.affiliate.pay');
});


require __DIR__ . '/auth.php';
