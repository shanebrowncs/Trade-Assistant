function updateSum(table, add, amount, currency){
	if(table == "left"){
		var sumLbl = document.getElementById("trader");
		var orig = sumLbl.innerHTML.substring(0, sumLbl.innerHTML.indexOf(':') + 1);
	}
	else{
		var sumLbl = document.getElementById("requested");
		var orig = sumLbl.innerHTML.substring(0, sumLbl.innerHTML.indexOf(':') + 1);
	}

	var sum = 0.0;
	if(sumLbl.innerHTML != orig){
		try{
			var cur = parseFloat(sumLbl.innerHTML.substring(orig.length + 11).replace(",", ""));
			if(add){
				sum = cur + amount;
			}else{
				sum = cur - amount;
			}
			sum = sum.toFixed(2);
		}catch(ex){
			console.log("Exception: " + ex);
			return;
		}
	}else{
		sum = amount;
		sum = sum.toFixed(2);
	}

	if(sum > 0.0){
		sumLbl.innerHTML = orig + " (Sum: " + currency + " " + sum + ")";
	}else{
		sumLbl.innerHTML = orig;
	}
}

function getCurrency(){
	var cookies = document.cookie.split(";");
	var cookie = cookies[0].replace("currency=", "");
	return cookie;
}

window.onload = function () {
  var down = false;
  $("#left td, #right td")
    .mousedown(function () {
      down = true;
	  var cookie = getCurrency();
      if($(this).html().substring(0, 3) == cookie){
	    $(this).toggleClass("highlighted");
	    if($(this).hasClass("checked")){
	    	updateSum($(this).closest('table').attr('id'), false, parseFloat($(this).html().substring(4).replace(",", "")), cookie);
	    	$(this).removeClass("checked");
	    }else{
	    	updateSum($(this).closest('table').attr('id'), true, parseFloat($(this).html().substring(4).replace(",", "")), cookie);
	    	$(this).addClass("checked");
	    }
	    return false;
  	  }
  })

    .mouseover(function () {
      if (down) {
		var cookie = getCurrency();
        if($(this).html().substring(0, 3) == cookie){
		    $(this).toggleClass("highlighted");
		    if($(this).hasClass("checked")){
		    	updateSum($(this).closest('table').attr('id'), false, parseFloat($(this).html().substring(4)), cookie);
		    	$(this).removeClass("checked");
		    }else{
		    	updateSum($(this).closest('table').attr('id'), true, parseFloat($(this).html().substring(4)), cookie);
		    	$(this).addClass("checked");
		    }
		    return false;
  	    }
      }
    });

  $(document)
    .mouseup(function () {
      down = false;
    });
}
