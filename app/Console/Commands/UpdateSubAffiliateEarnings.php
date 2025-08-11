<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Affiliate;
use Illuminate\Support\Facades\DB;

class UpdateSubAffiliateEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:sub-affiliate-earnings {--dry-run : Executar sem fazer alterações}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza a coluna pending_sub_affiliate_earnings baseado nos ganhos pendentes dos subafiliados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 MODO DRY-RUN - Nenhuma alteração será feita');
        }
        
        $this->info('Iniciando atualização dos ganhos pendentes de subafiliados...');
        
        // Buscar todos os afiliados que têm subafiliados
        $affiliatesWithSubAffiliates = Affiliate::whereHas('subAffiliates')
            ->with('subAffiliates')
            ->get();
            
        $this->info("Encontrados {$affiliatesWithSubAffiliates->count()} afiliados com subafiliados");
        
        $totalUpdated = 0;
        $totalAmount = 0;
        
        foreach ($affiliatesWithSubAffiliates as $affiliate) {
            // Calcular o total de ganhos pendentes dos subafiliados
            $totalSubAffiliatePendingEarnings = $affiliate->subAffiliates->sum('pending_earnings');
            
            // Calcular a comissão do pai (20% dos ganhos pendentes dos subafiliados)
            $parentCommission = ($totalSubAffiliatePendingEarnings * $affiliate->sub_affiliate_commission_rate) / 100;
            
            $this->line("\n📊 Afiliado: {$affiliate->user->name} (ID: {$affiliate->id})");
            $this->line("   Subafiliados: {$affiliate->subAffiliates->count()}");
            $this->line("   Taxa de comissão: {$affiliate->sub_affiliate_commission_rate}%");
            $this->line("   Total pendente dos subafiliados: R$ " . number_format($totalSubAffiliatePendingEarnings, 2, ',', '.'));
            $this->line("   Comissão calculada para o pai: R$ " . number_format($parentCommission, 2, ',', '.'));
            $this->line("   Valor atual em pending_sub_affiliate_earnings: R$ " . number_format($affiliate->pending_sub_affiliate_earnings, 2, ',', '.'));
            
            // Verificar se precisa atualizar
            if (abs($affiliate->pending_sub_affiliate_earnings - $parentCommission) > 0.01) {
                if (!$dryRun) {
                    $affiliate->pending_sub_affiliate_earnings = $parentCommission;
                    $affiliate->save();
                }
                
                $this->info("   ✅ Atualizado para: R$ " . number_format($parentCommission, 2, ',', '.'));
                $totalUpdated++;
                $totalAmount += $parentCommission;
            } else {
                $this->line("   ⏭️  Já está correto, não precisa atualizar");
            }
        }
        
        $this->info("\n📈 RESUMO:");
        $this->info("   Afiliados processados: {$affiliatesWithSubAffiliates->count()}");
        $this->info("   Afiliados atualizados: {$totalUpdated}");
        $this->info("   Valor total em comissões: R$ " . number_format($totalAmount, 2, ',', '.'));
        
        if ($dryRun) {
            $this->warn("\n⚠️  Este foi um DRY-RUN. Para aplicar as alterações, execute sem a opção --dry-run");
        } else {
            $this->info("\n✅ Atualização concluída com sucesso!");
        }
        
        return 0;
    }
}