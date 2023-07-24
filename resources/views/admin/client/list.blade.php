@extends('template.admin-template')

@section('styles')
    @parent
@endsection

@section('page-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Clientes</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Resultados</h6>
        <button class="btn btn-primary btn-icon-split"  data-toggle="modal" data-target="#add-client">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Novo Cliente</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="clients-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Tipo de Documento</th>
                        <th>Documento</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Endereço</th>
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
<div class="modal fade" id="add-client" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Novo Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-client-form">
                    <div class="form-group">
                        <label for="name">Nome <small>*</small></label>
                        <input type="text" name="name" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="document_type">Tipo de Documento <small>*</small></label>
                        <select name="document_type" class="form-control form-control-user" required>
                            <option value="" disabled selected>Selecione um tipo de documento</option>
                            <option value="cpf">CPF</option>
                            <option value="rg">RG</option>
                            <option value="cnh">CNH</option>
                            <!-- Adicionar outros tipos de documentos do enum da tabela aqui -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="document">Documento <small>*</small></label>
                        <input type="text" name="document" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" class="form-control form-control-user">
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Telefone</label>
                        <input type="tel" name="phone_number" class="form-control form-control-user">
                    </div>
                    <div class="form-group">
                        <label for="address">Endereço</label>
                        <input type="text" name="address" class="form-control form-control-user">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button id="submit-client-form" type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    @parent
    
    <script>
        $(document).ready(function() {
            getClients();
        });

        $('#submit-client-form').on('click', function(event) {
            event.preventDefault();

            if ($(this).hasClass('edit-mode')) {
                updateClient($(this).data('target'));
            } else {
                addClient();
            }
        });

        $('#add-client').on('hidden.bs.modal', function (e) {
            $('.modal-title').html('Novo Cliente');
            $('#add-client-form')[0].reset();
            $('#submit-client-form').removeClass('edit-mode').removeData('target');
        });

        function getClients() {
            const clientTableBody = $('#clients-table tbody');

            AjaxRequest({
                url: apiManagerURL + 'client',
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
                                '<td>' + item.document_type + '</td>' +
                                '<td>' + item.document + '</td>' +
                                '<td>' + item.email + '</td>' +
                                '<td>' + item.phone_number + '</td>' +
                                '<td>' + item.address + '</td>' +
                                '<td style="width:1%" class="text-nowrap">' +
                                    '<a class="action-buttons edit-client" data-target="' + item.id + '">' + 
                                        '<i style="margin-right: 1rem" class="fas fa-pen"></i>' + 
                                    '</a>'  +
                                    '<a class="action-buttons delete-client" data-target="' + item.id + '">' + 
                                        '<i class="fas fa-trash" aria-hidden="true"></i>' +
                                    '</a>'  +
                                '</td>' +
                            '</tr>';
                    }, '');

                    clientTableBody.html(tableContent);
                    $('#clients-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
                        },
                    });

                    $('.edit-client').on('click', function(event) {
                        event.preventDefault();
                        const clientId = $(this).data('target');
                        editClient(clientId);
                    });

                    $('.delete-client').on('click', function(event) {
                        event.preventDefault();
                        const clientId = $(this).data('target');
                        deleteClient(clientId);
                    });

                },
                error: function(error) {
                    if (error.status == 401) {
                        triggerErrorAlert('É necessário fazer login novamente');
                    }
                }
            }, 'client.index');
        }

        function addClient() {
            const form = $('#add-client-form');

            AjaxRequest({
                url: apiManagerURL + 'client/',
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: form.serialize(),
                success: function(result) {
                    $('#clients-table').DataTable().destroy();
                    getClients();     
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
            }, 'client.store');

            $('#add-client').modal('hide');
        }

        function deleteClient(clientId) {
            AjaxRequest({
                url: apiManagerURL + 'client/delete/' + clientId,
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('#clients-table').DataTable().destroy();
                    getClients();     
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
            }, 'client.destroy');
        }

        function editClient(clientId) {
            AjaxRequest({
                url: apiManagerURL + 'client/' + clientId,
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('input[name="name"]').val(result.name);
                    $('select[name="document_type"]').val(result.document_type);
                    $('input[name="document"]').val(result.document);
                    $('input[name="email"]').val(result.email);
                    $('input[name="phone_number"]').val(result.phone_number);
                    $('input[name="address"]').val(result.address);

                    $('#submit-client-form').addClass('edit-mode').data('target', result.id);

                    $('.modal-title').html('Editar Cliente');
                    $('#add-client').modal('show');
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
            }, 'client.index');
        }

        function updateClient(clientId) {
            let formData = $('#add-client-form').serializeArray(); 
            formData.push({name: 'id', value: clientId});

            AjaxRequest({
                url: apiManagerURL + 'client/update/' + clientId,
                method: 'PUT',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: $.param(formData),
                success: function(result) {
                    $('#clients-table').DataTable().destroy();
                    getClients();    
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
            }, 'client.update');

            $('#add-client').modal('hide');
        }
    </script>
@endsection
