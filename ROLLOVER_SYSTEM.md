# Sistema de Rollover - 100% do Depósito

Este documento explica como funciona o sistema de rollover implementado no RaspaKing, que exige que os usuários apostem 100% do valor depositado antes de poderem sacar.

## ⚠️ IMPORTANTE: Usuários Existentes vs Novos Usuários

### Usuários Existentes (Antes da Implementação)
- **NÃO são afetados pelo rollover**
- Podem continuar sacando normalmente
- O campo `can_withdraw` permanece como `true`
- Não precisam cumprir requisitos de rollover

### Novos Usuários (Após a Implementação)
- **São afetados pelo rollover a partir do primeiro depósito**
- Precisam apostar 100% do valor depositado para liberar saques
- O rollover é aplicado apenas quando fazem o primeiro depósito

## Como Funciona

### 1. Depósitos
- Quando um usuário faz um depósito via PIX, o valor é creditado no saldo
- O valor depositado é adicionado ao `rollover_requirement` (requisito de rollover)
- O sistema rastreia o total depositado em `total_deposited`

### 2. Apostas
- Cada aposta feita pelo usuário é registrada em `total_wagered`
- O valor apostado é adicionado ao `rollover_completed` até atingir o `rollover_requirement`
- O progresso é calculado como: `(rollover_completed / rollover_requirement) * 100`

### 3. Saques
- O usuário só pode sacar quando `rollover_completed >= rollover_requirement`
- Se tentar sacar antes, recebe uma mensagem informando quanto ainda precisa apostar
- A interface mostra o progresso do rollover em tempo real

## Campos Adicionados na Tabela `wallets`

- `total_deposited`: Soma total de todos os depósitos
- `total_wagered`: Soma total de todas as apostas
- `rollover_requirement`: Valor total que precisa ser apostado (100% dos depósitos)
- `rollover_completed`: Valor já apostado para o rollover
- `can_withdraw`: Boolean indicando se pode sacar

## Métodos Implementados no Model Wallet

### `addDeposit($amount)`
- Adiciona saldo
- Incrementa total depositado
- Incrementa requisito de rollover
- Atualiza status de saque

### `addWager($amount)`
- Incrementa total apostado
- Atualiza rollover completado
- Atualiza status de saque

### `checkCanWithdraw()`
- Verifica se rollover foi completado
- Retorna true/false

### `getRemainingRollover()`
- Calcula quanto ainda falta apostar
- Retorna valor em decimal

### `getRolloverPercentage()`
- Calcula percentual completado
- Retorna valor entre 0-100

## Interface do Usuário

### Indicadores Visuais
- Progresso do rollover mostrado no cabeçalho
- Botão de saque desabilitado quando rollover incompleto
- Mensagens informativas sobre quanto falta apostar

### Mensagens de Erro
- Saque bloqueado: "Você precisa apostar mais R$ X,XX para liberar o saque. Progresso: X.X%"
- Modal de saque: Alerta antes de abrir se rollover incompleto

## Exemplo de Funcionamento

1. **Usuário deposita R$ 100,00**
   - `balance`: R$ 100,00
   - `total_deposited`: R$ 100,00
   - `rollover_requirement`: R$ 100,00
   - `rollover_completed`: R$ 0,00
   - `can_withdraw`: false

2. **Usuário aposta R$ 50,00**
   - `balance`: R$ 50,00 (assumindo que perdeu)
   - `total_wagered`: R$ 50,00
   - `rollover_completed`: R$ 50,00
   - Progresso: 50%
   - `can_withdraw`: false

3. **Usuário aposta mais R$ 50,00**
   - `balance`: R$ 0,00 (assumindo que perdeu)
   - `total_wagered`: R$ 100,00
   - `rollover_completed`: R$ 100,00
   - Progresso: 100%
   - `can_withdraw`: true

4. **Usuário faz novo depósito de R$ 50,00**
   - `balance`: R$ 50,00
   - `total_deposited`: R$ 150,00
   - `rollover_requirement`: R$ 150,00
   - `rollover_completed`: R$ 100,00
   - Progresso: 66.7%
   - `can_withdraw`: false (precisa apostar mais R$ 50,00)

## Arquivos Modificados

1. **Migrations**: 
   - `2025_01_15_000000_add_rollover_fields_to_wallets_table.php` (adiciona campos de rollover)
   - `2025_01_15_000001_set_existing_users_can_withdraw.php` (protege usuários existentes)
2. **Models**: 
   - `app/Models/Wallet.php` (lógica de rollover)
   - `app/Models/User.php` (criação de carteira para novos usuários)
3. **Controllers**: 
   - `app/Http/Controllers/PixController.php`
   - `app/Http/Controllers/GameController.php`
4. **Views**: `resources/views/layouts/app.blade.php`

## Implementação Técnica para Usuários Existentes

### Proteção de Usuários Existentes
1. **Migration de Proteção**: A migration `2025_01_15_000001_set_existing_users_can_withdraw.php` define `can_withdraw = true` para todas as carteiras existentes
2. **Lógica no Model**: O método `addDeposit()` verifica se é o primeiro depósito antes de aplicar rollover
3. **Criação de Novos Usuários**: Novos usuários recebem `can_withdraw = true` inicialmente, mas isso muda no primeiro depósito

### Fluxo de Decisão
```
Usuário faz depósito:
├── É usuário existente (nunca teve rollover)?
│   ├── SIM: Não aplica rollover, mantém can_withdraw = true
│   └── NÃO: Continua para próxima verificação
└── É primeiro depósito OU já tem rollover ativo?
    ├── SIM: Aplica rollover, atualiza can_withdraw
    └── NÃO: Não aplica rollover
```

## Considerações de Segurança

- O sistema impede saques até que o rollover seja completado
- Validações tanto no frontend quanto no backend
- Transações de banco de dados para garantir consistência
- Rastreamento completo de depósitos e apostas
- Proteção total para usuários existentes

## Manutenção

- Para ajustar a porcentagem de rollover, modifique o método `addDeposit()` no model Wallet
- Para resetar rollover de um usuário, zere os campos `rollover_requirement` e `rollover_completed`
- Para verificar status de rollover via SQL:
  ```sql
  SELECT 
    user_id,
    balance,
    total_deposited,
    rollover_requirement,
    rollover_completed,
    (rollover_completed / rollover_requirement * 100) as percentage,
    can_withdraw
  FROM wallets 
  WHERE rollover_requirement > 0;
  ```