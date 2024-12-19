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

function fillTableComition(result)
{
    var table = $('#tbProf').DataTable();
    var btnInvoice = '';
    var btnPay = '';
    var btn = '';

    table.clear();
    result.coms.forEach( function(valor, indice, array) {

        btn = '<a href="#|" class="btn btn-primary" onclick="abrirComision('+valor.uid+','+valor.rec_delay+','+valor.contpp+','+valor.contpa+','+valor.lpnopay+')" >Cálculo</a>';

        table.row.add([valor.aname,valor.rec_delay,valor.contpp,valor.contpa,valor.lpnopay,btn]).node().id = valor.uid;
    });
    table.draw(false);
}

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
            $("#selectRegime").val(result.regime.fk_regime);
            // alert(result.regime)
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
    var months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    table.clear();
    result.rec.forEach( function(valor, indice, array) {
        if(valor.invoice_date == null)
            btnInvoice = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.id+',1,1)" >Pendiente</button>'
        else
            btnInvoice = '<button href="#|" class="btn btn-success" onclick="seePDF('+valor.id+',1,1)">'+valor.invoice_date+'</button>'

        if(valor.pay_date == null)
            btnPay = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.id+',2,1)" >Sin Pago</button>'
        else
            btnPay = '<button href="#|" class="btn btn-success" onclick="seePDF('+valor.id+',2,1)">'+valor.pay_date+'</button>'

        btn = '<button type="button" class="btn btn-primary"'+'onclick="pdfRec(' + valor.uid + ',' + valor.curr_month + ',' + valor.curr_year + ')">PDF</button>';

        table.row.add([months[parseInt(valor.curr_month)-1]+" "+valor.curr_year,btnInvoice,btnPay,btn]).node().id = valor.id;
    });
    table.draw(false);
    $("#dlls_com").val(result.regime.dlls);
}

function fillTablePP(result)
{
    var table = $('#tbNC').DataTable();
    var btnInvoice = '';
    var btnPay = '';
    var btn = '';

    table.clear();
    result.pp.forEach( function(valor, indice, array) {
        if(valor.fst_invoice == null)
            btnInvoice = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.idNuc+',1,2)" >Pendiente</button>'
        else
            btnInvoice = '<button href="#|" class="btn btn-success" onclick="seePDF('+valor.idNuc+',1,2)">'+valor.fst_invoice+'</button>'

        if(valor.fst_pay == null)
            btnPay = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.idNuc+',2,2)" >Sin Pago</button>'
        else
            btnPay = '<button href="#|" class="btn btn-success" onclick="seePDF('+valor.idNuc+',2,2)">'+valor.fst_pay+'</button>'

        btn = '<button type="button" class="btn btn-primary"'+'onclick="pdfPP('+valor.idNuc+')">PDF</button>';

        table.row.add([valor.nuc,valor.client_name,valor.apertura,btnInvoice,btnPay,btn]).node().id = valor.idNuc;
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
        if(valor.mov_invoice == null)
            btnInvoice = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.idM+',1,3)" >Pendiente</button>'
        else
            btnInvoice = '<button href="#|" class="btn btn-success" onclick="seePDF('+valor.idM+',1,3)">'+valor.mov_invoice+'</button>'

        if(valor.mov_pay == null)
            btnPay = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.idM+',2,3)" >Sin Pago</button>'
        else
            btnPay = '<button href="#|" class="btn btn-success" onclick="seePDF('+valor.idM+',2,3)">'+valor.mov_pay+'</button>'

        btn = '<button type="button" class="btn btn-primary"'+'onclick="pdfPA('+valor.idM+')">PDF</button>';

        table.row.add([valor.nuc,valor.client_name,valor.apertura,valor.prev_balance,valor.new_balance,valor.currency,valor.amount,btnInvoice,btnPay,btn]).node().id = valor.idM;
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
        if(valor.lp_invoice == null)
            btnInvoice = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.idNuc+',1,4)" >Pendiente</button>'
        else
            btnInvoice = '<button href="#|" class="btn btn-success" onclick="seePDF('+valor.idNuc+',1,4)">'+valor.lp_invoice+'</button>'

        if(valor.lp_pay == null)
            btnPay = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.idNuc+',2,4)" >Sin Pago</button>'
        else
            btnPay = '<button href="#|" class="btn btn-success" onclick="seePDF('+valor.idNuc+',2,4)">'+valor.lp_pay+'</button>'

        table.row.add([valor.nuc,valor.client_name,valor.apertura,btnInvoice,btnPay]).node().id = valor.idNuc;
        idsLP.push(valor.idNuc);
    });
    table.draw(false);
    $("#comitionAll").val("13,500.00");
}

