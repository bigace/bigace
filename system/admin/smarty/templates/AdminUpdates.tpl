{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

{literal}
  <script type="text/javascript">
  $(document).ready(function(){
    $("#tabpanes").tabs();
  });
  </script>
{/literal}

<div id="tabpanes">

    <ul>
        <li><a href="#tabpage1"><span>{translate key="plugins_legend"}</span></a></li>
        <li><a href="#tabpage2"><span>{translate key="general_legend"}</span></a></li>
        <li><a href="#tabpage3"><span>{translate key="upload_legend"}</span></a></li>
        <li><a href="#tabpage4"><span>{translate key="find_legend"}</span></a></li>
    </ul>

   <div id="tabpage1">
      <p>
      {translate key="plugins_info"}
      </p>
      {if count($PLUGINS_ACTIVE) == 0 && count($PLUGINS_DEACTIVE) == 0}
      <p style="margin:10px 0 10px 0"><b>{translate key="plugins_none"}</b></p>
      {/if}      
      {if count($PLUGINS_ACTIVE) > 0}
        <p style="margin:10px 0 0 0"><b>{translate key="active_plugins" default="Activated Plugins"}</b></p>
        <table id="pluginsa" class="tablesorter" cellspacing="0">
        <col width="150px" />
        <col width="90px"  />
        <col />
        <col width="130px" />
        <thead>
            <tr>
                <th>{translate key="name"}</th>
                <th align="center">{translate key="version"}</th>
                <th>{translate key="description"}</th>
                <th align="center">{translate key="action"}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$PLUGINS_ACTIVE item="plugin"}
          <tr>
            <td>{$plugin.title}</td>
            <td align="center">{$plugin.version}</td>
            <td>{$plugin.description}
                 <i>By {$plugin.author}.</i>
            </td>
            <td align="center">
                <form action="{$DEACTIVATE_URL}&name={$plugin.id}" method="post">
                    <button type="submit">{translate key="action_deactivate"}</button>
                </form>
            </td>
          </tr>
        {/foreach}
        </tbody>
        </table>
      {/if}
      {if count($PLUGINS_DEACTIVE) > 0}
        <p style="margin:10px 0 0 0"><b>{translate key="deactive_plugins" default="Deactivated Plugins"}</b></p>
        <table id="pluginsd" class="tablesorter" cellspacing="0">
        <col width="150px" />
        <col width="90px"  />
        <col />
        <col width="130px" />
        <thead>
            <tr>
                <th>{translate key="name"}</th>
                <th align="center">{translate key="version"}</th>
                <th>{translate key="description"}</th>
                <th align="center">{translate key="action"}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$PLUGINS_DEACTIVE item="plugin"}
          <tr>
            <td>{$plugin.title}</td>
            <td align="center">{$plugin.version}</td>
            <td>{$plugin.description}
                 <i>By {$plugin.author}.</i>
            </td>
            <td align="center">
                <form action="{$ACTIVATE_URL}&name={$plugin.id}" method="post">
                    <button class="ok" type="submit">{translate key="action_activate"}</button>
                </form>
            </td>
          </tr>
        {/foreach}
        </tbody>
        </table>
      {/if}
      <p>
      {translate key="plugins_hint"}
      </p>

   </div>
   

   <div id="tabpage2">
    {if count($MODULES) > 0}
    {foreach from=$MODULES key=modName item=moduleGroup}
      <b>{translate key=group_$modName default="Group $modName"}</b><br/>
        <table class="cut1 tablesorter" cellspacing="0">
        <col width="150px" />
        <col width="90px"  />
        <col />
        <col width="130px" />
        <thead>
            <tr>
                <th>{translate key="update_name"}</th>
                <th align="center">{translate key="update_version"}</th>
                <th>{translate key="update_description"}</th>
                <th align="center">{translate key="action"}</th>
            </tr>
        </thead>
        <tbody>
        {cycle name="rowCss" values="odd,even" reset=true} 
        {foreach from=$moduleGroup item=module}
          {cycle name="rowCss" values="even,odd" print=false assign="css"}        
            <tr class="{$css}">
                <td>{translate key=$module->getTitle() default=$module->getTitle()}</td>
                <td align="center">{translate key=$module->getVersion() default=$module->getVersion()}</td>
                <td>{translate key=$module->getDescription() default=$module->getDescription()}</td>
                <td align="center">
                    <form action="{$module->getSetting('installURL')}" method="post">
                        <button class="ok" type="submit">{translate key="update_install"}</button>
                    </form>
                </td>
            </tr>
        {/foreach}
        </tbody>
        </table>
        <br />
    {/foreach}
    {else}
    {translate key="error_update_empty"}
    {/if}
   </div>

   <div id="tabpage3">
      <form method="post" action="{$UPLOAD_URL}" enctype="multipart/form-data">
        <input type="hidden" name="{$PARAM_MODE}" value="uploadZip">
        <p>
            <b>{translate key="upload_info"}</b>
            <br /><br />
            {translate key="upload_attention"}
        </p>
        <br />
        <input type="checkbox" name="newUpdateInstall" id="uploadInstall" checked="checked"><label for="uploadInstall">{translate key="upload_install"}</label>
        <br/>
        <input type="file" name="newUpdateZip" size="50" />
        <button type="submit">{translate key="upload_button"}</button>
      </form>
   </div>

   <div id="tabpage4">
      <p>
      {translate key="find_howto"}
      {translate key="find_wiki"} <a href="http://www.bigace.de/plugins/" target="_blank">http://www.bigace.de/plugins/</a>.
      </p>
      {if count($REMOTE_EXTENSIONS) == 0}
        <form action="{$SEARCH_URL}" method="post">
          <input type="hidden" name="{$PARAM_MODE}" value="searchExtensions">
          <!--input type="radio" name="type" value="all" id="findCompatible" checked="checked"><label for="findCompatible" onMouseOver="tooltip('{translate key="find_version"}')" onMouseOut="nd()">Kompatible Erweiterungen</label>
          <input type="radio" name="type" value="all" id="findAll"><label for="findAll" onMouseOver="tooltip('{translate key="find_all"}')" onMouseOut="nd()">Alle Erweiterungen</label-->
          <input type="submit" value="{translate key="find_search"}">
        </form>
      {else}
        <table id="cut2" class="tablesorter" cellspacing="0">
        <col width="150px" />
        <col />
        <thead>
            <tr>
                <th>{translate key="update_name"}</th>
                <th>{translate key="update_description"}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$REMOTE_EXTENSIONS item="extension"}
            {cycle name="rowCss" values="even,odd" print=false assign="css"}        
              <tr class="{$css}">
                <td><a href="{$extension.link}" target="_blank" title="{translate key="find_web_link"}">{$extension.name}</a></td>
                <td>{$extension.description}</td>
              </tr>
            {/cycle}
        {/foreach}
        </tbody>
        </table>
      {/if}
   </div>


</div>

{literal}
<script type="text/javascript">
$(document).ready( function() { 
        $(".cut1").tablesorter({ widgets: ['zebra'], headers: { 3: {sorter: false} } }); 
        $("#cut2").tablesorter({ headers: { 1: {sorter: false} }  }); 
        $("#pluginsa").tablesorter({ widgets: ['zebra'], headers: { 2: {sorter: false}, 3: {sorter: false} }  }); 
        $("#pluginsd").tablesorter({ widgets: ['zebra'], headers: { 2: {sorter: false}, 3: {sorter: false} }  }); 
    {/literal}
    {if $ACTION == "plugins"}
        $("#tabpanes").tabs("select", 4);
    {elseif count($REMOTE_EXTENSIONS) > 0}
        $("#tabpanes").tabs("select", 3);
    {/if}
    {literal}
});
</script>
{/literal}
