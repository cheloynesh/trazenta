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
        // "columnDefs": [{
        //     "targets": [6,7,8],
        //     "visible": false
        //     // "searchable": false
        // }]
        // scrollY: '200px',
        // paging: false,
    });

    // $('a.toggle-vis').on('click', function (e) {
    //     e.preventDefault();

    //     // Get the column API object
    //     var column = table.column($(this).attr('data-column'));

    //     // Toggle the visibility
    //     column.visible(!column.visible());
    // });
});
$(document).ready( function () {
    $('#srcClient').DataTable({
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
        }
    });
} );

idClient = 0;
fund_type = "";
yieldr = 0;
yield_usd = 0;
charge_moves = [];

function FillTable(data,profile,permission)
{
    var table = $('#example').DataTable();
    var btnStatAg = '';
    var btnStat = '';
    var btnEdit = '';
    var btnTrash = '';
    table.clear();

    data.forEach( function(valor, indice, array) {
        btnStatAg = '<button class="btn btn-info" style="color: #'+valor.font_color+'; background-color: #'+valor.color+'; border-color: #'+valor.border_color+'" onclick="opcionesEstatusAgente('+valor.oid+')">'+valor.name+'</button>';
        btnEdit = '<button href="#|" class="btn btn-warning" onclick="editarApertura('+valor.oid+')" ><i class="fas fa-edit"></i></button>';
        btnTrash = '<button href="#|" class="btn btn-danger" onclick="eliminarApertura('+valor.oid+')"><i class="fa fa-trash"></i></button>';
        if (profile != 12)
        {
            if(valor.statid == 6 && valor.finestra_status == null)
            {
                btnStat = '<td><button class="btn btn-info" style="color: #ffffff; background-color: #e98c46; border-color: #e98c46" onclick="opcionesEstatus('+valor.oid+')">Pend. Finestra</button></td>';

            }
            else
            {
                btnStat = '<td><button class="btn btn-info" style="color: #'+valor.font_color+'; background-color: #'+valor.color+'; border-color: #'+valor.border_color+'" onclick="opcionesEstatus('+valor.oid+')">'+valor.name+'</button></td>'
            }
            if(permission["erase"] == 1)
                table.row.add([valor.agent,valor.cname,valor.insurance,valor.fund_type,valor.nuc,valor.limit_status,btnStatAg,btnStat,btnEdit+" "+btnTrash]).node().id = valor.oid;
            else
                table.row.add([valor.agent,valor.cname,valor.insurance,valor.fund_type,valor.nuc,valor.limit_status,btnStatAg,btnStat,btnEdit]).node().id = valor.oid;
        }
        else
        {
            if(permission["erase"] == 1)
                table.row.add([valor.agent,valor.cname,valor.insurance,valor.fund_type,valor.nuc,valor.limit_status,btnStatAg,btnEdit+" "+btnTrash]).node().id = valor.oid;
            else
                table.row.add([valor.agent,valor.cname,valor.insurance,valor.fund_type,valor.nuc,valor.limit_status,btnStatAg,btnEdit]).node().id = valor.oid;
        }
    });
    table.draw(false);
}

function nuevaApertura()
{
    idClient = 0;
    document.getElementById("divCP").style.display = "none";
    document.getElementById("divLP").style.display = "none";
    $("#client_edit").val("");
    var table = $('#tbProf1').DataTable();
    table.clear();
    table.draw(false);
    charge_moves = [];
    $("#myModal").modal('show');
}

