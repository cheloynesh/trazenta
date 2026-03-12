var ruta = window.location;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + getUrl.pathname;

$(document).ready( function () {
    $('#tbProfCP').DataTable({
        language : {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
              "sFirst":    "Primero",
              "sLast":     "Último",
              "sNext":     "Siguiente",
              "sPrevious": "Anterior"
            },
            "oAria": {
              "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
              "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        aLengthMenu: [
            [24],
            [24]
        ],
    });
} );

$(document).ready( function () {
    $('#tbProfLP').DataTable({
        language : {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
              "sFirst":    "Primero",
              "sLast":     "Último",
              "sNext":     "Siguiente",
              "sPrevious": "Anterior"
            },
            "oAria": {
              "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
              "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        aLengthMenu: [
            [24],
            [24]
        ],
    });
} );

$(document).ready( function () {
    $('#tbProfB').DataTable({
        language : {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
              "sFirst":    "Primero",
              "sLast":     "Último",
              "sNext":     "Siguiente",
              "sPrevious": "Anterior"
            },
            "oAria": {
              "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
              "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        aLengthMenu: [
            [24],
            [24]
        ],
    });
} );

let formatter = new Intl.NumberFormat("en-US", {
  style: "currency",
  currency: "USD",
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
});

let formatterdec = new Intl.NumberFormat("en-US", {
  style: "decimal",
  currency: "USD",
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
});

var fund_type = "";
var fund_typeB = "";

function fundchange(aux)
{
    var route;
    route = baseUrl + '/GetinfoFund/' + $("#selectInsurance" + aux).val() + '/' + $("#selectCurr" + aux).val();

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            // $("#selectCurr" + aux).val(result.data.fund_curr);
            var table = $('#tbProf' + aux).DataTable();
            if(result.data == null)
            {
                alert("No se encontró un fondo con la moneda y tipo seleccionados.");
                table.clear();
                table.draw(false);
            }
            else
            {
                $("#yield" + aux).val(result.data.yield_net);
                switch (aux)
                {
                    case 'CP':
                        fund_type = result.data.fund_type;
                        calculoCP();
                        break;
                    case 'LP':
                        calculoLP();
                        break;
                    case 'CPB':
                        fund_typeB = result.data.fund_type;
                        calculoB();
                        break;
                    case 'LPB':
                        calculoB();
                        break;
                }
            }
        }
    })
}

function  calculoCP()
{
    var table = $('#tbProfCP').DataTable();

    var curr = $("#selectCurrCP").val();
    var yrs = parseInt($("#selectyearsCP").val()) * 12;
    var yield = parseFloat($("#yieldCP").val())/100;
    var opening = parseFloat($("#openingCP").val().replace(/[^0-9.]/g, ''));
    var inc = $("#incrementCP").val() == '' ? 0 : parseFloat($("#incrementCP").val().replace(/[^0-9.]/g, ''));
    var reinv = parseFloat($("#selectReinvCP").val());

    $("#secondCP").val("");
    $("#secondutilityCP").val("");

    var initial_balance = opening;
    var yieldups = 0;
    var increments = 0;
    var final_balance = 0;
    var utility = 0;

    let cpform = document.getElementById('validationCP');

    if (cpform.checkValidity() == false){}
    else
    {
        if((opening >= 250000 && curr == "MXN" && fund_type == "CP") || (opening >= 750000 && curr == "MXN" && fund_type == "MP") || (opening >= 5000 && curr == "USD"))
        {

            table.clear();

            for(cont = 1; cont <= yrs; cont++)
            {
                yieldups = initial_balance*yield/12;
                increments = inc + (reinv == 2 ? 0 : yieldups);
                final_balance = increments + initial_balance;
                utility += yieldups;
                if(cont == 12)
                {
                    $("#firstCP").val(formatterdec.format(final_balance));
                    $("#firstutilityCP").val(formatterdec.format(utility));
                }
                if(cont == 24) 
                {
                    $("#secondCP").val(formatterdec.format(final_balance));
                    $("#secondutilityCP").val(formatterdec.format(utility));
                }
                table.row.add([cont,formatter.format(initial_balance),formatter.format(yieldups),formatter.format(increments),formatter.format(final_balance)]);
                initial_balance = final_balance;
            }

            table.draw(false);
        }
        else
        {
            alert("La apertura no debe ser menor a $250,000.00 MXN en CP o $750,000.00 MXN en MP o 5,000 USD");
            table.clear();
            table.draw(false);
        }
    }
    cpform.classList.add('was-validated');
}

