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
        order: [[1, 'desc']]
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
            [25, 50, 100, 200, -1],
            [25, 50, 100, 200, "All"]
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

function RefreshTable(data)
{
    var table = $('#tbProf1').DataTable();
    var btn;
    var btnPay;
    console.log(data);
    table.clear();
    data.forEach( function(valor, indice, array) {
        if(valor.contpa != 0)
            btn = '<button type="button" class="btn btn-success"'+'onclick="abrirIncrementos('+valor.idNuc+')">Incrementos</button>&nbsp;<button type="button" class="btn btn-success"'+'onclick="calcular('+valor.idNuc+')">Primer Pago</button>';
        else
            btn = '<button type="button" class="btn btn-success"'+'onclick="calcular('+valor.idNuc+')">Primer Pago</button>';
        if(valor.fst_pay == null)
            btnPay = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.idNuc+',2)" >Sin Pago</button>'
        else
            btnPay = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.idNuc+',2)">'+valor.fst_pay+'</button>'
        table.row.add([valor.nuc,valor.apertura,valor.client_name,valor.contpam,btnPay,btn]).node().id = valor.idNuc;
    });
    table.draw(false);
}

function abrirComision(id)
{
    idUser = id;
    var route = baseUrl + '/GetInfo/'+ idUser;
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
            RefreshTable(result.data);
        }
    })
    $("#myModal2").modal('show');
}

function setStatDate(id, type)
{
    userid = id;
    flagtype = type;
    $("#authModal").modal('show');
}

function setNullDate(id, type)
{
    var route = baseUrl + '/setNullDate';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        "id":id,
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
                    RefreshTable(result.data);
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
    var route = baseUrl + '/setStatDate';
    var date = $("#auth").val();
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        "id":userid,
        'date':date
    }
    jQuery.ajax({
        url:route,
        data: data,
        type:'post',
        dataType:'json',
        success:function(result){
            alertify.success(result.message);
            $("#authModal").modal('hide');
            RefreshTable(result.data);
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function cancelarComision()
{
    $("#myModal2").modal('hide');
}
function calcular(id)
{
    var TC = $("#change").val();
    var date = $("#month").val();
    date = date.split("-");
    var year = date[0];
    var month = date[1];
    var reg = $("#onoffRegime").prop('checked');
    var regime = 0;
    if(TC == "" || date == "") alert ("Los campos de Tipo de cambio y Fecha no deben quedar vacíos");
    else
    {
        if(reg)
            regime = 1;
        else
            regime = 0;

        var route = baseUrl + '/ExportPDF/'+ id + "/" + month + "/" + year + "/"+ TC + "/" + regime;
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
}

function calcularAll(id)
{
    var TC = $("#change").val();
    var date = $("#month").val();
    var route = baseUrl + '/ExportPDFAll/'+ id + '/' + $("#change").val();
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
    }
}

function abrirResumen(idNuc)
{
    var TC = $("#change").val();
    var date = $("#month").val();

    date = date.split("-");
    var year = date[0];
    var month = date[1];
    var reg = $("#onoffRegime").prop('checked');
    var regime = 0;
    if(reg)
        regime = 1;
    else
        regime = 0;

    if(date == null || date == "" && TC == null || TC == "")
    {
        alert("Los campos de Tipo de cambio y Fecha no deben quedar vacíos");
        return false;
    }else
    {
        var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
            'TC':TC,
            'id':idNuc,
            'year':year,
            'month':month,
            'regime':regime
        }

        var route = baseUrl+'/GetInfoComition';
        jQuery.ajax({
            url:route,
            data:data,
            type:'post',
            dataType:'json',
            success:function(result){
                console.log(result);
                $("#balance").val(result.b_amount);
                $("#b_amount").val(result.gross_amount);
                $("#iva").val(result.iva_amount);
                $("#ret_isr").val(result.ret_isr);
                $("#ret_iva").val(result.ret_iva);
                $("#n_amount").val(result.n_amount);

                // obtenerSaldo(idNuc);
                $("#myModalCalc").modal('show');
            }
        });
    }
}
function cancelarCalc()
{
    $("#myModalCalc").modal('hide');
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

function updateRegime()
{
    if(flagComition != 0)
    {
        var reg = $("#onoffRegime").prop('checked');
        var regime = 0;
        if(reg)
            regime = 0;
        else
            regime = 1;

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
                alertify.success(result.message);
            }
        })
    }
}
idincremento = 0;
function abrirIncrementos(id)
{
    idincremento = id;
    var table = $('#tbProfaug').DataTable();
    var route = baseUrl + '/GetInfoAugments/'+ id;
    var button = 'Autorizado';
    var btnTrash;

    table.clear();

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            table.clear();
            result.data.forEach( function(valor, indice, array) {
                if(valor.auth == "-")
                {
                    button = '<button href="#|" class="btn btn-primary" onclick="autorizarMovimiento('+valor.id+')" >Autorizar</button>';
                }
                else
                {
                    button = valor.auth;
                }
                btnTrash = '<button type="button" class="btn btn-danger"'+'onclick="calcularAll('+valor.id+')"><i class="fas fa-calculator"></i></button>';
                table.row.add([valor.apply_date,button,formatter.format(valor.prev_balance),formatter.format(valor.new_balance),
                    valor.currency,formatter.format(valor.amount),valor.type,btnTrash]).node().id = valor.id;
            });
            table.draw(false);
        }
    })
    $("#myModalMoves").modal('show');
}

function cerrarIncrementos()
{
    $("#myModalMoves").modal('hide');
}

