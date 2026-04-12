{{-- DEPRECATED: zero views extend this layout. Use layouts.main. Do not reference. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Gestor Proyectos UNICLARETIANA') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        /* Font Awesome fallback icons */
        .fa { display: inline-block; width: 1em; height: 1em; }
        .fa-save:before { content: "💾"; }
        .fa-spinner:before { content: "⌛"; }
        .fa-times:before { content: "❌"; }
        .fa-check:before { content: "✔"; }
    </style>
    @yield('styles')
</head>
<body>
    <div id="app">
        <main>
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // jQuery fallback
        window.$ = window.jQuery = function(selector) {
            return {
                ready: function(fn) { 
                    if (document.readyState !== 'loading') {
                        fn();
                    } else {
                        document.addEventListener('DOMContentLoaded', fn);
                    }
                },
                on: function(event, handler) {
                    document.querySelector(selector).addEventListener(event, handler);
                    return this;
                },
                prop: function(prop, value) {
                    document.querySelector(selector)[prop] = value;
                    return this;
                },
                html: function(content) {
                    document.querySelector(selector).innerHTML = content;
                    return this;
                }
            };
        };
    </script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    @stack('scripts')
</body>
</html>