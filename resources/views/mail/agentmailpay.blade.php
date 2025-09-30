{{-- <!DOCTYPE html>
<html>
    <head>
        <style>
            *{
                padding: 0;
                margin: 0;
                box-sizing: border-box;
            }

            .container {
                width: 100%;
                height: auto;
                position: relative;
            }

            .container img {
                width: 100%;
                height: auto;
            }

            .container .text {
                position: relative;
                color: black;
                right: 10px;
                top: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <img src="{{ $message->embed(public_path().'/img/mail.png')}}">
            <div class="text">
                <h1>Estimado/a [Nombre del Agente],</h1>
                <p>Esperamos que este mensaje le encuentre bien.<br>
                    Adjunto a este correo encontrará los recibos correspondientes a sus comisiones del período [mes/año]. Le agradeceremos que nos envíe su factura correspondiente a la brevedad, para proceder con el pago en tiempo y forma.<br>
                    Por favor, no dude en contactarnos si requiere alguna aclaración o tiene alguna duda respecto a los montos o el proceso.<br>
                    Quedamos atentos a su confirmación.<br>
                    Atentamente<br>
                </p>

            </div>
        </div>
    </body>
</html> --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correo de Notificación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: block;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .email-container {
            background-color: white !important;
            width: 500px;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .email-container h1 {
            color: #136b6a;
        }
        .logo {
            width: 100%;
            height: auto;
            margin: 10px 0;
        }
        .content {
            text-align: left;
            margin: 20px 0;
        }
        .footer {
            font-size: 12px;
            color: gray;
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .unsubscribe {
            color: #136b6a;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <img src="{{ $message->embed(public_path().'/img/mail.png')}}" alt="Logo" class="logo">
        <h1>Estimado {{$name}}</h1>
        <div class="content">
            <p>Esperamos que te encuentres muy bien.</p>
            <p>Adjunto le compartimos los comprobantes de pago correspondientes a las facturas de sus comisiones pagadas del periodo {{$mnth}} - {{$year}}.</p>
            <p>Quedamos atentos a su confirmación de recepción y disponibles para cualquier consulta adicional.</p>
        </div>
        <p>Saludos,</p>
        <div class="footer">
            <p>Trazenta</p>
        </div>
    </div>
</body>
</html>
