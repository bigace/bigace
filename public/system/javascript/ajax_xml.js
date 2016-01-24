// simple api to send ajax xml requests browser independent

// request object
var BIGACEAjaxXmlRequest = function()
{}

BIGACEAjaxXmlRequest.prototype.GetHttpRequest = function()
{
	if ( window.XMLHttpRequest )		// Gecko
		return new XMLHttpRequest() ;
	else if ( window.ActiveXObject )	// IE
		return new ActiveXObject("MsXml2.XmlHttp") ;
}

BIGACEAjaxXmlRequest.prototype.PostUrl = function( urlToCall, parameters, asyncFunctionPointer )
{
	var oAjaxXmlRequest = this ;

	var bAsync = ( typeof(asyncFunctionPointer) == 'function' ) ;

	var oXmlHttp = this.GetHttpRequest() ;

	oXmlHttp.open( "POST", urlToCall, bAsync ) ;
    oXmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    oXmlHttp.setRequestHeader("Content-length", parameters.length);
    oXmlHttp.setRequestHeader("Connection", "close");

	if ( bAsync )
	{
		oXmlHttp.onreadystatechange = function()
		{
			if ( oXmlHttp.readyState == 4 )
			{
				oAjaxXmlRequest.DOMDocument = oXmlHttp.responseXML ;
				if ( oXmlHttp.status == 200 || oXmlHttp.status == 304 ) {
					asyncFunctionPointer( oAjaxXmlRequest ) ;
				} else {
					alert( 'XML request error: ' + oXmlHttp.statusText + ' (' + oXmlHttp.status + ')' ) ;
				}
			}
		}
	}

	oXmlHttp.send(parameters);

	if ( ! bAsync )
	{
		if ( oXmlHttp.status == 200 || oXmlHttp.status == 304 )
		{
			this.DOMDocument = oXmlHttp.responseXML ;
        }
		else
		{
			alert( 'XML request error: ' + oXmlHttp.statusText + ' (' + oXmlHttp.status + ')' ) ;
		}
	}

}

BIGACEAjaxXmlRequest.prototype.LoadUrl = function( urlToCall, asyncFunctionPointer )
{
	var oAjaxXmlRequest = this ;

	var bAsync = ( typeof(asyncFunctionPointer) == 'function' ) ;

	var oXmlHttp = this.GetHttpRequest() ;

	oXmlHttp.open( "GET", urlToCall, bAsync ) ;

	if ( bAsync )
	{
		oXmlHttp.onreadystatechange = function()
		{
			if ( oXmlHttp.readyState == 4 )
			{
				oAjaxXmlRequest.DOMDocument = oXmlHttp.responseXML ;
				if ( oXmlHttp.status == 200 || oXmlHttp.status == 304 ) {
					asyncFunctionPointer( oAjaxXmlRequest ) ;
				} else {
					alert( 'XML request error: ' + oXmlHttp.statusText + ' (' + oXmlHttp.status + ')' ) ;
				}
			}
		}
	}

	oXmlHttp.send( null ) ;

	if ( ! bAsync )
	{
		if ( oXmlHttp.status == 200 || oXmlHttp.status == 304 )
		{
			this.DOMDocument = oXmlHttp.responseXML ;
        }
		else
		{
			alert( 'XML request error: ' + oXmlHttp.statusText + ' (' + oXmlHttp.status + ')' ) ;
		}
	}
}

BIGACEAjaxXmlRequest.prototype.SelectNodes = function( xpath )
{
	if ( document.all )		// IE
		return this.DOMDocument.selectNodes( xpath ) ;
	else					// Gecko
	{
		var aNodeArray = new Array();

		var xPathResult = this.DOMDocument.evaluate( xpath, this.DOMDocument,
				this.DOMDocument.createNSResolver(this.DOMDocument.documentElement), XPathResult.ORDERED_NODE_ITERATOR_TYPE, null) ;
		if ( xPathResult )
		{
			var oNode = xPathResult.iterateNext() ;
 			while( oNode )
 			{
 				aNodeArray[aNodeArray.length] = oNode ;
 				oNode = xPathResult.iterateNext();
 			}
		}
		return aNodeArray ;
	}
}

BIGACEAjaxXmlRequest.prototype.SelectSingleNode = function( xpath )
{
	if ( document.all )		// IE
		return this.DOMDocument.selectSingleNode( xpath ) ;
	else					// Gecko
	{
		var xPathResult = this.DOMDocument.evaluate( xpath, this.DOMDocument,
				this.DOMDocument.createNSResolver(this.DOMDocument.documentElement), 9, null);

		if ( xPathResult && xPathResult.singleNodeValue )
			return xPathResult.singleNodeValue ;
		else
			return null ;
	}
}

// failsafe reading of singlenode values
// @access private
function readXmlValue(node) {
    if (node != null && node.firstChild != null && node.firstChild.data != null)
        return node.firstChild.data;
    return '';
}

function readXmlBooleanValue(node) {
    s = readXmlValue(node);
    if(s != '' && (s == 'TRUE' || s == 'true'))
        return true;
    return false;
}
