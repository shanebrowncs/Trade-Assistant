$(function(){
    var availableTags = [];
    $("#autocomplete").keyup(function(){
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
    });
})
