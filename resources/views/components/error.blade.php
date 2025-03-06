
@if ($errors->get($name))
    <span class="text-red-600">{{ $errors->get($name)[0] }}</span>
@endif