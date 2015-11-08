function getIndexFromString(cur){
    switch(cur){
        case "USD":
            return 0;
        case "CAD":
            return 1;
        case "GBP":
            return 2;
        case "EUR":
            return 3;
        case "RUB":
            return 4;
        case "JPY":
            return 5;
        default:
            return 0;
    }
}

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}


$(document).ready(function(){
    document.getElementById("curID").selectedIndex = getIndexFromString(getCookie("currency"));

    if(getCookie("manualprice").localeCompare("true") == 0)
        document.getElementById("manualID").checked = true;
    else
        document.getElementById("manualID").checked = false;
});
