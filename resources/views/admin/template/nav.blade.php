{{-- @extends('layouts.app') --}}
<link rel="stylesheet" href="{{ URL::asset('css/login.css') }}">
<link rel="stylesheet" href="{{ URL::asset('css/app.css?v=2026') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="wrapper" style="display: flex; align-items: stretch;">
    <nav id="sidebar" class="custom-sidebar">
        <div class="sidebar-header">
            <p class="centrado-navbar">
                <a href="{{ route('home') }}">
                    <img src="{{ URL::asset('img/logo.png') }}" alt="logo" class="logo-modern">
                </a>
            </p>
        </div>

        <ul class="list-unstyled components" id="accordion">
            @foreach ($secciones as $seccion)
                <li class="nav-item-custom" data-toggle="tooltip" data-placement="right" title="{{$seccion->description}}">
                    <a href="#multi_menu{{$seccion->id}}" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle-custom">
                        <i class="{{$seccion->icon}} icon-fixed"></i>
                        <span class="nav-text">{{$seccion->section}}</span>
                    </a>
                    
                    <ul class="collapse list-unstyled submenu-custom" id="multi_menu{{$seccion->id}}" data-parent="#accordion">
                        @if(count($subsections->where('padre_id',$seccion->id))>0)
                            @foreach ($subsections as $sub)
                                @if ($seccion->id == $sub->padre_id)
                                    <li>
                                        <a href="#multi_menu_sub{{$sub->subsection}}" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle-custom sub-item">
                                            <span>{{$sub->subsection}}</span>
                                        </a>
                                        <ul class="collapse list-unstyled sub-submenu-custom" id="multi_menu_sub{{$sub->subsection}}">
                                            @foreach ($user_permissions as $module)
                                                @if ($module->reference == $sub->sub_section_id )
                                                    <li>
                                                        <a class="permisions_id" href="{{ $module->url ? route($module->url) : '#' }}">
                                                            <i class="fas fa-chevron-right small-icon"></i> {{$module->module}}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        @else
                            @foreach ($user_permissions as $module)
                                @if ($module->reference == $seccion->id )
                                    <li>
                                        <a class="permisions_id" href="{{ $module->url ? route($module->url) : '#' }}">
                                            <i class="fas fa-chevron-right small-icon"></i> {{$module->module}}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </li>
            @endforeach
        </ul>
    </nav>

    <div id="content">
        <div id="app" style="width: 100%">
            <nav class="navbar navbar-expand-lg navbar-light modern-topbar py-2 px-3">
                <div class="container-fluid d-flex justify-content-between align-items-center flex-nowrap p-0">
                    
                    {{-- BOTÓN MENÚ HAMBURGUESA --}}
                    <button type="button" id="sidebarCollapse" class="btn-toggle-sidebar flex-shrink-0">
                        <i class="fas fa-bars"></i>
                    </button>

                    {{-- CONTENEDOR DERECHO: NOTIFICACIONES + PERFIL --}}
                    <div class="d-flex align-items-center gap-2 flex-nowrap ml-auto">
                        
                        {{-- 🔔 CENTRO DE NOTIFICACIONES --}}
                        <div class="dropdown position-relative mr-2">
                            <a id="notifDropdown" class="nav-link position-relative p-2 text-secondary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="outline: none;">
                                <i class="fas fa-bell fa-lg" style="font-size: 1.1rem;"></i>
                                <span id="notif-badge" class="badge badge-danger rounded-circle position-absolute shadow-sm" style="top: 2px; right: 2px; font-size: 0.6rem; padding: 2px 5px; display: none; animation: pulse 2s infinite;">0</span>
                            </a>

                            {{-- Menú Desplegable de Notificaciones --}}
                            <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 p-0 mt-2" aria-labelledby="notifDropdown" style="width: 340px; border-radius: 12px; overflow: hidden; right: 0; left: auto;">
                                
                                <div class="dropdown-header d-flex justify-content-between align-items-center bg-white p-3 border-bottom shadow-sm" style="z-index: 10;">
                                    <span class="font-weight-bold text-dark" style="font-size: 0.95rem;">Notificaciones</span>
                                    <a href="javascript:void(0)" onclick="marcarTodasNotificacionesLeidas()" class="small font-weight-bold text-primary" style="text-decoration: none;" title="Marcar todas como leídas">
                                        <i class="fas fa-check mr-1"></i> Leer todas
                                    </a>
                                </div>

                                {{-- Lista dinámica donde se inyectan las notificaciones --}}
                                <div id="notif-list-container" style="max-height: 400px; overflow-y: auto; position: relative;">
                                    <div class="text-center py-4 text-muted small">Cargando notificaciones...</div>
                                </div>
                            </div>
                        </div>

                        {{-- ÁREA DE PERFIL --}}
                        <div class="user-dropdown-area">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle profile-link d-flex align-items-center p-1" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                {{-- 🟢 OCULTO EN MÓVIL, VISIBLE EN PANTALLAS MEDIANAS/GRANDES --}}
                                <span class="user-name-text d-none d-md-inline ml-2">{{ Auth::user()->name }}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right shadow-lg profile-dropdown" aria-labelledby="navbarDropdown">
                                <div class="dropdown-header">Mi Cuenta</div>
                                <a class="dropdown-item logout-red" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Cerrar Sesión') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </nav>
        </div>

        <main class="main-container">
                @yield('content')
        </main>
    </div>
