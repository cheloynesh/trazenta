var ruta = window.location;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + getUrl.pathname;
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
    $('#tbProf1').DataTable({
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
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        iDisplayLength: 10
    });
} );

var formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  });

var idUser = 0;
var flagComition = 0;
var nucId = 0;
var active = 0;

function FillTable(data,profile,permission)
{
    var table = $('#tbProf').DataTable();
    var btnMov = '';
    var activeStat = '';
    table.clear();

    data.forEach( function(valor, indice, array) {
        btnMov = '<button type="button" class="btn btn-success" onclick="abrirResumen('+valor.nucid+')"><i class="fas fa-calculator"></i></button>';
        if(valor.paid == 0) activeStat = '<font color="red">Falta de pago</font>'; else activeStat = '<font color="green">Pagado</font>';
        table.row.add([valor.nucid,valor.usname,valor.clname,valor.nuc,activeStat,valor.pay_date,btnMov]).node().id = valor.nucid;
    });
    table.draw(false);
}

function abrirComision(id)
{
    idUser = id;
    var table = $('#tbProf1').DataTable();
    var route = baseUrl + '/GetInfo/'+ idUser;
    var btn;
    flagComition = 0;
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            if(result.regime == 0)
                $("#onoffRegime").bootstrapToggle('on');
            else
                $("#onoffRegime").bootstrapToggle('off');

            flagComition = 1;
            table.clear();
            result.data.forEach( function(valor, indice, array) {
                btn = '<button type="button" class="btn btn-success"'+'onclick="abrirResumen('+valor.idNuc+')"><i class="fas fa-calculator"></i></button>';
                table.row.add([valor.nuc,valor.client_name,btn]).node().id = valor.idNuc;
            });
            table.draw(false);
        }
    })
    $("#myModal2").modal('show');
}
function cancelarComision()
{
    $("#myModal2").modal('hide');
}

function calcular()
{
    var comition = $("#comition").val().replace(/[^0-9.]/g, '');
    var date = $("#month").val();
    var reg = $("#onoffRegime").prop('checked');
    var regime = 0;

    if(reg)
        regime = 1;
    else
        regime = 0;

    date = date.split("-");
    var year = date[0];
    var month = date[1];

    var route = baseUrl + '/ExportPDF/'+ nucId + "/" + month + "/" + year + "/"+ comition + "/" + regime;

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
}

function abrirResumen(idNuc)
{
    nucId = idNuc;
    flagComition = 0;

    $("#comition").val("13,500.00");
    var comition = $("#comition").val().replace(/[^0-9.]/g, '');
    var date = $("#month").val();
    // var reg = $("#onoffRegime").prop('checked');
    // var regime = 0;
    // if(reg)
    //     regime = 1;
    // else
    //     regime = 0;

    date = date.split("-");
    var year = date[0];
    var month = date[1];
    var data = {
    "_token": $("meta[name='csrf-token']").attr("content"),
        'comition':comition,
        'id':idNuc,
        'year':year,
        'month':month,
    }

    var route = baseUrl+'/GetInfoComition';
    jQuery.ajax({
        url:route,
        data:data,
        type:'post',
        dataType:'json',
        success:function(result){
            // alert(result.regime);
            $("#selectRegime").val(result.regime);
            $("#balance").val(formatter.format(parseFloat(result.b_amount).toFixed(2)));
            $("#b_amount").val(formatter.format(parseFloat(result.gross_amount).toFixed(2)));
            $("#iva").val(formatter.format(parseFloat(result.iva_amount).toFixed(2)));
            $("#ret_isr").val(formatter.format(parseFloat(result.ret_isr).toFixed(2)));
            $("#ret_iva").val(formatter.format(parseFloat(result.ret_iva).toFixed(2)));
            $("#n_amount").val(formatter.format(parseFloat(result.n_amount).toFixed(2)));

            idUser = result.idUser;
            if(result.regime == 0)
                $("#onoffRegime").bootstrapToggle('on');
            else
                $("#onoffRegime").bootstrapToggle('off');

            flagComition = 1;
            if(result.paid == 0)
            {
                document.getElementById("paybtn").hidden = false;
                document.getElementById("cancelbtn").hidden = true;
            }
            else
            {
                document.getElementById("paybtn").hidden = true;
                document.getElementById("cancelbtn").hidden = false;
            }
            // obtenerSaldo(idNuc);
            $("#myModalCalc").modal('show');
        }
    });
}
function cancelarCalc(modal)
{
    $(modal).modal('hide');
}

