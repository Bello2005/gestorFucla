

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #FFD700 0%, #FFFFFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .access-container {
            width: 100%;
            max-width: 1100px;
            background: var(--secondary);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            display: flex;
            min-height: 600px;
        }
        .access-left {
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
        .access-left::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -50px;
            left: -50px;
        }
        .access-left::after {
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
        .access-right {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .access-header {
            margin-bottom: 40px;
        }
        .access-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }
        .access-header p {
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
        .btn-access {
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
        .btn-access:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .back-link {
            width: 100%;
            text-align: center;
            margin-top: 10px;
        }
        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1.1rem 1.4rem;
            margin-bottom: 1.7rem;
            font-size: 1.01rem;
            font-weight: 500;
            box-shadow: 0 1.5px 8px rgba(255,214,0,0.07);
        }
        .alert-success {
            background-color: #fffde7;
            color: var(--text-dark);
            border-left: 5px solid var(--primary);
        }
        .alert-danger {
            background-color: #fffde7;
            color: #ef4444;
            border-left: 5px solid #ef4444;
        }
        @media (max-width: 992px) {
            .access-container {
                flex-direction: column;
                max-width: 500px;
            }
            .access-left {
                padding: 30px;
            }
            .illustration {
                max-width: 250px;
            }
        }
        @media (max-width: 576px) {
            .access-right {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="access-container">
        <div class="access-left">
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
                <li>Recupera tu acceso fácilmente</li>
                <li>Enlace seguro a tu correo</li>
                <li>Soporte rápido y confiable</li>
            </ul>
        </div>
        <div class="access-right">
            <div class="access-header">
                <h2>¿Olvidaste tu contraseña?</h2>
                <p>Ingresa tu correo electrónico y te enviaremos las instrucciones para recuperarla.</p>
            </div>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-with-icon">
                        <span class="input-icon">
                            <svg width="20" height="20" fill="none" stroke="#666" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M22 6 12 13 2 6"/></svg>
                        </span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="tu.correo@ejemplo.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn-access">Enviar Instrucciones</button>
                <div class="back-link">
                    <a href="{{ route('login') }}">&larr; Volver al Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>