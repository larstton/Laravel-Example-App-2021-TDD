@extends('klick.layouts.app')

@section('body')
    <x-centered-card
        :title="!$recipient->dailySummary ? 'Success!' : 'Error!'"
    >
        @if(!$recipient->dailySummary)
            <p>You have been unsubscribed from daily summary e-mails</p>
        @else
            <p>We're very sorry but something went wrong.</p>
        @endif
    </x-centered-card>
@endsection
