@extends('layouts.app')

@section('title', '👑 Meus Subafiliados - Raspadinha Online')

@section('content')
<div class="user-sub-affiliates-container">
    <div class="header-section">
        <div class="header-content">
            <div class="header-title">
                <h1>👑 Meus Subafiliados</h1>
                <p>Acompanhe os ganhos e performance dos seus subafiliados</p>
            </div>
        </div>
    </div>

    <!-- Filtros de Busca -->
    <div class="filters-section">
        <form method="GET" action="{{ route('user.sub-affiliates.index') }}" class="filters-form">
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
                <a href="{{ route('user.sub-affiliates.index') }}" class="btn-clear">🗑️ Limpar</a>
            </div>
        </form>
    </div>

    <!-- Estatísticas -->
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <div class="stat-number">{{ $totalSubAffiliates }}</div>
                <div class="stat-label">Total de Subafiliados</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-info">
                <div class="stat-number">{{ $totalReferrals }}</div>
                <div class="stat-label">Total de Referências</div>
            </div>
        </div>
        <div class="stat-card pending">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <div class="stat-number">R$ {{ number_format($pendingEarnings, 2, ',', '.') }}</div>
                <div class="stat-label">Ganhos Pendentes</div>
            </div>
        </div>
        <div class="stat-card total">
            <div class="stat-icon">💎</div>
            <div class="stat-info">
                <div class="stat-number">R$ {{ number_format($totalEarnings, 2, ',', '.') }}</div>
                <div class="stat-label">Seu Ganho Total</div>
            </div>
        </div>
    </div>

    <!-- Lista de Subafiliados -->
    <div class="sub-affiliates-list">
        @forelse($subAffiliates as $subAffiliate)
        <div class="sub-affiliate-card" data-sub-affiliate-id="{{ $subAffiliate->id }}">
            <!-- Desktop Layout -->
            <div class="desktop-layout">
                <div class="sub-affiliate-info">
                    <div class="sub-affiliate-avatar">
                        <div class="avatar-circle">{{ substr($subAffiliate->user->name, 0, 1) }}</div>
                    </div>
                    <div class="sub-affiliate-details">
                        <div class="sub-affiliate-name">{{ $subAffiliate->user->name }}</div>
                        <div class="sub-affiliate-email">{{ $subAffiliate->user->email }}</div>
                    </div>
                </div>
                
                <div class="sub-affiliate-stats">
                    <div class="stat-item">
                        <div class="stat-value">{{ $subAffiliate->total_referrals }}</div>
                        <div class="stat-name">Referências</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $subAffiliate->parent_commission_rate }}%</div>
                        <div class="stat-name">Sua Taxa</div>
                    </div>
                    <div class="stat-item total">
                        <div class="stat-value">R$ {{ number_format($subAffiliate->total_earnings ?? 0, 2, ',', '.') }}</div>
                        <div class="stat-name">Total Ganho</div>
                    </div>
                    <div class="stat-item pending">
                        <div class="stat-value">R$ {{ number_format($subAffiliate->pending_earnings ?? 0, 2, ',', '.') }}</div>
                        <div class="stat-name">Pendente</div>
                    </div>
                    <div class="stat-item earning">
                        <div class="stat-value">R$ {{ number_format($subAffiliate->parent_earning ?? 0, 2, ',', '.') }}</div>
                        <div class="stat-name">Seu Ganho</div>
                    </div>
                </div>
                
                <div class="sub-affiliate-actions">
                    <button class="btn-action btn-referrals" data-sub-affiliate-id="{{ $subAffiliate->id }}">
                        <i class="fas fa-users"></i> Referências ({{ $subAffiliate->total_referrals }})
                    </button>
                </div>
            </div>
            
            <!-- Mobile Layout -->
            <div class="mobile-layout">
                <div class="mobile-header">
                    <div class="sub-affiliate-avatar">
                        <div class="avatar-circle">{{ substr($subAffiliate->user->name, 0, 1) }}</div>
                    </div>
                    <div class="sub-affiliate-info">
                        <div class="sub-affiliate-name">{{ $subAffiliate->user->name }}</div>
                        <div class="sub-affiliate-email">{{ $subAffiliate->user->email }}</div>
                    </div>
                </div>
                
                <div class="mobile-stats">
                    <div class="stat-row">
                        <span class="stat-label">Referências:</span>
                        <span class="stat-value">{{ $subAffiliate->total_referrals }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Sua Taxa:</span>
                        <span class="stat-value">{{ $subAffiliate->parent_commission_rate }}%</span>
                    </div>
                    <div class="stat-row total">
                        <span class="stat-label">Total Ganho:</span>
                        <span class="stat-value">R$ {{ number_format($subAffiliate->total_earnings ?? 0, 2, ',', '.') }}</span>
                    </div>
                    <div class="stat-row pending">
                        <span class="stat-label">Pendente:</span>
                        <span class="stat-value">R$ {{ number_format($subAffiliate->pending_earnings ?? 0, 2, ',', '.') }}</span>
                    </div>
                    <div class="stat-row earning">
                        <span class="stat-label">Seu Ganho:</span>
                        <span class="stat-value">R$ {{ number_format($subAffiliate->parent_earning ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="mobile-buttons">
                    <button class="btn-action btn-referrals" data-sub-affiliate-id="{{ $subAffiliate->id }}">
                        <i class="fas fa-users"></i> Referências
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="no-data">
            <div class="no-data-content">
                <div class="no-data-icon">👥</div>
                <div class="no-data-title">Nenhum subafiliado encontrado</div>
                <div class="no-data-text">Você ainda não possui subafiliados ativos.</div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Paginação -->
    @if($subAffiliates->hasPages())
    <div class="pagination-wrapper">
        {{ $subAffiliates->links() }}
    </div>
    @endif
</div>



<!-- Notification -->
<div id="notification" class="notification"></div>

<style>
.user-sub-affiliates-container {
    width: 100%;
    margin: 0;
    padding: 6rem 2rem 2rem 2rem;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    min-height: 100vh;
    color: #ffffff;
    overflow-x: auto;
}

.header-section {
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
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
    margin: 0;
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
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
    width: 100%;
    overflow: hidden;
}

.stat-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    border-radius: 15px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: transform 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.8;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #4ecdc4;
    margin-bottom: 0.25rem;
}

.stat-card.pending .stat-number {
    color: #ff6b6b;
}

.stat-card.total .stat-number {
    color: #ffd700;
}

.stat-label {
    color: #a0a0a0;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.sub-affiliates-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.sub-affiliate-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    border-radius: 15px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    width: 100%;
    box-sizing: border-box;
    overflow: hidden;
}

.sub-affiliate-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.desktop-layout {
    display: grid;
    grid-template-columns: minmax(300px, 2fr) minmax(400px, 3fr) minmax(150px, 1fr);
    gap: 1.5rem;
    align-items: center;
    width: 100%;
    overflow: hidden;
}

.mobile-layout {
    display: none;
}

.sub-affiliate-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sub-affiliate-avatar .avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.5rem;
    color: #ffffff;
}

.sub-affiliate-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 0.25rem;
    word-wrap: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

.sub-affiliate-email {
    color: #a0a0a0;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    word-wrap: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

.sub-affiliate-code {
    color: #4ecdc4;
    font-size: 0.8rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
    word-wrap: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-badge.active {
    background: rgba(78, 205, 196, 0.2);
    color: #4ecdc4;
    border: 1px solid rgba(78, 205, 196, 0.3);
}

.status-badge.inactive {
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
    border: 1px solid rgba(255, 107, 107, 0.3);
}

.sub-affiliate-stats {
    display: grid;
    grid-template-columns: repeat(5, minmax(80px, 1fr));
    gap: 0.75rem;
    width: 100%;
    overflow: hidden;
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

.stat-item.total .stat-value {
    color: #ffd700;
}

.stat-item.earning .stat-value {
    color: #00ff88;
    font-weight: 800;
}

.stat-row.earning {
    background: rgba(0, 255, 136, 0.1);
    border-radius: 8px;
    padding: 0.5rem;
    border: 1px solid rgba(0, 255, 136, 0.2);
}

.stat-item .stat-name {
    color: #a0a0a0;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.sub-affiliate-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.btn-action {
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-details {
    background: linear-gradient(45deg, #4ecdc4, #44a08d);
    color: #ffffff;
}

.btn-details:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
}

.btn-referrals {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: #ffffff;
}

.btn-referrals:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.no-data {
    text-align: center;
    padding: 4rem 2rem;
    color: #a0a0a0;
}

.no-data-icon {
    font-size: 5rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.no-data-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #ffffff;
}

.no-data-text {
    font-size: 1rem;
    margin-bottom: 2rem;
}

.btn-primary {
    background: linear-gradient(45deg, #4ecdc4, #44a08d);
    color: #ffffff;
    text-decoration: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
    text-decoration: none;
    color: #ffffff;
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
    max-width: 900px;
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

.details-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2rem;
}

.details-table th,
.details-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.details-table th {
    background: rgba(255, 255, 255, 0.1);
    color: #4ecdc4;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.8rem;
}

.details-table td {
    color: #ffffff;
}

.details-table tr:hover {
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

.notification.info {
    background: linear-gradient(45deg, #3498db, #2980b9);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .desktop-layout {
        grid-template-columns: minmax(250px, 1fr) minmax(300px, 2fr) minmax(120px, 1fr);
        gap: 1rem;
    }
    
    .sub-affiliate-stats {
        grid-template-columns: repeat(5, minmax(70px, 1fr));
        gap: 0.5rem;
    }
}

@media (max-width: 992px) {
    .stats-section {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .sub-affiliate-stats {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
    }
}

@media (max-width: 768px) {
    .user-sub-affiliates-container {
        padding: 8rem 1rem 1rem 1rem;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
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
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
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
    
    .stat-row.total .stat-value {
        color: #ffd700;
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
    
    .details-table {
        font-size: 0.8rem;
    }
    
    .details-table th,
    .details-table td {
        padding: 0.5rem;
    }
}

@media (max-width: 480px) {
    .user-sub-affiliates-container {
        padding: 10rem 0.5rem 0.5rem 0.5rem;
    }
    
    .header-title h1 {
        font-size: 1.5rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .sub-affiliate-card {
        padding: 1rem;
    }
    
    .mobile-buttons .btn-action {
        min-width: 100px;
        font-size: 0.8rem;
        padding: 0.5rem;
    }
    
    .modal-content {
        width: 98%;
        margin: 5% auto;
    }
}
</style>

<!-- Modal para exibir referências -->
<div id="referralsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Referências do Subafiliado</h3>
            <span class="close" onclick="closeReferralsModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="referralsLoading" style="text-align: center; padding: 20px;">
                <p>Carregando referências...</p>
            </div>
            <div id="referralsContent" style="display: none;">
                <div id="referralsTable"></div>
            </div>
            <div id="referralsError" style="display: none; text-align: center; padding: 20px; color: #e74c3c;">
                <p>Erro ao carregar referências.</p>
            </div>
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #1a1a2e;
    margin: 5% auto;
    padding: 0;
    border-radius: 10px;
    width: 80%;
    max-width: 600px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 10px 10px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.2rem;
}

.close {
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s;
}

.close:hover {
    color: #ccc;
}

.modal-body {
    padding: 20px;
    color: #e0e0e0;
}

.referrals-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.referrals-table th,
.referrals-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #333;
}

.referrals-table th {
    background-color: #2a2a3e;
    color: #fff;
    font-weight: bold;
}

.referrals-table tr:hover {
    background-color: #2a2a3e;
}

.no-referrals {
    text-align: center;
    padding: 20px;
    color: #888;
    font-style: italic;
}
</style>

<script>
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



// Função para abrir modal de referências
function openReferralsModal(subAffiliateId) {
    const modal = document.getElementById('referralsModal');
    const loading = document.getElementById('referralsLoading');
    const content = document.getElementById('referralsContent');
    const error = document.getElementById('referralsError');
    
    // Mostrar modal e loading
    modal.style.display = 'block';
    loading.style.display = 'block';
    content.style.display = 'none';
    error.style.display = 'none';
    
    // Fazer requisição AJAX
    fetch(`/user/sub-affiliates/${subAffiliateId}/referrals`)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.success) {
                content.style.display = 'block';
                displayReferrals(data.referrals);
            } else {
                error.style.display = 'block';
                showNotification(data.error || 'Erro ao carregar referências', 'error');
            }
        })
        .catch(err => {
            loading.style.display = 'none';
            error.style.display = 'block';
            showNotification('Erro ao carregar referências', 'error');
        });
}

// Função para exibir referências na tabela
function displayReferrals(referrals) {
    const tableContainer = document.getElementById('referralsTable');
    
    if (referrals.length === 0) {
        tableContainer.innerHTML = '<div class="no-referrals">Nenhuma referência encontrada.</div>';
        return;
    }
    
    let tableHTML = `
        <table class="referrals-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Data de Cadastro</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    referrals.forEach(referral => {
        tableHTML += `
            <tr>
                <td>${referral.user_name}</td>
                <td>${referral.created_at}</td>
            </tr>
        `;
    });
    
    tableHTML += '</tbody></table>';
    tableContainer.innerHTML = tableHTML;
}

// Função para fechar modal
function closeReferralsModal() {
    document.getElementById('referralsModal').style.display = 'none';
}

// Fechar modal ao clicar fora dele
window.onclick = function(event) {
    const modal = document.getElementById('referralsModal');
    if (event.target === modal) {
        closeReferralsModal();
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Botões de referências
    document.querySelectorAll('.btn-referrals').forEach(btn => {
        btn.addEventListener('click', function() {
            const subAffiliateId = parseInt(this.getAttribute('data-sub-affiliate-id'));
            openReferralsModal(subAffiliateId);
        });
    });
});
</script>
@endsection