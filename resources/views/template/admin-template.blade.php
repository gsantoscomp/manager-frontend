@extends('template.base')

@section('body-id')page-top
@endsection

@section('body')
    <!-- Page Wrapper -->
    <div id="wrapper">
        @include('template.components.sidebar')

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">

                <!-- Main Content -->
                <div id="content">
                    @include('template.components.topbar')

                    <!-- Begin Page Content -->
                    <div class="container-fluid">
                        @yield('page-content')
                    </div>
                </div>

                @include('template.components.footer')
            </div>
    </div>

    @include('template.components.logout-modal')
@endsection