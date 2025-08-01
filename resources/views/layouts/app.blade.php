<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Raspadinha Online')</title>
    
    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: #ffffff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0, 255, 135, 0.2);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            color: #00ff87;
            text-decoration: none;
        }

        .logo::before {
            content: "🎲";
            font-size: 2rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .balance {
            background: linear-gradient(135deg, #00ff87, #00b359);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: bold;
            font-size: 1.1rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            box-shadow: 0 4px 15px rgba(0, 255, 135, 0.3);
            animation: balancePulse 2s ease-in-out infinite;
        }

        @keyframes balancePulse {
            0%, 100% { 
                box-shadow: 0 4px 15px rgba(0, 255, 135, 0.3); 
            }
            50% { 
                box-shadow: 0 4px 20px rgba(0, 255, 135, 0.5); 
            }
        }

        .btn {
            background: transparent;
            border: 2px solid #00ff87;
            color: #00ff87;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: #00ff87;
            color: #1a1a2e;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #00ff87, #00b359);
            border: none;
            color: #1a1a2e;
            box-shadow: 0 5px 15px rgba(0, 255, 135, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 255, 135, 0.5);
        }

        .btn:disabled {
            background: #666;
            border-color: #666;
            color: #999;
            cursor: not-allowed;
            transform: none;
        }

        /* Botões de autenticação - ocultos no mobile */
        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Menu Hambúrguer */
        .hamburger-menu {
            display: none;
            position: relative;
        }

        .hamburger-btn {
            background: none;
            border: 2px solid #00ff87;
            color: #00ff87;
            padding: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 3px;
            width: 40px;
            height: 40px;
            justify-content: center;
            align-items: center;
        }

        .hamburger-btn:hover {
            background: rgba(0, 255, 135, 0.1);
            transform: scale(1.05);
        }

        .hamburger-line {
            width: 20px;
            height: 2px;
            background: #00ff87;
            transition: all 0.3s ease;
        }

        .hamburger-btn.active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger-btn.active .hamburger-line:nth-child(2) {
            opacity: 0;
        }

        .hamburger-btn.active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid #00ff87;
            border-radius: 15px;
            padding: 1rem;
            min-width: 200px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            display: none;
            flex-direction: column;
            gap: 0.8rem;
            margin-top: 10px;
            z-index: 1000;
        }

        .dropdown-menu.active {
            display: flex;
            animation: dropdownSlide 0.3s ease-out;
        }

        @keyframes dropdownSlide {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-menu .btn {
            width: 100%;
            text-align: center;
            margin: 0;
            white-space: nowrap;
        }

        .dropdown-menu form {
            width: 100%;
        }

        .dropdown-menu form .btn {
            width: 100%;
        }

        /* Mobile user info simplificada */
        .mobile-user-info {
            display: none;
            align-items: center;
            gap: 0.8rem;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            padding-top: 6rem; /* Espaço para o header fixo */
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            border-left: 4px solid;
        }

        .alert-success {
            background: rgba(0, 255, 135, 0.1);
            border-color: #00ff87;
            color: #00ff87;
        }

        .alert-error {
            background: rgba(255, 107, 107, 0.1);
            border-color: #ff6b6b;
            color: #ff6b6b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }

            .logo {
                font-size: 1.2rem;
            }

            .logo::before {
                font-size: 1.5rem;
            }

            /* Ocultar botões de autenticação no mobile */
            .auth-buttons {
                display: none;
            }

            /* Ocultar user-info desktop e mostrar versão mobile */
            .user-info {
                display: none;
            }

            .mobile-user-info {
                display: flex;
            }

            /* Mostrar menu hambúrguer no mobile */
            .hamburger-menu {
                display: block;
            }

            .balance {
                font-size: 0.9rem;
                padding: 0.4rem 0.8rem;
            }

            .btn {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }

            .container {
                padding: 1rem;
                padding-top: 5rem;
            }
        }

        /* Para telas muito pequenas (menos de 480px) */
        @media (max-width: 480px) {
            .header {
                padding: 0.8rem;
            }

            .mobile-user-info {
                gap: 0.5rem;
            }

            .balance {
                font-size: 0.8rem;
                padding: 0.3rem 0.6rem;
            }

            .btn {
                font-size: 0.7rem;
                padding: 0.3rem 0.6rem;
            }

            .hamburger-btn {
                width: 35px;
                height: 35px;
            }

            .hamburger-line {
                width: 18px;
            }
        }

        /* Animações dos Modais */
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

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }

        .modal-backdrop {
            animation: modalFadeIn 0.3s ease-out;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <a href="{{ route('game.index') }}" class="logo">
            RaspaKing
        </a>
        
        @auth
        <!-- Desktop user info -->
        <div class="user-info">
            <div class="balance" id="balance">
                R$ {{ number_format(auth()->user()->wallet->balance, 2, ',', '.') }}
            </div>
            <a href="#" class="btn" onclick="openDepositModal()">Depositar</a>
            <a href="{{ route('game.history') }}" class="btn">Histórico</a>
            
            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn" style="background: none; border: 1px solid #666; color: #999;">
                    Sair
                </button>
            </form>
        </div>

        <!-- Mobile user info -->
        <div class="mobile-user-info">
            <div class="balance" id="balance-mobile">
                R$ {{ number_format(auth()->user()->wallet->balance, 2, ',', '.') }}
            </div>
            <a href="#" class="btn btn-primary" onclick="openDepositModal()">Depositar</a>
            
            <!-- Menu Hambúrguer -->
            <div class="hamburger-menu">
                <button class="hamburger-btn" onclick="toggleMenu()">
                    <div class="hamburger-line"></div>
                    <div class="hamburger-line"></div>
                    <div class="hamburger-line"></div>
                </button>
                
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="{{ route('game.history') }}" class="btn">📊 Histórico</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn" style="background: none; border: 1px solid #666; color: #999;">
                            🚪 Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @else
        <!-- Botões de autenticação - ocultos no mobile -->
        <div class="auth-buttons">
            <a href="{{ route('login') }}" class="btn">Entrar</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Cadastrar</a>
        </div>
        @endauth
    </header>

    <!-- Alerts -->
    @if(session('success'))
        <div class="container">
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container">
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Modal de Depósito -->
    @auth
    <div id="depositModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #1a1a2e; padding: 2rem; border-radius: 15px; border: 2px solid #00ff87; z-index: 1001;">
            <h3 style="color: #00ff87; margin-bottom: 1rem;">Fazer Depósito</h3>
            <form id="depositForm">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem;">Valor:</label>
                    <input type="number" name="amount" min="10" step="0.01" required 
                           style="width: 100%; padding: 0.5rem; border-radius: 5px; border: 1px solid #666; background: #2a2a3e; color: white;">
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Depositar</button>
                    <button type="button" class="btn" onclick="closeDepositModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    @endauth

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

    <script>
        // CSRF Token para requisições AJAX
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Função para toggle do menu hambúrguer
        function toggleMenu() {
            const hamburgerBtn = document.querySelector('.hamburger-btn');
            const dropdownMenu = document.getElementById('dropdownMenu');
            
            hamburgerBtn.classList.toggle('active');
            dropdownMenu.classList.toggle('active');
        }

        // Fechar menu ao clicar fora
        document.addEventListener('click', function(e) {
            const hamburgerMenu = document.querySelector('.hamburger-menu');
            const dropdownMenu = document.getElementById('dropdownMenu');
            const hamburgerBtn = document.querySelector('.hamburger-btn');
            
            if (!hamburgerMenu.contains(e.target)) {
                dropdownMenu.classList.remove('active');
                hamburgerBtn.classList.remove('active');
            }
        });
        
        // Funções globais para modal de depósito
        function openDepositModal() {
            document.getElementById('depositModal').style.display = 'block';
            // Fechar menu hambúrguer se estiver aberto
            document.getElementById('dropdownMenu').classList.remove('active');
            document.querySelector('.hamburger-btn').classList.remove('active');
        }
        
        function closeDepositModal() {
            document.getElementById('depositModal').style.display = 'none';
        }
        
        // Submissão do formulário de depósito
        document.getElementById('depositForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Funcionalidade de depósito será implementada com gateway de pagamento');
            closeDepositModal();
        });

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
            
            // Fechar menu hambúrguer se estiver aberto
            document.getElementById('dropdownMenu').classList.remove('active');
            document.querySelector('.hamburger-btn').classList.remove('active');
            
            // Efeito de fogos de artifício
            setTimeout(() => {
                createFireworks();
            }, 500);
        }

        function closeWinModal() {
            document.getElementById('winModal').style.display = 'none';
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

        // CSS para animação de fogos
        const fireworkStyle = document.createElement('style');
        fireworkStyle.textContent = `
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
        `;
        document.head.appendChild(fireworkStyle);

        // Fechar modais clicando fora ou com ESC
        window.addEventListener('click', function(e) {
            if (e.target.id === 'winModal') {
                closeWinModal();
            }
            if (e.target.id === 'depositModal') {
                closeDepositModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeWinModal();
                closeDepositModal();
                // Fechar menu hambúrguer
                document.getElementById('dropdownMenu').classList.remove('active');
                document.querySelector('.hamburger-btn').classList.remove('active');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>