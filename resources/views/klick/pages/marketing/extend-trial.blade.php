@extends('klick.layouts.app')

@section('body')
    <x-centered-card
        :title="$user->wasChanged('trial_ends_at') ? 'Great!' : 'Sorry!'"
        link-text="Login to your account"
        :link-target="route('web.login')"
    >
        @if($user->wasChanged('trial_ends_at'))
            <p>Your trial has been extended for 15 days.</p>
        @else
            <p>Your trial was not extended. This is likely because you have already received an extension previously. If you believe this is in error please get in touch.</p>
        @endif
    </x-centered-card>
@endsection