function guardarApertura()
{
    var fk_agent = $("#selectAgent").val();
    var name = $("#name").val();
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var birth_date = $("#birth_date").val();
    var rfc = $("#rfc").val();
    var curp = $("#curp").val();
    var cellphone = $("#cellphone").val();
    var email = $("#email").val();
    var fk_insurance = $("#selectInsurance").val();
    var fk_currency = $("#selectCurrency").val();
    var fk_application = $("#selectAppli").val();
    var fk_payment_form = $("#selectPaymentform").val();
    // var fk_charge = $("#selectCharge").val();
    var domicile = $("#address").val();
    var nuc = $("#nuc").val();
    var amount = $("#amount").val().replace(/[^0-9.]/g, '');
    var initial_date = $("#initial_date").val();
    var onoff = document.getElementById("onoff");
    var checked = onoff.checked;
    var reinvest = 2;
    if(checked)
    {
        reinvest = 1;
    }

    var route = "opening";
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fk_agent':fk_agent,
        'fk_client':idClient,
        'name':name,
        'firstname':firstname,
        'lastname':lastname,
        'birth_date':birth_date,
        'rfc':rfc,
        'curp':curp,
        'cellphone':cellphone,
        'email':email,
        'fk_insurance':fk_insurance,
        'selectCurrency':fk_currency,
        'fk_application':fk_application,
        'fk_payment_form':fk_payment_form,
        // 'fk_charge':fk_charge,
        'domicile':domicile,
        'amount':amount,
        'nuc':nuc,
        'deposit_date':initial_date,
        'fund_type':fund_type,
        'estatus':reinvest,
        'yield':yieldr,
        'yield_usd':yield_usd,
        'charge_moves':charge_moves
    };
    jQuery.ajax({
        url:route,
        type:"post",
        data: data,
        dataType: 'json',
        success:function(result)
        {
            alertify.success(result.message);
            FillTable(result.openings,result.profile,result.permission);
            $("#myModal").modal('hide');
        }
    })

}

var idupdate = 0;
function editarApertura(id)
{
    idupdate=id;
    var cp = document.getElementById("divCP1");
    var lp = document.getElementById("divLP1");

    var route = baseUrl + '/GetInfo/'+ id;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            idClient = result.data.clid;
            $("#selectAgent1").val(result.data.fk_agent);
            $("#client_edit1").val(result.data.cname);
            $("#selectAppli1").val(result.data.fk_application);
            $("#selectPaymentform1").val(result.data.fk_payment_form);
            $("#selectCharge1").val(result.data.fk_charge);
            $("#selectCurrency1").val(result.data.currency);

            var assignPlan = $("#selectInsurance1");
            $("#selectInsurance1").empty();
            assignPlan.append('<option selected hidden value="0">Seleccione una opción</option>');
            result.insurances.forEach( function(valor, indice, array) {
                if(valor.active_fund == 1)
                    assignPlan.append("<option value='" + valor.id + "'>" + valor.name + "</option>");
                else
                    assignPlan.append("<option hidden value='" + valor.id + "'>" + valor.name + "</option>");
            });

            $("#selectInsurance1").val(result.data.fk_insurance);
            $("#nuc1").val(result.data.nuc);

            if(result.data.fund_type == "CP")
            {
                $("#selectStatusMF").val(result.data.estatus);
                cp.style.display = "";
                lp.style.display = "none";
            }
            else
            {
                $("#amount1").val(parseFloat(result.data.amount).toLocaleString('en-US'));
                $("#initial_date1").val(result.data.deposit_date);
                cp.style.display = "none";
                lp.style.display = "";

                charge_moves = [];
                result.chargeMoves.forEach( function(valor, indice, array) {
                    charge_moves.push({
                        'id': charge_moves.length+1,
                        'amount':valor.amount,
                        'fk_charge':valor.fk_charge,
                        'chargeName':valor.name,
                        'apply_date':valor.apply_date
                    });
                });
                if(result.profile != 12)
                    refreshTable(charge_moves);
                else
                    refreshTableAgent(charge_moves);
            }
            fund_type = result.data.fund_type;

            $("#selectCurrency1").val(result.data.fund_curr);

            yieldr = result.data.yieldr;
            yield_usd = result.data.yield_usd;

            $("#myModaledit").modal('show');
        }
    })
}
function cancelEdit()
{
    $("#myModaledit").modal('hide');
}
function actualizarApertura()
{
    var fk_agent = $("#selectAgent1").val();
    var name = $("#name1").val();
    var firstname = $("#firstname1").val();
    var lastname = $("#lastname1").val();
    var birth_date = $("#birth_date1").val();
    var rfc = $("#rfc1").val();
    var curp = $("#curp1").val();
    var cellphone = $("#cellphone1").val();
    var email = $("#email1").val();
    var fk_insurance = $("#selectInsurance1").val();
    var fk_currency = $("#selectCurrency1").val();
    var fk_application = $("#selectAppli1").val();
    var fk_payment_form = $("#selectPaymentform1").val();
    // var fk_charge = $("#selectCharge1").val();
    var domicile = $("#address1").val();
    var nuc = $("#nuc1").val();
    var amount = $("#amount1").val().replace(/[^0-9.]/g, '');
    var initial_date = $("#initial_date1").val();
    var reinvest = $("#selectStatusMF1").val();

    var route = "opening/"+idupdate;
    var data = {
        'id':idupdate,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fk_agent':fk_agent,
        'fk_client':idClient,
        'name':name,
        'firstname':firstname,
        'lastname':lastname,
        'birth_date':birth_date,
        'rfc':rfc,
        'curp':curp,
        'cellphone':cellphone,
        'email':email,
        'fk_insurance':fk_insurance,
        'currency':fk_currency,
        'fk_application':fk_application,
        'fk_payment_form':fk_payment_form,
        // 'fk_charge':fk_charge,
        'domicile':domicile,
        'amount':amount,
        'nuc':nuc,
        'deposit_date':initial_date,
        'fund_type':fund_type,
        'estatus':reinvest,
        'yield':yieldr,
        'yield_usd':yield_usd,
        'charge_moves':charge_moves
    };
    jQuery.ajax({
        url:route,
        type:'put',
        data:data,
        dataType:'json',
        success:function(result)
        {
            alertify.success(result.message);
            FillTable(result.openings,result.profile,result.permission);
            $("#myModaledit").modal('hide');
        }
    })
}

