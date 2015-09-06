function updateSum(table, add, amount){
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
			if(add){
				console.log("Adding");
				var cur = parseFloat(sumLbl.innerHTML.substring(orig.length + 12));
				console.log(sumLbl.innerHTML.substring(orig.length + 12));
				sum = cur + amount;
				sum = sum.toFixed(2);
			}else{
				console.log("Subtracting");
				var cur = parseFloat(sumLbl.innerHTML.substring(orig.length + 12))
				console.log(sumLbl.innerHTML.substring(orig.length + 12));
				sum = cur - amount;
				sum = sum.toFixed(2);
			}
		}catch(ex){
			console.log("Exception: " + ex);
			return;
		}
	}else{
		sum = amount;
		sum = sum.toFixed(2);
	}

	if(sum > 0.0){
		sumLbl.innerHTML = orig + " (Sum: CDN$ " + sum + ")";
	}else{
		sumLbl.innerHTML = orig;
	}
}

function isHighlighted(table, x, y){
	var table = document.getElementById(table);
	var style = window.getComputedStyle(table.rows[x].cells[y]);

	console.log("vars: " + table + " " + x + " " + y);

	if(style.getPropertyValue('background-color') != "rgb(48, 48, 48)"){
		console.log("style: " + style.getPropertyValue('background-color'));
		console.log("on");
		return true;
	}else{
		console.log("style: " + style.getPropertyValue('background-color'));
		console.log("off");
		return false;
	}
}

function getCurrency(){
	var cookies = document.cookie.split(";");
	var cookie = cookies[0].replace("currency=", "");
	//cookie = cookie.substring(0, cookie.length - 1);
	return cookie;
}

$(function () {
  var down = false;
  $("#left td, #right td")
    .mousedown(function () {
      down = true;
	  console.log("mouse down");
	  var cookie = getCurrency();
	  console.log(cookie);
      if($(this).html().substring(0, 3) == cookie){
      	var column = $(this).parent().children().index(this);

	    var row = $(this).parent().parent().children().index(this.parentNode);
	    $(this).toggleClass("highlighted");
	    if($(this).hasClass("checked")){
	    	updateSum($(this).closest('table').attr('id'), false, parseFloat($(this).html().substring(4, 9)));
	    	$(this).removeClass("checked");
	    	console.log("off");
	    }else{
	    	updateSum($(this).closest('table').attr('id'), true, parseFloat($(this).html().substring(4, 9)));
	    	$(this).addClass("checked");
	    	console.log("on");
	    }
	    return false;
  	  }



    })
    .mouseover(function () {
      if (down) {
		var cookie = getCurrency();
        if($(this).html().substring(0, 3) == cookie){
	      	var column = $(this).parent().children().index(this);

		    var row = $(this).parent().parent().children().index(this.parentNode);
		    $(this).toggleClass("highlighted");
		    if($(this).hasClass("checked")){
		    	updateSum($(this).closest('table').attr('id'), false, parseFloat($(this).html().substring(4, 9)));
		    	$(this).removeClass("checked");
		    	console.log("off");
		    }else{
		    	updateSum($(this).closest('table').attr('id'), true, parseFloat($(this).html().substring(4, 9)));
		    	$(this).addClass("checked");
		    	console.log("on");
		    }
		    return false;
  	    }
      }
    });

  $(document)
    .mouseup(function () {
      down = false;
    });
});
