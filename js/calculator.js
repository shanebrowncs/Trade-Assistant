$(function(){
    var availableTags = [
        "AK-47 Asiimov",
        "AK-47 Vulkan",
        "AWP Boom"
    ];
    $("#autocomplete").keyup(function(){
        var inputData = $("#autocomplete").val();
        $.ajax({
                url: "autocomplete.php",
                data: "search=" + inputData,
                success: function(msg){
                    alert("Returned Value: " + msg);
                }
        });
    });
    $("#autocomplete").autocomplete({
        source: availableTags
    });
})