var arraySaldo = [];
function obtenerSaldo(id)
{
        // llenar los campos con los calculos correspondientes como en la imagen

    arraySaldo = [];
    var date = $("#month").val();
    date = date.split("-");
    var year = date[0];
    var month = date[1];
    var route = baseUrl + '/GetInfoMonth/'+ id + "/" + month + "/" + year;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            if(Object.keys(result.data).length)
            {
                console.log("no null");
            }
            else
            {
                console.log("null");
                obtenerultimomovimiento(id,month,year);
                // aqui retornar array con valores
                // return(saldo obtenido)
                // aqui vamos a hacer otra consulta para obtener el ultimo movimiento
            }
        }
    })
}

function obtenerultimomovimiento(id,month,year)
{
    var route = baseUrl + '/GetInfoLast/'+ id + "/" + month + "/" + year;
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            console.log(result.data);
        }
    })
}

function updateRegime(selectreg)
{
    if(flagComition != 0)
    {
        var regime = $(selectreg).val();

        var route = baseUrl+"/UpdateRegime";
        var data = {
            "_token": $("meta[name='csrf-token']").attr("content"),
            'id':idUser,
            'regime':regime
        };
        jQuery.ajax({
            url:route,
            type:'put',
            data:data,
            dataType:'json',
            success:function(result)
            {
                fillCalc()
                alertify.success(result.message);
            }
        })
    }
}

function fillCalc()
{
    if($("#comition").val() == '')
    {
        alert("Llena todos los campos");
    }
    else
    {
        flagComition = 0;
        var comition = $("#comition").val().replace(/[^0-9.]/g, '');
        var date = $("#month").val();

        date = date.split("-");
        var year = date[0];
        var month = date[1];
        var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
            'comition':comition,
            'id':nucId,
            'year':year,
            'month':month
        }

        var route = baseUrl+'/GetInfoComition';
        jQuery.ajax({
            url:route,
            data:data,
            type:'post',
            dataType:'json',
            success:function(result){
                // alert(result.regime);
                $("#balance").val(formatter.format(parseFloat(result.b_amount).toFixed(2)));
                $("#b_amount").val(formatter.format(parseFloat(result.gross_amount).toFixed(2)));
                $("#iva").val(formatter.format(parseFloat(result.iva_amount).toFixed(2)));
                $("#ret_isr").val(formatter.format(parseFloat(result.ret_isr).toFixed(2)));
                $("#ret_iva").val(formatter.format(parseFloat(result.ret_iva).toFixed(2)));
                $("#n_amount").val(formatter.format(parseFloat(result.n_amount).toFixed(2)));

                flagComition = 1;
                // obtenerSaldo(idNuc);
            }
        });
    }
}

function pay(paym)
{
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'id':nucId,
        'pay':paym,
        'active':active
    }

    var route = baseUrl+'/SetPayment';

    alertify.confirm("Modificar Estatus","¿Desea modificar el estatus de pago?",
        function(){
            jQuery.ajax({
                url:route,
                data:data,
                type:'post',
                dataType:'json',
                success:function(result){
                    alertify.success(result.message);
                    // obtenerSaldo(idNuc);
                    FillTable(result.users);
                    $("#myModalCalc").modal('hide');
                    // window.location.reload(true);
                }
            });
        },
        function(){
            alertify.error('Cancelado');
    });
}

