<?php
/**
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 *
 * BIGACE is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * BIGACE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * For further information visit {@link http://www.kevinpapst.de www.kevinpapst.de}.
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.administration
 * @subpackage item.menu
 */

Hooks::add_action('admin_html_head', 'jstree_admin_menu', 10, 0);
function jstree_admin_menu()
{
    ?>
    <script type="text/javascript" src="<?php echo _BIGACE_DIR_PUBLIC_WEB; ?>system/javascript/ajax_xml.js"></script>
    <script type="text/javascript" src="<?php echo _BIGACE_DIR_PUBLIC_WEB; ?>system/javascript/bigace_ajax.js"></script>
    
    <link type="text/css" rel="stylesheet" href="<?php echo _BIGACE_DIR_ADDON_WEB; ?>jquery/jstree/source/tree_component.css">
    <script type="text/javascript" src="<?php echo _BIGACE_DIR_ADDON_WEB; ?>jquery/metadata/jquery.metadata.js"></script>    
    <script type="text/javascript" src="<?php echo _BIGACE_DIR_ADDON_WEB; ?>jquery/jstree/source/css.js"></script>    
    <script type="text/javascript" src="<?php echo _BIGACE_DIR_ADDON_WEB; ?>jquery/jstree/source/tree_component.js"></script>    
    <style type="text/css">
    #tree-panel { margin-bottom:5px; }
    #tree-panel .right { float: right; }
    #tree-panel .tree { height:309px; padding:5px 0; overflow:auto; border:1px solid silver; border-top:none; }
    #tree-panel .menu { border:1px solid #CCC; background:#F0F0EE; height:27px; position:relative; margin-bottom:1px; }
    #tree-panel .menu a { float:left; border:1px solid #F0F0EE; display:inline; height:20px; margin-left:1px; width:20px; margin-top:2px; }
    #tree-panel .menu input { float:left; display:inline; height:20px; margin-left:5px; margin-top:6px; }
    #tree-panel .menu a:hover { border:1px solid #0A246A; background:#B2BBD0; }
    #tree-panel .menu a img { border:0; display:block; margin:2px; }
    #tree-panel .menu .cmenu { width:22px; height:22px; position:absolute; right:1px; top:1px; border:1px solid #F0F0EE; background:#F0F0EE; overflow:hidden; }
    #tree-panel .menu .cmenu a { margin:0 0 0 0; }
    #tree-panel .menu .hover { height:auto; overflow:visible; border:1px solid #CCC; z-index:1; }
    #tree-data { margin-left:-7px; }
    #tree-panel .menu a.disabled,
    #tree-panel .menu a.disabled:hover { color:silver; text-decoration:none; border:1px solid transparent; cursor:default; outline: none;
	        background-repeat: no-repeat; background-position: 3px center; background-color:transparent;
	        opacity:0.5; -ms-filter:'alpha(opacity=50)'; filter:alpha(opacity=50); }
    </style>
    <?php
}

check_admin_login();
admin_header(true);

import('classes.item.ItemService');
import('classes.menu.Menu');
import('classes.menu.MenuService');
import('classes.menu.MenuAdminService');
import('classes.administration.MenuAdminMask');
import('classes.util.ApplicationLinks');
import('classes.workflow.WorkflowService');
import('classes.language.Language');
import('classes.language.LanguageEnumeration');
import('classes.util.links.EditorLink');
import('classes.util.MenuLink');
import('classes.util.LinkHelper');
import('classes.permission.UseCaseEditContent');

// TODO - is that the correct check? what about "admin_menus"?
$editContent = has_permission(_BIGACE_FRIGHT_USE_EDITOR) || has_permission('edit_menus');

/*

!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
TODO's
- rename
- multiple select via checkbox 
- expand all
- search
- newpage same level
- move css styles to general style.css
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

Löschen:
- _BIGACE_DIR_PUBLIC_WEB; ?>system/javascript/treeClipboard.js
    
        toolbar.add( new TreeActionLink('ExpandAll','down.png','tree.expandAll()', '<?php echo getTranslation('tree_action_expandAll'); ?>') );
        //toolbar2.add( new TreeActionLink('hidden','item_1_hidden.png','void(0)', '<?php echo getTranslation('item_state_hidden'); ?>') );
        //toolbar2.add( new TreeActionLink('workflow','item_1_workflow.png','void(0)', '<?php echo getTranslation('item_state_workflow'); ?>') );
        toolbar3.add( new TreeActionLink('newPageLevelSame','item_1_new.png','createPageSameLevel()', '<?php echo getTranslation('item_action_newpage_same'); ?>') );
*/

