var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + getUrl.pathname;

$(document).ready( function () {
    $('#tbProfInsurance').DataTable({
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

function refreshTable(charges)
{
    var table = $('#tbProfInsurance').DataTable();
    var type = {0 : "APORTACIONES",1: "CAPORTA",2: "OBCREDA",3: "OBCREDA DE OCCIDENTE",4: "OBDENA",null: "-"};
    console.log(charges);
    table.clear();
    charges.forEach( function(valor, indice, array) {
        btnEdit = '<button href="#|" class="btn btn-warning" onclick="editarAseguradora('+valor.id+')" ><i class="fas fa-edit"></i></button>';
        btnTrash = '<button href="#|" class="btn btn-danger" onclick="eliminarAseguradora('+valor.id+')"><i class="fa fa-trash"></i></button>';
        table.row.add([valor.name,valor.fund_type,valor.fund_curr,valor.yield,valor.yield_net,type[valor.type],valor.active_fund == 1 ? "Si" : "No",btnEdit+" "+btnTrash]);
    });
    table.draw(false);
}

function guardarAseguradora()
{
    var name = $("#name").val();
    var fund_type = $("#selectType").val();
    var fund_curr = $("#selectCurr").val();
    var yield = $("#yield").val();
    var yield_net = $("#yield_net").val();
    var active_fund = $("#selectActive").val();
    var type = $("#selectFType").val();
    var route = "insurances";
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'name':name,
        'fund_type':fund_type,
        'fund_curr':fund_curr,
        'yield':yield,
        'yield_net':yield_net,
        'active_fund':active_fund,
        'type':type,
    };
    jQuery.ajax({
        url:route,
        type:"post",
        data: data,
        dataType: 'json',
        success:function(result)
        {
            alertify.success(result.message);
            $("#myModal").modal('hide');
            refreshTable(result.funds);
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}
var idupdate = 0;
function editarAseguradora(id)
{
    idupdate=id;

    var route = baseUrl + '/GetInfo/'+ id;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            $("#name1").val(result.data.name);
            $("#selectType1").val(result.data.fund_type);
            $("#selectCurr1").val(result.data.fund_curr);
            $("#yield1").val(result.data.yield);
            $("#yield_net1").val(result.data.yield_net);
            $("#selectActive1").val(result.data.active_fund);
            $("#selectFType1").val(result.data.type);
            $("#myModaledit").modal('show');
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}
function cancelarEditar()
{
    $("#name1").val("");
    $("#myModaledit").modal('hide');
}
function actualizarAseguradora()
{
    var name = $("#name1").val();
    var fund_type = $("#selectType1").val();
    var fund_curr = $("#selectCurr1").val();
    var yield = $("#yield1").val();
    var yield_net = $("#yield_net1").val();
    var active_fund = $("#selectActive1").val();
    var type = $("#selectFType1").val();
    var route = "insurances/"+idupdate;
    var data = {
        'id':idupdate,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'name':name,
        'fund_type':fund_type,
        'fund_curr':fund_curr,
        'yield':yield,
        'yield_net':yield_net,
        'active_fund':active_fund,
        'type':type,
    };
    jQuery.ajax({
        url:route,
        type:'put',
        data:data,
        dataType:'json',
        success:function(result)
        {
            alertify.success(result.message);
            $("#myModaledit").modal('hide');
            refreshTable(result.funds);
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function eliminarAseguradora(id)
{
    var route = "insurances/"+id;
    var data = {
            'id':id,
            "_token": $("meta[name='csrf-token']").attr("content"),
    };
    alertify.confirm("Eliminar Fondo","¿Desea borrar el fondo?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'delete',
                dataType:'json',
                success:function(result)
                {
                    refreshTable(result.funds);
                },
                error:function(result,error,errorTrown)
                {
                    alertify.error(errorTrown);
                }
            })
            alertify.success('Eliminado');
        },
        function(){
            alertify.error('Cancelado');
    });

}
