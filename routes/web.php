<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PixController;
use App\Http\Controllers\AffiliateController;

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

// =====================================
// ENCONTRAR SEU USUÁRIO MANUAL ESPECÍFICO
// =====================================

Route::get('/find-your-user', function() {
    
    try {
        // Buscar TODOS os usuários ordenados por criação
        $allUsers = DB::table('users')
            ->orderBy('created_at', 'asc')
            ->get(['id', 'name', 'email', 'referred_by_code', 'created_at'])
            ->map(function($user, $index) {
                return [
                    'ordem' => $index + 1,
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'referred_by_code' => $user->referred_by_code ?: '❌ Nenhum',
                    'created_at' => $user->created_at,
                    'tipo' => strpos($user->email, '@example.com') !== false ? '🤖 Simulação' : '👤 Manual',
                    'status_afiliado' => $user->referred_by_code ? '✅ TEM referência' : '❌ SEM referência'
                ];
            });
        
        // Separar por tipo
        $manualUsers = $allUsers->filter(function($user) {
            return $user['tipo'] === '👤 Manual';
        })->values();
        
        $simulationUsers = $allUsers->filter(function($user) {
            return $user['tipo'] === '🤖 Simulação';
        })->values();
        
        // Identificar o problema
        $manualUsersWithoutRef = $manualUsers->filter(function($user) {
            return $user['referred_by_code'] === '❌ Nenhum';
        });
        
        return [
            '👤 SEUS usuários (criados manualmente):' => $manualUsers,
            '🤖 Usuários de teste/simulação:' => $simulationUsers,
            '❌ PROBLEMA IDENTIFICADO:' => [
                'usuarios_manuais_sem_referencia' => $manualUsersWithoutRef->count(),
                'usuarios_manuais_com_referencia' => $manualUsers->where('referred_by_code', '!=', '❌ Nenhum')->count(),
                'total_usuarios_manuais' => $manualUsers->count()
            ],
            '🔍 Análise do Problema:' => [
                'sistema_funciona' => 'Simulações passam ✅',
                'problema_real' => $manualUsersWithoutRef->count() > 0 ? 'Teste manual falha ❌' : 'Teste manual funciona ✅',
                'causa_provavel' => $manualUsersWithoutRef->count() > 0 ? 'Rota /ref/{code} não funciona ou cookie não salva' : 'Sistema 100% funcional'
            ],
            '🎯 Usuário do seu teste manual:' => $manualUsersWithoutRef->first() ? [
                'nome' => $manualUsersWithoutRef->first()['name'],
                'email' => $manualUsersWithoutRef->first()['email'],
                'problema' => 'NÃO tem referred_by_code - o link de afiliado não funcionou',
                'solucao' => 'Precisamos corrigir a rota /ref ou o processo de captura do cookie'
            ] : 'Todos os usuários manuais têm referência ✅'
        ];
        
    } catch (\Exception $e) {
        return [
            'erro' => $e->getMessage(),
            'linha' => $e->getLine()
        ];
    }
});

// =====================================
// VERIFICAR SE A ROTA /ref REALMENTE EXISTE
// =====================================

Route::get('/verify-ref-route', function() {
    
    try {
        // Verificar diretamente se conseguimos instanciar o controller
        $controllerExists = class_exists(\App\Http\Controllers\AffiliateController::class);
        $methodExists = method_exists(\App\Http\Controllers\AffiliateController::class, 'track');
        
        // Tentar fazer uma chamada direta ao método track
        $testResult = null;
        if ($controllerExists && $methodExists) {
            try {
                $controller = new \App\Http\Controllers\AffiliateController();
                // Não podemos chamar diretamente sem Request, mas podemos verificar
                $testResult = 'Método existe e pode ser chamado';
            } catch (\Exception $e) {
                $testResult = 'Erro ao instanciar: ' . $e->getMessage();
            }
        }
        
        return [
            '🔍 Verificação da Rota /ref:' => [
                'controller_existe' => $controllerExists ? '✅ Sim' : '❌ Não',
                'method_track_existe' => $methodExists ? '✅ Sim' : '❌ Não',
                'teste_instancia' => $testResult
            ],
            '📋 Verificar no Terminal:' => [
                'comando' => 'php artisan route:list | grep ref',
                'deve_mostrar' => 'GET|HEAD ref/{affiliateCode} ......... AffiliateController@track'
            ],
            '🧪 Teste Manual da Rota:' => [
                'link_direto' => url('/ref/G06L05UE'),
                'o_que_deve_acontecer' => 'Redirecionar para /game com mensagem de sucesso',
                'se_der_404' => 'A rota NÃO existe no routes/web.php',
                'se_der_erro_500' => 'A rota existe mas o controller tem problema'
            ],
            '⚠️ Possíveis Problemas:' => [
                '1' => 'Rota não adicionada no routes/web.php',
                '2' => 'Controller não existe ou tem erro',
                '3' => 'Cache de rotas não atualizado',
                '4' => 'Import do controller errado no routes/web.php'
            ]
        ];
        
    } catch (\Exception $e) {
        return [
            'erro' => $e->getMessage(),
            'linha' => $e->getLine()
        ];
    }
});

