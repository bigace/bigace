<?php

/*
 * Smarty modifier
 * -------------------------------------------------------------
 * File:     modifier.news_date.php
 * Type:     function
 * Name:     news_date
 * Purpose:  Encodes the timestamp as RFC 2822 compatible date    
 * -------------------------------------------------------------
 */
function smarty_modifier_news_date($str)
{
    return date('r', $str);
}

