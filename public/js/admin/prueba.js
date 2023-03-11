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
        "columnDefs": [{
            "targets": [4,5,6,7],
            "visible": false
            // "searchable": false
        }]
        // scrollY: '200px',
        // paging: false,
    });

    $('a.toggle-vis').on('click', function (e) {
        e.preventDefault();

        // Get the column API object
        var column = table.column($(this).attr('data-column'));

        // Toggle the visibility
        column.visible(!column.visible());
    });
});

function changeRe(id)
{
    // $("#1re").css("background","#129c00");
    // $("#1re").css("border","#129c00");
    $("#myModal").modal('show');
}

function showDate()
{
    var divDate = document.getElementById("divDate");

    if ($("#selectStatus").val() == 3)
    {
        divDate.style.display = "";
    }
    else
    {
        divDate.style.display = "none";
    }
}

function save()
{
    btn = document.getElementById("1re");
    if($("#selectStatus").val() == 1)
    {
        btn.innerText = "SI";
        $("#1re").css("background","#c9bd0e");
        $("#1re").css("border","#c9bd0e");
    }
    else if($("#selectStatus").val() == 2)
    {
        btn.innerText = "PENDIENTE";
        $("#1re").css("background","#fc1303");
        $("#1re").css("border","#fc1303");
    }
    else
    {
        btn.innerText = $("#auth").val();
        $("#1re").css("background","#129c00");
        $("#1re").css("border","#129c00");
    }
    $("#myModal").modal('hide');
}
