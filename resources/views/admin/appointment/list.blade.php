@extends('template.admin-template')

@section('styles')
    @parent
@endsection

@section('page-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Agendamentos</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Resultados</h6>
        <button class="btn btn-primary btn-icon-split"  data-toggle="modal" data-target="#add-appointment">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Novo Agendamento</span>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="appointments-table" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Animal</th>
                        <th>Descrição</th>
                        <th>Data Inicial</th>
                        <th>Data Final</th>
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
<div class="modal fade" id="add-appointment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Novo Agendamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-appointment-form">
                    <div class="form-group">
                        <label for="client_id">Cliente <small>*</small></label>
                        <select name="client_id" class="form-control form-control-user get-animal" required>
                            <option value="" disabled selected>Selecione um cliente</option>
                            <!-- Aqui será preenchido com os clientes via JavaScript -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="animal_id">Animal <small>*</small></label>
                        <select name="animal_id" class="form-control form-control-user animal-select" required>
                            <option value="" disabled selected>Selecione um animal</option>
                            <!-- Aqui será preenchido com os animais via JavaScript -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição <small></small></label>
                        <input type="text" name="description" class="form-control form-control-user">
                    </div>
                    <div class="form-group">
                        <label for="initial_date">Data Inicial <small>*</small></label>
                        <input type="datetime-local" name="initial_date" class="form-control form-control-user" required>
                    </div>
                    <div class="form-group">
                        <label for="final_date">Data Final <small>*</small></label>
                        <input type="datetime-local" name="final_date" class="form-control form-control-user" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button id="submit-appointment-form" type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    
    <script>
        $(document).ready(function() {
            getAppointments();
            getClientsForSelect();
        });

        $('.get-animal').on('change', function (event){
            $(".animal-select").html('<option value="" disabled selected>Selecione um animal</option>');
            const client = $(".get-animal option:selected").val();
            getAnimalsForSelect(client);
        });

        $('#submit-appointment-form').on('click', function(event) {
            event.preventDefault();

            if ($(this).hasClass('edit-mode')) {
                updateAppointment($(this).data('target'));
            } else {
                addAppointment();
            }
        });

        $('#add-appointment').on('hidden.bs.modal', function (e) {
            $('.modal-title').html('Novo Agendamento');
            $('#add-appointment-form')[0].reset();
            $('#submit-appointment-form').removeClass('edit-mode').removeData('target');
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

        function getAnimalsForSelect(clientId) {
            const animalSelect = $('select[name="animal_id"]');

            AjaxRequest({
                url: apiManagerURL + 'client/' + clientId + '/animals',
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken
                },
                success: function (result) {
                    console.log(result)
                    const options = result.data.reduce(function (finalString, item) {
                        return finalString + '<option value="' + item.id + '">' + item.name + '</option>';
                    }, '');

                    animalSelect.append(options);
                },
                error: function (error) {
                    console.log('Erro ao buscar animais:', error);
                }
            }, 'animals.index');
        }

        function getAppointments() {
            const appointmentTableBody = $('#appointments-table tbody');

            AjaxRequest({
                url: apiManagerURL + 'appointment',
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
                                '<td>' +  item.client.name + '</td>' +
                                '<td>' + item.animal.name + '</td>' +
                                '<td>' + item.description + '</td>' +
                                '<td>' + item.initial_date + '</td>' +
                                '<td>' + item.final_date  + '</td>' +
                                '<td style="width:1%" class="text-nowrap">' +
                                    '<a class="action-buttons edit-appointment" data-target="' + item.id + '">' + 
                                        '<i style="margin-right: 1rem" class="fas fa-pen"></i>' + 
                                    '</a>'  +
                                    '<a class="action-buttons delete-appointment" data-target="' + item.id + '">' + 
                                        '<i class="fas fa-trash" aria-hidden="true"></i>' +
                                    '</a>'  +
                                '</td>' +
                            '</tr>';
                    }, '');

                    appointmentTableBody.html(tableContent);
                    $('#appointments-table').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
                        },
                    });

                    $('.edit-appointment').on('click', function(event) {
                        event.preventDefault();
                        const appointmentId = $(this).data('target');
                        editAppointment(appointmentId);
                    });

                    $('.delete-appointment').on('click', function(event) {
                        event.preventDefault();
                        const appointmentId = $(this).data('target');
                        deleteAppointment(appointmentId);
                    });

                },
                error: function(error) {
                    if (error.status == 401) {
                        triggerErrorAlert('É necessário fazer login novamente');
                    }
                }
            }, 'appointment.index');
        }

        function addAppointment() {
            const form = $('#add-appointment-form');

            AjaxRequest({
                url: apiManagerURL + 'appointment/',
                method: 'POST',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: form.serialize(),
                success: function(result) {
                    $('#appointments-table').DataTable().destroy();
                    getAppointments();     
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
            }, 'appointment.store');

            $('#add-appointment').modal('hide');
        }

        function deleteAppointment(appointmentId) {
            AjaxRequest({
                url: apiManagerURL + 'appointment/delete/' + appointmentId,
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('#appointments-table').DataTable().destroy();
                    getAppointments();     
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
            }, 'appointment.destroy');
        }

        function editAppointment(appointmentId) {
            AjaxRequest({
                url: apiManagerURL + 'appointment/' + appointmentId,
                method: 'GET',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                success: function(result) {
                    $('select[name="client_id"]').val(result.client_id);
                    $('select[name="animal_id"]').val(result.animal_id);
                    $('select[name="description"]').val(result.description);
                    $('input[name="initial_date"]').val(result.initial_date);
                    $('input[name="final_date"]').val(result.final_date);

                    $('#submit-appointment-form').addClass('edit-mode').data('target', result.id);

                    $('.modal-title').html('Editar Agendamento');
                    $('#add-appointment').modal('show');
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
            }, 'appointment.index');
        }

        function updateAppointment(appointmentId) {
            let formData = $('#add-appointment-form').serializeArray(); 
            formData.push({name: 'id', value: appointmentId});

            AjaxRequest({
                url: apiManagerURL + 'appointment/update/' + appointmentId,
                method: 'PUT',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + accessToken 
                },
                data: $.param(formData),
                success: function(result) {
                    $('#appointments-table').DataTable().destroy();
                    getAppointments();    
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
            }, 'appointment.update');

            $('#add-appointment').modal('hide');
        }
    </script>
@endsection
