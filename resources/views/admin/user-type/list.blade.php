@extends('template.admin-template')

@section('styles')
    @parent
@endsection

@section('page-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Tipos de Usuários</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Resultados</h6>
        <button class="btn btn-primary btn-icon-split"  data-toggle="modal" data-target="#add-user-type">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tipo de Usuário</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="user-types-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
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

<!-- Modal -->
<div class="modal fade" id="add-user-type" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Novo Tipo de Usuário</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="add-user-type-form">
                <div class="form-group">
                    <label for="type">Tipo de Usuário <small>*</small></label>
                    <input type="text" name="type" class="form-control form-control-user" required>
                </div>
                <div class="form-group">
                    <label for="description">Descrição</label>
                    <input type="textarea" name="description" class="form-control form-control-user">
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button id="submit-user-type-form" type="submit" class="btn btn-primary">Salvar</button>
        </div>
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

        $('#submit-user-type-form').on('click', function(event) {
            event.preventDefault();

            if ($(this).hasClass('edit-mode')) {
                updateUserType($(this).data('target'));
            } else {
                addUserType();
            }
        });

        $('#add-user-type').on('hidden.bs.modal', function (e) {
            $('.modal-title').html('Novo Tipo de Usuário');
            $('#add-user-type-form')[0].reset();
            $('#submit-user-type-form').removeClass('edit-mode').removeData('target');
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
                    const tableContent = result.reduce(function (finalString, item, index) {
                        return finalString + 
                            '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + item.type + '</td>' +
                                '<td>' + item.description + '</td>' +
                                '<td style="width:1%" class="text-nowrap">' +
                                    '<a class="action-buttons edit-user-type" data-target="' + item.id + '">' + 
                                        '<i style="margin-right: 1rem" class="fas fa-pen"></i>' + 
                                    '</a>'  +
                                    '<a class="action-buttons delete-user-type" data-target="' + item.id + '">' + 
                                        '<i class="fas fa-trash" aria-hidden="true"></i>' +
                                    '</a>'  +
                                '</td>' +
                            '</tr>';
                    }, '');

                    userTypeTableBody.html(tableContent);
                    $('#user-types-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
                        },
                    });

                    $('.edit-user-type').on('click', function(event) {
                        event.preventDefault();
                        const idUserType = $(this).data('target');
                        editUserType(idUserType);
                    });

                    $('.delete-user-type').on('click', function(event) {
                        event.preventDefault();
                        const idUserType = $(this).data('target');
                        deleteUserType(idUserType);
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

        function addUserType() {
            const form = $('#add-user-type-form');

            $.ajax({
                url: apiManagerURL + 'userType',
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: form.serialize(),
                success: function(result) {
                    $('#user-types-table').DataTable().destroy();
                    getUserTypes();     
                },
                error: function(error) {
                    let message = 'Não foi possível realizar sua solicitação no momento.';
                    
                    if (error.status == 401) {
                        message = 'É necessário fazer login novamente';
                    }

                    if (error.status == 400) {
                        const { errors } = error.responseJSON;
                        const messages =  Object.values(errors);

                        message = messages.reduce(function (finalString, item) {
                            return finalString + (item + '<br>');
                        }, '');
                    }
                    
                    $('.container-fluid').prepend(
                        '<div class="alert alert-primary alert-dismissible fade show" role="alert">' +
                            message +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                        '</div>'
                    );
                }
            });

            $('#add-user-type').modal('hide');
        }

        function deleteUserType(idUserType) {
            $.ajax({
                url: apiManagerURL + 'userType/delete/' + idUserType,
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('#user-types-table').DataTable().destroy();
                    getUserTypes();     
                },
                error: function(error) {
                    let message = 'Não foi possível realizar sua solicitação no momento.';
                    
                    if (error.status == 401) {
                        message = 'É necessário fazer login novamente';
                    }

                    if (error.status == 400) {
                        const { errors } = error.responseJSON;
                        const messages =  Object.values(errors);

                        message = messages.reduce(function (finalString, item) {
                            return finalString + (item + '<br>');
                        }, '');
                    }
                    
                    $('.container-fluid').prepend(
                        '<div class="alert alert-primary alert-dismissible fade show" role="alert">' +
                            message +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                        '</div>'
                    );
                }
            });
        }

        function editUserType(idUserType) {
            $.ajax({
                url: apiManagerURL + 'userType/' + idUserType,
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('input[name="type"]').val(result.type);
                    $('input[name="description"]').val(result.description);

                    $('#submit-user-type-form').addClass('edit-mode').data('target', result.id);

                    $('.modal-title').html('Editar Tipo de Usuário');
                    $('#add-user-type').modal('show');
                },
                error: function(error) {
                    let message = 'Não foi possível realizar sua solicitação no momento.';
                    
                    if (error.status == 401) {
                        message = 'É necessário fazer login novamente';
                    }

                    if (error.status == 400) {
                        const { errors } = error.responseJSON;
                        const messages =  Object.values(errors);

                        message = messages.reduce(function (finalString, item) {
                            return finalString + (item + '<br>');
                        }, '');
                    }
                    
                    $('.container-fluid').prepend(
                        '<div class="alert alert-primary alert-dismissible fade show" role="alert">' +
                            message +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                        '</div>'
                    );
                }
            });
        }

        function updateUserType(idUserType) {
            let formData = $('#add-user-type-form').serializeArray(); 
            formData.push({name: 'id', value: idUserType});

            $.ajax({
                url: apiManagerURL + 'userType/update/' + idUserType,
                method: 'PUT',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: $.param(formData),
                success: function(result) {
                    $('#user-types-table').DataTable().destroy();
                    getUserTypes();    
                },
                error: function(error) {
                    let message = 'Não foi possível realizar sua solicitação no momento.';
                    
                    if (error.status == 401) {
                        message = 'É necessário fazer login novamente';
                    }

                    if (error.status == 400) {
                        const { errors } = error.responseJSON;
                        const messages =  Object.values(errors);

                        message = messages.reduce(function (finalString, item) {
                            return finalString + (item + '<br>');
                        }, '');
                    }
                    
                    $('.container-fluid').prepend(
                        '<div class="alert alert-primary alert-dismissible fade show" role="alert">' +
                            message +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                        '</div>'
                    );
                }
            });


            $('#add-user-type').modal('hide');
        }
    </script>
@endsection