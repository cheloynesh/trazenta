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

var formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  });

function getComitions()
{
    let selectForm = document.getElementById('validationSelect');

    if (selectForm.checkValidity() == false)
    {
        alert("Por favor llena los campos obligatorios antes de continuar.");
    }
    else
    {
        var initialDate = $("#start_date").val();
        var endDate = $("#end_date").val();

        var route = baseUrl + '/GetInfo/' + initialDate + '/' + endDate;
        console.log(route);

        jQuery.ajax({
            url:route,
            type:'get',
            dataType:'json',
            success:function(result)
            {
                console.log(result.data);
                if(result.data.length == 0) alert("No se encontraron datos para las fechas seleccionadas.");
                else RefreshTable(result.data);
            },
            error:function(result,error,errorTrown)
            {
                alertify.error(errorTrown);
            }
        })
    }
    selectForm.classList.add('was-validated');
}

var totalrec = 0;
var totalfst = 0;
var totalabon = 0;
var totallp = 0;

function RefreshTable(data,wax,permission)
{
    var table = $('#tbProf').DataTable();

    totalrec = 0;
    totalfst = 0;
    totalabon = 0;
    totallp = 0;

    table.clear();

    data.forEach( function(valor, indice, array) {
        totalrec += parseFloat(String(valor.contrec).replace(/[^0-9.]/g, ''));
        totalfst += parseFloat(String(valor.contpp).replace(/[^0-9.]/g, ''));
        totalabon += parseFloat(String(valor.contpa).replace(/[^0-9.]/g, ''));
        totallp += parseFloat(String(valor.lpnopay).replace(/[^0-9.]/g, ''));

        table.row.add([valor.aname,valor.contrec,valor.contpp,valor.contpa,valor.lpnopay]);
    });

    table.draw(false);

    $("#totalrec").val((parseFloat(totalrec) || 0).toLocaleString('en-US'));
    $("#totalfst").val((parseFloat(totalfst) || 0).toLocaleString('en-US'));
    $("#totalabon").val((parseFloat(totalabon) || 0).toLocaleString('en-US'));
    $("#totallp").val((parseFloat(totallp) || 0).toLocaleString('en-US'));
}

function selectEndDate(selectdate)
{
    var initialDate = $("#start_date").val();
    var endDate = $("#end_date").val();
    var turn = $("#selectTurnCut").val();

    if(initialDate == "")
    {
        alert("Selecciona una fecha de inicio.");
        $("#end_date").val('');
    }
    else
    {
        var initial = new Date(initialDate);
        var end = new Date(endDate);
        if(end < initial)
        {
            alert("La fecha de fin no puede ser anterior a la fecha de inicio.");
            $("#" + selectdate).val('');
        }
    }
}

function verCorte(id)
{
    var route = baseUrl + '/getCut/' + id;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            if(result.finishSales == null) alert("No hay corte para el turno seleccionado");
            else
            {
                FillCut(result.finishSales);
                $("#myModalViewCut").modal('show');
            }
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
        }
    })
}

function closeModal(modal)
{
    $(modal).modal('hide');
}

function FillCut(sales)
{
    var tableCut = $('#tbProfCut').DataTable();
    var efective = 0;
    var card = 0;
    var transfer = 0;
    var courtesy = 0;
    var totalnoex = 0;
    var totalex = 0;
    var totalsale = 0;
    var totalcom = 0;
    var totaldiscnoex = 0;
    var totaldiscex = 0;
    var totalcar = 0;

    tableCut.clear();
    sales.forEach( function(valor, indice, array) {
        tableCut.row.add([valor.plate,valor.brand,valor.model,valor.carColor,valor.total]);
        efective += parseFloat(String(valor.efective).replace(/[^0-9.]/g, ''));
        card += parseFloat(String(valor.card).replace(/[^0-9.]/g, ''));
        transfer += parseFloat(String(valor.transfer).replace(/[^0-9.]/g, ''));
        courtesy += parseFloat(String(valor.courtesy).replace(/[^0-9.]/g, ''));
        totalnoex += parseFloat(String(valor.noex).replace(/[^0-9.]/g, ''));
        totalex += parseFloat(String(valor.ex).replace(/[^0-9.]/g, ''));
        totaldiscnoex += parseFloat(String(valor.discount).replace(/[^0-9.]/g, ''));
        totaldiscex += parseFloat(String(valor.discount_ex).replace(/[^0-9.]/g, ''));
        totalsale += parseFloat(String(valor.total).replace(/[^0-9.]/g, ''));
        totalcar++;
    });

    totalcom = (totalex - totaldiscex)/2;

    $("#efectiveCut").val(efective.toLocaleString('en-US'));
    $("#cardCut").val(card.toLocaleString('en-US'));
    $("#transferCut").val(transfer.toLocaleString('en-US'));
    $("#courtesyCut").val(courtesy.toLocaleString('en-US'));
    $("#totalnoex").val(totalnoex.toLocaleString('en-US'));
    $("#totalex").val(totalex.toLocaleString('en-US'));
    $("#totalcars").val((parseFloat(totalcar) || 0).toLocaleString('en-US'));
    $("#discount").val((parseFloat(totaldiscnoex) || 0).toLocaleString('en-US'));
    $("#discountex").val((parseFloat(totaldiscex) || 0).toLocaleString('en-US'));
    $("#totalsale").val(totalsale.toLocaleString('en-US'));
    $("#totalcom").val(totalcom.toLocaleString('en-US'));

    tableCut.draw(false);
}

