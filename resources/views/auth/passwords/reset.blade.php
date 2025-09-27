
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary: #FFD700;
            --primary-dark: #F4C400;
            --secondary: #FFFFFF;
            --accent: #1E1E1E;
            --text-dark: #1E1E1E;
            --text-light: #666666;
            --gray-light: #F5F5F5;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.10);
            --transition: all 0.3s cubic-bezier(.4,1.4,.6,1);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(120deg, #fff, #f7f8fa 80%, #fff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .reset-container {
            width: 100%;
            max-width: 500px;
            background: var(--secondary);
            border-radius: 24px;
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
            animation: fadeIn 0.7s;
        }
        .reset-header {
            background: linear-gradient(90deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: var(--accent);
            padding: 2.5rem 2rem 1.5rem 2rem;
            text-align: center;
            border-bottom: 1.5px solid #ffe066;
            position: relative;
        }
        .reset-header h2 {
            font-size: 2.1rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 0 #fffbe7;
        }
        .reset-header p {
            color: var(--text-light);
            font-size: 1.08rem;
            font-weight: 500;
        }
        .reset-body {
            padding: 2.2rem 2rem 2rem 2rem;
            background: var(--secondary);
        }
        .form-group {
            margin-bottom: 1.7rem;
        }
        .form-label {
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 0.75rem;
            font-size: 1.05rem;
            letter-spacing: 0.2px;
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
        .form-control.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.08);
        }
        .invalid-feedback {
            font-size: 0.93rem;
            margin-top: 0.5rem;
            color: #ef4444;
            font-weight: 600;
        }
        .btn-reset {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            border: none;
            border-radius: 10px;
            color: var(--accent);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 18px;
        }
        .btn-reset:hover {
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
        .alert-danger {
            background-color: #fffbe7;
            color: #ef4444;
            border-left: 5px solid #ef4444;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 576px) {
            .reset-header, .reset-body { padding: 1.3rem 0.7rem; }
            .reset-container { margin-bottom: 1.2rem; }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h2>Restablecer Contraseña</h2>
            <p>Por favor, ingresa tu nueva contraseña para continuar.</p>
        </div>
        <div class="reset-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <div class="form-group">
                    <label for="password" class="form-label">Nueva Contraseña</label>
                    <div class="input-with-icon">
                        <span class="input-icon">
                            <svg width="20" height="20" fill="none" stroke="#666" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                    <div class="input-with-icon">
                        <span class="input-icon">
                            <svg width="20" height="20" fill="none" stroke="#666" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>
                <button type="submit" class="btn-reset">Guardar Nueva Contraseña</button>
                <div class="back-link">
                    <a href="{{ route('login') }}">&larr; Volver al Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>