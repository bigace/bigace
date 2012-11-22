// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) Kevin Papst
// http://www.kevinpapst.de/
// ----------------------------------------------------------------------------
// Initially written for the BIGACE CMS:
// http://www.bigace.de/
// ----------------------------------------------------------------------------
// Basic CSS set. Feel free to add more tags
// Removed all keybindings for international usage.
// ----------------------------------------------------------------------------
mySettings = {	
	onEnter:   		{},
	onShiftEnter:  	{keepDefault:false, placeHolder:'Your comment here', openWith:'\n\/* ', closeWith:' *\/'},
	onCtrlEnter:  	{keepDefault:false, placeHolder:"classname", openWith:'\n.', closeWith:' { \n'},
	onTab:    		{keepDefault:false, openWith:'  '},
	markupSet:  [ 	{name:'Class', className:'class', placeHolder:'Properties here...', openWith:'.[![Class name]!] {\n', closeWith:'\n}'},
				 	{separator:'---------------' },
					{name:'Bold', className:'bold', replaceWith:'font-weight:bold;'},
					{name:'Italic', className:'italic', replaceWith:'font-style:italic;'},
					{name:'Stroke through',  className:'stroke', replaceWith:'text-decoration:line-through;'},
					{separator:'---------------' },
					{name:'Lowercase', className:'lowercase', replaceWith:'text-transform:lowercase;'},
					{name:'Uppercase', className:'uppercase', replaceWith:'text-transform:uppercase;'},
					{separator:'---------------' },
					{name:'Alignments', className:'alignments', dropMenu:[																
						{name:'Left', className:'left', replaceWith:'text-align:left;'},
						{name:'Center', className:'center', replaceWith:'text-align:center;'},
						{name:'Right', className:'right', replaceWith:'text-align:right;'},
						{name:'Justify', className:'justify', replaceWith:'text-align:justify;'}
						]
					},
					{separator:'---------------' },
					{name:'Text indent', className:'indent', openWith:'text-indent:', placeHolder:'5px', closeWith:';' },
					{name:'Letter spacing', className:'letterspacing', openWith:'letter-spacing:', placeHolder:'5px', closeWith:';' },
					{name:'Line height', className:'lineheight', openWith:'line-height:', placeHolder:'1.5', closeWith:';' },
					{separator:'---------------' },
					{name:'Padding', className:'padding', dropMenu:[
							{name:'Top', className:'top', openWith:'padding-top:', placeHolder:'5px', closeWith:';' },
							{name:'Left', className:'left', openWith:'padding-left:', placeHolder:'5px', closeWith:';' },
							{name:'Right', className:'right', openWith:'padding-right:', placeHolder:'5px', closeWith:';' },
							{name:'Bottom', className:'bottom', openWith:'padding-bottom:', placeHolder:'5px', closeWith:';' }								]
					},
					{separator:'---------------' },
					{name:'Background Image', className:'background', replaceWith:'background:url([![Source:!:http://]!]) no-repeat 0 0;' },
					{separator:'---------------' },
					{name:'Import CSS file',  className:'css', replaceWith:'@import "[![Source file:!:style.css]!]";' }
				]
};
