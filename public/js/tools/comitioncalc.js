var ruta = window.location;
var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host + getUrl.pathname;

let formatter = new Intl.NumberFormat("en-US", {
  style: "currency",
  currency: "USD",
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
});

let formatterdec = new Intl.NumberFormat("en-US", {
  style: "decimal",
  currency: "USD",
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
});

var fund_type = "";
var fund_typeB = "";

function fundchange(aux)
{
    var route = baseUrl+'/GetinfoFund/'+$("#selectInsurance" + aux).val();

    jQuery.ajax({
        url:route,
        type:'get',
        dataType:'json',
        success:function(result){
            $("#selectCurr" + aux).val(result.data.fund_curr);
            $("#yield" + aux).val(result.data.yield);
            switch (aux)
            {
                case 'CP':
                    fund_type = result.data.fund_type;
                    calculoCP();
                    break;
                case 'LP':
                    calculoLP();
                    break;
                case 'CPB':
                    fund_typeB = result.data.fund_type;
                    calculoB();
                    break;
                case 'LPB':
                    calculoB();
                    break;
            }
        }
    })
}

function  calculoCP()
{
    var curr = $("#selectCurrCP").val();
    var opening = parseFloat($("#openingCP").val().replace(/[^0-9.]/g, ''));
    var insurance = parseFloat($("#selectInsuranceCP").val());

    var route = baseUrl+'/GetCPCalc/1';

    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'curr':curr,
        'opening':opening,
        'insurance':insurance,
    }

    let cpform = document.getElementById('validationCP');

    if (cpform.checkValidity() == false){}
    else
    {
        if((opening >= 250000 && curr == "MXN" && fund_type == "CP") || (opening >= 750000 && curr == "MXN" && fund_type == "MP") || (opening >= 5000 && curr == "USD"))
        {
            jQuery.ajax({
                url:route,
                data:data,
                type:'get',
                dataType:'json',
                success:function(result){
                    $("#fstpayCP").val(formatterdec.format(result.b_amountFst));
                    $("#recCP").val(formatterdec.format(result.b_amountRec));
                }
            })
        }
        else
        {
            alert("La apertura no debe ser menor a $250,000.00 MXN en CP o $750,000.00 MXN en MP o 5,000 USD");
            table.clear();
            table.draw(false);
        }
    }
    cpform.classList.add('was-validated');
}

function  calculoLP()
{
    var curr = $("#selectCurrLP").val();
    var opening = parseFloat($("#openingLP").val().replace(/[^0-9.]/g, ''));

    var route = baseUrl+'/GetLPCalc/1';

    var data = {
        "_token": $("meta[name='csrf-token']").attr("content"),
        'curr':curr,
        'opening':opening,
    }

    let cpform = document.getElementById('validationLP');

    if (cpform.checkValidity() == false){}
    else
    {
        if((opening >= 500000 && curr == "MXN") || (opening >= 25000 && curr == "USD"))
        {
            if ((opening % 500000 === 0 && curr == "MXN") || (opening % 25000 === 0 && curr == "USD"))
            {
                jQuery.ajax({
                    url:route,
                    data:data,
                    type:'get',
                    dataType:'json',
                    success:function(result){
                        $("#fstpayLP").val(formatterdec.format(result.b_amountLP));
                    }
                })
            }
            else
            {
                alert("La apertura debe ser multiplo de $500,000.00 MXN o $25,000 USD");
                table.clear();
                table.draw(false);
            }
        }
        else
        {
            alert("La apertura no debe ser menor a $500,000.00 o $25,000 USD");
            table.clear();
            table.draw(false);
        }
    }
    cpform.classList.add('was-validated');
}
