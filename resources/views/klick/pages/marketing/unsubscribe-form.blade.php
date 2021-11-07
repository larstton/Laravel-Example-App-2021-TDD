@extends('klick.layouts.app')

@section('body')
    <x-centered-card
        title="Confirmation needed"
        :form-action="url()->signedRoute('klick.unsubscribe.update', [$user, $team])"
        form-button-text="Yes, I'm sure"
    >
        <p>Do you really want to unsubscribe from all product news?</p>
    </x-centered-card>
@endsection

