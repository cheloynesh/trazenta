var ruta = window.location;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + getUrl.pathname;

function showimp()
{
   var get_value = document.getElementById("selectProfile");
   var valor = get_value.value;
//    alert(valor);
    if(valor == "12")
    {
        document.getElementById("etiqueta").hidden = false;
        document.getElementById("etiqueta").style.display = "block";
        document.getElementById("code").hidden = false;
        document.getElementById("code").style.display = "block";
        document.getElementById("agregarcol").hidden = false;
        document.getElementById("agregarcol").style.display = "block";
        document.getElementById("tbcodes").hidden = false;
        document.getElementById("tbcodes").style.display = "block";
        document.getElementById("tbody-codigo").hidden = false;
        document.getElementById("tbody-codigo").style.display = "block";

    }
    else
    {
        document.getElementById("etiqueta").hidden = true;
        document.getElementById("code").hidden = true;
        document.getElementById("agregarcol").hidden = true;
        document.getElementById("tbcodes").hidden = true;
        document.getElementById("tbody-codigo").hidden = true;

    }
}
var array = [];
var codigos=[];
function agregarcodigo()
{
    var codigo = $("#code").val();
    var table = $("#tbody-codigo");
    var str_row = '<tr id = "'+parseFloat(array.length+1)+'"><td><input type=text name="codigo[]" value="'+codigo+'"/></td><td><button type="button" class="btn btn-danger" onclick="delete_code(this)"><i class="fa fa-trash mr-2"></i></button></td></tr>';
    table.append(str_row);
    $("#code").val("");
    codigos.push({
        'id':array.length+1,
        'code':codigo
    });
}
function delete_code(row)
{
    var index = 0;
    var id = $(row).parent().parent().attr('id');
    $(row).parent().parent().remove();
    for(var i = 0; i<codigos.length; ++i)
    {
        if(codigos[i].id == id)
        {
            index=0;
        }
    }
    codigos.splice(index,1);
}

function mostrarDiv()
{
    var onoff = document.getElementById("onoff");
    var checked = onoff.checked;
    var fisica = document.getElementById("fisica");
    var moral = document.getElementById("moral");
    if(checked)
    {
        fisica.style.display = ""
        moral.style.display = "none"
    }
    else
    {

        fisica.style.display = "none"
        moral.style.display = "block"
    }
}
function mostrarAsegurado()
{
    var onoff = document.getElementById("onoffAseg");
    var checked = onoff.checked;
    var fisica = document.getElementById("fisicaAsegurado");
    var moral = document.getElementById("moralAsegurado");
    if(checked)
    {
        fisica.style.display = ""
        moral.style.display = "none"
    }
    else
    {

        fisica.style.display = "none"
        moral.style.display = "block"
    }
}
function mostrarDivAsegurado()
{
    var onoff = document.getElementById("onoffAsegurado");
    var checked = onoff.checked;
    var asegurado = document.getElementById("asegurado");
    if(checked)
    {
        asegurado.style.display = ""
    }
    else
    {

        asegurado.style.display = "none"
    }
}
function abrirExcel()
{
    var route = baseUrl + '/import/1';
    var file = $("#file").val();
    alert(file);
    // $.ajaxSetup({
    //     headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    // });

    // var form = $('<form></form>');

    // form.attr("method", "get");
    // form.attr("action", route);
    // form.attr('_token',$("meta[name='csrf-token']").attr("content"));
    // $.each(function(key, value) {
    //     var field = $('<input></input>');
    //     field.attr("type", "hidden");
    //     field.attr("name", key);
    //     field.attr("value", value);
    //     form.append(field);
    // });
    // var field = $('<input></input>');
    // field.attr("type", "hidden");
    // field.attr("name", "_token");
    // field.attr("value", $("meta[name='csrf-token']").attr("content"));
    // form.append(field);
    // $(document.body).append(form);
    // form.submit();
}
