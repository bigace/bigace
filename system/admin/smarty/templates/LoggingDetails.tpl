{$BACK_LINK}

<table class="tablesorter"cellspacing="0">
<col />
<col />
<thead>
	<tr>
		<th>{translate key="name"}</th>
		<th>{translate key="value"}</th>
	</tr>
</thead>
<tbody>
    <tr>
        <td>{translate key="id"}</td>
        <td>{$ID}</td>
    </tr>
    <tr>
        <td>{translate key="namespace"}</td>
        <td valign="top">{$NAMESPACE}</td>
    </tr>
    <tr>
        <td>{translate key="date"}</td>
        <td valign="top">{$TIMESTAMP}</td>
    </tr>
    <tr>
        <td>{translate key="level"}</td>
        <td>{$LEVEL}</td>
    </tr>
    <tr>
        <td>{translate key="file"}</td>
        <td valign="top">{$FILE}</td>
    </tr>
    <tr>
        <td>{translate key="line"}</td>
        <td valign="top">{$LINE}</td>
    </tr>
    <tr>
        <td>{translate key="user"}</td>
        <td valign="top">{$USER_ID}</td>
    </tr>
    <tr>
        <td>{translate key="message"}</td>
        <td valign="top">{$MESSAGE}</td>
    </tr>
    <tr>
        <td>{translate key="stacktrace"}</td>
        <td valign="top"><pre>{$STACKTRACE}</pre></td>
    </tr>
</tbody>
</table>

<br/>

<script type="text/javascript"> {literal}
$(document).ready( function() { 
        $(".tablesorter").tablesorter({ widgets: ['zebra'] }); 
    } 
); {/literal}
</script>

{$BACK_LINK}
