@extends('my.email.notification')
@section('header')
    @lang('marketing.YourHost') {{ $host->name }} <strong>@lang('marketing.IsNotMonitored')</strong>
@endsection
@section('text')
    @lang('marketing.EnabledInsideOSMonitoring') {{ $host->name }} @lang('marketing.ToGetFullInsights')
    <br>
    @lang('marketing.MonitoringNotSetUp').<br>
    @lang('marketing.InstallationRequired') <a style="color:#F76613"
                                          href="{{ route('web.host.show',['host' => $host->id]) }}">@lang('marketing.Hosts') &gt; {{ $host->name }}</a> @lang('marketing.AndClickInstallAgent').<br>
    @lang('marketing.FollowInstallationGuide').<br>

@endsection
