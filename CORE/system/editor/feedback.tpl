    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset={$charset}">
        <link rel="stylesheet" href="{$stylesheet}" type="text/css">
        <style type="text/css">
        {literal}
        body,td { font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; margin-left:5px; margin-right:5px; margin-top:5px; font-size:11px; background-color:#FFFFFF; }
        img { border-width:0px; }
        {/literal}
        </style>
        <script type="text/javascript">
        <!--
        {if $isError}
            parent.showEditorErrorText("{$feedback}", null);
        {else}
            parent.showEditorStatusText("{$feedback}", 2000);
        {/if}

        {if $close}
            parent.doClose();
        {/if}
        // -->
        </script>
    </head>
    <body>
    </body>
    </html>
