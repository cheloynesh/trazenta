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
        },
        aLengthMenu: [
            [25, 50, 100, 200, -1],
            [25, 50, 100, 200, "All"]
        ],
        iDisplayLength: -1
    });
} );

var formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  });

idnuc = 0;

var active = 1;

function FillTable(data,profile,permission)
{
    // alert("entre");
    var table = $('#tbProf').DataTable();
    var btnStat = '';
    var btnMov = '';
    var btnEdit = '';
    var btnTrash = '';
    var activeStat = '';
    table.clear();

    data.forEach( function(valor, indice, array) {
        btnMov = '<a href="#|" class="btn btn-primary" onclick="nuevoMovimiento('+valor.id+')" >Cuponera</a>';
        btnEdit = '<button href="#|" class="btn btn-warning" onclick="editarNuc('+valor.id+')" ><i class="fas fa-edit"></i></button>';
        btnTrash = '<button href="#|" class="btn btn-danger" onclick="eliminarNuc('+valor.id+')"><i class="fa fa-trash"></i></button>';
        if(valor.active_stat == 0) activeStat = '<font color="red">INACTIVO</font>'; else activeStat = '<font color="green">ACTIVO</font>';
        console.log(valor.active_stat);
        if (profile != 12)
        {
            table.row.add([valor.name,valor.nuc,valor.agname,valor.amount,valor.currency,activeStat,valor.deposit_date,valor.end_date,btnMov+" "+btnEdit+" "+btnTrash]).node().id = valor.id;
        }
        else
        {
            table.row.add([valor.name,valor.nuc,valor.agname,valor.amount,valor.currency,activeStat,valor.deposit_date,valor.end_date,btnMov]).node().id = valor.id;
        }
    });
    table.draw(false);
}

function nuevoMovimiento(id)
{
    idnuc = id;
    var table = $('#tbProf1').DataTable();
    var route = baseUrl + '/GetInfo/'+ id;

    $("#amount").val("");
    table.clear();

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            table.clear();

            result.data.forEach( function(valor, indice, array) {
                table.row.add([valor.number,valor.pay_date,formatter.format(valor.amount)]).node().id = valor.id;
            });

            table.draw(false);
        }
    })
    $("#myModal2").modal('show');
}
function cancelarMovimiento()
{
    $("#myModal2").modal('hide');
}
function returnFormat(number)
{
    return formatter.format(number);
}
function eliminarNuc(id)
{
    var route = "sixmonthfunds/"+id;
    var data = {
        'id':id,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'active':active
    };
    alertify.confirm("Eliminar Contrato","¿Desea borrar el contrato?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'delete',
                dataType:'json',
                success:function(result)
                {
                    FillTable(result.nucs,result.profile,result.permission);
                    alertify.success('Eliminado');
                    // window.location.reload(true);
                }
            })
        },
        function(){
            alertify.error('Cancelado');
    });
}
function importexc()
{
    $("#waitModal").modal('show');
    var formData = new FormData();
    var files = $('input[type=file]');
    for (var i = 0; i < files.length; i++) {
        if (files[i].value == "" || files[i].value == null)
        {
            // console.log(files.length);
            // return false;
        }
        else
        {
            formData.append(files[i].name, files[i].files[0]);
        }
    }
    // console.log("entre");
    var formSerializeArray = $("#Form").serializeArray();
    for (var i = 0; i < formSerializeArray.length; i++) {
        formData.append(formSerializeArray[i].name, formSerializeArray[i].value)
    }

    formData.append('_token', $("meta[name='csrf-token']").attr("content"));

    var route = baseUrl + '/import';

    jQuery.ajax({
        url:route,
        type:'post',
        data:formData,
        contentType: false,
        processData: false,
        cache: false,
        success:function(result)
        {
            alertify.success(result.message);
            console.log(result);
            notFnd = result.notFnd.filter((item,index)=>{return result.notFnd.indexOf(item) === index;})
            alert("Movimientos importados: " + result.importados + "\nDatos repetidos: " + result.repetidos + "\nNucs no encontrados: " + notFnd.length + "\n" + notFnd.join("\n"));
            $("#waitModal").modal('hide');

        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
            $("#waitModal").modal('hide');
        }
    })
}

var editNuc = 0;

function editarNuc(id)
{
    editNuc = id;
    var route = baseUrl + '/GetNuc/'+id;
    // alert(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            $("#nucSixMonth").val(result.data.nuc);
            $("#selectClient").val(result.data.fk_client);
            $("#selectAgent").val(result.data.fk_agent);
            $("#amountSixMonth").val(parseFloat(result.data.amount).toLocaleString('en-US'));
            $("#selectCurrencySixMonth").val(result.data.currency);
            $("#initial_date").val(result.data.deposit_date);
            $("#selectAppliSixMonth").val(result.data.fk_application);
            $("#selectCharge").val(result.data.fk_charge);
            $("#selectPaymentformSixMonth").val(result.data.fk_payment_form);
            $("#selectInsurance").val(result.data.fk_insurance);
            $("#selectRenew").val(result.data.renew_stat);
            $("#sixMonthNucModal").modal('show');
        }
    })
}

function cerrarNuc()
{
    $("#sixMonthNucModal").modal('hide');
}

function actualizarNuc()
{
    var selectCurrency = $("#selectCurrencySixMonth").val();
    var nuc = $("#nucSixMonth").val();
    var fk_agent = $("#selectAgent").val();
    var amount = $("#amountSixMonth").val().replace(/[^0-9.]/g, '');
    var deposit_date = $("#initial_date").val();
    var fk_application = $("#selectAppliSixMonth").val();
    var fk_payment_form = $("#selectPaymentformSixMonth").val();
    var fk_charge = $("#selectCharge").val();
    var fk_client = $("#selectClient").val();
    var fk_insurance = $("#selectInsurance").val();
    var renew_stat = $("#selectRenew").val();

    var route = "sixmonthfunds/"+editNuc;
    var data = {
        'id':editNuc,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'nuc':nuc,
        'currency':selectCurrency,
        'amount':amount,
        'deposit_date':deposit_date,
        'fk_charge':fk_charge,
        'fk_agent':fk_agent,
        'fk_client':fk_client,
        'fk_application':fk_application,
        'fk_payment_form':fk_payment_form,
        'fk_insurance':fk_insurance,
        'renew_stat':renew_stat,
        'active':active
    };
    jQuery.ajax({
        url:route,
        type:'put',
        data:data,
        dataType:'json',
        success:function(result)
        {
            alertify.success(result.message);
            $("#sixMonthNucModal").modal('hide');
            FillTable(result.nucs,result.profile,result.permission);
            // window.location.reload(true);
        }
    })
}

function chkActive()
{
    if (document.getElementById('chkActive').checked) active = 0; else active = 1;

    var route = baseUrl + '/GetLP/'+active;
    // alert(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            FillTable(result.nucs,result.profile,result.permission);
        }
    })
}
