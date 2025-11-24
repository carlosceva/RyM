
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Dashboard')</title>
  <link rel="shortcut icon" href="{{ asset('dist/img/rym.ico') }}" />
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
   <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}"> 
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  @livewireStyles
  <!-- DataTables y Buttons -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ asset('dist/img/rym.jpg') }}" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ route('dashboard')}}" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline" action="" method="POST">
            <div class="input-group input-group-sm">
              @csrf
              <input class="form-control form-control-navbar" type="search" name="buscar" placeholder="Search" aria-label="Search">
              
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit" >
                  <i class="fas fa-search"></i>
                </button>
                
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <!-- Notifications Dropdown Menu -->

        @include('partials.navbar')

      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <div class="dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('profile.show') }}">Perfil</a>
                <a class="dropdown-item" href="{{ route('logout') }}"
                  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
            </div>
        </div>
    
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('dashboard')}}" class="brand-link">
      <img src="{{asset('img/rym.jpg')}}" alt="NET CROW Logo" class="brand-image">
      <span class="brand-text"><b>R</b> y <b>M</b></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          @if (auth()->user()->can('usuarios_ver') || auth()->user()->can('roles_ver')|| auth()->user()->can('permisos_ver'))         
          <li class="nav-item menu-is-opening menu-open">  
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-cog" style="color: orange"></i>
              <p style="color: orange">
                Administracion
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('usuarios_ver')
              <li class="nav-item">
                <a href="{{route('usuario.index')}}" class="nav-link {{ request()->routeIs('usuario.index') ? 'active' : '' }}">
                  <i class="fa fa-users nav-icon"></i>
                  <p>Usuarios</p>
                </a>
              </li>
              @endcan
              @can('roles_ver')
              <li class="nav-item">
                <a href="{{route('roles.index')}}" class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}">
                  <i class="fa fa-book nav-icon"></i>
                  <p>Roles</p>
                </a>
              </li>
              @endcan
              @can('permisos_ver')
              <li class="nav-item">
                <a href="{{ route('permisos.index') }}" class="nav-link {{ request()->routeIs('permisos.index') ? 'active' : '' }}">
                  <i class="fa fa-key nav-icon"></i>
                  <p>Permisos</p>
                </a>
              </li>
              @endcan
              @can('usuarios_ver')
              <li class="nav-item">
                <a href="{{ route('almacen.index') }}" class="nav-link {{ request()->routeIs('almacen.index') ? 'active' : '' }}">
                  <i class="fas fa-file nav-icon"></i>
                  <p>Almacenes</p>
                </a>
              </li>
              @endcan
              @can('sistema_ver')
              <!-- <li class="nav-item">
                <a href="{{ route('backup.index') }}" class="nav-link {{ request()->routeIs('backup.index') ? 'active' : '' }}">
                  <i class="fa fa-hdd nav-icon"></i>
                  <p>Backup y Restore</p>
                </a>
              </li> -->
              <li class="nav-item">
                <a href="{{ route('configuracion.index') }}" class="nav-link {{ request()->routeIs('configuracion.index') ? 'active' : '' }}">
                  <i class="fa fa-cog nav-icon"></i>
                  <p>Configuraciones</p>
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endif
          @if (auth()->user()->can('Precio_especial_ver') || auth()->user()->can('Muestra_ver') || auth()->user()->can('Devolucion_ver') || auth()->user()->can('Anulacion_ver') || auth()->user()->can('Sobregiro_ver') || auth()->user()->can('Baja_ver') || auth()->user()->can('Cambio_ver') || auth()->user()->can('Vacaciones_ver') || auth()->user()->can('Extra_ver')) 
          <li class="nav-item menu-is-opening menu-open">  
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-copy" style="color: orange"></i>
              <p style="color: orange">
                Gestion de solicitudes
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @role('Administrador')
              <!--
              <li class="nav-item">
                <a href="{{route('general.index')}}" class="nav-link">
                  <i class="fas fa-copy nav-icon"></i>
                  <p>Solicitudes en general</p>
                </a>
              </li>
              -->
              @endrole
              @can('Devolucion_ver')
              <li class="nav-item">
                <a href="{{route('Devolucion.index')}}" class="nav-link {{ request()->routeIs('Devolucion.index') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-undo"></i>
                  <p>Devolucion de venta</p>
                </a>
              </li>
              @endcan
              @can('Anulacion_ver')
              <li class="nav-item">
                <a href="{{route('Anulacion.index')}}" class="nav-link {{ request()->routeIs('Anulacion.index') ? 'active' : '' }}">
                  <i class="nav-icon far fa-times-circle"></i>
                  <p>Anulacion de venta</p>
                </a>
              </li>
              @endcan
              @can('Sobregiro_ver')
              <li class="nav-item">
                <a href="{{route('Sobregiro.index')}}" class="nav-link {{ request()->routeIs('Sobregiro.index') ? 'active' : '' }}">
                  <i class="nav-icon far fa-arrow-alt-circle-up"></i>
                  <p>Sobregiro en ventas</p>
                </a>
              </li>
              @endcan
              @can('Precio_especial_ver')
              <li class="nav-item">
                <a href="{{route('PrecioEspecial.index')}}" class="nav-link {{ request()->routeIs('PrecioEspecial.index') ? 'active' : '' }}">
                  <i class="nav-icon far fa-file-alt"></i>
                  <p>Precio especial</p>
                </a>
              </li>
              @endcan
              @can('Muestra_ver')
              <li class="nav-item">
                <a href="{{route('Muestra.index')}}" class="nav-link {{ request()->routeIs('Muestra.index') ? 'active' : '' }}">
                  <i class="nav-icon fa fa-archive"></i>
                  <p>Muestra de mercadería</p>
                </a>
              </li>
              @endcan
              @can('Baja_ver')
              <li class="nav-item">
                <a href="{{route('Baja.index')}}" class="nav-link {{ request()->routeIs('Baja.index') ? 'active' : '' }}">
                  <i class="nav-icon far fa-trash-alt"></i>
                  <p>Ajuste de inventario</p>
                </a>
              </li>
              @endcan
              @can('Cambio_ver')
              <li class="nav-item">
                <a href="{{route('CambiosFisicos.index')}}" class="nav-link {{ request()->routeIs('CambiosFisicos.index') ? 'active' : '' }}">
                  <i class="nav-icon fa fa-box"></i>
                  <p>Cambios fisicos</p>
                </a>
              </li>
              @endcan
              @can('Vacaciones_ver')
              <li class="nav-item">
                <a href="{{route('Vacaciones.index')}}" class="nav-link {{ request()->routeIs('Vacaciones.index') ? 'active' : '' }}">
                  <i class="nav-icon fa fa-plane"></i>
                  <p>Permisos/vacaciones</p>
                </a>
              </li>
              @endcan
              @can('Extra_ver')
              <li class="nav-item">
                <a href="{{route('Extras.index')}}" class="nav-link {{ request()->routeIs('Extras.index') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-utensils"></i>
                  <p>Extras</p>
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endif
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    
  </br>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
       
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          
            <!-- Custom tabs (Charts with tabs)-->
            
              
            @yield('content')

            
            <!-- /.card -->

          
          
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2025 <a href="https://adminlte.io">Netcrow</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        @if(isset($num))
          Visitas:
          <strong> {{$num}} </strong>
        @else
        <br>
        @endif
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
 <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{asset('plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{asset('plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('dist/js/demo.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('dist/js/pages/dashboard.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

<script src="{{asset('dist/js/pages/dashboard.js')}}"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
<script>
  $('#usuario').DataTable();
  $('#roles').DataTable();
  $('#clientes').DataTable();
  $('#solicitud').DataTable();

$(document).ready(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const estadoFilter = urlParams.get('estado') || '';
  const filtroId = urlParams.get('filtro_id') || '';

  // Filtro por fecha (columna 2 asumida como fecha)
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    let min = '';
    let max = '';

    // Verifica qué input de fecha está visible
    if ($('#fechaInicio').is(':visible') && $('#fechaFin').is(':visible')) {
      // PC
      min = $('#fechaInicio').val();
      max = $('#fechaFin').val();
    } else if ($('#fechaInicioMobile').is(':visible') && $('#fechaFinMobile').is(':visible')) {
      // Móvil
      min = $('#fechaInicioMobile').val();
      max = $('#fechaFinMobile').val();
    }

    const fecha = data[2]; // Suponiendo que la fecha está en la columna 2

    if (!fecha) return true;

    const fechaData = new Date(fecha);
    if (min && new Date(min) > fechaData) return false;
    if (max && new Date(max) < fechaData) return false;

    return true;
  });

  // DOM con filtros y botones en una fila
  const customDom =
    "<'row align-items-center mb-2'<'col-12 col-md-8 d-flex flex-wrap align-items-center gap-2 dt-custom-toolbar'B><'col-12 col-md-4 text-end'f>>" +
    "<'row'<'col-sm-12'tr>>" +
    "<'row'<'col-sm-5'i><'col-sm-7'p>>";

  const filtrosFechas = `
    <!-- Inputs de fecha solo visibles en móvil -->
    <div class="d-flex d-md-none w-100 mb-2">
      <div class="flex-fill me-2">
        <label for="fechaInicioMobile" class="small mb-1">Fecha desde</label>
        <input type="date" id="fechaInicioMobile" class="form-control form-control-sm">
      </div>
      <div class="flex-fill">
        <label for="fechaFinMobile" class="small mb-1">Fecha hasta</label>
        <input type="date" id="fechaFinMobile" class="form-control form-control-sm">
      </div>
    </div>

    <!-- Inputs de fecha solo visibles en escritorio -->
    <div class="d-none d-md-flex align-items-center gap-2 me-3">
      <input type="date" id="fechaInicio" class="form-control form-control-sm me-2">
      <input type="date" id="fechaFin" class="form-control form-control-sm">
    </div>
  `;

  function initDataTable(tableId, exportPrefix, pdfTitle, exportCols) {
    const table = $(tableId).DataTable({
      dom: customDom,
      buttons: [
        {
          extend: 'excelHtml5',
          className: 'btn btn-success',
          text: '<i class="fas fa-file-excel"></i> Excel',
          title: null,
          filename: function () {
            const f1 = $('#fechaInicio').val()?.replaceAll('-', '_') || 'start';
            const f2 = $('#fechaFin').val()?.replaceAll('-', '_') || 'end';
            return `${exportPrefix}_${f1}-${f2}`;
          },
          exportOptions: {
            columns: exportCols
          }
        },
        {
          extend: 'pdfHtml5',
          className: 'btn btn-danger',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          title: pdfTitle,
          filename: function () {
            const f1 = $('#fechaInicio').val()?.replaceAll('-', '_') || 'start';
            const f2 = $('#fechaFin').val()?.replaceAll('-', '_') || 'end';
            return `${exportPrefix}_${f1}-${f2}`;
          },
          orientation: 'landscape',
          pageSize: 'A4',
          exportOptions: {
            columns: exportCols
          },
          customize: function (doc) {
            doc.styles.tableHeader.alignment = 'left';
            doc.defaultStyle.fontSize = 8;
          }
        },
        {
          extend: 'print',
          className: 'btn btn-primary',
          text: '<i class="fas fa-print"></i> Imprimir',
          exportOptions: {
            columns: exportCols
          },
          customize: function (win) {
            const css = '@page { size: landscape; }';
            const head = win.document.head || win.document.getElementsByTagName('head')[0];
            const style = win.document.createElement('style');
            style.type = 'text/css';
            style.media = 'print';

            if (style.styleSheet) {
              style.styleSheet.cssText = css;
            } else {
              style.appendChild(win.document.createTextNode(css));
            }
            head.appendChild(style);
          }
        }
      ],
      order: [[0, 'desc']],
      paging: true,
      searching: true,
      lengthChange: true,
      initComplete: function () {
        $('.dt-custom-toolbar').prepend(filtrosFechas);
        if (estadoFilter !== '') {
          this.api().search(estadoFilter).draw();
        }
        const filtroId = urlParams.get('filtro_id');
        if (filtroId) {
          this.api().column(0).search(filtroId).draw();
        }
      }
    });

    $('#fechaInicio, #fechaFin, #fechaInicioMobile, #fechaFinMobile').on('change', function () {
      table.draw();
    });

    return table;
  }

  // Inicializar todas las tablas con su configuración
  initDataTable('#solicitud_baja', 'Baja', 'Solicitud de Baja de Mercaderia', [0,1,2,3,4,5,6,7,8,9,10,11,12]);
  initDataTable('#solicitud_muestra', 'Muestra', 'Solicitud de Muestra de Mercaderia', [0,1,2,3,4,5,6,7,8,9,10,11,12,13]);
  initDataTable('#solicitud_sobregiro', 'Sobregiro', 'Solicitud de Sobregiro de Venta', [0,1,2,3,4,5,6,7,8,9,10,11,12]);
  initDataTable('#solicitud_devolucion', 'Devolucion', 'Solicitud de Devolucion de Venta', [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16]);
  initDataTable('#solicitud_anulacion', 'Anulacion', 'Solicitud de Anulacion de Venta', [0,1,2,3,4,5,6,7,8,9,10,11,12]);
  initDataTable('#solicitud_precio', 'Precio_Especial', 'Solicitud de Precio Especial de Venta', [0,1,2,3,4,5,6,7,8,9,10]);
  initDataTable('#solicitud_cambio', 'Cambio', 'Solicitud de Cambio Fisico de Mercaderia', [0,1,2,3,4,5,6,7,8,9,10,11,12,13]);
  initDataTable('#solicitud_extra', 'Extra', 'Solicitudes Extras', [0,1,2,3,4,5,6,7,8,9,10,11]);
  initDataTable('#solicitud_vacacion', 'Vacacion', 'Solicitud de Permisos/Vacaciones', [0,1,2,3,4,5,6,7,8,9,10,11,12]);
});
</script>

@stack('scripts')

@livewireScripts
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

<!-- Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

</body>
</html>

