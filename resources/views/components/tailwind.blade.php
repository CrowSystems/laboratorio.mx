{{--
    Carga de Tailwind CSS y tema del proyecto.

    Este componente se incluye en el <head> de los dos layouts para no repetir
    la configuración en dos archivos. Si mañana hay que cambiar un color de la
    marca, se cambia aquí y cambia en todo el sistema.

    Nota importante para el alumno: el proyecto usa Tailwind desde un CDN, es
    decir, el navegador descarga la hoja de estilos de internet. Es la forma
    más rápida de arrancar y no necesita npm ni Node.js instalados. En un
    sistema de verdad, en producción, se compila Tailwind con Vite para que el
    archivo CSS solo incluya las clases que de verdad se usan y el sitio
    funcione sin internet.
--}}
<script src="https://cdn.tailwindcss.com/3.4.17"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Figtree', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                },
                colors: {
                    // Azul tinta del CESBA: la identidad visual del sistema.
                    tinta: {
                        50: '#f4f6fb',
                        100: '#e6ebf7',
                        200: '#c9d4ec',
                        300: '#9cb0db',
                        400: '#6885c5',
                        500: '#4664ae',
                        600: '#354e92',
                        700: '#2c3f76',
                        800: '#283662',
                        900: '#252f52',
                        950: '#161c33',
                    },
                },
                boxShadow: {
                    tarjeta: '0 1px 2px rgba(22,28,51,.06), 0 10px 30px -18px rgba(22,28,51,.35)',
                },
            },
        },
    };
</script>
<style type="text/tailwindcss">
    /*
       Dos utilidades propias, para no repetir la misma lista de clases
       en cada campo de cada formulario.
    */
    @layer components {
        .campo-base {
            @apply w-full rounded-xl border-0 bg-white px-3.5 py-2.5 text-[15px] text-tinta-950
                   shadow-sm ring-1 ring-inset ring-tinta-200 transition
                   placeholder:text-tinta-300
                   focus:ring-2 focus:ring-inset focus:ring-tinta-500 focus:outline-none;
        }

        .campo-error {
            @apply ring-rose-400 focus:ring-rose-500;
        }
    }

    /* Rejilla sutil de fondo, hecha con degradados: cero archivos de imagen. */
    .fondo-rejilla {
        background-image:
            linear-gradient(to right, rgba(22, 28, 51, .045) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(22, 28, 51, .045) 1px, transparent 1px);
        background-size: 34px 34px;
    }
</style>
