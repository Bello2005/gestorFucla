<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Restringido — Uniclaretiana</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ===== PAGE SHELL ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-sans);
            background: linear-gradient(160deg, #0f1a2e 0%, #1b2a4a 45%, #2d4066 100%);
            overflow: hidden;
            position: relative;
        }

        /* ===== AMBIENT GLOW ORBS ===== */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }
        .orb--gold {
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(198,146,42,0.22) 0%, transparent 70%);
            top: -120px; right: -80px;
            animation: orb-drift 8s ease-in-out infinite alternate;
        }
        .orb--navy {
            width: 520px; height: 520px;
            background: radial-gradient(circle, rgba(45,64,102,0.45) 0%, transparent 70%);
            bottom: -160px; left: -140px;
            animation: orb-drift 11s ease-in-out infinite alternate-reverse;
        }
        @keyframes orb-drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, 20px) scale(1.08); }
        }

        /* ===== GLASS CARD ===== */
        .error-card {
            position: relative;
            z-index: 10;
            max-width: 520px;
            width: calc(100% - 32px);
            padding: 48px 40px 40px;
            border-radius: 32px;
            border: 1px solid rgba(255,255,255,0.18);
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(48px) saturate(160%);
            box-shadow:
                inset 0 0 0 0.5px rgba(255,255,255,0.12),
                inset 0 2px 0 rgba(255,255,255,0.18),
                0 40px 100px rgba(0,0,0,0.45),
                0 8px 32px rgba(0,0,0,0.25);
            text-align: center;
            animation: card-in 0.6s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes card-in {
            from { opacity: 0; transform: scale(0.92) translateY(20px); filter: blur(6px); }
            to   { opacity: 1; transform: scale(1)   translateY(0);     filter: blur(0); }
        }

        /* ===== CONSTRUCTION SCENE ===== */
        .scene {
            position: relative;
            height: 160px;
            margin-bottom: 28px;
            overflow: visible;
        }

        /* Ground */
        .ground {
            position: absolute;
            bottom: 0; left: -40px; right: -40px;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(198,146,42,0.6) 30%, rgba(198,146,42,0.6) 70%, transparent);
            border-radius: 2px;
        }

        /* Error code big */
        .code-403 {
            position: absolute;
            top: 0; left: 50%;
            transform: translateX(-50%);
            font-size: 88px;
            font-weight: 900;
            letter-spacing: -4px;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 2px rgba(255,255,255,0.15);
            user-select: none;
            z-index: 0;
        }

        /* ===== WORKER SVG ANIMATIONS ===== */
        .worker {
            position: absolute;
            bottom: 4px;
        }

        /* Worker A — left — drilling */
        .worker-a { left: 30px; animation: worker-a-bounce 0.55s ease-in-out infinite alternate; }
        @keyframes worker-a-bounce {
            from { transform: translateY(0); }
            to   { transform: translateY(-5px); }
        }

        /* Worker B — center — waving sign */
        .worker-b { left: 50%; transform: translateX(-50%); }

        /* Worker C — right — shoveling */
        .worker-c { right: 30px; animation: worker-c-dig 0.7s ease-in-out infinite alternate; }
        @keyframes worker-c-dig {
            from { transform: rotate(0deg); transform-origin: bottom center; }
            to   { transform: rotate(4deg); transform-origin: bottom center; }
        }

        /* Sign swing on worker B */
        .sign-swing {
            animation: sign-swing 1.2s ease-in-out infinite alternate;
            transform-origin: top center;
        }
        @keyframes sign-swing {
            from { transform: rotate(-8deg); }
            to   { transform: rotate(8deg); }
        }

        /* Drill shake */
        .drill-shake {
            animation: drill-shake 0.12s linear infinite alternate;
            transform-origin: bottom left;
        }
        @keyframes drill-shake {
            from { transform: rotate(-3deg); }
            to   { transform: rotate(3deg); }
        }

        /* Cone bounce */
        .cone {
            position: absolute;
            bottom: 4px;
            animation: cone-pulse 2s ease-in-out infinite;
        }
        .cone-left  { left: 14px; }
        .cone-right { right: 14px; }
        @keyframes cone-pulse {
            0%, 100% { opacity: 0.8; }
            50%       { opacity: 1; }
        }

        /* Dust particles from drilling */
        .dust {
            position: absolute;
            bottom: 14px;
            left: 62px;
        }
        .dust-dot {
            position: absolute;
            width: 4px; height: 4px;
            border-radius: 50%;
            background: rgba(198,146,42,0.7);
            animation: dust-fly 0.8s ease-out infinite;
        }
        .dust-dot:nth-child(1) { animation-delay: 0s;    --dx: -8px;  --dy: -12px; }
        .dust-dot:nth-child(2) { animation-delay: 0.2s;  --dx: 6px;   --dy: -15px; }
        .dust-dot:nth-child(3) { animation-delay: 0.4s;  --dx: -4px;  --dy: -8px; }
        @keyframes dust-fly {
            0%   { opacity: 1; transform: translate(0,0) scale(1); }
            100% { opacity: 0; transform: translate(var(--dx), var(--dy)) scale(0.3); }
        }

        /* ===== TYPOGRAPHY ===== */
        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }

        .error-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.55);
            line-height: 1.6;
            margin-bottom: 32px;
            max-width: 360px;
            margin-left: auto;
            margin-right: auto;
        }

        .error-subtitle strong {
            color: rgba(232,185,74,0.9);
            font-weight: 600;
        }

        /* ===== ACTIONS ===== */
        .error-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .btn-back--primary {
            background: linear-gradient(135deg, #c6922a 0%, #8b6914 100%);
            color: white;
            box-shadow: 0 4px 16px rgba(198,146,42,0.35);
        }
        .btn-back--primary:hover {
            background: linear-gradient(135deg, #e8b94a 0%, #c6922a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(198,146,42,0.45);
            color: white;
        }

        .btn-back--ghost {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.7);
        }
        .btn-back--ghost:hover {
            background: rgba(255,255,255,0.14);
            color: white;
            transform: translateY(-1px);
        }

        /* ===== LOGO ===== */
        .error-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 32px;
            opacity: 0.65;
        }
        .error-logo img { width: 32px; height: 32px; }
        .error-logo span {
            font-size: 13px;
            font-weight: 700;
            color: white;
            letter-spacing: 0.05em;
        }

        /* ===== MOBILE ===== */
        @media (max-width: 479px) {
            .error-card { padding: 36px 24px 32px; }
            .scene { height: 130px; }
            .code-403 { font-size: 66px; }
            .worker-a { left: 10px; }
            .worker-c { right: 10px; }
            .cone-left  { left: 0; }
            .cone-right { right: 0; }
        }
    </style>