function eliminarApertura(id)
{
    var route = "opening/"+id;
    var data = {
            'id':id,
            "_token": $("meta[name='csrf-token']").attr("content"),
    };
    alertify.confirm("Eliminar Apertura","¿Desea borrar la Apertura?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'delete',
                dataType:'json',
                success:function(result)
                {
                    FillTable(result.openings,result.profile,result.permission);
                    alertify.success('Eliminado');
                }
            })
        },
        function(){
            alertify.error('Cancelado');
    });
}

idStatus = 0;
function opcionesEstatus(id)
{
    idStatus=id;

    var route = baseUrl+'/GetinfoStatus/'+idStatus;
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            $("#selectStatus").val(result.data.fk_status);
            fillStatusDates(result.data.pick_status,"Pick");
            fillStatusDates(result.data.limit_status,"Limit");
            fillStatusDates(result.data.agent_status,"Agent");
            fillStatusDates(result.data.office_status,"Office");
            fillStatusDates(result.data.finestra_status,"Finestra");
            $("#myModalStatus").modal('show');
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
    var route = baseUrl+"/updateStatus";
    var fk_status = $("#selectStatus").val();
    var pick_status = $("#authPick").val();
    var limit_status = $("#authLimit").val();
    var agent_status = $("#authAgent").val();
    var office_status = $("#authOffice").val();
    var finestra_status = $("#authFinestra").val();

    var data = {
        'id':idStatus,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fk_status':fk_status,
        'pick_status':pick_status,
        'limit_status':limit_status,
        'agent_status':agent_status,
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
            FillTable(result.openings,result.profile,result.permission);
            $("#myModalStatus").modal('hide');
        }
    })
}

function closeStatusInt()
{
    $("#myModalStatus").modal('hide');
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

    var route = baseUrl+'/GetinfoStatus/'+idStatus;
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            $("#selectStatus1").val(result.data.fk_status);
            fillStatusDatesAgent(result.data.pick_status,"Pick1");
            fillStatusDatesAgent(result.data.limit_status,"Limit1");
            fillStatusDatesAgent(result.data.agent_status,"Agent1");
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

function closeStatusAg()
{
    $("#myModalStatusAgent").modal('hide');
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

var clienttext = "";

function buscarclientes(flag){
    if(flag == 0)
    {
        clienttext = "#client_edit";
    }
    else
    {
        clienttext = "#client_edit1";
    }
    $("#modalSrcClient").modal("show");
}

function ocultar(){
    $("#modalSrcClient").modal("hide");
}

function obtenerid(id, name){
    idClient = id;
    var fisica = document.getElementById("fisica");
    fisica.style.display="none";
    $(clienttext).val(name);
    $("#modalSrcClient").modal("hide");
}

function noRegistrado()
{
    idClient = 0;
    var fisica = document.getElementById("fisica");
    fisica.style.display="";
    $(clienttext).val("");
    $("#modalSrcClient").modal("hide");
}

function fundchange(flag)
{
    var route;
    var cp;
    var lp;

    if(flag == 0)
    {
        route = baseUrl+'/GetinfoFund/'+$("#selectInsurance").val();
        cp = document.getElementById("divCP");
        lp = document.getElementById("divLP");
    }
    else
    {
        route = baseUrl+'/GetinfoFund/'+$("#selectInsurance1").val();
        cp = document.getElementById("divCP1");
        lp = document.getElementById("divLP1");
    }

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            if(result.data.fund_type == "CP")
            {
                cp.style.display = "";
                lp.style.display = "none";
            }
            else
            {
                cp.style.display = "none";
                lp.style.display = "";
            }
            if(flag == 0)
            {
                $("#selectCurrency").val(result.data.fund_curr);
            }
            else
            {
                $("#selectCurrency1").val(result.data.fund_curr);
            }
            fund_type = result.data.fund_type;
            yieldr = result.data.yield;
            yield_usd = result.data.yield_usd;
        }
    })
}

