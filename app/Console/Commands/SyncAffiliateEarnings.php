<?php

namespace App\Console\Commands;

use App\Models\Affiliate;
use App\Models\Commission;
use App\Models\Referral;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAffiliateEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate:sync-earnings {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza os valores de total_earnings e pending_earnings dos afiliados com os dados reais das comissões';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 Executando em modo dry-run (apenas visualização)');
        } else {
            $this->info('🔄 Sincronizando valores dos afiliados...');
        }

        $affiliates = Affiliate::all();
        $updated = 0;
        $errors = 0;

        foreach ($affiliates as $affiliate) {
            try {
                // Calcular valores reais das comissões
                $realPendingEarnings = $affiliate->commissions()
                    ->where('status', 'pending')
                    ->where('commission_amount', '>', 0)
                    ->sum('commission_amount');

                $realTotalEarnings = $affiliate->commissions()
                    ->where('status', 'paid')
                    ->where('commission_amount', '>', 0)
                    ->sum('commission_amount');

                // Verificar se há diferenças
                $pendingDiff = abs($affiliate->pending_earnings - $realPendingEarnings) > 0.01;
                $totalDiff = abs($affiliate->total_earnings - $realTotalEarnings) > 0.01;

                if ($pendingDiff || $totalDiff) {
                    $this->warn("Afiliado ID {$affiliate->id} ({$affiliate->user->name}):");
                    
                    if ($pendingDiff) {
                        $this->line("  Pendente: R$ {$affiliate->pending_earnings} → R$ {$realPendingEarnings}");
                    }
                    
                    if ($totalDiff) {
                        $this->line("  Total: R$ {$affiliate->total_earnings} → R$ {$realTotalEarnings}");
                    }

                    if (!$dryRun) {
                        $affiliate->update([
                            'pending_earnings' => $realPendingEarnings,
                            'total_earnings' => $realTotalEarnings
                        ]);
                    }
                    
                    $updated++;
                }

                // Sincronizar também os totais dos referrals
                foreach ($affiliate->referrals as $referral) {
                    $realTotalCommission = $referral->commissions()
                        ->where('commission_amount', '>', 0)
                        ->sum('commission_amount');

                    if (abs($referral->total_commission - $realTotalCommission) > 0.01) {
                        $this->line("  Referral ID {$referral->id}: R$ {$referral->total_commission} → R$ {$realTotalCommission}");
                        
                        if (!$dryRun) {
                            $referral->update(['total_commission' => $realTotalCommission]);
                        }
                    }
                }

            } catch (\Exception $e) {
                $this->error("Erro ao processar afiliado ID {$affiliate->id}: {$e->getMessage()}");
                $errors++;
            }
        }

        if ($dryRun) {
            $this->info("\n📊 Resumo (dry-run):");
            $this->info("   {$updated} afiliados precisam de sincronização");
            $this->info("   {$errors} erros encontrados");
            $this->info("\n💡 Execute sem --dry-run para aplicar as correções");
        } else {
            $this->info("\n✅ Sincronização concluída:");
            $this->info("   {$updated} afiliados atualizados");
            $this->info("   {$errors} erros encontrados");
        }

        return $errors > 0 ? 1 : 0;
    }
}
