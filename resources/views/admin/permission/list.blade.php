@extends('template.admin-template')

@section('styles')
    @parent
@endsection

@section('page-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Permissões</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Resultados</h6>
        <button class="btn btn-primary btn-icon-split"  data-toggle="modal" data-target="#add-permission">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Nova Permissão</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="permissions-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Tipo de Usuário</th>
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
<div class="modal fade" id="add-permission" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Nova Permissão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-permission-form">
                    <input type="hidden" name="module" value="x">
                    <input type="hidden" name="page" value="x">

                    <div class="form-group">
                        <label for="name">Nome <small>*</small></label>
                        <input type="text" name="name" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="user_type_id">Tipo de Usuário <small>*</small></label>
                        <select name="user_type_id" class="form-control form-control-user" required>
                            <option value="" disabled selected>Selecione um tipo de usuário</option>
                            <!-- Aqui será preenchido com os tipos de usuário via JavaScript -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button id="submit-permission-form" type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    
    <script>
        $(document).ready(function() {
            getPermissions();
            getUserTypesForSelect();
        });

        $('#submit-permission-form').on('click', function(event) {
            event.preventDefault();

            if ($(this).hasClass('edit-mode')) {
                updatePermission($(this).data('target'));
            } else {
                addPermission();
            }
        });

        $('#add-permission').on('hidden.bs.modal', function (e) {
            $('.modal-title').html('Nova Permissão');
            $('#add-permission-form')[0].reset();
            $('#submit-permission-form').removeClass('edit-mode').removeData('target');
        });

        function getUserTypesForSelect() {
            const userTypeSelect = $('select[name="user_type_id"]');

            AjaxRequest({
                url: apiManagerURL + 'userType',
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken
                },
                success: function (result) {
                    const options = result.reduce(function (finalString, item) {
                        return finalString + '<option value="' + item.id + '">' + item.type + '</option>';
                    }, '');

                    userTypeSelect.append(options);
                },
                error: function (error) {
                    console.log('Erro ao buscar tipos de usuário:', error);
                }
            }, 'userType.index');
        }

        function getPermissions() {
            const permissionTableBody = $('#permissions-table tbody');

            AjaxRequest({
                url: apiManagerURL + 'permission',
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
                                '<td>' + item.name + '</td>' +
                                '<td>' + item.user_type.type + '</td>' +
                                '<td style="width:1%" class="text-nowrap">' +
                                    '<a class="action-buttons edit-permission" data-target="' + item.id + '">' + 
                                        '<i style="margin-right: 1rem" class="fas fa-pen"></i>' + 
                                    '</a>'  +
                                    '<a class="action-buttons delete-permission" data-target="' + item.id + '">' + 
                                        '<i class="fas fa-trash" aria-hidden="true"></i>' +
                                    '</a>'  +
                                '</td>' +
                            '</tr>';
                    }, '');

                    permissionTableBody.html(tableContent);
                    $('#permissions-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
                        },
                    });

                    $('.edit-permission').on('click', function(event) {
                        event.preventDefault();
                        const permissionId = $(this).data('target');
                        editPermission(permissionId);
                    });

                    $('.delete-permission').on('click', function(event) {
                        event.preventDefault();
                        const permissionId = $(this).data('target');
                        deletePermission(permissionId);
                    });

                },
                error: function(error) {
                    if (error.status == 401) {
                        triggerErrorAlert('É necessário fazer login novamente');
                    }
                }
            }, 'permission.index');
        }

        function addPermission() {
            const form = $('#add-permission-form');

            AjaxRequest({
                url: apiManagerURL + 'permission',
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: form.serialize(),
                success: function(result) {
                    $('#permissions-table').DataTable().destroy();
                    getPermissions();     
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
                    
                    triggerErrorAlert(message);
                }
            }, 'permission.store');

            $('#add-permission').modal('hide');
        }

        function deletePermission(permissionId) {
            AjaxRequest({
                url: apiManagerURL + 'permission/delete/' + permissionId,
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('#permissions-table').DataTable().destroy();
                    getPermissions();     
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
                    
                    triggerErrorAlert(message);
                }
            }, 'permission.destroy');
        }

        function editPermission(permissionId) {
            AjaxRequest({
                url: apiManagerURL + 'permission/' + permissionId,
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('input[name="name"]').val(result.name);
                    $('select[name="user_type_id"]').val(result.user_type_id);

                    $('#submit-permission-form').addClass('edit-mode').data('target', result.id);

                    $('.modal-title').html('Editar Permissão');
                    $('#add-permission').modal('show');
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
                    
                    triggerErrorAlert(message);
                }
            }, 'permission.index');
        }

        function updatePermission(permissionId) {
            let formData = $('#add-permission-form').serializeArray(); 
            formData.push({name: 'id', value: permissionId});

            AjaxRequest({
                url: apiManagerURL + 'permission/update/' + permissionId,
                method: 'PUT',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: $.param(formData),
                success: function(result) {
                    $('#permissions-table').DataTable().destroy();
                    getPermissions();    
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
                    
                    triggerErrorAlert(message);
                }
            }, 'permission.update');

            $('#add-permission').modal('hide');
        }
    </script>
@endsection