function cerrarApertura()
{
    $("#myModal").modal("hide");
}

function OpenCharge()
{
    $("#myModal2").modal("show");
}

function cancelar(modal)
{
    $(modal).modal("hide");
}

function refreshTable(charges)
{
    var table = $('#tbProf1').DataTable();
    table.clear();
    charges.forEach( function(valor, indice, array) {
        btnEdit = '<button href="#|" class="btn btn-warning" onclick="editarConducto('+valor.id+')" ><i class="fas fa-edit"></i></button>';
        btnTrash = '<button href="#|" class="btn btn-danger" onclick="eliminarConducto('+valor.id+')"><i class="fa fa-trash"></i></button>';
        table.row.add([parseFloat(valor.amount).toLocaleString('en-US'),valor.chargeName,valor.apply_date,btnEdit+" "+btnTrash]);
    });
    console.log(charges);
    table.draw(false);
}

function refreshTableAgent(charges)
{
    var table = $('#tbProf1').DataTable();
    table.clear();
    charges.forEach( function(valor, indice, array) {
        table.row.add([parseFloat(valor.amount).toLocaleString('en-US'),valor.chargeName,valor.apply_date]);
    });
    console.log(charges);
    table.draw(false);
}

function guardarConducto()
{
    var amount = $("#camount").val().replace(/[^0-9.]/g, '');
    var fk_charge = $("#selectCharge").val();
    var chargeName = $("#selectCharge option:selected").text();
    var apply_date = $("#apply_date").val();

    if(amount == null || amount=="" || apply_date==null || fk_charge==0)
    {
        alert("Los campos no deben de quedar vacios.");
        return false;
    }
    else
    {
        // $("#code").val("");
        charge_moves.push({
            'id': charge_moves.length+1,
            'amount':amount,
            'fk_charge':fk_charge,
            'chargeName':chargeName,
            'apply_date':apply_date
        });
        refreshTable(charge_moves);
    }
}

function eliminarConducto(id)
{
    var index = 0;
    for(var i = 0; i<charge_moves.length; ++i)
    {
        if(charge_moves[i].id == id)
        {
            index=i;
        }
    }
    charge_moves.splice(index,1);
    refreshTable(charge_moves);
}

chargeId = 0;
function editarConducto(id)
{
    for(var i = 0; i<charge_moves.length; ++i)
    {
        if(charge_moves[i].id == id)
        {
            chargeId = id;
            $("#camount1").val(parseFloat(charge_moves[i].amount).toLocaleString('en-US'));
            $("#selectCharge1").val(charge_moves[i].fk_charge);
            $("#apply_date1").val(charge_moves[i].apply_date);
            $("#myModalEditCharge").modal("show");
        }
    }
}

function actualizarConducto()
{
    for(var i = 0; i<charge_moves.length; ++i)
    {
        if(charge_moves[i].id == chargeId)
        {
            charge_moves[i].amount = $("#camount1").val().replace(/[^0-9.]/g, '');
            charge_moves[i].fk_charge = $("#selectCharge1").val();
            charge_moves[i].chargeName = $("#selectCharge1 option:selected").text();
            charge_moves[i].apply_date = $("#apply_date1").val();
            refreshTable(charge_moves);
            $("#myModalEditCharge").modal("hide");
        }
    }
}
