var rutaAsignacion = window.location;
var getUrlAsignacion = window.location;
var baseUrlAsignacion = getUrlAsignacion .protocol + "//" + getUrlAsignacion.host + getUrlAsignacion.pathname;
$(document).ready( function () {
    $('#tableClients').DataTable({
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
    $('#tbClient').DataTable({
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
var idAgent = 0;
var button = "";
var idNuc = 0;
var fundNuc = 0;

function FillTable(result)
{
    var table = $("#tableClients").DataTable();
    table.clear();
    result.data.forEach(function(valor, indice, array){
        button = '<button type="button" class="btn btn-danger" onclick=delete_code_edit('+ valor.id +',"'+ valor.fund_type +'",'+ valor.idAgent +')><i class="fas fa-trash"></i></button>';
        table.row.add([valor.nuc, valor.fund_type, valor.cname, button]).node().id=valor.id;
    })
    table.draw(false);
}

function FillTableNonAsigned(result)
{
    var table = $("#tableClients").DataTable();
    table.clear();
    result.data.forEach(function(valor, indice, array){
        button = '<button type="button" class="btn btn-primary" onclick=assignmodal('+ valor.id +',"'+ valor.fund_type +'")>Asignar</button>';
        table.row.add([valor.nuc, valor.fund_type, valor.cname, button]).node().id=valor.id;
    })
    table.draw(false);
}

function assignment(id)
{
    idAgent=id;
    var route = baseUrlAsignacion + '/Viewclients/'+id;
    // console.log(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            FillTable(result);
        }
    })
    $("#assigmentModal").modal('show');
}
function nonAssigned()
{
    var route = baseUrlAsignacion + '/ViewNonAssigned/1';
    // console.log(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            FillTableNonAsigned(result);
        }
    })
    $("#assigmentModal").modal('show');
}
function cerrarmodal(modal)
{
    $(modal).modal('hide');
}

// function assignclient()
// {
//     var client = $("#selectAgent").val();
//     // alert(client);
//     var route = baseUrlAsignacion+'/updateClient';
//     var data = {
//         "_token": $("meta[name='csrf-token']").attr("content"),
//         "id":idAgent,
//         "client":client
//     }
//     jQuery.ajax({
//         url:route,
//         data: data,
//         type:'post',
//         dataType:'json',
//         success:function(result){
//             alertify.success(result.message);
//             $("#assigmentModal").modal('hide');
//             window.location.reload(true);
//         }
//     })
// }
function delete_code_edit(id,fund,idAgent)
{
    var route = "assigment/"+id;
    var data = {
        'id':id,
        'fund':fund,
        'idAgent':idAgent,
        "_token": $("meta[name='csrf-token']").attr("content"),
    };
    alertify.confirm("Eliminar asignación","¿Desea borrar la asignación del Cliente?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'delete',
                dataType:'json',
                success:function(result)
                {
                    // window.location.reload(true);
                    FillTable(result);
                    alertify.success(result.message);
                }
            })
        },
        function(){
            alertify.error('Cancelado');
    });
}
function assignmodal(id, fund)
{
    document.getElementById('btnAssign').disabled = true;
    idNuc = id;
    fundNuc = fund;
    $("#selectAgentmodal").val(0);
    $("#agentsModal").modal('show');
}
function changeagent()
{
    if($("#selectAgentmodal").val() != 0)
    {
        document.getElementById('btnAssign').disabled = false;
    }
}
function assign()
{
    idAgent = $("#selectAgentmodal").val();
    var route = baseUrlAsignacion+'/updateClient';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        "agent":idAgent,
        "nuc":idNuc,
        "fund":fundNuc
    }
    jQuery.ajax({
        url:route,
        data: data,
        type:'post',
        dataType:'json',
        success:function(result){
            alertify.success(result.message);
            FillTableNonAsigned(result);
            $("#agentsModal").modal('hide');
        }
    })
}
