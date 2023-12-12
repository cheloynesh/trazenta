var ruta = window.location;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + getUrl.pathname;
$(document).ready( function () {
    $('#tableNoLeader').DataTable({
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
    $('#tableAgents').DataTable({
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
    $('#tableLeaders').DataTable({
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

idAssign = 0;

function FillTable(data,tbl,func,text)
{
    var table = $(tbl).DataTable();
    table.clear();
    data.forEach(function(valor, indice, array){
        button = '<button type="button" class="btn btn-primary" onclick='+func+'('+ valor.id +')>'+text+'</button>';
        table.row.add([valor.name, valor.email, button]).node().id=valor.id;
    })
    table.draw(false);
}

function FillTableLeaders(data)
{
    var table = $("#tableLeaders").DataTable();
    table.clear();
    data.forEach(function(valor, indice, array){
        buttonView = '<button type="button" class="btn btn-primary" onclick=viewAgents('+ valor.id +')>Ver Agentes</button>';
        button = '<button type="button" class="btn btn-primary" onclick=assignment('+ valor.id +')>Asignar</button>';
        buttonTrash = '<button type="button" class="btn btn-danger" onclick=deleteLeader('+ valor.id +')><i class="fas fa-trash"></i></button>';
        table.row.add([valor.name, valor.email, buttonView + " " + button + " " + buttonTrash]).node().id=valor.id;
    })
    table.draw(false);
}

function FillTableAgents(data)
{
    var table = $("#tableAgents").DataTable();
    table.clear();
    data.forEach(function(valor, indice, array){
        buttonTrash = '<button type="button" class="btn btn-danger" onclick=deleteAgent('+ valor.id +')><i class="fas fa-trash"></i></button>';
        table.row.add([valor.name, valor.email, buttonTrash]).node().id=valor.id;
    })
    table.draw(false);
}

function noLeader()
{
    var route = baseUrl + '/ViewNonLeader/1';
    // console.log(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            FillTable(result.data, "#tableNoLeader","dessign","Designar");
            $("#leaderModal").modal('show');
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function dessign(id)
{
    var route = baseUrl + '/Dessign';
    var data = {
        'id':id,
        "_token": $("meta[name='csrf-token']").attr("content"),
    };
    alertify.confirm("Designar Líder","¿Desea designar este usuario como líder?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'post',
                dataType:'json',
                success:function(result)
                {
                    // window.location.reload(true);
                    FillTable(result.noleader, "#tableNoLeader","dessign","Designar");
                    FillTableLeaders(result.leader);
                    alertify.success(result.message);
                },
                error:function(result,error,errorTrown)
                {
                    alertify.error(errorTrown);
                }
            })
        },
        function(){
            alertify.error('Cancelado');
    });
}

function closeModal(modal)
{
    $(modal).modal('hide');
}

function assignment(id)
{
    idAssign = id;
    var route = baseUrl + '/ViewNonAssigned/1';
    // console.log(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            FillTable(result.data, "#tableAgents","assign","Asignar");
            $("#agentsModal").modal('show');
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function assign(id)
{
    var route = baseUrl + '/Assign';
    var data = {
        'id':idAssign,
        'agent':id,
        "_token": $("meta[name='csrf-token']").attr("content"),
    };
    alertify.confirm("Asignar Agente","¿Desea asignar este agente?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'post',
                dataType:'json',
                success:function(result)
                {
                    // window.location.reload(true);
                    FillTable(result.data, "#tableAgents","assign","Asignar");
                    alertify.success(result.message);
                },
                error:function(result,error,errorTrown)
                {
                    alertify.error(errorTrown);
                }
            })
        },
        function(){
            alertify.error('Cancelado');
    });
}

function viewAgents(id)
{
    idAssign = id;
    var route = baseUrl + '/ViewAssigned/' + id;
    // console.log(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            FillTableAgents(result.data);
            $("#agentsModal").modal('show');
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function deleteLeader(id)
{
    var route = baseUrl + '/DeleteLeader';
    var data = {
        'id':id,
        "_token": $("meta[name='csrf-token']").attr("content"),
    };
    alertify.confirm("Eliminar Líder","¿Desea remover este usuario como líder?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'post',
                dataType:'json',
                success:function(result)
                {
                    FillTableLeaders(result.leader);
                    alertify.success(result.message);
                },
                error:function(result,error,errorTrown)
                {
                    alertify.error(errorTrown);
                }
            })
        },
        function(){
            alertify.error('Cancelado');
    });
}

function deleteAgent(id)
{
    var route = baseUrl + '/DeleteAgent';
    var data = {
        'id':idAssign,
        'agent':id,
        "_token": $("meta[name='csrf-token']").attr("content"),
    };
    alertify.confirm("Eliminar Agente","¿Desea remover este agente?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'post',
                dataType:'json',
                success:function(result)
                {
                    FillTableAgents(result.data);
                    alertify.success(result.message);
                },
                error:function(result,error,errorTrown)
                {
                    alertify.error(errorTrown);
                }
            })
        },
        function(){
            alertify.error('Cancelado');
    });
}
