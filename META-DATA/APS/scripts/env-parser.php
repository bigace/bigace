<?php

function get_psa_modify_hash($params)
{
    $scheme = fetch_env_var("BASE_URL_SCHEME");
    $host   = fetch_env_var("BASE_URL_HOST");
    $port   = fetch_env_var("BASE_URL_PORT");
    $path   = fetch_env_var("BASE_URL_PATH");

    $full   = $scheme . "://" . $host . (($port !== NULL) && ($port != 80) ? ":$port" : "") . ($path[0] == "/" ? "" : "/") . $path;

    $parameters = array();

    // -------------------------------------------------------------------------
    // not used currently
    $parameters[START_DELIM."BASE_URL_SCHEME".END_DELIM] = $scheme;
    if($scheme == 'http'){
        $parameters[START_DELIM."SSL_ENABLED".END_DELIM] = 0;
        $parameters[START_DELIM."SSL_ENABLED_YN".END_DELIM] = 'n';	
    }
    else if($scheme == 'https'){
        $parameters[START_DELIM."SSL_ENABLED".END_DELIM] = 1;
        $parameters[START_DELIM."SSL_ENABLED_YN".END_DELIM] = 'y';
    }
    $parameters[START_DELIM."BASE_URL_HOST".END_DELIM] = $host;
    $parameters[START_DELIM."BASE_URL_PORT".END_DELIM] = $port;
    // -------------------------------------------------------------------------
    
    $my_url_path = $path;
    $my_urlwls_path = $path;
    if($my_url_path == "/"){
        $my_url_path = ""; // ".";
     $my_urlwls_path = $my_url_path;
    }
    else if($my_url_path[strlen($my_url_path)-1] == "/"){
        // keep trailing slash - do not remove it, its required!
        // $my_url_path = substr($my_url_path, 0, strlen($my_url_path)-1);
        $my_urlwls_path = "/".$my_url_path;
    }
    $parameters[START_DELIM."BASE_DIR".END_DELIM] = $my_url_path;
    $parameters[START_DELIM."MOD_REWRITE".END_DELIM] = fetch_env_var_fb("MOD_REWRITE","false");

    // -------------------------------------------------------------------------
    // not used currently
    $parameters[START_DELIM."INSTALL_PREFIX_WLS".END_DELIM] = $my_urlwls_path;

    $my_root_url = $full;
    if($my_root_url[strlen($my_root_url)-1] == "/"){
	$my_root_url = substr($my_root_url, 0, strlen($my_root_url)-1);
    }
    $parameters[START_DELIM."ROOT_URL".END_DELIM] = $my_root_url;

    $my_web_dir = fetch_env_var("WEB___DIR");
    while($my_web_dir[strlen($my_web_dir)-1] == "/"){
	    $my_web_dir = substr($my_web_dir, 0, strlen($my_web_dir)-1);
    }
    $parameters[START_DELIM."ROOT_DIR".END_DELIM] = $my_web_dir;
    // -------------------------------------------------------------------------
    
    return $parameters;
}

function get_db_type($db_id)
{
    return fetch_env_var("DB_${db_id}_TYPE");
}

function get_db_name($db_id)
{
    return fetch_env_var("DB_${db_id}_NAME");
}

function get_db_login($db_id)
{
    return fetch_env_var("DB_${db_id}_LOGIN");
}

function get_db_password($db_id)
{
    return fetch_env_var("DB_${db_id}_PASSWORD");
}

function get_db_prefix($db_id)
{
    if(fetch_env_var("DB_${db_id}_PREFIX") !== False){
        return fetch_env_var("DB_${db_id}_PREFIX");
    } else{
        return '';
    }
}

function get_db_address($db_id)
{
    $db_address = fetch_env_var("DB_${db_id}_HOST");
    if(fetch_env_var("DB_${db_id}_PORT") !== False)
        $db_address .= ':' . fetch_env_var("DB_${db_id}_PORT");

    return $db_address;
}

