{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

<div id="tabpanes" class="ui-tabs">

	<ul>
		<li><a href="#tabpage1"><span>{translate key="menu_credits"}</span></a></li>
		<li><a href="#tabpage2"><span>{translate key="menu_license"}</span></a></li>
		<li><a href="#tabpage3"><span>{translate key="menu_feedback"}</span></a></li>
	</ul>

   <div id="tabpage1"{if $ACTION == "feedback"} class="ui-tabs-hide"{/if}>
      <h2>{translate key="title_credits"}</h2>
      <p><i>{translate key="credits_intro"}</i></p>
      <h1 class="credit">Credits</h1>
      <p>BIGACE {translate key="created_by"} <b>Kevin Papst</b> (<a href="http://www.kevinpapst.de/" target="_blank">http://www.kevinpapst.de/</a>)
        <br /><br /><i>{translate key="third_party_intro"}</i></p>

        {foreach from=$CREDITS item="creditlist" key="title"}
            <h1 class="credit">{$title}</h1>
            <ul class="creditList">
                {foreach from=$creditlist item="credit" key="creditTitle"}
                	<li class="creditEntry">
                	    <b>{$creditTitle}</b> by {$credit.copyright}
                	    <br />{$credit.description}<br />
                	    <a href="{$credit.weblink}" target="_blank">{$credit.weblink}</a>
                	</li>
                {/foreach}
           	</ul>
        {/foreach}
   </div>

   <div id="tabpage2" class="ui-tabs-hide">
      <h2>{translate key="title_license"}</h2>
      <div class="license">
          <b>Version {bigace_version build=true} - Copyright (C) 2002-{$YEAR_TODAY} Kevin Papst</b>
            <br>
            See 
            <a href="http://www.opensource.org/licenses/gpl-license.php" target="_blank">Opensource.org</a> and
            <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GNU.org</A>.
            <br/><br/>
            <div>{$LICENSE}</div>
       </div>
   </div>

   <div id="tabpage3"{if $ACTION != "feedback"} class="ui-tabs-hide"{/if}>
      <h2>{translate key="title_feedback"}</h2>
        {if $FEEDBACK.status != ''}<div class="info">{$FEEDBACK.status}</div>{/if}
        {if $FEEDBACK.error != ''}<div class="error">{$FEEDBACK.error}</div>{/if}
        <form action="{$FEEDBACK.url}" method="post">
        <input type="hidden" name="mode" value="send" />
        <div id="feedbackContainer">
        <p>{translate key="feedback_name"}:</p>
        <input type="text" class="feedbackInput" name="data[name]" value="{$FEEDBACK.name}">
        <br><br>

        <p>{translate key="feedback_email"}:</p>
        <input type="text" class="feedbackInput" name="data[email]" value="{$FEEDBACK.email}">
        <br><br>

        <p>{translate key="feedback_subject"}*:</p>
        <input type="text" class="feedbackInput" name="data[subject]" value="{$FEEDBACK.subject}">
        <br><br>

        <p>{translate key="feedback_message"}*:</p>
        <textarea wrap="soft" name="data[message]">{$FEEDBACK.message}</textarea>
        <br>
        <input type="checkbox" name="data[emailOwn]"> <span style="font-size:85%">{translate key="feedback_emailcopy"}</span>
        <br><br>
        <span style="font-size:85%">* {translate key="feedback_required"}</span>
        <br>
        <br>
        </div>
        <div align="center" style="margin-top:5px""><button type="submit">{translate key="feedback_send"}</button></div>
        </form>
   </div>
</div>

{literal}
<script type="text/javascript">
$(document).ready( function() { 
    $("#tabpanes").tabs();
	{/literal}
	{if $ACTION == "feedback"}
		$("#tabpanes").tabs("select", 2);
	{/if}
	{literal}
});
</script>
{/literal}
