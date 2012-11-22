
var BigaceLoadCommand = function() { this.Name = 'LoadFromBigace' ;}
BigaceLoadCommand.prototype.Execute = function() { parent.showBigaceMenuChooser(); }
BigaceLoadCommand.prototype.GetState = function() { return FCK_TRISTATE_OFF ; }

FCKCommands.RegisterCommand( 'LoadFromBigace'		, new BigaceLoadCommand() ) ;

var oLoadFromBigace = new FCKToolbarButton( 'LoadFromBigace', FCKLang['DlgLoadFromBigaceTitle'] ) ;
oLoadFromBigace.IconPath	= FCKConfig.PluginsPath + 'LoadFromBigace/LoadFromBigace.gif' ;

FCKToolbarItems.RegisterItem( 'LoadFromBigace', oLoadFromBigace ) ;