function  calculoLP()
{
    var table = $('#tbProfLP').DataTable();

    var curr = $("#selectCurrLP").val();
    var yield = parseFloat($("#yieldLP").val())/100;
    var opening = parseFloat($("#openingLP").val().replace(/[^0-9.]/g, ''));
    var yrs = parseInt($("#selectyearsLP").val()) * 12;

    var initial_balance = opening;
    var yieldups = initial_balance*yield/6;
    var final_balance = initial_balance;

    let cpform = document.getElementById('validationLP');

    if (cpform.checkValidity() == false){}
    else
    {
        if((opening >= 500000 && curr == "MXN") || (opening >= 25000 && curr == "USD"))
        {
            if ((opening % 500000 === 0 && curr == "MXN") || (opening % 25000 === 0 && curr == "USD"))
            {
                $("#obligationsLP").val(curr == "MXN" ? opening/500000 : opening/25000);

                $("#firstLP").val(formatterdec.format(opening));
                $("#secondLP").val(formatterdec.format(opening));
                $("#firstutilityLP").val(formatterdec.format(yieldups*6));
                $("#secondutilityLP").val(formatterdec.format(yieldups*12));

                table.clear();

                for(cont = 1; cont <= yrs; cont++)
                {
                    table.row.add([cont,formatter.format(initial_balance),formatter.format(cont % 2 === 0 ? yieldups : 0),formatter.format(final_balance)]);
                    initial_balance = final_balance;
                }

                table.draw(false);
            }
            else
            {
                alert("La apertura debe ser multiplo de $500,000.00 MXN o $25,000 USD");
                table.clear();
                table.draw(false);
            }
        }
        else
        {
            alert("La apertura no debe ser menor a $500,000.00 o $25,000 USD");
            table.clear();
            table.draw(false);
        }
    }
    cpform.classList.add('was-validated');
}

