var ruta = window.location;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + getUrl.pathname;
$(document).ready(function () {
    var table = $('#example').DataTable({
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
    });
});

$(document).ready( function () {
    $('#srcNuc').DataTable({
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
});

nucTxtbox = "";
idnuc = 0;
idupdate = 0;
idStatus = 0;
servType = 0;

function FillTable(data,profile,permission)
{
    var table = $('#example').DataTable();
    var btnStat = '';
    var btnEdit = '';
    var btnTrash = '';
    table.clear();

    data.forEach( function(valor, indice, array) {
        btnStat = '<button class="btn btn-info" style="color: #'+valor.font_color+'; background-color: #'+valor.color+'; border-color: #'+valor.border_color+'" onclick="opcionesEstatus('+valor.sid+')">'+valor.name+'</button>';
        btnStatAg = '<button class="btn btn-info" style="color: #'+valor.intFont+'; background-color: #'+valor.intColor+'; border-color: #'+valor.intBorder+'" onclick="opcionesEstatusAgente('+valor.sid+')">'+valor.intName+'</button>';
        btnEdit = '<button href="#|" class="btn btn-warning" onclick="editarApertura('+valor.sid+')" ><i class="fas fa-edit"></i></button>';
        btnTrash = '<button href="#|" class="btn btn-danger" onclick="eliminarApertura('+valor.sid+')"><i class="fa fa-trash"></i></button>';

        if (profile != 12)
        {
            if(valor.intId == 6 && valor.finestra_status == null)
            {
                btnStatInt = '<td><button class="btn btn-info" style="color: #ffffff; background-color: #e98c46; border-color: #e98c46" onclick="opcionesEstatusInt('+valor.sid+')">Pend. Finestra</button></td>';

            }
            else
            {
                btnStatInt = '<td><button class="btn btn-info" style="color: #'+valor.intFont+'; background-color: #'+valor.intColor+'; border-color: #'+valor.intBorder+'" onclick="opcionesEstatusInt('+valor.sid+')">'+valor.intName+'</button></td>'
            }
            if(permission["erase"] == 1)
                table.row.add([valor.agent,valor.folio,valor.nuc,valor.cname,valor.servname,valor.mnt,btnStat,btnStatAg,btnStatInt,btnEdit+" "+btnTrash]).node().id = valor.oid;
            else
                table.row.add([valor.agent,valor.folio,valor.nuc,valor.cname,valor.servname,valor.mnt,btnStat,btnStatAg,btnStatInt,btnEdit]).node().id = valor.oid;
        }
        else
        {
            if(permission["erase"] == 1)
                table.row.add([valor.agent,valor.folio,valor.nuc,valor.cname,valor.servname,valor.mnt,btnStat,btnStatAg,btnStatInt,btnEdit+" "+btnTrash]).node().id = valor.sid;
            else
                table.row.add([valor.agent,valor.folio,valor.nuc,valor.cname,valor.servname,valor.mnt,btnStat,btnStatAg,btnStatInt,btnEdit]).node().id = valor.sid;
        }
    });
    table.draw(false);
}

function nuevoServicio()
{
    idClient = 0;
    $("#nuc_edit").val("");
    $("#amount").val(0);
    document.getElementById('btnSrcNuc').disabled = true;
    document.getElementById("amountDiv").style.display = "none";
    $("#myModal").modal('show');
}

function fundChange()
{
    if($("#selectFund").val() != 0)
    {
        document.getElementById('btnSrcNuc').disabled = false;
    }
}

function buscarnuc(flag){
    if(flag == 0)
    {
        nucTxtbox = "#nuc_edit";
        fund = $("#selectFund").val();
    }
    else
    {
        nucTxtbox = "#nuc_edit1";
        fund = $("#selectFund1").val();
    }

    var route = baseUrl + '/GetFunds/'+ fund;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            var table = $('#srcNuc').DataTable();
            var btnOpt = '';
            table.clear();

            result.data.forEach( function(valor, indice, array) {
                btnOpt = '<button type="button" class="btn btn-primary" onclick="obtenerid('+valor.nucid+',`'+valor.cname+'`,`'+valor.nuc+'`)">Seleccionar</button>';

                table.row.add([valor.cname,valor.nuc,btnOpt]).node().id = valor.nucid;
            });
            table.draw(false);
            $("#modalSrcNuc").modal("show");
        }
    })
}