function updateRegime()
{
    var regime = $("#selectRegime").val();

    var route = baseUrl+"/UpdateRegime";
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'id':idUser,
        'regime':regime
    };
    jQuery.ajax({
        url:route,
        type:'post',
        data:data,
        dataType:'json',
        success:function(result)
        {
            alertify.success(result.message);
        }
    })
}

// ------------------------------------------------------------------------------------------------------------------- AUTH DATES
var flagtype = 0;
var userid = 0;

function setStatDate(id, type,authT)
{
    userid = id;
    flagtype = type;
    authType = authT;
    if(type == 1) document.getElementById("doc_row").style.display = "none";
    else
    {
        document.getElementById("doc_row").style.display = "block";

        if(authT == 1)
        {
            var route = baseUrl + '/GetPDFAuth/' + id + '/' + type + '/' + authT;

            jQuery.ajax({
                url:route,
                type:'get',
                dataType:'json',
                success:function(result)
                {
                    recMonth = result.mov.curr_month;
                    recYear = result.mov.curr_year;
                },
                error:function(result,error,errorTrown)
                {
                    alertify.error(errorTrown);
                }
            })
        }
    }
    $("#authModal").modal('show');
}

function seePDF(id, type, authT)
{
    userid = id;
    flagtype = type;
    authType = authT;
    var route = baseUrl + '/GetPDFAuth/' + id + '/' + type + '/' + authT;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            document.getElementById("viewPDF").href = getUrl.protocol + "//" + getUrl.host + result.route + result.doc;

            $("#viewPdfModal").modal('show');
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function setNullDate()
{
    if(authType == 1) var route = baseUrl + '/setNullDateRec';
    if(authType == 2) var route = baseUrl + '/setNullDate';
    if(authType == 3) var route = baseUrl + '/setNullDateMoves';
    if(authType == 4) var route = baseUrl + '/setNullDateLP';
    console.log(route);
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        "id":userid,
        "idUser":idUser,
        'flagtype':flagtype,
        'recMonth':recMonth,
        'recYear':recYear,
    }
    alertify.confirm("Cancelar fecha","¿Desea remover la fecha?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'post',
                dataType:'json',
                success:function(result){
                    alertify.success(result.message);
                    if(authType == 1) fillTableRec(result);
                    if(authType == 2) fillTablePP(result);
                    if(authType == 3) fillTablePA(result);
                    if(authType == 4) fillTableLP(result);
                    $("#viewPdfModal").modal('hide');
                },
                error:function(result,error,errorTrown)
                {
                    alertify.error(errorTrown);
                }
            })
        },
        function(){});
}

