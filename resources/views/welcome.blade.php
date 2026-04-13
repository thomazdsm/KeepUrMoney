<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keep Ur Mnoey - Controle Financeiro</title>

    <!-- Scripts -->
    @vite(['resources/scss/theme/app.scss', 'resources/js/app.js'])

    <!-- Custom Styles -->
    <style>
        :root {
            --kum-green: #198754;
            --kum-dark: #212529;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* Hero com textura leve pontilhada */
        .hero-section {
            background-color: #f8f9fa;
            background-image: radial-gradient(rgba(25, 135, 84, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            padding-top: 5rem;
            padding-bottom: 5rem;
        }

        .feature-icon-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 4rem;
            height: 4rem;
            border-radius: 1rem;
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--kum-green);
            font-size: 2rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-icon-box {
            transform: translateY(-5px);
            background-color: var(--kum-green);
            color: white;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .btn-primary-custom {
            background-color: var(--kum-green);
            border-color: var(--kum-green);
        }

        .btn-primary-custom:hover {
            background-color: #146c43;
            border-color: #13653f;
        }
    </style>
</head>
<body>
    <!-- Navegação -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top">
        <div class="container">
            <a class="navbar-brand text-success" href="#">
                <i class="bi bi-wallet2 me-2"></i>Keep Ur Mnoey
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ url('/') }}">Início</a>
                    </li>
                </ul>
                <div class="d-flex gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-success">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-success">Entrar</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary-custom text-white">Cadastrar</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main>
        <div class="hero-section mt-5">
            <div class="container col-xxl-8 px-4 py-5">
                <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                    <div class="col-10 col-sm-8 col-lg-6 mx-auto text-center">
                        <!-- Imagem Ilustrativa usando Ícones gigantes do BS -->
                        <div class="position-relative">
                            <i class="bi bi-graph-up-arrow text-success opacity-25" style="font-size: 15rem; position: absolute; top: -50px; right: -20px; z-index: 0;"></i>
                            <i class="bi bi-cash-coin text-dark shadow-sm bg-white rounded-circle p-4 border" style="font-size: 8rem; position: relative; z-index: 1;"></i>
                        </div>
                    </div>
                    <div class="col-lg-6 position-relative z-1">
                        <span class="badge bg-success mb-3 px-3 py-2 rounded-pill">Versão 2.0 Web</span>
                        <h1 class="display-4 fw-bold lh-1 mb-3 text-dark">Adeus, planilha.<br><span class="text-success">Olá, controle total.</span></h1>
                        <p class="lead text-muted mb-4">A evolução da nossa clássica planilha de Excel. O <strong>Keep Ur Mnoey</strong> é a nossa plataforma exclusiva para gerenciar o dinheiro da casa, lançar gastos na hora e saber exatamente para onde nossa grana está indo.</p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <button type="button" class="btn btn-primary-custom text-white btn-lg px-4 me-md-2">Acessar Painel</button>
                            <button type="button" class="btn btn-outline-secondary btn-lg px-4">Saiba Mais</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secção de Recursos -->
        <div class="container px-4 py-5" id="recursos">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="fw-bold">Feito sob medida para nós</h2>
                    <p class="text-muted">Por que usar aplicativos genéricos com anúncios se podemos ter o nosso próprio sistema, com as nossas regras?</p>
                </div>
            </div>

            <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
                <div class="col feature-card text-center text-lg-start">
                    <div class="feature-icon-box shadow-sm">
                        <i class="bi bi-phone"></i>
                    </div>
                    <h3 class="h4 fw-bold">No celular ou no PC</h3>
                    <p class="text-muted">Lembrei de anotar o pão na padaria? É só abrir o site no celular. Interface totalmente responsiva e rápida.</p>
                </div>
                <div class="col feature-card text-center text-lg-start">
                    <div class="feature-icon-box shadow-sm">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="h4 fw-bold">Visão Compartilhada</h3>
                    <p class="text-muted">Os dois acompanham o orçamento em tempo real. Quem pagou o quê, quanto falta pro mês e quanto podemos gastar no final de semana.</p>
                </div>
                <div class="col feature-card text-center text-lg-start">
                    <div class="feature-icon-box shadow-sm">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                    <h3 class="h4 fw-bold">Gráficos Claros</h3>
                    <p class="text-muted">Chega de caçar números em linhas infinitas. Gráficos bonitos e resumos mensais diretos ao ponto para facilitar nossa vida.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-dark text-white py-5">
            <div class="container text-center py-5">
                <i class="bi bi-heart-fill text-danger fs-1 mb-3"></i>
                <h2 class="fw-bold">Pronto para organizar o mês?</h2>
                <p class="lead mb-4 text-white-50">Não deixe para anotar amanhã o que você gastou hoje.</p>
                <a href="{{ route('login') }}" class="btn btn-success btn-lg px-5 rounded-pill shadow">Fazer Login Agora</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="container py-4 mt-auto">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <p class="col-md-4 mb-0 text-muted">© 2026 Keep Ur Mnoey</p>
            <ul class="nav col-md-4 justify-content-end">
                <li class="nav-item"><a href="{{ url('/') }}" class="nav-link px-2 text-muted">Home</a></li>
            </ul>
        </div>
    </footer>
</body>
</html>
