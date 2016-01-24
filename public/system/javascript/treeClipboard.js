/*
 * BIGACE - a PHP and MySQL based Web CMS.
 * Copyright (C) Kevin Papst.
 *
 * BIGACE is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * For further information visit {@link http://www.bigace.de www.bigace.de}.
 *
 * @version $Id: treeClipboard.js,v 1.2 2007/07/22 23:13:27 kpapst Exp $
 * @author Kevin Papst 
 */

    // --------------------------------------------------
    //  MENU TREE FUNCTIONS
    // --------------------------------------------------

    var adminMenuHelper = {
        item            : null,
    	getName         : function()     { if(this.item == null) return null; return this.item.getName(); },
    	getMenuID       : function()     { if(this.item == null) return null; return this.item.getID(); },
    	getLanguageID   : function()     { if(this.item == null) return null; return this.item.getLanguage(); },
    	getItem         : function()     { return this.item; },
    	setItem         : function(item) { this.item = item; }
    };

	function createNodeClone(node) {
	    if(node.folder) {
            return new WebFXLoadTreeItem(node.text,node.src,node.action);
        }
        return new WebFXTreeItem(node.text,node.action,null,node.icon);
	}

	// returns the currently selected item (not tree node!) or null
	function getSelectedItem() {
	    if(adminMenuHelper.getItem() == null)
	        return null;
	    return adminMenuHelper.getItem();
	}

	function setSelectedItem(item) {
	    adminMenuHelper.setItem(item);
	}

    // --------------------------------------------------
    //   CLIPBOARD FUNCTIONS, used for Cut/Copy/Paste
    // --------------------------------------------------

    var menuClipboard = {
        parentNode      : null,
        node            : null,
        item            : null,
        cutted          : false,
        isCutted        : function() { return this.cutted; },
        setIsCutted     : function(c)    { this.cutted = c; },
        getItem         : function() { return this.item; },
        setItem         : function(item)    { this.item = item; },
        getNode         : function() { return this.node; },
        setNode         : function(node)    { this.node = node; },
        getParent       : function() { return this.parentNode; },
        setParent       : function(p)  { this.parentNode = p; },
        refresh         : function() { this.parentNode = null; this.node = null; this.item = null; this.cutted = false; }
    };


    // returns the clipboard or null
	function getClipboard() {
        return menuClipboard;
	}

	function cutClipboard() {
	    menu = getSelectedItem();
        node = getTree().getSelected();
	    if(menu != null && node != null) {
	        setClipboardItem(menu,node,node.parentNode,true);
	        return true;
	    }
        return false;
	}

	function copyClipboard() {
	    menu = getSelectedItem();
        node = getTree().getSelected();
	    if(menu != null && node != null) {
	        setClipboardItem(menu,node,node.parentNode,false);
	        return true;
	    }
        return false;
	}

	function pasteClipboard() {
	    c = getClipboard();
        clipNode = c.getNode();
	    if(clipNode != null)
	    {
	        // first add cause otherwise the focus will be changed
	        newNode = createNodeClone(clipNode);
            getTree().getSelected().add(newNode);
            clipNode.remove();
            // send ajax callback

            emptyClipboard();
	        return true;
        }
        return false;
    }

	// sets the actual clipboard item and refreshes the clipboard action view
	function setClipboardItem(item,node,parent,cutted) {
	    c = getClipboard();
	    c.setIsCutted(cutted);
	    c.setItem(item);
	    c.setNode(node);
	    c.setParent(parent);
        validateClipboardState(null);
	}

	// emptys the clipboard and refreshes the clipboard action view
	function emptyClipboard() {
	    getClipboard().refresh();
	}

