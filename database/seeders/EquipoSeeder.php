<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EstadoEquipo;
use App\Models\Equipo;
use Illuminate\Database\Seeder;

/**
 * Catálogo del laboratorio de cómputo.
 *
 * Está escrito a mano, equipo por equipo, porque es el inventario real que se
 * proyecta en clase: números de inventario legibles, marcas reconocibles y
 * restricciones de semestre que tienen una razón de ser (el osciloscopio y el
 * analizador de red se prestan a semestres avanzados).
 */
class EquipoSeeder extends Seeder
{
    /**
     * @var list<array{numero_inventario: string, tipo: string, marca: string, modelo: string, descripcion: string, estado: EstadoEquipo, semestre_minimo: int}>
     */
    protected function catalogo(): array
    {
        return [
            // --- Cómputo de uso general ---
            ['numero_inventario' => 'LAB-1001', 'tipo' => 'Laptop', 'marca' => 'Dell', 'modelo' => 'Latitude 5450', 'descripcion' => 'Core i5, 16 GB RAM, SSD 512 GB. Incluye cargador y funda.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 1],
            ['numero_inventario' => 'LAB-1002', 'tipo' => 'Laptop', 'marca' => 'Dell', 'modelo' => 'Latitude 5450', 'descripcion' => 'Core i5, 16 GB RAM, SSD 512 GB. Incluye cargador y funda.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 1],
            ['numero_inventario' => 'LAB-1003', 'tipo' => 'Laptop', 'marca' => 'Lenovo', 'modelo' => 'ThinkPad E14', 'descripcion' => 'Ryzen 5, 8 GB RAM. Teclado en español.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 1],
            ['numero_inventario' => 'LAB-1004', 'tipo' => 'Laptop', 'marca' => 'Lenovo', 'modelo' => 'ThinkPad E14', 'descripcion' => 'Ryzen 5, 8 GB RAM. La batería dura poco, se presta con cargador.', 'estado' => EstadoEquipo::EnReparacion, 'semestre_minimo' => 1],
            ['numero_inventario' => 'LAB-1005', 'tipo' => 'Laptop', 'marca' => 'HP', 'modelo' => 'ProBook 450 G10', 'descripcion' => 'Core i7, 16 GB RAM. Reservada para proyectos de titulación.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 7],

            // --- Audiovisual ---
            ['numero_inventario' => 'LAB-2001', 'tipo' => 'Proyector', 'marca' => 'Epson', 'modelo' => 'PowerLite E20', 'descripcion' => '3400 lúmenes. Incluye cable HDMI de 3 m y control.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 1],
            ['numero_inventario' => 'LAB-2002', 'tipo' => 'Proyector', 'marca' => 'BenQ', 'modelo' => 'MS550', 'descripcion' => '3600 lúmenes. Se usa en el auditorio.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 1],
            ['numero_inventario' => 'LAB-2003', 'tipo' => 'Cámara web', 'marca' => 'Logitech', 'modelo' => 'C920 HD Pro', 'descripcion' => '1080p con micrófono integrado. Para clases en línea.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 1],
            ['numero_inventario' => 'LAB-2004', 'tipo' => 'Audífonos', 'marca' => 'HyperX', 'modelo' => 'Cloud Stinger 2', 'descripcion' => 'Diadema con micrófono. Para el laboratorio de idiomas.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 1],
            ['numero_inventario' => 'LAB-2005', 'tipo' => 'Micrófono', 'marca' => 'Blue', 'modelo' => 'Yeti Nano', 'descripcion' => 'Micrófono USB de condensador. Incluye base.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 3],

            // --- Electrónica y prototipos ---
            ['numero_inventario' => 'LAB-3001', 'tipo' => 'Kit de desarrollo', 'marca' => 'Arduino', 'modelo' => 'Starter Kit UNO R3', 'descripcion' => 'Placa UNO, protoboard, sensores y cables. Revisar piezas al devolver.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 2],
            ['numero_inventario' => 'LAB-3002', 'tipo' => 'Kit de desarrollo', 'marca' => 'Arduino', 'modelo' => 'Starter Kit UNO R3', 'descripcion' => 'Placa UNO, protoboard, sensores y cables.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 2],
            ['numero_inventario' => 'LAB-3003', 'tipo' => 'Kit de desarrollo', 'marca' => 'Espressif', 'modelo' => 'ESP32 DevKit V1', 'descripcion' => 'Microcontrolador con WiFi y Bluetooth. Para la materia de Redes Inalámbricas.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 3],
            ['numero_inventario' => 'LAB-3004', 'tipo' => 'Kit de desarrollo', 'marca' => 'Raspberry Pi', 'modelo' => 'Pi 4 Model B 4 GB', 'descripcion' => 'Incluye microSD de 32 GB con Raspberry Pi OS y fuente USB-C.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 4],
            ['numero_inventario' => 'LAB-3005', 'tipo' => 'Multímetro', 'marca' => 'Steren', 'modelo' => 'HER-410', 'descripcion' => 'Multímetro digital con puntas. Verificar fusible.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 2],
            ['numero_inventario' => 'LAB-3006', 'tipo' => 'Multímetro', 'marca' => 'Truper', 'modelo' => 'MUT-105', 'descripcion' => 'Multímetro digital de gancho.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 2],
            ['numero_inventario' => 'LAB-3007', 'tipo' => 'Osciloscopio', 'marca' => 'Rigol', 'modelo' => 'DS1054Z', 'descripcion' => '4 canales, 50 MHz. Equipo delicado: solo semestres avanzados.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 6],
            ['numero_inventario' => 'LAB-3008', 'tipo' => 'Cautín', 'marca' => 'Hakko', 'modelo' => 'FX-888D', 'descripcion' => 'Estación de soldadura con control de temperatura.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 4],
            ['numero_inventario' => 'LAB-3009', 'tipo' => 'Impresora 3D', 'marca' => 'Creality', 'modelo' => 'Ender-3 V3 SE', 'descripcion' => 'Se presta solo para proyectos registrados. No sale del edificio.', 'estado' => EstadoEquipo::Baja, 'semestre_minimo' => 5],

            // --- Redes ---
            ['numero_inventario' => 'LAB-4001', 'tipo' => 'Switch administrable', 'marca' => 'Cisco', 'modelo' => 'Catalyst 1000-8T', 'descripcion' => '8 puertos Gigabit. Para prácticas de VLAN.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 5],
            ['numero_inventario' => 'LAB-4002', 'tipo' => 'Switch administrable', 'marca' => 'TP-Link', 'modelo' => 'TL-SG108E', 'descripcion' => '8 puertos Gigabit con VLAN por etiqueta.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 5],
            ['numero_inventario' => 'LAB-4003', 'tipo' => 'Router', 'marca' => 'Ubiquiti', 'modelo' => 'EdgeRouter X', 'descripcion' => 'Router de 5 puertos para prácticas de enrutamiento.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 5],
            ['numero_inventario' => 'LAB-4004', 'tipo' => 'Punto de acceso', 'marca' => 'Ubiquiti', 'modelo' => 'UniFi U6 Lite', 'descripcion' => 'Punto de acceso WiFi 6. Requiere inyector PoE (incluido).', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 6],
            ['numero_inventario' => 'LAB-4005', 'tipo' => 'Analizador de red', 'marca' => 'Fluke', 'modelo' => 'LinkIQ', 'descripcion' => 'Certificador de cableado. Equipo de alto costo: autorización del encargado.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 7],
            ['numero_inventario' => 'LAB-4006', 'tipo' => 'Ponchadora', 'marca' => 'Steren', 'modelo' => 'HER-401', 'descripcion' => 'Pinza para conectores RJ45. Incluye probador de cable.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 3],

            // --- Periféricos y almacenamiento ---
            ['numero_inventario' => 'LAB-5001', 'tipo' => 'Tableta gráfica', 'marca' => 'Wacom', 'modelo' => 'Intuos S', 'descripcion' => 'Tableta con lápiz. Para diseño y mercadotecnia.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 3],
            ['numero_inventario' => 'LAB-5002', 'tipo' => 'Disco externo', 'marca' => 'Seagate', 'modelo' => 'Expansion 2 TB', 'descripcion' => 'Disco USB 3.0. Se entrega formateado.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 1],
            ['numero_inventario' => 'LAB-5003', 'tipo' => 'Disco externo', 'marca' => 'Western Digital', 'modelo' => 'Elements 1 TB', 'descripcion' => 'Disco USB 3.0 para respaldos de proyecto.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 1],
            ['numero_inventario' => 'LAB-5004', 'tipo' => 'Lector de código de barras', 'marca' => 'Honeywell', 'modelo' => 'Voyager 1250g', 'descripcion' => 'Lector láser USB. Para prácticas de punto de venta.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 4],
            ['numero_inventario' => 'LAB-5005', 'tipo' => 'Monitor portátil', 'marca' => 'ASUS', 'modelo' => 'ZenScreen MB16ACE', 'descripcion' => 'Pantalla USB-C de 15.6". Incluye funda y cable.', 'estado' => EstadoEquipo::Disponible, 'semestre_minimo' => 3],
        ];
    }

    public function run(): void
    {
        foreach ($this->catalogo() as $equipo) {
            Equipo::updateOrCreate(
                ['numero_inventario' => $equipo['numero_inventario']],
                $equipo,
            );
        }
    }
}
