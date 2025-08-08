<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Raspadinha Online')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

        /* Header Simples para Admin */
        .admin-header {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0, 255, 135, 0.2);
            position: relative;
            z-index: 10;
        }

        .admin-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #00ff87;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .admin-logo::before {
            content: '👑';
            font-size: 1.8rem;
        }

        .admin-nav {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        /* Menu Hambúrguer */
        .hamburger-menu {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .hamburger-menu:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .hamburger-line {
            width: 25px;
            height: 3px;
            background: #00ff87;
            margin: 3px 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .hamburger-menu.active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        
        .hamburger-menu.active .hamburger-line:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger-menu.active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }
        
        /* Menu Mobile */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 280px;
            height: 100vh;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(0, 255, 135, 0.2);
            transition: right 0.3s ease;
            z-index: 1000;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .mobile-menu.active {
            right: 0;
        }
        
        .mobile-menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }
        
        .mobile-menu-title {
            color: #00ff87;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .close-menu {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .close-menu:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ff6b6b;
        }
        
        .mobile-menu-item {
            display: block;
            color: #ffffff;
            text-decoration: none;
            padding: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            font-weight: 500;
        }
        
        .mobile-menu-item:hover {
            background: rgba(0, 255, 135, 0.1);
            border-color: rgba(0, 255, 135, 0.3);
            color: #00ff87;
            text-decoration: none;
            transform: translateX(5px);
        }
        
        .mobile-menu-user {
            background: rgba(0, 255, 135, 0.1);
            border: 1px solid rgba(0, 255, 135, 0.2);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            color: #00ff87;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .mobile-menu-logout {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: auto;
        }
        
        .mobile-menu-logout:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-2px);
        }
        
        /* Overlay */
        .menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .admin-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .admin-btn:hover {
            background: linear-gradient(135deg, #764ba2, #667eea);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .admin-btn.logout {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .admin-btn.logout:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }

        /* Container principal */
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
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
            .admin-header {
                padding: 1rem;
            }

            .admin-logo {
                font-size: 1.2rem;
            }

            .admin-nav {
                display: none; /* Esconder navegação desktop no mobile */
            }
            
            .hamburger-menu {
                display: flex; /* Mostrar menu hambúrguer no mobile */
            }

            .admin-container {
                padding: 1rem;
            }
        }
        
        @media (min-width: 769px) {
            .hamburger-menu {
                display: none; /* Esconder hambúrguer no desktop */
            }
            
            .mobile-menu {
                display: none; /* Esconder menu mobile no desktop */
            }
        }
    </style>

    @stack('head')
    @stack('styles')
</head>

<body>
    <!-- Header Admin -->
    <header class="admin-header">
        <a href="{{ route('admin.demo-accounts') }}" class="admin-logo">
            Admin Panel
        </a>

        @auth
        <!-- Navegação Desktop -->
        <nav class="admin-nav">
            <span style="color: #94a3b8;">{{ auth()->user()->name }}</span>
            <a href="{{ route('admin.demo-accounts') }}" class="admin-btn">👥 Contas Demo</a>
            <a href="{{ route('affiliate.manager') }}" class="admin-btn">🤝 Gerenciar Afiliados</a>
            <a href="{{ route('game.index') }}" class="admin-btn">🎮 Voltar ao Jogo</a>
            
            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="admin-btn logout">
                    🚪 Sair
                </button>
            </form>
        </nav>
        
        <!-- Menu Hambúrguer -->
        <div class="hamburger-menu" id="hamburgerMenu">
            <div class="hamburger-line"></div>
            <div class="hamburger-line"></div>
            <div class="hamburger-line"></div>
        </div>
        @endauth
    </header>
    
    @auth
    <!-- Overlay -->
    <div class="menu-overlay" id="menuOverlay"></div>
    
    <!-- Menu Mobile -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <span class="mobile-menu-title">👑 Admin Panel</span>
            <button class="close-menu" id="closeMenu">✕</button>
        </div>
        
        <div class="mobile-menu-user">
            👤 {{ auth()->user()->name }}
        </div>
        
        <a href="{{ route('admin.demo-accounts') }}" class="mobile-menu-item">
            👥 Contas Demo
        </a>
        
        <a href="{{ route('affiliate.manager') }}" class="mobile-menu-item">
            🤝 Gerenciar Afiliados
        </a>
        
        <a href="{{ route('game.index') }}" class="mobile-menu-item">
            🎮 Voltar ao Jogo
        </a>
        
        <!-- Logout Mobile -->
        <form method="POST" action="{{ route('logout') }}" style="margin-top: auto;">
            @csrf
            <button type="submit" class="mobile-menu-logout">
                🚪 Sair
            </button>
        </form>
    </div>
    @endauth

    <!-- Alerts -->
    @if(session('success'))
    <div class="admin-container">
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="admin-container">
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    @stack('scripts')
    
    <!-- Script do Menu Hambúrguer -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerMenu = document.getElementById('hamburgerMenu');
            const mobileMenu = document.getElementById('mobileMenu');
            const menuOverlay = document.getElementById('menuOverlay');
            const closeMenu = document.getElementById('closeMenu');
            
            // Verificar se os elementos existem
            if (!hamburgerMenu || !mobileMenu || !menuOverlay || !closeMenu) {
                return;
            }
            
            // Função para abrir o menu
            function openMenu() {
                hamburgerMenu.classList.add('active');
                mobileMenu.classList.add('active');
                menuOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            // Função para fechar o menu
            function closeMenuFunc() {
                hamburgerMenu.classList.remove('active');
                mobileMenu.classList.remove('active');
                menuOverlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
            
            // Event listeners
            hamburgerMenu.addEventListener('click', function(e) {
                e.preventDefault();
                if (mobileMenu.classList.contains('active')) {
                    closeMenuFunc();
                } else {
                    openMenu();
                }
            });
            
            closeMenu.addEventListener('click', function(e) {
                e.preventDefault();
                closeMenuFunc();
            });
            
            menuOverlay.addEventListener('click', function(e) {
                e.preventDefault();
                closeMenuFunc();
            });
            
            // Fechar menu ao clicar em um link
            const menuItems = document.querySelectorAll('.mobile-menu-item');
            menuItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    closeMenuFunc();
                });
            });
            
            // Fechar menu com ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                    closeMenuFunc();
                }
            });
            
            // Fechar menu ao redimensionar para desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
                    closeMenuFunc();
                }
            });
        });
    </script>
</body>

</html>