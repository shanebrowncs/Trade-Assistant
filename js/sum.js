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

	if(style.getPropertyValue('background-color') != "rgb(48, 48, 48)"){
		return true;
	}else{
		return false;
	}
}

$(function () {
  var down = false;
  $("#left td, #right td")
    .mousedown(function () {
      down = true;
      if($(this).html().substring(0, 3) == "CDN"){
      	var column = $(this).parent().children().index(this);

	    var row = $(this).parent().parent().children().index(this.parentNode);
	    $(this).toggleClass("highlighted");

      	if(isHighlighted($(this).closest('table').attr('id'), row, column)){
	        console.log("on");
	        updateSum($(this).closest('table').attr('id'), true, parseFloat($(this).html().substring(5)));
	    }else{
	    	console.log("off");
	    	updateSum($(this).closest('table').attr('id'), false, parseFloat($(this).html().substring(5)));
	    }
	    return false;
  	  }

  	  
      
    })
    .mouseover(function () {
      if (down) {
        if($(this).html().substring(0, 3) == "CDN"){
	      	var column = $(this).parent().children().index(this);

		    var row = $(this).parent().parent().children().index(this.parentNode);
		    $(this).toggleClass("highlighted");

	      	if(isHighlighted($(this).closest('table').attr('id'), row, column)){
		        console.log("on");
		        updateSum($(this).closest('table').attr('id'), true, parseFloat($(this).html().substring(5)));
		    }else{
		    	console.log("off");
		    	updateSum($(this).closest('table').attr('id'), false, parseFloat($(this).html().substring(5)));
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