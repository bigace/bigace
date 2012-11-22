{sort assign="classleftindex2" var=$classleftindex}
{foreach key=subpackage item=files from=$classleftindex2}
  <div class="package">
	{if $subpackage != ""}<strong>{$subpackage}</strong><br />{/if}
	{section name=files loop=$files}
    {if $subpackage != ""}<span style="padding-left: 1em;">{/if}
		{if $files[files].link != ''}<a href="{$files[files].link}">{/if}{$files[files].title}{if $files[files].link != ''}</a>{/if}
    {if $subpackage != ""}</span>{/if}
	 <br />
	{/section}
  </div>
{/foreach}