// =====================================
// TESTAR A ROTA /ref DIRETAMENTE
// =====================================

Route::get('/test-ref-route-direct/{code}', function($code) {
    
    try {
        // Simular exatamente o que a rota /ref/{code} deveria fazer
        $affiliate = DB::table('affiliates')
            ->where('affiliate_code', $code)
            ->where('status', 'active')
            ->first();

        $result = [
            'codigo_testado' => $code,
            'afiliado_encontrado' => $affiliate ? '✅ Sim' : '❌ Não'
        ];
        
        if ($affiliate) {
            // Fazer exatamente o que o AffiliateController::track faz
            session(['affiliate_code' => $code]);
            cookie()->queue('affiliate_code', $code, 60 * 24 * 30);
            
            $result['acoes_executadas'] = [
                'session_salva' => session('affiliate_code'),
                'cookie_configurado' => '✅ Sim',
                'affiliate_data' => [
                    'id' => $affiliate->id,
                    'user_id' => $affiliate->user_id,
                    'status' => $affiliate->status
                ]
            ];
            
            $result['proximos_passos'] = [
                '1' => 'Verifique o cookie: ' . url('/decrypt-affiliate-cookie'),
                '2' => 'Registre uma conta: ' . url('/register'),  
                '3' => 'Verifique se funcionou: ' . url('/find-your-user')
            ];
        } else {
            $result['erro'] = 'Código de afiliado não encontrado';
            $result['codigos_validos'] = DB::table('affiliates')
                ->where('status', 'active')
                ->pluck('affiliate_code');
        }
        
        return $result;
        
    } catch (\Exception $e) {
        return [
            'erro' => $e->getMessage(),
            'linha' => $e->getLine()
        ];
    }
});

// =====================================
// INSTRUÇÕES PARA RESOLVER O PROBLEMA
// =====================================

Route::get('/fix-manual-test', function() {
    
    return [
        '🎯 PLANO PARA RESOLVER:' => [
            '1️⃣ Identificar o problema' => url('/find-your-user'),
            '2️⃣ Verificar rota /ref' => url('/verify-ref-route'),
            '3️⃣ Testar rota diretamente' => url('/test-ref-route-direct/G06L05UE'),
            '4️⃣ Fazer novo teste manual' => 'Seguir instruções abaixo'
        ],
        '🔧 Se a rota /ref não existir:' => [
            'adicionar_no_routes' => 'Route::get(\'/ref/{affiliateCode}\', [AffiliateController::class, \'track\'])->name(\'affiliate.track\');',
            'limpar_cache' => 'php artisan route:clear && php artisan config:clear',
            'verificar' => 'php artisan route:list | grep ref'
        ],
        '🧪 Novo Teste Manual (CORRETO):' => [
            '1' => 'Abrir aba anônima nova',
            '2' => 'Acessar: ' . url('/test-ref-route-direct/G06L05UE'),
            '3' => 'Verificar cookie: ' . url('/decrypt-affiliate-cookie'),
            '4' => 'Se cookie estiver OK, registrar conta nova',
            '5' => 'Verificar resultado: ' . url('/find-your-user')
        ],
        '⚠️ IMPORTANTE:' => [
            'nao_usar_simulacao' => 'Use emails reais, não @example.com',
            'mesma_aba' => 'Não feche a aba entre os passos 2-4',
            'verificar_cada_passo' => 'Confirme que cada passo funcionou antes do próximo'
        ]
    ];
});


require __DIR__ . '/auth.php';
