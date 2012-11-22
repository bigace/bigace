<?php
/**
* Translation file for the Installer.
* Language: Swedish
* Copyright (C) DragonSlayer
*
* For further information go to {@link http://www.bigace.de http://www.bigace.de}.
*
* @version $Id$
* @@author DragonSlayer
*/

if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

$LANG['title']             = 'BIGACE '._BIGACE_ID;
$LANG['intro']             = '... easily manages your Content!';
$LANG['thanks']            = 'Tack för att du installerar BIGACE CMS!';

// -----------------------------------------------------------------------------
// Navigation
$LANG['menu_title']        = 'Installera System';
$LANG['menu_step_1']       = 'Licens (GPL)';
$LANG['menu_step_2']       = 'Kontrollera Inställningar';
$LANG['menu_step_3']       = 'Installation';
$LANG['menu_step_4']       = 'Skapa Community';
$LANG['menu_step'] = 'Steg';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Welcome Screen
$LANG['install_state']     = 'Status';
$LANG['install_begin']     = 'Starta Installationen';
$LANG['introduction']      = 'Introduktion';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Form Tooltip
$LANG['form_tip_close']		= 'Stäng';
$LANG['form_tip_hide']		= "Visa inte detta meddelande igen";
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// LANGUAGES - chooser and names for languages
$LANG['language_choose']   = 'Språkval';
$LANG['language_text']     = 'Välj det språk du vill använda under installationen.';
$LANG['language_button']   = 'Ändra språk';
// -----------------------------------------------------------------------------

$LANG['failure']           = 'Ett fel uppstod';
$LANG['new']               = 'Ny';
$LANG['old']               = 'Gammal';
$LANG['successfull']       = 'Lyckad';
$LANG['main_menu']         = 'Huvud Meny';
$LANG['back']              = 'Tillbaka';
$LANG['next']              = 'Nästa';
$LANG['state_no_db']       = 'Databasen verkar inte vara installerad!';
$LANG['state_not_all_db']  = 'Databas Installationen verkar inte komplett!';
$LANG['state_installed']   = 'Core-System installerades utan problem!';
$LANG['help_title']        = 'Hjälp';
$LANG['help_text']         = 'För att få mer information om varje steg, håll musen över hjälp ikonen invid varje textfält, så kommer ett kort informationsmeddelande upp. som demonstration håll muspekaren över följande ikon:';
$LANG['help_demo']         = 'Du hittade en hjälp-ruta!';
$LANG['db_install']        = 'Installera CMS';
$LANG['cid_install']       = 'Website Inställningar';
$LANG['install_finish']    = 'Komplett Installation';
$LANG['db_install_state']  = 'Status: ';
$LANG['db_install_help']   = 'Det här steget installerar CMS Core System. Du konfigurerar databasen och några inställningar för din första Community';

//-----------------------------------------------------------
// Translation for the System installation Dialog
$LANG['db_value_title']    = 'Databas koppling';
$LANG['ext_value_title']   = 'System Konfiguration';
$LANG['db_type']           = 'Databas Typ';
$LANG['db_host']           = 'Server/Host';
$LANG['db_database']       = 'Databas';
$LANG['db_user']           = 'Användarnamn';
$LANG['db_password']       = 'Lösenord';
$LANG['db_prefix']         = 'Tabell Prefix';
$LANG['mod_rewrite']       = 'Apache MOD-Rewrite';
$LANG['mod_rewrite_yes']   = 'Modul aktiv / Kan användas (.htaccess)';
$LANG['mod_rewrite_no']    = 'Inte möjligt / Vet inte';
$LANG['base_dir']          = 'Bas Katalog';
// Translation for the System Installation Help Images
$LANG['base_dir_help']     = 'Ange installationskatalogen relativt från din <B>Bas Katalog</B>. Lämna tomt för att installera i web rot-katalogen (http://www.example.com/). <br><b>För ifyllt värde borde vara rätt!</b><br>Sökvägen får inte börja med en slash men måste avslutas med slash. för att till exempel installera i &quot;http://www.example.com/cms/&quot;, så skall värdet <b>cms/</b> användas.';
$LANG['mod_rewrite_help']  = '<b>Den här inställningen ger dig &quot;Vänliga&quot; URLer!</b><br/>Se bara till att välja rätt inställning för ditt system. Om du väljer att använda rewrite utan att servern stödjer detta så kommer du inte att komma åt några sidor. Den här inställningen kan även göras via konfig-fil. Om du inte är säker på om servern har stödet påslaget bör du lämna inställningen som den är.!';
$LANG['db_password']       = 'Lösenord';
$LANG['def_language']      = 'Standard Språk';
$LANG['def_language_help'] = 'Välj det språk som är det vanligaste för dina användare.';
$LANG['db_type_help']      = 'Välj den databas typ du kommer att använda.<br>Installationen stöder alla visade databaser men CMS systemet stödjer ännu sålänge <b>ENDAST MySQL</b> fullt ut.<br>Om du bestämmer dig för en annan databas än MySQL, så får det ske på egen risk!';
$LANG['db_host_help']      = 'Ange servernamnet eller IP-adressen till din server med din databas, (<B>localhost</B> brukar fungera i de flesta fall!).';
$LANG['db_database_help']  = 'Ange namnet på din databas (detta är det du ser i vänstra spalten i phpMyAdmin).';
$LANG['db_user_help']      = 'Ange en användare som har skrivrätigheter till din databas';
$LANG['db_prefix_help']    = 'Ange ett prefix för BIGACE tabellerna. Använder du ett unikt namn är de lättare att urskilja.<BR> Om du inte förstår innebörden av detta kan du låta det vara med standardvärdet.';
$LANG['db_password_help']  = 'Ange lösenordet för ovanstående användare.';
$LANG['db_already_exists'] = 'Databasen finns redan, hoppar över detta steg.';

