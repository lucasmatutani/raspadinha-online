<!-- resources/views/affiliate/manager.blade.php -->
@extends('layouts.app')

@section('title', 'Gerenciador de Afiliados - RaspaKing')

@push('styles')
<style>
    .affiliate-manager {
        background: linear-gradient(145deg, #2a2a3e, #1a1a2e);
        border-radius: 20px;
        padding: 2rem;
        margin: 2rem auto;
        border: 2px solid rgba(0, 255, 135, 0.3);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .affiliate-manager h1 {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #00ff87, #ffffff, #00ff87);
        background-size: 200% 200%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradient 3s ease infinite;
        margin-bottom: 1.5rem;
        text-shadow: 0 0 30px rgba(0, 255, 135, 0.5);
    }

    .affiliate-card {
        background: rgba(26, 26, 46, 0.8);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(0, 255, 135, 0.2);
        transition: all 0.3s ease;
    }

    .affiliate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        border-color: rgba(0, 255, 135, 0.4);
    }

    .affiliate-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 1rem;
    }

    .affiliate-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: #00ff87;
    }

    .affiliate-email {
        color: #cccccc;
        font-size: 0.9rem;
    }

    .affiliate-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-box {
        background: rgba(0, 255, 135, 0.1);
        border-radius: 10px;
        padding: 1rem;
        flex: 1;
        min-width: 150px;
        text-align: center;
        border: 1px solid rgba(0, 255, 135, 0.2);
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: bold;
        color: #00ff87;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #cccccc;
    }

    .referrals-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 1.5rem;
    }

    .referrals-table th {
        background: rgba(0, 255, 135, 0.1);
        color: #00ff87;
        text-align: left;
        padding: 1rem;
        font-weight: 600;
        border-bottom: 1px solid rgba(0, 255, 135, 0.2);
    }

    .referrals-table td {
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: #ffffff;
    }

    .referrals-table tr:hover td {
        background: rgba(0, 255, 135, 0.05);
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

    .save-btn {
        background: linear-gradient(135deg, #00ff87, #00b359);
        border: none;
        border-radius: 5px;
        color: #1a1a2e;
        padding: 0.5rem 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .save-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    .toggle-referrals {
        background: rgba(0, 255, 135, 0.1);
        border: 1px solid rgba(0, 255, 135, 0.2);
        border-radius: 5px;
        color: #00ff87;
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .toggle-referrals:hover {
        background: rgba(0, 255, 135, 0.2);
    }

    .referrals-container {
        display: none;
        margin-top: 1rem;
    }

    .show-referrals .referrals-container {
        display: block;
    }

    @media (max-width: 768px) {
        .affiliate-stats {
            flex-direction: column;
        }

        .stat-box {
            min-width: 100%;
        }

        .referrals-table {
            font-size: 0.9rem;
        }

        .referrals-table th,
        .referrals-table td {
            padding: 0.75rem 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="affiliate-manager">
        <h1>Gerenciador de Afiliados</h1>
        <p class="mb-4">Gerencie os afiliados que possuem mais de um afiliado registrado.</p>

        @if(count($affiliates) > 0)
            @foreach($affiliates as $affiliate)
            <div class="affiliate-card" id="affiliate-{{ $affiliate['id'] }}">
                <div class="affiliate-header">
                    <div>
                        <div class="affiliate-name">{{ $affiliate['user']['name'] }}</div>
                        <div class="affiliate-email">{{ $affiliate['user']['email'] }}</div>
                        <div class="text-gray-400 mt-1">Código: {{ $affiliate['affiliate_code'] }}</div>
                    </div>
                    <div>
                        <form class="commission-form d-flex align-items-center gap-2" data-affiliate-id="{{ $affiliate['id'] }}">
                            <div>
                                <label for="commission-{{ $affiliate['id'] }}" class="me-2">Comissão:</label>
                                <input type="number" id="commission-{{ $affiliate['id'] }}" class="commission-input" 
                                    value="{{ $affiliate['commission_rate'] }}" min="0" max="100" step="0.01">
                                <span>%</span>
                            </div>
                            <div class="ms-3">
                                <label for="status-{{ $affiliate['id'] }}" class="me-2">Status:</label>
                                <select id="status-{{ $affiliate['id'] }}" class="status-toggle">
                                    <option value="active" {{ $affiliate['status'] == 'active' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inactive" {{ $affiliate['status'] == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>
                            <button type="submit" class="save-btn ms-3">Salvar</button>
                        </form>
                    </div>
                </div>

                <div class="affiliate-stats">
                    <div class="stat-box">
                        <div class="stat-value">{{ $affiliate['total_referrals'] }}</div>
                        <div class="stat-label">Total de Afiliados</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">R$ {{ number_format($affiliate['total_earnings'], 2, ',', '.') }}</div>
                        <div class="stat-label">Ganhos Totais</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">R$ {{ number_format($affiliate['pending_earnings'], 2, ',', '.') }}</div>
                        <div class="stat-label">Ganhos Pendentes</div>
                    </div>
                </div>

                <button class="toggle-referrals" onclick="toggleReferrals('{{ $affiliate['id'] }}')">
                    <span class="toggle-icon">+</span> Mostrar Afiliados ({{ $affiliate['total_referrals'] }})
                </button>

                <div class="referrals-container" id="referrals-{{ $affiliate['id'] }}">
                    <table class="referrals-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Data de Registro</th>
                                <th>Perdas Totais</th>
                                <th>Comissão Gerada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($affiliate['referrals'] as $referral)
                            <tr>
                                <td>{{ $referral['user']['name'] }}</td>
                                <td>{{ $referral['user']['email'] }}</td>
                                <td>{{ $referral['registered_at'] }}</td>
                                <td>R$ {{ number_format($referral['total_losses'], 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($referral['total_commission'], 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-5">
                <p class="text-xl text-gray-400">Nenhum afiliado encontrado com mais de um afiliado registrado.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleReferrals(affiliateId) {
        const container = document.getElementById(`referrals-${affiliateId}`);
        const button = container.previousElementSibling;
        const icon = button.querySelector('.toggle-icon');
        
        if (container.style.display === 'block') {
            container.style.display = 'none';
            icon.textContent = '+';
            button.innerHTML = `<span class="toggle-icon">+</span> Mostrar Afiliados`;
        } else {
            container.style.display = 'block';
            icon.textContent = '-';
            button.innerHTML = `<span class="toggle-icon">-</span> Ocultar Afiliados`;
        }
    }

    // Atualizar taxa de comissão e status
    document.querySelectorAll('.commission-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const affiliateId = this.getAttribute('data-affiliate-id');
            const commissionRate = document.getElementById(`commission-${affiliateId}`).value;
            const status = document.getElementById(`status-${affiliateId}`).value;
            
            // Atualizar taxa de comissão
            fetch(`/affiliate_manager/${affiliateId}/commission`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ commission_rate: commissionRate })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Taxa de comissão atualizada com sucesso', 'success');
                } else {
                    showNotification('Erro ao atualizar taxa de comissão', 'error');
                }
            });
            
            // Atualizar status
            fetch(`/affiliate_manager/${affiliateId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Status atualizado com sucesso', 'success');
                } else {
                    showNotification('Erro ao atualizar status', 'error');
                }
            });
        });
    });
</script>
@endpush