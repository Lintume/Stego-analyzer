<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Steganography analyzer')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body>
    @yield('content')

@section('stylesheets')
@show

@section('javascript')
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <script src="{{asset('js/app.js')}}"></script>
    <script>
        Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
        Vue.config.devtools = true;
        Vue.config.debug = true;
        Vue.config.silent = true;
    </script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    @show
</body>
</html>


