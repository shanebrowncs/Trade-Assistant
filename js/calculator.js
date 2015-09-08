function autoCompleteKeyUp(){
    var availableTags = [];
    var inputData = $("#autocomplete").val();
        $.ajax({
                url: "autocomplete.php",
                data: "search=" + inputData,
                success: function(msg){
                    if(msg != ""){
                        console.log(msg);
                        availableTags = msg.split("<br />");
                        $('#result').html('');
                        for(var i = 0; i < availableTags.length; i++){
                            $("#result").append('<div class="item">' + availableTags[i] + '</div>');
                        }
                    }else{
                        $("#result").html('');
                    }
                }
        });
}

function removeHighlightedRows(){
    $("#left tr td:first-child, #right tr td:first-child").each(function( index ){
        if($(this).hasClass("highlighted")){
            console.log("highlighted");
            $(this).closest("tr").remove();
        }
    });
}

function ajaxGetItemJSON(inputData, currency){
    $.ajax({
        url: "autocomplete.php",
        data: "getitem=" + inputData + "&cur=" + currency,
        success: function(msg){
            if(msg != ""){
                obj = JSON.parse(msg);
                addItemToTable(obj);
            }
        }
    });

    return false;
}

function addItemToTable(json){
    var currency = getCurrency();
    if($("#traderradio").is(':checked')){
        $('#left > tbody > tr').eq(i-1).before("<tr><td>" + json.name + "</td><td>" + currency + " " + json.current + "</td><td>" + currency + " " + json.median + "</td><td>" + currency + " " + json.market + "</td><td>" + json.volume + "</td></tr>");
        traderSums[0] += parseFloat(json.current);
        traderSums[1] += parseFloat(json.median);
        traderSums[2] += parseFloat(json.market);
        traderSums[3] += parseFloat(json.volume);
    }else{
        $('#right > tbody > tr').eq(i-1).before("<tr><td>" + json.name + "</td><td>" + currency + " " + json.current + "</td><td>" + currency + " " + json.median + "</td><td>" + currency + " " + json.market + "</td><td>" + json.volume + "</td></tr>");
        requestedSums[0] += parseFloat(json.current);
        requestedSums[1] += parseFloat(json.median);
        requestedSums[2] += parseFloat(json.market);
        requestedSums[3] += parseFloat(json.volume);
        $('#right > tbody > tr').eq(i).replaceWith("<tr><td>Total:</td><td>" + currency + " " + requestedSums[0] + "</td><td>" + currency + " 0.00</td><td>" + currency + " 0.00</td><td>0</td></tr>");
    }
}

function getCurrency(){
	var cookies = document.cookie.split(";");
    var cookie;
    var cookieSize = cookies.length;
    for(i = 0; i < cookieSize; i++){
        if(cookie = cookies[0].replace("currency=", "")){
            return cookie;
        }
    }

    return "USD";
}

$(function(){
    var mouseDown = false;

    var traderSums = [];
    var requestedSums = [];
    for(var i = 0; i < 4; i++){
        traderSums[i] = 0;
        requestedSums[i] = 0;
    }


    var currency = getCurrency();
    $("#left, #right").append("<tr><td>Total:</td><td>" + currency + " 0.00</td><td>" + currency + " 0.00</td><td>" + currency + " 0.00</td><td>0</td></tr>");

    $("#left td, #right td")
    .mousedown(function () {
        mouseDown = true;
        var col = $(this).parent().children().index($(this));
        var row = $(this).parent().index();
        if(col == 0 && row != 0){ // First column & not first row - Item Name
            $(this).toggleClass("highlighted");
        }

        return false;

    })

    .mouseover(function () {
        var col = $(this).parent().children().index($(this));
        var row = $(this).parent().index();
        if(mouseDown && col == 0 && row != 0){ // First column & not first row - Item Name
            $(this).toggleClass("highlighted");
        }
    });

    $("#autocomplete").keyup(function(){
        autoCompleteKeyUp();
    });

    $("#removebtn").click(function(){
        removeHighlightedRows();
    });

    $(document)
        .mouseup(function () {
            mouseDown = false;
            $(".item").click(function(){
                $("#autocomplete").val("");
                $("#result").html('');
                ajaxGetItemJSON($(this).text(), getCurrency());
            });
        });
})
