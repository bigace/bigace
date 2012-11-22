function popup(url, name, width, height)
{
    fenster = open (url,name,"menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=no,height="+height+",width="+width+",screenX=0,screenY=0");
    bBreite=screen.width;
    bHoehe=screen.height;
    fenster.moveTo((bBreite-430)/2,(bHoehe-500)/2);
	return fenster;
}

function changeContentByID(elementID, newtext) 
{
    if(document.getElementById(elementID) == null)
        return false;

    document.getElementById(elementID).innerHTML = newtext;
    return true;
}

// return -1 if element could not be found
// return true if element is visible afterwards
// return false if element is hidden afterwards
function toogleVisibilityById(elementID)
{
    if(document.getElementById(elementID) == null)
        return false;

    if(document.getElementById(elementID).style.visibility == 'hidden' || document.getElementById(elementID).style.display == 'none') {
        document.getElementById(elementID).style.visibility = 'visible';
        document.getElementById(elementID).style.display = 'block';
        return true;
    }
    else {
        document.getElementById(elementID).style.visibility = 'hidden';
        document.getElementById(elementID).style.display = 'none';
        return false;
    }
}


function showHelp(urlToHelp)
{
    fenster = open (urlToHelp,"Manual","menubar=no,toolbar=no,statusbar=no,directories=no,location=no,scrollbars=yes,resizable=no,height=550,width=450,screenX=0,screenY=0");
    bBreite=screen.width;
    bHoehe=screen.height;
    fenster.moveTo((bBreite-450)/2,(bHoehe-550)/2);
}

function tooltip(msg) {
    overlib(msg, VAUTO, WIDTH, 250);
}

function showJSError(ecaption, emsg) {
    overlib(emsg, STICKY, CENTER, OFFSETY, -30, CAPTION, ecaption, WIDTH, 250, CLOSECLICK);
}
