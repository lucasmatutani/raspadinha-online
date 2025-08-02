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
        /* Animação de loading */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsividade do modal */
        @media (max-width: 480px) {
            #depositModal, #withdrawModal {
                padding: 10px 0;
            }

            #depositModal>div, #withdrawModal>div {
                width: 95%;
                padding: 1.5rem;
                margin: 10px auto;
            }

            #depositModal h3, #withdrawModal h3 {
                font-size: 1.3rem;
            }

            #depositModal input, #withdrawModal input {
                padding: 0.8rem;
                font-size: 1rem;
            }

            #depositModal .btn, #withdrawModal .btn {
                padding: 0.8rem;
                font-size: 1rem;
            }
        }

        /* Melhor scroll no mobile */
        @media (max-height: 700px) {
            #depositModal, #withdrawModal {
                padding: 5px 0;
            }

            #depositModal>div, #withdrawModal>div {
                margin: 5px auto;
                padding: 1.5rem;
            }
        }

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

            0%,
            100% {
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

        /* Botão de saque com cor diferente */
        .btn-warning {
            background: linear-gradient(135deg, #ffa500, #ff8c00);
            border: 2px solid #ffa500;
            color: #1a1a2e;
            box-shadow: 0 5px 15px rgba(255, 165, 0, 0.3);
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #ff8c00, #ff7700);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 165, 0, 0.5);
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
            padding-top: 6rem;
            /* Espaço para o header fixo */
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

            0%,
            20%,
            50%,
            80%,
            100% {
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
            <a href="#" class="btn btn-warning" onclick="openWithdrawModal()">Sacar</a>
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
                    <a href="#" class="btn btn-warning" onclick="openWithdrawModal()">💰 Sacar</a>
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
    <div id="depositModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; backdrop-filter: blur(5px); overflow-y: auto; padding: 20px 0;">
        <div style="position: relative; margin: 20px auto; background: linear-gradient(145deg, #1a1a2e, #16213e); padding: 2rem; border-radius: 20px; border: 2px solid #00ff87; box-shadow: 0 10px 40px rgba(0,255,135,0.3); z-index: 1001; max-width: 500px; width: 90%; min-height: auto;">

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: #00ff87; margin: 0; font-size: 1.5rem;">💰 Fazer Depósito</h3>
                <button onclick="closeDepositModal()" style="background: none; border: none; color: #666; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 50%; transition: all 0.3s ease;" onmouseover="this.style.color='#ff4757'; this.style.background='rgba(255,71,87,0.1)'" onmouseout="this.style.color='#666'; this.style.background='none'">
                    ✕
                </button>
            </div>

            <!-- Formulário -->
            <form id="depositForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: #00ff87; font-weight: bold; font-size: 1rem;">
                        💵 Valor do Depósito:
                    </label>
                    <input type="number" name="amount" min="1" step="0.01" required placeholder="Ex: 50.00"
                        style="width: 100%; padding: 1rem; border-radius: 10px; border: 2px solid #666; background: #2a2a3e; color: white; font-size: 1.1rem; transition: all 0.3s ease;"
                        onfocus="this.style.borderColor='#00ff87'"
                        onblur="this.style.borderColor='#666'">
                    <div style="margin-top: 0.5rem; font-size: 0.9rem; color: #999;">
                        Valor mínimo: R$ 10,00
                    </div>
                </div>

                <!-- Botões -->
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 1rem; font-size: 1.1rem; font-weight: bold;">
                        🚀 Gerar PIX
                    </button>
                    <button type="button" class="btn" onclick="closeDepositModal()" style="flex: 0 0 auto; padding: 1rem;">
                        Cancelar
                    </button>
                </div>
            </form>

            <!-- Loading -->
            <div id="depositLoading" style="display: none; text-align: center; margin: 2rem 0;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #333; border-top: 4px solid #00ff87; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="color: #00ff87; margin-top: 1rem; font-weight: bold;">Gerando seu PIX...</p>
            </div>

            <!-- Mensagens (onde aparecerá o QR Code) -->
            <div id="depositMessage" style="margin-top: 1rem;"></div>
        </div>
    </div>

    <!-- Modal de Saque -->
    <div id="withdrawModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; backdrop-filter: blur(5px); overflow-y: auto; padding: 20px 0;">
        <div style="position: relative; margin: 20px auto; background: linear-gradient(145deg, #1a1a2e, #16213e); padding: 2rem; border-radius: 20px; border: 2px solid #ffa500; box-shadow: 0 10px 40px rgba(255,165,0,0.3); z-index: 1001; max-width: 500px; width: 90%; min-height: auto;">

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: #ffa500; margin: 0; font-size: 1.5rem;">💳 Solicitar Saque</h3>
                <button onclick="closeWithdrawModal()" style="background: none; border: none; color: #666; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 50%; transition: all 0.3s ease;" onmouseover="this.style.color='#ff4757'; this.style.background='rgba(255,71,87,0.1)'" onmouseout="this.style.color='#666'; this.style.background='none'">
                    ✕
                </button>
            </div>

            <!-- Formulário -->
            <form id="withdrawForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: #ffa500; font-weight: bold; font-size: 1rem;">
                        💰 Valor do Saque:
                    </label>
                    <input type="number" name="amount" min="1" step="0.01" required placeholder="Ex: 100.00"
                        style="width: 100%; padding: 1rem; border-radius: 10px; border: 2px solid #666; background: #2a2a3e; color: white; font-size: 1.1rem; transition: all 0.3s ease;"
                        onfocus="this.style.borderColor='#ffa500'"
                        onblur="this.style.borderColor='#666'">
                    <div style="margin-top: 0.5rem; font-size: 0.9rem; color: #999;">
                        Saldo disponível: R$ {{ number_format(auth()->user()->wallet->balance, 2, ',', '.') }}
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: #ffa500; font-weight: bold; font-size: 1rem;">
                        🔑 Tipo da Chave PIX:
                    </label>
                    <select name="key_type" required 
                        style="width: 100%; padding: 1rem; border-radius: 10px; border: 2px solid #666; background: #2a2a3e; color: white; font-size: 1.1rem; transition: all 0.3s ease; cursor: pointer;"
                        onfocus="this.style.borderColor='#ffa500'"
                        onblur="this.style.borderColor='#666'"
                        >
                        <option value="" disabled selected style="color: #999;">Selecione o tipo de chave</option>
                        <option value="cpf" style="background: #2a2a3e; color: white;">📄 CPF</option>
                        <option value="phone" style="background: #2a2a3e; color: white;">📱 Telefone</option>
                        <option value="email" style="background: #2a2a3e; color: white;">📧 E-mail</option>
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: #ffa500; font-weight: bold; font-size: 1rem;">
                        🏦 Chave PIX:
                    </label>
                    <input type="text" name="pix_key" id="pixKeyInput" required placeholder="Primeiro selecione o tipo de chave"
                        style="width: 100%; padding: 1rem; border-radius: 10px; border: 2px solid #666; background: #2a2a3e; color: white; font-size: 1.1rem; transition: all 0.3s ease;"
                        onfocus="this.style.borderColor='#ffa500'"
                        onblur="this.style.borderColor='#666'"
                        oninput="formatPixKey(this)">
                    <div id="pixKeyHelper" style="margin-top: 0.5rem; font-size: 0.9rem; color: #999;">
                        Selecione o tipo de chave acima para ver as instruções
                    </div>
                </div>

                <!-- Botões -->
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                    <button type="submit" class="btn btn-warning" style="flex: 1; padding: 1rem; font-size: 1.1rem; font-weight: bold;">
                        💸 Solicitar Saque
                    </button>
                    <button type="button" class="btn" onclick="closeWithdrawModal()" style="flex: 0 0 auto; padding: 1rem;">
                        Cancelar
                    </button>
                </div>
            </form>

            <!-- Loading -->
            <div id="withdrawLoading" style="display: none; text-align: center; margin: 2rem 0;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #333; border-top: 4px solid #ffa500; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="color: #ffa500; margin-top: 1rem; font-weight: bold;">Processando saque...</p>
            </div>

            <!-- Mensagens -->
            <div id="withdrawMessage" style="margin-top: 1rem;"></div>
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
            // Reset do formulário
            document.getElementById('depositForm').style.display = 'block';
            document.getElementById('depositMessage').innerHTML = '';
        }

        // Funções globais para modal de saque
        function openWithdrawModal() {
            document.getElementById('withdrawModal').style.display = 'block';
            // Fechar menu hambúrguer se estiver aberto
            document.getElementById('dropdownMenu').classList.remove('active');
            document.querySelector('.hamburger-btn').classList.remove('active');
        }

        function closeWithdrawModal() {
            document.getElementById('withdrawModal').style.display = 'none';
            // Reset do formulário
            document.getElementById('withdrawForm').reset();
            document.getElementById('withdrawMessage').innerHTML = '';
        }

        // Função para atualizar placeholder e helper text baseado no tipo de chave
        function updatePixKeyPlaceholder(keyType) {
            const pixKeyInput = document.getElementById('pixKeyInput');
            const pixKeyHelper = document.getElementById('pixKeyHelper');
            
            // switch(keyType) {
            //     case 'cpf':
            //         pixKeyInput.placeholder = 'Ex: 123.456.789-00';
            //         pixKeyInput.setAttribute('maxlength', '14');
            //         pixKeyHelper.innerHTML = 'Digite seu CPF (será formatado automaticamente)';
            //         break;
            //     case 'phone':
            //         pixKeyInput.placeholder = 'Ex: (11) 99999-9999';
            //         pixKeyInput.setAttribute('maxlength', '15');
            //         pixKeyHelper.innerHTML = 'Digite seu telefone com DDD (será formatado automaticamente)';
            //         break;
            //     case 'email':
            //         pixKeyInput.placeholder = 'Ex: seuemail@exemplo.com';
            //         pixKeyInput.removeAttribute('maxlength');
            //         pixKeyHelper.innerHTML = 'Digite seu e-mail completo';
            //         break;
            //     default:
            //         pixKeyInput.placeholder = 'Primeiro selecione o tipo de chave';
            //         pixKeyHelper.innerHTML = 'Selecione o tipo de chave acima para ver as instruções';
            // }
            
            // Limpar o campo quando trocar de tipo
            pixKeyInput.value = '';
        }

        // Função para formatar a chave PIX conforme o tipo
        function formatPixKey(input) {
            const keyType = document.querySelector('select[name="key_type"]').value;
            let value = input.value.replace(/\D/g, ''); // Remove tudo que não é número
            
            switch(keyType) {
                case 'cpf':
                    // Formatação CPF: 123.456.789-00
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                    input.value = value;
                    break;
                case 'phone':
                    // Formatação telefone: (11) 99999-9999
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
                    input.value = value;
                    break;
                case 'email':
                    // Para email, não formatar - apenas manter o valor original
                    input.value = input.value;
                    break;
            }
        }

        // Função para validar chave PIX antes do envio
        function validatePixKey(keyType, pixKey) {
            switch(keyType) {
                case 'cpf':
                    // Validação básica de CPF (11 dígitos)
                    const cpfNumbers = pixKey.replace(/\D/g, '');
                    return cpfNumbers.length === 11;
                case 'phone':
                    // Validação básica de telefone (10 ou 11 dígitos)
                    const phoneNumbers = pixKey.replace(/\D/g, '');
                    return phoneNumbers.length >= 10 && phoneNumbers.length <= 11;
                case 'email':
                    // Validação básica de email
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailRegex.test(pixKey);
                default:
                    return false;
            }
        }

        // Submissão do formulário de saque
        document.getElementById('withdrawForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const amount = formData.get('amount');
            const keyType = formData.get('key_type');
            const pixKey = formData.get('pix_key');

            // Validar se o tipo de chave foi selecionado
            if (!keyType) {
                showValidationError('Por favor, selecione o tipo de chave PIX');
                return;
            }

            // Validar formato da chave PIX
            if (!validatePixKey(keyType, pixKey)) {
                const errorMessages = {
                    'cpf': 'CPF deve ter 11 dígitos válidos',
                    'phone': 'Telefone deve ter 10 ou 11 dígitos',
                    'email': 'E-mail deve ter um formato válido'
                };
                showValidationError(errorMessages[keyType]);
                return;
            }

            // Mostra loading e limpa mensagens anteriores
            document.getElementById('withdrawLoading').style.display = 'block';
            document.getElementById('withdrawMessage').innerHTML = '';

            // Desabilita o botão
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processando...';

            try {
                const response = await fetch('/pix/withdrawal', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: parseFloat(amount),
                        key_type: keyType,
                        pix_key: pixKey
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Sucesso - mostrar mensagem de confirmação
                    document.getElementById('withdrawForm').style.display = 'none';
                    
                    // Obter o nome amigável do tipo de chave
                    const keyTypeNames = {
                        'cpf': 'CPF',
                        'phone': 'Telefone',
                        'email': 'E-mail'
                    };
                    
                    document.getElementById('withdrawMessage').innerHTML = `
                        <div style="color: #00ff87; text-align: center;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
                            <h4 style="color: #ffa500; margin-bottom: 1rem; font-size: 1.4rem;">Saque Solicitado com Sucesso!</h4>
                            
                            <div style="background: rgba(255,165,0,0.1); padding: 1.5rem; border-radius: 15px; border: 1px solid rgba(255,165,0,0.3); margin: 1.5rem 0;">
                                <p style="font-size: 1.1rem; margin-bottom: 0.8rem;"><strong>Valor:</strong> R$ ${parseFloat(amount).toFixed(2).replace('.', ',')}</p>
                                <p style="font-size: 1rem; margin-bottom: 0.8rem;"><strong>Tipo de Chave:</strong> ${keyTypeNames[keyType]}</p>
                                <p style="font-size: 1rem; margin-bottom: 0.8rem;"><strong>Chave PIX:</strong> ${pixKey}</p>
                                <p style="font-size: 0.9rem; color: #999;">ID da Solicitação: ${data.transaction_id || 'Processando...'}</p>
                            </div>
                            
                            <div style="background: rgba(0,255,135,0.1); padding: 1.2rem; border-radius: 15px; border: 1px solid rgba(0,255,135,0.3); margin: 1.5rem 0;">
                                <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                    <span style="width: 12px; height: 12px; background: #ffd700; border-radius: 50%; margin-right: 0.5rem; animation: pulse 2s infinite;"></span>
                                    <span style="font-weight: bold; color: #ffd700;">Status: Em Análise</span>
                                </div>
                                
                                <div style="text-align: left; font-size: 0.9rem; line-height: 1.6;">
                                    <p style="margin-bottom: 0.8rem;"><strong>⏰ Prazo de processamento:</strong></p>
                                    <p style="margin-bottom: 0.5rem;">• Dias úteis: até 2 horas</p>
                                    <p style="margin-bottom: 0.5rem;">• Fins de semana: até 24 horas</p>
                                    <p style="margin-bottom: 1rem;">• Feriados: até 24 horas</p>
                                    
                                    <p style="font-size: 0.85rem; color: #999; font-style: italic;">
                                        💡 Você receberá uma notificação quando o saque for processado
                                    </p>
                                </div>
                            </div>
                            
                            <button onclick="closeWithdrawModal()" class="btn btn-warning" style="padding: 1rem 2rem; font-size: 1.1rem; margin-top: 1rem;">
                                Fechar
                            </button>
                        </div>
                    `;

                    // Atualizar saldo na tela (assumindo que o backend retorna o novo saldo)
                    if (data.new_balance !== undefined) {
                        const newBalanceFormatted = `R$ ${data.new_balance.toFixed(2).replace('.', ',')}`;
                        document.getElementById('balance').textContent = newBalanceFormatted;
                        document.getElementById('balance-mobile').textContent = newBalanceFormatted;
                    }

                } else {
                    // Erro na solicitação
                    document.getElementById('withdrawMessage').innerHTML = `
                        <div style="color: #ff4757; text-align: center;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">❌</div>
                            <h4 style="color: #ff4757; margin-bottom: 1rem;">Erro ao Solicitar Saque</h4>
                            <p style="font-size: 1rem; margin-bottom: 1.5rem;">
                                ${data.message || 'Não foi possível processar sua solicitação'}
                            </p>
                            
                            <div style="background: rgba(255,71,87,0.1); padding: 1rem; border-radius: 10px; border: 1px solid rgba(255,71,87,0.3); margin-bottom: 1.5rem;">
                                <p style="font-size: 0.9rem; line-height: 1.5;">
                                    <strong>Possíveis causas:</strong><br>
                                    • Saldo insuficiente<br>
                                    • Chave PIX inválida<br>
                                    • Valor abaixo do mínimo<br>
                                    • Limite diário excedido
                                </p>
                            </div>
                            
                            <button onclick="resetWithdrawForm()" class="btn btn-warning" style="padding: 1rem 1.5rem; margin-right: 1rem;">
                                Tentar Novamente
                            </button>
                            <button onclick="closeWithdrawModal()" class="btn" style="padding: 1rem 1.5rem;">
                                Fechar
                            </button>
                        </div>
                    `;
                }

            } catch (error) {
                console.error('Erro:', error);
                document.getElementById('withdrawMessage').innerHTML = `
                    <div style="color: #ff4757; text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🚫</div>
                        <h4 style="color: #ff4757; margin-bottom: 1rem;">Erro de Conexão</h4>
                        <p style="font-size: 1rem; margin-bottom: 1.5rem;">
                            Não foi possível conectar com o servidor. Verifique sua conexão e tente novamente.
                        </p>
                        <button onclick="resetWithdrawForm()" class="btn btn-warning" style="padding: 1rem 1.5rem; margin-right: 1rem;">
                            Tentar Novamente
                        </button>
                        <button onclick="closeWithdrawModal()" class="btn" style="padding: 1rem 1.5rem;">
                            Fechar
                        </button>
                    </div>
                `;
            } finally {
                // Esconde loading e reabilita botão
                document.getElementById('withdrawLoading').style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.textContent = '💸 Solicitar Saque';
            }
        });

        // Função para mostrar erros de validação
        function showValidationError(message) {
            document.getElementById('withdrawMessage').innerHTML = `
                <div style="color: #ff4757; text-align: center; padding: 1rem; background: rgba(255,71,87,0.1); border-radius: 10px; border: 1px solid rgba(255,71,87,0.3);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚠️</div>
                    <p style="font-size: 1rem; margin: 0;">${message}</p>
                </div>
            `;
            
            // Remover a mensagem após 3 segundos
            setTimeout(() => {
                document.getElementById('withdrawMessage').innerHTML = '';
            }, 3000);
        }

        // Função para resetar o formulário de saque
        function resetWithdrawForm() {
            document.getElementById('withdrawForm').style.display = 'block';
            document.getElementById('withdrawMessage').innerHTML = '';
        }

        // Submissão do formulário de depósito - VERSÃO ATUALIZADA
        document.getElementById('depositForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const amount = formData.get('amount');
            const description = 'Depósito PIX';

            // Mostra loading e limpa mensagens anteriores
            document.getElementById('depositLoading').style.display = 'block';
            document.getElementById('depositMessage').innerHTML = '';

            // Desabilita o botão
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processando...';

            try {
                const response = await fetch('/pix/deposit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: parseFloat(amount),
                        description: description
                    })
                });

                const data = await response.json();

                if (data.success && data.data && data.data.qrCodeResponse) {
                    // Sucesso - esconder formulário e mostrar QR Code
                    const qrData = data.data.qrCodeResponse;
                    const pixCode = qrData.qrcode;

                    // Esconde o formulário
                    document.getElementById('depositForm').style.display = 'none';

                    document.getElementById('depositMessage').innerHTML = `
                <div style="color: #00ff87; text-align: center;">
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="color: #00ff87; margin-bottom: 0.5rem; font-size: 1.3rem;">✅ PIX Gerado com Sucesso!</h4>
                        <p style="font-size: 1.1rem;"><strong>Valor:</strong> R$ ${qrData.amount.toFixed(2).replace('.', ',')}</p>
                        <p style="font-size: 0.9rem; color: #999;">ID: ${qrData.transactionId}</p>
                    </div>
                    
                    <!-- QR Code visual gerado pelo JavaScript -->
                    <div style="margin: 1.5rem 0;">
                        <p style="margin-bottom: 1rem; font-weight: bold; font-size: 1.1rem;">📱 Escaneie o QR Code:</p>
                        <div id="qrcode-container" style="background: white; padding: 1.5rem; border-radius: 15px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                            <div id="qrcode" style="display: flex; justify-content: center; align-items: center;"></div>
                        </div>
                    </div>
                    
                    <!-- Código PIX para copiar -->
                    <div style="margin: 1.5rem 0;">
                        <p style="margin-bottom: 0.8rem; font-size: 1rem; font-weight: bold;">💰 Código PIX Copia e Cola:</p>
                        <div style="background: #2a2a3e; padding: 1rem; border-radius: 10px; border: 1px solid #666; margin-bottom: 1rem; max-height: 120px; overflow-y: auto;">
                            <code style="font-family: 'Courier New', monospace; font-size: 0.75rem; word-break: break-all; color: #00ff87; line-height: 1.5; display: block;">
                                ${pixCode}
                            </code>
                        </div>
                        <button onclick="copyPixCode('${pixCode}')" class="btn btn-primary" style="font-size: 0.9rem; padding: 0.8rem 1.5rem; margin-bottom: 1rem;">
                            📋 Copiar Código PIX
                        </button>
                    </div>
                    
                    <!-- Status e instruções -->
                    <div style="margin-top: 1.5rem; padding: 1.2rem; background: rgba(0,255,135,0.1); border-radius: 15px; border: 1px solid rgba(0,255,135,0.3);">
                        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <span style="width: 12px; height: 12px; background: #ffd700; border-radius: 50%; margin-right: 0.5rem; animation: pulse 2s infinite;"></span>
                            <span style="font-weight: bold; color: #ffd700;">Status: Aguardando Pagamento</span>
                        </div>
                        <p style="font-size: 0.95rem; margin-bottom: 1rem; font-weight: bold;">📋 Como pagar:</p>
                        <div style="text-align: left; font-size: 0.85rem; line-height: 1.6;">
                            <p>1️⃣ Abra o app do seu banco</p>
                            <p>2️⃣ Escaneie o QR Code OU cole o código PIX</p>
                            <p>3️⃣ Confirme o valor (R$ ${qrData.amount.toFixed(2).replace('.', ',')})</p>
                            <p>4️⃣ Finalize o pagamento</p>
                            <p>5️⃣ Seu saldo será creditado automaticamente!</p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: center;">
                        <button onclick="resetDepositModal()" class="btn" style="padding: 1rem 1.5rem; font-size: 1rem;">
                            ← Novo Depósito
                        </button>
                        <button onclick="closeDepositModal()" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1rem;">
                            Fechar
                        </button>
                    </div>
                </div>
            `;

                    // Gerar QR Code visual usando biblioteca
                    generateQRCode(pixCode);
                } else {
                    // Erro
                    document.getElementById('depositMessage').innerHTML = `
                <div style="color: #ff4757; text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">❌</div>
                    <p style="font-size: 1.1rem; margin-bottom: 1rem;">
                        ${data.message || 'Erro ao processar depósito'}
                    </p>
                    <button onclick="closeDepositModal()" class="btn">
                        Tentar Novamente
                    </button>
                </div>
            `;
                }

            } catch (error) {
                console.error('Erro:', error);
                document.getElementById('depositMessage').innerHTML = `
            <div style="color: #ff4757; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🚫</div>
                <p style="font-size: 1.1rem; margin-bottom: 1rem;">
                    Erro de conexão. Verifique sua internet e tente novamente.
                </p>
                <button onclick="closeDepositModal()" class="btn">
                    Fechar
                </button>
            </div>
        `;
            } finally {
                // Esconde loading e reabilita botão
                document.getElementById('depositLoading').style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.textContent = '🚀 Gerar PIX';
            }
        });

        // Função para resetar o modal de depósito
        function resetDepositModal() {
            document.getElementById('depositForm').style.display = 'block';
            document.getElementById('depositForm').reset();
            document.getElementById('depositMessage').innerHTML = '';
        }

        // Função para gerar QR Code visual
        function generateQRCode(pixCode) {
            const qrContainer = document.getElementById('qrcode');
            qrContainer.innerHTML = ''; // Limpa conteúdo anterior

            // Verifica se a biblioteca QR.js está disponível
            if (typeof QRCode !== 'undefined') {
                try {
                    new QRCode(qrContainer, {
                        text: pixCode,
                        width: 200,
                        height: 200,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                } catch (error) {
                    console.log('Erro ao gerar QR Code com QRCode.js:', error);
                    generateQRCodeFallback(pixCode);
                }
            } else {
                // Fallback usando API online
                generateQRCodeFallback(pixCode);
            }
        }

        // Fallback para gerar QR Code usando API
        function generateQRCodeFallback(pixCode) {
            const qrContainer = document.getElementById('qrcode');

            // Usando API do QR Server
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(pixCode)}`;

            qrContainer.innerHTML = `
        <img src="${qrUrl}" 
             alt="QR Code PIX" 
             style="width: 200px; height: 200px; display: block;"
             onload="this.style.opacity='1'"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='block'"
             style="opacity: 0; transition: opacity 0.3s ease;">
        <div style="display: none; padding: 2rem; color: #666; font-size: 0.9rem;">
            ⚠️ Não foi possível gerar o QR Code visual.<br>
            Use o código PIX acima para fazer o pagamento.
        </div>
    `;
        }

        // Carregar biblioteca QR.js dinamicamente
        function loadQRCodeLibrary() {
            if (typeof QRCode === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js';
                script.onload = function() {
                    console.log('QR Code library loaded successfully');
                };
                script.onerror = function() {
                    console.log('Failed to load QR Code library, using fallback');
                };
                document.head.appendChild(script);
            }
        }

        // Carregar a biblioteca quando a página carrega
        document.addEventListener('DOMContentLoaded', loadQRCodeLibrary);

        function copyPixCode(code) {
            if (navigator.clipboard && window.isSecureContext) {
                // Método moderno
                navigator.clipboard.writeText(code).then(() => {
                    showCopySuccess();
                }).catch(() => {
                    fallbackCopyTextToClipboard(code);
                });
            } else {
                // Fallback para navegadores mais antigos
                fallbackCopyTextToClipboard(code);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showCopySuccess();
                } else {
                    showCopyError();
                }
            } catch (err) {
                showCopyError();
            }

            document.body.removeChild(textArea);
        }

        function showCopySuccess() {
            // Cria notificação de sucesso
            const notification = document.createElement('div');
            notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #00ff87, #00b359);
        color: #1a1a2e;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,255,135,0.3);
        z-index: 10000;
        font-weight: bold;
        animation: slideInRight 0.3s ease-out;
    `;
            notification.innerHTML = '✅ Código PIX copiado!';

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }

        function showCopyError() {
            const notification = document.createElement('div');
            notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #ff4757;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(255,71,87,0.3);
        z-index: 10000;
        font-weight: bold;
        animation: slideInRight 0.3s ease-out;
    `;
            notification.innerHTML = '❌ Erro ao copiar código';

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => notification.remove(), 2000);
            }, 2000);
        }

        // CSS para animações das notificações e outros efeitos
        const notificationStyle = document.createElement('style');
        notificationStyle.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
    
    @keyframes pulse {
        0% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(1.1);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    /* Estilo para o código PIX */
    .pix-code-container {
        position: relative;
    }
    
    .pix-code-container:hover {
        background: #333 !important;
    }
    
    /* Loading spinner personalizado */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
        document.head.appendChild(notificationStyle);

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
            if (e.target.id === 'withdrawModal') {
                closeWithdrawModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeWinModal();
                closeDepositModal();
                closeWithdrawModal();
                // Fechar menu hambúrguer
                document.getElementById('dropdownMenu').classList.remove('active');
                document.querySelector('.hamburger-btn').classList.remove('active');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>