function cerrarModal(modl)
{
    $(modl).modal("hide");
}

function obtenerid(nucid,cname,nuc)
{
    idnuc = nucid;
    $(nucTxtbox).val(nuc + " - " + cname);
    $("#modalSrcNuc").modal("hide");
}

function guardarServicio()
{
    var fund = $("#selectFund").val();
    var fk_nuc = idnuc;
    var fk_service_type = $("#selectService").val();
    var type = $("#selectType").val();
    var folio = $("#folio").val();
    var amount = $("#amount").val().replace(/[^0-9.]/g, '');

    var route = "services";
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fund':fund,
        'fk_nuc':fk_nuc,
        'fk_service_type':fk_service_type,
        'folio':folio,
        'amount':amount,
        'type':type
    };
    jQuery.ajax({
        url:route,
        type:"post",
        data: data,
        dataType: 'json',
        success:function(result)
        {
            alertify.success(result.message);
            FillTable(result.services,result.profile,result.permission);
            $("#myModal").modal('hide');
        }
    })

}

function editarApertura(id)
{
    idupdate=id;
    var route = baseUrl + '/GetInfo/'+ id;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            idnuc = result.data.fk_nuc;
            $("#selectFund1").val(result.data.fund);
            $("#nuc_edit1").val(result.data.nuc + " - " + result.data.cname);
            $("#selectService1").val(result.data.fk_service_type);
            $("#selectType1").val(result.data.type);
            $("#selectDelivered1").val(result.data.delivered);
            $("#amount1").val(parseFloat(result.data.amount).toLocaleString('en-US'));
            $("#folio1").val(result.data.folio);

            if(result.data.fk_service_type == 5 || result.data.fk_service_type == 6)
            {
                document.getElementById("amountDiv1").style.display = "";
            }
            else
            {
                document.getElementById("amountDiv1").style.display = "none";
            }

            $("#myModaledit").modal('show');
        }
    })
}

function actualizarServicio()
{
    var fund = $("#selectFund1").val();
    var fk_nuc = idnuc;
    var fk_service_type = $("#selectService1").val();
    var type = $("#selectType1").val();
    var delivered = $("#selectDelivered1").val();
    var folio = $("#folio1").val();
    var amount = $("#amount1").val().replace(/[^0-9.]/g, '');

    var route = "services/"+idupdate;
    var data = {
        'id':idupdate,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fund':fund,
        'fk_nuc':fk_nuc,
        'fk_service_type':fk_service_type,
        'type':type,
        'folio':folio,
        'amount':amount,
        'delivered':delivered
    };
    jQuery.ajax({
        url:route,
        type:'put',
        data:data,
        dataType:'json',
        success:function(result)
        {
            alertify.success(result.message);
            FillTable(result.services,result.profile,result.permission);
            $("#myModaledit").modal('hide');
        }
    })
}

