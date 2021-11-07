@extends('klick.layouts.app')

@section('body')
    <x-centered-card
        title="Error!"
        link-text="Login to your account"
        :link-target="route('web.login')"
    >
        <p>We're very sorry but something went wrong. If you believe this is in error please get in touch.</p>
    </x-centered-card>
@endsection
