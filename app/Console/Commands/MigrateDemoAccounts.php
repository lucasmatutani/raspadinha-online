<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MigrateDemoAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra as contas demo do .env para o banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $demoAccounts = env('DEMO_ACOUNTS', '');
        
        if (empty($demoAccounts)) {
            $this->info('Nenhuma conta demo encontrada no .env');
            return;
        }
        
        $demoIds = explode(',', $demoAccounts);
        $demoIds = array_map('trim', $demoIds);
        $demoIds = array_filter($demoIds, 'is_numeric');
        
        if (empty($demoIds)) {
            $this->info('Nenhum ID válido encontrado');
            return;
        }
        
        $this->info('Migrando ' . count($demoIds) . ' contas demo...');
        
        $updated = 0;
        $notFound = 0;
        
        foreach ($demoIds as $userId) {
            $user = User::find($userId);
            
            if ($user) {
                $user->demo = true;
                $user->save();
                $updated++;
                $this->line("✓ Usuário {$user->name} ({$user->email}) marcado como demo");
            } else {
                $notFound++;
                $this->error("✗ Usuário com ID {$userId} não encontrado");
            }
        }
        
        $this->info("\nResumo:");
        $this->info("- {$updated} contas marcadas como demo");
        if ($notFound > 0) {
            $this->warn("- {$notFound} contas não encontradas");
        }
        
        $this->info("\nMigração concluída!");
    }
}