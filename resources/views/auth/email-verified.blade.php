<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="text-center">
                @if(isset($error))
                    <div class="mb-4">
                        <i class="fas fa-exclamation-circle text-red-500" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-red-600 mb-4">
                        Error en la Verificación
                    </h2>
                    <p class="text-gray-600 mb-6">
                        {{ $error }}
                    </p>
                @else
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-green-500" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-green-600 mb-4">
                        ¡Correo Verificado!
                    </h2>
                    <p class="text-gray-600 mb-6">
                        {{ $message ?? 'Tu dirección de correo electrónico ha sido verificada y actualizada exitosamente.' }}
                    </p>
                @endif
                <div class="mt-8">
                    <p class="text-sm text-gray-500">
                        Serás redirigido automáticamente en unos momentos...
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Redirigir al dashboard después de 3 segundos
    setTimeout(function() {
        window.location.href = "{{ route('dashboard') }}";
    }, 3000);
</script>

</body>
</html>