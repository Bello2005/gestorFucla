<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesion - UNICLARETIANA</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css'])
</head>
<body class="auth-page">
    <div class="auth-container">
        <!-- Left Panel: Branding -->
        <div class="auth-brand">
            <div class="auth-brand-content">
                <div class="auth-brand-logo--img">
                    <img src="{{ asset('images/brand/logo-full.png') }}" alt="Uniclaretiana — Fundación Universitaria Claretiana" />
                </div>
                <div class="auth-brand-accent"></div>
                <blockquote class="auth-brand-quote">
                    "Formando líderes para la transformación social"
                </blockquote>
                <p class="auth-brand-subtitle">
                    Sistema de Gestión de Proyectos de Extensión y Proyección Social
                </p>
                <ul class="auth-features">
                    <li>
                        <span class="feature-icon"><i class="fas fa-shield-halved"></i></span>
                        Gestión segura de proyectos
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-chart-line"></i></span>
                        Estadísticas en tiempo real
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-file-export"></i></span>
                        Exportación a PDF, Excel y Word
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-users"></i></span>
                        Control de acceso por roles
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Panel: Form -->
        <div class="auth-form-panel">
            <div class="auth-form-header">
                <h2 class="auth-form-title">Bienvenido</h2>
                <p class="auth-form-desc">Ingresa tus credenciales para acceder al sistema</p>
            </div>

            @if(session('success'))
                <div class="auth-alert auth-alert--success">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="auth-alert auth-alert--error">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="email">Correo electronico</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="auth-input" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="usuario@uniclaretiana.edu.co"
                               required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Contrasena</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="auth-input" id="password" name="password"
                               placeholder="Ingresa tu contrasena"
                               required autocomplete="current-password"
                               style="padding-right: 44px;">
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Recordarme
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Olvidaste tu contrasena?</a>
                </div>

                <button type="submit" class="auth-submit">
                    Iniciar Sesion
                </button>
            </form>

            <div class="auth-footer">
                No tienes una cuenta? <a href="{{ route('access-requests.create') }}">Solicitar acceso</a>
            </div>
        </div>
    </div>

    @if(app()->environment('local'))
    <!-- DEV CREDENTIALS MODAL — only visible in local environment -->
    <div id="devModal" class="dev-modal-backdrop" role="dialog" aria-modal="true" aria-label="Credenciales de desarrollo">
        <div class="dev-modal">
            <!-- Header -->
            <div class="dev-modal-header">
                <div class="dev-modal-header-left">
                    <div class="dev-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        DEV
                    </div>
                    <span class="dev-modal-title">Credenciales de prueba</span>
                </div>
                <button class="dev-modal-close" id="devModalClose" aria-label="Cerrar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Separator -->
            <div class="dev-modal-sep"></div>

            <!-- Credential rows -->
            <div class="dev-modal-body">
                <div class="dev-cred-row" data-email="test1@uniclaretiana.edu.co" data-password="password">
                    <div class="dev-cred-avatar dev-cred-avatar--admin">A</div>
                    <div class="dev-cred-info">
                        <span class="dev-cred-name">Usuario Prueba 1 <span class="dev-role-pill dev-role-pill--admin">Admin</span></span>
                        <span class="dev-cred-email">test1@uniclaretiana.edu.co</span>
                        <span class="dev-cred-pass"><code>password</code></span>
                    </div>
                    <button class="dev-fill-btn" title="Usar estas credenciales">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Usar
                    </button>
                </div>

                <div class="dev-cred-row" data-email="test2@uniclaretiana.edu.co" data-password="password">
                    <div class="dev-cred-avatar dev-cred-avatar--user">S</div>
                    <div class="dev-cred-info">
                        <span class="dev-cred-name">Usuario Prueba 2 <span class="dev-role-pill dev-role-pill--supervisor">Supervisor</span></span>
                        <span class="dev-cred-email">test2@uniclaretiana.edu.co</span>
                        <span class="dev-cred-pass"><code>password</code></span>
                    </div>
                    <button class="dev-fill-btn" title="Usar estas credenciales">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Usar
                    </button>
                </div>

                <div class="dev-cred-row" data-email="test3@uniclaretiana.edu.co" data-password="password">
                    <div class="dev-cred-avatar dev-cred-avatar--user">U</div>
                    <div class="dev-cred-info">
                        <span class="dev-cred-name">Usuario Prueba 3 <span class="dev-role-pill">Usuario</span></span>
                        <span class="dev-cred-email">test3@uniclaretiana.edu.co</span>
                        <span class="dev-cred-pass"><code>password</code></span>
                    </div>
                    <button class="dev-fill-btn" title="Usar estas credenciales">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        Usar
                    </button>
                </div>
            </div>

            <!-- Footer hint -->
            <div class="dev-modal-footer">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Solo visible en entorno <strong>local</strong>. No aparece en producción.
            </div>
        </div>
    </div>

    <style>
        /* ===== DEV MODAL — Liquid Glass ===== */
        .dev-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            padding: 24px;
            pointer-events: none;
        }

        .dev-modal {
            pointer-events: all;
            width: 340px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            background: rgba(12, 20, 40, 0.72);
            backdrop-filter: blur(48px) saturate(180%);
            box-shadow:
                inset 0 0 0 0.5px rgba(255, 255, 255, 0.15),
                inset 0 2px 0 rgba(255, 255, 255, 0.20),
                0 32px 80px rgba(0, 0, 0, 0.55),
                0 8px 32px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            animation: dev-modal-in 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes dev-modal-in {
            from { opacity: 0; transform: translateY(20px) scale(0.95); filter: blur(4px); }
            to   { opacity: 1; transform: translateY(0)    scale(1);    filter: blur(0); }
        }

        .dev-modal.is-closing {
            animation: dev-modal-out 0.28s cubic-bezier(0.4, 0, 1, 1) both;
        }
        @keyframes dev-modal-out {
            from { opacity: 1; transform: translateY(0)    scale(1);    filter: blur(0); }
            to   { opacity: 0; transform: translateY(16px) scale(0.96); filter: blur(3px); }
        }

        /* Header */
        .dev-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 16px 14px;
        }
        .dev-modal-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dev-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px 3px 8px;
            border-radius: 99px;
            background: rgba(232, 185, 74, 0.18);
            border: 1px solid rgba(232, 185, 74, 0.35);
            color: #e8b94a;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
        }
        .dev-modal-title {
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: -0.01em;
        }
        .dev-modal-close {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.09);
            color: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.16s ease;
        }
        .dev-modal-close:hover {
            background: rgba(255, 255, 255, 0.16);
            color: white;
            transform: scale(1.08);
        }

        /* Separator */
        .dev-modal-sep {
            height: 1px;
            background: linear-gradient(90deg,
                transparent,
                rgba(255, 255, 255, 0.10) 20%,
                rgba(255, 255, 255, 0.10) 80%,
                transparent
            );
            margin: 0 16px;
        }

        /* Body */
        .dev-modal-body {
            padding: 12px 12px 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        /* Credential row */
        .dev-cred-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.07);
            background: rgba(255, 255, 255, 0.04);
            transition: background 0.16s ease, border-color 0.16s ease;
            cursor: default;
        }
        .dev-cred-row:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.14);
        }

        /* Avatar */
        .dev-cred-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .dev-cred-avatar--admin {
            background: linear-gradient(135deg, #2d4066 0%, #0f1a2e 100%);
            color: #e8b94a;
            border: 1px solid rgba(232, 185, 74, 0.3);
        }
        .dev-cred-avatar--user {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.10);
        }

        /* Info */
        .dev-cred-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }
        .dev-cred-name {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.88);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .dev-cred-email {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.40);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .dev-cred-pass {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.35);
        }
        .dev-cred-pass code {
            font-family: ui-monospace, monospace;
            background: rgba(255, 255, 255, 0.07);
            padding: 1px 5px;
            border-radius: 4px;
            color: rgba(232, 185, 74, 0.75);
            font-size: 11px;
        }

        /* Role pill */
        .dev-role-pill {
            font-size: 9px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 99px;
            background: rgba(255, 255, 255, 0.09);
            color: rgba(255, 255, 255, 0.45);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .dev-role-pill--admin {
            background: rgba(232, 185, 74, 0.15);
            color: #e8b94a;
            border: 1px solid rgba(232, 185, 74, 0.25);
        }
        .dev-role-pill--supervisor {
            background: rgba(100, 180, 255, 0.15);
            color: #64b4ff;
            border: 1px solid rgba(100, 180, 255, 0.25);
        }

        /* Fill button */
        .dev-fill-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.55);
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.16s ease;
            white-space: nowrap;
        }
        .dev-fill-btn:hover {
            background: rgba(232, 185, 74, 0.15);
            border-color: rgba(232, 185, 74, 0.35);
            color: #e8b94a;
            transform: scale(1.04);
        }
        .dev-fill-btn.is-filled {
            background: rgba(34, 197, 94, 0.15);
            border-color: rgba(34, 197, 94, 0.35);
            color: #4ade80;
        }

        /* Footer */
        .dev-modal-footer {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px 14px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.25);
        }
        .dev-modal-footer strong { color: rgba(255, 255, 255, 0.38); font-weight: 600; }

        /* Mobile — smaller, anchored bottom-center */
        @media (max-width: 600px) {
            .dev-modal-backdrop { padding: 16px; align-items: flex-end; justify-content: center; }
            .dev-modal { width: 100%; max-width: 380px; }
        }
    </style>
    @endif

    <script>
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        @if(app()->environment('local'))
        // Dev modal logic
        (function () {
            const modal    = document.getElementById('devModal');
            const closeBtn = document.getElementById('devModalClose');
            const emailInput    = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            function closeModal() {
                modal.querySelector('.dev-modal').classList.add('is-closing');
                setTimeout(() => { modal.style.display = 'none'; }, 280);
            }

            closeBtn.addEventListener('click', closeModal);

            // Click outside the card closes it
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });

            // Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modal.style.display !== 'none') closeModal();
            });

            // "Usar" buttons — fill the form fields
            document.querySelectorAll('.dev-cred-row').forEach(function (row) {
                const btn = row.querySelector('.dev-fill-btn');
                btn.addEventListener('click', function () {
                    emailInput.value    = row.dataset.email;
                    passwordInput.value = row.dataset.password;

                    // Visual feedback on the button
                    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Listo';
                    btn.classList.add('is-filled');

                    // Close after short delay
                    setTimeout(closeModal, 600);
                });
            });
        })();
        @endif
    </script>
</body>
</html>
