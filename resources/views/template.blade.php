@extends('home')
<head>
    <title>
        Home | Trazenta
    </title>
</head>
@section('content')
    <div class="text-center"><h1>Trazenta</h1></div>
    <div style="max-width: 100%; margin: auto;">
        {{-- modal| --}}
        {{-- fin modal| --}}
        @include('admin.applications.applicationEdit')
        {{-- Inicia pantalla de inicio --}}
        <div class="container-fluid bd-example-row">
            <div class="col-lg-12">
                <br>
                <hr class="section-divider">

                <div class="row mt-4">
                    <div class="col-lg-12 text-center">
                        <h1 class="section-title">Accede a nuestros diversos servicios desde los siguientes enlaces:</h1>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-lg-3 text-center">
                    </div>

                    <div class="col-lg-3 text-center">
                        <a href="https://contenido.elan.mx/formulario-de-servicio" target="_blank" rel="noopener noreferrer">
                            <img src="{{ URL::asset('img/Servicios.png') }}" class="service-img" alt="Servicios">
                        </a>
                        <h4 class="img-title">Servicios</h4>
                    </div>

                    <div class="col-lg-3 text-center">
                        <a href="https://contenido.elan.mx/aperturas-inversiones" target="_blank" rel="noopener noreferrer">
                            <img src="{{ URL::asset('img/Aperturas.png') }}" class="service-img" alt="Emisiones">
                        </a>
                        <h4 class="img-title">Aperturas</h4>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection
@push('head')
    <script src="{{URL::asset('js/template.js')}}"></script>
@endpush
