<!-- resources/views/game/index.blade.php -->
@extends('layouts.app')

@section('title', 'Raspadinha Online - Jogue Agora')

@push('styles')
    <style>
        /* Game Section */
        .game-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .game-title {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, #00ff87, #ffffff, #00ff87);
            background-size: 200% 200%;
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent;
            animation: gradient 3s ease infinite;
            margin-bottom: 1rem;
            text-shadow: 0 0 30px rgba(0, 255, 135, 0.5);
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .game-subtitle {
            font-size: 1.2rem;
            color: #cccccc;
            margin-bottom: 2rem;
        }

        /* Scratch Card */
        .scratch-card-container {
            background: linear-gradient(145deg, #2a2a3e, #1a1a2e);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 500px;
            border: 2px solid rgba(0, 255, 135, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .scratch-card {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 2rem 0;
        }

        .scratch-cell {
            aspect-ratio: 1;
            background: linear-gradient(135deg, #00ff87, #00b359);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #1a1a2e;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            user-select: none;
        }

        .scratch-cell::before {
            content: "💰";
            position: absolute;
            font-size: 2rem;
            opacity: 0.3;
            pointer-events: none;
        }

        .scratch-cell:not(.revealed):hover {
            transform: scale(1.1) !important;
            box-shadow: 0 8px 20px rgba(0, 255, 135, 0.5) !important;
            background: linear-gradient(135deg, #00ff87, #00d96b) !important;
        }

        .scratch-cell:not(.revealed):active {
            transform: scale(0.9) !important;
            box-shadow: 0 3px 10px rgba(0, 255, 135, 0.3) !important;
        }

        .scratch-cell.revealed {
            background: linear-gradient(145deg, #1a1a2e, #2a2a3e);
            color: #00ff87;
            border: 2px solid rgba(0, 255, 135, 0.5);
            animation: reveal 0.5s ease;
            cursor: default;
            transform: none !important;
            font-family: 'Courier New', 'Monaco', 'Consolas', monospace;
            font-weight: 900;
            font-size: 1.2rem;
            text-shadow: 0 0 10px rgba(0, 255, 135, 0.8);
            box-shadow: 
                inset 0 2px 4px rgba(0, 255, 135, 0.2),
                0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .scratch-cell.revealed::before {
            display: none;
        }

        .scratch-cell.winning {
            background: linear-gradient(145deg, #2a1810, #3a2415) !important;
            color: #ffd700 !important;
            border: 2px solid #ffd700 !important;
            animation: pulse 1.5s ease infinite, winningGlow 2s ease-in-out infinite;
            box-shadow: 
                inset 0 2px 4px rgba(255, 215, 0, 0.3),
                0 0 20px rgba(255, 215, 0, 0.6),
                0 4px 12px rgba(0, 0, 0, 0.4) !important;
            text-shadow: 0 0 15px rgba(255, 215, 0, 1) !important;
            font-weight: 900 !important;
            font-size: 1.3rem !important;
        }

        @keyframes winningGlow {
            0%, 100% {
                box-shadow: 
                    inset 0 2px 4px rgba(255, 215, 0, 0.3),
                    0 0 20px rgba(255, 215, 0, 0.6),
                    0 4px 12px rgba(0, 0, 0, 0.4);
            }
            50% {
                box-shadow: 
                    inset 0 2px 4px rgba(255, 215, 0, 0.5),
                    0 0 30px rgba(255, 215, 0, 0.9),
                    0 4px 12px rgba(0, 0, 0, 0.4);
            }
        }

        @keyframes reveal {
            from {
                transform: scale(0.8);
                opacity: 0;
                filter: blur(5px);
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
                filter: blur(2px);
            }
            to {
                transform: scale(1);
                opacity: 1;
                filter: blur(0px);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Play Button */
        .play-button {
            background: linear-gradient(135deg, #00ff87, #00b359);
            border: none;
            color: #1a1a2e;
            padding: 1rem 3rem;
            font-size: 1.3rem;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 1rem;
            box-shadow: 0 5px 15px rgba(0, 255, 135, 0.3);
        }

        .play-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 255, 135, 0.5);
        }

        .play-button:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
        }

        /* Prize Info */
        .prize-info {
            background: rgba(0, 255, 135, 0.1);
            border: 1px solid rgba(0, 255, 135, 0.3);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .prize-text {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .prize-amount {
            font-size: 2rem;
            font-weight: bold;
            color: #00ff87;
        }

        /* Instructions */
        .instructions {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: left;
        }

        .instructions h3 {
            color: #00ff87;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .instructions ul {
            list-style: none;
            padding: 0;
        }

        .instructions li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .instructions li::before {
            content: "✓";
            color: #00ff87;
            font-weight: bold;
            margin-right: 0.5rem;
        }

        /* Money Cards Display */
        .money-cards {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .money-card {
            width: 80px;
            height: 50px;
            background: linear-gradient(135deg, #00ff87, #00b359);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1a2e;
            font-weight: bold;
            font-size: 0.8rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        /* Loading Animation */
        .loading {
            display: none;
            text-align: center;
            margin: 1rem 0;
        }

        .spinner {
            border: 3px solid rgba(0, 255, 135, 0.3);
            border-top: 3px solid #00ff87;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .game-title {
                font-size: 2.5rem;
            }

            .scratch-card-container {
                padding: 1rem;
            }

            .money-cards {
                gap: 0.5rem;
            }

            .money-card {
                width: 60px;
                height: 40px;
                font-size: 0.7rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <!-- Game Section -->
        <section class="game-section">
            <h1 class="game-title">RASPE AQUI!</h1>
            <p class="game-subtitle">
                Raspe e revele os prêmios escondidos.<br>
                3 símbolos iguais e você ganha o valor!
            </p>

            @guest
                <!-- Usuário não logado -->
                <div class="scratch-card-container">
                    <div style="text-align: center; padding: 2rem;">
                        <h3 style="color: #00ff87; margin-bottom: 1rem;">Faça login para jogar!</h3>
                        <p style="margin-bottom: 2rem;">Entre na sua conta para começar a raspar e ganhar prêmios incríveis.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary" style="margin-right: 1rem;">Entrar</a>
                        <a href="{{ route('register') }}" class="btn">Cadastrar</a>
                    </div>
                </div>
            @else
                <!-- Usuário logado - Jogo -->
                <div class="scratch-card-container">
                    <div id="gameInstructions"
                        style="display: none; background: rgba(0, 255, 135, 0.1); border: 1px solid rgba(0, 255, 135, 0.3); border-radius: 15px; padding: 1rem; margin-bottom: 1rem; text-align: center;">
                        <div style="color: #00ff87; font-weight: bold; margin-bottom: 0.5rem;">👆 Clique nos quadrados para
                            raspar!</div>
                        <div style="color: #ccc; font-size: 0.9rem;">Encontre 3 símbolos iguais para ganhar o prêmio</div>
                    </div>

                    <div class="prize-info" id="prizeInfo" style="display: none;">
                        <div class="prize-text">Parabéns! Você ganhou:</div>
                        <div class="prize-amount" id="prizeAmount">R$ 0,00</div>
                    </div>

                    <div class="scratch-card" id="scratchCard">
                        <div class="scratch-cell" data-pos="0,0"></div>
                        <div class="scratch-cell" data-pos="0,1"></div>
                        <div class="scratch-cell" data-pos="0,2"></div>
                        <div class="scratch-cell" data-pos="1,0"></div>
                        <div class="scratch-cell" data-pos="1,1"></div>
                        <div class="scratch-cell" data-pos="1,2"></div>
                        <div class="scratch-cell" data-pos="2,0"></div>
                        <div class="scratch-cell" data-pos="2,1"></div>
                        <div class="scratch-cell" data-pos="2,2"></div>
                    </div>

                    <div class="loading" id="loading">
                        <div class="spinner"></div>
                        <p>Gerando sua raspadinha...</p>
                    </div>

                    <button class="play-button" id="playButton">
                        💰 Comprar Raspadinha - R$ {{ number_format($betAmount, 2, ',', '.') }}
                    </button>
                </div>
            @endguest

            <!-- Instructions -->
            <div class="instructions">
                <h3>CONTEÚDO DESSA RASPADINHA:</h3>
                <ul>
                    <li>Raspe 3 símbolos iguais e ganhe o valor correspondente</li>
                    <li>Linha completa multiplica o prêmio por 2x</li>
                    <li>Diagonal completa multiplica o prêmio por 3x</li>
                    <li>4 cantos iguais multiplica o prêmio por 4x</li>
                    <li>Prêmios de R$ 0,50 até R$ 2.000,00</li>
                    <li>Cada raspadinha custa apenas R$ {{ number_format($betAmount, 2, ',', '.') }}</li>
                </ul>
            </div>
        </section>
    </div>

    <!-- Modal de Vitória -->
    <div id="winModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 2000; backdrop-filter: blur(5px);">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: linear-gradient(145deg, #2a2a3e, #1a1a2e); padding: 3rem; border-radius: 20px; border: 3px solid #ffd700; box-shadow: 0 0 50px rgba(255, 215, 0, 0.5); max-width: 500px; text-align: center; z-index: 2001;">
            <div style="font-size: 6rem; margin-bottom: 1rem; animation: bounce 1s ease infinite;">🎉</div>
            <h2 style="color: #ffd700; margin-bottom: 1rem; font-size: 2.5rem; text-shadow: 0 0 20px rgba(255, 215, 0, 0.5);">PARABÉNS!</h2>
            <div id="winType" style="color: #00ff87; font-size: 1.3rem; margin-bottom: 1rem; font-weight: bold;"></div>
            <div style="color: #ffffff; font-size: 1.2rem; margin-bottom: 1rem;">Você ganhou:</div>
            <div id="winAmount" style="color: #ffd700; font-size: 3rem; font-weight: bold; margin-bottom: 2rem; text-shadow: 0 0 20px rgba(255, 215, 0, 0.8);"></div>
            <button class="btn btn-primary" onclick="closeWinModal()" style="padding: 1rem 3rem; font-size: 1.3rem; background: linear-gradient(135deg, #ffd700, #ffed4e); color: #1a1a2e;">
                Continuar Jogando 🎰
            </button>
        </div>
    </div>

    <!-- Modal de Derrota -->
    <div id="loseModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 2000; backdrop-filter: blur(5px);">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: linear-gradient(145deg, #2a2a3e, #1a1a2e); padding: 3rem; border-radius: 20px; border: 3px solid #ff6b6b; box-shadow: 0 0 50px rgba(255, 107, 107, 0.5); max-width: 500px; text-align: center; z-index: 2001;">
            <div style="font-size: 6rem; margin-bottom: 1rem; animation: shake 1s ease infinite;">😔</div>
            <h2 style="color: #ff6b6b; margin-bottom: 1rem; font-size: 2.5rem; text-shadow: 0 0 20px rgba(255, 107, 107, 0.5);">QUE PENA!</h2>
            <div style="color: #cccccc; font-size: 1.3rem; margin-bottom: 1rem; font-weight: bold;">Não foi dessa vez...</div>
            <div style="color: #ffffff; font-size: 1.2rem; margin-bottom: 1rem;">Mas não desista!</div>
            <div style="color: #ff6b6b; font-size: 1.5rem; font-weight: bold; margin-bottom: 2rem;">A próxima pode ser a sua! 🍀</div>
            <button class="btn btn-primary" onclick="closeLoseModal()" style="padding: 1rem 3rem; font-size: 1.3rem; background: linear-gradient(135deg, #00ff87, #00b359); color: #1a1a2e;">
                Tentar Novamente 🎯
            </button>
        </div>
    </div>
@endsection

@auth
    @push('scripts')
        <script>
            // Variáveis do jogo
            let currentBalance = {{ auth()->user()->wallet->balance }};
            let betAmount = {{ $betAmount }};
            let gameData = null;
            let isPlaying = false;
            let revealedCells = 0;
            let totalCells = 9;

            // Elementos DOM
            const playButton = document.getElementById('playButton');
            const scratchCard = document.getElementById('scratchCard');
            const prizeInfo = document.getElementById('prizeInfo');
            const prizeAmount = document.getElementById('prizeAmount');
            const loading = document.getElementById('loading');
            const gameInstructions = document.getElementById('gameInstructions');

            // Event listener para o botão de jogar
            playButton.addEventListener('click', () => {
                if (isPlaying) return;

                if (currentBalance < betAmount) {
                    alert('Saldo insuficiente! Faça um depósito para continuar.');
                    return;
                }

                startGame();
            });

            // Event listeners para as células (raspar)
            scratchCard.addEventListener('click', (e) => {
                if (e.target.classList.contains('scratch-cell') && gameData && !e.target.classList.contains('revealed')) {
                    revealCell(e.target);
                }
            });

            function startGame() {
                isPlaying = true;
                playButton.disabled = true;
                playButton.textContent = 'Comprando...';
                loading.style.display = 'block';
                prizeInfo.style.display = 'none';
                revealedCells = 0;

                balanceGame = currentBalance - 1;
                updateBalance(balanceGame);

                // Reset das células
                const cells = scratchCard.querySelectorAll('.scratch-cell');
                cells.forEach(cell => {
                    cell.classList.remove('revealed', 'winning');
                    cell.textContent = '';
                    cell.style.pointerEvents = 'auto';
                    cell.style.cursor = 'pointer';
                });

                // Chamada para o backend Laravel
                fetch('{{ route("game.play") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                    .then(response => response.json())
                    .then(data => {
                        loading.style.display = 'none';

                        if (data.success) {
                            gameData = data.card;
                            currentBalance = data.new_balance;

                            // Habilitar as células para clique
                            playButton.textContent = '🎮 Raspe os Quadrados!';
                            playButton.style.background = 'linear-gradient(135deg, #ff6b6b, #ee5a52)';

                            // Mostrar instruções
                            gameInstructions.style.display = 'block';

                        } else {
                            alert(data.error || 'Erro ao processar o jogo');
                            resetGame();
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        loading.style.display = 'none';
                        alert('Erro de conexão. Tente novamente.');
                        resetGame();
                    });
            }

            // Função para atualizar saldo com efeito visual
            function updateBalance(newBalance) {
                const balanceElement = document.getElementById('balance');
                const balanceMobileElement = document.getElementById('balance-mobile');
                
                [balanceElement, balanceMobileElement].forEach(element => {
                    if (element) {
                        // Converter para número antes de usar toFixed
                        const balance = parseFloat(newBalance);
                        const newText = 'R$ ' + balance.toFixed(2).replace('.', ',');
                        
                        // Efeito de atualização
                        element.style.transform = 'scale(1.1)';
                        element.style.background = 'linear-gradient(135deg, #ffd700, #ffed4e)';
                        element.style.color = '#1a1a2e';
                        
                        setTimeout(() => {
                            element.textContent = newText;
                            
                            setTimeout(() => {
                                element.style.transform = 'scale(1)';
                                element.style.background = 'linear-gradient(135deg, #00ff87, #00b359)';
                                element.style.color = '#1a1a2e';
                            }, 300);
                        }, 200);
                    }
                });
            }

            function revealCell(cell) {
                // Verificar se gameData existe
                if (!gameData || !gameData.grid) {
                    console.error('gameData or grid is null');
                    return;
                }

                const pos = cell.getAttribute('data-pos').split(',');
                const row = parseInt(pos[0]);
                const col = parseInt(pos[1]);
                const value = gameData.grid[row][col];

                // Valores para display com símbolos modernos
                const displayValues = {
                    50: '$ 0.50',
                    100: '$ 1.00',
                    200: '$ 2.00',
                    500: '$ 5.00',
                    1000: '$ 10.00',
                    2000: '$ 20.00',
                    5000: '$ 50.00',
                    10000: '$ 100',
                    20000: '$ 200',
                    50000: '$ 500',
                    100000: '$ 1K',
                    200000: '$ 2K'
                };

                // Efeito de raspagem mais suave
                cell.style.transform = 'scale(0.95)';
                cell.style.filter = 'blur(3px)';
                setTimeout(() => {
                    cell.textContent = displayValues[value] || `$ ${(value / 100).toFixed(2)}`;
                    cell.classList.add('revealed');
                    cell.style.transform = 'scale(1)';
                    cell.style.filter = 'blur(0px)';
                    cell.style.pointerEvents = 'none';
                    cell.style.cursor = 'default';

                    revealedCells++;

                    // Verificar se todas as células foram reveladas
                    if (revealedCells >= totalCells) {
                        setTimeout(() => {
                            finishGame();
                        }, 500);
                    }
                }, 300);
            }

            function highlightWinningPattern() {
                // Verificar se gameData existe
                if (!gameData || !gameData.grid) return;

                // Destacar todas as células vencedoras
                const cells = scratchCard.querySelectorAll('.scratch-cell');
                cells.forEach(cell => {
                    const pos = cell.getAttribute('data-pos').split(',');
                    const row = parseInt(pos[0]);
                    const col = parseInt(pos[1]);
                    const value = gameData.grid[row][col];

                    if (gameData.winning_value && value == gameData.winning_value) {
                        cell.classList.add('winning');
                    }
                });
            }

            function finishGame() {
                // Verificar se gameData existe antes de usar
                if (!gameData) {
                    console.error('gameData is null in finishGame');
                    resetGame();
                    return;
                }

                if (gameData.prize > 0) {
                    // Destacar células vencedoras
                    highlightWinningPattern();

                    // Atualizar saldo na interface
                    updateBalance(currentBalance);

                    // Garantir que o prize seja um número
                    const prize = parseFloat(gameData.prize);
                    prizeAmount.textContent = `R$ ${prize.toFixed(2).replace('.', ',')}`;
                    prizeInfo.style.display = 'block';

                    // Mostrar modal de vitória com delay
                    setTimeout(() => {
                        const winType = gameData.win_type || 'three_same';
                        showWinModal(winType, prize);
                    }, 1000);
                } else {
                    // Mostrar modal de derrota
                    setTimeout(() => {
                        showLoseModal();
                    }, 1000);
                }
                
                // Aguardar antes de resetar
                setTimeout(() => {
                    resetGame();
                }, 2000);
            }

            function resetGame() {
                isPlaying = false;
                playButton.disabled = false;
                playButton.textContent = '💰 Comprar Raspadinha - R$ ' + betAmount.toFixed(2).replace('.', ',');
                playButton.style.background = 'linear-gradient(135deg, #00ff87, #00b359)';
                revealedCells = 0;
                gameData = null;
                gameInstructions.style.display = 'none';
            }

            // Função para mostrar modal de vitória
            function showWinModal(winType, amount) {
                const winMessages = {
                    'three_same': '3 Símbolos Iguais!',
                    'horizontal_line': 'Linha Completa!',
                    'vertical_line': 'Coluna Completa!',
                    'diagonal': 'Diagonal Completa!',
                    'corners': '4 Cantos Iguais!'
                };

                document.getElementById('winType').textContent = winMessages[winType] || 'Você Ganhou!';
                document.getElementById('winAmount').textContent = `R$ ${amount.toFixed(2).replace('.', ',')}`;
                document.getElementById('winModal').style.display = 'block';
                
                // Efeito de fogos de artifício
                setTimeout(() => {
                    createFireworks();
                }, 500);
            }

            function closeWinModal() {
                document.getElementById('winModal').style.display = 'none';
            }

            // Função para mostrar modal de derrota
            function showLoseModal() {
                document.getElementById('loseModal').style.display = 'block';
                
                // Efeito de chuva de lágrimas
                setTimeout(() => {
                    createRaindrops();
                }, 500);
            }

            function closeLoseModal() {
                document.getElementById('loseModal').style.display = 'none';
            }

            // Efeito de fogos de artifício para vitórias
            function createFireworks() {
                const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7', '#dda0dd', '#98d8c8'];
                
                for (let i = 0; i < 15; i++) {
                    setTimeout(() => {
                        const firework = document.createElement('div');
                        firework.style.cssText = `
                            position: fixed;
                            top: ${Math.random() * 50 + 20}%;
                            left: ${Math.random() * 80 + 10}%;
                            width: 6px;
                            height: 6px;
                            background: ${colors[Math.floor(Math.random() * colors.length)]};
                            border-radius: 50%;
                            z-index: 3000;
                            pointer-events: none;
                            animation: fireworkExplode 1.5s ease-out forwards;
                        `;
                        document.body.appendChild(firework);
                        
                        setTimeout(() => firework.remove(), 1500);
                    }, i * 100);
                }
            }

            // Efeito de chuva de lágrimas para derrota
            function createRaindrops() {
                const colors = ['#87ceeb', '#4682b4', '#6495ed', '#b0c4de', '#add8e6', '#e0f6ff', '#b8e6f0'];
                
                for (let i = 0; i < 20; i++) {
                    setTimeout(() => {
                        const raindrop = document.createElement('div');
                        raindrop.style.cssText = `
                            position: fixed;
                            top: -10px;
                            left: ${Math.random() * 100}%;
                            width: 3px;
                            height: ${Math.random() * 20 + 10}px;
                            background: ${colors[Math.floor(Math.random() * colors.length)]};
                            border-radius: 50%;
                            z-index: 3000;
                            pointer-events: none;
                            animation: raindropFall 2s linear forwards;
                            opacity: 0.7;
                        `;
                        document.body.appendChild(raindrop);
                        
                        setTimeout(() => raindrop.remove(), 2000);
                    }, i * 100);
                }
            }

            // Fechar modais clicando fora ou com ESC
            window.addEventListener('click', function(e) {
                if (e.target.id === 'winModal') {
                    closeWinModal();
                }
                if (e.target.id === 'loseModal') {
                    closeLoseModal();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeWinModal();
                    closeLoseModal();
                }
            });

            // CSS para animações dos efeitos visuais
            const effectsStyle = document.createElement('style');
            effectsStyle.textContent = `
                @keyframes bounce {
                    0%, 20%, 50%, 80%, 100% {
                        transform: translateY(0);
                    }
                    40% {
                        transform: translateY(-10px);
                    }
                    60% {
                        transform: translateY(-5px);
                    }
                }

                @keyframes shake {
                    0%, 100% { 
                        transform: translateX(0); 
                    }
                    10%, 30%, 50%, 70%, 90% { 
                        transform: translateX(-5px); 
                    }
                    20%, 40%, 60%, 80% { 
                        transform: translateX(5px); 
                    }
                }

                @keyframes fireworkExplode {
                    0% {
                        transform: scale(0);
                        opacity: 1;
                    }
                    50% {
                        transform: scale(8);
                        opacity: 0.8;
                    }
                    100% {
                        transform: scale(12);
                        opacity: 0;
                    }
                }

                @keyframes raindropFall {
                    0% {
                        transform: translateY(-10px);
                        opacity: 0.7;
                    }
                    100% {
                        transform: translateY(100vh);
                        opacity: 0;
                    }
                }

                .scratch-cell {
                    position: relative;
                    overflow: hidden;
                }

                .scratch-cell:not(.revealed) {
                    cursor: pointer !important;
                }

                .scratch-cell:not(.revealed):hover {
                    transform: scale(1.05) !important;
                    box-shadow: 0 5px 15px rgba(0, 255, 135, 0.4) !important;
                }

                .scratch-cell:not(.revealed):active {
                    transform: scale(0.95) !important;
                }
            `;
            document.head.appendChild(effectsStyle);
        </script>
    @endpush
@endauth