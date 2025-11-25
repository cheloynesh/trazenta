var ruta = window.location;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + getUrl.pathname;
var baseUrlRec = getUrl .protocol + "//" + getUrl.host + "/funds/monthlycomission/monthcomission";
var baseUrlPP = getUrl .protocol + "//" + getUrl.host + "/funds/fstmonthcomission/fstmonthcomission";
var baseUrlLP = getUrl .protocol + "//" + getUrl.host + "/funds/sixmonthlycomission/sixmonthcomission";


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
    $('#tbRecps').DataTable({
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
        order: [[5, 'asc']],
        'columnDefs': [
            {
               'targets': 0,
               'checkboxes': {
                  'selectRow': true
               }
            }
         ],
         'select': {
            'style': 'multi'
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
var docname = "";

function fillTables(result,invoice,contpp,contpa,lpnopay)
{
    if(invoice != "NA")
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
    table.clear();
    result.rec.forEach( function(valor, indice, array) {
        btn = '<button type="button" class="btn btn-success"'+'onclick="abrirResumen('+valor.idNuc+')"><i class="fas fa-calculator"></i></button>';
        table.row.add([valor.nuc,valor.client_name,btn]).node().id = valor.idNuc;
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
            btnInvoice = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.idNuc+',1,2)">'+valor.fst_invoice+'</button>'

        if(valor.fst_pay == null)
            btnPay = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.idNuc+',2,2)" >Sin Pago</button>'
        else
            btnPay = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.idNuc+',2,2)">'+valor.fst_pay+'</button>'

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
            btnInvoice = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.idM+',1,3)">'+valor.mov_invoice+'</button>'

        if(valor.mov_pay == null)
            btnPay = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.idM+',2,3)" >Sin Pago</button>'
        else
            btnPay = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.idM+',2,3)">'+valor.mov_pay+'</button>'

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
            btnInvoice = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.idNuc+',1,4)">'+valor.lp_invoice+'</button>'

        if(valor.lp_pay == null)
            btnPay = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.idNuc+',2,4)" >Sin Pago</button>'
        else
            btnPay = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.idNuc+',2,4)">'+valor.lp_pay+'</button>'

        table.row.add([valor.nuc,valor.client_name,valor.apertura,btnInvoice,btnPay]).node().id = valor.idNuc;
        idsLP.push(valor.idNuc);
    });
    table.draw(false);
    $("#comitionAll").val("13,500.00");
}

function fillTableComition(result)
{
    var table = $('#tbProf').DataTable();
    var btnInvoice = '';
    var btnPay = '';
    var btn = '';

    table.clear();
    result.coms.forEach( function(valor, indice, array) {
        if(valor.invoice_flag == null)
            btnInvoice = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.uid+',1,1)" >Pendiente</button>'
        else if (valor.invoice_flag == "NA")
            btnInvoice = '<a>N/A</a>'
        else
            btnInvoice = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.uid+',1,1)">'+valor.invoice_flag+'</button>'

        if(valor.pay_flag == null)
            btnPay = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.uid+',2,1)" >Sin Pago</button>'
        else if (valor.pay_flag == "NA")
            btnPay = '<a>N/A</a>'
        else
            btnPay = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.uid+',2,1)">'+valor.pay_flag+'</button>'

        btn = '<a href="#|" class="btn btn-primary" onclick="abrirComision('+valor.uid+',`'+valor.invoice_flag+'`,'+valor.contpp+','+valor.contpa+','+valor.lpnopay+')" >Cálculo</a>';

        table.row.add([valor.aname,btnInvoice,btnPay,valor.contpp,valor.contpa,valor.lpnopay,btn]).node().id = valor.uid;
    });
    table.draw(false);
}

function abrirComision(id,invoice,contpp,contpa,lpnopay)
{
    idUser = id;
    if(invoice == "") invoice = "pend";
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
            if(invoice != "NA") document.getElementById("cardRec").style.display = "block";
            if(contpp != 0)
            {
                document.getElementById("cardNC").style.display = "block";
                $("#fst_month").val(result.regime.fst_month);
                $("#five_month").val(result.regime.five_month);
            }
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
var TCAux = 0;
var dllsAux = 0;
var monthaux = 0;
var yearaux = 0;
var regimeAux = 0;

function setStatDate(id, type,authT)
{
    userid = id;
    flagtype = type;
    authType = authT;
    if(type == 1) document.getElementById("doc_row").style.display = "none";
    else document.getElementById("doc_row").style.display = "block";
    $("#authModal").modal('show');
}

function setNullDate(id, type, authT)
{
    if(authT == 1) var route = baseUrlRec + '/setNullDate';
    if(authT == 2) var route = baseUrl + '/setNullDate';
    if(authT == 3) var route = baseUrl + '/setNullDateMoves';
    if(authT == 4) var route = baseUrl + '/setNullDateLP';
    console.log(route);
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        "id":id,
        "idUser":idUser,
        'flagtype':type,
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
                    if(authT == 1) fillTableComition(result);
                    if(authT == 2) fillTablePP(result);
                    if(authT == 3) fillTablePA(result);
                    if(authT == 4) fillTableLP(result);
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

    if(authType == 1) var route = baseUrlRec + '/setStatDate';
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
    formData.append('year', year);
    formData.append('month', month);
    formData.append('TC', TCAux);
    formData.append('dlls', dllsAux);
    formData.append('monthaux', monthaux);
    formData.append('yearaux', yearaux);
    formData.append('regime', regimeAux);
    // formData.append('docname', docname);

    jQuery.ajax({
        url:route,
        type:'post',
        data:formData,
        contentType: false,
        processData: false,
        cache: false,
        success:function(result)
        {
            alertify.success(result.message);
            if(authType == 1) fillTableComition(result);
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

// ------------------------------------------------------------------------------------------------------------------- RECURRENTE
function abrirResumen(nucid)
{
    var TC = $("#change").val();
    var date = $("#month").val();
    var dlls = $("#dlls_com").val();
    var regime = $("#selectRegime").val();

    date = date.split("-");
    var year = date[0];
    var month = date[1];

    if(date == null || date == "" && TC == null || TC == "")
    {
        alert("Ningun campo debe quedar vacio");
        return false;
    }else
    {
        var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
            'TC':TC,
            'id':nucid,
            'year':year,
            'month':month,
            'regime':regime,
            'dlls':dlls
        }

        var route = baseUrlRec+'/GetInfoComition';
        jQuery.ajax({
            url:route,
            data:data,
            type:'post',
            dataType:'json',
            success:function(result){
                // alert(result.regime);
                $("#balance_rec").val(result.b_amount);
                $("#b_amount_rec").val(result.gross_amount);
                $("#iva_rec").val(result.iva_amount);
                $("#ret_isr_rec").val(result.ret_isr);
                $("#ret_iva_rec").val(result.ret_iva);
                $("#n_amount_rec").val(result.n_amount);

                // obtenerSaldo(idNuc);
                $("#myModalCalcRec").modal('show');
            }
        });
    }
}

function pdfRec()
{
    var TC = $("#change").val();
    var dlls = $("#dlls_com").val();
    var date = $("#month").val();
    var regime = $("#selectRegime").val();

    if(TC == "" || date == "") alert ("Ningun campo debe quedar vacio");
    else
    {
        date = date.split("-");
        var year = date[0];
        var month = date[1];

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
        TCAux = TC;
        dllsAux = dlls;
        monthaux = month;
        yearaux = year;
        regimeAux = regime;
        authType = 1;
        flagtype = 1;
        document.getElementById("doc_row").style.display = "none";
        $("#authModal").modal('show');
    }
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
        // docname = 'FST_'+ id + "_" + month + "_" + year + ".pdf";
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
        // docname = 'INC_'+ id + "_" + month + "_" + year + ".pdf";
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
        // docname = 'LP_'+ ids + "_" + month + "_" + year + ".pdf";
    }
}

// ------------------------------------------------- Mailing ------------------------------------------------
mailingtype = 0;
function abrir(modal,type)
{
    var route = baseUrl + '/GetInfoMailing/'+type;
    mailingtype = type;

    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
    }

    jQuery.ajax({
        url:route,
        data:data,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            var table = $('#tbRecps').DataTable();

            table.clear();
            result.data.forEach( function(valor, indice, array) {
                contrec = valor.contrec != 0 ? '<p><a onclick="viewPrev('+ valor.uid +',1,'+ type +')" class="btn btn-success">'+valor.contrec+'</a></p>' : valor.contrec;
                contpp = valor.contpp != 0 ? '<p><a onclick="viewPrev('+ valor.uid +',2,'+ type +')" class="btn btn-success">'+valor.contpp+'</a></p>' : valor.contpp;
                contpa = valor.contpa != 0 ? '<p><a onclick="viewPrev('+ valor.uid +',3,'+ type +')" class="btn btn-success">'+valor.contpa+'</a></p>' : valor.contpa;
                lpnopay = valor.lpnopay != 0 ? '<p><a onclick="viewPrev('+ valor.uid +',4,'+ type +')" class="btn btn-success">'+valor.lpnopay+'</a></p>' : valor.lpnopay;
                table.row.add([valor.uid,valor.aname,contrec,contpp,contpa,lpnopay]).node().uid = valor.uid;
            });
            table.draw(false);
            $(modal).modal('show');
        }
    })
}

function viewPrev(id, fund, type)
{
    var route = baseUrl + '/GetInfoDocs/' + id  + '/' + fund  + '/' + type;

    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
    }

    jQuery.ajax({
        url:route,
        data:data,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            result.data.forEach( function(valor, indice, array) {
                console.log(getUrl.protocol + "//" + getUrl.host + valor);
                window.open(getUrl.protocol + "//" + getUrl.host + valor, "_blank");
            });
        }
    })
}

function sendMail()
{
    var route = baseUrl + '/sendMailing';

    var table = $('#tbRecps').DataTable();
    var rows_selected = table.column(0).checkboxes.selected();
    var ids = [];

    $.each(rows_selected, function(index, rowId){
        ids.push(rowId);
    });

    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        "type":mailingtype,
        "ids":ids,
    }

    alertify.confirm("Enviar correos","¿Desea enviar los correos?",
        function(){
            $("#waitModal").modal('show');
            $('#waitModal').one('shown.bs.modal', function() {
                $("#recpModal").modal('hide');
                jQuery.ajax({
                    url:route,
                    data: data,
                    type:'post',
                    dataType:'json',
                    success:function(result){
                        $("#waitModal").modal('hide');
                        alertify.success(result.message);
                    },
                    error:function(result,error,errorTrown)
                    {
                        alertify.error(errorTrown);
                        $("#waitModal").modal('hide');
                        $("#recpModal").modal('show');
                    }
                })
            });
        },
        function(){});
}

function updateFstPays()
{
    var route = baseUrl + '/UpdateFstPays';
    console.log(route);
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        "idUser":idUser,
        'fst_month':$("#fst_month").val(),
        'five_month':$("#five_month").val(),
    }
    jQuery.ajax({
        url:route,
        data: data,
        type:'post',
        dataType:'json',
        success:function(result){
            alertify.success(result.message);
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}
