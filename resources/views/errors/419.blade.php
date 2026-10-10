{{--
    419: el token CSRF caducó. Pasa cuando el formulario llevaba mucho rato
    abierto y la sesión ya expiró. No es un error del usuario.
--}}
@include('errors._plantilla', [
    'codigo' => 419,
    'titulo' => 'La página estuvo demasiado tiempo abierta',
    'mensaje' => 'Por seguridad, el formulario caducó. Vuelve a abrirlo y captura los datos otra vez.',
])
