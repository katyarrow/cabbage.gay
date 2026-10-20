<?php

namespace App\Http\Controllers;

use App\Events\RotaUpdated;
use App\Http\Requests\RotaRequest;
use App\Http\Requests\RotaUpdateRequest;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\RotaResource;
use App\Http\Resources\RotaResourceSimple;
use App\Models\Meeting;
use App\Models\Rota;
use Illuminate\View\View;

class RotaController extends Controller
{
    public function create(): View
    {
        return view('create-rota' );
    }

    public function show(Rota $rota): View
    {
        return view('rota', [
            'rota' => $rota,
        ]);
    }

    public function store(RotaRequest $request): RotaResource
    {
        $rota = new Rota();
        $request->save($rota);

        return new RotaResource($rota);
    }

    public function update(RotaUpdateRequest $request, Rota $rota) : RotaResourceSimple {
        $request->save($rota);
        broadcast(new RotaUpdated($rota));
        return new RotaResourceSimple($rota);
    }
}
