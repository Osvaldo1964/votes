const BASE_URL = "http://app-votes.com";
const BASE_URL_API = "http://api-votes.com";

document.addEventListener('DOMContentLoaded', function () {

    // Referencia al div del calendario
    var calendarEl = document.getElementById('calendar');

    // Helper para headers con token
    const getAuthHeaders = () => {
        return {
            'Authorization': `Bearer ${localStorage.getItem('userToken')}`
            // No ponemos content-type aquí si vamos a enviar FormData, fetch lo pone solo
        };
    };

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es', // Español
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Agenda'
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: true,
        selectable: true,

        // Cargar eventos (Función para incluir Token)
        events: async function (info, successCallback, failureCallback) {
            try {
                const response = await fetch(`${BASE_URL_API}/Agenda/getAgenda`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('userToken')}`,
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                successCallback(data);
            } catch (error) {
                console.error("Error cargando eventos", error);
                failureCallback(error);
            }
        },

        // Al hacer clic en una fecha vacía (Nuevo Evento)
        select: function (info) {
            limpiarModal();
            let startStr = moment(info.start).format('YYYY-MM-DDTHH:mm');
            let endStr = moment(info.end).format('YYYY-MM-DDTHH:mm');
            document.querySelector('#start').value = startStr;
            document.querySelector('#end').value = endStr;
            $('#modalAgenda').modal('show');
        },

        // Al hacer clic en un evento existente (Editar)
        eventClick: function (info) {
            let evento = info.event;
            document.querySelector('#titleModal').innerHTML = "Actualizar Evento";
            document.querySelector('#btnText').innerHTML = "Actualizar";
            document.querySelector('#id').value = evento.id;
            document.querySelector('#title').value = evento.title;
            document.querySelector('#description').value = evento.extendedProps.description || '';
            document.querySelector('#color').value = evento.backgroundColor;

            document.querySelector('#start').value = moment(evento.start).format('YYYY-MM-DDTHH:mm');
            if (evento.end) {
                document.querySelector('#end').value = moment(evento.end).format('YYYY-MM-DDTHH:mm');
            }

            document.querySelector('#btnEliminar').style.display = "inline-block";
            $('#modalAgenda').modal('show');
        },

        // Al arrastrar y soltar / Redimensionar
        eventDrop: async function (info) { await actualizarFechaEvento(info.event); },
        eventResize: async function (info) { await actualizarFechaEvento(info.event); }
    });

    calendar.render();

    // Guardar Evento
    let formAgenda = document.querySelector("#formAgenda");
    formAgenda.onsubmit = async function (e) {
        e.preventDefault();
        let strTitle = document.querySelector('#title').value;
        if (strTitle == '') {
            swal("Atención", "El título es obligatorio", "warning");
            return false;
        }

        let formData = new FormData(formAgenda);

        try {
            let request = await fetch(BASE_URL_API + '/Agenda/setEvento', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('userToken')}`
                },
                body: formData
            });
            let response = await request.json();

            if (response.status) {
                $('#modalAgenda').modal('hide');
                swal("Agenda", response.msg, "success");
                calendar.refetchEvents();
            } else {
                swal("Error", response.msg || "Error desconocido", "error");
            }
        } catch (error) {
            console.error(error);
            swal("Error", "Error de conexión", "error");
        }
    }

    // Botón Eliminar
    document.querySelector("#btnEliminar").onclick = function () {
        let idEvento = document.querySelector("#id").value;
        swal({
            title: "Eliminar Evento",
            text: "¿Realmente quiere eliminar este evento?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, eliminar!",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false,
            closeOnCancel: true
        }, async function (isConfirm) {
            if (isConfirm) {
                let formData = new FormData();
                formData.append('id', idEvento);

                try {
                    let request = await fetch(BASE_URL_API + '/Agenda/delEvento', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('userToken')}`
                        },
                        body: formData
                    });
                    let response = await request.json();
                    if (response.status) {
                        $('#modalAgenda').modal('hide');
                        swal("Eliminado!", response.msg, "success");
                        calendar.refetchEvents();
                    } else {
                        swal("Atención!", response.msg, "error");
                    }
                } catch (error) {
                    swal("Error", "Error de conexión", "error");
                }
            }
        });
    }

});

function limpiarModal() {
    document.querySelector('#id').value = "";
    document.querySelector('#title').value = "";
    document.querySelector('#description').value = "";
    document.querySelector('#color').value = "#3788d8";
    document.querySelector('#titleModal').innerHTML = "Nuevo Evento";
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#btnEliminar').style.display = "none";
    document.querySelector('#formAgenda').reset();
}

async function actualizarFechaEvento(evento) {
    let formData = new FormData();
    formData.append('id', evento.id);
    formData.append('title', evento.title);
    formData.append('description', evento.extendedProps.description || '');
    formData.append('color', evento.backgroundColor);
    formData.append('start', moment(evento.start).format('YYYY-MM-DDTHH:mm'));
    if (evento.end) formData.append('end', moment(evento.end).format('YYYY-MM-DDTHH:mm'));

    try {
        let request = await fetch(BASE_URL_API + '/Agenda/setEvento', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('userToken')}`
            },
            body: formData
        });
        let response = await request.json();
        if (!response.status) {
            swal("Error", "No se pudo mover el evento", "error");
        }
    } catch (e) {
        console.error(e);
    }
}