function  calculoB()
{
    var table = $('#tbProfB').DataTable();

    var curr = $("#selectCurrCPB").val();
    var yield = parseFloat($("#yieldCPB").val())/100;
    var opening = parseFloat($("#openingCPB").val().replace(/[^0-9.]/g, ''));
    var inc = $("#incrementCPB").val() == '' ? 0 : parseFloat($("#incrementCPB").val().replace(/[^0-9.]/g, ''));
    var reinv = parseFloat($("#selectReinvCPB").val());

    var initial_balance = opening;
    var yieldups = 0;
    var increments = 0;
    var final_balance = 0;

    var currL = $("#selectCurrLPB").val();
    var yieldL = parseFloat($("#yieldLPB").val())/100;
    var openingL = parseFloat($("#openingLPB").val().replace(/[^0-9.]/g, ''));
    var reinvL = parseFloat($("#selectReinvToCPB").val());

    var yrs = parseInt($("#selectyearsB").val()) * 12;

    var initial_balanceL = openingL;
    var yieldupsL = initial_balanceL*yieldL/6;
    var final_balanceL = initial_balanceL;

    var utility = 0;

    let cpform = document.getElementById('validationB');

    if (cpform.checkValidity() == false){}
    else
    {
        if((opening >= 250000 && curr == "MXN" && fund_typeB == "CP") || (opening >= 750000 && curr == "MXN" && fund_typeB == "MP") || (opening >= 5000 && curr == "USD"))
        {
            if((openingL >= 500000 && currL == "MXN") || (openingL >= 25000 && currL == "USD"))
            {
                if ((openingL % 500000 === 0 && currL == "MXN") || (openingL % 25000 === 0 && currL == "USD"))
                {
                    table.clear();

                    for(cont = 1; cont <= yrs; cont++)
                    {
                        yieldups = initial_balance*yield/12;
                        increments = inc + (reinv == 2 ? 0 : yieldups) + (cont % 2 === 0 && reinvL == 1 ? yieldupsL : 0);
                        final_balance = increments + initial_balance;

                        utility += yieldups + (cont % 2 === 0 ? yieldupsL : 0);

                        table.row.add([cont,formatter.format(initial_balance),formatter.format(yieldups),formatter.format(increments),formatter.format(final_balance),
                            formatter.format(initial_balanceL),formatter.format(cont % 2 === 0 ? yieldupsL : 0),formatter.format(final_balanceL)]);

                        initial_balance = final_balance;
                        initial_balanceL = final_balanceL;

                        if(cont == 12)
                        {
                            $("#firstLPB").val(formatterdec.format(initial_balance + final_balanceL));
                            $("#firstutilityLPB").val(formatterdec.format(utility));
                        }

                        if(cont == 24)
                        {
                            $("#secondLPB").val(formatterdec.format(initial_balance + final_balanceL));
                            $("#secondutilityLPB").val(formatterdec.format(utility));
                        }
                    }

                    table.draw(false);

                    $("#obligationsLPB").val(currL == "MXN" ? openingL/500000 : openingL/25000);
                }
                else
                {
                    alert("La apertura de largo plazo debe ser multiplo de $500,000.00 MXN o $25,000 USD");
                    table.clear();
                    table.draw(false);
                }
            }
            else
            {
                alert("La apertura de largo plazo no debe ser menor a $500,000.00 MXN o $25,000 USD");
                table.clear();
                table.draw(false);
            }
        }
        else
        {
            alert("La apertura no debe ser menor a $250,000.00 MXN en CP o $750,000.00 MXN en MP o 5,000 USD");
            table.clear();
            table.draw(false);
        }
    }
    cpform.classList.add('was-validated');
}

