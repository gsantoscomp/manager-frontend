@extends('template.admin-template')

@section('styles')
    @parent
@endsection

@section('page-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Empresas</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Resultados</h6>
        <button class="btn btn-primary btn-icon-split"  data-toggle="modal" data-target="#add-company">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Nova Empresa</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="companies-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>CNPJ</th>
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
<div class="modal fade" id="add-company" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Novo Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-company-form">
                    <div class="form-group">
                        <label for="name">Nome <small>*</small></label>
                        <input type="text" name="name" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="cnpj">CNPJ</label>
                        <input type="text" name="cnpj" class="form-control form-control-user">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button id="submit-company-form" type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    @parent
    
    <script>
        $(document).ready(function() {
            getCompanies();
        });

        $('#submit-company-form').on('click', function(event) {
            event.preventDefault();

            if ($(this).hasClass('edit-mode')) {
                updateCompany($(this).data('target'));
            } else {
                addCompany();
            }
        });

        $('#add-company').on('hidden.bs.modal', function (e) {
            $('.modal-title').html('Novo Cliente');
            $('#add-company-form')[0].reset();
            $('#submit-company-form').removeClass('edit-mode').removeData('target');
        });

        function getCompanies() {
            const companyTableBody = $('#companies-table tbody');

            AjaxRequest({
                url: apiManagerURL + 'companies',
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
                                '<td>' + (item.cnpj || '-') + '</td>' +
                                '<td style="width:1%" class="text-nowrap">' +
                                    '<a class="action-buttons edit-company" data-target="' + item.id + '">' + 
                                        '<i style="margin-right: 1rem" class="fas fa-pen"></i>' + 
                                    '</a>'  +
                                    '<a class="action-buttons delete-company" data-target="' + item.id + '">' + 
                                        '<i class="fas fa-trash" aria-hidden="true"></i>' +
                                    '</a>'  +
                                '</td>' +
                            '</tr>';
                    }, '');

                    companyTableBody.html(tableContent);
                    $('#companies-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
                        },
                    });

                    $('.edit-company').on('click', function(event) {
                        event.preventDefault();
                        const companyId = $(this).data('target');
                        editClient(companyId);
                    });

                    $('.delete-company').on('click', function(event) {
                        event.preventDefault();
                        const companyId = $(this).data('target');
                        deleteClient(companyId);
                    });

                },
                error: function(error) {
                    if (error.status == 401) {
                        triggerErrorAlert('É necessário fazer login novamente');
                    }
                }
            }, 'companies.index');
        }

        function addCompany() {
            const form = $('#add-company-form');

            AjaxRequest({
                url: apiManagerURL + 'companies/',
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: form.serialize(),
                success: function(result) {
                    $('#companies-table').DataTable().destroy();
                    getCompanies();     
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
            }, 'companies.store');

            $('#add-company').modal('hide');
        }

        function deleteClient(companyId) {
            AjaxRequest({
                url: apiManagerURL + 'companies/delete/' + companyId,
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('#companies-table').DataTable().destroy();
                    getCompanies();     
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
            }, 'companies.destroy');
        }

        function editClient(companyId) {
            AjaxRequest({
                url: apiManagerURL + 'companies/' + companyId,
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('input[name="name"]').val(result.name);
                    $('input[name="cnpj"]').val(result.cnpj);

                    $('#submit-company-form').addClass('edit-mode').data('target', result.id);

                    $('.modal-title').html('Editar Cliente');
                    $('#add-company').modal('show');
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
            }, 'companies.index');
        }

        function updateCompany(companyId) {
            let formData = $('#add-company-form').serializeArray(); 
            formData.push({name: 'id', value: companyId});

            AjaxRequest({
                url: apiManagerURL + 'companies/update/' + companyId,
                method: 'PUT',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: $.param(formData),
                success: function(result) {
                    $('#companies-table').DataTable().destroy();
                    getCompanies();    
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
            }, 'companies.update');

            $('#add-company').modal('hide');
        }
    </script>
@endsection
