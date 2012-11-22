// load the item from the given URL, see PHP Class:
// loadClass('util', 'ApplicationLinks');
// example: ApplicationLinks::getAjaxItemInfoURL
// @access public
function loadItem(requestUrl, asynchronous)
{
    var oXML = new BIGACEAjaxXmlRequest() ;

    if ( asynchronous ) {
        // Asynchronous load.
        oXML.LoadUrl(requestUrl, "_prepareItemFromXml");
    }
    else
    {
        oXML.LoadUrl(requestUrl);
        return _prepareItemFromXml( oXML );
    }
}


// this creates an JS BigaceItem Object from the XML Answer
// @access private
function _prepareItemFromXml(itemXml) {
    if(itemXml != null && itemXml.DOMDocument != null) {
        var item = new BigaceItem();
        item._initFromXml(itemXml);
        return item;
    }
    return null;
}

// -----------------------------------------
// Item definition
// -----------------------------------------

// definition of a BigaceItem - not all possible values from the PHP Class are available
var BigaceItem = function() {
    this.itemtype = '';
    this.id = '';
    this.name = '';
    this.language = '';
    this.description = '';
    this.catchwords = '';
    this.parent = '';
    this.languages = new Array();
    this.leaf = true;
    this.inWorkflow = false;
    this.hidden = false;
    this.readable = false;
    this.writeable = false;
    this.deletable = false;
}

// define public getters to access Item values
BigaceItem.prototype.getItemtype = function () { return this.itemtype; }
BigaceItem.prototype.getID = function () { return this.id; }
BigaceItem.prototype.getName = function () { return this.name; }
BigaceItem.prototype.getLanguage = function () { return this.language; }
BigaceItem.prototype.getDescription = function () { return this.description; }
BigaceItem.prototype.getCatchwords = function () { return this.catchwords; }
BigaceItem.prototype.getParent = function () { return this.parent; }
BigaceItem.prototype.getLanguages = function () { return this.languages; }
BigaceItem.prototype.canRead = function () { return this.readable; }
BigaceItem.prototype.canWrite = function () { return this.writeable; }
BigaceItem.prototype.canDelete = function () { return this.deletable; }
BigaceItem.prototype.isHidden = function () { return this.hidden; }
BigaceItem.prototype.isInWorkflow = function () { return this.inWorkflow; }
BigaceItem.prototype.isLeaf = function () { return this.leaf; }

// read all values from the xml answer and fill variables
// @access private
BigaceItem.prototype._initFromXml = function (xmlAnswer) {
    this.itemtype = readXmlValue(xmlAnswer.SelectSingleNode('Item/Itemtype'));
    this.id = readXmlValue(xmlAnswer.SelectSingleNode('Item/ID'));
    this.name = readXmlValue(xmlAnswer.SelectSingleNode('Item/Name'));
    this.language = readXmlValue(xmlAnswer.SelectSingleNode('Item/Language'));
    this.description = readXmlValue(xmlAnswer.SelectSingleNode('Item/Description'));
    this.catchwords = readXmlValue(xmlAnswer.SelectSingleNode('Item/Catchwords'));
    this.parent = readXmlValue(xmlAnswer.SelectSingleNode('Item/Parent'));
    this.readable = readXmlBooleanValue(xmlAnswer.SelectSingleNode('Item/Right/Read'));
    this.writeable = readXmlBooleanValue(xmlAnswer.SelectSingleNode('Item/Right/Write'));
    this.deletable = readXmlBooleanValue(xmlAnswer.SelectSingleNode('Item/Right/Delete'));
    this.hidden = readXmlBooleanValue(xmlAnswer.SelectSingleNode('Item/IsHidden'));
    this.inWorkflow = readXmlBooleanValue(xmlAnswer.SelectSingleNode('Item/Workflow/InWorkflow'));
    this.leaf = readXmlBooleanValue(xmlAnswer.SelectSingleNode('Item/IsLeaf'));

	var langNodes = xmlAnswer.SelectNodes('Item/Languages/Language') ;
	for ( var i = 0 ; i < langNodes.length ; i++ )
	{
	    this.languages.push(readXmlValue(langNodes[i]));
	}
}

