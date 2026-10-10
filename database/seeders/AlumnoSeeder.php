<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Alumno;
use Illuminate\Database\Seeder;

/**
 * Padrón de alumnos.
 *
 * Los primeros doce están escritos a mano porque las pruebas manuales y la demo
 * en clase necesitan matrículas conocidas y semestres concretos (para enseñar
 * la regla del semestre mínimo). El resto se genera con la factory para que el
 * laboratorio se vea con volumen real y la paginación tenga sentido.
 */
class AlumnoSeeder extends Seeder
{
    protected const TOTAL_ALUMNOS = 300;

    /**
     * @var list<array{matricula: string, nombre: string, carrera: string, semestre: int, correo: string}>
     */
    protected const ALUMNOS = [
        ['matricula' => 'SC202401', 'nombre' => 'Diana Laura Moreno Vázquez', 'carrera' => 'Licenciatura en Sistemas Computacionales', 'semestre' => 8, 'correo' => 'dmoreno@cesba.edu.mx'],
        ['matricula' => 'SC202402', 'nombre' => 'Emiliano Zúñiga Robles', 'carrera' => 'Licenciatura en Sistemas Computacionales', 'semestre' => 8, 'correo' => 'ezuniga@cesba.edu.mx'],
        ['matricula' => 'SC202403', 'nombre' => 'Fernanda Guadalupe Ríos Cazares', 'carrera' => 'Licenciatura en Sistemas Computacionales', 'semestre' => 6, 'correo' => 'frios@cesba.edu.mx'],
        ['matricula' => 'SC202404', 'nombre' => 'Rodrigo Alcántara Peña', 'carrera' => 'Licenciatura en Sistemas Computacionales', 'semestre' => 6, 'correo' => 'ralcantara@cesba.edu.mx'],
        ['matricula' => 'SC202405', 'nombre' => 'Itzel Marisol Padilla Gómez', 'carrera' => 'Licenciatura en Sistemas Computacionales', 'semestre' => 4, 'correo' => 'ipadilla@cesba.edu.mx'],
        ['matricula' => 'SC202406', 'nombre' => 'Ángel Eduardo Trejo Lara', 'carrera' => 'Licenciatura en Sistemas Computacionales', 'semestre' => 2, 'correo' => 'atrejo@cesba.edu.mx'],
        ['matricula' => 'SC202407', 'nombre' => 'Valeria Monserrat Ocampo Ruiz', 'carrera' => 'Licenciatura en Sistemas Computacionales', 'semestre' => 1, 'correo' => 'vocampo@cesba.edu.mx'],
        ['matricula' => 'AD202401', 'nombre' => 'Jesús Antonio Barrera Nieto', 'carrera' => 'Licenciatura en Administración', 'semestre' => 5, 'correo' => 'jbarrera@cesba.edu.mx'],
        ['matricula' => 'AD202402', 'nombre' => 'Paulina Cervantes Aguilar', 'carrera' => 'Licenciatura en Administración', 'semestre' => 3, 'correo' => 'pcervantes@cesba.edu.mx'],
        ['matricula' => 'CO202401', 'nombre' => 'Ricardo Salinas Jiménez', 'carrera' => 'Licenciatura en Contaduría', 'semestre' => 7, 'correo' => 'rsalinas@cesba.edu.mx'],
        ['matricula' => 'MK202401', 'nombre' => 'Karla Michelle Fonseca Durán', 'carrera' => 'Licenciatura en Mercadotecnia', 'semestre' => 4, 'correo' => 'kfonseca@cesba.edu.mx'],
        ['matricula' => 'DE202401', 'nombre' => 'Octavio Galván Mendoza', 'carrera' => 'Licenciatura en Derecho', 'semestre' => 2, 'correo' => 'ogalvan@cesba.edu.mx'],
    ];

    public function run(): void
    {
        foreach (self::ALUMNOS as $alumno) {
            Alumno::updateOrCreate(['matricula' => $alumno['matricula']], $alumno);
        }

        // El laboratorio atiende a unos 300 alumnos: se completa el padrón para
        // que los listados y la paginación se vean como en la vida real.
        // Solo se crean los que faltan, para que el seeder se pueda correr
        // varias veces sin duplicar alumnos ni chocar con los ya existentes.
        $faltan = self::TOTAL_ALUMNOS - Alumno::count();

        if ($faltan > 0) {
            Alumno::factory()->count($faltan)->create();
        }
    }
}