function pdfCP()
{
    const compania = document.getElementById('selectInsuranceCP').options[document.getElementById('selectInsuranceCP').selectedIndex].text;
    const monto = document.getElementById('openingCP').value;
    const inc = document.getElementById('incrementCP').value === "" ? "0" : document.getElementById('incrementCP').value;
    const tasa = document.getElementById('yieldCP').value;
    const moneda = document.getElementById('selectCurrCP').value;
    const utilityfst = document.getElementById('firstutilityCP').value;
    const saldfst = document.getElementById('firstCP').value;
    const utilityscnd = document.getElementById('secondutilityCP').value;
    const saldscnd = document.getElementById('secondCP').value;
    const reinv = document.getElementById('selectReinvCP').value === "1" ? "Sí" : "No";
    const hoy = new Date();

    // const tabla = document.querySelector('#tbProfCP').outerHTML.replace('<table', '<table class="pdf-table"');
    const dt = $('#tbProfCP').DataTable();
    const data = dt.rows().data();

    let filas = '';
    data.each(row => {
        filas += `<tr>
            <td>${row[0]}</td>
            <td>${row[1]}</td>
            <td>${row[2]}</td>
            <td>${row[3]}</td>
            <td>${row[4]}</td>
        </tr>`;
    });

    const contenido = `
        <div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 40px; color: #333; background-color: #fff;">
        <style>
            /* Estilos Generales */
            .header-box {
                display: flex;
                justify-content: space-between;
                margin-bottom: 30px;
                border-bottom: 2px solid #106a6a;
                padding-bottom: 10px;
            }
            
            .info-table {
                width: 100%;
                margin-bottom: 30px;
                border-collapse: separate;
                border-spacing: 0;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                overflow: hidden;
            }
            
            .info-table td {
                padding: 10px 15px;
                border-bottom: 1px solid #e0e0e0;
            }

            .label {
                background-color: #106a6a;
                color: #ffffff;
                font-weight: bold;
                width: 35%;
                font-size: 11px;
                text-transform: uppercase;
            }

            .value {
                background-color: #fcfcfc;
                text-align: left;
                font-size: 12px;
            }

            /* Tablas de Datos */
            .pdf-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                font-size: 11px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .pdf-table th {
                background-color: #106a6a;
                color: #ffffff;
                padding: 12px 8px;
                text-align: center;
                border: 1px solid #0d5454;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .pdf-table td {
                padding: 10px 8px;
                border: 1px solid #e0e0e0;
                text-align: right;
            }

            .pdf-table tr:nth-child(even) td {
                background-color: #f8f9f9;
            }

            .section-title {
                color: #106a6a;
                font-size: 16px;
                margin-top: 30px;
                margin-bottom: 10px;
                font-weight: bold;
                border-left: 4px solid #106a6a;
                padding-left: 10px;
            }

            .legal-notice {
                margin-top: 40px;
                padding: 20px;
                background-color: #f4f7f7;
                border-radius: 8px;
                border-left: 5px solid #106a6a;
                page-break-inside: avoid;
            }
        </style>

        <div class="header-box">
            <h1 style="margin: 0; color: #106a6a; font-size: 22px;">Proyección de Inversión</h1>
            <div style="text-align: right; font-size: 10px; color: #666;">
                Generado el: ${hoy.toLocaleDateString()}
            </div>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Portafolio</td><td class="value"><strong>${compania}</strong></td>
                <td class="label">Monto Apertura</td><td class="value">${monto}</td>
            </tr>
            <tr>
                <td class="label">Moneda</td><td class="value">${moneda}</td>
                <td class="label">Incremento Mensual</td><td class="value">${inc}</td>
            </tr>
            <tr>
                <td class="label">Tasa Proyectada</td><td class="value" style="color: #106a6a; font-weight: bold;">${tasa}%</td>
                <td class="label">Reinversión</td><td class="value">${reinv}</td>
            </tr>
        </table>

        <div class="section-title">Proyección Anual</div>
        <table class="pdf-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Año</th>
                    <th style="width: 40%;">Saldo Acumulado</th>
                    <th style="width: 40%;">Utilidad Generada</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>${saldfst}</td>
                    <td style="color: #106a6a; font-weight: bold;">+ ${utilityfst}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>${saldscnd}</td>
                    <td style="color: #106a6a; font-weight: bold;">+ ${utilityscnd}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Detalle Mensual</div>
        <table class="pdf-table">
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Saldo Inicial</th>
                    <th>Rendimientos</th>
                    <th>Incrementos</th>
                    <th>Saldo Final</th>
                </tr>
            </thead>
            <tbody>${filas}</tbody>
        </table>

        <div class="legal-notice">
            <h3 style="margin: 0 0 8px 0; color: #106a6a; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">
                Variabilidad de Tasas y Condiciones
            </h3>
            <p style="margin: 0; line-height: 1.6; color: #444; text-align: justify; font-size: 11px;">
                El fondo de inversión opera bajo una estrategia activa que busca optimizar los rendimientos según el entorno económico vigente. 
                Debido a esta dinámica, la tasa de interés puede fluctuar mes a mes. Favor de consultar el histórico de rendimientos.
                Los valores mostrados en esta corrida son estimaciones proyectadas con base en las condiciones actuales y no garantizan un rendimiento futuro idéntico.
            </p>
        </div>
    </div>
    `;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = contenido;
    document.body.appendChild(wrapper);

    html2pdf().set({
        margin: [20, 0.5, 20, 0.5],
        filename: 'corrida.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, scrollY: 0 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: {
            mode: ['css', 'legacy'],
            avoid: ['tr']
        }
    }).from(wrapper).save().then(() => {
        document.body.removeChild(wrapper);
    });
}

