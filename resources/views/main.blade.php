<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>{{$tittle}} @if(isset($subtit)) - {{$subtit}} @endif</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="{{url('img/logo.png')}}" rel="icon">
  <link href="{{url('img/logo.png')}}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{url('vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{url('vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{url('vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
  <link href="{{url('vendor/quill/quill.snow.css')}}" rel="stylesheet">
  <link href="{{url('vendor/quill/quill.bubble.css')}}" rel="stylesheet">
  <link href="{{url('vendor/remixicon/remixicon.css')}}" rel="stylesheet">
  <link href="{{url('vendor/simple-datatables/style.css')}}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{url('css/style.css')}}" rel="stylesheet">
  <link href="{{url('css/gmustyles.css')}}" rel="stylesheet">


    <!--  -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <!-- SELECT2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- MULTISELECT -->
    <link rel="stylesheet" href="https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.css">
    <script src="{{ url('js/multiple-select.min.js')}}"></script>
    <!-- SWAL -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- MASK -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
    <!-- MORRIS -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <!-- BUTTONS DATATABLE  -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.13.2/b-2.3.4/b-colvis-2.3.4/b-html5-2.3.4/b-print-2.3.4/datatables.min.css"/>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.13.2/b-2.3.4/b-colvis-2.3.4/b-html5-2.3.4/b-print-2.3.4/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.13.4/api/sum().js"></script>


    {{-- BACKGROUNDS ANIMADOS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/ScrollTrigger.min.js"></script>

    <!--  -->
    <!--  -->
    <script src="{{ url('js/funciones.js')}}"></script>
    <!--  -->

</head>

<body class="toggle-sidebar">
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
    <i class="bi bi-list toggle-sidebar-btn"></i>
    &nbsp;&nbsp;
      <a href="{{ route('home') }}" class="logo d-flex align-items-center">
        <img src="{{url('img/logo.png')}}" alt="">
        <img src="{{url('img/text-logo.png')}}" alt="">
        {{-- <span class="d-none d-lg-block">NiceAdmin</span> --}}
      </a>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">


      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('home') }}" id="dashINICIO">
          <i class="ri-home-2-line"></i>
          <span>INICIO</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('productos') }}" id="dashPRODUCTOS">
          <i class="ri-shopping-basket-line"></i>
          <span>PRODUCTOS</span>
        </a>
      </li>

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
        <section class="wrapper">
            <div class="top">{{$tittle}} @if(isset($subtit)) - {{$subtit}} @endif</div>
            <div class="bottom" aria-hidden="true">{{$tittle}} @if(isset($subtit)) - {{$subtit}} @endif</div>
        </section>
      </div>

      <section class="section">
        <div class="row">
          <div class="col-lg-12">

            <div class="card">
              <div class="card-body">

                <!-- Table with stripped rows -->
                @yield('contenido')
                <!-- End Table with stripped rows -->

              </div>
            </div>
              
          </div>
        </div>
      </section>

  </main><!-- End #main -->


  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{url('vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{url('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{url('vendor/chart.js/chart.min.js')}}"></script>
  <script src="{{url('vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{url('vendor/quill/quill.min.js')}}"></script>
  {{-- <script src="{{url('vendor/simple-datatables/simple-datatables.js')}}"></script> --}}
  <script src="{{url('vendor/tinymce/tinymce.min.js')}}"></script>
  <script src="{{url('vendor/php-email-form/validate.js')}}"></script>

  <!-- Template Main JS File -->
  <script src="{{url('js/main.js')}}"></script>

  <script type="text/javascript">
    var tittle = '{{$tittle}}';

    $('#dash'+tittle).removeClass("collapsed");
  </script>
</body>

</html>
