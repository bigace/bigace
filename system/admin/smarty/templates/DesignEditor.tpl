{* @license http://opensource.org/licenses/gpl-license.php GNU Public License
   @author Kevin Papst
   @copyright Copyright (C) Kevin Papst
   @version $Id$ *}

{admin_box_support_header}

    {translate key="template" assign="title"}
    {admin_box_header title="$title &quot;$NAME&quot;" toggle=false}

            <form action="{$SAVE_URL}" method="post">
            <input type="hidden" name="designName" value="{$NAME}">

            <table border="0" width="100%" cellspacing="5">
            <tr>
                <td>
                    {translate key="description"}:
                </td>
                <td>
		            <input type="text" name="description" value="{$DESCRIPTION}" size="50"/>
                </td>
            </tr>
            <tr>
                <td>
                    {translate key="template"}:
                </td>
                <td>
		            {$TEMPLATE}
                </td>
            </tr>
            <tr>
                <td>
                    {translate key="stylesheet"}:
                </td>
                <td>
		            {$STYLESHEET}
                </td>
            </tr>
            <tr>
                <td valign="top">
                    {translate key="portlets"}:
                </td>
                <td>
		            <input id="pt123" type="checkbox" value="1" name="portletSupport" {$PORTLET_SUPPORT}>
		            <label for="pt123">{translate key="portlets_info"}</label>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    {translate key="portletColumns"}:
                </td>
                <td>
		            <input type="text" name="portletColumns" value="{$PORTLETS}" size="50"/>
		            <br />
		            <i>{translate key="comma_separated"}</i>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    {translate key="contents}:
                </td>
                <td>
		            <input type="text" name="contents" value="{$CONTENTS}" size="50"/>
		            <br />
		            {translate key="contents_info"}
		            <br />
		            <i>{translate key="comma_separated"}</i>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <input type="submit" value="{translate key="save"}">
                </td>
            </tr>
            </table>

            </form>

    {admin_box_footer}    
    
{admin_box_support_footer}
