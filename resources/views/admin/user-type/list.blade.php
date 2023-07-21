@extends('template.admin-template')

@section('styles')
    @parent
@endsection

@section('page-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Tipos de Usuários</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Resultados</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="user-types-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    
    <script>
        $(document).ready(function() {
            getUserTypes();
        });

        function getUserTypes() {
            const userTypeTableBody = $('#user-types-table tbody');

            $.ajax({
                url: apiManagerURL + 'userType',
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    const tableContent = result.reduce(function (finalString, item) {
                        return finalString + 
                            '<tr>' +
                                '<td>' + item.type + '</td>' +
                                '<td>' + item.description + '</td>' +
                                '<td></td>' +
                            '</tr>';
                    }, '');

                    userTypeTableBody.html(tableContent);
                    $('#user-types-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
                        },
                    });
                },
                error: function(error) {
                    if (error.status == 401) {
                        $('.container-fluid').prepend(
                            '<div class="alert alert-primary" role="alert">' +
                                'É necessário fazer login novamente' +
                            '</div>'
                        );
                    }
                }
            });
        }
    </script>
@endsection