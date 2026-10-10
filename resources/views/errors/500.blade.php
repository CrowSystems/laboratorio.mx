{{-- Error del servidor. Se muestra sin detalles técnicos al usuario final. --}}
@include('errors._plantilla', [
    'codigo' => 500,
    'titulo' => 'Algo falló en el servidor',
    'mensaje' => 'El sistema no pudo completar la operación. Inténtalo de nuevo; si sigue igual, avisa al encargado de sistemas.',
])
