
var tt=null;
var ti=null;
document.onmousemove = updateTooltip;

function updateTooltip(e) {
	x = (document.all) ? window.event.x + document.body.scrollLeft : e.pageX;
	y = (document.all) ? window.event.y + document.body.scrollTop  : e.pageY;
	if (tt != null) {
		tt.style.left = (x + 20) + "px";
		tt.style.top  = (y + 20) + "px";
	}
}

function showTooltip(strId,objSelf) 
{
	ti = document.getElementById(strId+"_img");
	ti.title = "";
	tt = document.getElementById(strId);
	tt.style.display = "block";
	objSelf.onmouseout = new Function('fx','tt.style.display = "none"');
}

function toogleCheckbox(checkboxid)
{
	myButton = document.getElementById(checkboxid);
	if (myButton.checked == true)
	    myButton.checked = false;
	else
	    myButton.checked = true;
}

function changeDisabledState(buttonId)
{
	myButton = document.getElementById(buttonId);
	if (myButton.disabled == true)
	    myButton.disabled = false;
	else
	    myButton.disabled = true;
}

function toogleLicense(checkboxid, buttonId)
{
	toogleCheckbox(checkboxid);
	changeDisabledState(buttonId);
}

function toogleRadionButton(elemid)
{
	myButton = document.getElementById(elemid);
	if (myButton.checked != true) {
	    myButton.checked = true;
	}
}

function switchVisibility(elementID)
{
    if(document.getElementById(elementID).style.visibility != 'visible')
    {
        document.getElementById(elementID).style.visibility = 'visible';
        document.getElementById(elementID).style.display = 'block';
        return true;
    }
    else
    {
        document.getElementById(elementID).style.visibility = 'hidden';
        document.getElementById(elementID).style.display = 'none';
        return true;
    }
    return false;
}