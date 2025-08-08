@extends('layouts.admin')

@section('title', 'Gerenciar Contas Demo')

@section('content')
<div class="admin-container">
    <div class="admin-header-content">
        <h1 class="admin-title">🎮 Gerenciar Contas Demo</h1>
        <p class="admin-subtitle">Controle as contas de demonstração dos usuários</p>
    </div>

    <!-- Filtros -->
    <div class="admin-filters">
        <form method="GET" action="{{ route('admin.demo-accounts') }}" class="filter-form">
            <div class="filter-group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Buscar por nome, email ou telefone..." class="filter-input">
                <select name="demo_filter" class="filter-select">
                    <option value="">Todos os usuários</option>
                    <option value="1" {{ request('demo_filter') == '1' ? 'selected' : '' }}>Apenas contas demo</option>
                    <option value="0" {{ request('demo_filter') == '0' ? 'selected' : '' }}>Apenas contas reais</option>
                </select>
                <button type="submit" class="filter-btn">🔍 Filtrar</button>
                <a href="{{ route('admin.demo-accounts') }}" class="filter-btn filter-btn-clear">🗑️ Limpar</a>
            </div>
        </form>
    </div>

    <!-- Tabela de usuários -->
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>👤 Usuário</th>
                    <th>📧 Email</th>
                    <th>💰 Saldo</th>
                    <th>🎮 Conta Demo</th>
                    <th>📅 Cadastro</th>
                    <th>⚙️ Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="user-info">
                            <strong>{{ $user->name }}</strong>
                            @if($user->demo)
                                <span class="demo-badge">DEMO</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="balance">R$ {{ number_format($user->wallet->balance ?? 0, 2, ',', '.') }}</span>
                    </td>
                    <td>
                        <div class="toggle-container">
                            <label class="toggle-switch">
                                <input type="checkbox" 
                                       class="demo-toggle" 
                                       data-user-id="{{ $user->id }}"
                                       data-user-name="{{ $user->name }}"
                                       {{ $user->demo ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="action-buttons">
                            @if($user->demo)
                                <button class="btn-action btn-balance" 
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-current-balance="{{ $user->wallet->balance ?? 0 }}"
                                        title="Adicionar saldo demo">
                                    💰 Saldo
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="no-data">
                        <div class="no-data-content">
                            <span class="no-data-icon">🔍</span>
                            <p>Nenhum usuário encontrado</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    @if($users->hasPages())
    <div class="admin-pagination">
        {{ $users->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Modal de Confirmação -->
<div class="modal fade modern-modal" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">⚠️ Confirmação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmButton">✅ Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Adicionar Saldo -->
<div class="modal fade modern-modal" id="balanceModal" tabindex="-1" aria-labelledby="balanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="balanceModalLabel">💰 Adicionar Saldo Demo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="balanceForm">
                    <div class="mb-3">
                        <label for="balanceAmount" class="form-label">Valor (R$)</label>
                        <input type="number" class="form-control" id="balanceAmount" step="0.01" min="0.01" required>
                    </div>
                    <p class="text-muted">Usuário: <span id="balanceUserName"></span></p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancelar</button>
                <button type="button" class="btn btn-success" id="addBalanceButton">💰 Atualizar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Estilos específicos da página de demo accounts */
     
    .admin-filters {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(0, 255, 135, 0.2);
        margin-bottom: 20px;
    }
    
    .filter-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .filter-group {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .filter-input, .filter-select {
        padding: 10px 15px;
        border: 2px solid rgba(0, 255, 135, 0.3);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        min-width: 200px;
        background: rgba(0, 0, 0, 0.6);
        color: #ffffff;
        backdrop-filter: blur(5px);
    }
    
    .filter-input:focus, .filter-select:focus {
        outline: none;
        border-color: #00ff87;
        box-shadow: 0 0 0 3px rgba(0, 255, 135, 0.2);
        background: rgba(0, 0, 0, 0.8);
    }
    
    .filter-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    
    .filter-select option {
        background: #1a1a2e;
        color: #ffffff;
    }
    
    .filter-btn {
        padding: 10px 20px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
    }
    
    .filter-btn-clear {
        background: linear-gradient(135deg, #6b7280, #4b5563);
    }
    
    .filter-btn-clear:hover {
        box-shadow: 0 5px 15px rgba(107, 114, 128, 0.3);
    }
    
    .admin-table-container {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(0, 255, 135, 0.2);
    }
    
    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .admin-table th {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.7));
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #00ff87;
        border-bottom: 2px solid rgba(0, 255, 135, 0.3);
    }
    
    .admin-table td {
        padding: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        vertical-align: middle;
        color: #ffffff;
    }
    
    .admin-table tr:hover {
        background-color: rgba(0, 255, 135, 0.1);
    }
    
    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .demo-badge {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
    }
    
    .balance {
        font-weight: 600;
        color: #059669;
    }
    
    .toggle-container {
        display: flex;
        justify-content: center;
    }
    
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }
    
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }
    
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .toggle-slider {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    
    input:checked + .toggle-slider:before {
        transform: translateX(26px);
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }
    
    .btn-action {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-balance {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .btn-balance:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
    }
    
    .no-data {
        text-align: center;
        padding: 40px;
    }
    
    .no-data-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        color: rgba(255, 255, 255, 0.7);
    }
    
    .no-data-icon {
        font-size: 48px;
        opacity: 0.5;
    }
    
    .admin-pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }
    
    /* Modal Styles */
    .modern-modal .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-width: 500px;
        margin: 0 auto;
    }
    
    .modern-modal .modal-header {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 20px 25px;
        border: none;
    }
    
    .modern-modal .modal-title {
        font-weight: 600;
        font-size: 18px;
    }
    
    .modern-modal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }
    
    .modern-modal .modal-body {
        padding: 25px;
        font-size: 16px;
        line-height: 1.6;
        color: #374151;
    }
    
    .modern-modal .modal-footer {
        border: none;
        padding: 20px 25px;
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        display: flex;
        justify-content: center;
        gap: 15px;
        box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.2);
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0 0 15px 15px;
    }
    
    .modern-modal .modal-footer .btn {
        min-width: 120px;
        padding: 12px 24px;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .modern-modal .modal-footer .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .modern-modal .modal-footer .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }
    
    .modern-modal .modal-footer .btn-secondary {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        border: none;
    }
    
    .modern-modal .modal-footer .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .filter-group {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-input, .filter-select {
            min-width: auto;
            width: 100%;
        }
        
        .admin-table-container {
            overflow-x: auto;
        }
        
        .admin-table {
            min-width: 800px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing modals...');
        
        // Verificar se Bootstrap está disponível
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap não está carregado!');
            return;
        }
        
        // Inicializar modais
        const confirmModalElement = document.getElementById('confirmModal');
        const balanceModalElement = document.getElementById('balanceModal');
        
        if (!confirmModalElement) {
            console.error('Modal de confirmação não encontrado!');
            return;
        }
        
        if (!balanceModalElement) {
            console.error('Modal de saldo não encontrado!');
            return;
        }
        
        console.log('Modais encontrados, inicializando...');
        
        // Garantir que os modais estejam ocultos inicialmente
        confirmModalElement.style.display = 'none';
        confirmModalElement.classList.remove('show');
        balanceModalElement.style.display = 'none';
        balanceModalElement.classList.remove('show');
        
        const confirmModal = new bootstrap.Modal(confirmModalElement, {
            backdrop: 'static',
            keyboard: false,
            show: false
        });
        
        const balanceModal = new bootstrap.Modal(balanceModalElement, {
            backdrop: 'static',
            keyboard: false
        });
        
        console.log('Modais inicializados com sucesso!');
        
        let currentToggle = null;
        let currentBalanceUserId = null;
        
        // Toggle demo status
        document.querySelectorAll('.demo-toggle').forEach(toggle => {
            toggle.addEventListener('change', function(e) {
                e.preventDefault();
                
                currentToggle = this;
                const userId = this.dataset.userId;
                const userName = this.dataset.userName;
                const isDemo = this.checked;
                
                const message = isDemo 
                    ? `Tem certeza que deseja converter a conta de <strong>${userName}</strong> para DEMO?`
                    : `Tem certeza que deseja converter a conta de <strong>${userName}</strong> para REAL?`;
                
                document.getElementById('confirmMessage').innerHTML = message;
                confirmModal.show();
            });
        });
        
        // Confirmar alteração
        document.getElementById('confirmButton').addEventListener('click', function() {
            if (currentToggle) {
                const userId = currentToggle.dataset.userId;
                const isDemo = currentToggle.checked;
                
                // Fazer requisição
                fetch(`/admin/demo-accounts/${userId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        demo: isDemo
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ Status alterado com sucesso!', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification('❌ Erro ao alterar status: ' + (data.message || 'Erro desconhecido'), 'error');
                        currentToggle.checked = !isDemo; // Reverter o toggle
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('❌ Erro ao alterar status', 'error');
                    currentToggle.checked = !isDemo; // Reverter o toggle
                });
                
                confirmModal.hide();
                currentToggle = null;
            }
        });
        
        // Cancelar alteração
        document.querySelector('#confirmModal [data-bs-dismiss="modal"]').addEventListener('click', function() {
            if (currentToggle) {
                currentToggle.checked = !currentToggle.checked; // Reverter o toggle
                currentToggle = null;
            }
            confirmModal.hide();
        });
        
        // Adicionar saldo
        document.querySelectorAll('.btn-balance').forEach(btn => {
            btn.addEventListener('click', function() {
                currentBalanceUserId = this.dataset.userId;
                const userName = this.dataset.userName;
                const currentBalance = this.dataset.currentBalance || '0';
                
                document.getElementById('balanceUserName').textContent = userName;
                document.getElementById('balanceAmount').value = currentBalance;
                balanceModal.show();
            });
        });
        
        // Confirmar adição de saldo
        document.getElementById('addBalanceButton').addEventListener('click', function() {
            const amount = document.getElementById('balanceAmount').value;
            
            if (!amount || amount <= 0) {
                showNotification('❌ Por favor, insira um valor válido', 'error');
                return;
            }
            
            if (currentBalanceUserId) {
                fetch(`/admin/demo-accounts/${currentBalanceUserId}/add-balance`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        amount: parseFloat(amount)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ Saldo adicionado com sucesso!', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification('❌ Erro ao adicionar saldo: ' + (data.message || 'Erro desconhecido'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('❌ Erro ao adicionar saldo', 'error');
                });
                
                balanceModal.hide();
                currentBalanceUserId = null;
            }
        });
        
        // Função para mostrar notificações
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = message;
            
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                z-index: 10000;
                animation: slideIn 0.3s ease;
                max-width: 400px;
                word-wrap: break-word;
            `;
            
            if (type === 'success') {
                notification.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            } else {
                notification.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
            }
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
        
        // Adicionar animações CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endpush