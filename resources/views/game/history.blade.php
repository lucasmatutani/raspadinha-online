@extends('layouts.app')

@section('title', 'Histórico de Jogos - Raspadinha Online')

@push('styles')
<style>
    .history-container {
        background: linear-gradient(145deg, #2a2a3e, #1a1a2e);
        border-radius: 20px;
        padding: 2rem;
        border: 2px solid rgba(0, 255, 135, 0.3);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .history-title {
        color: #00ff87;
        font-size: 2rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    .game-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border-left: 4px solid #666;
        transition: all 0.3s ease;
    }

    .game-card.winner {
        border-left-color: #00ff87;
        background: rgba(0, 255, 135, 0.1);
    }

    .game-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .game-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .game-date {
        color: #999;
        font-size: 0.9rem;
    }

    .game-result {
        font-weight: bold;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.9rem;
    }

    .game-result.winner {
        background: rgba(0, 255, 135, 0.2);
        color: #00ff87;
    }

    .game-result.loser {
        background: rgba(255, 107, 107, 0.2);
        color: #ff6b6b;
    }

    .game-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 5px;
        margin: 1rem 0;
        max-width: 200px;
    }

    .mini-cell {
        aspect-ratio: 1;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: bold;
    }

    .mini-cell.winner {
        background: rgba(0, 255, 135, 0.3);
        color: #00ff87;
    }

    .game-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .bet-info {
        color: #ccc;
    }

    .prize-info {
        font-weight: bold;
        color: #00ff87;
        font-size: 1.1rem;
    }

    .no-games {
        text-align: center;
        padding: 3rem;
        color: #999;
    }

    .no-games-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        border: 1px solid rgba(0, 255, 135, 0.2);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #00ff87;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #999;
        font-size: 0.9rem;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    .pagination {
        display: flex;
        gap: 0.5rem;
    }

    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.1);
        color: #ccc;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        background: rgba(0, 255, 135, 0.2);
        color: #00ff87;
    }

    .pagination .current {
        background: #00ff87;
        color: #1a1a2e;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .history-container {
            padding: 1rem;
        }

        .game-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .game-info {
            flex-direction: column;
            align-items: flex-start;
        }

        .stats-cards {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="history-container">
        <h1 class="history-title">Histórico de Jogos</h1>

        @php
            $totalGames = $cards->total();
            $totalWins = auth()->user()->scratchCards()->where('is_winner', true)->count();
            $totalSpent = auth()->user()->scratchCards()->sum('bet_amount');
            $totalWon = auth()->user()->scratchCards()->sum('prize_amount');
            $winRate = $totalGames > 0 ? round(($totalWins / $totalGames) * 100, 1) : 0;
        @endphp

        <!-- Estatísticas -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-number">{{ $totalGames }}</div>
                <div class="stat-label">Jogos Totais</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $totalWins }}</div>
                <div class="stat-label">Vitórias</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $winRate }}%</div>
                <div class="stat-label">Taxa de Vitória</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">R$ {{ number_format($totalWon - $totalSpent, 2, ',', '.') }}</div>
                <div class="stat-label">Lucro/Perda</div>
            </div>
        </div>

        @if($cards->count() > 0)
            <!-- Lista de Jogos -->
            @foreach($cards as $card)
            <div class="game-card {{ $card->is_winner ? 'winner' : '' }}">
                <div class="game-header">
                    <div class="game-date">
                        {{ $card->played_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="game-result {{ $card->is_winner ? 'winner' : 'loser' }}">
                        {{ $card->is_winner ? '🎉 GANHOU!' : '😔 Não foi dessa vez' }}
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                    <!-- Mini Grid -->
                    <div class="game-grid">
                        @php
                            $displayValues = [
                                50 => '0,50',
                                100 => '1,00',
                                200 => '2,00',
                                500 => '5,00',
                                1000 => '10',
                                2000 => '20',
                                5000 => '50',
                                10000 => '100',
                                20000 => '200',
                                50000 => '500',
                                100000 => '1K',
                                200000 => '2K'
                            ];
                        @endphp
                        
                        @for($row = 0; $row < 3; $row++)
                            @for($col = 0; $col < 3; $col++)
                                @php
                                    $value = $card->symbols[$row][$col];
                                    $isWinning = $card->is_winner && isset($card->winning_value) && $value == $card->winning_value;
                                @endphp
                                <div class="mini-cell {{ $isWinning ? 'winner' : '' }}">
                                    {{ $displayValues[$value] ?? number_format($value/100, 0) }}
                                </div>
                            @endfor
                        @endfor
                    </div>

                    <!-- Informações do Jogo -->
                    <div style="flex: 1;">
                        <div class="game-info">
                            <div class="bet-info">
                                <div>Aposta: R$ {{ number_format($card->bet_amount, 2, ',', '.') }}</div>
                                @if($card->is_winner)
                                    <div style="color: #00ff87; font-size: 0.9rem; margin-top: 0.25rem;">
                                        Tipo: {{ ucfirst(str_replace('_', ' ', $card->win_type ?? 'three_same')) }}
                                    </div>
                                @endif
                            </div>
                            <div class="prize-info">
                                @if($card->is_winner)
                                    +R$ {{ number_format($card->prize_amount, 2, ',', '.') }}
                                @else
                                    R$ 0,00
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Paginação -->
            <div class="pagination-wrapper">
                {{ $cards->links() }}
            </div>

        @else
            <div class="no-games">
                <div class="no-games-icon">🎲</div>
                <h3>Nenhum jogo encontrado</h3>
                <p>Você ainda não jogou nenhuma raspadinha.</p>
                <a href="{{ route('game.index') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    Jogar Agora
                </a>
            </div>
        @endif
    </div>
</div>
@endsection