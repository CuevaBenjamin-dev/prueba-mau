<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportarPersonajeRequest;
use App\Models\Personaje;
use App\Services\SwapiService;
use DomainException;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class ImportacionSwapiController extends Controller
{
    public function store(ImportarPersonajeRequest $request, SwapiService $swapi): RedirectResponse
    {
        $id = (int) $request->validated('id');
        if (Personaje::where('swapi_id', $id)->exists()) {
            throw ValidationException::withMessages(['id' => 'Este personaje de SWAPI ya fue importado.']);
        }

        try {
            $datos = $swapi->obtenerPersonaje($id);
            Personaje::create([...$datos, 'origen_swapi' => true, 'swapi_id' => $id]);
        } catch (DomainException $exception) {
            throw ValidationException::withMessages(['id' => $exception->getMessage()]);
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062 && Personaje::where('swapi_id', $id)->exists()) {
                throw ValidationException::withMessages(['id' => 'Este personaje de SWAPI ya fue importado.']);
            }
            throw $exception;
        }

        return to_route('personajes.index')->with('success', 'Personaje importado correctamente.');
    }
}
