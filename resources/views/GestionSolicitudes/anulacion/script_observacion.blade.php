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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tienePagoSi = document.getElementById('tiene_pago_si');
            const tienePagoNo = document.getElementById('tiene_pago_no');
            const obsPagoGroup = document.getElementById('obs_pago_group');

            function toggleObsPago() {
                if (tienePagoSi.checked) {
                    obsPagoGroup.classList.remove('d-none');
                    document.getElementById('obs_pago').required = true;
                } else {
                    obsPagoGroup.classList.add('d-none');
                    document.getElementById('obs_pago').required = false;
                    document.getElementById('obs_pago').value = '';
                }
            }

            tienePagoSi.addEventListener('change', toggleObsPago);
            tienePagoNo.addEventListener('change', toggleObsPago);
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.entrega-radio');

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                const solicitudId = this.dataset.solicitudId;
                const mensaje = document.getElementById('mensajeEntregaF' + solicitudId);

                if (this.value === "1") {
                    mensaje?.classList.remove('d-none');
                } else {
                    mensaje?.classList.add('d-none');
                }
            });
        });
    });
    </script>