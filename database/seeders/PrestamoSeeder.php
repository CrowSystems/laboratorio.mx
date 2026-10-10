<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CondicionDevolucion;
use App\Enums\EstadoEquipo;
use App\Models\Alumno;
use App\Models\Equipo;
use App\Models\Prestamo;
use App\Models\Usuario;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Historial de préstamos para que el sistema se vea con vida al abrirlo.
 *
 * Se siembra en tres tandas y en orden cronológico, porque el estado final de
 * cada equipo depende del último movimiento que tuvo:
 *   1. Préstamos ya devueltos (los últimos seis meses). Alimentan el ranking
 *      de "equipos más solicitados" del tablero.
 *   2. Préstamos abiertos y al corriente. Dejan el equipo en "prestado".
 *   3. Préstamos VENCIDOS. Son los que hacen visible el problema que el
 *      laboratorio quería resolver, y el tablero los muestra en rojo.
 *
 * Las tandas 2 y 3 usan alumnos distintos a propósito: la regla "un alumno con
 * adeudo vencido no puede llevarse nada más" debe cumplirse también en los
 * datos sembrados, o el sistema arrancaría contradiciéndose.
 */
class PrestamoSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = Usuario::query()->get();

        if ($usuarios->isEmpty()) {
            return;
        }

        // Los equipos que más se piden en un laboratorio real: las laptops y
        // los proyectores. Se les carga el historial para que el ranking del
        // tablero no sea un empate de todos contra todos.
        $masPedidos = Equipo::query()
            ->whereIn('numero_inventario', ['LAB-1001', 'LAB-1002', 'LAB-1003', 'LAB-2001', 'LAB-3001'])
            ->get();

        $resto = Equipo::query()
            ->whereNotIn('numero_inventario', ['LAB-3009'])
            ->whereNotIn('id', $masPedidos->pluck('id'))
            ->get();

        $alumnos = Alumno::query()->inRandomOrder()->limit(120)->get();

        $this->sembrarDevueltos($masPedidos, $resto, $alumnos, $usuarios);
        $this->sembrarAbiertos($usuarios);
        $this->sembrarVencidos($usuarios);
    }

    /**
     * Tanda 1: historial cerrado de los últimos seis meses.
     *
     * @param  Collection<int, Equipo>  $masPedidos
     * @param  Collection<int, Equipo>  $resto
     * @param  Collection<int, Alumno>  $alumnos
     * @param  Collection<int, Usuario>  $usuarios
     */
    protected function sembrarDevueltos(
        Collection $masPedidos,
        Collection $resto,
        Collection $alumnos,
        Collection $usuarios,
    ): void {
        // Cada equipo estrella sale un número distinto de veces, para que el
        // ranking del tablero muestre barras de distinta altura y no un empate.
        $vecesPorEquipo = [14, 11, 9, 7, 5];

        $cola = $masPedidos
            ->values()
            ->flatMap(fn (Equipo $equipo, int $i) => array_fill(0, $vecesPorEquipo[$i] ?? 4, $equipo))
            ->concat($resto->flatMap(fn (Equipo $equipo) => array_fill(0, random_int(1, 3), $equipo)))
            ->shuffle();

        $dia = 180;

        foreach ($cola as $equipo) {
            $dia = max(7, $dia - random_int(1, 3));

            $salida = CarbonImmutable::now()->subDays($dia)->setTime(random_int(8, 17), random_int(0, 59));
            $compromiso = $salida->addWeekdays(random_int(1, 5));
            // Nueve de cada diez se regresan a tiempo o con un día de retraso.
            $devolucion = $compromiso
                ->subDays(random_int(0, 1))
                ->setTime(random_int(8, 18), random_int(0, 59));

            Prestamo::create([
                'equipo_id' => $equipo->id,
                'alumno_id' => $alumnos->random()->id,
                'autorizado_por' => $usuarios->random()->id,
                'fecha_salida' => $salida,
                'fecha_compromiso' => $compromiso->toDateString(),
                'fecha_devolucion' => $devolucion,
                'condicion_devolucion' => CondicionDevolucion::Bien,
                'notas' => null,
            ]);
        }

        // Un caso con daño, el que explica por qué LAB-1004 está en reparación.
        $danado = Equipo::query()->where('numero_inventario', 'LAB-1004')->first();

        if ($danado instanceof Equipo) {
            Prestamo::create([
                'equipo_id' => $danado->id,
                'alumno_id' => $alumnos->random()->id,
                'autorizado_por' => $usuarios->random()->id,
                'fecha_salida' => CarbonImmutable::now()->subDays(14)->setTime(10, 15),
                'fecha_compromiso' => CarbonImmutable::now()->subDays(9)->toDateString(),
                'fecha_devolucion' => CarbonImmutable::now()->subDays(9)->setTime(16, 40),
                'condicion_devolucion' => CondicionDevolucion::ConDano,
                'notas' => 'Al devolver: la bisagra derecha de la pantalla quedó floja y el cargador trae el cable pelado.',
            ]);

            $danado->update(['estado' => EstadoEquipo::EnReparacion]);
        }
    }

    /**
     * Tanda 2: equipo que está fuera hoy, dentro del plazo.
     *
     * @param  Collection<int, Usuario>  $usuarios
     */
    protected function sembrarAbiertos(Collection $usuarios): void
    {
        $vigentes = [
            ['inventario' => 'LAB-1001', 'matricula' => 'SC202401', 'dias' => 2, 'plazo' => 3],
            ['inventario' => 'LAB-2001', 'matricula' => 'SC202403', 'dias' => 1, 'plazo' => 4],
            ['inventario' => 'LAB-3003', 'matricula' => 'SC202405', 'dias' => 3, 'plazo' => 2],
            ['inventario' => 'LAB-5001', 'matricula' => 'MK202401', 'dias' => 4, 'plazo' => 1],
            ['inventario' => 'LAB-2003', 'matricula' => 'DE202401', 'dias' => 1, 'plazo' => 6],
        ];

        foreach ($vigentes as $caso) {
            $equipo = Equipo::query()->where('numero_inventario', $caso['inventario'])->first();
            $alumno = Alumno::query()->where('matricula', $caso['matricula'])->first();

            if (! $equipo instanceof Equipo || ! $alumno instanceof Alumno) {
                continue;
            }

            Prestamo::create([
                'equipo_id' => $equipo->id,
                'alumno_id' => $alumno->id,
                'autorizado_por' => $usuarios->random()->id,
                'fecha_salida' => CarbonImmutable::now()->subDays($caso['dias'])->setTime(random_int(8, 16), 30),
                'fecha_compromiso' => CarbonImmutable::now()->addDays($caso['plazo'])->toDateString(),
                'fecha_devolucion' => null,
                'notas' => null,
            ]);

            $equipo->update(['estado' => EstadoEquipo::Prestado]);
        }
    }

    /**
     * Tanda 3: los préstamos vencidos. Son el dato que el laboratorio quería ver.
     *
     * @param  Collection<int, Usuario>  $usuarios
     */
    protected function sembrarVencidos(Collection $usuarios): void
    {
        $casos = [
            ['inventario' => 'LAB-1002', 'matricula' => 'SC202402', 'atraso' => 11, 'notas' => 'Se llevó el cargador y la funda.'],
            ['inventario' => 'LAB-3001', 'matricula' => 'SC202404', 'atraso' => 6, 'notas' => 'Kit completo: 1 placa UNO, 1 protoboard, 12 sensores.'],
            ['inventario' => 'LAB-3007', 'matricula' => 'CO202401', 'atraso' => 3, 'notas' => 'Autorizado por el encargado para la práctica de Electrónica.'],
            ['inventario' => 'LAB-4001', 'matricula' => 'AD202401', 'atraso' => 1, 'notas' => 'Práctica de VLAN del edificio B.'],
        ];

        foreach ($casos as $caso) {
            $equipo = Equipo::query()->where('numero_inventario', $caso['inventario'])->first();
            $alumno = Alumno::query()->where('matricula', $caso['matricula'])->first();

            if (! $equipo instanceof Equipo || ! $alumno instanceof Alumno) {
                continue;
            }

            Prestamo::create([
                'equipo_id' => $equipo->id,
                'alumno_id' => $alumno->id,
                'autorizado_por' => $usuarios->random()->id,
                'fecha_salida' => CarbonImmutable::now()->subDays($caso['atraso'] + 4)->setTime(9, 20),
                'fecha_compromiso' => CarbonImmutable::now()->subDays($caso['atraso'])->toDateString(),
                'fecha_devolucion' => null,
                'notas' => $caso['notas'],
            ]);

            $equipo->update(['estado' => EstadoEquipo::Prestado]);
        }
    }
}
