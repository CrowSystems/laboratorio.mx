{{-- Acceso denegado: el rol del usuario no alcanza para esta sección. --}}
@include('errors._plantilla', [
    'codigo' => 403,
    'titulo' => 'No tienes acceso a esta sección',
    'mensaje' => $exception?->getMessage() ?: 'Tu rol no permite esta operación. Si necesitas entrar, pídeselo al encargado del laboratorio.',
])
