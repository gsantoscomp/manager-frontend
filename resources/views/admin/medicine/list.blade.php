@extends('template.admin-template')

@section('styles')
    @parent
@endsection

@section('page-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Medicamentos</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Resultados</h6>
        <button class="btn btn-primary btn-icon-split"  data-toggle="modal" data-target="#add-medicine">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Novo Medicamento</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="medicines-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Preço de Compra</th>
                        <th>Preço de Venda</th>
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
<div class="modal fade" id="add-medicine" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Novo Medicamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-medicine-form">
                    <div class="form-group">
                        <label for="name">Nome <small>*</small></label>
                        <input type="text" name="name" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantidade <small>*</small></label>
                        <input type="number" name="quantity" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="purchase_price">Preço de Compra <small>*</small></label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="sale_price">Preço de Venda <small>*</small></label>
                        <input type="number" step="0.01" name="sale_price" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea name="description" class="form-control form-control-user"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button id="submit-medicine-form" type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    
    <script>
        $(document).ready(function() {
            getMedicines();
        });

        $('#submit-medicine-form').on('click', function(event) {
            event.preventDefault();

            if ($(this).hasClass('edit-mode')) {
                updateMedicine($(this).data('target'));
            } else {
                addMedicine();
            }
        });

        $('#add-medicine').on('hidden.bs.modal', function (e) {
            $('.modal-title').html('Novo Medicamento');
            $('#add-medicine-form')[0].reset();
            $('#submit-medicine-form').removeClass('edit-mode').removeData('target');
        });

        function getMedicines() {
            const medicineTableBody = $('#medicines-table tbody');

            AjaxRequest({
                url: apiManagerURL + 'medicine',
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
                                '<td>' + item.quantity + '</td>' +
                                '<td>' + item.purchase_price + '</td>' +
                                '<td>' + item.sale_price + '</td>' +
                                '<td>' + item.description + '</td>' +
                                '<td style="width:1%" class="text-nowrap">' +
                                    '<a class="action-buttons edit-medicine" data-target="' + item.id + '">' + 
                                        '<i style="margin-right: 1rem" class="fas fa-pen"></i>' + 
                                    '</a>'  +
                                    '<a class="action-buttons delete-medicine" data-target="' + item.id + '">' + 
                                        '<i class="fas fa-trash" aria-hidden="true"></i>' +
                                    '</a>'  +
                                '</td>' +
                            '</tr>';
                    }, '');

                    medicineTableBody.html(tableContent);
                    $('#medicines-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
                        },
                    });

                    $('.edit-medicine').on('click', function(event) {
                        event.preventDefault();
                        const medicineId = $(this).data('target');
                        editMedicine(medicineId);
                    });

                    $('.delete-medicine').on('click', function(event) {
                        event.preventDefault();
                        const medicineId = $(this).data('target');
                        deleteMedicine(medicineId);
                    });

                },
                error: function(error) {
                    if (error.status == 401) {
                        triggerErrorAlert('É necessário fazer login novamente');
                    }
                }
            }, 'medicine.index');
        }

        function addMedicine() {
            const form = $('#add-medicine-form');

            AjaxRequest({
                url: apiManagerURL + 'medicine/',
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: form.serialize(),
                success: function(result) {
                    $('#medicines-table').DataTable().destroy();
                    getMedicines();     
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
            }, 'medicine.store');

            $('#add-medicine').modal('hide');
        }

        function deleteMedicine(medicineId) {
            AjaxRequest({
                url: apiManagerURL + 'medicine/delete/' + medicineId,
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('#medicines-table').DataTable().destroy();
                    getMedicines();     
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
            }, 'medicine.destroy');
        }

        function editMedicine(medicineId) {
            AjaxRequest({
                url: apiManagerURL + 'medicine/' + medicineId,
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('input[name="name"]').val(result.name);
                    $('input[name="quantity"]').val(result.quantity);
                    $('input[name="purchase_price"]').val(result.purchase_price);
                    $('input[name="sale_price"]').val(result.sale_price);
                    $('textarea[name="description"]').val(result.description);

                    $('#submit-medicine-form').addClass('edit-mode').data('target', result.id);

                    $('.modal-title').html('Editar Medicamento');
                    $('#add-medicine').modal('show');
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
            }, 'medicine.index');
        }

        function updateMedicine(medicineId) {
            let formData = $('#add-medicine-form').serializeArray(); 
            formData.push({name: 'id', value: medicineId});

            AjaxRequest({
                url: apiManagerURL + 'medicine/update/' + medicineId,
                method: 'PUT',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: $.param(formData),
                success: function(result) {
                    $('#medicines-table').DataTable().destroy();
                    getMedicines();    
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
            }, 'medicine.update');

            $('#add-medicine').modal('hide');
        }
    </script>
@endsection
