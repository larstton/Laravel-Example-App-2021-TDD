@extends('my.email.notification')
@section('header')
    @lang('marketing.YourLocation') {{ $frontman->location }} <strong>@lang('marketing.IsNotMonitored')</strong>
@endsection
@section('text')
    @lang('marketing.EnabledFrontman') {{ $frontman->location }} @lang('marketing.ToMonitorIntranet').
    <br>
    @lang('marketing.MonitoringNotSetUp').<br>
    @lang('marketing.FrontmanInstallationRequired') <a style="color:#F76613"
                                               href="{{ route('web.frontmen') }}">@lang('marketing.Frontman') &gt; {{ $frontman->location }}</a> @lang('marketing.ClickInstallFrontman').<br>
    @lang('marketing.FollowInstallationGuide').<br>
@endsection
