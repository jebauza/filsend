<!DOCTYPE html>
<html>

@include('sections.head')

<body class="hold-transition sidebar-mini">
    <div class="wrapper" id="app">

        @if (Auth::check())
        <App basepath="{{route('web.basepath')}}" :auth_user="{{ Auth::user() }}"></App>
        @else
        <Auth basepath="{{route('web.basepath')}}"></Auth>
        @endif

        <div id="sidebar-overlay"></div>
    </div>
    <!-- ./wrapper -->

    @include('sections.script')

</body>

</html>
