<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada — Uniclaretiana</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
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

        /* ===== STARS ===== */
        .stars {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .star {
            position: absolute;
            border-radius: 50%;
            background: white;
            animation: star-twinkle var(--d, 3s) ease-in-out infinite alternate;
        }
        @keyframes star-twinkle {
            from { opacity: var(--o1, 0.2); transform: scale(1); }
            to   { opacity: var(--o2, 0.7); transform: scale(1.4); }
        }

        /* ===== AMBIENT ORBS ===== */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }
        .orb--gold {
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(198,146,42,0.20) 0%, transparent 70%);
            top: -100px; right: -60px;
            animation: orb-drift 9s ease-in-out infinite alternate;
        }
        .orb--blue {
            width: 480px; height: 480px;
            background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
            bottom: -150px; left: -120px;
            animation: orb-drift 13s ease-in-out infinite alternate-reverse;
        }
        @keyframes orb-drift {
            from { transform: translate(0,0) scale(1); }
            to   { transform: translate(25px,18px) scale(1.07); }
        }

        /* ===== GLASS CARD ===== */
        .error-card {
            position: relative;
            z-index: 10;
            max-width: 540px;
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

        /* ===== SCENE ===== */
        .scene {
            position: relative;
            height: 170px;
            margin-bottom: 28px;
            overflow: visible;
        }

        .ground {
            position: absolute;
            bottom: 0; left: -40px; right: -40px;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(198,146,42,0.5) 30%, rgba(198,146,42,0.5) 70%, transparent);
            border-radius: 2px;
        }

        .code-404 {
            position: absolute;
            top: 0; left: 50%;
            transform: translateX(-50%);
            font-size: 88px;
            font-weight: 900;
            letter-spacing: -4px;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 2px rgba(255,255,255,0.13);
            user-select: none;
            z-index: 0;
        }

        /* Workers */
        .worker { position: absolute; bottom: 4px; }

        /* Worker A — left — scratching head (confused) */
        .worker-a {
            left: 20px;
            animation: scratch 1.4s ease-in-out infinite alternate;
            transform-origin: bottom center;
        }
        @keyframes scratch {
            0%   { transform: rotate(-1deg); }
            50%  { transform: rotate(1.5deg); }
            100% { transform: rotate(-1deg); }
        }

        /* Worker B — center — holding map */
        .worker-b { left: 50%; transform: translateX(-50%); }

        /* Map rotate */
        .map-rotate {
            animation: map-turn 3s ease-in-out infinite alternate;
            transform-origin: center center;
        }
        @keyframes map-turn {
            from { transform: rotate(-12deg); }
            to   { transform: rotate(12deg); }
        }

        /* Worker C — right — magnifying glass */
        .worker-c { right: 18px; animation: search 1.8s ease-in-out infinite alternate; }
        @keyframes search {
            from { transform: translateY(0) rotate(0deg); }
            to   { transform: translateY(-6px) rotate(5deg); }
        }

        /* Floating ? marks */
        .floating-q {
            position: absolute;
            font-size: 20px;
            font-weight: 900;
            color: rgba(232,185,74,0.6);
            animation: float-q var(--dur,2.5s) ease-in-out infinite alternate;
        }
        .q1 { top: 30px; left: 50px;  --dur: 2.2s; animation-delay: 0s; }
        .q2 { top: 20px; right: 60px; --dur: 3.1s; animation-delay: 0.4s; font-size: 14px; color: rgba(232,185,74,0.35); }
        .q3 { top: 55px; left: 160px; --dur: 2.7s; animation-delay: 0.8s; font-size: 12px; color: rgba(232,185,74,0.25); }
        @keyframes float-q {
            from { transform: translateY(0) rotate(-5deg); opacity: 0.5; }
            to   { transform: translateY(-10px) rotate(5deg); opacity: 1; }
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
            color: rgba(255,255,255,0.52);
            line-height: 1.65;
            margin-bottom: 32px;
            max-width: 370px;
            margin-left: auto;
            margin-right: auto;
        }
        .error-subtitle strong {
            color: rgba(232,185,74,0.9);
            font-weight: 600;
        }

        /* ===== SEARCH BAR (decorative hint) ===== */
        .error-hint {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 99px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            font-size: 12px;
            color: rgba(255,255,255,0.35);
            margin-bottom: 28px;
            font-family: ui-monospace, monospace;
            letter-spacing: 0.02em;
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
            background: rgba(255,255,255,0.13);
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
            opacity: 0.6;
        }
        .error-logo img { width: 32px; height: 32px; }
        .error-logo span { font-size: 13px; font-weight: 700; color: white; letter-spacing: 0.05em; }

        /* ===== MOBILE ===== */
        @media (max-width: 479px) {
            .error-card { padding: 36px 22px 32px; }
            .scene { height: 140px; }
            .code-404 { font-size: 66px; }
            .worker-a { left: 4px; }
            .worker-c { right: 4px; }
            .q2, .q3 { display: none; }
        }
    </style>
