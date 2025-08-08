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
                gap: 0.5rem;
            }

            .admin-btn {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }

            .admin-container {
                padding: 1rem;
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
        <nav class="admin-nav">
            <span style="color: #94a3b8;">{{ auth()->user()->name }}</span>
            <a href="{{ route('game.index') }}" class="admin-btn">🎮 Voltar ao Jogo</a>
            
            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="admin-btn logout">
                    🚪 Sair
                </button>
            </form>
        </nav>
        @endauth
    </header>

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
</body>

</html>