function opcionesEstatus(id)
{
    idupdate=id;
    var route = baseUrl + '/GetInfoStatus/'+ id;
    console.log(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            idStatus = result.data.fk_status;
            servType = result.data.fk_service_type;
            $("#selectStatus").val(idStatus);
            if(idStatus == 7 && (servType == 5 || servType == 6))
            {
                document.getElementById("divDates").style.display = "";
                $("#selectTime").val(result.data.paytime);
                $("#attendDate").val(result.data.attend_date);
                $("#payDate").val(result.data.pay_date);
                if(result.profile != 12)
                {
                    document.getElementById('selectStatus').disabled = false;
                    document.getElementById('selectTime').disabled = false;
                    document.getElementById('attendDate').disabled = false;
                    document.getElementById('payDate').disabled = false;
                }
                else
                {
                    document.getElementById('selectStatus').disabled = true;
                    document.getElementById('selectTime').disabled = true;
                    document.getElementById('attendDate').disabled = true;
                    document.getElementById('payDate').disabled = true;
                }
            }
            else
            {
                document.getElementById("divDates").style.display = "none";
            }

            $("#myModalStatus").modal('show');
        }
    })
}

function statusChange()
{
    if(($("#selectStatus").val() == 7 && servType == 5) || ($("#selectStatus").val() == 7 && servType == 6))
    {
        var now = new Date();
        var today = now.getFullYear()+"-"+(("0" + (now.getMonth() + 1)).slice(-2))+"-"+(("0" + now.getDate()).slice(-2));
        $("#attendDate").val(today);
        var dateplus = addWorkDays(now, $("#selectTime").val());
        var todayplus = dateplus.getFullYear()+"-"+(("0" + (dateplus.getMonth() + 1)).slice(-2))+"-"+(("0" + dateplus.getDate()).slice(-2));
        $("#payDate").val(todayplus);

        document.getElementById("divDates").style.display = "";
    }
    else
    {
        document.getElementById("divDates").style.display = "none";
    }
}

function dateChange()
{
    var date = $("#attendDate").val();
    date = date.split('-');
    var dateplus = new Date(date[0], date[1]-1, date[2], 0, 0, 0);

    dateplus = addWorkDays(dateplus, $("#selectTime").val());
    var todayplus = dateplus.getFullYear()+"-"+(("0" + (dateplus.getMonth() + 1)).slice(-2))+"-"+(("0" + dateplus.getDate()).slice(-2));
    $("#payDate").val(todayplus);
}

function addWorkDays(startDate, days)
{
    var daysToAdd = parseInt(days);
    i=0;
    while (i<daysToAdd)
    {
        startDate.setTime(startDate.getTime()+24*60*60*1000); // añadimos 1 día
        if (startDate.getDay() != 6 && startDate.getDay() != 0)
            i++;
    }
    return startDate;
}

function actualizarEstatus()
{
    var fk_status = $("#selectStatus").val();
    var paytime = $("#selectTime").val();
    var attend_date = $("#attendDate").val();
    var pay_date = $("#payDate").val();

    var route = baseUrl+"/updateStatus";
    var data = {
        'id':idupdate,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fk_status':fk_status,
        'paytime':paytime,
        'attend_date':attend_date,
        'pay_date':pay_date
    };
    jQuery.ajax({
        url:route,
        type:'post',
        data:data,
        dataType:'json',
        success:function(result)
        {
            alertify.success(result.message);
            FillTable(result.services,result.profile,result.permission);
            $("#myModalStatus").modal('hide');
        }
    })
}

function eliminarApertura(id)
{
    var route = "services/"+id;
    var data = {
            'id':id,
            "_token": $("meta[name='csrf-token']").attr("content"),
    };
    alertify.confirm("Eliminar Servicio","¿Desea borrar el Servicio?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'delete',
                dataType:'json',
                success:function(result)
                {
                    FillTable(result.services,result.profile,result.permission);
                    alertify.success('Eliminado');
                }
            })
        },
        function(){
            alertify.error('Cancelado');
    });
}

function showAmount(select, div, txt)
{
    if($(select).val() == 5 || $(select).val() == 6)
    {
        document.getElementById(div).style.display = "";
    }
    else
    {
        document.getElementById(div).style.display = "none";
    }
    $(txt).val(0);
}

