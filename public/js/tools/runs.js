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
                $("#yield" + aux).val(result.data.yield);
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

    var initial_balance = opening;
    var yieldups = 0;
    var increments = 0;
    var final_balance = 0;

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
    const reinv = document.getElementById('selectReinvCP').value === "1" ? "Sí" : "No";

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
        <div style="font-family:sans-serif; padding:20px; font-size:12px; color:#000;">
        <style>
            .pdf-table {
                width: 90% !important;
                margin: 0 auto;
                border-collapse: collapse;
                margin-top: 10px;
                font-size: 12px;
                table-layout: fixed;
            }
            .pdf-table th {
                background: #106a6a;
                color: #fff;
                border: 1px solid #000;
                padding: 4px;
                text-align: center;
            }
            .pdf-table td {
                background: #f0f0f0;
                color: #000;
                border: 1px solid #000;
                padding: 4px;
                text-align: right;
            }
            .pdf-table tr:nth-child(even) {
                background: #fafafa;
            }
            .pdf-table th:nth-child(1),
            .pdf-table td:nth-child(1) { width: 10%; }

            .pdf-table th:nth-child(2),
            .pdf-table td:nth-child(2) { width: 20%; }

            .pdf-table th:nth-child(3),
            .pdf-table td:nth-child(3) { width: 20%; }

            .pdf-table th:nth-child(4),
            .pdf-table td:nth-child(4) { width: 20%; }

            .pdf-table th:nth-child(5),
            .pdf-table td:nth-child(5) { width: 20%; }
        </style>

        <table style="table-layout: fixed; width:40%; margin-bottom:20px; margin-top:30px; margin-left:50px; border-collapse:collapse;">
            <tr>
                <td style="width:55%; border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Portafolio</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${compania}</td>
            </tr>
            <tr>
                <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Moneda</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${moneda}</td>
            </tr>
            <tr>
                <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Tasa</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${tasa}%</td>
            </tr>
            <tr>
                <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Monto apertura</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${monto}</td>
            </tr>
            <tr>
                <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Incremento mensual</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${inc}</td>
            </tr>
            <tr>
                <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Reinversión</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${reinv}</td>
            </tr>
        </table>

        <table class="pdf-table">
        <thead>
            <tr>
            <th>Mes</th>
            <th>Saldo inicial</th>
            <th>Rendimientos</th>
            <th>Incrementos</th>
            <th>Saldo final</th>
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
        <div style="font-family:sans-serif; padding:20px; font-size:12px; color:#000;">
        <style>
            .pdf-table {
                width: 90% !important;
                margin: 0 auto;
                border-collapse: collapse;
                margin-top: 10px;
                font-size: 12px;
                table-layout: fixed;
            }
            .pdf-table th {
                background: #106a6a;
                color: #fff;
                border: 1px solid #000;
                padding: 4px;
                text-align: center;
            }
            .pdf-table td {
                background: #f0f0f0;
                color: #000;
                border: 1px solid #000;
                padding: 4px;
                text-align: right;
            }
            .pdf-table tr:nth-child(even) {
                background: #fafafa;
            }
            .pdf-table th:nth-child(1),
            .pdf-table td:nth-child(1) { width: 10%; }

            .pdf-table th:nth-child(2),
            .pdf-table td:nth-child(2) { width: 20%; }

            .pdf-table th:nth-child(3),
            .pdf-table td:nth-child(3) { width: 20%; }

            .pdf-table th:nth-child(4),
            .pdf-table td:nth-child(4) { width: 20%; }

            .pdf-table th:nth-child(5),
            .pdf-table td:nth-child(5) { width: 20%; }
        </style>

        <table style="table-layout: fixed; width:40%; margin-bottom:20px; margin-top:30px; margin-left:50px; border-collapse:collapse;">
            <tr>
                <td style="width:55%; border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Portafolio</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${compania}</td>
            </tr>
                <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Moneda</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${moneda}</td>
            </tr>
            <tr>
                <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Tasa</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${tasa}%</td>
            </tr>
            <tr>
            <tr>
                <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Monto apertura</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${monto}</td>
            </tr>
            <tr>
                <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Obligaciones</td>
                <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${obli}</td>
            </tr>
        </table>

        <table class="pdf-table">
        <thead>
            <tr>
                <th>Año</th>
                <th>Saldo</th>
                <th>Utilidad</th>
            </tr>
        </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: center;">${saldfst}</td>
                    <td style="text-align: center;">${utilityfst}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td style="text-align: center;">${saldscnd}</td>
                    <td style="text-align: center;">${utilityscnd}</td>
                </tr>
            </tbody>
        </table>

        <table class="pdf-table">
        <thead>
            <tr>
            <th>Mes</th>
            <th>Saldo inicial</th>
            <th>Rendimientos</th>
            <th>Saldo final</th>
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
        <div style="font-family:sans-serif; padding:20px; font-size:12px; color:#000;">
        <style>
            .pdf-table {
                width: 90% !important;
                margin: 0 auto;
                border-collapse: collapse;
                font-size: 12px;
                table-layout: fixed;
                margin-bottom:20px;
            }
            .pdf-table th {
                background: #106a6a;
                color: #fff;
                border: 1px solid #000;
                padding: 4px;
                text-align: center;
            }
            .pdf-table td {
                background: #f0f0f0;
                color: #000;
                border: 1px solid #000;
                padding: 4px;
                text-align: right;
            }
            .pdf-table tr:nth-child(even) {
                background: #fafafa;
            }
            .pdf-table th:nth-child(1),
            .pdf-table td:nth-child(1) { width: 10%; }

            .pdf-table th:nth-child(2),
            .pdf-table td:nth-child(2) { width: 20%; }

            .pdf-table th:nth-child(3),
            .pdf-table td:nth-child(3) { width: 20%; }

            .pdf-table th:nth-child(4),
            .pdf-table td:nth-child(4) { width: 20%; }

            .pdf-table th:nth-child(5),
            .pdf-table td:nth-child(5) { width: 20%; }

            .pdf-table th:nth-child(6),
            .pdf-table td:nth-child(6) { width: 20%; }

            .pdf-table th:nth-child(7),
            .pdf-table td:nth-child(7) { width: 20%; }

            .pdf-table th:nth-child(8),
            .pdf-table td:nth-child(8) { width: 20%; }

            #tbProfBpdf th:nth-child(6),
            #tbProfBpdf td:nth-child(6) {
                border-left: 3px solid #000; /* línea negra divisoria */
            }
        </style>

        <div style="text-align:center;">
            <table style="display:inline-block; table-layout: fixed; width:40%; margin-bottom:20px; margin-left:50px; border-collapse:collapse; font-size: 12px;">
                <tr>
                    <td style="width:55%; border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Portafolio</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${compania}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Moneda</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${moneda}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Tasa</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${tasa}%</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Monto apertura</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${monto}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Incremento mensual</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${inc}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Reinversión</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${reinv}</td>
                </tr>
            </table>

            <table style="display:inline-block; table-layout: fixed; width:40%; margin-bottom:20px; margin-left:50px; border-collapse:collapse; font-size: 12px;">
                <tr>
                    <td style="width:55%; border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Portafolio</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${companiaL}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Moneda</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${monedaL}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Tasa</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${tasaL}%</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Monto apertura</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${montoL}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #000; padding:4px; background: #106a6a; color: #fff">Obligaciones</td>
                    <td style="border:1px solid #000; padding:4px; background: #f0f0f0; color: #000; text-align: center;">${obli}</td>
                </tr>
            </table>
        </div>

        <table class="pdf-table">
        <thead>
            <tr>
                <th>Año</th>
                <th>Saldo</th>
                <th>Utilidad</th>
            </tr>
        </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: center;">${saldfst}</td>
                    <td style="text-align: center;">${utilityfst}</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td style="text-align: center;">${saldscnd}</td>
                    <td style="text-align: center;">${utilityscnd}</td>
                </tr>
            </tbody>
        </table>

        <table class="pdf-table" id="tbProfBpdf">
        <thead>
            <tr>
            <th>Mes</th>
            <th>Saldo inicial</th>
            <th>Rendimientos</th>
            <th>Incrementos</th>
            <th>Saldo final</th>
            <th>Saldo inicial</th>
            <th>Rendimientos</th>
            <th>Saldo final</th>
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
