{{-- resources/views/partials/datatables_estado_filter.blade.php --}}
<div id="dtEstadoFilterPopup" class="dt-estado-filter-popup d-none">
    <div class="dt-estado-filter-header-box">
        <strong>Filtrar por estado</strong>
        <button type="button" id="dtEstadoFilterClose" class="btn-close" aria-label="Cerrar"></button>
    </div>

    <div class="dt-estado-filter-body">
        <div id="dtEstadoFilterOptions" class="dt-estado-filter-options"></div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="button" id="dtEstadoFilterClear" class="btn btn-sm btn-outline-secondary">
                Limpiar
            </button>
            <button type="button" id="dtEstadoFilterApply" class="btn btn-sm btn-primary">
                Aplicar
            </button>
        </div>
    </div>
</div>

<style>
    .dt-estado-filter-popup {
        position: absolute;
        z-index: 3000;
        min-width: 230px;
        max-width: 280px;
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: .5rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .18);
        overflow: hidden;
    }

    .dt-estado-filter-header-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .65rem .8rem;
        background: #343a40;
        color: #ffffff;
        font-size: .9rem;
    }

    .dt-estado-filter-header-box .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
        opacity: .85;
    }

    .dt-estado-filter-body {
        padding: .8rem;
    }

    .dt-estado-filter-options {
        max-height: 210px;
        overflow-y: auto;
    }

    .dt-estado-filter-header {
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
    }

    .dt-estado-filter-icon {
        margin-left: .35rem;
        color: #ffc107;
        font-size: .85rem;
    }

    .dt-estado-filter-active .dt-estado-filter-icon {
        color: #00d084;
    }
</style>
