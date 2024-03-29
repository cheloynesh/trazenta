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
            },
            buttons: {
                colvis: 'Mostrar/Ocultar',
            }
        },
        dom: 'Blfrtip',
        buttons: [
            'excel',
            {
                extend: 'colvis',
                collectionLayout: 'fixed columns',
                collectionTitle: 'Mostrar/Ocultar',
                postfixButtons: [ 'colvisRestore' ]
            }
        ]
    });
} );

document.addEventListener("DOMContentLoaded", function () {

    // var route = baseUrl + '/GetInfo/'+ 1;

    // jQuery.ajax({
    //     url:route,
    //     type:'get',
    //     dataType:'json',
    //     success:function(result)
    //     {
    //         fillCharts(result.insurances, result.branches, result.status, result.pay);
    //     }
    // })

    var today = new Date();
    var assignPlan = $("#selectYear");
    $("#selectYear").empty();
    assignPlan.append('<option selected hidden value="1">Seleccione una opción</option>');
    for(cont = 2000; cont <= today.getFullYear(); cont++)
        assignPlan.append("<option value='" + cont + "'>" + cont + "</option>");
    assignPlan.append('<option selected hidden value="1">Todos</option>');
    $("#selectYear").val(today.getFullYear());
    // GetFilters();
});

function cancelar(modal)
{
    $(modal).modal("hide");
}

function abrirColumnas(modal)
{
    $(modal).modal("show");
}

function QuarterChange()
{
    $("#month").val("%");
    GetFilters();
}
function MonthChange()
{
    $("#selectQuarter").val("%");
    GetFilters();
}

function GetFilters()
{
    var route = baseUrl + '/GetInfoFilters/'+ 1;
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'month':$("#month").val(),
        'quarter':$("#selectQuarter").val(),
        'year':$("#selectYear").val(),
        'fund':$("#selectFund").val()
    };
    var table = $('#tbProf').DataTable();

    jQuery.ajax({
        url:route,
        type:'get',
        data: data,
        dataType:'json',
        success:function(result)
        {
            table.clear();
            result.moneyflow.forEach( function(valor, indice, array) {
                table.row.add([valor.AgentName,valor.NCP,valor.NLP,valor.ToN,valor.OCP,valor.OLP,valor.ToO,valor.RCP,valor.RLP,valor.ToR]).node().id = valor.AgentId;
            });
            table.draw(false);
        }
    })
}

function downloadBreakdown(id,type)
{
    var month = $("#month").val();
    var quarter = $("#selectQuarter").val();
    var fund = $("#selectFund").val();
    var year = $("#selectYear").val();
    if(month == '%') month = "a"; else month = month;
    if(quarter == '%') quarter = "a"; else quarter = quarter;
    if(fund == '%') fund = "a"; else fund = fund;
    var route = baseUrl + '/ExportBreakdown/' + id + '/' + month + '/' + quarter + '/' + year + '/' + fund + '/' + type;
    // alert(route);
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
