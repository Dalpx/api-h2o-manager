<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Filters\V1\UserQuery;
use App\Http\Requests\V1\StoreUsuarioRequest;
use App\Http\Requests\V1\UpdateUserRequest;
use App\Models\User;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\UserCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new UserQuery();
        $filterItems = $filter->Transform($request);

        $users = User::join('sucursal', 'usuario.sucursal_id', '=', 'sucursal.id')
            ->select('usuario.*');

        if ($request->filled('sucursal')) {
            $valor = $request->query('sucursal');
            $nombre = is_array($valor) ? ($valor['eq'] ?? null) : $valor;

            if ($nombre) {
                $users->where('sucursal.nombre', 'like', "%{$nombre}%");
            }
        }
        
        if ($request->query('incluirElim')) {
            $users->withTrashed();
        }

        foreach ($filterItems as $item) {
            $users->where("usuario." . $item[0], $item[1], $item[2]);
        }

        if ($request->query('incluirDetalle')) {
            $users->with('detalles');
        }

        return new UserCollection($users->paginate()->appends($request->query()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUsuarioRequest $request)
    {   
        $datos = $request->validated();
        return new UserResource(User::create($datos));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $usuario)
    {
        return new UserResource($usuario);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $usuario)
    {
        $usuario->update($request->validated());

        return response()->json(['message'=> 'Usuario actualizado exitosamente'],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UpdateUserRequest $request, User $usuario)
    {
        $usuario->delete();

        return response()->json(['message'=> 'Usuario eliminado con éxito'],200);
        
    }
}
