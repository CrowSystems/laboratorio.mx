{{-- No encontrado: la URL no existe o el registro ya no está. --}}
@include('errors._plantilla', [
    'codigo' => 404,
    'titulo' => 'No encontramos esta página',
    'mensaje' => 'La dirección no existe, o el equipo, alumno o préstamo que buscabas ya no está en el sistema.',
])