$LANG['htaccess_security']      = 'Apache .htaccess funktionen';
$LANG['htaccess_security_yes']  = 'Allow override aktiv (.htaccess)';
$LANG['htaccess_security_no']   = 'Inte möjligt / Vet inte';
$LANG['htaccess_security_help']	= '<b>Den här inställningen främjar säkerheten för ditt data!</b><br/>Försäkra dig om att din server stödjer  <b>Override All</b> via .htaccess Filer. (det går även att göra inställningen direkt i <B>httpd.conf</B>). Om du är osäker, lämna inställningen på standardvärdet!';
//-----------------------------------------------------------

// Translation for Consumer Installation
// First Dialog
$LANG['error_enter_domain']     = 'Ange domän-namnet för det nya Communityt.';
$LANG['error_enter_adminuser']  = 'Ange ett namn för det nya Administratörskontot (minst 4 tecken).';
$LANG['error_enter_adminpass']  = 'Ange lösenordet för det nya Administratörskontot (minst 6 tecken) och verifiera nedan.';

$LANG['cid_check_failed']   	= 'Testet för fil-rättigheter misslyckades! Rätta till innan du fortsätter!';
$LANG['cid_domain']         	= 'Community Domän';
$LANG['cid_id_help']        	= 'Välj ett ID för det nya Communityt (kommer bara att användas internt). Om du uppgraderar från en tidigare installation bör du använda samma ID som du hade tidigare.';
$LANG['cid_domain_help']    	= 'Ange domännamnet som skall mappas till Communityt. Det angivna värdet borde vara korrekt.<br><b>OBSERVERA: ANVÄND INTE EN SÖKVÄG ELLER EN AVLUTANDE SLASH!</b>';

$LANG['statistics']         	= 'Statistik';
$LANG['statistics_on']      	= 'Aktivera Statistik';
$LANG['statistics_off']     	= 'Inaktivera Statistik';
$LANG['statistics_help']    	= 'Välj om du vill aktivera Statistik eller inte. Om du aktiverar dem, kommer det skrivas i databasen varje gång en sida öppnas.';
$LANG['sitename']        		= 'Webplatsens namn';
$LANG['sitename_help']   		= 'Här anger du webplatsens namn eller titlen på din sida. Detta kan användas i Mallar och kan enkelt ändras i Administrations Konsollen.';
$LANG['mailserver']        		= 'E-post Server';
$LANG['mailserver_help']   		= 'Ange adressen till din E-postserver (smtp.example.com), som hanterar utgående e-post. Lämna tomt för att använda PHP standard proxy (för de flesta delade system).';
$LANG['webmastermail']     		= 'Epost adress';
$LANG['webmastermail_help']		= "Ange epost adressen till communityts administratörs konto.";
$LANG['bigace_admin']      		= 'Användarnamn';
$LANG['bigace_password']   		= 'Lösenord';
$LANG['bigace_check']      		= 'Lösenord [Verifiera]';
$LANG['bigace_admin_help'] 		= 'Ange användarnamnet för Administratörs kontot. Den här administratören kommer att äga alla Behörigheter och få Rättigheter på alla objekt och administrativa funktioner.';
$LANG['bigace_password_help']	= 'Ange Lösenordet till Administratörs kontot.';
$LANG['bigace_check_help'] 		= 'Verifiera det valda lösenordet. Om det inte matchar kommer du tillbaks hit.';
$LANG['create_files']      		= 'Skapar filsystem';
$LANG['save_cconfig']      		= 'Sparar Community konfiguration';
$LANG['added_consumer']    		= 'Community lades till';
$LANG['added_consumer']    		= 'Existerande Community lades till';
$LANG['community_exists']  		= 'Det finns redan en Community för den angivna domänen, Ange en annan domän.';

