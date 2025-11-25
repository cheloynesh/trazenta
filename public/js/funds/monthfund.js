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
    $('#tbnotFnd').DataTable({
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

var idnuc = 0;

var active = 1;

function FillTable(data,profile,permission)
{
    var table = $('#tbProf').DataTable();
    var btnStat = '';
    var btnMov = '';
    var btnEdit = '';
    var btnTrash = '';
    var activeStat = '';
    table.clear();

    data.forEach( function(valor, indice, array) {
        btnStat = '<button class="btn btn-info" style="background-color: #'+valor.color+'; border-color: #'+valor.color+'" onclick="opcionesEstatus('+valor.id+','+valor.statId+')">'+valor.estatus+'</button>';
        btnMov = '<a href="#|" class="btn btn-primary" onclick="nuevoMovimiento('+valor.id+')" >Movimientos</a>';
        btnEdit = '<button href="#|" class="btn btn-warning" onclick="editarNuc('+valor.id+')" ><i class="fas fa-edit"></i></button>';
        btnTrash = '<button href="#|" class="btn btn-danger" onclick="deleteFund('+valor.id+')"><i class="fa fa-trash"></i></button>';
        if(valor.active_stat == 0) activeStat = '<font color="red">INACTIVO</font>'; else activeStat = '<font color="green">ACTIVO</font>';
        if (profile != 12)
        {
            table.row.add([valor.agname,valor.nuc,valor.currency,valor.name,activeStat,btnStat,btnMov+" "+btnEdit+" "+btnTrash]).node().id = valor.id;
        }
        else
        {
            table.row.add([valor.agname,valor.nuc,valor.currency,valor.name,activeStat,btnStat,btnMov]).node().id = valor.id;
        }
    });
    table.draw(false);
}

function nuevoMovimiento(id)
{
    idnuc = id;
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    var table = $('#tbProf1').DataTable();
    var options = [];
    var route = baseUrl + '/GetInfo/'+ id;
    var button = 'Autorizado';
    var btnTrash;

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
                if(valor.auth == "-")
                {
                    button = '<button href="#|" class="btn btn-primary" onclick="autorizarMovimiento('+valor.id+')" >Autorizar</button>';
                }
                else
                {
                    button = valor.auth;
                }
                btnTrash = '<button type="button" class="btn btn-danger"'+'onclick="deleteMove('+valor.id+')"><i class="fas fa-trash"></i></button>';
                btnEdit = '<button href="#|" class="btn btn-warning" onclick="editarConducto('+valor.id+')" ><i class="fas fa-edit"></i></button>';
                if(valor.type == "Apertura")
                    table.row.add([valor.apply_date,button,formatter.format(valor.prev_balance),formatter.format(valor.new_balance),
                        valor.currency,formatter.format(valor.amount),valor.type,btnEdit + " " + btnTrash]).node().id = valor.id;
                else
                    table.row.add([valor.apply_date,button,formatter.format(valor.prev_balance),formatter.format(valor.new_balance),
                        valor.currency,formatter.format(valor.amount),valor.type,btnTrash]).node().id = valor.id;
            });
            table.draw(false);
            $('#selectType').empty();
            if(result.data.length == 0)
            {
                options = ["Apertura"];
            }
            else
            {
                options = ["Abono", "Retiro parcial", "Retiro total", "Ajuste"];
            }
            $.each(options, function(i, p) {
                $('#selectType').append($('<option></option>').val(p).html(p));
            });

            $('#apply_date').val(yyyy+"-"+mm+"-"+dd);
        }
    })
    $("#myModal2").modal('show');
}
function cancelarMovimiento()
{
    $("#myModal2").modal('hide');
}
function guardarMovimiento()
{
    var table = $('#tbProf1').DataTable();
    var type = $("#selectType").val();
    var amount = parseFloat($("#amount").val());
    var currency = $("#selectCurrency").val();
    var prev_balance = 0;
    var new_balance = 0;
    var button = 'Autorizado';
    var options = [];

    var apply_date = $('#apply_date').val();
    // obtener balances anteriores
    var route = baseUrl + '/GetInfoLast/'+ idnuc;

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            // guardar balance
            route = "monthfunds";

            if (result.data != null)
            {
                prev_balance = parseFloat(result.data.new_balance);
            }
            if(type == "Abono")
            {
                new_balance = amount + prev_balance;
            }
            else if(type == "Retiro parcial")
            {
                new_balance = prev_balance - amount;
            }
            else if (type == "Retiro total")
            {
                new_balance = 0;
            }
            else
            {
                new_balance = amount;
            }

            var data = {
                "_token": $("meta[name='csrf-token']").attr("content"),
                'fk_nuc':idnuc,
                'type':type,
                'amount':amount,
                'currency':currency,
                'prev_balance':prev_balance,
                'new_balance':new_balance,
                'apply_date':apply_date,
            };

            jQuery.ajax({
                url:route,
                type:'post',
                data:data,
                dataType:'json',
                success:function(result)
                {
                    table.clear();
                    result.data.forEach( function(valor, indice, array) {
                        if(valor.auth == "-")
                        {
                            button = '<button href="#|" class="btn btn-primary" onclick="autorizarMovimiento('+valor.id+')" >Autorizar</button>';
                        }
                        else
                        {
                            button = valor.auth;
                        }
                        btnTrash = '<button type="button" class="btn btn-danger"'+'onclick="deleteMove('+valor.id+')"><i class="fas fa-trash"></i></button>';
                        table.row.add([valor.apply_date,button,formatter.format(valor.prev_balance),formatter.format(valor.new_balance),
                            valor.currency,formatter.format(valor.amount),valor.type,btnTrash]).node().id = valor.id;
                    });
                    table.draw(false);
                    if(type == "Apertura")
                    {
                        $('#selectType').empty();
                        options = ["Abono", "Retiro parcial", "Retiro total", "Ajuste"];
                        $.each(options, function(i, p) {
                            $('#selectType').append($('<option></option>').val(p).html(p));
                        });
                    }
                    alertify.success(result.message);
                }
            })
        }
    })
}
var id_initial = 0;
function opcionesEstatus(initialId,statusId)
{
    id_initial=initialId;
    $("#selectStatus").val(statusId);
    $("#myEstatusModal").modal('show');
}

