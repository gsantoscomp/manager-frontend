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

@section('scripts')
    <script>
        const accessToken = '{{ session("accessToken") }}';
        const userPermissions = @json(session("permissions"));

        console.log(userPermissions);

        function hasPermission(permission) {
            return !!userPermissions.find(item => item == permission);
        }

        function triggerErrorAlert(message) {
            $('.container-fluid').prepend(
                '<div class="alert alert-danger" role="alert">' +
                    message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                '</div>'
            );
        }

        function triggerSuccessAlert(message) {
            $('.container-fluid').prepend(
                '<div class="alert alert-success" role="alert">' +
                    message +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                '</div>'
            );
        }

        const AjaxRequest = (options, permission = false) => {
            if (hasPermission(permission)) {
                $.ajax(options);
            } else {
                triggerErrorAlert('Você não possui permissão para acessar este recurso.');
            }
        }
    </script>
@endsection