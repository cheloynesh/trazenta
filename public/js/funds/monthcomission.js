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
            // alert(result.regime)
            if(result.regime.regime == 0)
                $("#onoffRegime").bootstrapToggle('on');
            else
                $("#onoffRegime").bootstrapToggle('off');

            $("#dlls_com").val(result.regime.dlls);

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
    var TC = $("#change").val();
    var dlls = $("#dlls_com").val();
    var date = $("#month").val();
    var reg = $("#onoffRegime").prop('checked');
    var regime = 0;

    if(TC == "" || date == "") alert ("Ningun campo debe quedar vacio");
    else
    {
        if(reg)
            regime = 1;
        else
            regime = 0;

        date = date.split("-");
        var year = date[0];
        var month = date[1];

        var route = baseUrl + '/ExportPDF/'+ idUser + "/" + month + "/" + year + "/"+ TC + "/" + regime + "/" + dlls;

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
        flagtype = 1;
        $("#authModal").modal('show');
    }
}

function abrirResumen(idNuc)
{
    var TC = $("#change").val();
    var date = $("#month").val();
    var dlls = $("#dlls_com").val();
    var reg = $("#onoffRegime").prop('checked');
    var regime = 0;
    if(reg)
        regime = 1;
    else
        regime = 0;

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
            'id':idNuc,
            'year':year,
            'month':month,
            'regime':regime,
            'dlls':dlls
        }

        var route = baseUrl+'/GetInfoComition';
        jQuery.ajax({
            url:route,
            data:data,
            type:'post',
            dataType:'json',
            success:function(result){
                // alert(result.regime);
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
        // alert(route);
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

function updateDlls()
{
    var dlls = $("#dlls_com").val();

    var route = baseUrl+"/updateDlls";
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'id':idUser,
        'dlls':dlls
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

var flagtype = 0;
var userid = 0;

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
                    RefreshTable(result.users);
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
        'flagtype':flagtype,
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
            RefreshTable(result.users);
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function cerrarAuth()
{
    $("#authModal").modal('hide');
}

function RefreshTable(data,profile,permission)
{
    var table = $('#tbProf').DataTable();
    var btnInvoice = '';
    var btnPay = '';
    var btnComition = '';

    table.clear();
    data.forEach( function(valor, indice, array) {
        if(valor.invoice_flag == null)
            btnInvoice = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.uid+',1)" >Pendiente</button>'
        else
            btnInvoice = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.uid+',1)">'+valor.invoice_flag+'</button>'

        if(valor.pay_flag == null)
            btnPay = '<button href="#|" class="btn btn-danger" onclick="setStatDate('+valor.uid+',2)" >Sin Pago</button>'
        else
            btnPay = '<button href="#|" class="btn btn-success" onclick="setNullDate('+valor.uid+',2)">'+valor.pay_flag+'</button>'

        btnComition = '<a href="#|" class="btn btn-primary" onclick="abrirComision('+valor.uid+')" >Cálculo de Comision</a>';

        table.row.add([valor.uname,btnInvoice,btnPay,btnComition]).node().id = valor.uid;
    });
    table.draw(false);
}
