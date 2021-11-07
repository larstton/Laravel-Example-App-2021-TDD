@extends('klick.layouts.app')

@section('body')
    <x-centered-card
        :title="$recipient->alerts ? 'Error!' : 'Success!'"
        link-text="Login to your account"
        :link-target="route('web.login')"
    >
        @if(! $recipient->comments)
            <p>Recipient <span style="font-weight: bold">{{ $recipient->sendto }}</span> has been unsubscribed from
                event comments.</p>
        @else
            <p>We're very sorry but something went wrong. Either the recipient or the event given was not found or does
                not belong to given team. If you believe this is in error please get in touch.</p>
        @endif
    </x-centered-card>
@endsection
