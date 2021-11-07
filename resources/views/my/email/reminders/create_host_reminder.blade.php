@extends('my.email.notification')
@section('header')
    @lang('marketing.StartYourMonitoring')<br>
@endsection
@section('text')
    @lang('marketing.ToGetStarted')
    <br>
    @lang('marketing.Link'): <a style="color:#F76613" href="{{route('web.host.create')}}">@lang('marketing.CreateFirstHost')</a><br>
    <br>
    @lang('marketing.IfYouHaveQuestions').<br>
    <br>
    @lang('marketing.YourCloudRadarTeam')<br>

@endsection
