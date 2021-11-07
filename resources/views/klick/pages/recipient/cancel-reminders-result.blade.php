@extends('klick.layouts.app')

@section('body')
    <x-centered-card
        :title="$event->reminders->is(\App\Enums\EventReminder::Disabled()) ? 'Success!' : 'Error!'"
        link-text="Login to your account"
        :link-target="route('web.login')"
    >
        @if($event->reminders->is(\App\Enums\EventReminder::Disabled()))
            <p>You have been unsubscribed from reminders for this event</p>
        @else
            <p>We're very sorry but something went wrong. Either the recipient or the event given was not found or does not belong to given team. If you believe this is in error please get in touch.</p>
        @endif
    </x-centered-card>
@endsection
