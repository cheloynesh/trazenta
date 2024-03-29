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
$(document).ready( function () {
    $('#tbContract').DataTable({
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

idAssign = 0;
idsFstYr = "";
idsScndYr = "";
flag = 0;

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
        buttonCalc = '<button type="button" class="btn btn-success"'+'onclick="abrirResumen('+valor.id+')"><i class="fas fa-calculator"></i></button>';
        table.row.add([valor.name, valor.email, buttonCalc + " " + buttonTrash]).node().id=valor.id;
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

function abrirResumen(idNuc)
{
    var TC = $("#change").val();
    var date = $("#month").val();

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
                $("#b_amount").val(result.b_amount);
                $("#dll_conv").val(result.dll_conv);
                $("#usd_invest").val(result.usd_invest);
                $("#usd_invest1").val(result.usd_invest1);
                $("#seventy").val(result.seventy);
                $("#thirty").val(result.thirty);

                // obtenerSaldo(idNuc);
                $("#myModalCalc").modal('show');
            }
        });
    }

}

function GetPDFAll()
{
    var TC = $("#change").val();
    var date = $("#month").val();

    date = date.split("-");
    var year = date[0];
    var month = date[1];

    if(date == null || date == "" && TC == null || TC == "")
    {
        alert("Ningun campo debe quedar vacio");
        return false;
    }else
    {
        var route = baseUrl + '/GetPDF/' + idAssign + '/' + year + '/' + month + '/' + TC + '/' + idsFstYr + '/' + idsScndYr;

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

function GetPDF()
{
    var route = baseUrl + '/GetSixMonth/' + idAssign + '/1';
    // console.log(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            FillTableSixMonth(result.data);
            $("#sixMonthText").html("Pago Primer Año");
            $("#sixMonthModal").modal('show');
            flag = 0;
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function continueContract()
{
    var table = $('#tbContract').DataTable();

    if(flag == 0)
    {
        var rows_selected = table.column(0).checkboxes.selected();
        idsFstYr = "";

        if(rows_selected.length == 0)
        {
            idsFstYr = "0";
        }
        else
        {
            $.each(rows_selected, function(index, rowId){
                idsFstYr = idsFstYr+rowId
                if(index != rows_selected.length-1) idsFstYr = idsFstYr + "-";
            });
            table.columns().checkboxes.deselect(true);
        }

        var route = baseUrl + '/GetSixMonth/' + idAssign + '/2';
        // console.log(route);
        jQuery.ajax({
            url:route,
            type:'get',
            dataType:'json',
            success:function(result){
                FillTableSixMonth(result.data);
                $("#sixMonthText").html("Pago Segundo Año");
                $("#sixMonthModal").modal('show');
                flag = 1;
            },
            error:function(result,error,errorTrown)
            {
                alertify.error(errorTrown);
            }
        })
    }
    else
    {
        var rows_selected = table.column(0).checkboxes.selected();
        idsScndYr = "";

        if(rows_selected.length == 0)
        {
            idsScndYr = "0";
        }
        else
        {
            $.each(rows_selected, function(index, rowId){
                idsScndYr = idsScndYr+rowId
                if(index != rows_selected.length-1) idsScndYr = idsScndYr + "-";
            });
            table.columns().checkboxes.deselect(true);
        }

        GetPDFAll();
    }
}

function FillTableSixMonth(data,profile,permission)
{
    var table = $('#tbContract').DataTable();
    var btnFst = '';
    var btnScnd = '';
    table.clear();

    data.forEach( function(valor, indice, array) {
        if(valor.fst_yr == null) btnFst = '<font color="red">Falta de pago</font>'; else btnFst = '<font color="green">Pagado</font>';

        if(valor.scnd_yr == null) btnScnd = '<font color="red">Falta de pago</font>'; else btnScnd = '<font color="green">Pagado</font>';

        table.row.add([valor.nucid,valor.usname,valor.clname,valor.nuc,btnFst,btnScnd]).node().id = valor.nucid;
    });
    table.draw(false);
}
