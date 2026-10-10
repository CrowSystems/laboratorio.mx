<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Valida el formulario de acceso e intenta autenticar.
 *
 * Además del formato de los campos, aquí se limita el número de intentos:
 * cinco fallos desde el mismo equipo y la cuenta se bloquea un minuto. Sin eso,
 * cualquiera podría probar contraseñas a ciegas toda la noche.
 */
class IniciarSesionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'correo' => ['required', 'email:rfc', 'max:160'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'correo.required' => 'Escribe tu correo.',
            'correo.email' => 'Ese correo no tiene un formato válido.',
            'password.required' => 'Escribe tu contraseña.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'correo' => 'correo',
            'password' => 'contraseña',
        ];
    }

    /**
     * Intenta iniciar la sesión con los datos ya validados.
     *
     * Nota: la clave del arreglo es 'password' y no 'contrasena'. Laravel trata
     * esa clave como caso especial (la compara contra el hash en lugar de
     * buscarla en una columna), y la columna real la resuelve el modelo Usuario
     * con getAuthPassword().
     *
     * @throws ValidationException
     */
    public function autenticar(): void
    {
        $this->verificarIntentos();

        $credenciales = [
            'correo' => $this->string('correo')->lower()->toString(),
            'password' => $this->string('password')->toString(),
        ];

        if (! Auth::attempt($credenciales, $this->boolean('recordarme'))) {
            RateLimiter::hit($this->llaveDeIntentos());

            throw ValidationException::withMessages([
                'correo' => 'Esas credenciales no coinciden con ningún usuario del laboratorio.',
            ]);
        }

        RateLimiter::clear($this->llaveDeIntentos());

        $this->session()->regenerate();
    }

    /**
     * @throws ValidationException
     */
    protected function verificarIntentos(): void
    {
        if (! RateLimiter::tooManyAttempts($this->llaveDeIntentos(), maxAttempts: 5)) {
            return;
        }

        $segundos = RateLimiter::availableIn($this->llaveDeIntentos());

        throw ValidationException::withMessages([
            'correo' => "Demasiados intentos. Vuelve a probar en {$segundos} segundos.",
        ]);
    }

    protected function llaveDeIntentos(): string
    {
        return 'acceso:'.mb_strtolower($this->string('correo')->toString()).'|'.$this->ip();
    }
}