$LANG['check_reload']           = 'Kör igenom För-koll igen';
$LANG['check_up']               = 'För-koll';
$LANG['check_up_help']          = 'VIKTIGT: Kör igenom För-koll igen innan du installerar BIGACE eller lägger till ett nytt Community!';

// FIXME
$LANG['required_empty_dirs'] = 'Required directories';
// FIXME
$LANG['empty_dirs_description'] = 'The following directories are required by BIGACE, but could not be created automatically. Please create them manually:';
$LANG['check_yes']              = 'Ja';
$LANG['check_no']               = 'Nej';
$LANG['check_on']               = 'På';
$LANG['check_off']              = 'Av';
$LANG['check_status']           = 'Status';
$LANG['check_setting']          = 'Inställning';
$LANG['check_recommended']      = 'Rekommenderad';
$LANG['check_install_help']     = 'Om någon av markörerna är röda, så måste du korrigera din Apache och PHP konfiguration. Om du inte skulle göra det blir resultatet förmodligen en korrupt installation.';
$LANG['required_settings_title']= 'Krävda inställningar';
$LANG['check_settings_title']   = 'Rekommenderad Inställning';
$LANG['check_settings_help']    = 'Följande PHP inställningar är rekommenderade, för att BIGACE skall fungera smidigt. <br><br>Även om vissa av inställningarna inte matchar så kommer BIGACE att fungera. Vi rekommenderar ändå att korrigera nämnda problem, innan du fortsätter med installationen.';
$LANG['check_files_title']      = 'Katalog och Fil-rättigheter';
$LANG['check_files_help']       = 'BIGACE behöver skriv rättigheter på följande kataloger &amp; filer. Om du ser &quot;unwriteable&quot; (No), Behöver du korrigera rättigheterna innan du fortsätter.';

$LANG['config_consumer']        = 'Community Inställningar';
$LANG['config_admin']           = 'Administratörs konto';

$LANG['community_install_good'] = '
<p>Grattis, installationen är klar!</p>
<p>Om du någon gång skulle behöva support, eller om BIGACE inte skulle fungera som förväntat, kom ihåg att <a href="http://forum.bigace.de" target="_blank">hjälp finns tillgängligt</a> om du skulle behöva det.
<p>Ditt installations paket ligger kvar på servern, för säkerhetens skull bör du ta bort detta.</p>
<p>Du kan nu <a href="../../">se din nya website</a> och börja använda den. Börja med att logga in, så att du kommer in i Administrationen.</p>
<br />
<p>Lycka till!</p>
<br /><br />
<p><a href="../../">Besök din nya website</a></p>';

$LANG['community_install_bad'] 	= 'Problem uppstod under installationen.';
$LANG['community_install_infos']= 'Visa System meddelanden...';

$LANG['error_db_connect']       = 'FEL: Kunde inte ansluta till DataBas server';
$LANG['error_db_select']        = 'FEL: Kunde inte välja Databasen';
$LANG['error_db_create']        = 'FEL: Kunde inte skapa Databasen.';
$LANG['error_read_dir']         = 'FEL: Kunde inte läsa katalogen';
$LANG['error_created_dir']      = 'FEL: Kunde inte skapa katalogen';
$LANG['error_removed_dir']      = 'FEL: Kunde inte ta bort katalogen';
$LANG['error_copied_file']      = 'FEL: Kunde inte kopiera filen';
$LANG['error_remove_file']      = 'FEL: Kunde inte ta bort filen';
$LANG['error_close_file']       = 'FEL: Kunde inte stänga filen';
$LANG['error_open_file']        = 'FEL: Kunde inte öppna filen';
$LANG['error_db_statement']     = 'Error in DB Statement';
$LANG['error_open_cconfig']        = 'FEL: Kunde inte öppna Community konfigurations Fil';
$LANG['error_double_cconfig']      = 'FEL: Communityt finns redan!';
$LANG['could_not_find_consumer']   = 'FEL: Communityt kunde inte hittas';