function pdfLP()
{
    const compania = document.getElementById('selectInsuranceLP').options[document.getElementById('selectInsuranceLP').selectedIndex].text;
    const monto = document.getElementById('openingLP').value;
    const tasa = document.getElementById('yieldLP').value;
    const moneda = document.getElementById('selectCurrLP').value;
    const utilityfst = document.getElementById('firstutilityLP').value;
    const saldfst = document.getElementById('firstLP').value;
    const utilityscnd = document.getElementById('secondutilityLP').value;
    const saldscnd = document.getElementById('secondLP').value;
    const obli = document.getElementById('obligationsLP').value;
    const hoy = new Date();

    // const tabla = document.querySelector('#tbProfCP').outerHTML.replace('<table', '<table class="pdf-table"');
    const dt = $('#tbProfLP').DataTable();
    const data = dt.rows().data();

    let filas = '';
    data.each(row => {
        filas += `<tr>
            <td>${row[0]}</td>
            <td>${row[1]}</td>
            <td>${row[2]}</td>
            <td>${row[3]}</td>
        </tr>`;
    });

    const contenido = `
    <div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 20px 40px; color: #333; background-color: #fff; line-height: 1.2;">
        <style>
            /* Optimizaciones para una sola página */
            .header-box {
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
                border-bottom: 2px solid #106a6a;
                padding-bottom: 5px;
            }
            
            .info-table {
                width: 100%;
                margin-bottom: 15px;
                border-collapse: collapse;
                font-size: 10px;
            }
            
            .info-table td {
                padding: 6px 10px;
                border: 1px solid #e0e0e0;
            }

            .label {
                background-color: #106a6a;
                color: #ffffff;
                font-weight: bold;
                width: 25%;
                text-transform: uppercase;
            }

            .value {
                background-color: #fcfcfc;
                width: 25%;
            }

            /* Tablas compactas */
            .pdf-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                font-size: 9.5px; /* Tamaño reducido para asegurar que entren los 24 meses */
            }

            .pdf-table th {
                background-color: #106a6a;
                color: #ffffff;
                padding: 6px 4px;
                text-align: center;
                border: 1px solid #0d5454;
                text-transform: uppercase;
            }

            .pdf-table td {
                padding: 4px 6px;
                border: 1px solid #e0e0e0;
                text-align: right;
            }

            .pdf-table tr:nth-child(even) td {
                background-color: #f8f9f9;
            }

            .section-title {
                color: #106a6a;
                font-size: 13px;
                margin-top: 15px;
                margin-bottom: 5px;
                font-weight: bold;
                border-left: 3px solid #106a6a;
                padding-left: 8px;
            }

            /* Forzar que no haya saltos de página dentro de la tabla */
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        </style>

        <div class="header-box">
            <h1 style="margin: 0; color: #106a6a; font-size: 18px;">Proyección de Inversión</h1>
            <div style="text-align: right; font-size: 9px; color: #666;">
                Fecha: ${hoy.toLocaleDateString()}
            </div>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Portafolio</td><td class="value"><strong>${compania}</strong></td>
                <td class="label">Monto Apertura</td><td class="value">${monto}</td>
            </tr>
            <tr>
                <td class="label">Moneda</td><td class="value">${moneda}</td>
                <td class="label">Obligaciones</td><td class="value">${obli}</td>
            </tr>
            <tr>
                <td class="label">Tasa Proyectada</td><td class="value" style="color: #106a6a; font-weight: bold;">${tasa}%</td>
                <td class="label">Estatus</td><td class="value">Proyección a 24 meses</td>
            </tr>
        </table>

        <div class="section-title">Proyección Anual</div>
        <table class="pdf-table" style="margin-bottom: 10px;">
            <thead>
                <tr>
                    <th style="width: 20%;">Año</th>
                    <th style="width: 40%;">Saldo Acumulado</th>
                    <th style="width: 40%;">Utilidad Generada</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>${saldfst}</td>
                    <td style="color: #106a6a; font-weight: bold;">+ ${utilityfst}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>${saldscnd}</td>
                    <td style="color: #106a6a; font-weight: bold;">+ ${utilityscnd}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Detalle Mensual</div>
        <table class="pdf-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Mes</th>
                    <th style="width: 30%;">Saldo Inicial</th>
                    <th style="width: 30%;">Rendimientos</th>
                    <th style="width: 30%;">Saldo Final</th>
                </tr>
            </thead>
            <tbody>${filas}</tbody>
        </table>
    </div>
    `;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = contenido;
    document.body.appendChild(wrapper);

    html2pdf().set({
        margin: [20, 0.5, 20, 0.5],
        filename: 'corrida.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, scrollY: 0 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: {
            mode: ['css', 'legacy'],
            avoid: ['tr']
        }
    }).from(wrapper).save().then(() => {
        document.body.removeChild(wrapper);
    });
}

