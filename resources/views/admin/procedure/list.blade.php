@extends('template.admin-template')

@section('styles')
    @parent
@endsection

@section('page-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Procedimentos</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Resultados</h6>
        <button class="btn btn-primary btn-icon-split"  data-toggle="modal" data-target="#add-procedure">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Novo Procedimento</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="procedures-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Preço</th>
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
<div class="modal fade" id="add-procedure" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Novo Procedimento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-procedure-form">
                    <div class="form-group">
                        <label for="name">Nome <small>*</small></label>
                        <input type="text" name="name" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Preço <small>*</small></label>
                        <input type="text" name="price" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <input type="text" name="description" class="form-control form-control-user">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button id="submit-procedure-form" type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    
    <script>
        $(document).ready(function() {
            getProcedures();
        });

        $('#submit-procedure-form').on('click', function(event) {
            event.preventDefault();

            if ($(this).hasClass('edit-mode')) {
                updateProcedure($(this).data('target'));
            } else {
                addProcedure();
            }
        });

        $('#add-procedure').on('hidden.bs.modal', function (e) {
            $('.modal-title').html('Novo Procedimento');
            $('#add-procedure-form')[0].reset();
            $('#submit-procedure-form').removeClass('edit-mode').removeData('target');
        });

        function getProcedures() {
            const procedureTableBody = $('#procedures-table tbody');

            AjaxRequest({
                url: apiManagerURL + 'procedure',
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
                                '<td>' + item.price + '</td>' +
                                '<td>' + item.description + '</td>' +
                                '<td style="width:1%" class="text-nowrap">' +
                                    '<a class="action-buttons edit-procedure" data-target="' + item.id + '">' + 
                                        '<i style="margin-right: 1rem" class="fas fa-pen"></i>' + 
                                    '</a>'  +
                                    '<a class="action-buttons delete-procedure" data-target="' + item.id + '">' + 
                                        '<i class="fas fa-trash" aria-hidden="true"></i>' +
                                    '</a>'  +
                                '</td>' +
                            '</tr>';
                    }, '');

                    procedureTableBody.html(tableContent);
                    $('#procedures-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
                        },
                    });

                    $('.edit-procedure').on('click', function(event) {
                        event.preventDefault();
                        const procedureId = $(this).data('target');
                        editProcedure(procedureId);
                    });

                    $('.delete-procedure').on('click', function(event) {
                        event.preventDefault();
                        const procedureId = $(this).data('target');
                        deleteProcedure(procedureId);
                    });

                },
                error: function(error) {
                    if (error.status == 401) {
                        triggerErrorAlert('É necessário fazer login novamente');
                    }
                }
            }, 'procedure.index');
        }

        function addProcedure() {
            const form = $('#add-procedure-form');

            AjaxRequest({
                url: apiManagerURL + 'procedure',
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: form.serialize(),
                success: function(result) {
                    $('#procedures-table').DataTable().destroy();
                    getProcedures();     
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
            }, 'procedure.store');

            $('#add-procedure').modal('hide');
        }

        function deleteProcedure(procedureId) {
            AjaxRequest({
                url: apiManagerURL + 'procedure/delete/' + procedureId,
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('#procedures-table').DataTable().destroy();
                    getProcedures();     
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
            }, 'procedure.destroy');
        }

        function editProcedure(procedureId) {
            AjaxRequest({
                url: apiManagerURL + 'procedure/' + procedureId,
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('input[name="name"]').val(result.name);
                    $('input[name="price"]').val(result.price);
                    $('input[name="description"]').val(result.description);

                    $('#submit-procedure-form').addClass('edit-mode').data('target', result.id);

                    $('.modal-title').html('Editar Procedimento');
                    $('#add-procedure').modal('show');
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
            }, 'procedure.index');
        }

        function updateProcedure(procedureId) {
            let formData = $('#add-procedure-form').serializeArray(); 
            formData.push({name: 'id', value: procedureId});

            AjaxRequest({
                url: apiManagerURL + 'procedure/update/' + procedureId,
                method: 'PUT',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: $.param(formData),
                success: function(result) {
                    $('#procedures-table').DataTable().destroy();   
                    getProcedures();    
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
            }, 'procedure.update');

            $('#add-procedure').modal('hide');
        }
    </script>
@endsection
