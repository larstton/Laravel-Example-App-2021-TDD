@component('mail::message')
Hi

You've reached the end of your free trial with CloudRadar.

***All your monitoring and alerts have been paused.***

You will need to upgrade your account to reactivate your monitoring.

@component('mail::button', ['url' => $loginUrl])
    Log in and click on "upgrade"
@endcomponent

Your data and configuration will be stored for 60 days. You can re-activate your account at any time by upgrading to a paid plan.

CloudRadar is one of the best deals out there. For just €1.5 or US$1.70 per host you get all features you need to monitor your infrastructure: Full Windows/Linux support, dashboard, push alerting, rules, and reporting.

Nicholas Thiede, CEO<br>
CloudRadar<br>
PS: Not ready to upgrade? Let us know what's holding you back.
@endcomponent
