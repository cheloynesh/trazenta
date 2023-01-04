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
            var amount = (parseInt(result.data.amount) * 0.1450)/6;

            var fecha = result.data.initial_date.split("-");
            var fechamas = fecha[0].toString() + "-" + fecha[1] + "-" + fecha[2];

            table.row.add([1,fechamas,formatter.format(amount)]).node().id = 1;

            for(var x = 0; x<11; x++)
            {
                fecha[1] = parseInt(fecha[1]) + 2;
                if(fecha[1] > 12)
                {
                    fecha[1] = "0" + (fecha[1] - 12).toString();
                    fecha[0] = parseInt(fecha[0]) + 1;
                }
                else if(parseInt(fecha[1]) < 10)
                {
                    fecha[1] = "0" + fecha[1].toString();
                }
                fechamas = fecha[0].toString() + "-" + fecha[1] + "-" + fecha[2];

                table.row.add([x+2,fechamas,formatter.format(amount)]).node().id = x+2;
            }

            table.draw(false);
        }
    })
    $("#myModal2").modal('show');
}
function cancelarMovimiento()
{
    $("#myModal2").modal('hide');
}
