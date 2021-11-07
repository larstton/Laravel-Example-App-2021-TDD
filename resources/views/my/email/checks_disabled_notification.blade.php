@extends('my.email.notification')
@section('header')
    @lang('marketing.WeDetectedChecks')
@endsection
@section('text')
    <ul>
        @foreach( $data['web'] as $check )
            <li>@lang('marketing.WebCheck') {{ $check->host->name }} {{ $check->host->connect }} {{ $check->method }} {{ $check->path }}</li>
        @endforeach
        @foreach( $data['service'] as $check )
            <li>@lang('marketing.ServiceCheck') {{ $check->host->name }} {{ $check->service }} {{ $check->port }}</li>
        @endforeach
    </ul>
    <br>
    @lang('marketing.DisabledAutomatically')
    <br>
    @lang('marketing.IfYouHaveQuestions')<br>
    <br>
    @lang('marketing.YourCloudRadarTeam')<br>
@endsection
