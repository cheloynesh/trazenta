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

$(document).ready( function () {
    $('#tbProf2').DataTable({
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

idnuc = 0;
charge_moves = [];

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
            table.row.add([valor.agname,valor.nuc,valor.name,valor.amount,valor.currency,activeStat,valor.deposit_date,valor.end_date,btnMov+" "+btnEdit+" "+btnTrash]).node().id = valor.id;
        }
        else
        {
            table.row.add([valor.agname,valor.nuc,valor.name,valor.amount,valor.currency,activeStat,valor.deposit_date,valor.end_date,btnMov]).node().id = valor.id;
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
    // $("#waitModal").modal('show');
    // var formData = new FormData();
    // var files = $('input[type=file]');
    // for (var i = 0; i < files.length; i++) {
    //     if (files[i].value == "" || files[i].value == null)
    //     {
    //         // console.log(files.length);
    //         // return false;
    //     }
    //     else
    //     {
    //         formData.append(files[i].name, files[i].files[0]);
    //     }
    // }
    // // console.log("entre");
    // var formSerializeArray = $("#Form").serializeArray();
    // for (var i = 0; i < formSerializeArray.length; i++) {
    //     formData.append(formSerializeArray[i].name, formSerializeArray[i].value)
    // }

    // formData.append('_token', $("meta[name='csrf-token']").attr("content"));

    // var route = baseUrl + '/import';

    // jQuery.ajax({
    //     url:route,
    //     type:'post',
    //     data:formData,
    //     contentType: false,
    //     processData: false,
    //     cache: false,
    //     success:function(result)
    //     {
    //         alertify.success(result.message);
    //         console.log(result);
    //         notFnd = result.notFnd.filter((item,index)=>{return result.notFnd.indexOf(item) === index;})
    //         alert("Movimientos importados: " + result.importados + "\nDatos repetidos: " + result.repetidos + "\nNucs no encontrados: " + notFnd.length + "\n" + notFnd.join("\n"));
    //         $("#waitModal").modal('hide');

    //     },
    //     error:function(result,error,errorTrown)
    //     {
    //         alertify.error(errorTrown);
    //         $("#waitModal").modal('hide');
    //     }
    // })
    var route = baseUrl + '/import';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content")
    }
    jQuery.ajax({
        url:route,
        data: data,
        type:'post',
        dataType:'json',
        success:function(result){
            alertify.success(result.message);
        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
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
            $("#selectActive").val(result.data.active_stat);

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
    // var fk_charge = $("#selectCharge").val();
    var fk_client = $("#selectClient").val();
    var fk_insurance = $("#selectInsurance").val();
    var renew_stat = $("#selectRenew").val();
    var active_stat = $("#selectActive").val();

    var route = "sixmonthfunds/"+editNuc;
    var data = {
        'id':editNuc,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'nuc':nuc,
        'currency':selectCurrency,
        'amount':amount,
        'deposit_date':deposit_date,
        // 'fk_charge':fk_charge,
        'fk_agent':fk_agent,
        'fk_client':fk_client,
        'fk_application':fk_application,
        'fk_payment_form':fk_payment_form,
        'fk_insurance':fk_insurance,
        'renew_stat':renew_stat,
        'active_stat':active_stat,
        'active':active,
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

function OpenCharge()
{
    $("#myModalC").modal("show");
}

function cancelar(modal)
{
    $(modal).modal("hide");
}

function refreshTable(charges)
{
    var table = $('#tbProf2').DataTable();
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
    var table = $('#tbProf2').DataTable();
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
    var fk_charge = $("#selectChargeS").val();
    var chargeName = $("#selectChargeS option:selected").text();
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
