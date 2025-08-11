@extends('layouts.admin')

@section('title', '👑 Painel de Subafiliados - Raspadinha Online')

@section('content')
<div class="affiliate-manager-container">
    <div class="header-section">
        <div class="header-content">
            <div class="header-title">
                <h1>👥 Painel de Subafiliados</h1>
                <p>Gerencie afiliados que possuem subafiliados ativos</p>
            </div>
        </div>
    </div>

    <!-- Filtros de Busca -->
    <div class="filters-section">
        <form method="GET" action="{{ route('sub-affiliates.index') }}" class="filters-form">
            <div class="filter-group">
                <input type="text" 
                       name="search_name" 
                       placeholder="🔍 Buscar por nome..." 
                       value="{{ request('search_name') }}"
                       class="filter-input">
            </div>
            <div class="filter-group">
                <input type="text" 
                       name="search_email" 
                       placeholder="📧 Buscar por e-mail..." 
                       value="{{ request('search_email') }}"
                       class="filter-input">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">🔍 Buscar</button>
                <a href="{{ route('sub-affiliates.index') }}" class="btn-clear">🗑️ Limpar</a>
            </div>
        </form>
    </div>

    <!-- Estatísticas -->
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-number">{{ $affiliates->total() }}</div>
            <div class="stat-label">Afiliados com Subafiliados</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $affiliates->sum('total_sub_affiliates') }}</div>
            <div class="stat-label">Total de Subafiliados</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">R$ {{ number_format($affiliates->sum('pending_sub_affiliate_earnings'), 2, ',', '.') }}</div>
            <div class="stat-label">Comissões Pendentes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">R$ {{ number_format($affiliates->sum('total_sub_affiliate_earnings'), 2, ',', '.') }}</div>
            <div class="stat-label">Total Pago</div>
        </div>
    </div>

    <!-- Lista de Afiliados -->
    <div class="affiliates-list">
        @forelse($affiliates as $affiliate)
        <div class="affiliate-card" data-affiliate-id="{{ $affiliate['id'] }}">
            <!-- Desktop Layout -->
            <div class="desktop-layout">
                <div class="affiliate-info">
                    <div class="affiliate-avatar">
                        <div class="avatar-circle">{{ substr($affiliate['user']['name'], 0, 1) }}</div>
                    </div>
                    <div class="affiliate-details">
                        <div class="affiliate-name">{{ $affiliate['user']['name'] }}</div>
                        <div class="affiliate-email">{{ $affiliate['user']['email'] }}</div>
                        <div class="affiliate-code">Código: {{ $affiliate['affiliate_code'] }}</div>
                    </div>
                </div>
                
                <div class="affiliate-stats">
                    <div class="stat-item">
                        <div class="stat-value">{{ $affiliate['total_sub_affiliates'] }}</div>
                        <div class="stat-name">Subafiliados</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">R$ {{ number_format($affiliate['total_sub_affiliate_earnings'], 2, ',', '.') }}</div>
                        <div class="stat-name">Total Pago</div>
                    </div>
                    <div class="stat-item pending">
                        <div class="stat-value">R$ {{ number_format($affiliate['pending_sub_affiliate_earnings'], 2, ',', '.') }}</div>
                        <div class="stat-name">Pendente</div>
                    </div>
                </div>
                
                <div class="affiliate-controls">
                    <div class="control-group">
                        <label>Taxa Subafiliado (%):</label>
                        <input type="number" 
                               class="commission-input" 
                               data-affiliate-id="{{ $affiliate['id'] }}"
                               value="{{ $affiliate['sub_affiliate_commission_rate'] }}"
                               min="0" 
                               max="50" 
                               step="0.01">
                    </div>
                    <div class="control-group">
                        <label>Status:</label>
                        <select class="status-toggle" data-affiliate-id="{{ $affiliate['id'] }}">
                            <option value="active" {{ $affiliate['status'] === 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ $affiliate['status'] === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>
                
                <div class="affiliate-actions">
                    <button class="btn-action btn-save" data-affiliate-id="{{ $affiliate['id'] }}">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <button class="btn-action btn-sub-affiliates" data-affiliate-id="{{ $affiliate['id'] }}">
                        <i class="fas fa-users"></i> Subafiliados ({{ $affiliate['total_sub_affiliates'] }})
                    </button>
                    <button class="btn-action btn-reset" data-affiliate-id="{{ $affiliate['id'] }}">
                        <i class="fas fa-dollar-sign"></i> Zerar
                    </button>
                </div>
            </div>
            
            <!-- Mobile Layout -->
            <div class="mobile-layout">
                <div class="mobile-header">
                    <div class="affiliate-avatar">
                        <div class="avatar-circle">{{ substr($affiliate['user']['name'], 0, 1) }}</div>
                    </div>
                    <div class="affiliate-info">
                        <div class="affiliate-name">{{ $affiliate['user']['name'] }}</div>
                        <div class="affiliate-email">{{ $affiliate['user']['email'] }}</div>
                        <div class="affiliate-code">{{ $affiliate['affiliate_code'] }}</div>
                    </div>
                </div>
                
                <div class="mobile-stats">
                    <div class="stat-row">
                        <span class="stat-label">Subafiliados:</span>
                        <span class="stat-value">{{ $affiliate['total_sub_affiliates'] }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Total Pago:</span>
                        <span class="stat-value">R$ {{ number_format($affiliate['total_sub_affiliate_earnings'], 2, ',', '.') }}</span>
                    </div>
                    <div class="stat-row pending">
                        <span class="stat-label">Pendente:</span>
                        <span class="stat-value">R$ {{ number_format($affiliate['pending_sub_affiliate_earnings'], 2, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="mobile-controls">
                    <div class="control-row">
                        <label>Taxa Subafiliado (%):</label>
                        <input type="number" 
                               class="commission-input" 
                               data-affiliate-id="{{ $affiliate['id'] }}"
                               value="{{ $affiliate['sub_affiliate_commission_rate'] }}"
                               min="0" 
                               max="50" 
                               step="0.01">
                    </div>
                    <div class="control-row">
                        <label>Status:</label>
                        <select class="status-toggle" data-affiliate-id="{{ $affiliate['id'] }}">
                            <option value="active" {{ $affiliate['status'] === 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ $affiliate['status'] === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>
                
                <div class="mobile-buttons">
                    <button class="btn-action btn-save" data-affiliate-id="{{ $affiliate['id'] }}">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <button class="btn-action btn-sub-affiliates" data-affiliate-id="{{ $affiliate['id'] }}">
                        <i class="fas fa-users"></i> Subafiliados
                    </button>
                    <button class="btn-action btn-reset" data-affiliate-id="{{ $affiliate['id'] }}">
                        <i class="fas fa-dollar-sign"></i> Zerar
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="no-data">
            <div class="no-data-content">
                <div class="no-data-icon">👥</div>
                <div>Nenhum afiliado com subafiliados encontrado</div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Paginação -->
    @if($affiliates->hasPages())
    <div class="pagination-wrapper">
        {{ $affiliates->links() }}
    </div>
    @endif
</div>

<!-- Modal para Subafiliados -->
<div id="subAffiliatesModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Subafiliados</h3>
            <span class="close" onclick="closeSubAffiliatesModal()">&times;</span>
        </div>
        <div class="modal-body" id="subAffiliatesContent">
            <!-- Conteúdo será carregado via JavaScript -->
        </div>
    </div>
</div>

<!-- Notification -->
<div id="notification" class="notification"></div>

<style>
.affiliate-manager-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    min-height: 100vh;
    color: #ffffff;
}

.header-section {
    margin-bottom: 2rem;
}

.header-content {
    text-align: center;
}

.header-title h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.header-title p {
    color: #a0a0a0;
    font-size: 1.1rem;
}

.filters-section {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
}

.filters-form {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.filter-input:focus {
    outline: none;
    border-color: #4ecdc4;
    background: rgba(255, 255, 255, 0.15);
}

.filter-input::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-filter, .btn-clear {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 1rem;
}

.btn-filter {
    background: linear-gradient(45deg, #4ecdc4, #44a08d);
    color: #ffffff;
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
}

.btn-clear {
    background: linear-gradient(45deg, #ff6b6b, #ee5a52);
    color: #ffffff;
}

.btn-clear:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
}

.stats-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #4ecdc4;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #a0a0a0;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.affiliates-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.affiliate-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    border-radius: 15px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.affiliate-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.desktop-layout {
    display: grid;
    grid-template-columns: 2fr 2fr 2fr 1fr;
    gap: 2rem;
    align-items: center;
}

.mobile-layout {
    display: none;
}

.affiliate-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.affiliate-avatar .avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    color: #ffffff;
}

.affiliate-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 0.25rem;
}

.affiliate-email {
    color: #a0a0a0;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.affiliate-code {
    color: #4ecdc4;
    font-size: 0.8rem;
    font-weight: 500;
}

.affiliate-stats {
    display: flex;
    gap: 1.5rem;
}

.stat-item {
    text-align: center;
}

.stat-item .stat-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #4ecdc4;
    margin-bottom: 0.25rem;
}

.stat-item.pending .stat-value {
    color: #ff6b6b;
}

.stat-item .stat-name {
    color: #a0a0a0;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.affiliate-controls {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.control-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.control-group label {
    color: #a0a0a0;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.commission-input, .status-toggle {
    padding: 0.5rem;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.commission-input:focus, .status-toggle:focus {
    outline: none;
    border-color: #4ecdc4;
    background: rgba(255, 255, 255, 0.15);
}

.affiliate-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.btn-action {
    padding: 0.6rem 1rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-save {
    background: linear-gradient(45deg, #4ecdc4, #44a08d);
    color: #ffffff;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
}

.btn-sub-affiliates {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: #ffffff;
}

.btn-sub-affiliates:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-reset {
    background: linear-gradient(45deg, #ff6b6b, #ee5a52);
    color: #ffffff;
}

.btn-reset:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
}

.no-data {
    text-align: center;
    padding: 3rem;
    color: #a0a0a0;
}

.no-data-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    margin: 5% auto;
    padding: 0;
    border-radius: 15px;
    width: 90%;
    max-width: 800px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.modal-header {
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #ffffff;
    font-size: 1.5rem;
}

.close {
    color: #ffffff;
    font-size: 2rem;
    font-weight: bold;
    cursor: pointer;
    transition: opacity 0.3s ease;
}

.close:hover {
    opacity: 0.7;
}

.modal-body {
    padding: 2rem;
    max-height: 60vh;
    overflow-y: auto;
    color: #ffffff;
}

.sub-affiliates-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.sub-affiliates-table th,
.sub-affiliates-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sub-affiliates-table th {
    background: rgba(255, 255, 255, 0.1);
    color: #4ecdc4;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.8rem;
}

.sub-affiliates-table td {
    color: #ffffff;
}

.sub-affiliates-table tr:hover {
    background: rgba(255, 255, 255, 0.05);
}

/* Notification */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: 10px;
    color: #ffffff;
    font-weight: 600;
    z-index: 1001;
    transform: translateX(400px);
    transition: transform 0.3s ease;
}

.notification.show {
    transform: translateX(0);
}

.notification.success {
    background: linear-gradient(45deg, #4ecdc4, #44a08d);
}

.notification.error {
    background: linear-gradient(45deg, #ff6b6b, #ee5a52);
}

/* Responsive Design */
@media (max-width: 768px) {
    .affiliate-manager-container {
        padding: 1rem;
    }
    
    .header-title h1 {
        font-size: 2rem;
    }
    
    .filters-form {
        flex-direction: column;
    }
    
    .filter-group {
        min-width: 100%;
    }
    
    .stats-section {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .desktop-layout {
        display: none;
    }
    
    .mobile-layout {
        display: block;
    }
    
    .mobile-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .mobile-stats {
        margin-bottom: 1rem;
    }
    
    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .stat-row:last-child {
        border-bottom: none;
    }
    
    .stat-row.pending .stat-value {
        color: #ff6b6b;
    }
    
    .mobile-controls {
        margin-bottom: 1rem;
    }
    
    .control-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .control-row label {
        margin-bottom: 0;
    }
    
    .control-row input,
    .control-row select {
        width: 120px;
    }
    
    .mobile-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .mobile-buttons .btn-action {
        flex: 1;
        min-width: 120px;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    .sub-affiliates-table {
        font-size: 0.8rem;
    }
    
    .sub-affiliates-table th,
    .sub-affiliates-table td {
        padding: 0.5rem;
    }
}
</style>

<script>
// Dados dos afiliados para uso no JavaScript
const affiliatesData = @json($affiliates->items());

// Função para mostrar notificação
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.classList.add('show');
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

// Função para salvar configurações
function saveAffiliateSettings(affiliateId) {
    const card = document.querySelector(`[data-affiliate-id="${affiliateId}"]`);
    const commissionRate = card.querySelector('.commission-input').value;
    const status = card.querySelector('.status-toggle').value;
    const saveBtn = card.querySelector('.btn-save');
    
    // Mostrar loading
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    saveBtn.disabled = true;
    
    // Atualizar taxa de comissão
    fetch(`/sub-affiliates/${affiliateId}/commission-rate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            sub_affiliate_commission_rate: commissionRate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Configurações salvas com sucesso!', 'success');
        } else {
            throw new Error(data.message || 'Erro ao salvar configurações');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showNotification(error.message || 'Erro ao salvar configurações', 'error');
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

// Função para zerar ganhos
function resetEarnings(affiliateId) {
    if (!confirm('Tem certeza que deseja zerar os ganhos pendentes? Esta ação não pode ser desfeita.')) {
        return;
    }
    
    const card = document.querySelector(`[data-affiliate-id="${affiliateId}"]`);
    const resetBtn = card.querySelector('.btn-reset');
    
    // Mostrar loading
    const originalText = resetBtn.innerHTML;
    resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Zerando...';
    resetBtn.disabled = true;
    
    fetch(`/sub-affiliates/${affiliateId}/reset-earnings`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Ganhos zerados com sucesso!', 'success');
            // Recarregar a página para atualizar os valores
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Erro ao zerar ganhos');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showNotification(error.message || 'Erro ao zerar ganhos', 'error');
    })
    .finally(() => {
        resetBtn.innerHTML = originalText;
        resetBtn.disabled = false;
    });
}

// Função para mostrar subafiliados
function showSubAffiliates(affiliateId) {
    const affiliate = affiliatesData.find(a => a.id === affiliateId);
    if (!affiliate) {
        showNotification('Afiliado não encontrado', 'error');
        return;
    }
    
    const modal = document.getElementById('subAffiliatesModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('subAffiliatesContent');
    
    modalTitle.textContent = `Subafiliados de ${affiliate.user.name}`;
    
    let content = '';
    
    if (affiliate.sub_affiliates && affiliate.sub_affiliates.length > 0) {
        content = `
            <div style="margin-bottom: 15px; color: #00ff87; font-weight: 600;">
                Total de subafiliados: ${affiliate.sub_affiliates.length}
            </div>
            <table class="sub-affiliates-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Código</th>
                        <th>Status</th>
                        <th>Referrals</th>
                        <th>Total Ganho</th>
                        <th>Pendente</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        affiliate.sub_affiliates.forEach(subAffiliate => {
            const totalEarnings = parseFloat(subAffiliate.total_earnings || 0);
            const pendingEarnings = parseFloat(subAffiliate.pending_earnings || 0);
            const statusBadge = subAffiliate.status === 'active' ? 
                '<span style="color: #4ecdc4; font-weight: 600;">✅ Ativo</span>' : 
                '<span style="color: #ff6b6b; font-weight: 600;">❌ Inativo</span>';
            
            content += `
                <tr>
                    <td>${subAffiliate.user.name}</td>
                    <td>${subAffiliate.user.email}</td>
                    <td style="color: #4ecdc4; font-weight: 600;">${subAffiliate.affiliate_code}</td>
                    <td>${statusBadge}</td>
                    <td style="text-align: center;">${subAffiliate.total_referrals}</td>
                    <td style="color: #4ecdc4;">R$ ${totalEarnings.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                    <td style="color: #ff6b6b;">R$ ${pendingEarnings.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                </tr>
            `;
        });
        
        content += `
                </tbody>
            </table>
        `;
    } else {
        content = `
            <div style="text-align: center; padding: 2rem; color: #a0a0a0;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                <div>Nenhum subafiliado encontrado</div>
            </div>
        `;
    }
    
    modalContent.innerHTML = content;
    modal.style.display = 'block';
}

// Função para fechar modal
function closeSubAffiliatesModal() {
    document.getElementById('subAffiliatesModal').style.display = 'none';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Botões de salvar
    document.querySelectorAll('.btn-save').forEach(btn => {
        btn.addEventListener('click', function() {
            const affiliateId = parseInt(this.getAttribute('data-affiliate-id'));
            saveAffiliateSettings(affiliateId);
        });
    });
    
    // Botões de subafiliados
    document.querySelectorAll('.btn-sub-affiliates').forEach(btn => {
        btn.addEventListener('click', function() {
            const affiliateId = parseInt(this.getAttribute('data-affiliate-id'));
            showSubAffiliates(affiliateId);
        });
    });
    
    // Botões de zerar
    document.querySelectorAll('.btn-reset').forEach(btn => {
        btn.addEventListener('click', function() {
            const affiliateId = parseInt(this.getAttribute('data-affiliate-id'));
            resetEarnings(affiliateId);
        });
    });
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('subAffiliatesModal');
        if (event.target === modal) {
            closeSubAffiliatesModal();
        }
    });
});
</script>
@endsection