function get_db_modify_hash($db_ids)
{
    $parameters = array();
    foreach($db_ids as $db_id) 
    {
        $parameters[START_DELIM."CID_DB_TYPE".END_DELIM] = get_db_type($db_id);
	    $parameters[START_DELIM."CID_DB_NAME".END_DELIM] = get_db_name($db_id);
	    $parameters[START_DELIM."CID_DB_USER".END_DELIM] = get_db_login($db_id);
	    $parameters[START_DELIM."CID_DB_PASS".END_DELIM] = get_db_password($db_id);
	    $parameters[START_DELIM."CID_DB_HOST".END_DELIM] = fetch_env_var("DB_${db_id}_HOST");
	    $parameters[START_DELIM."CID_DB_PREFIX".END_DELIM] = get_db_prefix($db_id);

        // -------------------------------------------------------------------------
        // not used currently
	    $parameters[START_DELIM."DB_".strtoupper($db_id)."_VERSION".END_DELIM] = fetch_env_var("DB_${db_id}_VERSION");
	    $parameters[START_DELIM."DB_".strtoupper($db_id)."_PORT".END_DELIM] = fetch_env_var("DB_${db_id}_PORT");
	    $parameters[START_DELIM."DB_".strtoupper($db_id)."_ADDRESS".END_DELIM] = get_db_address($db_id);
        // -------------------------------------------------------------------------
    }

    return $parameters;
}

function get_web_dir($web_id)
{
    $web_id_parameter = str_replace("/", "_", $web_id);
    return fetch_env_var("WEB_${web_id_parameter}_DIR");
}

function get_web_modify_hash($web_ids)
{
    $parameters = array();
    foreach($web_ids as $web_id) {
        $web_id_parameter = str_replace("/", "_", $web_id);
        $parameters[START_DELIM.strtoupper($web_id)."_DIR".END_DELIM] = fetch_env_var("WEB_${web_id_parameter}_DIR");
    }

    return $parameters;
}

function get_settings_modify_hash($params)
{
    $parameters = array();
    foreach($params as $param) {
        $parameters[START_DELIM.strtoupper($param).END_DELIM] = fetch_env_var("SETTINGS_${param}");
    }

    // BIGACE defaults
    $parameters[START_DELIM."CID_EMAIL_SERVER".END_DELIM] = "";
    $parameters[START_DELIM."WRITE_STATISTICS".END_DELIM] = "0";
    $parameters[START_DELIM."DEFAULT_STYLE".END_DELIM] = "standard";
    $parameters[START_DELIM."DEFAULT_EDITOR".END_DELIM] = "fckeditor";

    $parameters[START_DELIM."SITE_NAME".END_DELIM] = (isset($parameters[START_DELIM."TITLE".END_DELIM]) ? $parameters[START_DELIM."TITLE".END_DELIM] : "BIGACE Web CMS");
    $parameters[START_DELIM."CID_WEBMASTER_EMAIL".END_DELIM] = (isset($parameters[START_DELIM."ADMIN_EMAIL".END_DELIM]) ? $parameters[START_DELIM."ADMIN_EMAIL".END_DELIM] : "test@example.com");
    $parameters[START_DELIM."CID_ADMIN".END_DELIM] = (isset($parameters[START_DELIM."ADMIN_NAME".END_DELIM]) ? $parameters[START_DELIM."ADMIN_NAME".END_DELIM] : "admin");
    return $parameters;
}

function get_settings_enum_modify_hash($enum_params)
{
    $parameters = array();
    foreach($enum_params as $param_id => $elements_ids_map) {
        $param_value = fetch_env_var("SETTINGS_${param_id}");
    	foreach($elements_ids_map as $element_id => $value_for_app){
	        if($element_id == $param_value){
                    $parameters[START_DELIM.strtoupper($param_id).END_DELIM] = $value_for_app;
	        }
	    }
    }
    // BIGACE defaults
    $locale = "en";
    if(isset($parameters[START_DELIM."LOCALE".END_DELIM])) {
        $tmp = $parameters[START_DELIM."LOCALE".END_DELIM];
        $locale = substr($tmp,0,2);
    }
    $parameters[START_DELIM."DEFAULT_LANGUAGE".END_DELIM] = $locale;

    return $parameters;
}

function get_crypt_settings_modify_hash($crypt_params)
{
    $parameters = array();
    foreach($crypt_params as $param) {
        $fname = "${param}_crypt";
        $parameters[START_DELIM.strtoupper($param).END_DELIM] = $fname(fetch_env_var("SETTINGS_${param}"));
    }
    
    $parameters[START_DELIM."CID_PW".END_DELIM] = (isset($parameters[START_DELIM."ADMIN_PASSWORD".END_DELIM]) ? $parameters[START_DELIM."ADMIN_PASSWORD".END_DELIM] : "");

    return $parameters;
}

function get_additional_modify_hash()
{
    $parameters = array();
    return $parameters;
}

function fetch_env_var($envvar)
{
    $res = getenv($envvar);
    if ($res === false)
        return NULL;
    return $res;
}

function fetch_env_var_fb($envvar, $fallback)
{
    $res = getenv($envvar);
    if ($res === false)
        return $fallback;
    return $res;
}

?>