function actualizarEstatus()
{
    var status = $("#selectStatus").val();
    var route = baseUrl+"/updateStatus";
    console.log(route);
    var data = {
        'id':id_initial,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'status':status,
        'active':active
    };
    jQuery.ajax({
        url:route,
        type:'post',
        data:data,
        dataType:'json',
        success:function(result)
        {
            alertify.success(result.message);
            $("#myEstatusModal").modal('hide');
            FillTable(result.nucs,result.profile,result.permission);
            // window.location.reload(true);
        }
    })
}
function cerrarmodal()
{
    $("#myEstatusModal").modal('hide');
    $("#comentary").val("");

}
var idMovimiento = 0;
function autorizarMovimiento(id)
{
    idMovimiento = id;
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();

    $("#auth").val(yyyy+"-"+mm+"-"+dd);
    $("#authModal").modal('show');
}
function cerrarAuth()
{
    $("#authModal").modal('hide');
}
function guardarAuth()
{
    var table = $('#tbProf1').DataTable();
    var auth = $("#auth").val();
    var route = baseUrl+"/updateAuth";
    var data = {
        'id':idMovimiento,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'auth':auth,
    };
    jQuery.ajax({
        url:route,
        type:'post',
        data:data,
        dataType:'json',
        success:function(result)
        {
            $("#authModal").modal('hide');
            alertify.success(result.message);
            table.clear();
            result.data.forEach( function(valor, indice, array) {
                if(valor.auth == "-")
                {
                    button = '<button href="#|" class="btn btn-primary" onclick="autorizarMovimiento('+valor.id+')" >Autorizar</button>';
                }
                else
                {
                    button = valor.auth;
                }
                btnTrash = '<button type="button" class="btn btn-danger"'+'onclick="deleteMove('+valor.id+')"><i class="fas fa-trash"></i></button>';
                table.row.add([valor.apply_date,button,formatter.format(valor.prev_balance),formatter.format(valor.new_balance),
                    valor.currency,formatter.format(valor.amount),valor.type,btnTrash]).node().id = valor.id;
            });
            table.draw(false);
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
            $("#nuc").val(result.data.nuc);
            $("#selectClient").val(result.data.fk_client);
            $("#selectAgent").val(result.data.fk_agent);
            $("#selectAppli").val(result.data.fk_application);
            $("#selectPaymentform").val(result.data.fk_payment_form);
            // $("#selectCharge").val(result.data.fk_charge);
            $("#selectInsurance").val(result.data.fk_insurance);
            $("#selectActiveStat").val(result.data.active_stat);
            $("#editModal").modal('show');
        }
    })
}

function cerrarNuc()
{
    $("#editModal").modal('hide');
}

