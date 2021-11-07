@extends('my.email.notification')
@section('header')
    @lang('marketing.YourHost') {{ $host->name }} <strong>@lang('marketing.IsNotMonitored')</strong>
@endsection
@section('text')
    @lang('marketing.ToGetHostMonitored').<br>
    @lang('marketing.Link'): <a style="color:#F76613" href="{{ route('web.host.show',['host' => $host->id]) }}">@lang('marketing.AddChecks')</a><br>
    <br>
    @lang('marketing.IfYouHaveQuestions').<br>
    <br>
    @lang('marketing.YourCloudRadarTeam')<br>
@endsection
