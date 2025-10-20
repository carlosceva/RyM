<script>
    function setAccionAndSolicitudId(accion, solicitudId, boton = null) {
        document.getElementById('accion').value = accion;
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

        if (boton) {
            const detalle = boton.getAttribute('data-detalle');
            const cliente = boton.getAttribute('data-cliente') || 'No asignado';
            const glosa = boton.getAttribute('data-glosa') || 'Sin glosa';

            // Mostrar cliente y glosa en el modal
            document.getElementById('clienteModal').value = cliente;
            document.getElementById('glosaModal').value = glosa;

            if (detalle) {
                cargarProductosParaEditar(detalle);
            }
        }
    }
</script>

<script>
function cargarProductosParaEditar(detalleString) {
    const productos = detalleString.split(',').map(item => {
        const [producto, cantidad, medida, precio] = item.split('-');
        return { producto, cantidad, medida, precio };
    });

    const tbody = document.querySelector("#tablaProductosEditar tbody");
    tbody.innerHTML = "";

    productos.forEach((item, index) => {
        const fila = `
        <tr>
            <td>${item.producto}</td>
            <td>${item.cantidad}</td>
            <td>${item.medida}</td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control precio-input" 
                       value="${item.precio}" data-index="${index}">
            </td>
        </tr>`;
        tbody.innerHTML += fila;
    });

    // Guarda en un dataset para usarlo al enviar
    document.getElementById('tablaProductosEditar').dataset.productos = JSON.stringify(productos);
}

function actualizarDetalleProductosEditado() {
    const precios = document.querySelectorAll('.precio-input');
    const productos = JSON.parse(document.getElementById('tablaProductosEditar').dataset.productos);

    precios.forEach(input => {
        const index = input.dataset.index;
        productos[index].precio = parseFloat(input.value).toFixed(2);
    });

    const detalleCadena = productos.map(p => `${p.producto}-${p.cantidad}-${p.medida}-${p.precio}`).join(",");
    document.getElementById('detalle_productos_editado').value = detalleCadena;
}

// Antes de enviar el formulario, actualiza el campo oculto
document.getElementById('formObservacion').addEventListener('submit', function (e) {
    actualizarDetalleProductosEditado();
});
</script>