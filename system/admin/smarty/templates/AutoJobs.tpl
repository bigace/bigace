{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

{* if count($RUN_RESULT) > 0}
<p>{translate key="job.name"}: {$RUN_RESULT.name}</p>
<p>{translate key="job.description"}: {$RUN_RESULT.description}</p>
<p>{translate key="job.state"}: {$RUN_RESULT.state}</p>
<p>{translate key="job.message"}: {$RUN_RESULT.message}</p>
{/if *}
<table class="tablesorter" cellspacing="0">
<col />
<col />
<col />
<col />
<col />
<col />
<col />
<thead>
	<tr>
	    <th>{translate key="job.name"} &amp; {translate key="job.description"}</th>
		<th>{translate key="job.class"}</th>
    	<th>{translate key="job.state"}</th>
    	<th>{translate key="job.message"}</th>
    	<th>{translate key="job.last"}</th>
    	<th>{translate key="job.next"}</th>
    	<th>{translate key="job.execute"}</th>
    </tr>
</thead>
<tbody>
{foreach from=$AUTO_JOBS item=job}
    <tr>
	    <td><b>{$job.NAME}</b><br>{$job.DESCRIPTION}</td>
	    <td>{$job.CLASS}</td>
	    <td>
			<form style="display:inline" action="" method="POST">
	    	<input type="hidden" name="jobID" value="{$job.ID}">
	    	<input type="hidden" name="mode" value="save">
			<select name="state">
			<option value="0"{if $job.STATE == '0'} selected{/if}>{translate key=job.state.0}</option>
			{if $job.STATE == '10'}
			<option value="10" selected>{translate key=job.state.10}</option>
			{elseif $job.STATE == '20'}
			<option value="20" selected>{translate key=job.state.20}</option>
			{/if}
			<option value="30"{if $job.STATE == '30'} selected{/if}>{translate key=job.state.30}</option>
			</select>
			<input type="image" src="{$STYLE_DIR}save.png">
			</form>
		</td>
	    <td>{$job.MESSAGE}</td>
	    <td>{$job.LAST|date_format:"%Y:%m:%d<br>%H:%M:%S"}</td>
	    <td>{$job.NEXT|date_format:"%Y:%m:%d %H:%M:%S"}<br><input type="checkbox" name="recalculate" value="1" id="recalc{$job.NAME}"><label for="recalc{$job.NAME}">&nbsp;{translate key="job.recalculate"}</label></td>
	    <td>
			<form style="display:inline" action="" method="POST">
	    	<input type="hidden" name="jobID" value="{$job.ID}">
	    	<input type="hidden" name="mode" value="execute">
	    	<input type="submit" value="{translate key="job.execute"}">
			</form>
	    </td>
    </tr>
	</form>
{/foreach}
</tbody>
</table>
            
{literal}
<script type="text/javascript">
$(document).ready( function() { 
		$(".tablesorter").tablesorter({ widgets: ['zebra'], headers: { 2: {sorter: false}, 6: {sorter: false} } }); 
	} 
);
</script>
{/literal}
