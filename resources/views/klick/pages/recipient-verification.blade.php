@extends('klick.layouts.app')

@section('body')
    <x-centered-card
        :title="$recipient->isVerified() ? 'Great!' : 'Error!'"
    >
        @if($recipient->isVerified())
            <p>Congratulations. Your email has been confirmed. You'll receive alerts from this point forward.</p>
            <p>Although we hope this will never happen.</p>
            <p><strong>Finger's crossed...</strong></p>
        @else
            <p>We're very sorry but something went wrong. Either we don't know the submitted email or the verification
                code is wrong.</p>
        @endif
    </x-centered-card>
@endsection
