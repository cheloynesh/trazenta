<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Estatus</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f9; padding: 20px 0;">
        <tr>
            <td align="center">
                
                {{-- Contenedor Principal del Correo --}}
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                    
                    {{-- 1. ENCABEZADO CON LOGO --}}
                    <tr>
                        <td align="center" style="background-color: #014235; padding: 30px 20px 15px 20px;">
                            <img src="{{ $message->embed(public_path('img/logoheader.png')) }}" alt="elan Agente de Seguros" width="160" style="display: block; margin: 0 auto 15px auto;">
                            <h2 style="color: #ffffff; font-size: 20px; font-weight: 700; margin: 0;">¡Buen día, {{ $data['agente'] }}!</h2>
                        </td>
                    </tr>

                    {{-- 2. BANDA DE ESTATUS DINÁMICA --}}
                    <tr>
                        <td align="center" style="background-color: #002d24; padding: 15px 20px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="color: #e60000; font-size: 18px; font-weight: bold; letter-spacing: 1px;">
                                        &gt;&gt; &nbsp; ¡Suspendido! &nbsp; &lt;&lt;
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- 3. CUERPO DE DATOS PRINCIPALES --}}
                    <tr>
                        <td style="padding: 30px 40px; text-align: center;">
                            
                            <p style="color: #4a5568; font-size: 14px; line-height: 1.6; margin-bottom: 25px;">
                                Te informamos que tu solicitud se encuentra acutalmente <strong>❌SUSPENDIDA❌</strong>, adjunta encontrarás la carta de suspensión y en la nota más detalles.
                            </p>

                            {{-- Bloque de Datos Formateados --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center" style="padding: 6px 0; color: #1a202c; font-size: 16px;">
                                        <strong>Cliente:</strong> <span style="color: #00a859; font-weight: bold;">{{ $data['cliente'] }}</span>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f0fdf4; border-left: 4px solid #00a859; border-radius: 4px; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 12px 15px; text-align: left; color: #166534; font-size: 14px; line-height: 1.5;">
                                        <strong>Notas:</strong><br>
                                        {{ $data['ticket']->comm_susp }}
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #4a5568; font-size: 14px; margin-bottom: 20px;">
                                Para completar la solicitud, es necesario que adjuntes los archivos a través de la siguiente liga:
                            </p>

                            {{-- BOTÓN CTA (Portal de tickets agente) --}}
                            <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center" style="background-color: #00a859; border-radius: 25px; padding: 12px 30px;">
                                        <a href="https://trazenta.in/tickets/view" target="_blank" style="color: #ffffff; font-size: 16px; font-weight: bold; text-decoration: none; display: inline-block;">
                                            <img src="{{ $message->embed(public_path('img/globe.png')) }}" alt="X" width="16" height="16" 
                                                style="vertical-align: middle; margin-right: 4px; border: 0; display: inline-block; filter: brightness(0) invert(1); -webkit-filter: brightness(0) invert(1);">
                                            Portal de tickets agente
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 25px 0;">

                            {{-- LOGO BODY CENTRADO --}}
                            <img src="{{ $message->embed(public_path('img/logobody.png')) }}" alt="ELAN" width="50" style="display: block; margin: 0 auto 15px auto;">
                            
                            <p style="color: #4a5568; font-size: 14px; margin: 0;">
                                Si tienes alguna duda, no dudes en contactar a tu<br>
                                <strong style="color: #014235;">EJECUTIVA DE SOPORTE.</strong>
                            </p>

                            {{-- INSERCIÓN DINÁMICA DE LA FIRMA DIGITAL --}}
                            @if (!empty($data['signature_path']) && file_exists($data['signature_path']))
                                <div style="margin-top: 20px; text-align: center;">
                                    <img src="{{ $message->embed($data['signature_path']) }}" 
                                        alt="Firma Ejecutiva" 
                                        width="520" 
                                        style="display: block; margin: 0 auto; width: 100%; max-width: 520px; height: auto; border: 0; border-radius: 6px;">
                                </div>
                            @endif

                        </td>
                    </tr>

                    {{-- 4. PIE DE PÁGINA Y REDES SOCIALES --}}
                    <tr>
                        <td style="background-color: #014235; padding: 25px 30px; text-align: center;">

                            {{-- LOGO FOOTER CENTRADO --}}
                            <img src="{{ $message->embed(public_path('img/logofooter.png')) }}" alt="ELAN" width="50" style="display: block; margin: 0 auto 15px auto;">
                            
                            <p style="color: #ffffff; font-size: 14px; font-weight: bold; margin-bottom: 15px;">
                                ¡Te invitamos a seguirnos en nuestras redes!
                            </p>

                            {{-- Redes Sociales con Iconos Blancos via CSS --}}
                            <p style="color: #ffffff; font-size: 13px; margin-bottom: 20px; text-align: center;">
                                
                                {{-- Facebook --}}
                                <a href="#" target="_blank" style="color: #ffffff; text-decoration: none; margin: 0 6px; display: inline-block; vertical-align: middle;">
                                    <img src="{{ $message->embed(public_path('img/facebook.png')) }}" alt="Facebook" width="16" height="16" 
                                        style="vertical-align: middle; margin-right: 4px; border: 0; display: inline-block; filter: brightness(0) invert(1); -webkit-filter: brightness(0) invert(1);">
                                    <span style="vertical-align: middle;">Facebook</span>
                                </a> 
                                
                                <span style="color: #005a48; vertical-align: middle;">|</span>

                                {{-- LinkedIn --}}
                                <a href="#" target="_blank" style="color: #ffffff; text-decoration: none; margin: 0 6px; display: inline-block; vertical-align: middle;">
                                    <img src="{{ $message->embed(public_path('img/linkedin.png')) }}" alt="LinkedIn" width="16" height="16" 
                                        style="vertical-align: middle; margin-right: 4px; border: 0; display: inline-block; filter: brightness(0) invert(1); -webkit-filter: brightness(0) invert(1);">
                                    <span style="vertical-align: middle;">LinkedIn</span>
                                </a> 
                                
                                <span style="color: #005a48; vertical-align: middle;">|</span>

                                {{-- X (Twitter) --}}
                                <a href="#" target="_blank" style="color: #ffffff; text-decoration: none; margin: 0 6px; display: inline-block; vertical-align: middle;">
                                    <img src="{{ $message->embed(public_path('img/x.png')) }}" alt="X" width="16" height="16" 
                                        style="vertical-align: middle; margin-right: 4px; border: 0; display: inline-block; filter: brightness(0) invert(1); -webkit-filter: brightness(0) invert(1);">
                                    <span style="vertical-align: middle;">X</span>
                                </a> 
                                
                                <span style="color: #005a48; vertical-align: middle;">|</span>

                                {{-- Instagram --}}
                                <a href="#" target="_blank" style="color: #ffffff; text-decoration: none; margin: 0 6px; display: inline-block; vertical-align: middle;">
                                    <img src="{{ $message->embed(public_path('img/instagram.png')) }}" alt="Instagram" width="16" height="16" 
                                        style="vertical-align: middle; margin-right: 4px; border: 0; display: inline-block; filter: brightness(0) invert(1); -webkit-filter: brightness(0) invert(1);">
                                    <span style="vertical-align: middle;">Instagram</span>
                                </a>

                            </p>

                            <hr style="border: none; border-top: 1px solid #005a48; margin: 15px 0;">

                            {{-- Datos Legales --}}
                            <p style="color: #a0aec0; font-size: 11px; line-height: 1.4; margin: 0;">
                                ELAN Asesores Patrimoniales, Agente de Seguros y Fianzas, SA de CV, Paseo de los Virreyes 45,<br>
                                Puerta de Hierro, Zapopan, Jalisco 45116, México, +5233363276<br>
                                <a href="#" style="color: #e2e8f0; text-decoration: underline;">Darme de baja</a> &nbsp;
                                <a href="#" style="color: #e2e8f0; text-decoration: underline;">Gestionar preferencias</a>
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>