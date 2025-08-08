<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemoAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('wallet');
        
        // Filtro por busca (nome, email ou telefone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        // Filtro por tipo de conta (demo ou real)
        if ($request->filled('demo_filter')) {
            $query->where('demo', $request->demo_filter);
        }
        
        // Paginação
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.demo-accounts', compact('users'));
    }
    
    public function toggleDemo(Request $request, User $user)
    {
        try {
            $user->demo = $request->boolean('demo');
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => $user->demo ? 'Conta marcada como demo' : 'Conta removida da demo',
                'demo' => $user->demo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar conta: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function search(Request $request)
    {
        $email = $request->get('email');
        
        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Email é obrigatório'
            ]);
        }
        
        $users = User::where('email', 'like', '%' . $email . '%')
                    ->select('id', 'name', 'email', 'demo', 'created_at')
                    ->limit(10)
                    ->get();
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
    
    public function addBalance(Request $request, User $user)
    {
        // Verificar se a conta é demo
        if (!$user->demo) {
            return response()->json([
                'success' => false,
                'message' => 'Só é possível adicionar saldo em contas demo'
            ], 403);
        }
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:10000'
        ]);
        
        try {
            DB::beginTransaction();
            
            $amount = $request->amount;
            
            // Buscar ou criar carteira do usuário
            $wallet = $user->wallet;
            if (!$wallet) {
                $wallet = $user->wallet()->create([
                    'balance' => 0,
                    'can_withdraw' => true,
                    'rollover_amount' => 0,
                    'rollover_required' => 0
                ]);
            }
            
            // Definir saldo
            $wallet->balance = $amount;
            $wallet->save();
            
            // Registrar transação
            $user->transactions()->create([
                'type' => 'deposit',
                'amount' => $amount,
                'description' => 'Definição de saldo demo pelo admin',
                'status' => 'completed'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Saldo definido com sucesso',
                'new_balance' => $wallet->balance
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar saldo: ' . $e->getMessage()
            ], 500);
        }
    }
}