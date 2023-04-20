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

function guardarApertura()
{
    var fk_agent = $("#selectAgent").val();
    var name = $("#name").val();
    var firstname = $("#firstname").val();
    var lastname = $("#lastname").val();
    var rfc = $("#rfc").val();
    var fk_insurance = $("#selectInsurance").val();
    var fk_currency = $("#selectCurrency").val();
    var fk_application = $("#selectAppli").val();
    var fk_payment_form = $("#selectPaymentform").val();
    var domicile = $("#address").val();
    var amount = $("#amount").val().replace(/[^0-9.]/g, '');
    var nuc = $("#nuc").val();

    var route = "opening";
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fk_agent':fk_agent,
        'name':name,
        'firstname':firstname,
        'lastname':lastname,
        'rfc':rfc,
        'fk_insurance':fk_insurance,
        'fk_currency':fk_currency,
        'fk_application':fk_application,
        'fk_payment_form':fk_payment_form,
        'domicile':domicile,
        'amount':amount,
        'nuc':nuc
    };
    jQuery.ajax({
        url:route,
        type:"post",
        data: data,
        dataType: 'json',
        success:function(result)
        {
            alertify.success(result.message);
            // result.data.forEach( function(valor, indice, array) {
            //     table.row.add([valor.name,'<button href="#|" class="btn btn-warning" onclick="editarperfil('+valor.id+')" ><i class="fa-solid fa-pen-to-square"></i></button>&nbsp<button href="#|" class="btn btn-danger" onclick="eliminarperfil('+valor.id+')"><i class="fa-solid fa-trash"></i></button>']).node().id = valor.id;
            // });
            // table.draw(false);
            window.location.reload(true);
        }
    })

}

var idupdate = 0;
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
            $("#selectAgent1").val(result.data.fk_agent);
            $("#name1").val(result.data.name);
            $("#firstname1").val(result.data.firstname);
            $("#lastname1").val(result.data.lastname);
            $("#rfc1").val(result.data.rfc);
            $("#selectInsurance1").val(result.data.fk_insurance);
            $("#selectCurrency1").val(result.data.fk_currency);
            $("#selectAppli1").val(result.data.fk_application);
            $("#selectPaymentform1").val(result.data.fk_payment_form);
            $("#address1").val(result.data.domicile);
            $("#amount1").val(parseFloat(result.data.amount).toLocaleString('en-US'));
            $("#nuc1").val(result.data.nuc);
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
    var rfc = $("#rfc1").val();
    var fk_insurance = $("#selectInsurance1").val();
    var fk_currency = $("#selectCurrency1").val();
    var fk_application = $("#selectAppli1").val();
    var fk_payment_form = $("#selectPaymentform1").val();
    var domicile = $("#address1").val();
    var amount = $("#amount1").val().replace(/[^0-9.]/g, '');
    var nuc = $("#nuc1").val();

    var route = "opening/"+idupdate;
    var data = {
        'id':idupdate,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fk_agent':fk_agent,
        'name':name,
        'firstname':firstname,
        'lastname':lastname,
        'rfc':rfc,
        'fk_insurance':fk_insurance,
        'fk_currency':fk_currency,
        'fk_application':fk_application,
        'fk_payment_form':fk_payment_form,
        'domicile':domicile,
        'amount':amount,
        'nuc':nuc
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
                    window.location.reload(true);
                }
            })
            alertify.success('Eliminado');
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
        $("#selectStatus"+status).val(1);
        document.getElementById("divDate"+status).style.display = "none";
    }
    else
    {
        $("#selectStatus"+status).val(2);
        document.getElementById("divDate"+status).style.display = "";
    }
    $("#auth"+status).val(date);
}

function save()
{
    var route = baseUrl+"/updateStatus";
    var fk_status = $("#selectStatus").val();
    var pick_status = $("#authPick").val();
    var agent_status = $("#authAgent").val();
    var office_status = $("#authOffice").val();
    var finestra_status = $("#authFinestra").val();

    var data = {
        'id':idStatus,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'fk_status':fk_status,
        'pick_status':pick_status,
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
            $("#myModalStatus").modal('hide');
            window.location.reload(true);
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
    }
    else
    {
        document.getElementById("divDate"+div).style.display = "none";
        $("#auth"+div).val(null);
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
            var dateplus = new Date(date);
            dateplus = addWorkDays(dateplus, 10);
            $("#authLimit1").val(dateplus.getFullYear() + "-" +((dateplus.getMonth()+1).length != 2 ? "0" + (dateplus.getMonth() + 1) : (dateplus.getMonth()+1)) + "-" + (dateplus.getDate().length != 2 ?"0" + dateplus.getDate() : dateplus.getDate()));
        }
    }
}

function closeStatusAg()
{
    $("#myModalStatusAgent").modal('hide');
}

function addWorkDays(startDate, days)
{
    var dow = startDate.getDay();
    var daysToAdd = parseInt(days);
    if (dow == 0)
        daysToAdd++;
    if (dow + daysToAdd >= 6)
    {
        var remainingWorkDays = daysToAdd - (5 - dow);
        daysToAdd += 2;
        if (remainingWorkDays > 5)
        {
            daysToAdd += 2 * Math.floor(remainingWorkDays / 5);
            if (remainingWorkDays % 5 == 0)
                daysToAdd -= 2;
        }
    }
    startDate.setDate(startDate.getDate() + daysToAdd);
    return startDate;
}

