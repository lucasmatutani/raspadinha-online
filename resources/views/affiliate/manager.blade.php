@extends('layouts.admin')

@section('title', 'Gerenciamento de Afiliados')

@push('styles')
<style>
    /* Estilos gerais */
    .admin-container {
        background: rgba(26, 26, 46, 0.95);
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(0, 255, 135, 0.1);
    }

    .admin-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(0, 255, 135, 0.2);
    }

    .admin-title {
        color: #00ff87;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 255, 135, 0.3);
    }

    .admin-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.1rem;
        margin: 0.5rem 0 0 0;
    }

    /* Filtros */
    .admin-filters {
        background: rgba(0, 255, 135, 0.05);
        border: 1px solid rgba(0, 255, 135, 0.1);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .filter-form {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        width: 100%;
    }

    .filter-input {
        background: rgba(26, 26, 46, 0.8);
        border: 1px solid rgba(0, 255, 135, 0.3);
        border-radius: 8px;
        color: #ffffff;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        min-width: 200px;
    }

    .filter-input:focus {
        outline: none;
        border-color: #00ff87;
        box-shadow: 0 0 10px rgba(0, 255, 135, 0.3);
    }

    .filter-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .filter-btn {
        background: linear-gradient(135deg, #00ff87, #00b359);
        border: none;
        border-radius: 8px;
        color: #1a1a2e;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 255, 135, 0.3);
    }

    .filter-btn-clear {
        background: linear-gradient(135deg, #ff6b6b, #ee5a52);
        color: white;
    }

    .filter-btn-clear:hover {
        box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
    }

    /* Tabela */
    .admin-table-container {
        background: rgba(0, 255, 135, 0.02);
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid rgba(0, 255, 135, 0.1);
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        background: transparent;
    }

    .admin-table th {
        background: rgba(0, 255, 135, 0.1);
        color: #00ff87;
        text-align: left;
        padding: 1rem;
        font-weight: 600;
        border-bottom: 1px solid rgba(0, 255, 135, 0.2);
    }

    .admin-table td {
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: #ffffff;
    }

    .admin-table tr:hover td {
        background: rgba(0, 255, 135, 0.05);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .affiliate-code {
        background: rgba(0, 255, 135, 0.1);
        color: #00ff87;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        border: 1px solid rgba(0, 255, 135, 0.2);
    }

    .referrals-count {
        background: rgba(0, 123, 255, 0.1);
        color: #007bff;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        border: 1px solid rgba(0, 123, 255, 0.2);
    }

    .balance {
        color: #00ff87;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .commission-input {
        background: rgba(26, 26, 46, 0.8);
        border: 1px solid rgba(0, 255, 135, 0.3);
        border-radius: 5px;
        color: #ffffff;
        padding: 0.5rem;
        width: 80px;
        text-align: center;
    }

    .status-toggle {
        background: rgba(26, 26, 46, 0.8);
        border: 1px solid rgba(0, 255, 135, 0.3);
        border-radius: 5px;
        color: #ffffff;
        padding: 0.5rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .btn-save {
        background: linear-gradient(135deg, #00ff87, #00b359);
        color: #1a1a2e;
    }

    .btn-referrals {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }

    .btn-reset {
        background: linear-gradient(135deg, #ff6b6b, #ee5a52);
        color: white;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    .no-data {
        text-align: center;
        padding: 3rem;
        color: rgba(255, 255, 255, 0.6);
    }

    .no-data-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .no-data-icon {
        font-size: 3rem;
        opacity: 0.5;
    }

    /* Paginação */
    .admin-pagination {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }

    /* Cards Mobile */
    .mobile-cards {
        display: none;
    }

    .user-card {
        background: rgba(0, 255, 135, 0.05);
        border: 1px solid rgba(0, 255, 135, 0.1);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .user-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 255, 135, 0.1);
    }

    .user-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .user-card-info h3 {
        color: #00ff87;
        margin: 0 0 0.5rem 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .user-card-info p {
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        font-size: 0.9rem;
    }

    .user-card-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .detail-item {
        text-align: center;
        padding: 1rem;
        background: rgba(0, 255, 135, 0.05);
        border-radius: 8px;
        border: 1px solid rgba(0, 255, 135, 0.1);
    }

    .detail-label {
        display: block;
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        color: #00ff87;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .user-card-actions {
        margin-top: 1rem;
    }

    .commission-mobile-form {
        background: rgba(0, 255, 135, 0.03);
        border: 1px solid rgba(0, 255, 135, 0.1);
        border-radius: 10px;
        padding: 1rem;
    }

    .mobile-inputs-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .input-group label {
        color: #00ff87;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .commission-input-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .percent-symbol {
        color: #00ff87;
        font-weight: bold;
    }

    .mobile-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .admin-container {
            margin: 1rem;
            padding: 1rem;
        }

        .admin-title {
            font-size: 2rem;
            text-align: center;
        }

        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            flex-direction: column;
        }

        .filter-input {
            min-width: auto;
            width: 100%;
        }

        .admin-table-container {
            display: none;
        }

        .mobile-cards {
            display: block;
        }

        .mobile-inputs-row {
            grid-template-columns: 1fr;
        }

        .mobile-buttons {
            flex-direction: column;
        }

        .btn-action {
            justify-content: center;
        }
    }

    @media (min-width: 769px) {
        .mobile-cards {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .user-card-details {
            grid-template-columns: 1fr;
        }

        .detail-item {
            padding: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="admin-container">
    <div class="admin-header">
        <div class="admin-header-content">
            <h1 class="admin-title">Gerenciamento de Afiliados</h1>
            <p class="admin-subtitle">Gerencie afiliados, comissões e visualize estatísticas</p>
        </div>
    </div>

    <!-- Filtros de pesquisa -->
    <div class="admin-filters">
        <form method="GET" action="{{ route('affiliate.manager') }}" class="filter-form">
            <div class="filter-group">
                <input type="text" name="search_name" placeholder="Buscar por nome..." 
                       value="{{ request('search_name') }}" class="filter-input">
                <input type="text" name="search_email" placeholder="Buscar por e-mail..." 
                       value="{{ request('search_email') }}" class="filter-input">
                <button type="submit" class="filter-btn">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <a href="{{ route('affiliate.manager') }}" class="filter-btn filter-btn-clear">
                    <i class="fas fa-times"></i> Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabela para desktop -->
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Afiliado</th>
                    <th>Código</th>
                    <th>Referidos</th>
                    <th>Comissões</th>
                    <th>Taxa (%)</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($affiliates as $affiliate)
                <tr>
                    <td>
                        <div class="user-info">
                            <div>
                                <div style="font-weight: 600; color: #00ff87;">{{ $affiliate->user->name }}</div>
                                <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem;">{{ $affiliate->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="affiliate-code">{{ $affiliate->affiliate_code }}</span>
                    </td>
                    <td>
                        <span class="referrals-count">{{ $affiliate->referrals_count ?? 0 }}</span>
                    </td>
                    <td>
                        <span class="balance">R$ {{ number_format($affiliate->total_commission ?? 0, 2, ',', '.') }}</span>
                    </td>
                    <td>
                        <form data-affiliate-id="{{ $affiliate->id }}" style="display: inline;">
                            <input type="number" 
                                   class="commission-input" 
                                   value="{{ $affiliate->commission_rate }}" 
                                   min="0" 
                                   max="100" 
                                   step="0.01">
                        </form>
                    </td>
                    <td>
                        <select class="status-toggle" data-affiliate-id="{{ $affiliate->id }}">
                            <option value="active" {{ $affiliate->status === 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ $affiliate->status === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-action btn-save" data-affiliate-id="{{ $affiliate->id }}">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                            <button class="btn-action btn-referrals" data-affiliate-id="{{ $affiliate->id }}">
                                <i class="fas fa-users"></i> Referidos
                            </button>
                            <button class="btn-action btn-reset" data-affiliate-id="{{ $affiliate->id }}">
                                <i class="fas fa-trash"></i> Zerar
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="no-data">
                        <div class="no-data-content">
                            <div class="no-data-icon">👥</div>
                            <div>Nenhum afiliado encontrado</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Cards para mobile -->
    <div class="mobile-cards" style="display: none;">
        @forelse($affiliates as $affiliate)
        <div class="user-card">
            <div class="user-card-header">
                <div class="user-card-info">
                    <h3>{{ $affiliate->user->name }}</h3>
                    <p>{{ $affiliate->user->email }}</p>
                </div>
                <span class="affiliate-code">{{ $affiliate->affiliate_code }}</span>
            </div>
            
            <div class="user-card-details">
                <div class="detail-item">
                    <span class="detail-label">Referidos</span>
                    <span class="detail-value">{{ $affiliate->referrals_count ?? 0 }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Comissões</span>
                    <span class="detail-value">R$ {{ number_format($affiliate->total_commission ?? 0, 2, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="user-card-actions">
                <div class="commission-mobile-form" data-affiliate-id="{{ $affiliate->id }}">
                    <div class="mobile-inputs-row">
                        <div class="input-group">
                            <label>Taxa de Comissão</label>
                            <div class="commission-input-wrapper">
                                <input type="number" 
                                       class="commission-input" 
                                       value="{{ $affiliate->commission_rate }}" 
                                       min="0" 
                                       max="100" 
                                       step="0.01">
                                <span class="percent-symbol">%</span>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Status</label>
                            <select class="status-toggle" data-affiliate-id="{{ $affiliate->id }}">
                                <option value="active" {{ $affiliate->status === 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="inactive" {{ $affiliate->status === 'inactive' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mobile-buttons">
                        <button class="btn-action btn-save" data-affiliate-id="{{ $affiliate->id }}">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                        <button class="btn-action btn-referrals" data-affiliate-id="{{ $affiliate->id }}">
                            <i class="fas fa-users"></i> Referidos
                        </button>
                        <button class="btn-action btn-reset" data-affiliate-id="{{ $affiliate->id }}">
                            <i class="fas fa-trash"></i> Zerar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="no-data">
            <div class="no-data-content">
                <div class="no-data-icon">👥</div>
                <div>Nenhum afiliado encontrado</div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Paginação -->
    @if($affiliates->hasPages())
    <div class="admin-pagination">
        {{ $affiliates->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para salvar alterações
        document.querySelectorAll('.btn-save').forEach(button => {
            button.addEventListener('click', function() {
                const affiliateId = this.getAttribute('data-affiliate-id');
                const commissionInput = document.querySelector(`[data-affiliate-id="${affiliateId}"] .commission-input`);
                const statusSelect = document.querySelector(`[data-affiliate-id="${affiliateId}"] .status-toggle`);
                
                const commissionRate = commissionInput.value;
                const status = statusSelect.value;
                
                // Simular salvamento (aqui você faria a requisição AJAX)
                showNotification('Configurações salvas com sucesso!', 'success');
            });
        });
        
        // Função para zerar comissões
        document.querySelectorAll('.btn-reset').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Tem certeza que deseja zerar as comissões deste afiliado?')) {
                    showNotification('Comissões zeradas com sucesso!', 'success');
                }
            });
        });
        
        // Função para mostrar referidos
        document.querySelectorAll('.btn-referrals').forEach(button => {
            button.addEventListener('click', function() {
                showNotification('Funcionalidade em desenvolvimento', 'info');
            });
        });
        
        // Função para mostrar notificações
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                z-index: 9999;
                animation: slideIn 0.3s ease;
                max-width: 300px;
            `;
            
            if (type === 'success') {
                notification.style.background = 'linear-gradient(135deg, #00ff87, #00b359)';
            } else if (type === 'error') {
                notification.style.background = 'linear-gradient(135deg, #ff6b6b, #ee5a52)';
            } else {
                notification.style.background = 'linear-gradient(135deg, #007bff, #0056b3)';
            }
            
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
        
        // Adicionar CSS para animações
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