function guardarAuth()
{
    var formData = new FormData();
    var files = $('input[type=file]');
    for (var i = 0; i < files.length; i++) {
        if (files[i].value == "" || files[i].value == null)
        {
            // console.log(files.length);
            // return false;
        }
        else
        {
            formData.append(files[i].name, files[i].files[0]);
        }
    }

    var formSerializeArray = $("#Form").serializeArray();
    for (var i = 0; i < formSerializeArray.length; i++) {
        formData.append(formSerializeArray[i].name, formSerializeArray[i].value)
    }

    if(authType == 1) var route = baseUrl + '/setStatDateRec';
    if(authType == 2) var route = baseUrl + '/setStatDate';
    if(authType == 3) var route = baseUrl + '/setStatDateMoves';
    if(authType == 4) var route = baseUrl + '/setStatDateLP';
    if(authType == 5) var route = baseUrl + '/setStatDateMultLP';
    console.log(route);
    var date = $("#auth").val();
    var dateSet = $("#month").val();

    dateSet = dateSet.split("-");
    var year = dateSet[0];
    var month = dateSet[1];

    formData.append('_token', $("meta[name='csrf-token']").attr("content"));
    formData.append('id', userid);
    formData.append('flagtype', flagtype);
    formData.append('idUser', idUser);
    formData.append('date', date);
    if(authType != 1)
    {
        formData.append('year', year);
        formData.append('month', month);
    }
    else
    {
        formData.append('year', recYear);
        formData.append('month', recMonth);
    }

    jQuery.ajax({
        url:route,
        type:'post',
        data:formData,
        contentType: false,
        processData: false,
        cache: false,
        success:function(result)
        {
            // console.log("entre");
            alertify.success(result.message);
            if(authType == 1) fillTableRec(result);
            if(authType == 2) fillTablePP(result);
            if(authType == 3) fillTablePA(result);
            if(authType == 4 || authType == 5) fillTableLP(result);
            $("#authModal").modal('hide');
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

// ------------------------------------------------------------------------------------------------------------------- PRIMER PAGO
function pdfPP(id)
{
    var TC = $("#change").val();
    var date = $("#month").val();
    date = date.split("-");
    var year = date[0];
    var month = date[1];
    var regime = $("#selectRegime").val();
    if(TC == "" || date == "") alert ("Los campos de Tipo de cambio y Fecha no deben quedar vacíos");
    else
    {
        var route = baseUrlPP + '/ExportPDF/'+ id + "/" + month + "/" + year + "/"+ TC + "/" + regime;
        $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });

        var form = $('<form></form>');

        form.attr("method", "get");
        form.attr("action", route);
        form.attr('_token',$("meta[name='csrf-token']").attr("content"));
        $.each(function(key, value) {
            var field = $('<input></input>');
            field.attr("type", "hidden");
            field.attr("name", key);
            field.attr("value", value);
            form.append(field);
        });
        var field = $('<input></input>');
        field.attr("type", "hidden");
        field.attr("name", "_token");
        field.attr("value", $("meta[name='csrf-token']").attr("content"));
        form.append(field);
        $(document.body).append(form);
        form.submit();

        userid = id;
        authType = 2;
        flagtype = 1;
        document.getElementById("doc_row").style.display = "none";
        $("#authModal").modal('show');
    }
}

// ------------------------------------------------------------------------------------------------------------------- PAGO INCREMENTOS
function pdfPA(id)
{
    var TC = $("#change").val();
    var date = $("#month").val();
    date = date.split("-");
    var year = date[0];
    var month = date[1];
    var route = baseUrlPP + '/ExportPDFAll/'+ id + '/' + $("#change").val() + '/' + month  + '/' + year;
    // alert(route);
    if(TC == "" || date == "") alert ("Los campos de Tipo de cambio y Fecha no deben quedar vacíos");
    else
    {
        $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });

        var form = $('<form></form>');

        form.attr("method", "get");
        form.attr("action", route);
        form.attr('_token',$("meta[name='csrf-token']").attr("content"));
        $.each(function(key, value) {
            var field = $('<input></input>');
            field.attr("type", "hidden");
            field.attr("name", key);
            field.attr("value", value);
            form.append(field);
        });
        var field = $('<input></input>');
        field.attr("type", "hidden");
        field.attr("name", "_token");
        field.attr("value", $("meta[name='csrf-token']").attr("content"));
        form.append(field);
        $(document.body).append(form);
        form.submit();

        userid = id;
        authType = 3;
        flagtype = 1;
        document.getElementById("doc_row").style.display = "none";
        $("#authModal").modal('show');
    }
}

