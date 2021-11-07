@extends('klick.layouts.app')

@section('body')
    <x-centered-card
        :title="$user->product_news ? 'Error!' : 'Success!'"
        link-text="Login to your account"
        :link-target="route('web.login')"
    >
        @if(! $user->product_news)
            <p>You have been unsubscribed from product news.</p>
        @else
            <p>We're very sorry but something went wrong. Either the user given was not found or does not belong to given team. If you believe this is in error please get in touch.</p>
        @endif
    </x-centered-card>
@endsection
