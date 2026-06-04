@props(['errors'])

@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'auth-error']) }}>
        <div style="font-weight: 700; margin-bottom: 6px;">
            {{ __('Whoops! Something went wrong.') }}
        </div>

        <ul style="margin: 0; padding-left: 18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
