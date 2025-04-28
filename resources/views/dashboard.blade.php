
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
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a> 
             
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
      <li class="nav-item dropdown">
        <div class="dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">Perfil</a>
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
                <a href="{{route('usuario.index')}}" class="nav-link">
                  <i class="fa fa-users nav-icon"></i>
                  <p>Usuarios</p>
                </a>
              </li>
              @endcan
              @can('roles_ver')
              <li class="nav-item">
                <a href="{{route('roles.index')}}" class="nav-link">
                  <i class="fa fa-book nav-icon"></i>
                  <p>Roles</p>
                </a>
              </li>
              @endcan
              @can('permisos_ver')
              <li class="nav-item">
                <a href="{{ route('permisos.index') }}" class="nav-link">
                  <i class="fa fa-key nav-icon"></i>
                  <p>Permisos</p>
                </a>
              </li>
              @endcan
            </ul>
          </li>
          @endif
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
              <li class="nav-item">
                <a href="{{route('general.index')}}" class="nav-link">
                  <i class="fas fa-copy nav-icon"></i>
                  <p>Solicitudes en general</p>
                </a>
              </li>
              @endrole
              @can('Devolucion_ver')
              <li class="nav-item">
                <a href="{{route('Devolucion.index')}}" class="nav-link">
                  <i class="nav-icon far fa-file-alt"></i>
                  <p>Devolucion de venta</p>
                </a>
              </li>
              @endcan
              @can('Anulacion_ver')
              <li class="nav-item">
                <a href="{{route('Anulacion.index')}}" class="nav-link">
                  <i class="nav-icon far fa-times-circle"></i>
                  <p>Anulacion de venta</p>
                </a>
              </li>
              @endcan
              @can('Sobregiro_ver')
              <li class="nav-item">
                <a href="{{route('Sobregiro.index')}}" class="nav-link">
                  <i class="nav-icon far fa-arrow-alt-circle-up"></i>
                  <p>Sobregiro en ventas</p>
                </a>
              </li>
              @endcan
              @can('Precio_especial_ver')
              <li class="nav-item">
                <a href="{{route('PrecioEspecial.index')}}" class="nav-link">
                  <i class="nav-icon far fa-file-alt"></i>
                  <p>Precio especial</p>
                </a>
              </li>
              @endcan
              @can('Muestra_ver')
              <li class="nav-item">
                <a href="{{route('Muestra.index')}}" class="nav-link">
                  <i class="nav-icon far fa-file-alt"></i>
                  <p>Muestra de mercadería</p>
                </a>
              </li>
              @endcan
              @can('Baja_ver')
              <li class="nav-item">
                <a href="{{route('Baja.index')}}" class="nav-link">
                  <i class="nav-icon far fa-trash-alt"></i>
                  <p>Baja de mercadería</p>
                </a>
              </li>
              @endcan
            </ul>
          </li>

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
  $(document).ready(function() {
        $('#solicitud_baja').DataTable({
            "order": [[0, 'desc']], // Esta es la configuración que ordena por la primera columna (ID) de forma descendente
            "paging": true,         // Activar la paginación
            "searching": true,      // Habilitar la búsqueda
            "lengthChange": true    // Permite cambiar el número de elementos por página
        });
    });
    $(document).ready(function() {
        $('#solicitud_muestra').DataTable({
            "order": [[0, 'desc']], // Esta es la configuración que ordena por la primera columna (ID) de forma descendente
            "paging": true,         // Activar la paginación
            "searching": true,      // Habilitar la búsqueda
            "lengthChange": true    // Permite cambiar el número de elementos por página
        });
    });
    $(document).ready(function() {
        $('#solicitud_precio').DataTable({
            "order": [[0, 'desc']], // Esta es la configuración que ordena por la primera columna (ID) de forma descendente
            "paging": true,         // Activar la paginación
            "searching": true,      // Habilitar la búsqueda
            "lengthChange": true    // Permite cambiar el número de elementos por página
        });
    });
    $(document).ready(function() {
        $('#solicitud_sobregiro').DataTable({
            "order": [[0, 'desc']], // Esta es la configuración que ordena por la primera columna (ID) de forma descendente
            "paging": true,         // Activar la paginación
            "searching": true,      // Habilitar la búsqueda
            "lengthChange": true    // Permite cambiar el número de elementos por página
        });
    });
    $(document).ready(function() {
        $('#solicitud_anulacion').DataTable({
            "order": [[0, 'desc']], // Esta es la configuración que ordena por la primera columna (ID) de forma descendente
            "paging": true,         // Activar la paginación
            "searching": true,      // Habilitar la búsqueda
            "lengthChange": true    // Permite cambiar el número de elementos por página
        });
    });
</script>    
</body>
</html>