function pdfB()
{
    const compania = document.getElementById('selectInsuranceCPB').options[document.getElementById('selectInsuranceCPB').selectedIndex].text;
    const monto = document.getElementById('openingCPB').value;
    const inc = document.getElementById('incrementCPB').value === "" ? "0" : document.getElementById('incrementCPB').value;
    const tasa = document.getElementById('yieldCPB').value;
    const moneda = document.getElementById('selectCurrCPB').value;
    const reinv = document.getElementById('selectReinvCPB').value === "1" ? "Sí" : "No";

    const companiaL = document.getElementById('selectInsuranceLPB').options[document.getElementById('selectInsuranceLPB').selectedIndex].text;
    const montoL = document.getElementById('openingLPB').value;
    const tasaL = document.getElementById('yieldLPB').value;
    const monedaL = document.getElementById('selectCurrLPB').value;
    const utilityfst = document.getElementById('firstutilityLPB').value;
    const saldfst = document.getElementById('firstLPB').value;
    const utilityscnd = document.getElementById('secondutilityLPB').value;
    const saldscnd = document.getElementById('secondLPB').value;
    const obli = document.getElementById('obligationsLPB').value;
    const hoy = new Date();

    // const tabla = document.querySelector('#tbProfCP').outerHTML.replace('<table', '<table class="pdf-table"');
    const dt = $('#tbProfB').DataTable();
    const data = dt.rows().data();

    let filas = '';
    data.each(row => {
        filas += `<tr>
            <td>${row[0]}</td>
            <td>${row[1]}</td>
            <td>${row[2]}</td>
            <td>${row[3]}</td>
            <td>${row[4]}</td>
            <td>${row[5]}</td>
            <td>${row[6]}</td>
            <td>${row[7]}</td>
        </tr>`;
    });

    const contenido = `
    <div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 15px 30px; color: #333; background-color: #fff; line-height: 1.1;">
        <style>
            /* Ajustes de compactación extrema */
            .header-box {
                border-bottom: 2px solid #106a6a;
                margin-bottom: 10px;
                padding-bottom: 5px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .container-capsulas {
                display: flex;
                justify-content: space-between;
                gap: 10px;
                margin-bottom: 10px;
            }

            .info-table {
                width: 49%;
                border-collapse: collapse;
                font-size: 9px;
            }
            
            .info-table td {
                padding: 4px 8px;
                border: 1px solid #e0e0e0;
            }

            .label {
                background-color: #106a6a;
                color: #ffffff;
                font-weight: bold;
                width: 45%;
                text-transform: uppercase;
            }

            .value {
                background-color: #fcfcfc;
                text-align: center;
            }

            /* Tablas de resultados */
            .pdf-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 8.5px; /* Tamaño optimizado para 8 columnas */
                margin-bottom: 10px;
            }

            .pdf-table th {
                background-color: #106a6a;
                color: #ffffff;
                padding: 5px 2px;
                text-align: center;
                border: 1px solid #0d5454;
                text-transform: uppercase;
            }

            .pdf-table td {
                padding: 3px 4px;
                border: 1px solid #e0e0e0;
                text-align: right;
            }

            /* Línea divisoria negra solicitada en el portafolio B */
            .pdf-table th:nth-child(6),
            .pdf-table td:nth-child(6) {
                border-left: 2px solid #000;
            }

            .pdf-table tr:nth-child(even) td {
                background-color: #f8f9f9;
            }

            .section-title {
                color: #106a6a;
                font-size: 11px;
                margin-top: 10px;
                margin-bottom: 5px;
                font-weight: bold;
                border-left: 3px solid #106a6a;
                padding-left: 5px;
            }

            .legal-notice {
                margin-top: 40px;
                padding: 20px;
                background-color: #f4f7f7;
                border-radius: 8px;
                border-left: 5px solid #106a6a;
                page-break-inside: avoid;
            }
        </style>

        <div class="header-box">
            <h1 style="margin: 0; color: #106a6a; font-size: 16px;">Proyección de Inversión</h1>
            <span style="font-size: 9px; color: #666;">${hoy.toLocaleDateString()}</span>
        </div>

        <div class="container-capsulas">
            <table class="info-table">
                <tr><td class="label">Nombre</td><td class="value">${compania}</td></tr>
                <tr><td class="label">Moneda</td><td class="value">${moneda}</td></tr>
                <tr><td class="label">Tasa</td><td class="value"><strong>${tasa}%</strong></td></tr>
                <tr><td class="label">Apertura</td><td class="value">${monto}</td></tr>
                <tr><td class="label">Inc. Mensual</td><td class="value">${inc}</td></tr>
            </table>

            <table class="info-table">
                <tr><td class="label">Nombre</td><td class="value">${companiaL}</td></tr>
                <tr><td class="label">Moneda</td><td class="value">${monedaL}</td></tr>
                <tr><td class="label">Tasa</td><td class="value"><strong>${tasaL}%</strong></td></tr>
                <tr><td class="label">Apertura</td><td class="value">${montoL}</td></tr>
                <tr><td class="label">Obligaciones</td><td class="value">${obli}</td></tr>
            </table>
        </div>

        <div class="section-title">Proyección Anual</div>
        <table class="pdf-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Año</th>
                    <th style="width: 45%;">Saldo Acumulado</th>
                    <th style="width: 45%;">Utilidad Generada</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: center;">${saldfst}</td>
                    <td style="text-align: center; color: #106a6a; font-weight: bold;">${utilityfst}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td style="text-align: center;">${saldscnd}</td>
                    <td style="text-align: center; color: #106a6a; font-weight: bold;">${utilityscnd}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Detalle Mensual</div>
        <table class="pdf-table">
            <thead>
                <tr>
                    <th style="width: 4%;">Mes</th>
                    <th style="width: 13%;">S. Inicial (A)</th>
                    <th style="width: 13%;">Rend. (A)</th>
                    <th style="width: 13%;">Incr. (A)</th>
                    <th style="width: 13%;">S. Final (A)</th>
                    <th style="width: 13%;">S. Inicial (B)</th>
                    <th style="width: 13%;">Rend. (B)</th>
                    <th style="width: 13%;">S. Final (B)</th>
                </tr>
            </thead>
            <tbody>${filas}</tbody>
        </table>

        <div class="legal-notice">
            <h3 style="margin: 0 0 8px 0; color: #106a6a; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">
                Variabilidad de Tasas y Condiciones (Solo aplica para fondos de corto y mediano plazo)
            </h3>
            <p style="margin: 0; line-height: 1.6; color: #444; text-align: justify; font-size: 11px;">
                El fondo de inversión opera bajo una estrategia activa que busca optimizar los rendimientos según el entorno económico vigente. 
                Debido a esta dinámica, la tasa de interés puede fluctuar mes a mes. Favor de consultar el histórico de rendimientos.
                Los valores mostrados en esta corrida son estimaciones proyectadas con base en las condiciones actuales y no garantizan un rendimiento futuro idéntico.
            </p>
        </div>
    </div>
    `;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = contenido;
    document.body.appendChild(wrapper);

    html2pdf().set({
        margin: [10, 0.5, 10, 0.5],
        filename: 'corrida.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, scrollY: 0 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: {
            mode: ['css', 'legacy'],
            avoid: ['tr']
        }
    }).from(wrapper).save().then(() => {
        document.body.removeChild(wrapper);
    });
}
