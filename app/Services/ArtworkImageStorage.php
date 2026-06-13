<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Almacena imágenes de obra en el disco público local.
 *
 * Vive en la capa de infraestructura: aísla todo el contacto
 * con el filesystem para que el controller (capa HTTP) no
 * conozca paths físicos ni nombres de archivo.
 *
 * El controller recibe la URL pública resultante y la guarda
 * en `image_url`; el dominio sigue tratando ese campo como un
 * simple string opaco.
 */
class ArtworkImageStorage
{
    private const DIRECTORY = 'artworks';
    private const DISK = 'public';

    /**
     * Persiste un UploadedFile y devuelve una URL pública relativa
     * (`/storage/artworks/<uuid>.<ext>`).
     *
     * Devolvemos relativa a propósito: las URLs absolutas dependen
     * de APP_URL y se rompen al cambiar de host/puerto entre dev,
     * staging y producción. La relativa la resuelve el navegador
     * contra el host actual y funciona en cualquier entorno
     * siempre que exista el symlink `public/storage`.
     */
    public function store(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: ($file->guessExtension() ?? 'bin');
        $name = Str::uuid()->toString().'.'.$extension;

        $path = $file->storeAs(self::DIRECTORY, $name, self::DISK);

        return '/storage/'.$path;
    }

    /**
     * Borra la imagen referenciada por una URL si pertenece
     * a este disco. Las URLs externas (https://...) o vacías
     * se ignoran silenciosamente — no son nuestras.
     */
    public function delete(?string $imageUrl): void
    {
        $path = $this->resolveLocalPath($imageUrl);

        if ($path === null) {
            return;
        }

        Storage::disk(self::DISK)->delete($path);
    }

    /**
     * Convierte una URL pública del tipo `/storage/artworks/x.png`
     * en su path relativo dentro del disk (`artworks/x.png`).
     * Devuelve null si la URL no apunta a este disk.
     */
    private function resolveLocalPath(?string $imageUrl): ?string
    {
        if ($imageUrl === null || $imageUrl === '') {
            return null;
        }

        $parsedPath = parse_url($imageUrl, PHP_URL_PATH);
        if (! is_string($parsedPath)) {
            return null;
        }

        $prefix = '/storage/'.self::DIRECTORY.'/';
        if (! str_starts_with($parsedPath, $prefix)) {
            return null;
        }

        return self::DIRECTORY.'/'.substr($parsedPath, strlen($prefix));
    }
}