// ------------------------------------------------------------------------------------------------------------------- LARGO PLAZO
function CalculoMult()
{
    var comition = $("#comitionAll").val().replace(/[^0-9.]/g, '');
    var route = baseUrlLP + '/GetInfoAgents/1';

    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'comition':comition,
        'ids':idsLP
    }

    jQuery.ajax({
        url:route,
        data:data,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            if(result.agents.length != 1)
            {
                alert("No es posible realizar el cálculo seleccionando diferentes agentes.");
            }
            else
            {
                flagComition = 0;

                $("#selectRegimeAll").val(result.regime);
                $("#balanceAll").val(formatter.format(parseFloat(result.b_amount).toFixed(2)));
                $("#b_amountAll").val(formatter.format(parseFloat(result.gross_amount).toFixed(2)));
                $("#ivaAll").val(formatter.format(parseFloat(result.iva_amount).toFixed(2)));
                $("#ret_isrAll").val(formatter.format(parseFloat(result.ret_isr).toFixed(2)));
                $("#ret_ivaAll").val(formatter.format(parseFloat(result.ret_iva).toFixed(2)));
                $("#n_amountAll").val(formatter.format(parseFloat(result.n_amount).toFixed(2)));

                flagComition = 1;

                $("#myModalCalcAll").modal('show');
            }
        }
    })
}

function pdfLP()
{
    var comition = $("#comitionAll").val().replace(/[^0-9.]/g, '');
    var date = $("#month").val();
    var regime = $("#selectRegime").val();

    date = date.split("-");
    var year = date[0];
    var month = date[1];
    var ids = "";

    $.each(idsLP, function(index, rowId){
        ids += rowId.toString();
        if(idsLP.length-1 != index)
            ids += "-";
    });

    var route = baseUrlLP + '/ExportPDFAll/'+ ids + "/" + month + "/" + year + "/"+ comition + "/" + regime;

    if(date == "") alert ("El campo de fecha no deben quedar vacío");
    else
    {
        $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });

        var form = $('<form></form>');

        form.attr("method", "get");
        form.attr("action", route);
        form.attr('_token',$("meta[name='csrf-token']").attr("content"));
        $.each(function(key, value) {
            var field = $('<input></input>');
            field.attr("type", "hidden");
            field.attr("name", key);
            field.attr("value", value);
            form.append(field);
        });
        var field = $('<input></input>');
        field.attr("type", "hidden");
        field.attr("name", "_token");
        field.attr("value", $("meta[name='csrf-token']").attr("content"));
        form.append(field);
        $(document.body).append(form);
        form.submit();

        userid = 0;
        authType = 5;
        flagtype = 1;
        document.getElementById("doc_row").style.display = "none";
        $("#authModal").modal('show');
    }
}

// ------------------------------------------------------------------------------------------------------------------- RECURRENTE
function pdfRec(idUser, month, year)
{
    var TC = $("#change").val();
    var dlls = $("#dlls_com").val();
    var regime = $("#selectRegime").val();
    recMonth = month;
    recYear = year;

    if(TC == "") alert ("Ningun campo debe quedar vacio");
    else
    {
        var route = baseUrlRec + '/ExportPDF/'+ idUser + "/" + month + "/" + year + "/"+ TC + "/" + regime + "/" + dlls;

        $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });

        var form = $('<form></form>');

        form.attr("method", "get");
        form.attr("action", route);
        form.attr('_token',$("meta[name='csrf-token']").attr("content"));
        $.each(function(key, value) {
            var field = $('<input></input>');
            field.attr("type", "hidden");
            field.attr("name", key);
            field.attr("value", value);
            form.append(field);
        });
        var field = $('<input></input>');
        field.attr("type", "hidden");
        field.attr("name", "_token");
        field.attr("value", $("meta[name='csrf-token']").attr("content"));
        form.append(field);
        $(document.body).append(form);
        form.submit();

        userid = idUser;
        authType = 1;
        flagtype = 1;
        document.getElementById("doc_row").style.display = "none";
        $("#authModal").modal('show');
    }
}
