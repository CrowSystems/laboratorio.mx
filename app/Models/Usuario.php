<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RolUsuario;
use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Encargado del laboratorio (admin) o auxiliar de mostrador.
 *
 * Sustituye al modelo User que Laravel trae de fábrica. Al cambiar el nombre
 * de la tabla y de las columnas al español hay que avisarle dos cosas a Laravel:
 *   - #[Table(name: 'usuarios')] indica la tabla real.
 *   - getAuthPassword() indica en qué columna está la contraseña.
 * Lo demás (sesión, recordarme, hash) sigue funcionando igual.
 *
 * @property-read int $id
 * @property string $nombre
 * @property string $correo
 * @property string $contrasena
 * @property RolUsuario $rol
 */
#[Table(name: 'usuarios')]
#[Fillable(['nombre', 'correo', 'contrasena', 'rol'])]
#[Hidden(['contrasena', 'remember_token'])]
class Usuario extends Authenticatable
{
    /** @use HasFactory<UsuarioFactory> */
    use HasFactory;

    /**
     * Conversiones automáticas de tipo.
     *
     * - `rol` deja de ser una cadena y se convierte en el enum RolUsuario.
     * - `contrasena` se cifra con bcrypt al asignarla, así que nunca guardamos
     *   una contraseña en texto plano aunque se nos olvide llamar a Hash::make().
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rol' => RolUsuario::class,
            'contrasena' => 'hashed',
        ];
    }

    /**
     * Columna donde Laravel debe buscar la contraseña al autenticar.
     */
    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

    /**
     * Préstamos que este usuario autorizó.
     *
     * @return HasMany<Prestamo, $this>
     */
    public function prestamosAutorizados(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'autorizado_por');
    }

    /**
     * ¿Es el encargado del laboratorio?
     *
     * Se usa en las Policies. Ojo: preguntar el rol aquí está bien, pero la
     * decisión de permitir o negar una acción vive en app/Policies.
     */
    public function esAdmin(): bool
    {
        return $this->rol === RolUsuario::Admin;
    }
}
