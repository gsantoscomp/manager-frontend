@extends('template.admin-template')

@section('styles')
    @parent
@endsection

@section('page-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Usuários</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Resultados</h6>
        <button class="btn btn-primary btn-icon-split"  data-toggle="modal" data-target="#add-user">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Novo Usuário</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="users-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Tipo de usuário</th>
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
<div class="modal fade" id="add-user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Novo Usuário</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-user-form">
                    <div class="form-group">
                        <label for="name">Nome <small>*</small></label>
                        <input type="text" name="name" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" class="form-control form-control-user">
                    </div>
                    <div class="form-group">
                        <label for="user_type_id">Tipo de Usuário <small>*</small></label>
                        <select name="user_type_id" class="form-control form-control-user" required>
                            <option value="" disabled selected>Selecione um tipo de usuário</option>
                            <!-- Aqui será preenchido com os tipos de usuário via JavaScript -->
                        </select>
                    <div class="form-group">
                        <label for="password">Senha <small>*</small></label>
                        <input type="password" name="password" class="form-control form-control-user" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button id="submit-user-form" type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    
    <script>
        $(document).ready(function() {
        
            getUsers();
            getUserTypesForSelect();
        });

        $('#submit-user-form').on('click', function(event) {
            event.preventDefault();

            if ($(this).hasClass('edit-mode')) {
                updateUser($(this).data('target'));
            } else {
                addUser();
            }
        });

        $('#add-user').on('hidden.bs.modal', function (e) {
            $('.modal-title').html('Novo Usuário');
            $('#add-user-form')[0].reset();
            $('#submit-user-form').removeClass('edit-mode').removeData('target');
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


        function getUsers() {
            const userTableBody = $('#users-table tbody');

            AjaxRequest({
                url: apiManagerURL + 'user',
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    const tableContent = result.reduce(function (finalString, item, index) {
                        console.log(item)
                        return finalString + 
                            '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + item.name + '</td>' +
                                '<td>' + item.email + '</td>' +
                                '<td>' + item.user_type.type + '</td>' +
                                '<td style="width:1%" class="text-nowrap">' +
                                    '<a class="action-buttons edit-user" data-target="' + item.id + '">' + 
                                        '<i style="margin-right: 1rem" class="fas fa-pen"></i>' + 
                                    '</a>'  +
                                    '<a class="action-buttons delete-user" data-target="' + item.id + '">' + 
                                        '<i class="fas fa-trash" aria-hidden="true"></i>' +
                                    '</a>'  +
                                '</td>' +
                            '</tr>';
                    }, '');

                    userTableBody.html(tableContent);
                    $('#users-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
                        },
                    });

                    $('.edit-user').on('click', function(event) {
                        event.preventDefault();
                        const userId = $(this).data('target');
                        editUser(userId);
                    });

                    $('.delete-user').on('click', function(event) {
                        event.preventDefault();
                        const userId = $(this).data('target');
                        deleteUser(userId);
                    });

                },
                error: function(error) {
                    if (error.status == 401) {
                        triggerErrorAlert('É necessário fazer login novamente');
                    }
                }
            }, 'user.index');
        }

        function addUser() {
            const form = $('#add-user-form');
            console.log(form.serialize())
            AjaxRequest({
                url: apiManagerURL + 'user/',
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: form.serialize(),
                success: function(result) {
                    $('#users-table').DataTable().destroy();
                    getUsers();     
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
            }, 'user.store');

            $('#add-user').modal('hide');
        }

        function deleteUser(userId) {
            AjaxRequest({
                url: apiManagerURL + 'user/delete/' + userId,
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('#users-table').DataTable().destroy();
                    getUsers();     
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
            }, 'user.destroy');
        }

        function editUser(userId) {
            AjaxRequest({
                url: apiManagerURL + 'user/' + userId,
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('input[name="name"]').val(result.name);
                    $('input[name="email"]').val(result.email);
                    $('select[name="user_type_id"]').val(result.user_type_id);

                    $('#submit-user-form').addClass('edit-mode').data('target', result.id);

                    $('.modal-title').html('Editar Usuário');
                    $('#add-user').modal('show');
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
            }, 'user.index');
        }

        function updateUser(userId) {
            let formData = $('#add-user-form').serializeArray(); 
            formData.push({name: 'id', value: userId});

            AjaxRequest({
                url: apiManagerURL + 'user/update/' + userId,
                method: 'PUT',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: $.param(formData),
                success: function(result) {
                    $('#users-table').DataTable().destroy();
                    getUsers();    
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
            }, 'user.update');

            $('#add-user').modal('hide');
        }
    </script>
@endsection
