document.addEventListener('DOMContentLoaded', function () {

    // Referencia al div del calendario
    var calendarEl = document.getElementById('calendar');

    // Helper para headers con token (ELIMINADO - Usamos fetchData)

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

        // Cargar eventos (Usando fetchData)
        events: async function (info, successCallback, failureCallback) {
            try {
                // fetchData retorna directamente el JSON parseado o un objeto de error
                const data = await fetchData(`${BASE_URL_API}/Agenda/getAgenda`);

                // FullCalendar espera un array, si la API retorna array directamente o envuelta
                // Según tu código anterior: successCallback(data)
                // Usualmente es data directamente si es un array de eventos
                if (Array.isArray(data)) {
                    successCallback(data);
                } else if (data && data.status && data.data) {
                    // Si viene envuelto en { status: true, data: [...] }
                    successCallback(data.data);
                } else {
                    // Si data es null o error
                    console.warn("No se pudieron cargar eventos o lista vacía");
                    successCallback([]);
                }
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
        // Usamos fetchData, que maneja automáticamente el Content-Type para FormData
        const response = await fetchData(`${BASE_URL_API}/Agenda/setEvento`, 'POST', formData);

        if (response && response.status) {
            $('#modalAgenda').modal('hide');
            swal("Agenda", response.msg, "success");
            calendar.refetchEvents();
        } else {
            swal("Error", response?.msg || "Error desconocido", "error");
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

                const response = await fetchData(`${BASE_URL_API}/Agenda/delEvento`, 'POST', formData);

                if (response && response.status) {
                    $('#modalAgenda').modal('hide');
                    swal("Eliminado!", response.msg, "success");
                    calendar.refetchEvents();
                } else {
                    swal("Atención!", response?.msg || "No se pudo eliminar", "error");
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

    const response = await fetchData(`${BASE_URL_API}/Agenda/setEvento`, 'POST', formData);

    if (!response || !response.status) {
        swal("Error", "No se pudo mover el evento", "error");
    }
}