</head>
<body>

<!-- Starfield -->
<div class="stars" id="stars"></div>

<div class="orb orb--gold"></div>
<div class="orb orb--blue"></div>

<div class="error-card">

    <!-- Logo -->
    <div class="error-logo">
        <img src="{{ asset('images/brand/logo-mark.svg') }}" alt="Uniclaretiana">
        <span>UNICLARETIANA</span>
    </div>

    <!-- Scene -->
    <div class="scene">
        <span class="code-404">404</span>

        <!-- Floating question marks -->
        <span class="floating-q q1">?</span>
        <span class="floating-q q2">?</span>
        <span class="floating-q q3">?</span>

        <!-- Worker A — left — confused, hand on head -->
        <svg class="worker worker-a" width="50" height="108" viewBox="0 0 50 108">
            <!-- Hard hat -->
            <ellipse cx="25" cy="22" rx="17" ry="6" fill="#e8b94a"/>
            <ellipse cx="25" cy="20" rx="13" ry="11" fill="#e8b94a"/>
            <!-- Head -->
            <circle cx="25" cy="34" r="10" fill="#f5cba0"/>
            <!-- Eyes — confused: one brow raised -->
            <circle cx="21" cy="33" r="1.5" fill="#333"/>
            <circle cx="29" cy="33" r="1.5" fill="#333"/>
            <!-- Confused brow -->
            <path d="M19 30 Q21 28.5 23 30" stroke="#555" stroke-width="1.2" fill="none" stroke-linecap="round"/>
            <path d="M27 29 Q29 31 31 29" stroke="#555" stroke-width="1.2" fill="none" stroke-linecap="round"/>
            <!-- Wavy mouth (unsure) -->
            <path d="M21 38 Q23 36.5 25 38 Q27 39.5 29 38" stroke="#555" stroke-width="1.2" fill="none" stroke-linecap="round"/>
            <!-- Vest -->
            <rect x="13" y="44" width="24" height="26" rx="4" fill="#ff6b1a" opacity="0.9"/>
            <rect x="15" y="46" width="20" height="22" rx="3" fill="#e85d10"/>
            <rect x="13" y="55" width="24" height="3" rx="1" fill="#e8b94a" opacity="0.9"/>
            <!-- Left arm down -->
            <rect x="4"  y="46" width="9" height="18" rx="4" fill="#f5cba0"/>
            <!-- Right arm raised — hand on head -->
            <rect x="37" y="34" width="8" height="16" rx="4" fill="#f5cba0" transform="rotate(-30 41 34)"/>
            <!-- Legs -->
            <rect x="15" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <rect x="26" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <!-- Boots -->
            <rect x="13" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <rect x="25" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
        </svg>

        <!-- Worker B — center — holding a map upside-down -->
        <svg class="worker worker-b" width="62" height="130" viewBox="0 0 62 130">
            <!-- Hard hat -->
            <ellipse cx="31" cy="22" rx="18" ry="6" fill="#3b82f6"/>
            <ellipse cx="31" cy="20" rx="14" ry="11" fill="#3b82f6"/>
            <!-- Head -->
            <circle cx="31" cy="34" r="10" fill="#f5cba0"/>
            <circle cx="27" cy="33" r="1.5" fill="#333"/>
            <circle cx="35" cy="33" r="1.5" fill="#333"/>
            <!-- Frown -->
            <path d="M27 38 Q31 35 35 38" stroke="#555" stroke-width="1.2" fill="none" stroke-linecap="round"/>
            <!-- Sweat drop -->
            <ellipse cx="36" cy="28" rx="2.5" ry="3.5" fill="rgba(59,130,246,0.6)"/>
            <!-- Vest -->
            <rect x="19" y="44" width="24" height="26" rx="4" fill="#ff6b1a" opacity="0.9"/>
            <rect x="21" y="46" width="20" height="22" rx="3" fill="#e85d10"/>
            <rect x="19" y="55" width="24" height="3" rx="1" fill="#e8b94a" opacity="0.9"/>
            <!-- Both arms forward holding map -->
            <rect x="7"  y="46" width="9" height="22" rx="4" fill="#f5cba0" transform="rotate(20 11 46)"/>
            <rect x="46" y="46" width="9" height="22" rx="4" fill="#f5cba0" transform="rotate(-20 50 46)"/>
            <!-- Legs -->
            <rect x="21" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <rect x="32" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <rect x="19" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <rect x="31" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <!-- Map (held upside-down, rotated) -->
            <g class="map-rotate">
                <rect x="16" y="52" width="30" height="22" rx="3" fill="#fefce8" stroke="#d4a843" stroke-width="1"/>
                <!-- Map lines -->
                <line x1="20" y1="58" x2="38" y2="58" stroke="#9ca3af" stroke-width="1" stroke-dasharray="2,2"/>
                <line x1="20" y1="62" x2="34" y2="62" stroke="#9ca3af" stroke-width="1" stroke-dasharray="2,2"/>
                <line x1="20" y1="66" x2="40" y2="66" stroke="#9ca3af" stroke-width="1" stroke-dasharray="2,2"/>
                <!-- Map pin -->
                <circle cx="30" cy="60" r="2.5" fill="#e53e3e"/>
                <line x1="30" y1="62" x2="30" y2="67" stroke="#e53e3e" stroke-width="1.2"/>
                <!-- "?" on map -->
                <text x="34" y="66" font-size="8" font-weight="700" fill="#c6922a" font-family="system-ui,sans-serif">?</text>
            </g>
        </svg>

        <!-- Worker C — right — magnifying glass -->
        <svg class="worker worker-c" width="56" height="115" viewBox="0 0 56 115">
            <!-- Hard hat -->
            <ellipse cx="26" cy="22" rx="17" ry="6" fill="#e8b94a"/>
            <ellipse cx="26" cy="20" rx="13" ry="11" fill="#e8b94a"/>
            <!-- Head -->
            <circle cx="26" cy="34" r="10" fill="#f5cba0"/>
            <circle cx="22" cy="33" r="1.5" fill="#333"/>
            <circle cx="30" cy="33" r="1.5" fill="#333"/>
            <!-- Focused expression -->
            <path d="M23 30 Q25 29 27 30" stroke="#555" stroke-width="1.2" fill="none" stroke-linecap="round"/>
            <path d="M22 37 Q26 40 30 37" stroke="#555" stroke-width="1.2" fill="none" stroke-linecap="round"/>
            <!-- Vest -->
            <rect x="14" y="44" width="24" height="26" rx="4" fill="#ff6b1a" opacity="0.9"/>
            <rect x="16" y="46" width="20" height="22" rx="3" fill="#e85d10"/>
            <rect x="14" y="55" width="24" height="3" rx="1" fill="#e8b94a" opacity="0.9"/>
            <!-- Left arm down -->
            <rect x="5" y="46" width="9" height="18" rx="4" fill="#f5cba0"/>
            <!-- Right arm extended holding magnifier -->
            <rect x="37" y="42" width="9" height="20" rx="4" fill="#f5cba0" transform="rotate(-15 41 42)"/>
            <!-- Legs -->
            <rect x="16" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <rect x="27" y="68" width="9" height="28" rx="4" fill="#2d4066"/>
            <rect x="14" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <rect x="26" y="92" width="12" height="10" rx="3" fill="#1a1a1a"/>
            <!-- Magnifying glass -->
            <circle cx="48" cy="28" r="10" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="2.5"/>
            <circle cx="48" cy="28" r="10" fill="rgba(147,197,253,0.15)"/>
            <!-- Glass shine -->
            <path d="M43 23 Q45 21 48 22" stroke="white" stroke-width="1.2" fill="none" stroke-linecap="round" opacity="0.6"/>
            <!-- Handle -->
            <line x1="55" y1="35" x2="60" y2="42" stroke="rgba(255,255,255,0.4)" stroke-width="3" stroke-linecap="round"/>
            <!-- Glare dot in glass -->
            <circle cx="45" cy="25" r="1.5" fill="white" opacity="0.3"/>
        </svg>

        <div class="ground"></div>
    </div>

    <!-- Text -->
    <h1 class="error-title">Página no encontrada</h1>
    <p class="error-subtitle">
        Nuestros obreros buscaron por todas partes pero la página
        <strong>no existe o fue movida</strong>.
        Verifica la URL o regresa al inicio.
    </p>

    <!-- URL hint -->
    <div class="error-hint">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        404 · La ruta solicitada no existe
    </div>

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

<script>
    // Generate random starfield
    (function () {
        const container = document.getElementById('stars');
        for (let i = 0; i < 55; i++) {
            const s = document.createElement('div');
            s.className = 'star';
            const size = Math.random() * 2 + 1;
            s.style.cssText = [
                `width:${size}px`, `height:${size}px`,
                `top:${Math.random() * 100}%`,
                `left:${Math.random() * 100}%`,
                `--d:${(Math.random() * 3 + 1.5).toFixed(1)}s`,
                `--o1:${(Math.random() * 0.2 + 0.05).toFixed(2)}`,
                `--o2:${(Math.random() * 0.5 + 0.3).toFixed(2)}`,
                `animation-delay:${(Math.random() * 3).toFixed(1)}s`,
            ].join(';');
            container.appendChild(s);
        }
    })();
</script>

</body>
</html>
