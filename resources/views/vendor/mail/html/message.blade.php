@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img src="file://{{ public_path() . '/assets/images/CloudRadar_Logo_blue_big.png' }}" alt="CloudRadar.io">
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
{{--@isset($subcopy)--}}
{{--@slot('subcopy')--}}
{{--@component('mail::subcopy')--}}
{{--{{ $subcopy }}--}}
{{--@endcomponent--}}
{{--@endslot--}}
{{--@endisset--}}

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
Cloudradar.io is a service from cloudradar GmbH, 14467 Potsdam, Germany
@endcomponent
@endslot
@endcomponent
