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
$(document).ready( function () {
    $('#tbassignBranches').DataTable({
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
    $('#tbassignPlans').DataTable({
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

function actualizarTablaBranch(result)
{
    var table = $('#tbassignBranches').DataTable();
    var assignBranch = $('#assignBranch');

    table.clear();

    result.assigned.forEach( function(valor, indice, array) {
        btnTrash = '<button type="button" class="btn btn-primary"'+'onclick="assignPlan('+valor.id+')">Asignar Planes</button> <button type="button" class="btn btn-danger"'+'onclick="deleteBranchAssign('+valor.id+')"><i class="fas fa-trash"></i></button>';
        table.row.add([valor.name,btnTrash]).node().id = valor.id;
    });

    table.draw(false);

    $("#assignBranch").empty();
    assignBranch.append('<option selected hidden value="0">Seleccione una opción</option>');
    result.branches.forEach( function(valor, indice, array) {
        assignBranch.append("<option value='" + valor.id + "'>" + valor.name + "</option>");
    });
}

function actualizarTablaPlans(result)
{
    var table = $('#tbassignPlans').DataTable();
    var assignPlan = $('#assignPlan');

    table.clear();

    result.assigned.forEach( function(valor, indice, array) {
        btnTrash = '<button type="button" class="btn btn-danger"'+'onclick="deletePlansAssign('+valor.id+')"><i class="fas fa-trash"></i></button>';
        table.row.add([valor.name,btnTrash]).node().id = valor.id;
    });

    table.draw(false);

    $("#assignPlan").empty();
    assignPlan.append('<option selected hidden value="0">Seleccione una opción</option>');
    result.plans.forEach( function(valor, indice, array) {
        assignPlan.append("<option value='" + valor.id + "'>" + valor.name + "</option>");
    });
}

function guardarAseguradora()
{
    var name = $("#name").val();
    var route = "insurances";
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'name':name
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
            window.location.reload(true);
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
    var route = "insurances/"+idupdate;
    var data = {
        'id':idupdate,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'name':name
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
            window.location.reload(true);
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
    alertify.confirm("Eliminar Aseguradora","¿Desea borrar a la aseguradora?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'delete',
                dataType:'json',
                success:function(result)
                {
                    window.location.reload(true);
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

idInsurance = 0;
function abrirBranches(id)
{
    idInsurance=id;

    var route = baseUrl + '/getBranches/'+ id;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            actualizarTablaBranch(result);
            $("#myModalBranches").modal('show');
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function cancelarBranches(id)
{
    $("#myModalBranches").modal('hide');
}

function assignBranches()
{
    var branch = $("#assignBranch").val();
    var route = baseUrl + '/saveBranch';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'insurance':idInsurance,
        'branch':branch
    };
    jQuery.ajax({
        url:route,
        type:"post",
        data: data,
        dataType: 'json',
        success:function(result)
        {
            alertify.success(result.message);
            actualizarTablaBranch(result);
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function deleteBranchAssign(id)
{
    var route = baseUrl + '/deleteBranch';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'insurance':idInsurance,
        'branch':id
    };
    alertify.confirm("Remover Ramo","¿Desea quitar la asignación?",
        function(){
            jQuery.ajax({
                url:route,
                type:"post",
                data: data,
                dataType: 'json',
                success:function(result)
                {
                    alertify.success(result.message);
                    actualizarTablaBranch(result);
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

function openNewBranch()
{
    $("#nameBranch").val("");
    $("#myModalNewBranch").modal('show');
}

function cancelarNewBranches()
{
    $("#myModalNewBranch").modal('hide');
}

function guardarRamo()
{
    var name = $("#nameBranch").val();
    var select_days = $("#select_days").val();
    var route = baseUrl + '/saveNewBranch';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'name':name,
        'insurance':idInsurance,
        'days':select_days
    };
    jQuery.ajax({
        url:route,
        type:"post",
        data: data,
        dataType: 'json',
        success:function(result)
        {
            alertify.success(result.message);
            actualizarTablaBranch(result);
            $("#myModalNewBranch").modal('hide');
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

idBranch = 0;
function assignPlan(id)
{
    idBranch = id;
    var route = baseUrl + '/getPlans/'+ id + '/' + idInsurance;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            actualizarTablaPlans(result);
            $("#myModalPlans").modal('show');
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function cancelarPlans()
{
    $("#myModalPlans").modal('hide');
}

function assignatePlans()
{
    var plan = $("#assignPlan").val();
    var route = baseUrl + '/savePlan';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'insurance':idInsurance,
        'branch':idBranch,
        'plan':plan
    };
    jQuery.ajax({
        url:route,
        type:"post",
        data: data,
        dataType: 'json',
        success:function(result)
        {
            alertify.success(result.message);
            actualizarTablaPlans(result);
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function deletePlansAssign(id)
{
    var route = baseUrl + '/deletePlan';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'insurance':idInsurance,
        'branch':idBranch,
        'plan':id
    };
    alertify.confirm("Remover Plan","¿Desea quitar la asignación?",
        function(){
            jQuery.ajax({
                url:route,
                type:"post",
                data: data,
                dataType: 'json',
                success:function(result)
                {
                    alertify.success(result.message);
                    actualizarTablaPlans(result);
                },
                error:function(result,error,errorTrown)
                {
                    alertify.error(errorTrown);
                }
            })
        },
        function(){
    });
}

function openNewPlan()
{
    $("#namePlan").val("");
    $("#myModalNewPlan").modal('show');
}

function cancelarNewPlan()
{
    $("#myModalNewPlan").modal('hide');
}

function guardarNewPlan()
{
    var name = $("#namePlan").val();
    var route = baseUrl + '/saveNewPlan';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'name':name,
        'insurance':idInsurance,
        'branch':idBranch
    };
    jQuery.ajax({
        url:route,
        type:"post",
        data: data,
        dataType: 'json',
        success:function(result)
        {
            alertify.success(result.message);
            actualizarTablaPlans(result);
            $("#myModalNewPlan").modal('hide');
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}
