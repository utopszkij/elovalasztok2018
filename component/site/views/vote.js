// vote.js
//
// This is the Javascript support for the animated ballot. Rows are
// automatically resorted in ranking order, and buttons are provided
// for convenient manipulation of the ballot.

/**
* selectedIndex -ek rendezése
* drag utáni update up/down gombok és egeres drag használják
*/
function drag_update2(e,u) {
	var i = 0;
	var row = false;
	var rows = jQuery('#preftable tr');		
	if (e == undefined) e = null;
	if (u == undefined) u = null;
	for (i=1; i<rows.length; i++) {
	  row = rows[i];
	  row.cells[3].firstChild.selectedIndex = i - 1;
	}
	jQuery('#okBtn').attr('disabled','');
}

// selectes pozició változtatás utáni átrendezés
function resort_row(i) {
	var j = 0;
	var w1 = '';
	var w2 = 0;
	var w3 = 0;	
	var w4 = 0;
	var w5 = '';  
	var rows = jQuery('#preftable tr');		
	for (i=rows.length -1; i > 1; i--) {
		for (j=1; j<i; j++) {
			if (rows[j].cells[3].firstChild.selectedIndex > 
				rows[i].cells[3].firstChild.selectedIndex) {

				// w1 = rows[i].cells[1].firstChild.innerHTML;
				w1 = rows[i].cells[1].innerHTML;
				w2 = rows[i].cells[3].firstChild.selectedIndex;
				w3 = rows[i].cells[1].style.backgroundColor;
				w4 = rows[i].cells[1].style.color;
				w5 = rows[i].cells[1].id;

				//rows[i].cells[1].firstChild.innerHTML = rows[j].cells[1].firstChild.innerHTML;
				rows[i].cells[1].innerHTML = rows[j].cells[1].innerHTML;
				rows[i].cells[3].firstChild.selectedIndex = rows[j].cells[3].firstChild.selectedIndex;
				rows[i].cells[1].style.backgroundColor = rows[j].cells[1].style.backgroundColor;	
				rows[i].cells[1].style.color = rows[j].cells[1].style.color;	
				rows[i].cells[1].id = rows[j].cells[1].id;	

				//rows[j].cells[1].firstChild.innerHTML = w1;
				rows[j].cells[1].innerHTML = w1;
				rows[j].cells[3].firstChild.selectedIndex = w2;
				rows[j].cells[1].style.backgroundColor = w3;	
				rows[j].cells[1].style.color = w4;	
				rows[j].cells[1].id = w5;	
			}
		}
	}
	return;
}


jQuery(function() {

	/**
	* preftable tbody sortable bekapcsolása és drag_update2 hozzá rendelése
	*/
    jQuery('#preftable tbody').sortable({'items':'tr:not(.heading)',
			'axis':'y', 
			'update':drag_update2});
	/**
	* fel/le gombok müködésének definiálása
	*/
	jQuery(".up,.down").click(function(){
		    var row = jQuery(this).parents("tr:first");
		    if (jQuery(this).is(".up")) {
		        row.insertBefore(row.prev());
				drag_update2();
		    } else {
		        row.insertAfter(row.next());
				drag_update2();
		    }
		});
	/**
	* OK gomb funkció
	*/
	jQuery("#okBtn").click(function() {
		var s = '';
		var i = 0;
		var row = false;
		var rows = jQuery('#preftable tr');		
		var errorMsg = '';

		for (i=1; i<rows.length; i++) {
		  if (s != '') {
				s += ',';
		  }
		  row = rows[i];
		  s += row.cells[1].id.substr(6,10)+'='+(1 + row.cells[3].firstChild.selectedIndex);
		  if (row.cells[1].firstChild.innerHTML.substr(0,2) == '--') {
				if (i < rows.length - 1) {
					if (row.cells[3].firstChild.selectedIndex === 
							rows[i+1].cells[3].firstChild.selectedIndex) {
						errorMsg = 'A "többit ellenzem" vonalnál nem lehet "holtverseny"! ';
					}	
				}
				if (row.cells[3].firstChild.selectedIndex === rows[i-1].cells[3].firstChild.selectedIndex) {
						errorMsg = 'A "többit ellenzem" vonalnál nem lehet "holtverseny"! ';
				}
		  }
		}
		document.forms.szavazatForm.szavazat.value = s;
		if (errorMsg == '') {
			document.forms.szavazatForm.submit();
		} else {
			popupAlert(errorMsg);
		}
		return;
	});
});


