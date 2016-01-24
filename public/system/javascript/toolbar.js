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
 * @version $Id: toolbar.js,v 1.2 2007/07/22 23:12:31 kpapst Exp $
 * @author Kevin Papst 
 */

// --------------------------------------------------
//  TOOLBAR FUNCTIONS
//  required javascript variables:
//  - styleImageDir
// --------------------------------------------------

    var actionHandler = {
        actions : [],
        add     : function(action) { this.actions[this.actions.length] = action; },
        call    : function(id, callback) {
                    act = actionHandler.get(id);
                    if(act != null) {
                       eval('act.'+callback+'()');
                    }
                  },
        get     : function(id) {
                    for (var i = 0; i < this.actions.length; i++) {
                        if (this.actions[i].id == id) {
                            return this.actions[i];
                        }
                    }
                    return null;
                  }
    };

    function TreeActionSpacerHorizontal() {
    }
    TreeActionSpacerHorizontal.prototype.toString = function() {
        return "<div class=\"actionDivSpacerH\">|</div>";
    }

    function TreeActionHtmlElement(html, title) {
        this.html = html;
        this.title = title;
    }
    TreeActionHtmlElement.prototype.toString = function() {
        return "<div class=\"actionDiv\" onMouseOver=\"overlib('"+this.title+"');\" onMouseOut=\"nd();\">" + this.html + "</div>";
    }

    function TreeActionLink(id,image,action,title) {
        this.action = action;
        this.id = id;
        this.icon = image;
        this.title = title;
        this.enab = true;
        actionHandler.add(this);
    }

    TreeActionLink.prototype.execute = function() {
        if(this.isEnabled()) {
            eval(this.action);
        }
        return false;
    }

    TreeActionLink.prototype.getLinkHtml = function(withLink) {
        var str = "<img onMouseOut=\"actionHandler.call('"+this.id+"','out')\" onMouseOver=\"actionHandler.call('"+this.id+"','over')\" class=\"TREE_Button_Off\" id=\"image"+this.id+"\" src=\""+styleImageDir+""+this.icon+"\" align=\"absmiddle\">";
        if(withLink) {
            str = "<a href=\"#\" onClick=\"actionHandler.call('"+this.id+"','execute')\" id=\"action"+this.id+"\">" + str + "</a>";
        }
        return str;
    }

    TreeActionLink.prototype.toString = function() {
        return "<div class=\"actionDiv\" id=\""+this.id+"\">" + this.getLinkHtml(this.isEnabled()) + "</div>";
    }

    TreeActionLink.prototype.on = function() {
        document.getElementById('image'+this.id).className = document.getElementById('image'+this.id).className.replace('_Off','_On');
    }

    TreeActionLink.prototype.off = function() {
        document.getElementById('image'+this.id).className = document.getElementById('image'+this.id).className.replace('_On','_Off');
    }

    TreeActionLink.prototype.over = function() {
        if(this.isEnabled()) {
            tempClass = document.getElementById('image'+this.id).className;
            classAppend = '_Over';

            if(tempClass.indexOf(classAppend) == -1)
                document.getElementById('image'+this.id).className = tempClass + classAppend;

            if(this.title != null)
                overlib(this.title);
         }
    }

    TreeActionLink.prototype.isEnabled = function() {
        return this.enab;
    }

    TreeActionLink.prototype.setIsEnabled = function(enable) {
        this.enab = enable;
    }

    TreeActionLink.prototype.out = function() {
        if(this.isEnabled()) {
            tempClass = document.getElementById('image'+this.id).className;
            classAppend = '_Over';
            if(tempClass.indexOf(classAppend) != -1)
                document.getElementById('image'+this.id).className = tempClass.substr(0,tempClass.length-classAppend.length);
            nd();
         }
    }

    TreeActionLink.prototype.disable = function() {
        document.getElementById(this.id).innerHTML = this.getLinkHtml(false);
        tempClass = document.getElementById('image'+this.id).className;
        classAppend = '_Disabled';
        if(tempClass.indexOf(classAppend) == -1) {
            document.getElementById('image'+this.id).className = tempClass + classAppend;
        }
        this.setIsEnabled(false);
        nd();
    }

    TreeActionLink.prototype.enable = function() {
        document.getElementById(this.id).innerHTML = this.getLinkHtml(true);
        tempClass = document.getElementById('image'+this.id).className;
        classAppend = '_Disabled';
        if(tempClass.indexOf(classAppend) != -1) {
            document.getElementById('image'+this.id).className = tempClass.substr(0,tempClass.length-classAppend.length);
        }
        this.setIsEnabled(true);
    }

    function TreeActionToolbar() {
        this.tools = [];
        this.delimiter = '';
    }

    TreeActionToolbar.prototype.setDelimiter = function(delim) {
        this.delimiter = delim;
    }

    TreeActionToolbar.prototype.add = function(newTool) {
        this.tools[this.tools.length] = newTool;
    }

    TreeActionToolbar.prototype.toString = function() {
        var str = "";
        for (var i = 0; i < this.tools.length; i++) {
            str += this.tools[i].toString() + this.delimiter;
        }
        return str;
    }

    TreeActionToolbar.prototype.get = function (id) {
        for (var i = 0; i < this.tools.length; i++) {
            if (this.tools[i].id == id)
                return this.tools[i];
        }
        return null;
    }
