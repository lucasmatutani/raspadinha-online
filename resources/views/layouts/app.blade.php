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

            #depositModal,
            #withdrawModal,
            #affiliateModal {
                padding: 10px 0;
            }

            #depositModal>div,
            #withdrawModal>div,
            #affiliateModal>div {
                width: 95%;
                padding: 1.5rem;
                margin: 10px auto;
            }

            #depositModal h3,
            #withdrawModal h3,
            #affiliateModal h3 {
                font-size: 1.3rem;
            }

            #depositModal input,
            #withdrawModal input,
            #affiliateModal input {
                padding: 0.8rem;
                font-size: 1rem;
            }

            #depositModal .btn,
            #withdrawModal .btn,
            #affiliateModal .btn {
                padding: 0.8rem;
                font-size: 1rem;
            }
        }

        /* Melhor scroll no mobile */
        @media (max-height: 700px) {

            #depositModal,
            #withdrawModal,
            #affiliateModal {
                padding: 5px 0;
            }

            #depositModal>div,
            #withdrawModal>div,
            #affiliateModal>div {
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

        /* Botão de afiliado */
        .btn-affiliate {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            border: 2px solid #ff6b6b;
            color: #ffffff;
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }

        .btn-affiliate:hover {
            background: linear-gradient(135deg, #ee5a52, #dc4848);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.5);
            color: #ffffff;
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

        /* Estilos específicos do modal de afiliado */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.2rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 107, 107, 0.3);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.2);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #ff6b6b;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .referral-link-container {
            background: rgba(255, 107, 107, 0.1);
            padding: 1.2rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 107, 107, 0.3);
            margin: 1rem 0;
        }

        .referral-link {
            background: #2a2a3e;
            padding: 0.8rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            color: #ff6b6b;
            word-break: break-all;
            margin: 0.8rem 0;
            border: 1px solid #666;
        }

        .recent-referrals {
            margin-top: 1.5rem;
        }

        .referral-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.8rem;
            border-left: 4px solid #ff6b6b;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .referral-info {
            flex: 1;
        }

        .referral-name {
            font-weight: bold;
            color: #ff6b6b;
            margin-bottom: 0.3rem;
        }

        .referral-date {
            font-size: 0.8rem;
            color: #999;
        }

        .referral-earnings {
            text-align: right;
            color: #00ff87;
            font-weight: bold;
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

            .auth-buttons {
                display: none;
            }

            .user-info {
                display: none;
            }

            .mobile-user-info {
                display: flex;
            }

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

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.8rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-number {
                font-size: 1.5rem;
            }

            .referral-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .referral-earnings {
                text-align: left;
            }
        }

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

        /* Animações */
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
            <a href="#" class="btn btn-affiliate" id="affiliateBtn">🤝 Afiliado</a>
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
                    <a href="#" class="btn btn-affiliate" id="affiliateBtnMobile">🤝 Afiliado</a>
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

    <!-- Modal de Afiliado -->
    @auth
    <div id="affiliateModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; backdrop-filter: blur(5px); overflow-y: auto; padding: 20px 0;">
        <div style="position: relative; margin: 20px auto; background: linear-gradient(145deg, #1a1a2e, #16213e); padding: 2rem; border-radius: 20px; border: 2px solid #ff6b6b; box-shadow: 0 10px 40px rgba(255,107,107,0.3); z-index: 1001; max-width: 700px; width: 90%; min-height: auto;">

            <!-- Cabeçalho -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: #ff6b6b; margin: 0; font-size: 1.5rem;">🤝 Programa de Afiliados</h3>
                <button onclick="closeAffiliateModal()" style="background: none; border: none; color: #666; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 50%; transition: all 0.3s ease;" onmouseover="this.style.color='#ff4757'; this.style.background='rgba(255,71,87,0.1)'" onmouseout="this.style.color='#666'; this.style.background='none'">
                    ✕
                </button>
            </div>

            <!-- Loading -->
            <div id="affiliateLoading" style="display: block; text-align: center; margin: 2rem 0;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #333; border-top: 4px solid #ff6b6b; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="color: #ff6b6b; margin-top: 1rem; font-weight: bold;">Carregando seus dados...</p>
            </div>

            <!-- Conteúdo do Afiliado -->
            <div id="affiliateContent" style="display: none;">
                <!-- Como Funciona -->
                <div style="background: rgba(255,107,107,0.1); padding: 1.5rem; border-radius: 15px; border: 1px solid rgba(255,107,107,0.3); margin-bottom: 2rem;">
                    <h4 style="color: #ff6b6b; margin-bottom: 1rem; font-size: 1.2rem;">💡 Como Funciona:</h4>
                    <div style="font-size: 0.95rem; line-height: 1.6; color: #ccc;">
                        <p style="margin-bottom: 0.8rem;">✅ <strong>Indique amigos</strong> usando seu link exclusivo</p>
                        <p style="margin-bottom: 0.8rem;">✅ <strong>Ganhe 50%</strong> de todos os depósitos dos seus indicados</p>
                        <p style="margin-bottom: 0.8rem;">✅ <strong>Receba automaticamente</strong></p>
                        <p style="margin-bottom: 0;">✅ <strong>Quanto mais indicar, mais ganha!</strong></p>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number" id="totalReferrals">0</div>
                        <div class="stat-label">Total Indicados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="pendingEarnings">R$ 0,00</div>
                        <div class="stat-label">Comissões Pendentes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="totalEarnings">R$ 0,00</div>
                        <div class="stat-label">Total Ganho</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="thisMonthEarnings">R$ 0,00</div>
                        <div class="stat-label">Este Mês</div>
                    </div>
                </div>

                <!-- Link de Afiliado -->
                <div class="referral-link-container">
                    <h4 style="color: #ff6b6b; margin-bottom: 1rem; font-size: 1.1rem;">🔗 Seu Link de Afiliado:</h4>
                    <div class="referral-link" id="affiliateLink">
                        Carregando...
                    </div>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <button onclick="copyAffiliateLink()" class="btn btn-affiliate" style="padding: 0.8rem 1.5rem; font-size: 0.9rem;">
                            📋 Copiar Link
                        </button>
                        <button onclick="shareAffiliateLink()" class="btn" style="padding: 0.8rem 1.5rem; font-size: 0.9rem;">
                            📤 Compartilhar
                        </button>
                    </div>
                </div>

                <!-- Indicações Recentes -->
                <div class="recent-referrals">
                    <h4 style="color: #ff6b6b; margin-bottom: 1rem; font-size: 1.1rem;">👥 Indicações Recentes:</h4>
                    <div id="recentReferralsList">
                        <!-- Será preenchido via JavaScript -->
                    </div>
                </div>

                <!-- Botão Fechar -->
                <div style="text-align: center; margin-top: 2rem;">
                    <button onclick="closeAffiliateModal()" class="btn btn-affiliate" style="padding: 1rem 3rem; font-size: 1.1rem;">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Outros modais mantidos como estão -->
    <!-- ... (resto dos modais) ... -->
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

        // ================================
        // SISTEMA DE AFILIADOS - VERSÃO FIXA
        // ================================

        (function() {
            'use strict';

            console.log('🔧 Carregando sistema de afiliados...');

            // Variável global para dados do afiliado
            window.affiliateData = null;

            // Função para abrir modal do afiliado
            window.openAffiliateModal = function() {
                console.log('🤝 Abrindo modal de afiliados...');

                const modal = document.getElementById('affiliateModal');
                const loading = document.getElementById('affiliateLoading');
                const content = document.getElementById('affiliateContent');

                if (!modal) {
                    console.error('❌ Modal de afiliado não encontrado!');
                    alert('Erro: Modal de afiliado não encontrado.');
                    return;
                }

                modal.style.display = 'block';
                if (loading) loading.style.display = 'block';
                if (content) content.style.display = 'none';

                // Fechar menu hambúrguer se estiver aberto
                const dropdownMenu = document.getElementById('dropdownMenu');
                const hamburgerBtn = document.querySelector('.hamburger-btn');

                if (dropdownMenu) dropdownMenu.classList.remove('active');
                if (hamburgerBtn) hamburgerBtn.classList.remove('active');

                // CARREGAR DADOS REAIS DA API
                loadRealAffiliateData();
            };

            window.loadRealAffiliateData = async function() {
                console.log('📡 Carregando dados reais do afiliado...');

                try {
                    const response = await fetch('/affiliate/dashboard', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });

                    console.log('📡 Status da resposta:', response.status);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log('📊 Dados recebidos da API:', data);

                    if (data.success) {
                        window.affiliateData = data.data;
                        displayAffiliateData();
                    } else {
                        console.error('❌ Erro na resposta da API:', data.message);
                        showAffiliateError(data.message || 'Erro ao carregar dados do afiliado');
                    }

                } catch (error) {
                    console.error('❌ Erro ao carregar dados:', error);

                    // Se a rota não existir ainda, mostrar dados zerados
                    if (error.message.includes('404') || error.message.includes('HTTP error! status: 404')) {
                        console.log('🔧 Rota não implementada ainda, mostrando dados zerados...');
                        showEmptyAffiliateData();
                    } else {
                        showAffiliateError('Erro de conexão. Tente novamente.');
                    }
                }
            };

            window.showEmptyAffiliateData = function() {
                console.log('📊 Mostrando dados zerados...');

                window.affiliateData = {
                    total_referrals: 0,
                    pending_earnings: 0,
                    total_earnings: 0,
                    this_month_earnings: 0,
                    referral_link: 'https://seusite.com/ref/AGUARDANDO_IMPLEMENTACAO',
                    recent_referrals: []
                };

                displayAffiliateData();
            };

            // Função para mostrar erro no modal de afiliado
            window.showAffiliateError = function(message) {
                console.log('❌ Mostrando erro:', message);

                const loading = document.getElementById('affiliateLoading');
                const content = document.getElementById('affiliateContent');

                if (loading) loading.style.display = 'none';

                if (content) {
                    content.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: #ff4757;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">❌</div>
                <h4 style="color: #ff4757; margin-bottom: 1rem;">Ops!</h4>
                <p>${message}</p>
                <div style="margin: 1.5rem 0; padding: 1rem; background: rgba(255,165,0,0.1); border-radius: 10px; border: 1px solid rgba(255,165,0,0.3);">
                    <p style="color: #ffa500; font-size: 0.9rem; margin: 0;">
                        <strong>💡 Para desenvolvedores:</strong><br>
                        É necessário implementar a rota <code>/affiliate/dashboard</code> no backend.
                    </p>
                </div>
                <button onclick="loadRealAffiliateData()" class="btn btn-affiliate" style="margin-top: 1rem;">
                    Tentar Novamente
                </button>
                <button onclick="showEmptyAffiliateData()" class="btn" style="margin-top: 0.5rem;">
                    Mostrar Dados Zerados
                </button>
            </div>
        `;
                    content.style.display = 'block';
                }
            };

            // Função para fechar modal do afiliado
            window.closeAffiliateModal = function() {
                console.log('🚪 Fechando modal de afiliados...');
                const modal = document.getElementById('affiliateModal');
                if (modal) {
                    modal.style.display = 'none';
                }
            };

            // Função para exibir dados do afiliado
            window.displayAffiliateData = function() {
                console.log('🎨 Exibindo dados do afiliado...');

                // Esconder loading e mostrar conteúdo
                const loading = document.getElementById('affiliateLoading');
                const content = document.getElementById('affiliateContent');

                if (loading) loading.style.display = 'none';
                if (content) content.style.display = 'block';

                // Preencher estatísticas
                const elements = {
                    totalReferrals: document.getElementById('totalReferrals'),
                    pendingEarnings: document.getElementById('pendingEarnings'),
                    totalEarnings: document.getElementById('totalEarnings'),
                    thisMonthEarnings: document.getElementById('thisMonthEarnings'),
                    affiliateLink: document.getElementById('affiliateLink')
                };

                if (elements.totalReferrals) {
                    elements.totalReferrals.textContent = window.affiliateData.total_referrals || '0';
                }

                if (elements.pendingEarnings) {
                    elements.pendingEarnings.textContent = formatMoney(window.affiliateData.pending_earnings || 0);
                }

                if (elements.totalEarnings) {
                    elements.totalEarnings.textContent = formatMoney(window.affiliateData.total_earnings || 0);
                }

                if (elements.thisMonthEarnings) {
                    elements.thisMonthEarnings.textContent = formatMoney(window.affiliateData.this_month_earnings || 0);
                }

                if (elements.affiliateLink) {
                    elements.affiliateLink.textContent = window.affiliateData.referral_link || 'Carregando...';
                }

                // Indicações recentes
                displayRecentReferrals();
            };

            // Função para exibir indicações recentes
            window.displayRecentReferrals = function() {
                const container = document.getElementById('recentReferralsList');

                if (!container) {
                    console.error('❌ Container de referrals não encontrado');
                    return;
                }

                if (!window.affiliateData.recent_referrals || window.affiliateData.recent_referrals.length === 0) {
                    container.innerHTML = `
                        <div style="text-align: center; padding: 2rem; color: #999; background: rgba(255,255,255,0.05); border-radius: 10px;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                            <p>Você ainda não tem indicações.</p>
                            <p style="font-size: 0.9rem; margin-top: 0.5rem;">Compartilhe seu link e comece a ganhar!</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                window.affiliateData.recent_referrals.forEach(referral => {
                    html += `
                        <div class="referral-item">
                            <div class="referral-info">
                                <div class="referral-name">${referral.name}</div>
                                <div class="referral-date">Entrou em ${referral.joined_at}</div>
                            </div>
                            <div class="referral-earnings">
                                <div>Perdas: ${formatMoney(referral.total_losses)}</div>
                                <div>Sua comissão: ${formatMoney(referral.commission_generated)}</div>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;
            };

            // Função para copiar link de afiliado
            window.copyAffiliateLink = function() {
                console.log('📋 Copiando link de afiliado...');

                const link = window.affiliateData?.referral_link || '';

                if (!link) {
                    showNotification('❌ Erro ao obter link', 'error');
                    return;
                }

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(link).then(() => {
                        showNotification('✅ Link copiado!', 'success');
                    }).catch(() => {
                        fallbackCopyTextToClipboard(link);
                    });
                } else {
                    fallbackCopyTextToClipboard(link);
                }
            };

            // Função para compartilhar link de afiliado
            window.shareAffiliateLink = function() {
                console.log('📤 Compartilhando link de afiliado...');

                const link = window.affiliateData?.referral_link || '';
                const text = `🎲 Venha jogar raspadinha online e ganhar dinheiro! Use meu link especial: ${link}`;

                if (navigator.share) {
                    navigator.share({
                        title: 'RaspaKing - Raspadinha Online',
                        text: text,
                        url: link
                    }).catch(() => {
                        copyAffiliateLink();
                    });
                } else {
                    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(text)}`;
                    window.open(whatsappUrl, '_blank');
                }
            };

            // Função para formatar dinheiro
            window.formatMoney = function(value) {
                if (typeof value !== 'number') value = parseFloat(value) || 0;
                return `R$ ${value.toFixed(2).replace('.', ',')}`;
            };

            // Sistema de notificações
            window.showNotification = function(message, type = 'success') {
                const notification = document.createElement('div');
                const colors = {
                    success: {
                        bg: 'linear-gradient(135deg, #00ff87, #00b359)',
                        color: '#1a1a2e'
                    },
                    error: {
                        bg: '#ff4757',
                        color: 'white'
                    },
                    info: {
                        bg: 'linear-gradient(135deg, #3742fa, #2f3542)',
                        color: 'white'
                    }
                };

                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${colors[type].bg};
                    color: ${colors[type].color};
                    padding: 1rem 1.5rem;
                    border-radius: 10px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                    z-index: 10000;
                    font-weight: bold;
                    animation: slideInRight 0.3s ease-out;
                    max-width: 300px;
                `;
                notification.innerHTML = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.animation = 'slideOutRight 0.3s ease-in';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            };

            // Fallback para copiar texto
            window.fallbackCopyTextToClipboard = function(text) {
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
                        showNotification('✅ Link copiado!', 'success');
                    } else {
                        showNotification('❌ Erro ao copiar', 'error');
                    }
                } catch (err) {
                    showNotification('❌ Erro ao copiar', 'error');
                }

                document.body.removeChild(textArea);
            };

            console.log('✅ Sistema de afiliados carregado!');

        })();

        // ================================
        // EVENT LISTENERS PARA OS BOTÕES
        // ================================

        document.addEventListener('DOMContentLoaded', function() {
            console.log('📱 Configurando event listeners...');

            // Botão desktop
            const affiliateBtn = document.getElementById('affiliateBtn');
            if (affiliateBtn) {
                affiliateBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('🖱️ Clique no botão desktop');
                    openAffiliateModal();
                });
                console.log('✅ Botão desktop configurado');
            }

            // Botão mobile
            const affiliateBtnMobile = document.getElementById('affiliateBtnMobile');
            if (affiliateBtnMobile) {
                affiliateBtnMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('📱 Clique no botão mobile');
                    openAffiliateModal();
                });
                console.log('✅ Botão mobile configurado');
            }

            // Verificar se modal existe
            const modal = document.getElementById('affiliateModal');
            if (modal) {
                console.log('✅ Modal de afiliado encontrado');
            } else {
                console.error('❌ Modal de afiliado não encontrado!');
            }
        });

        // ================================
        // OUTRAS FUNÇÕES EXISTENTES
        // ================================

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

            if (hamburgerMenu && !hamburgerMenu.contains(e.target)) {
                dropdownMenu.classList.remove('active');
                hamburgerBtn.classList.remove('active');
            }
        });

        // Funções globais para modal de depósito
        function openDepositModal() {
            console.log('💰 Abrindo modal de depósito...');
            // Implementar depois
        }

        function closeDepositModal() {
            console.log('🚪 Fechando modal de depósito...');
            // Implementar depois
        }

        // Funções globais para modal de saque
        function openWithdrawModal() {
            console.log('💸 Abrindo modal de saque...');
            // Implementar depois
        }

        function closeWithdrawModal() {
            console.log('🚪 Fechando modal de saque...');
            // Implementar depois
        }

        // Função para mostrar modal de vitória
        function showWinModal(winType, amount) {
            console.log('🎉 Mostrando modal de vitória...');
            // Implementar depois
        }

        function closeWinModal() {
            console.log('🚪 Fechando modal de vitória...');
            const modal = document.getElementById('winModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Fechar modais clicando fora ou com ESC
        window.addEventListener('click', function(e) {
            if (e.target.id === 'affiliateModal') {
                closeAffiliateModal();
            }
            if (e.target.id === 'winModal') {
                closeWinModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAffiliateModal();
                closeWinModal();
                // Fechar menu hambúrguer
                const dropdownMenu = document.getElementById('dropdownMenu');
                const hamburgerBtn = document.querySelector('.hamburger-btn');
                if (dropdownMenu) dropdownMenu.classList.remove('active');
                if (hamburgerBtn) hamburgerBtn.classList.remove('active');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>