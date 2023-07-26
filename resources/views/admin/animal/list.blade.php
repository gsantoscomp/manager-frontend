@extends('template.admin-template')

@section('styles')
    @parent
@endsection

@section('page-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Animais</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Resultados</h6>
        <button class="btn btn-primary btn-icon-split"  data-toggle="modal" data-target="#add-animal">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Novo Animal</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="animals-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Espécie</th>
                        <th>Cliente</th>
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
<div class="modal fade" id="add-animal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Novo Animal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-animal-form">
                    <div class="form-group">
                        <label for="name">Nome <small>*</small></label>
                        <input type="text" name="name" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="animal_type">Espécie <small>*</small></label>
                        <input type="text" name="animal_type" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="client_id">Cliente <small>*</small></label>
                        <select name="client_id" class="form-control form-control-user" required>
                            <option value="" disabled selected>Selecione um cliente</option>
                            <!-- Aqui será preenchido com os clientes via JavaScript -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button id="submit-animal-form" type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    
    <script>
        $(document).ready(function() {
            getAnimals();
            getClientsForSelect();
        });


        $('#submit-animal-form').on('click', function(event) {
            event.preventDefault();

            if ($(this).hasClass('edit-mode')) {
                updateAnimal($(this).data('target'));
            } else {
                addAnimal();
            }
        });

        $('#add-animal').on('hidden.bs.modal', function (e) {
            $('.modal-title').html('Novo Animal');
            $('#add-animal-form')[0].reset();
            $('#submit-animal-form').removeClass('edit-mode').removeData('target');
        });

        function getClientsForSelect() {
            const clientSelect = $('select[name="client_id"]');

            AjaxRequest({
                url: apiManagerURL + 'client',
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken
                },
                success: function (result) {
                    const options = result.reduce(function (finalString, item) {
                        return finalString + '<option value="' + item.id + '">' + item.name + '</option>';
                    }, '');

                    clientSelect.append(options);
                },
                error: function (error) {
                    console.log('Erro ao buscar clientes:', error);
                }
            }, 'client.index');
        }

        function getAnimals() {
            const animalTableBody = $('#animals-table tbody');

            AjaxRequest({
                url: apiManagerURL + 'company/animals',
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
                                '<td>' + item.animal_type + '</td>' +
                                '<td>' + item.client.name + '</td>' +
                                '<td style="width:1%" class="text-nowrap">' +
                                    '<a class="action-buttons edit-animal" data-target="' + item.id + '">' + 
                                        '<i style="margin-right: 1rem" class="fas fa-pen"></i>' + 
                                    '</a>'  +
                                    '<a  data-confirm="Tem certeza que deseja excluir este Animal?" class="action-buttons delete-animal" data-target="' + item.id + '">' + 
                                        '<i class="fas fa-trash" aria-hidden="true"></i>' +
                                    '</a>'  +
                                '</td>' +   
                            '</tr>';
                    }, '');

                    animalTableBody.html(tableContent);
                    $('#animals-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
                        },
                    });

                    $('.edit-animal').on('click', function(event) {
                        event.preventDefault();
                        const animalId = $(this).data('target');
                        editAnimal(animalId);
                    });

                    $('.delete-animal').on('click', function(event) {
                        event.preventDefault();
                        const animalId = $(this).data('target');
                        deleteAnimal(animalId);
                    });

                },
                error: function(error) {
                    if (error.status == 401) {
                        triggerErrorAlert('É necessário fazer login novamente');
                    }
                }
            }, 'animals.index');
        }

        function addAnimal() {
            const form = $('#add-animal-form');

            AjaxRequest({
                url: apiManagerURL + 'animals/',
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: form.serialize(),
                success: function(result) {
                    $('#animals-table').DataTable().destroy();
                    getAnimals();     
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
            }, 'animals.store');

            $('#add-animal').modal('hide');
        }

        function deleteAnimal(animalId) {
            AjaxRequest({
                url: apiManagerURL + 'animals/delete/' + animalId,
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('#animals-table').DataTable().destroy();
                    getAnimals();     
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
            }, 'animals.destroy');
        }

        function editAnimal(animalId) {
            AjaxRequest({
                url: apiManagerURL + 'animals/' + animalId,
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('input[name="name"]').val(result.name);
                    $('input[name="animal_type"]').val(result.animal_type);
                    $('select[name="client_id"]').val(result.client_id);

                    $('#submit-animal-form').addClass('edit-mode').data('target', result.id);

                    $('.modal-title').html('Editar Animal');
                    $('#add-animal').modal('show');
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
            }, 'animals.index');
        }

        function updateAnimal(animalId) {
            let formData = $('#add-animal-form').serializeArray(); 
            formData.push({name: 'id', value: animalId});

            AjaxRequest({
                url: apiManagerURL + 'animals/update/' + animalId,
                method: 'PUT',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: $.param(formData),
                success: function(result) {
                    $('#animals-table').DataTable().destroy();
                    getAnimals();    
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
            }, 'animals.update');

            $('#add-animal').modal('hide');
        }
    </script>
@endsection