function actualizarNuc()
{
    var nuc = $("#nuc").val();
    var fk_client = $("#selectClient").val();
    var fk_agent = $("#selectAgent").val();
    var fk_application = $("#selectAppli").val();
    var fk_payment_form = $("#selectPaymentform").val();
    // var fk_charge = $("#selectCharge").val();
    var fk_insurance = $("#selectInsurance").val();
    var active_stat = $("#selectActiveStat").val();
    var route = "monthfunds/"+editNuc;
    var data = {
        'id':editNuc,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'nuc':nuc,
        'fk_application':fk_application,
        'fk_payment_form':fk_payment_form,
        // 'fk_charge':fk_charge,
        'fk_client':fk_client,
        'fk_agent':fk_agent,
        'fk_insurance':fk_insurance,
        'active_stat':active_stat,
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
            $("#editModal").modal('hide');
            FillTable(result.nucs,result.profile,result.permission);
            // window.location.reload(true);
        }
    })
}
function excel_nuc(){
    var route = baseUrl + '/ExportFunds/'+idnuc;
    // alert(route);
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

function deleteMove(id)
{
    var table = $('#tbProf1').DataTable();
    var route = "monthfunds/"+id;
    var data = {
        'id':id,
        "_token": $("meta[name='csrf-token']").attr("content"),
    };
    alertify.confirm("Eliminar Movimiento","¿Desea borrar el Movimiento?",
        function(){
            jQuery.ajax({
                url:route,
                data: data,
                type:'delete',
                dataType:'json',
                success:function(result)
                {
                    alertify.success(result.message);
                    table.clear();
                    result.data.forEach( function(valor, indice, array) {
                        if(valor.auth == "-")
                        {
                            button = '<button href="#|" class="btn btn-primary" onclick="autorizarMovimiento('+valor.id+')" >Autorizar</button>';
                        }
                        else
                        {
                            button = valor.auth;
                        }
                        btnTrash = '@if ($perm_btn['+'modify'+']==1)<button type="button" class="btn btn-danger"'+'onclick="deleteMove('+valor.id+')"><i class="fas fa-trash"></i></button>@endif';
                        table.row.add([valor.apply_date,button,formatter.format(valor.prev_balance),formatter.format(valor.new_balance),
                            valor.currency,formatter.format(valor.amount),valor.type,btnTrash]).node().id = valor.id;
                    });
                    table.draw(false);
                }
            })
        },
        function(){
            alertify.error('Cancelado');
    });
}
function importexc()
{
    var table = $('#tbnotFnd').DataTable();
    var resultrow = document.getElementById("resultrow");
    var waitrow = document.getElementById("waitrow");
    document.getElementById("closeBtn").hidden = true;
    waitrow.style.display = "";
    resultrow.style.display = "none";
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
            $("#importados").val(result.importados);
            $("#repetidos").val(result.repetidos);
            $("#notFnd").val(notFnd.length);
            table.clear();
            notFnd.forEach( function(valor, indice, array) {
                table.row.add([valor]).node().id = valor;
            });
            table.draw(false);
            document.getElementById("closeBtn").hidden = false;
            waitrow.style.display = "none";
            resultrow.style.display = "";
            // alert("Movimientos importados: " + result.importados + "\nDatos repetidos: " + result.repetidos + "\nNucs no encontrados: " + notFnd.length + "\n" + notFnd.join("\n"));
            // $("#waitModal").modal('hide');

        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
            $("#waitModal").modal('hide');
        }
    })
}

function actualizarFondo()
{
    var route = baseUrl + '/updateFund';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
    };
    jQuery.ajax({
        url:route,
        type:'post',
        data:data,
        dataType:'json',
        success:function(result)
        {
            alertify.success(result.message);
            $("#waitModal").modal('hide');

        },
        error:function(result,error,errorTrown)
        {
            alertify.error(errorTrown);
            $("#waitModal").modal('hide');
        }
    })
}

function cerrarWait()
{
    $("#waitModal").modal('hide');
}

function deleteFund(id)
{
    var route = baseUrl + '/deleteFund';
    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        "id":id,
        'active':active
    };

    alertify.confirm("Eliminar Apertura","¿Desea borrar el fondo?",
        function(){
            jQuery.ajax({
                url:route,
                type:'post',
                data:data,
                dataType:'json',
                success:function(result)
                {
                    alertify.success(result.message);
                    $("#waitModal").modal('hide');
                    FillTable(result.nucs,result.profile,result.permission);
                },
                error:function(result,error,errorTrown)
                {
                    alertify.error(errorTrown);
                    $("#waitModal").modal('hide');
                }
            })
        },
        function(){
            alertify.error('Cancelado');
    });
}

function chkActive()
{
    if (document.getElementById('chkActive').checked) active = 0; else active = 1;

    var route = baseUrl + '/GetCP/'+active;
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

chargeId = 0;
function editarConducto(id)
{
    chargeId = id;
    var route = baseUrl + '/GetCharge/'+id;
    // alert(route);
    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result)
        {
            $("#selectCharge1").val(result.charge);
            $("#myModalEditCharge").modal("show");
        }
    })
}

function actualizarConducto()
{
    var charge = $("#selectCharge1").val();
    var route = baseUrl+"/updateCharge";

    var data = {
        'id':chargeId,
        "_token": $("meta[name='csrf-token']").attr("content"),
        'charge':charge
    };
    jQuery.ajax({
        url:route,
        type:'post',
        data:data,
        dataType:'json',
        success:function(result)
        {
            alertify.success(result.message);
            $("#myModalEditCharge").modal("hide");
        }
    })
}
