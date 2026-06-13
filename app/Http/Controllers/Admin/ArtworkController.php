<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Services\ArtworkImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ArtworkController extends Controller
{
    /*
     * Inyección por constructor: el controller delega
     * cualquier I/O de filesystem al servicio. Si mañana
     * cambiamos a S3, solo tocamos el servicio.
     */
    public function __construct(private readonly ArtworkImageStorage $imageStorage)
    {
    }

    public function index()
    {
        return Inertia::render('Admin/Artworks/Index', [
            'artworks' => Artwork::orderByDesc('created_at')
                ->get()
                ->map(fn (Artwork $artwork) => [
                    'id' => $artwork->id,
                    'title' => $artwork->title,
                    'slug' => $artwork->slug,
                    'price' => $artwork->price,
                    'technique' => $artwork->technique,
                    'dimensions' => $artwork->dimensions,
                    'year' => $artwork->year,
                    'is_published' => $artwork->is_published,
                    'vendido_at' => optional($artwork->vendido_at)?->toIso8601String(),
                    'image_url' => $artwork->image_url,
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Artworks/Form', [
            'artwork' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateArtwork($request);
        $data = $this->resolveImage($request, $data);

        Artwork::create($data);

        return redirect()
            ->route('admin.artworks.index')
            ->with('success', 'Obra creada correctamente.');
    }

    public function edit(Artwork $artwork)
    {
        return Inertia::render('Admin/Artworks/Form', [
            'artwork' => [
                'id' => $artwork->id,
                'title' => $artwork->title,
                'slug' => $artwork->slug,
                'description' => $artwork->description,
                'price' => $artwork->price,
                'image_url' => $artwork->image_url,
                'technique' => $artwork->technique,
                'dimensions' => $artwork->dimensions,
                'year' => $artwork->year,
                'is_published' => $artwork->is_published,
            ],
        ]);
    }

    public function update(Request $request, Artwork $artwork)
    {
        $data = $this->validateArtwork($request, $artwork);
        $data = $this->resolveImage($request, $data, $artwork);

        $artwork->update($data);

        return redirect()
            ->route('admin.artworks.index')
            ->with('success', 'Obra actualizada correctamente.');
    }

    public function destroy(Artwork $artwork)
    {
        // Capturamos la URL antes de borrar para poder limpiar el disco.
        $imageUrl = $artwork->image_url;

        $artwork->delete();

        $this->imageStorage->delete($imageUrl);

        return redirect()
            ->route('admin.artworks.index')
            ->with('success', 'Obra eliminada.');
    }

    /**
     * Si llega un archivo de imagen, lo guarda y reemplaza
     * la URL en el array de datos validados. Al actualizar,
     * además borra la imagen anterior si era local.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function resolveImage(Request $request, array $data, ?Artwork $artwork = null): array
    {
        // image_file no es columna del modelo: nunca debe llegar al persistirse.
        unset($data['image_file']);

        if (! $request->hasFile('image_file')) {
            return $data;
        }

        $previousImage = $artwork?->image_url;
        $data['image_url'] = $this->imageStorage->store($request->file('image_file'));

        if ($previousImage !== null && $previousImage !== $data['image_url']) {
            $this->imageStorage->delete($previousImage);
        }

        return $data;
    }

    /**
     * Reglas:
     *  - image_file: archivo opcional, imagen real, máximo 5 MB.
     *  - image_url: requerido si NO se sube archivo, opcional si sí.
     *    Esto cubre los tres casos: subida nueva, URL externa,
     *    o edición sin tocar la imagen (la URL ya viene del form).
     *
     * @return array<string, mixed>
     */
    protected function validateArtwork(Request $request, ?Artwork $artwork = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('artworks', 'slug')->ignore($artwork?->id),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_url' => ['required_without:image_file', 'nullable', 'string', 'max:2048'],
            'technique' => ['nullable', 'string', 'max:255'],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'is_published' => ['required', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['title']);

        return $validated;
    }
}
