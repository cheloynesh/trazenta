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
        order: [[4, 'asc']],
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

    var route = baseUrl + '/ExportPDF/'+ idUser + "/" + month + "/" + year + "/"+ comition + "/" + regime;

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
    idUser = idNuc;
    $("#comition").val("13,500.00");
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
    var data = {
    "_token": $("meta[name='csrf-token']").attr("content"),
        'comition':comition,
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
            // alert(result.regime);
            $("#balance").val(formatter.format(parseFloat(result.b_amount).toFixed(2)));
            $("#b_amount").val(formatter.format(parseFloat(result.gross_amount).toFixed(2)));
            $("#iva").val(formatter.format(parseFloat(result.iva_amount).toFixed(2)));
            $("#ret_isr").val(formatter.format(parseFloat(result.ret_isr).toFixed(2)));
            $("#ret_iva").val(formatter.format(parseFloat(result.ret_iva).toFixed(2)));
            $("#n_amount").val(formatter.format(parseFloat(result.n_amount).toFixed(2)));

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

function fillCalc()
{
    if($("#change").val() == '' || $("#month").val() == '')
    {
        alert("Llena todos los campos");
    }
}

function pay(paym)
{
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'id':idUser,
        'pay':paym
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
                    $("#myModalCalc").modal('hide');
                    window.location.reload(true);
                }
            });
        },
        function(){
            alertify.error('Cancelado');
    });
}