</div>


<!-- jQuery CDN - Slim version (=without AJAX) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<!-- Popper.JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

<script type="text/javascript">
    $(document).ready(function () {
        // Al presionar el botón de la hamburguesa
        $('#sidebarCollapse').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('#sidebar, #content, .wrapper').toggleClass('active');
            $('.#sidebar, .wrapper').toggleClass('active-mobile');
        });

        // Al presionar CUALQUIER COSA que no sea el sidebar, si está abierto, lo cerramos
        $(document).on('click', function (e) {
            // Si el clic NO fue dentro del sidebar Y el sidebar está abierto (tiene la clase active)
            if (!$(e.target).closest('#sidebar').length && $('#sidebar').hasClass('active')) {
                if ($(window).width() <= 768) {
                    $('#sidebar, #content, .wrapper').removeClass('active active-mobile');
                    $('.wrapper').removeClass('active-mobile');
                }
            }
        });
        
        // No detenemos la propagación en el sidebar para que los menús funcionen
    });
</script>
<style>
    /* ========================================================= */
    /* 📱 AJUSTES RESPONSIVOS PARA TOPBAR EN MÓVILES             */
    /* ========================================================= */

    .modern-topbar {
        background: #ffffff !important;
        border-radius: 15px !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03) !important;
        margin: 10px 15px !important;
    }

    .profile-link {
        text-decoration: none !important;
        color: #334155 !important;
        font-weight: 600;
    }

    /* Reducir el tamaño del desplegable de notificaciones en celulares para evitar desbordes */
    @media (max-width: 576px) {
        .modern-topbar {
            margin: 8px 10px !important;
            padding: 6px 12px !important;
        }

        #notifDropdown + .dropdown-menu {
            width: 280px !important;
            position: absolute !important;
            right: -50px !important; /* Centra el desplegable para que no se salga de la pantalla */
        }
    }
    /* ========================================================= */
    /* 👤 CORRECCIÓN DE CLIC/FOCUS EN EL BOTÓN DE PERFIL         */
    /* ========================================================= */

    /* 1. Elimina el fondo verde, el texto oscuro y el borde azul al darle clic o al estar abierto */
    .user-dropdown-area .profile-link:focus,
    .user-dropdown-area .profile-link:active,
    .user-dropdown-area .profile-link[aria-expanded="true"],
    .show > .user-dropdown-area .profile-link {
        background-color: transparent !important;
        box-shadow: none !important;
        outline: none !important;
        color: #334155 !important; /* Mantiene el color original del texto */
    }

    /* 2. Elimina el destello gris/azul que hacen los celulares al hacer "Tap" (tocar) */
    .user-dropdown-area .profile-link {
        -webkit-tap-highlight-color: transparent !important;
    }

    /* 3. Asegurar que el botón de la campana tampoco haga recuadros raros */
    #notifDropdown:focus,
    #notifDropdown:active,
    #notifDropdown[aria-expanded="true"] {
        background-color: transparent !important;
        box-shadow: none !important;
        outline: none !important;
    }
</style>
{{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}