<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Gestor de Archivos Uniclaretiana</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FFD700;
            --primary-dark: #F4C400;
            --secondary: #FFFFFF;
            --text-dark: #1E1E1E;
            --text-light: #666666;
            --gray-light: #F5F5F5;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #FFD700 0%, #FFFFFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            width: 100%;
            max-width: 1100px;
            background: var(--secondary);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            display: flex;
            min-height: 600px;
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--text-dark);
            position: relative;
            overflow: hidden;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -50px;
            left: -50px;
        }
        
        .login-left::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -100px;
            right: -100px;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 40px;
            z-index: 1;
        }
        
        .logo-container h1 {
            font-size: 28px;
            font-weight: 700;
            margin-top: 20px;
        }
        
        .illustration {
            width: 100%;
            max-width: 350px;
            margin-bottom: 30px;
            z-index: 1;
        }
        
        .features {
            list-style: none;
            margin-top: 30px;
            z-index: 1;
        }
        
        .features li {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .features li::before {
            content: '✓';
            background: var(--secondary);
            color: var(--primary);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .login-right {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            margin-bottom: 40px;
        }
        
        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: var(--text-light);
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }
        
        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            font-size: 16px;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 8px;
            accent-color: var(--primary);
        }
        
        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            border: none;
            border-radius: 10px;
            color: var(--text-dark);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 20px;
        }
        
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .divider {
            text-align: center;
            position: relative;
            margin: 25px 0;
            color: var(--text-light);
        }
        
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e1e1e1;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            z-index: 1;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gray-light);
            border: 1px solid #e1e1e1;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: var(--text-light);
        }
        
        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .login-left {
                padding: 30px;
            }
            
            .illustration {
                max-width: 250px;
            }
        }
        
        @media (max-width: 576px) {
            .login-right {
                padding: 30px;
            }
            
            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="logo-container">
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="40" r="38" stroke="#1E1E1E" stroke-width="4"/>
                    <path d="M25 40L35 50L55 30" stroke="#1E1E1E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h1>Uniclaretiana</h1>
            </div>
            
            <svg class="illustration" viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg">
                <path d="M150,300 C50,250 50,150 150,100 C250,50 350,100 350,200 C350,300 250,350 150,300 Z" fill="#FFFFFF" opacity="0.2"/>
                <path d="M250,100 C350,150 350,250 250,300 C150,350 50,300 50,200 C50,100 150,50 250,100 Z" fill="#FFFFFF" opacity="0.2"/>
                <circle cx="250" cy="200" r="80" fill="#FFFFFF"/>
                <circle cx="150" cy="200" r="80" fill="#FFFFFF"/>
                <path d="M150,200 L250,200" stroke="#1E1E1E" stroke-width="8" stroke-linecap="round"/>
                <path d="M200,150 L200,250" stroke="#1E1E1E" stroke-width="8" stroke-linecap="round"/>
            </svg>
            
            <ul class="features">
                <li>Gestión segura de archivos académicos</li>
                <li>Acceso multi-dispositivo</li>
                <li>Colaboración en tiempo real</li>
                <li>Compatible con todos los formatos</li>
            </ul>
        </div>
        
        <div class="login-right">
            <div class="login-header">
                <h2>Bienvenido al Gestor de Archivos</h2>
                <p>Ingresa tus credenciales para acceder a tu cuenta</p>
            </div>
            
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-with-icon">
                        <span class="input-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.5 6.66669L9.0755 11.0504C9.63533 11.4236 10.3647 11.4236 10.9245 11.0504L17.5 6.66669M4.16667 15.8334H15.8333C16.7538 15.8334 17.5 15.0872 17.5 14.1667V5.83335C17.5 4.91288 16.7538 4.16669 15.8333 4.16669H4.16667C3.24619 4.16669 2.5 4.91288 2.5 5.83335V14.1667C2.5 15.0872 3.24619 15.8334 4.16667 15.8334Z" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="usuario@uniclaretiana.edu.co" required autofocus autocomplete="username">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-with-icon">
                        <span class="input-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.8333 9.16669H4.16667C3.24619 9.16669 2.5 9.91288 2.5 10.8334V15.8334C2.5 16.7539 3.24619 17.5 4.16667 17.5H15.8333C16.7538 17.5 17.5 16.7539 17.5 15.8334V10.8334C17.5 9.91288 16.7538 9.16669 15.8333 9.16669Z" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M5.83333 9.16669V5.83335C5.83333 4.72828 6.27232 3.66848 7.05372 2.88708C7.83512 2.10568 8.89493 1.66669 10 1.66669C11.1051 1.66669 12.1649 2.10568 12.9463 2.88708C13.7277 3.66848 14.1667 4.72828 14.1667 5.83335V9.16669" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required autocomplete="current-password">
                        <button type="button" class="password-toggle" id="togglePassword">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.66675 10C1.66675 10 4.16675 4.16669 10.0001 4.16669C15.8334 4.16669 18.3334 10 18.3334 10C18.3334 10 15.8334 15.8334 10.0001 15.8334C4.16675 15.8334 1.66675 10 1.66675 10Z" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 12.5C11.3807 12.5 12.5 11.3807 12.5 10C12.5 8.61929 11.3807 7.5 10 7.5C8.61929 7.5 7.5 8.61929 7.5 10C7.5 11.3807 8.61929 12.5 10 12.5Z" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Recordarme</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-password">¿Olvidaste tu contraseña?</a>
                </div>
                
                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>

            <div class="register-link">
                ¿No tienes una cuenta? <a href="{{ route('access-requests.create') }}">Solicitar acceso</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <script>
        // Funcionalidad para mostrar/ocultar contraseña
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Cambiar el icono
            this.querySelector('svg').innerHTML = type === 'password' 
                ? '<path d="M1.66675 10C1.66675 10 4.16675 4.16669 10.0001 4.16669C15.8334 4.16669 18.3334 10 18.3334 10C18.3334 10 15.8334 15.8334 10.0001 15.8334C4.16675 15.8334 1.66675 10 1.66675 10Z" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 12.5C11.3807 12.5 12.5 11.3807 12.5 10C12.5 8.61929 11.3807 7.5 10 7.5C8.61929 7.5 7.5 8.61929 7.5 10C7.5 11.3807 8.61929 12.5 10 12.5Z" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
                : '<path d="M3.33325 3.33331L16.6666 16.6666M10.8333 10.8333C10.3923 11.1633 9.87592 11.338 9.34502 11.3367C8.81412 11.3354 8.29852 11.1583 7.85905 10.8265C7.41958 10.4947 7.07707 10.0234 6.87859 9.4727C6.68011 8.92198 6.63461 8.31726 6.74825 7.73831M14.9999 12.0833C14.0674 13.0333 12.8333 13.6593 11.6666 13.9583M6.66659 13.9583C5.07742 13.5133 3.33325 12.0833 2.08325 10C2.91659 8.74998 3.74992 7.91665 4.99992 7.08331" stroke="#666666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
        });
        
        // Efectos de hover mejorados
        const buttons = document.querySelectorAll('button');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });
        
        // Configurar el token CSRF para todas las llamadas AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
</body>
</html>