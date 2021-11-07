@extends('klick.layouts.app')

@section('body')
    <x-centered-card
        title="Confirmation needed"
        :form-action="url()->signedRoute('klick.mute-comments.update', [$recipient])"
        form-button-text="Yes, disable notifications"
    >
        <p>Do you want to disable all comment notifications for recipient <span style="font-weight: bold">{{ $recipient->sendto }}</span>?</p>
        <p>You can re-enable notifications at any time by editing the recipient's settings.</p>
    </x-centered-card>
@endsection

