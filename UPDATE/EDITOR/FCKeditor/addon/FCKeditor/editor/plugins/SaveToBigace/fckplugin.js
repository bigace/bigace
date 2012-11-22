
var BigaceSaveCommand = function() { this.Name = 'SaveToBigace' ;}
BigaceSaveCommand.prototype.Execute = function() { parent.saveBigaceMenu(); }
BigaceSaveCommand.prototype.GetState = function() { return FCK_TRISTATE_OFF ; }

FCKCommands.RegisterCommand( 'SaveToBigace'		, new BigaceSaveCommand() ) ;

var oSaveToBigace = new FCKToolbarButton( 'SaveToBigace', FCKLang['DlgSaveToBigaceTitle'] ) ;
oSaveToBigace.IconPath	= FCKConfig.PluginsPath + 'SaveToBigace/SaveToBigace.gif' ;

FCKToolbarItems.RegisterItem( 'SaveToBigace', oSaveToBigace ) ;