function CalculoMult()
{
    var table = $('#tbProf').DataTable();
    var rows_selected = table.column(0).checkboxes.selected();
    var ids = [];

    $.each(rows_selected, function(index, rowId){
        ids.push(rowId);
    });

    if(ids.length == 0)
    {
        alert("No hay casillas seleccionadas.");
    }
    else
    {
        var route = baseUrl + '/GetInfoAgents/1';
        $("#comitionAll").val("13,500.00");
        var comition = $("#comitionAll").val().replace(/[^0-9.]/g, '');

        var data = {
            "_token": $("meta[name='csrf-token']").attr("content"),
            'comition':comition,
            'ids':ids
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
}

function chkActive()
{
    if (document.getElementById('chkActive').checked) active = 1; else active = 0;

    var route = baseUrl + '/GetComitions/'+active;
    // alert(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            FillTable(result.users);
        }
    })
}

function fillCalcAll()
{
    if($("#comitionAll").val() == '')
    {
        alert("Llena todos los campos");
    }
    else
    {
        var table = $('#tbProf').DataTable();
        var rows_selected = table.column(0).checkboxes.selected();
        var ids = [];

        $.each(rows_selected, function(index, rowId){
            ids.push(rowId);
        });

        flagComition = 0;
        var comition = $("#comitionAll").val().replace(/[^0-9.]/g, '');

        var data = {
            "_token": $("meta[name='csrf-token']").attr("content"),
            'comition':comition,
            'ids':ids
        }

        var route = baseUrl + '/GetInfoAgents/1';
        jQuery.ajax({
            url:route,
            data:data,
            type:'get',
            dataType:'json',
            success:function(result){
                flagComition = 0;

                $("#balanceAll").val(formatter.format(parseFloat(result.b_amount).toFixed(2)));
                $("#b_amountAll").val(formatter.format(parseFloat(result.gross_amount).toFixed(2)));
                $("#ivaAll").val(formatter.format(parseFloat(result.iva_amount).toFixed(2)));
                $("#ret_isrAll").val(formatter.format(parseFloat(result.ret_isr).toFixed(2)));
                $("#ret_ivaAll").val(formatter.format(parseFloat(result.ret_iva).toFixed(2)));
                $("#n_amountAll").val(formatter.format(parseFloat(result.n_amount).toFixed(2)));

                if(result.regime == 0)
                    $("#onoffRegimeAll").bootstrapToggle('on');
                else
                    $("#onoffRegimeAll").bootstrapToggle('off');

                flagComition = 1;
            }
        });
    }
}

function calcularAll()
{
    var table = $('#tbProf').DataTable();
    var rows_selected = table.column(0).checkboxes.selected();
    var ids = "";

    $.each(rows_selected, function(index, rowId){
        ids += rowId.toString();
        if(rows_selected.length-1 != index)
            ids += "-";
    });

    var comition = $("#comitionAll").val().replace(/[^0-9.]/g, '');
    var date = $("#monthAll").val();
    var reg = $("#onoffRegimeAll").prop('checked');
    var regime = 0;

    if(reg)
        regime = 1;
    else
        regime = 0;

    date = date.split("-");
    var year = date[0];
    var month = date[1];

    var route = baseUrl + '/ExportPDFAll/'+ ids + "/" + month + "/" + year + "/"+ comition + "/" + regime;

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
}

function payAll(paym)
{
    var table = $('#tbProf').DataTable();
    var rows_selected = table.column(0).checkboxes.selected();
    var ids = [];

    $.each(rows_selected, function(index, rowId){
        ids.push(rowId);
    });

    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'ids':ids,
        'pay':paym,
        'active':active
    }

    var route = baseUrl+'/SetPaymentAll';

    alertify.confirm("Modificar Estatus","¿Desea modificar el estatus de pago?",
        function(){
            jQuery.ajax({
                url:route,
                data:data,
                type:'post',
                dataType:'json',
                success:function(result){
                    alertify.success(result.message);
                    // obtenerSaldo(idNuc);
                    FillTable(result.users);
                    $("#myModalCalcAll").modal('hide');
                    // window.location.reload(true);
                }
            });
        },
        function(){
            alertify.error('Cancelado');
    });
}
