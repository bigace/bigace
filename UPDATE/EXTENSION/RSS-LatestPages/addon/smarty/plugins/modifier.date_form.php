<?php

/*
 * Smarty modifier
 * -------------------------------------------------------------
 * File:     modifier.date_form.php
 * Purpose:  Encodes the timestamp as RFC 2822 compatible date
 *           or by using the date function.
 * $Id: modifier.date_form.php 10 2010-12-14 12:19:30Z kevin $    
 * -------------------------------------------------------------
 */
function smarty_modifier_date_form($str, $format = 'r')
{
    return date($format, $str);
}

