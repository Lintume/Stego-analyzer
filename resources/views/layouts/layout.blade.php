<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Steganography analyzer')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="/">Steganography</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li @if(request()->url() == route('lsb'))class="active"@endif>
                    <a href="{{route('lsb')}}">LSB</a>
                </li>
                <li @if(request()->url() == route('lsbCrypt'))class="active"@endif>
                    <a href="{{route('lsbCrypt')}}">Crypto LSB</a>
                </li>
                <li @if(request()->url() == route('lsbOffset'))class="active"@endif>
                    <a href="{{route('lsbOffset')}}">Crypto LSB offset</a>
                </li>
                <li @if(request()->url() == route('lsb2bits'))class="active"@endif>
                    <a href="{{route('lsb2bits')}}">Crypto LSB 2 bits</a>
                </li>
                <li @if(request()->url() == route('lsb3channels'))class="active"@endif>
                    <a href="{{route('lsb3channels')}}">Crypto LSB 3 channels</a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li @if(request()->url() == route('steganography'))class="active"@endif>
                    <a href="{{route('steganography')}}">Analyze</a>
                </li>
                <li>
                    <a href="https://github.com/Lintume/Leader-crawler">GIT</a>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
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
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    @show
</body>
</html>


