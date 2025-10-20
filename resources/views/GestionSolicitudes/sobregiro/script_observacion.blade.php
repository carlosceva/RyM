<script>
    const form = document.querySelector('form');
    form.addEventListener('submit', function () {
        form.querySelector('button[type=submit]').disabled = true;
    });
</script>

<script>
    function setAccionAndSolicitudId(accion, solicitudId) {
        // Asigna la acción al campo oculto 'accion'
        document.getElementById('accion').value = accion;
        // Asigna la ID de la solicitud al campo oculto 'solicitud_id'
        document.getElementById('solicitud_id').value = solicitudId;

        const header = document.getElementById('observacionModalHeader');
        const title = document.getElementById('observacionModalLabel');

        // Limpiar clases anteriores
        header.classList.remove('bg-primary', 'bg-danger', 'text-white');

        if (accion === 'aprobar') {
            header.classList.add('bg-primary', 'text-white');
            title.textContent = 'Aprobar Solicitud';
        } else if (accion === 'rechazar') {
            header.classList.add('bg-danger', 'text-white');
            title.textContent = 'Rechazar Solicitud';
        } else {
            title.textContent = 'Agregar Observación';
        }
    }
</script>