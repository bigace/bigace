<?php
/**
 * Include used for all Error Pages.
 *
 * Copyright (C) Kevin Papst.
 *
 * For further information go to http://www.bigace.de/
 *
 * @version $Id$
 * @author Kevin Papst 
 * @package bigace.error
 */

import('classes.template.TemplateService');

function loadErrorTemplate($name = null)
{
    if($name == null)
        $name = 'GenericError.tpl.html';

    $ts = new TemplateService();
    $ts->addTemplateDirectory(dirname(__FILE__));
    setBigaceTemplateValue('PUBLIC_DIR', _BIGACE_DIR_PUBLIC_WEB);
    setBigaceTemplateValue('IMAGE_DIR', _BIGACE_DIR_PUBLIC_WEB.'system/images/');
    setBigaceTemplateValue('ERROR_DIR', _BIGACE_DIR_PUBLIC_WEB.'system/error/');
    setBigaceTemplateValue('BIGACE_VERSION', _BIGACE_ID);

    $tpl = $ts->loadTemplatefile($name, true, true);

    return $tpl;
}


?>