// --------------------------------------------------------------------------------------- //
// --------------------------------------------------------------------------------------- //

    $openOnSelect = ConfigurationReader::getConfigurationValue('admin.menu', 'open.on.select', false);

    $treeLanguage = (isset($_GET['treeLanguage']) ? $_GET['treeLanguage'] : ADMIN_LANGUAGE);

    $_SERVICE = new ItemService(_BIGACE_ITEM_MENU);
    //$topLevel = $GLOBALS['_SERVICE']->getItem(_BIGACE_TOP_LEVEL,ITEM_LOAD_FULL,$treeLanguage);

    $ms = new MenuService();
    $topLevel = $ms->getMenu(_BIGACE_TOP_LEVEL,$treeLanguage);

    function createItemTreeURL($id=null,$lng=null) {
        if(is_null($id))
            return createAdminLink('ajaxCommand', array('ajaxCmd' => 'TreeJSON'), 'tree.js', 'plain');
        else
            return createAdminLink('ajaxCommand', array('ajaxCmd' => 'TreeJSON', 'treeID' => $id, 'treeLng' => $lng), 'tree.js', 'plain');
    }

    ?>

    <div id="tree-panel">
		<div class="menu">
		    <input type="checkbox" id="openOnSelect" value="" title="<?php echo getTranslation('tree_action_openOnSelect'); ?>" />
			<a href="#" rel="multiple" title="TODO: translate"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>multiple.png" alt="" /></a>
			<?php /*
			<a href="#" class="disabled" rel="copy" title="<?php echo getTranslation('tree_action_Copy'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>copy.png" alt="" /></a>
			<a href="#" class="disabled" rel="cut" title="<?php echo getTranslation('tree_action_Cut'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>cut.png" alt="" /></a>
			<a href="#" class="disabled" rel="paste" title="<?php echo getTranslation('tree_action_Paste'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>paste.png" alt="" /></a>
			
			*/ ?>
			<div class="right">
			<a href="#" rel="create" title="<?php echo getTranslation('item_action_newpage_below'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>item_1_new.png" alt="" /></a>
			<a href="#" rel="refresh" title="<?php echo getTranslation('tree_action_reloadTree'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>refresh.png" alt="" /></a>
			</div>
		</div>
		<?php /*
		<a href="#" rel="options" title="<?php echo getTranslation('options'); ?>"><img id="optionsImg" src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>down.png" alt="" /></a>
		<div class="menu" id="menuoptions">
		    <input type="checkbox" id="openOnSelect" value="" title="<?php echo getTranslation('tree_action_openOnSelect'); ?>" />
			<a href="#" rel="multiple" title="TODO: translate"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>multiple.png" alt="" /></a>
		</div>
	    Panel.options    = function () { 
            var isVisible = $('#menuoptions').is(':visible');
            if(isVisible) {
                $('#optionsImg').attr('src', '<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>down.png');
            }
            else {
                $('#optionsImg').attr('src', '<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>up.png');
            }
	        $('#menuoptions').toggle(); 
	    }			
	    */ ?>
		<div class="menu">
			<a href="#" class="disabled" rel="admin" title="<?php echo getTranslation('admin'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>menu_fckeditor.png" alt="" /></a>
			<?php if ($editContent) { ?>
			<a href="#" class="disabled" rel="edit" title="<?php echo getTranslation('edit_content'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>menu_htmleditor.png" alt="" /></a>
			<?php } ?>
			<a href="#" class="disabled" rel="category" title="<?php echo getTranslation('category'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>category_link.png" alt="" /></a>
			<a href="#" class="disabled" rel="permission" title="<?php echo getTranslation('rights'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>rights.png" alt="" /></a>
			<a href="#" class="disabled" rel="preview" title="<?php echo getTranslation('preview'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>preview.png" alt="" /></a>
			<a href="#" class="disabled" rel="remove" title="<?php echo getTranslation('delete'); ?>""><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>delete.png" alt="" /></a>
			<a href="#" class="disabled" rel="reorder" title="<?php echo getTranslation('item_reorder_childs'); ?>"><img src="<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>reorder.png" alt="" /></a>
			<div class="cmenu">
			<?php
		    $am = new Language($treeLanguage);
            echo '<a href="#" class="lang" rel="'.$am->getShortLocale().'" ><img class="lang" src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$am->getShortLocale().'.gif" alt=""/></a>' . "\n";
            //echo '<a rel="'.$am->getShortLocale().'" href="#0" class="lang" title="'.$am->getName(ADMIN_LANGUAGE).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$am->getShortLocale().'.gif" alt=""/></a>' . "\n";

            $langEnum = new LanguageEnumeration();
            $langJS = "'".$treeLanguage."'";
            $langCSS = "    .".$am->getShortLocale() . " {} \n";
            for($i = 0; $i < $langEnum->count(); $i++) {
                $langTemp = $langEnum->next(); 
                if($langTemp->getShortLocale() != $treeLanguage) {
                    $langCSS .= "    .".$langTemp->getShortLocale() . " {} \n";
                    $langJS .= ", '".$langTemp->getShortLocale()."'";
//                    echo '<a rel="'.$langTemp->getShortLocale().'" href="'.createAdminLink($GLOBALS['MENU']->getID(), array('treeLanguage' => $langTemp->getShortLocale())).'" class="lang" title="'.$langTemp->getName(ADMIN_LANGUAGE).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$langTemp->getShortLocale().'.gif" alt=""/></a>' . "\n";
                    echo '<a rel="'.$langTemp->getShortLocale().'" href="#'.($i+1).'" class="lang" title="'.$langTemp->getName(ADMIN_LANGUAGE).'"><img src="'.$GLOBALS['_BIGACE']['style']['DIR'].'languages/'.$langTemp->getShortLocale().'.gif" alt=""/></a>' . "\n";
                }
            }
            ?>
			</div>
		</div>
	</div>
    <style type="text/css">
    <?php echo $langCSS; ?>
    </style>
    <form id="multipleForm" method="post" target="menuAdminContent" action="<?php echo createAdminLink("itemMenu"); ?>">
    </form>
    <div id="tree-data"></div>
    <script type="text/javascript">
    <!--
    var rootID = <?php echo $topLevel->getID(); ?>;
    var rootLang = "<?php echo $treeLanguage; ?>";
    
	Panel = {};
	Panel.contextNormal = [ 
	            { 
                    id      : "admin",
                    label   : "<?php echo getTranslation('admin'); ?>", 
                    icon    : "<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>menu_fckeditor.png",
                    visible : function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return TREE_OBJ.check("renameable", NODE); }, 
                    action  : function (NODE, TREE_OBJ) { action_admin_context(NODE); } 
                },
		<?php if ($editContent) { ?>
                { 
                    id      : "edit",
                    label   : "<?php echo getTranslation('edit_content'); ?>", 
                    icon    : "<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>menu_htmleditor.png",
                    visible : function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return TREE_OBJ.check("renameable", NODE); }, 
                    action  : function (NODE, TREE_OBJ) { action_editContent_context(NODE); }
                },
		<?php } ?>
                "separator",/*
                { 
                    id      : "rename",
                    label   : "Rename", 
                    icon    : "rename.png",
                    visible : function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return TREE_OBJ.check("renameable", NODE); }, 
                    action  : function (NODE, TREE_OBJ) { TREE_OBJ.rename(); } 
                },*/
                { 
                    id      : "categories",
                    label   : "<?php echo getTranslation('item_category'); ?>", 
                    icon    : "<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>category_link.png",
                    visible : function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return TREE_OBJ.check("renameable", NODE); }, 
                    action  : function (NODE, TREE_OBJ) { action_categories_context(NODE); }
                },
                { 
                    id      : "permissions",
                    label   : "<?php echo getTranslation('rights'); ?>", 
                    icon    : "<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>rights.png",
                    visible : function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return TREE_OBJ.check("renameable", NODE); }, 
                    action  : function (NODE, TREE_OBJ) { action_permissions_context(NODE); } 
                },
                { 
                    id      : "preview",
                    label   : "<?php echo getTranslation('preview'); ?>", 
                    icon    : "<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>preview.png",
                    visible : function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return true; }, 
                    action  : function (NODE, TREE_OBJ) { action_preview_context(NODE); }
                },
                "separator",
                {
                    id      : "create",
                    label   : "<?php echo getTranslation('item_action_newpage_below'); ?>", 
                    icon    : "<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>item_1_new.png",
                    visible : function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return TREE_OBJ.check("creatable", NODE); }, 
                    action  : function (NODE, TREE_OBJ) { action_createMenu_context(NODE); } 
                },
                "separator",
                { 
                    id      : "delete",
                    label   : "<?php echo getTranslation('delete'); ?>",
                    icon    : "<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>delete.png", // deleteNode()
                    visible : function (NODE, TREE_OBJ) { var ok = true; $.each(NODE, function () { if(TREE_OBJ.check("deletable", this) == false) ok = false; return false; }); return ok; }, 
                    action  : function (NODE, TREE_OBJ) { action_remove_context(NODE); } 
                } 
    ];
    
	Panel.contextMultiple = [ 
	            { 
                    id      : "updateselected",
                    label   : "Update selected", 
                    icon    : "<?php echo $GLOBALS['_BIGACE']['style']['DIR']; ?>menu_fckeditor.png",
                    visible : function (NODE, TREE_OBJ) { if(NODE.length != 1) return false; return TREE_OBJ.check("renameable", NODE); }, 
                    action  : function (NODE, TREE_OBJ) { 
                        var inForm = '<input type="hidden" name="mode" value="<?php echo _MODE_UPDATE_MULTIPLE; ?>" />';
                        var state = 0;
                        $("#tree-data").find("a.checked").each(function(i) {
                            var data = Panel.jsTree.get_node(this).metadata();
                            inForm += '<input type="hidden" name="data[ids][]" value="'+data.id+'" />';
                            state = 1;
                        });
                        if(state == 1) {
                            $("#multipleForm").html(inForm).submit();
                        }
                    } 
                } 
    ];
	
    Panel.treeConfig = {
        data    : {
            type    : "json",
            async   : true,
            async_data : function (NODE) {  // PARAMETERS PASSED TO SERVER
                return { treeID : $(NODE).attr("id") || <?php echo _BIGACE_TOP_PARENT; ?>, treeLng: rootLang } 
            },
            url     : "<?php echo createItemTreeURL(); ?>"
        },
        selected    : false,  // FALSE or STRING or ARRAY
        opened      : [],     // ARRAY OF INITIALLY OPENED NODES
        ui          : {
	        theme_name  : "bigace",
            context     : Panel.contextNormal
        },
        rules   : {
            multiple    : false,    // FALSE | CTRL | ON - multiple selection off/ with or without holding Ctrl
            metadata    : "mdata",    // FALSE or STRING - attribute name (use metadata plugin)
            type_attr   : "rel",    // STRING attribute name (where is the type stored if no metadata)
            multitree   : false,    // BOOL - is drag n drop between trees allowed
            createat    : "bottom", // STRING (top or bottom) new nodes get inserted at top or bottom
            use_inline  : true,    // CHECK FOR INLINE RULES - REQUIRES METADATA
            clickable   : "all",    // which node types can the user select | default - all
            renameable  : "all",    // which node types can the user select | default - all
            deletable   : "all" , // which node types can the user delete | default - all
//                creatable : [ "folder" ], // which node types can the user create in | default - all
            draggable   : "all",
            dragrules   : [ "folder inside folder" ],
            drag_copy   : false,    // FALSE | CTRL | ON - drag to copy off/ with or without holding Ctrl
            drag_button : "left",
            droppable : [ "tree-drop" ]
        },
        lang : {
            new_node    : "New Page",
            loading     : "Loading ..."
        },
        callback    : {
            beforechange: function(NODE,TREE_OBJ) { return true }, // before focus  - should return true | false
            beforeopen  : function(NODE,TREE_OBJ) { return true },
            beforeclose : function(NODE,TREE_OBJ) { return true },
            beforemove  : function(NODE,REF_NODE,TYPE,TREE_OBJ) { return true },  // before move   - should return true | false
            beforecreate: function(NODE,REF_NODE,TYPE,TREE_OBJ) { return true },  // before create - should return true | false
            beforerename: function(NODE,LANG,TREE_OBJ) { return true }, // before rename - should return true | false
            beforedelete: function(NODE,TREE_OBJ) { return true },      // before delete - should return true | false
            onselect    : function(NODE,TREE_OBJ) {
                var data = Panel.jsTree.selected.metadata();
                if(openAdminTabOnSelect() && TREE_OBJ.check("renameable", NODE)) {
                    openMenuAdministration(data.id,data.language);
                }
                validatePanel(data,NODE,TREE_OBJ);
            },
            ondeselect  : function(NODE,TREE_OBJ) { },                  // node deselected
            onchange    : function(NODE,TREE_OBJ) { 
		        if(TREE_OBJ.settings.ui.theme_name == "checkbox") {
			        var $this = $(NODE).is("li") ? $(NODE) : $(NODE).parent();
			        if($this.children("a.unchecked").size() == 0) {
				        TREE_OBJ.container.find("a").addClass("unchecked");
			        }
			        $this.children("a").removeClass("clicked");
			        if($this.children("a").hasClass("checked")) {
				        $this.find("li").andSelf().children("a").removeClass("checked").removeClass("undetermined").addClass("unchecked");
				        var state = 0;
			        }
			        else {
				        $this.find("li").andSelf().children("a").removeClass("unchecked").removeClass("undetermined").addClass("checked");
				        var state = 1;
			        }
			        /*
		            $this.parents("li").each(function () { 
			            if(state == 1) {
				            if($(this).find("a.unchecked, a.undetermined").size() - 1 > 0) {
					            $(this).parents("li").andSelf().children("a").removeClass("unchecked").removeClass("checked").addClass("undetermined");
					            return false;
				            }
				            else $(this).children("a").removeClass("unchecked").removeClass("undetermined").addClass("checked");
			            }
			            else {
				            if($(this).find("a.checked, a.undetermined").size() - 1 > 0) {
					            $(this).parents("li").andSelf().children("a").removeClass("unchecked").removeClass("checked").addClass("undetermined");
					            return false;
				            }
				            else $(this).children("a").removeClass("checked").removeClass("undetermined").addClass("unchecked");
			            }
		            });
		            */
		        }
            },
            onrename    : function(NODE,LANG,TREE_OBJ) { },             // node renamed ISNEW - TRUE|FALSE, current language
            onmove      : function(NODE,REF_NODE,TYPE,TREE_OBJ,ROLLBACK) {       // move completed (TYPE is BELOW|ABOVE|INSIDE)
                action_move_context(NODE,REF_NODE,TYPE,TREE_OBJ,ROLLBACK);
            },    
            oncopy      : function(NODE,REF_NODE,TYPE,TREE_OBJ) { },    // copy completed (TYPE is BELOW|ABOVE|INSIDE)
            oncreate    : function(NODE,REF_NODE,TYPE,TREE_OBJ) { return false; },    // node created, parent node (TYPE is createat)
            ondelete    : function(NODE, TREE_OBJ) {  },// node deleted
            onopen      : function(NODE, TREE_OBJ) { },                 // node opened
            onopen_all  : function(TREE_OBJ) { },                       // all nodes opened
            onclose     : function(NODE, TREE_OBJ) { },                 // node closed
            error       : function(TEXT, TREE_OBJ) { },                 // error occured
            ondblclk    : function(NODE, TREE_OBJ) {                    // double click on node - defaults to open/close & select
                TREE_OBJ.toggle_branch.call(TREE_OBJ, NODE); 
                TREE_OBJ.select_branch.call(TREE_OBJ, NODE);
                var data = Panel.jsTree.selected.metadata();
                openMenuAdministration(data.id,data.language);
            },
            onrgtclk    : function(NODE, TREE_OBJ, EV) { }, // right click - to prevent use: EV.preventDefault(); EV.stopPropagation(); return false
            onload      : function(TREE_OBJ) { },
            onfocus     : function(TREE_OBJ) { },
            ondrop      : function(NODE,REF_NODE,TYPE,TREE_OBJ) {}
        }
    };
    
    // on page load
    $(function () {
        Panel.jsTree = $.tree_create();
        Panel.jsTree.init($("#tree-data"),$.extend({},Panel.treeConfig));

	    $("#tree-panel .menu")
		    .find("a").not(".lang")
			    .bind("click", function (event) {
				    try { 
				        if(!$(this).hasClass("disabled")) {
				            m = $(this).attr("rel");
				            Panel[$(this).attr("rel")]();
				        }
					    event.stopPropagation();
					    event.preventDefault();
				    } catch(err) { }
				    this.blur();
				    return false;
			    })
			    .end().end()
		    .children(".cmenu")
			    .hover( function () { $(this).addClass("hover"); }, function () { $(this).removeClass("hover") });

	    $("#tree-panel .menu a.lang")
		    .live("click", function (event) {
        		rootLang = ($(this).attr("rel"));

                Panel.jsTree.destroy();
                Panel.jsTree.init($("#tree-data"), $.extend({},Panel.treeConfig));

			    // move language icon below
			    $(this).clone().prependTo($(this).parent()); 
			    $(this).remove(); 
			    
			    event.preventDefault();
			    event.stopPropagation();
			    return false;
        });

        $('#openOnSelect').attr('checked', <?php echo (($openOnSelect) ? 'true' : 'false'); ?>);
	    $('#menuoptions').toggle(); 
    });

	Panel.category   = function () { action_categories(); }
	Panel.permission = function () { action_permissions(); }
	Panel.create	 = function () { action_createMenu(); }
	Panel.admin      = function () { action_admin(); }
	Panel.edit       = function () { action_editContent(); }
	Panel.remove	 = function () { action_remove() }
	Panel.copy		 = function () { if(Panel.jsTree.selected) { Panel.jsTree.copy(); } }
	Panel.cut		 = function () { action_cut(); }
	Panel.preview	 = function () { action_preview(); }
	Panel.paste		 = function () { action_paste(); }
	Panel.refresh    = function () { Panel.jsTree.refresh(); }
	Panel.reorder    = function () { action_sortMenus(); }

	Panel.multiple   = function () { 
        if(Panel.treeConfig.ui.theme_name == 'bigace') {
            Panel.treeConfig.ui.theme_name = 'checkbox';
            Panel.treeConfig.ui.context = Panel.contextMultiple;
            $('#openOnSelect').attr('checked', false);
        }
        else {
            Panel.treeConfig.ui.theme_name = 'bigace';
            Panel.treeConfig.ui.context = Panel.contextNormal;
            $('#openOnSelect').attr('checked', true);
        }
        
        Panel.jsTree.destroy();
        Panel.jsTree.init($("#tree-data"), $.extend({},Panel.treeConfig));
	}
	
    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------

    function action_cut_context(NODE) {
        Panel.jsTree.cut(); 
        validatePanel(NODE.metadata(),NODE,Panel.jsTree);
    }
    
    function action_cut() {
        if(Panel.jsTree.selected) { 
            action_cut_context(Panel.jsTree.selected);
        }
    }

    function action_paste_context(NODE) {
        Panel.jsTree.paste(); 
        validatePanel(NODE.metadata(),NODE,Panel.jsTree);
    }
    
    function action_paste() {
        if(Panel.jsTree.selected) { 
            action_paste_context(Panel.jsTree.selected);
        }
    }

    function action_preview_context(NODE) {
        var data = NODE.metadata();
        try {
            <?php
            $prevLink = new MenuLink();
            $prevLink->setItemID("'+data.id+'");
            $prevLink->setLanguageID("'+data.language+'");
            ?>
            openAdminFrame('<?php echo LinkHelper::getUrlFromCMSLink($prevLink); ?>');
        } catch(ex) {
            alert('Error in preview: ' + ex);
        }
    }
    
    function action_preview() {
        if(Panel.jsTree.selected) {
            action_preview_context(Panel.jsTree.selected);
        } 
    }

    function action_remove_context(NODE) {
        var itemid = NODE.metadata().id;
        var langid = NODE.metadata().language;
        try {
            var ditem = requestItemInfo(itemid, langid, false);
            msg = '<?php echo addslashes(getTranslation('confirm_delete_node')); ?>';
            if(!ditem.isLeaf()) {
                msg = '<?php echo addslashes(getTranslation('confirm_delete_tree')); ?>\r\n' + msg;
            }
            if(confirm(msg + ' ' + ditem.getName())) {
                var result = deletePage(ditem.getID());
                if(result.getResult()) {
                    Panel.jsTree.remove(NODE);
                    //$.each(Panel.jsTree.selected, function () { Panel.jsTree.remove(this); });
                    validatePanel();
                    // TODO support rollback?
                } else {
                    alert( result.getMessage() );
                }
            }
        } catch(ex) {
            alert('Failed deleting Page: ' + ex);
        }
    }

    function action_remove() {
        if(Panel.jsTree.selected) {
            action_remove_context(Panel.jsTree.selected);
        } 
    }

    function action_move_context(NODE,REF_NODE,TYPE,TREE_OBJ,ROLLBACK) {
        //alert("Moved " + NODE.textContent + " from " + dataMoved.position + " " + TYPE + " " + REF_NODE.textContent + " to " + dataMovedTo.position);
        var dataMoved = $.metadata.get(NODE);
        var dataMovedTo = $.metadata.get(REF_NODE);
        try {
            var result = movePage(dataMoved.id, dataMovedTo.id);
            if(result.getResult()) {
                validatePanel();
                TREE_OBJ.refresh(REF_NODE);
            } else {
                $.tree_rollback(ROLLBACK);
                TREE_OBJ.lock(false);
                alert( result.getMessage() );
            }
        } catch(ex) {
            alert('Failed deleting Page: ' + ex);
        }
    }

    function action_permissions_context(NODE) {
        var data = NODE.metadata();
        openScreenByMode(data.id,data.language,'showUserRights');
    }

    function action_permissions() {
        if(Panel.jsTree.selected) {
            action_permissions_context(Panel.jsTree.selected);
        } 
    }

	<?php if ($editContent) { ?>
    function action_editContent_context(NODE) {
        var data = NODE.metadata();
        try {
            <?php
            $editorLink = new EditorLink();
            $editorLink->setItemID('"+data.id+"');
            $editorLink->setLanguageID('"+data.language+"');
            $editorLink->setEditor(ConfigurationReader::getConfigurationValue('editor', 'default.editor', 'htmleditor'));
            ?>
            openAdminFrame("<?php echo LinkHelper::getUrlFromCMSLink($editorLink); ?>");
        } catch(ex) {
            alert('Error in editorByName: ' + ex);
        }
    }
    
    function action_editContent() {
        if(Panel.jsTree.selected) {
            action_editContent_context(Panel.jsTree.selected);
        } 
    }
	<?php } ?>

    function action_categories_context(NODE) {
        var data = NODE.metadata();
        openScreenByMode(data.id,data.language,'changecategory');
    }

    function action_categories() {
        if(Panel.jsTree.selected) {
            action_categories_context(Panel.jsTree.selected);
        } 
    }
    
    function action_admin_context(NODE) {
        var data = NODE.metadata();
        openMenuAdministration(data.id,data.language);	
    }
    
    function action_admin() {
        action_admin_context(Panel.jsTree.selected);
    }
    
    function action_createMenu_context(NODE) {
        var data = NODE.metadata();
        createPage(data.id, data.language);
    }
    
    function action_createMenu() {
        if(Panel.jsTree.selected) {
            var data = Panel.jsTree.selected.metadata();
            createPage(data.id, data.language);
            // create on same level
            //createPage(data.parent, data.language);
        }
        else {
            createPage(rootID, rootLang);
        }
        //Panel.jsTree.create(false, (Panel.jsTree.selected ? Panel.jsTree.selected[0] : $("#-1")));
        //TREE_OBJ.create(false, TREE_OBJ.selected);
    }

    function action_sortMenus_context(NODE) {
        var data = NODE.metadata();
        try {
            var sorturl = '<?php echo createAdminLink('menuReorder', array('parentid' => "'+data.id+'", 'lngID' => "'+data.language")); ?>;
            openAdminFrame(sorturl);
        } catch(ex) {
            alert('Error in action_sortMenus: ' + ex);
        }
    }

    function action_sortMenus() {
        if(Panel.jsTree.selected) {
            action_sortMenus_context(Panel.jsTree.selected);
        }
    }
    
    function openScreenByMode(menuid,menulng,mode) {
        var url = "<?php echo createAdminLink('menuAttributes', array('data[id]' => '"+menuid+"', 'data[langid]' => '"+menulng+"', 'mode' => '"+mode+"')); ?>";
        openAdminFrame( url );
    }    

    function togglePanelButton(REL,RULE) {
        $("#tree-panel .menu").find("a[rel='"+REL+"']").toggleClass("disabled", RULE);
    }
    
    function validatePanel(data,NODE,TREE_OBJ) {
        if (validatePanel.arguments.length == 0) {
            togglePanelButton("admin",true);
	<?php if ($editContent) { ?>
            togglePanelButton("edit",true);
	<?php } ?>
            togglePanelButton("category",true);
            togglePanelButton("permission",true);
            togglePanelButton("preview",true);
            togglePanelButton("remove",true);
            //togglePanelButton("copy",true);
            togglePanelButton("cut",true);
            togglePanelButton("paste",true);
            togglePanelButton("reorder",true);
            togglePanelButton("create",true);
        }
        else {
            togglePanelButton("admin",(!TREE_OBJ.check("renameable", NODE)));
	<?php if ($editContent) { ?>
            togglePanelButton("edit",(!TREE_OBJ.check("renameable", NODE)));
	<?php } ?>
            togglePanelButton("category",(!TREE_OBJ.check("renameable", NODE)));
            togglePanelButton("permission",(!TREE_OBJ.check("renameable", NODE)));
            togglePanelButton("preview",false);
            togglePanelButton("remove",(!TREE_OBJ.check("deletable", NODE)));
            togglePanelButton("copy",(data.id == -1));
            togglePanelButton("cut",(data.id == -1 || (!TREE_OBJ.check("renameable", NODE))));
            togglePanelButton("paste",(!Panel.jsTree.cut_nodes || (!TREE_OBJ.check("renameable", NODE))));
            togglePanelButton("reorder",(data.leaf || !TREE_OBJ.check("renameable", NODE)));
            togglePanelButton("create",(!TREE_OBJ.check("creatable", NODE)));
        }
    }

    function createPage(parentid, locale) {
        var url = "<?php echo createAdminLink('itemMenuCreate', array('data[nextAdmin]' => 'menuAttributes', 'data[id]' => '"+parentid+"', 'data[langid]' => '"+locale+"')); ?>";
        openAdminFrame( url );
    }
    
    function openMenuAdministration(menuid,menulang) {
        var url = "<?php echo createAdminLink('menuAttributes', array('data[id]' => '"+menuid+"', 'data[langid]' => '"+menulang+"', 'mode' => _MODE_EDIT_ITEM)); ?>";
        openAdminFrame( url );
    }

    function openAdminFrame(urlToContent) {
        parent.frames[1].location.href = urlToContent;
    }
    
    function openAdminTabOnSelect() {
        return document.getElementById("openOnSelect").checked;
    }

    function requestItemInfo(itemid, languageid, asynchronous) {
        var itemRequestUrl = "<?php echo ApplicationLinks::getAjaxItemInfoURL(_BIGACE_ITEM_MENU, '"+itemid+"', '"+languageid+"'); ?>";
        return loadItem(itemRequestUrl, asynchronous);
    }
      	
    // ---------------------------------------
    // AJAX FUNCTIONS
    // ---------------------------------------

    function movePage(id, newParent) {
        res = new AjaxResult();

        try {
	        var oXML = new BIGACEAjaxXmlRequest();
	        oXML.LoadUrl("<?php echo createAdminLink('ajaxCommand', array('treeID' => '"+id+"', 'parentID' => '"+newParent+"', 'ajaxCmd' => 'MovePage'), 'tree.xml', 'plain'); ?>");
	
	        if(oXML != null && oXML.DOMDocument != null) {
	            res.setResult(readXmlBooleanValue(oXML.SelectSingleNode('MovePage/Result')));
	            res.setMessage(readXmlValue(oXML.SelectSingleNode('MovePage/text')));
	        }
        } catch (exc) {
        	alert('Error occured: ' + exc);	
        	res.setMessage('Failed moving Page!');
        	res.setResult(false);
        }

        return res;
    }

    function deletePage(id) {
        res = new AjaxResult();
        
        try {
	        var oXML = new BIGACEAjaxXmlRequest();
	        oXML.LoadUrl("<?php echo createAdminLink('ajaxCommand', array('treeID' => '"+id+"', 'ajaxCmd' => 'DeletePage'), 'tree.xml', 'plain'); ?>");
	
	        if(oXML != null && oXML.DOMDocument != null) {
	            res.setResult(readXmlBooleanValue(oXML.SelectSingleNode('DeletePage/Result')));
	            res.setMessage(readXmlValue(oXML.SelectSingleNode('DeletePage/text')));
	        }
        } catch (exc) {
        	alert('Error occured: ' + exc);	
        	res.setMessage('Failed deleting Page!');
        	res.setResult(false);
        }
        
        return res;
    }

    function AjaxResult() {
        this.msg = 'Could not execute AJAX Command.';
        this.result = false;
    }
    AjaxResult.prototype.getResult = function() {
        return this.result;
    }
    AjaxResult.prototype.getMessage = function() {
        return this.msg;
    }

    AjaxResult.prototype.setResult = function(res) {
        this.result = res;
    }
    AjaxResult.prototype.setMessage = function(msg) {
        this.msg = msg;
    }      	
    // ------------------ TODO ------------------    
    
    <?php
    	$autoShow = 'false';
        if(isset($_GET['preloadID']) && isset($_GET['preloadLang'])) {
    		$preSelect = $GLOBALS['_SERVICE']->getItem($_GET['preloadID'],ITEM_LOAD_FULL,$_GET['preloadLang']);
    		$autoShow = 'true';
        }
        else {
    		$preSelect = $GLOBALS['_SERVICE']->getItem(_BIGACE_TOP_LEVEL,ITEM_LOAD_FULL,ADMIN_LANGUAGE);
        }        
    ?>
        try {
            //preloadItem = requestItemInfo("<?php echo $preSelect->getID(); ?>", "<?php echo $preSelect->getLanguageID(); ?>", false);
            //selectMenuForAdmin(preloadItem,<?php echo $autoShow; ?>);
        } catch (exc) {
            alert('Could not load preselect Item: ' + exc);
      	}
      	
    // -->
    </script>

<?php

    admin_footer(true);
    