// Função para processar resultado do jogo após raspar tudo
    function processGameResult() {
        if (!gameData) return;

        // Se ganhou, destacar células vencedoras e mostrar prêmio
        if (gameData.prize > 0) {
            highlightWinningPattern();
            
            // Mostrar informação do prêmio
            const prize = parseFloat(gameData.prize);
            prizeAmount.textContent = `R$ ${prize.toFixed(2).replace('.', ',')}`;
            prizeInfo.style.display = 'block';
        }
    }    // Função para preparar as cartas com valores escondidos
    function prepareCards() {
        if (!gameData || !gameData.grid) return;

        const displayValues = {
            50: 'R$ 0,50',
            100: 'R$ 1,00',
            200: 'R$ 2,00',
            500: 'R$ 5,00',
            1000: 'R$ 10,00',
            2000: 'R$ 20,00',
            5000: 'R$ 50,00',
            10000: 'R$ 100,00',
            20000: 'R$ 200,00',
            50000: 'R$ 500,00',
            100000: 'R$ 1.000,00',
            200000: 'R$ 2.000,00'
        };

        const cells = scratchCard.querySelectorAll('.scratch-cell');
        cells.forEach((cell, index) => {
            const row = Math.floor(index / 3);
            const col = index % 3;
            const value = gameData.grid[row][col];
            
            // Criar elemento para o valor escondido
            const valueElement = document.createElement('div');
            valueElement.className = 'cell-value';
            valueElement.textContent = displayValues[value] || `R$ ${(value / 100).toFixed(2)}`;
            
            // Limpar célula e adicionar valor escondido
            cell.innerHTML = '';
            cell.appendChild(valueElement);
            
            // Reset dos estilos
            cell.classList.remove('revealed', 'winning', 'scratching');
            cell.style.pointerEvents = 'auto';
            cell.style.cursor = 'pointer';
        });
    }<!-- resources/views/game/index.blade.php -->
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
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
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
        background: linear-gradient(135deg, #ffffff, #f8f8f8);
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
        border: 2px solid #ddd;
    }

    .scratch-cell .cell-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        font-size: 1.2rem;
        font-weight: bold;
        color: #1a1a2e;
        pointer-events: none;
    }

    .scratch-cell .scratch-layer {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #c0c0c0, #a0a0a0);
        background-image: 
            radial-gradient(circle at 25% 25%, rgba(255,255,255,0.4) 2px, transparent 2px),
            radial-gradient(circle at 75% 75%, rgba(0,0,0,0.2) 1px, transparent 1px);
        background-size: 12px 12px;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        font-size: 1.5rem;
        cursor: pointer;
        user-select: none;
        border-radius: 15px;
    }

    .scratch-cell .scratch-layer::before {
        content: "💰";
        opacity: 0.7;
    }

    .scratch-cell .scratch-layer::before {
        content: "💰";
        opacity: 0.7;
    }

    .scratch-cell:not(.fully-revealed):hover .scratch-layer {
        background: linear-gradient(135deg, #d0d0d0, #b0b0b0);
        transform: scale(1.02);
    }

    .scratch-cell:not(.fully-revealed):active .scratch-layer {
        transform: scale(0.98);
    }

    .scratch-cell.fully-revealed .scratch-layer {
        display: none;
    }

    .scratch-cell.winning {
        background: linear-gradient(135deg, #ffd700, #ffed4e) !important;
        border-color: #ffb700 !important;
        animation: pulse 1.5s ease infinite;
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.6) !important;
    }

    .scratch-cell.winning .cell-value {
        color: #1a1a2e;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }

    @keyframes reveal {
        from { transform: scale(0.8); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
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
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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
        <h1 class="game-title">RASPETAQUI!</h1>
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
            <div id="gameInstructions" style="display: none; background: rgba(0, 255, 135, 0.1); border: 1px solid rgba(0, 255, 135, 0.3); border-radius: 15px; padding: 1rem; margin-bottom: 1rem; text-align: center;">
                <div style="color: #00ff87; font-weight: bold; margin-bottom: 0.5rem;">👆 Clique nos quadrados para revelar!</div>
                <div style="color: #ccc; font-size: 0.9rem;">Clique em cada quadrado para revelar os valores. Encontre 3 símbolos iguais para ganhar!</div>
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

        <!-- Money Cards Display -->
        <div class="money-cards">
            <div class="money-card">R$ 0,50</div>
            <div class="money-card">R$ 1,00</div>
            <div class="money-card">R$ 2,00</div>
            <div class="money-card">R$ 5,00</div>
            <div class="money-card">R$ 10,00</div>
            <div class="money-card">R$ 20,00</div>
            <div class="money-card">R$ 50,00</div>
            <div class="money-card">R$ 100,00</div>
            <div class="money-card">R$ 200,00</div>
            <div class="money-card">R$ 500,00</div>
            <div class="money-card">R$ 1.000</div>
            <div class="money-card">R$ 2.000</div>
        </div>

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
    let gameId = null; // ID do jogo atual

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
    scratchCard.addEventListener('mousedown', startScratch);
    scratchCard.addEventListener('mousemove', scratch);
    scratchCard.addEventListener('mouseup', stopScratch);
    scratchCard.addEventListener('mouseleave', stopScratch);
    
    // Touch events para mobile
    scratchCard.addEventListener('touchstart', startScratchTouch);
    scratchCard.addEventListener('touchmove', scratchTouch);
    scratchCard.addEventListener('touchend', stopScratch);

    let isScratching = false;
    let scratchedCells = new Set();

    function startGame() {
        isPlaying = true;
        playButton.disabled = true;
        playButton.textContent = 'Comprando...';
        loading.style.display = 'block';
        prizeInfo.style.display = 'none';
        revealedCells = 0;
        
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
                // Atualizar saldo imediatamente (já foi debitado no backend)
                currentBalance = parseFloat(data.card.new_balance);
                updateBalance(currentBalance);
                
                // Debug: verificar se gameData foi recebido corretamente
                console.log('GameData recebido:', gameData);
                
                // Preparar as células com os valores escondidos
                prepareCards();
                
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

    function showInstruction() {
        // Criar overlay de instrução temporário
        const instruction = document.createElement('div');
        instruction.innerHTML = `
            <div style="
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgba(0, 255, 135, 0.95);
                color: #1a1a2e;
                padding: 1rem 2rem;
                border-radius: 15px;
                font-weight: bold;
                font-size: 1.2rem;
                z-index: 1000;
                animation: pulse 0.5s ease;
            ">
                👆 Clique nos quadrados para raspar!
            </div>
        `;
        document.body.appendChild(instruction);
        
        setTimeout(() => {
            instruction.remove();
        }, 2000);
    }

    function revealCell(cell) {
        // Verificar se gameData existe
        if (!gameData || !gameData.grid) {
            console.error('gameData or grid is null');
            return;
        }

        // Começar animação de raspagem
        cell.classList.add('scratching');
        cell.style.pointerEvents = 'none';
        
        // Após a animação, revelar o valor
        setTimeout(() => {
            cell.classList.remove('scratching');
            cell.classList.add('revealed');
            
            revealedCells++;
            
            // Verificar se todas as células foram reveladas
            if (revealedCells >= totalCells) {
                setTimeout(() => {
                    // Agora sim, processar o resultado e atualizar saldo
                    processGameResult();
                    finishGame();
                }, 300);
            }
        }, 800); // Tempo da animação de raspagem
    }

    // Função para finalizar o jogo no servidor
    function finishGameOnServer() {
        if (!gameId) {
            console.error('Game ID não encontrado');
            return;
        }

        fetch('{{ route("game.finish") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                game_id: gameId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Agora temos as informações completas do resultado
                gameData.prize = data.prize;
                gameData.win_type = data.win_type;
                gameData.winning_value = data.winning_value;
                gameData.is_winner = data.is_winner;
                
                // Atualizar saldo se ganhou
                if (data.new_balance) {
                    currentBalance = data.new_balance;
                    updateBalance(currentBalance);
                }
                
                // Processar resultado visual
                processGameResult();
                finishGame();
            } else {
                console.error('Erro ao finalizar jogo:', data.error);
                alert('Erro ao processar resultado do jogo');
                resetGame();
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('Erro de conexão ao finalizar jogo');
            resetGame();
        });
    }

    // Função para processar resultado visual
    function processGameResult() {
        if (!gameData || !gameData.is_winner) return;

        // Destacar células vencedoras
        highlightWinningPattern();
        
        // Mostrar informação do prêmio
        const prize = parseFloat(gameData.prize);
        prizeAmount.textContent = `R$ ${prize.toFixed(2).replace('.', ',')}`;
        prizeInfo.style.display = 'block';
    }

    function highlightWinningPattern() {
        if (!gameData || !gameData.grid || !gameData.winning_value) return;

        // Destacar todas as células vencedoras
        const cells = scratchCard.querySelectorAll('.scratch-cell');
        cells.forEach((cell, index) => {
            const row = Math.floor(index / 3);
            const col = index % 3;
            const value = gameData.grid[row][col];
            
            if (value == gameData.winning_value) {
                cell.classList.add('winning');
            }
        });
    }

    function finishGame() {
        // Debug: verificar gameData
        console.log('finishGame - gameData:', gameData);
        
        // Verificar se gameData existe antes de usar
        if (!gameData) {
            console.error('gameData is null in finishGame');
            resetGame();
            return;
        }

        if (gameData.prize > 0) {
            // Mostrar modal de vitória com delay para ver o efeito
            setTimeout(() => {
                const winType = gameData.win_type || 'three_same';
                const prize = parseFloat(gameData.prize);
                console.log('Showing win modal with:', winType, prize);
                showWinModal(winType, prize);
            }, 1000);
        } else {
            // Alert simples para derrota
            setTimeout(() => {
                const sadMessages = [
                    'Não foi dessa vez! 😔',
                    'Quase lá! Tente novamente! 🎯',
                    'A sorte está chegando! 🍀'
                ];
                alert(sadMessages[Math.floor(Math.random() * sadMessages.length)]);
            }, 500);
        }
        
        // Aguardar um pouco antes de resetar para permitir que o modal apareça
        setTimeout(() => {
            resetGame();
        }, 2000);
    }

    function highlightWinningPattern() {
        // Verificar se gameData existe
        if (!gameData || !gameData.grid) return;

        // Destacar todas as células vencedoras
        const cells = scratchCard.querySelectorAll('.scratch-cell');
        cells.forEach((cell, index) => {
            const row = Math.floor(index / 3);
            const col = index % 3;
            const value = gameData.grid[row][col];
            
            if (gameData.winning_value && value == gameData.winning_value) {
                cell.classList.add('winning');
            }
        });
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

    // CSS para animação adicional
    const style = document.createElement('style');
    style.textContent = `
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
    document.head.appendChild(style);
</script>
@endpush
@endauth