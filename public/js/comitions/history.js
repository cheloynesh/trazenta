var ruta = window.location;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + getUrl.pathname;
var baseUrlRec = getUrl .protocol + "//" + getUrl.host + "/funds/monthlycomission/monthcomission";
var baseUrlPP = getUrl .protocol + "//" + getUrl.host + "/funds/fstmonthcomission/fstmonthcomission";
var baseUrlLP = getUrl .protocol + "//" + getUrl.host + "/funds/sixmonthlycomission/sixmonthcomission";
var baseUrlCoimition = getUrl .protocol + "//" + getUrl.host + "/comitions/comition/comition";


$(document).ready( function () {
    $('#tbProf').DataTable({
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
        }
    });
} );

$(document).ready( function () {
    $('#tbRec').DataTable({
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
        }
    });
} );

$(document).ready( function () {
    $('#tbAd').DataTable({
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
        }
    });
} );

$(document).ready( function () {
    $('#tbNC').DataTable({
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
        }
    });
} );

$(document).ready( function () {
    $('#tbLP').DataTable({
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
        }
    });
} );

var formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  });

var idUser = 0;
var authType = 0;
var idsLP = [];
var recMonth = 0;
var recYear = 0;

function abrirComision(id,invoice,contpp,contpa,lpnopay)
{
    idUser = id;
    var route = baseUrl + '/GetInfo/'+ idUser + "/" + invoice + "/" + contpp + "/" + contpa + "/" + lpnopay;
    flagComition = 0;
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            document.getElementById("cardRec").style.display = "none";
            document.getElementById("cardNC").style.display = "none";
            document.getElementById("cardAd").style.display = "none";
            document.getElementById("cardLP").style.display = "none";
            if(invoice != 0) document.getElementById("cardRec").style.display = "block";
            if(contpp != 0) document.getElementById("cardNC").style.display = "block";
            if(contpa != 0) document.getElementById("cardAd").style.display = "block";
            if(lpnopay != 0) document.getElementById("cardLP").style.display = "block";
            fillTables(result,invoice,contpp,contpa,lpnopay);
            $("#myModal").modal('show');
        }
    })
}

function cancelar(modal)
{
    $(modal).modal('hide');
}

function fillTables(result,invoice,contpp,contpa,lpnopay)
{
    if(invoice != 0)
    {
        fillTableRec(result);
    }

    if(contpp != 0)
    {
        fillTablePP(result);
    }

    if(contpa != 0)
    {
        fillTablePA(result);
    }

    if(lpnopay != 0)
    {
        fillTableLP(result);
    }
}

function fillTableRec(result)
{
    var table = $('#tbRec').DataTable();
    var btnInvoice = '';
    var btnPay = '';
    var months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    table.clear();
    result.rec.forEach( function(valor, indice, array) {
        btnInvoice = '<a href="'+getUrl.protocol + "//" + getUrl.host + "/comition_files/rec_invoice/" + valor.invoice_doc + '" id="viewPDF" target="_blank" class="btn btn-success">'+valor.invoice_date+'</a>';
        btnPay = '<a href="'+getUrl.protocol + "//" + getUrl.host + "/comition_files/rec_pay/" + valor.pay_doc + '" id="viewPDF" target="_blank" class="btn btn-success">'+valor.invoice_date+'</a>';

        table.row.add([months[parseInt(valor.curr_month)-1]+" "+valor.curr_year,btnInvoice,btnPay]).node().id = valor.id;
    });
    table.draw(false);
}

function fillTablePP(result)
{
    var table = $('#tbNC').DataTable();
    var btnInvoice = '';
    var btnPay = '';

    table.clear();
    result.pp.forEach( function(valor, indice, array) {
        btnInvoice = '<a href="'+getUrl.protocol + "//" + getUrl.host + "/comition_files/fst_invoice/" + valor.fst_invoice_doc + '" id="viewPDF" target="_blank" class="btn btn-success">'+valor.fst_invoice+'</a>';
        btnPay = '<a href="'+getUrl.protocol + "//" + getUrl.host + "/comition_files/fst_pay/" + valor.fst_pay_doc + '" id="viewPDF" target="_blank" class="btn btn-success">'+valor.fst_pay+'</a>';

        table.row.add([valor.nuc,valor.client_name,valor.apertura,btnInvoice,btnPay]).node().id = valor.idNuc;
    });
    table.draw(false);
}

function fillTablePA(result)
{
    var table = $('#tbAd').DataTable();
    var btnInvoice = '';
    var btnPay = '';
    var btn = '';

    table.clear();
    result.pa.forEach( function(valor, indice, array) {
        btnInvoice = '<a href="'+getUrl.protocol + "//" + getUrl.host + "/comition_files/mov_invoice/" + valor.mov_invoice_doc + '" id="viewPDF" target="_blank" class="btn btn-success">'+valor.mov_invoice+'</a>';
        btnPay = '<a href="'+getUrl.protocol + "//" + getUrl.host + "/comition_files/mov_pay/" + valor.mov_pay_doc + '" id="viewPDF" target="_blank" class="btn btn-success">'+valor.mov_pay+'</a>';

        table.row.add([valor.nuc,valor.client_name,valor.apertura,valor.prev_balance,valor.new_balance,valor.currency,valor.amount,btnInvoice,btnPay]).node().id = valor.idM;
    });
    table.draw(false);
}

function fillTableLP(result)
{
    var table = $('#tbLP').DataTable();
    var btnInvoice = '';
    var btnPay = '';
    idsLP = [];

    table.clear();
    result.lp.forEach( function(valor, indice, array) {
        btnInvoice = '<a href="'+getUrl.protocol + "//" + getUrl.host + "/comition_files/lp_invoice/" + valor.lp_invoice_doc + '" id="viewPDF" target="_blank" class="btn btn-success">'+valor.lp_invoice+'</a>';
        btnPay = '<a href="'+getUrl.protocol + "//" + getUrl.host + "/comition_files/lp_pay/" + valor.lp_pay_doc + '" id="viewPDF" target="_blank" class="btn btn-success">'+valor.lp_pay+'</a>';

        table.row.add([valor.nuc,valor.client_name,valor.apertura,btnInvoice,btnPay]).node().id = valor.idNuc;
        idsLP.push(valor.idNuc);
    });
    table.draw(false);
    $("#comitionAll").val("13,500.00");
}
