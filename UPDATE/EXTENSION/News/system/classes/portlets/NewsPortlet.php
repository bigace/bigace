<?php
import('api.portlet.Portlet');
import('classes.util.LinkHelper');
import('classes.menu.MenuService');
import('classes.smarty.BigaceSmarty');
import('classes.smarty.SmartyTemplate');
import('classes.item.ItemRequest');
import('classes.item.SimpleItemTreeWalker');
import('classes.news.News');

/**
 * An portlet for the BIGACE Web CMS, reading news from news extension. 
 * Read the full tutorial at http://wiki.bigace.de/bigace:tutorial:portlet
 *
 * @author 
 */
 
class NewsPortlet extends Portlet
{
	/**
	* Constructor
	*/
	function NewsPortlet() {
        $this->setParameter( 'amount', '7' );
        $this->setParameter( 'link', true );
    }
 
    /** 
     * The title of this portlet.
     */
    function getTitle() {
        return "Latest News";
    }
 
    /** 
     * The HTML, which is returned by this portlet.
     */
    function getHtml() 
    {
        // could be filled with more parameters
        $params = array();

		$lang = (isset($params['language']) ? $params['language'] : ConfigurationReader::getConfigurationValue("news", "default.language"));
    	$id = ConfigurationReader::getConfigurationValue("news", "root.id");

        $ms = new MenuService();
        $linkMenu = $ms->getMenu($id,ITEM_LOAD_FULL,$lang);

		$order = (isset($params['order']) ? $params['order'] : 'DESC');
		$orderby = (isset($params['orderby']) ? $params['orderby'] : "date_2");
	
		$from = (isset($params['start']) ? $params['start'] : 0);
		$to = (isset($params['end']) ? $params['end'] : (isset($params['limit']) ? $params['limit'] : null));
	
		$ir = new ItemRequest(_BIGACE_ITEM_MENU);
		$ir->setID($id);
		$ir->setLanguageID($lang);
		$ir->setReturnType("News");
		$ir->setOrderBy($orderby);
		$ir->setOrder($order);
	
		if(isset($params['hidden']) && $params['hidden'] === true)
			$ir->setFlagToExclude($ir->FLAG_ALL_EXCEPT_TRASH);
	
		if($to != null && $to != 0)
			$ir->setLimit($from, $to);
	
		if(isset($params['category']) && $params['category'] != '') {
			if(strpos($params['category'], ",") === FALSE) {
				$ir->setCategory($params['category']); 		
			}
			else {
				$tmp = explode(",", $params['category']);
				foreach($tmp AS $x)	{
					$ir->setCategory($x);
				} 		
			}
		}

        // fetch menus
	    $menu_info = new SimpleItemTreeWalker($ir);
        
		$items = array();
		if(isset($params['counter']))
			$smarty->assign($params['counter'], $menu_info->count());

	    $html = '';		
		
		//Making limitation
		$amount = $menu_info->count();
		if ($menu_info->count() > $this->getParameter('amount')-1) $amount = $this->getParameter('amount');

        $doLink = (bool) $this->getParameter('link');
        
		$html = '<ul>';
		for ($i=0; $i < $amount; $i++) {
            $temp = $menu_info->next();
            if ($doLink === true) {
				$html .= '<br/><a href="'.LinkHelper::getUrlFromCMSLink(LinkHelper::getCMSLinkFromItem( $temp )).'"><li>'.$temp->getName() . '</a><br/>'.str_replace(chr(13), '<br/>', $temp->getTeaser());
			} else {
				$html .= '<br/><li><b>'.$temp->getName() . '</b><br/>'.str_replace(chr(13), '<br/>', $temp->getTeaser());
			}
		}
        return $html . '</ul><br/>';
    }
 
    /** 
     * Returns a proper type for each parameter.
     */
    function getParameterType($key) {
        switch($key) {
            case 'amount':
                return PORTLET_TYPE_INT_POSITIVE;
            case 'link':
                return PORTLET_TYPE_BOOLEAN;
            default:
                return PORTLET_TYPE_STRING;
        }
    }
 
    /** 
     * Returns a human readable name for each parameter.
     */
    function getParameterName($key) {
        switch($key) {
            case 'amount':
                return 'Show latest';
            case 'link':
                return 'Link to news [yes/no]';
        }
    }
}

?>
