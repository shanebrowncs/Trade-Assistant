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
            data: "getitem=" + inputData + "?cur=" + currency,
            success: function(msg){
                if(msg != ""){
                    console.log(msg);
                    obj = JSON.parse(msg);
                    return obj;
                }else{
                    return false;
                }
            }
    });
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

    return false;
}

$(function(){
    var mouseDown = false;

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
            var itemJSON;
            if(itemJSON = ajaxGetItemJSON($(this).text()) != false){
                if($("#traderradio").is(" :checked")){
                    var currency;
                    if(currency = getCurrency()){
                        
                    }
                }else{
                    getCurrency();
                }
            }
        });
      });
})
