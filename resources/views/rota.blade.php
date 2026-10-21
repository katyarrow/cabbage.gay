<x-main-layout
    :noindex="true"
    title="Encrypted Rota"
    description="Show your availability on this rota."
    feature="ROTA"
    :broadcasting="true"
>
    <show-rota
        :rota="{{ json_encode(new \App\Http\Resources\RotaResource($rota)) }}"
        site-title="{{ config('app.name') }}"
    ></show-rota>
</x-main-layout>