</head>
<body>

<div class="orb orb--gold"></div>
<div class="orb orb--navy"></div>

<div class="error-card">

    <!-- Logo -->
    <div class="error-logo">
        <img src="{{ asset('images/brand/logo-mark.svg') }}" alt="Uniclaretiana">
        <span>UNICLARETIANA</span>
    </div>

    <!-- Construction scene -->
    <div class="scene">
        <span class="code-403">403</span>

        <!-- Traffic cone left -->
        <svg class="cone cone-left" width="22" height="30" viewBox="0 0 22 30">
            <polygon points="11,2 20,28 2,28" fill="#e8b94a" opacity="0.9"/>
            <rect x="2" y="24" width="18" height="4" rx="2" fill="#c6922a"/>
            <rect x="7" y="12" width="8" height="2.5" rx="1" fill="white" opacity="0.8"/>
            <rect x="5" y="18" width="12" height="2.5" rx="1" fill="white" opacity="0.8"/>
        </svg>

        <!-- Traffic cone right -->
        <svg class="cone cone-right" width="22" height="30" viewBox="0 0 22 30">
            <polygon points="11,2 20,28 2,28" fill="#e8b94a" opacity="0.9"/>
            <rect x="2" y="24" width="18" height="4" rx="2" fill="#c6922a"/>
            <rect x="7" y="12" width="8" height="2.5" rx="1" fill="white" opacity="0.8"/>
            <rect x="5" y="18" width="12" height="2.5" rx="1" fill="white" opacity="0.8"/>
        </svg>

        <!-- Worker A (left) — drilling -->
        <svg class="worker worker-a" width="52" height="110" viewBox="0 0 52 110">
            <!-- Hard hat -->
            <ellipse cx="26" cy="22" rx="18" ry="6" fill="#e8b94a"/>
            <ellipse cx="26" cy="20" rx="14" ry="11" fill="#e8b94a"/>
            <!-- Head -->
            <circle cx="26" cy="34" r="10" fill="#f5cba0"/>
            <!-- Eyes -->
            <circle cx="22" cy="33" r="1.5" fill="#333"/>
            <circle cx="30" cy="33" r="1.5" fill="#333"/>
            <!-- Safety vest -->
            <rect x="14" y="44" width="24" height="26" rx="4" fill="#ff6b1a" opacity="0.9"/>
            <rect x="16" y="46" width="20" height="22" rx="3" fill="#e85d10"/>
            <!-- Hi-vis stripes -->
            <rect x="14" y="55" width="24" height="3" rx="1" fill="#e8b94a" opacity="0.9"/>
            <!-- Arms -->
            <rect x="5" y="46" width="9" height="18" rx="4" fill="#f5cba0"/>
            <rect x="38" y="46" width="9" height="18" rx="4" fill="#f5cba0"/>
            <!-- Legs -->
            <rect x="16" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <rect x="27" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <!-- Boots -->
            <rect x="14" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <rect x="26" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <!-- Drill (rotated toward ground) -->
            <g class="drill-shake">
                <rect x="2" y="62" width="14" height="6" rx="3" fill="#555"/>
                <rect x="-4" y="64" width="10" height="3" rx="1.5" fill="#888"/>
                <rect x="12" y="63" width="8" height="4" rx="2" fill="#777"/>
            </g>
        </svg>

        <!-- Dust from drilling -->
        <div class="dust">
            <div class="dust-dot"></div>
            <div class="dust-dot"></div>
            <div class="dust-dot"></div>
        </div>

        <!-- Worker B (center) — holding STOP sign -->
        <svg class="worker worker-b" width="56" height="130" viewBox="0 0 56 130">
            <!-- Hard hat (red for safety marshal) -->
            <ellipse cx="28" cy="22" rx="18" ry="6" fill="#e53e3e"/>
            <ellipse cx="28" cy="20" rx="14" ry="11" fill="#e53e3e"/>
            <!-- Head -->
            <circle cx="28" cy="34" r="10" fill="#f5cba0"/>
            <circle cx="24" cy="33" r="1.5" fill="#333"/>
            <circle cx="32" cy="33" r="1.5" fill="#333"/>
            <!-- Smile -->
            <path d="M24 37 Q28 41 32 37" stroke="#333" stroke-width="1.5" fill="none" stroke-linecap="round"/>
            <!-- Vest -->
            <rect x="16" y="44" width="24" height="26" rx="4" fill="#ff6b1a" opacity="0.9"/>
            <rect x="18" y="46" width="20" height="22" rx="3" fill="#e85d10"/>
            <rect x="16" y="55" width="24" height="3" rx="1" fill="#e8b94a" opacity="0.9"/>
            <!-- Legs -->
            <rect x="18" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <rect x="29" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <rect x="16" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <rect x="28" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <!-- Right arm raised holding sign pole -->
            <rect x="40" y="44" width="8" height="20" rx="4" fill="#f5cba0"/>
            <!-- Left arm normal -->
            <rect x="8"  y="46" width="8" height="18" rx="4" fill="#f5cba0"/>
            <!-- Pole -->
            <rect x="45" y="28" width="4" height="52" rx="2" fill="#aaa"/>
            <!-- STOP sign (octagon approx) swinging -->
            <g class="sign-swing">
                <polygon points="47,4 53,4 57,10 57,20 53,26 41,26 37,20 37,10 41,4" fill="#e53e3e"/>
                <text x="47" y="20" font-size="7" font-weight="900" fill="white" text-anchor="middle" font-family="system-ui,sans-serif">STOP</text>
            </g>
        </svg>

        <!-- Worker C (right) — shoveling -->
        <svg class="worker worker-c" width="52" height="110" viewBox="0 0 52 110">
            <!-- Hard hat -->
            <ellipse cx="26" cy="22" rx="18" ry="6" fill="#e8b94a"/>
            <ellipse cx="26" cy="20" rx="14" ry="11" fill="#e8b94a"/>
            <!-- Head -->
            <circle cx="26" cy="34" r="10" fill="#f5cba0"/>
            <circle cx="22" cy="33" r="1.5" fill="#333"/>
            <circle cx="30" cy="33" r="1.5" fill="#333"/>
            <!-- Vest -->
            <rect x="14" y="44" width="24" height="26" rx="4" fill="#ff6b1a" opacity="0.9"/>
            <rect x="16" y="46" width="20" height="22" rx="3" fill="#e85d10"/>
            <rect x="14" y="55" width="24" height="3" rx="1" fill="#e8b94a" opacity="0.9"/>
            <!-- Arms -->
            <rect x="5"  y="46" width="9" height="18" rx="4" fill="#f5cba0"/>
            <rect x="38" y="44" width="9" height="22" rx="4" fill="#f5cba0"/>
            <!-- Legs -->
            <rect x="16" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <rect x="27" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <rect x="14" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <rect x="26" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <!-- Shovel handle -->
            <rect x="42" y="30" width="4" height="48" rx="2" fill="#8b5e3c" transform="rotate(15 44 54)"/>
            <!-- Shovel head -->
            <ellipse cx="50" cy="76" rx="9" ry="6" fill="#555" transform="rotate(15 44 54)"/>
        </svg>

        <div class="ground"></div>
    </div>

    <!-- Text -->
    <h1 class="error-title">Zona Restringida</h1>
    <p class="error-subtitle">
        Esta área está en obras para <strong>personal autorizado</strong> únicamente.
        Tu cuenta no tiene los permisos necesarios para acceder aquí.
    </p>

    <!-- Actions -->
    <div class="error-actions">
        <a href="{{ url('/dashboard') }}" class="btn-back btn-back--primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Ir al Dashboard
        </a>
        <a href="javascript:history.back()" class="btn-back btn-back--ghost">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
            Volver
        </a>
    </div>

</div>

</body>
</html>