idStatus = 0;
function opcionesEstatusInt(id)
{
    idStatus=id;
    var route = baseUrl+'/GetInfoStatus/'+idStatus;
    console.log(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            $("#selectStatusInt").val(result.data.intern_status);
            fillStatusDates(result.data.pick_status,"Pick");
            fillStatusDates(result.data.limit_status,"Limit");
            fillStatusDates(result.data.office_status,"Office");
            fillStatusDates(result.data.finestra_status,"Finestra");
            $("#myModalStatusInt").modal('show');
        }
    })
}

function fillStatusDates(date,status)
{
    if(date == null)
    {
        if(status != "Limit")
            $("#selectStatus"+status).val(1);
        document.getElementById("divDate"+status).style.display = "none";
    }
    else
    {
        if(status != "Limit")
            $("#selectStatus"+status).val(2);
        document.getElementById("divDate"+status).style.display = "";
    }
    $("#auth"+status).val(date);
}

function changeLimit()
{
    var date = $("#authPick").val();
    date = date.split('-');
    var dateplus = new Date(date[0], date[1]-1, date[2], 0, 0, 0);

    dateplus = addWorkDays(dateplus, 10);

    var day = dateplus.toLocaleString("default", { day: "2-digit" });
    var year = dateplus.toLocaleString("default", { year: "numeric" });
    var month = dateplus.toLocaleString("default", { month: "2-digit" });

    $("#authLimit").val(year + "-" + month + "-" + day);
}

function save()
{
    var route = baseUrl+"/updateStatusInt";
    var intern_status = $("#selectStatusInt").val();
    var pick_status = $("#authPick").val();
    var limit_status = $("#authLimit").val();
    var office_status = $("#authOffice").val();
    var finestra_status = $("#authFinestra").val();

    var data = {
        'id':idStatus,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'intern_status':intern_status,
        'pick_status':pick_status,
        'limit_status':limit_status,
        'office_status':office_status,
        'finestra_status':finestra_status
    };
    jQuery.ajax({
        url:route,
        type:'post',
        data:data,
        dataType:'json',
        success:function(result)
        {
            alertify.success(result.message);
            FillTable(result.services,result.profile,result.permission);
            $("#myModalStatusInt").modal('hide');
        }
    })
}

function showDate(div)
{
    if ($("#selectStatus"+div).val() == 2)
    {
        document.getElementById("divDate"+div).style.display = "";
        if(div == "Pick")
        {
            document.getElementById("divDateLimit").style.display = "";
        }
    }
    else
    {
        document.getElementById("divDate"+div).style.display = "none";
        $("#auth"+div).val(null);
        if(div == "Pick")
        {
            document.getElementById("divDateLimit").style.display = "none";
            $("#authLimit").val(null);
        }
    }
}

function opcionesEstatusAgente(id)
{
    idStatus=id;

    var route = baseUrl+'/GetInfoStatus/'+idStatus;
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            $("#selectStatus1").val(result.data.intern_status);
            fillStatusDatesAgent(result.data.pick_status,"Pick1");
            fillStatusDatesAgent(result.data.limit_status,"Limit1");
            fillStatusDatesAgent(result.data.office_status,"Office1");
            $("#myModalStatusAgent").modal('show');
        }
    })
}

function fillStatusDatesAgent(date,status)
{
    if(date == null)
    {
        $("#auth"+status).val("PENDIENTE");
        if(status == "Pick1")
        {
            document.getElementById("divLimit").style.display = "none";
        }
    }
    else
    {
        $("#auth"+status).val(date);
        if(status == "Pick1")
        {
            document.getElementById("divLimit").style.display = "";
        }
    }
}

function addWorkDays(startDate, days)
{
    var daysToAdd = parseInt(days);
    i=0;
    while (i<daysToAdd)
    {
        startDate.setTime(startDate.getTime()+24*60*60*1000); // añadimos 1 día
        if (startDate.getDay() != 6 && startDate.getDay() != 0)
            i++;
    }
    return startDate;
}
