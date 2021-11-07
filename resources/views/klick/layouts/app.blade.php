<!DOCTYPE html>
<!--[if lte IE 9]> <html lang="en" class="lte-ie9 app_my_theme"> <![endif]-->
<!--[if gt IE 9]>--> <html lang="en" class="app_my_theme"> <!--<![endif]-->
@include('klick.partials.head')
<body>
<div class="mdl-layout mdl-js-layout">
    <main class="mdl-layout__content" style="flex: 1 0 auto;">
        @yield('body')
    </main>
    @include('klick.partials.footer')
</div>
</body>
</html>
