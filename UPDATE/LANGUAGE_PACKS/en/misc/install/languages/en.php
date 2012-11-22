<?php
/**
* Translation file for the installer.
* Language: English
* Copyright (C) Kevin Papst
*
* For further information go to {@link http://www.bigace.de http://www.bigace.de}.
*
* @version $Id$
* @author Kevin Papst
*/

if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

$LANG['title'] = 'BIGACE '._BIGACE_ID;
$LANG['intro'] = '... easily manages your Content!';
$LANG['thanks'] = 'Thanks for installing the BIGACE CMS!';

// -----------------------------------------------------------------------------
// Navigation
$LANG['menu_title'] = 'Install System';
$LANG['menu_step_2'] = 'Check Settings';
$LANG['menu_step_3'] = 'Installation';
$LANG['menu_step_4'] = 'Create Community';
$LANG['menu_step_5'] = 'Installation was successful';
$LANG['menu_step'] = 'Step';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Welcome Screen
$LANG['install_state'] = 'Status';
$LANG['install_begin'] = 'Start Installation';
$LANG['introduction'] = 'Introduction';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Form Tooltip
$LANG['form_tip_close'] = 'Close';
$LANG['form_tip_hide'] = "Don't show this message again";
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// LANGUAGES - chooser and names for languages
$LANG['language_choose'] = 'Language Definition';
$LANG['language_text'] = 'Choose the language that is used during the Installation Process.';
$LANG['language_button'] = 'Change language';

$LANG['language_de'] = 'Deutsch (German)';
$LANG['language_en'] = 'English';
$LANG['language_it'] = 'Italiano (Italian)';
$LANG['language_se'] = 'Svenska (Swedish)';
$LANG['language_fi'] = 'Suomeksi (Finnish)';
$LANG['language_ro'] = 'Românã (Romanian)';
$LANG['language_tr'] = 'Türkçe (Turkish)';
$LANG['language_hr'] = 'Hrvatski (Croatian)';
$LANG['language_ru'] = 'Русский (Russian)';
$LANG['language_pt'] = 'Português (Portuguese)';
$LANG['language_nl'] = 'Nederlandse (Dutch)';
$LANG['language_es'] = 'Español (Spanish)';
$LANG['language_si'] = 'Slovensko (Slovene)';
// -----------------------------------------------------------------------------

$LANG['failure'] = 'Errors occured';
$LANG['new'] = 'New';
$LANG['old'] = 'Old';
$LANG['successfull'] = 'Successful';
$LANG['main_menu'] = 'Main Menu';
$LANG['back'] = 'Back';
$LANG['next'] = 'Next';
$LANG['state_no_db'] = 'The Database seems not to be installed!';
$LANG['state_not_all_db'] = 'The Database Installation seems to be imncomplete!';
$LANG['state_installed'] = 'Core-System successful installed!';
$LANG['help_title'] = 'Help';
$LANG['help_text'] = 'To see further information for each Step, move your Mouse over the Help icon behind each input field. A short information message will appear.<br>For demonstration purpose move your Mouse above the following icon:';
$LANG['help_demo'] = 'You found the right way to see your Help-Infos!';
$LANG['db_install'] = 'Install CMS';
$LANG['cid_install'] = 'Website Settings';
$LANG['install_finish'] = 'Complete Installation';
$LANG['db_install_state'] = 'State: ';
$LANG['db_install_help'] = 'This Step install the CMS Core System. You configure the database connection and some settings for you first Website (Community)';

//-----------------------------------------------------------
// Translation for the System installation Dialog
$LANG['db_value_title'] = 'Database Connection';
$LANG['ext_value_title'] = 'System Configuration';
$LANG['db_type'] = 'Database Type';
$LANG['db_host'] = 'Server/Host';
$LANG['db_database'] = 'Database';
$LANG['db_user'] = 'User';
$LANG['db_password'] = 'Password';
$LANG['db_prefix'] = 'Table Prefix';
$LANG['mod_rewrite'] = 'Apache MOD-Rewrite';
$LANG['mod_rewrite_yes'] = 'Modul active / Usage possible (.htaccess)';
$LANG['mod_rewrite_no'] = 'Not possible / Do not know';
$LANG['base_dir'] = 'Base Directory';
// Translation for the System Installation Help Images
$LANG['base_dir_help'] = 'Enter the installation directory relative from you BaseDir. Leave empty if installed in web root (http://www.example.com/). <br><b>Auto calculated value should be correct!</b><br>The path must NOT begin, but end with a Slash. For the example installation in &quot;http://www.example.com/cms/&quot;, the value <b>cms/</b> would be correct.';
$LANG['mod_rewrite_help'] = '<b>This setting allows friendly URLs!</b><br/>Please make sure to choose the right Setting. If you choose usage possible without Rewrite Support, your System may not be browsable. This setting is configurable via an Config Entry. If you are not sure leave this setting as is!';
$LANG['db_password'] = 'Password';
$LANG['def_language'] = 'Default Language';
$LANG['def_language_help'] = 'Choose the default language for your CMS.';
$LANG['db_type_help'] = 'Choose the Database Type you are going to use.<br>The Installation supports all shown Databases, but the Core System <b>currently ONLY supports MySQL</b> completely.<br>If you decide to use a different Database than MySQL, its at your own risk!';
$LANG['db_host_help'] = 'Enter the Server name where your database is installed (try to use <b>localhost</b> which often works!).';
$LANG['db_database_help'] = 'Enter the name of your database (for example the same you see in the left frame of phpMyAdmin).';
$LANG['db_user_help'] = 'Enter the user that has write permission to your database.';
$LANG['db_prefix_help'] = 'Enter the prefix for the BIGACE database tables. Using a unique name, they will always be directly identifiable. If you do not understand the meaning of this, use the default value.';
$LANG['db_password_help'] = 'Enter the password for the above entered user.';
$LANG['db_already_exists'] = 'The database seems to be correctly installed, skipping this installation step.';

$LANG['htaccess_security'] = 'Apache .htaccess Feature';
$LANG['htaccess_security_yes'] = 'Allow override active (.htaccess)';
$LANG['htaccess_security_no'] = 'Not possible / Do not know';
$LANG['htaccess_security_help']	= '<b>This is a setting for the security of your data!</b><br/>Be sure that your Server allows <b>Override All</b> by .htaccess Files. If you are not sure leave this setting as is!';
//-----------------------------------------------------------

// Translation for Consumer Installation
// First Dialog
$LANG['error_enter_domain'] = 'Please enter a correct Domain, where the new Community will be available.';
$LANG['error_enter_adminuser'] = 'Please enter a name for the new Administrator account (at least 4 character).';
$LANG['error_enter_adminpass'] = 'Please enter a password for the new Administrator account (at least 6 character) and verify it below.';

$LANG['cid_check_failed'] = 'Checkup of your file permissions FAILED! You have to correct them BEFORE you can continue!';
$LANG['cid_domain'] = 'Community Domain';
$LANG['cid_id_help'] = 'Choose the ID for the new Community (which will be used only internal). If you are going to migrate an existing Installation, the best idea is to choose the ID of your old Community.';
$LANG['cid_domain_help'] = 'Enter the Domain Name, which will be mapped to the new Community. The auto-detected value should be correct.<br><b>NOTE: DO NOT ENTER A PATH OR A TRAINLING SLASH!</b>';

$LANG['statistics'] = 'Statistics';
$LANG['statistics_on'] = 'Activate statistics';
$LANG['statistics_off'] = 'Deactivate Statistics';
$LANG['statistics_help'] = 'Choose whether you want to activate statistics or not. If you activate them, with each Page call a writing Database call will be executed.';
$LANG['sitename'] = 'Website name';
$LANG['sitename_help'] = 'Enter the name or title of your page. This value can be used in Templates and easily be changed using the Administration.';
$LANG['mailserver'] = 'Mail Server';
$LANG['mailserver_help'] = 'Enter your Mail Server (mail.yourdomain.com), which will send your emails. Leave empty if using the PHP default proxy (for most shared systems).';
$LANG['webmastermail'] = 'Email adress';
$LANG['webmastermail_help'] = "Enter the email adress for the administrator account of your new community.";
$LANG['bigace_admin'] = 'Username';
$LANG['bigace_password'] = 'Password';
$LANG['bigace_check'] = 'Password [re-enter]';
$LANG['bigace_admin_help'] = 'Enter the username for the administrator account. This administrator will own all permissions on Items and administrative functions.';
$LANG['bigace_password_help'] = 'Enter the password for your administrator account.';
$LANG['bigace_check_help'] = 'Please verify your choosen password. If the entered passwords do not match, you will come back here.';
$LANG['create_files'] = 'Creating Filesystem';
$LANG['save_cconfig'] = 'Save Community Configuration';
$LANG['added_consumer'] = 'Added Community';
$LANG['added_consumer'] = 'Added exisiting Community';
$LANG['community_exists'] = 'A Consumer is already existing for the given Domain, please enter a different Domain.';

$LANG['check_reload'] = 'Execute Pre-Check again';
$LANG['check_up']               = 'Pre-Check';
$LANG['check_up_help'] = 'ATTENTION: Please perform the Pre-Check before you begin to install BIGACE or add a new Community!';

$LANG['required_empty_dirs'] = 'Required directories';
$LANG['empty_dirs_description'] = 'The following directories are required by BIGACE, but could not be created automatically. Please create them manually:';
$LANG['check_yes'] = 'Yes';
$LANG['check_no'] = 'No';
$LANG['check_on'] = 'On';
$LANG['check_off'] = 'Off';
$LANG['check_status'] = 'State';
$LANG['check_setting'] = 'but current State is';
$LANG['check_recommended'] = 'Recommended setting is:';
$LANG['check_install_help'] = 'If one of the flags is marked red, you have to adjust/correct your Apache and PHP Configuration. If you do not so, it will probably end in a corrupt Installation.';
$LANG['required_settings_title']= 'Required Settings';
$LANG['check_settings_title'] = 'Recommended Settings';
$LANG['check_settings_help'] = 'Following PHP Settings are recommended, to offer a smooth work of BIGAC. <br><br>The CMS should even work if some of the settings do not match. Nevertheless we recommend, to fix any mentioned problem, before proceeding with the installation.';
$LANG['check_files_title'] = 'Directory- and File Permission';
$LANG['check_files_help'] = 'For a proper work, BIGACE needs write permissions for the following directorys &amp; files. If you see a red dot, you have to fix the permission before continuing.';

$LANG['config_consumer'] = 'Community Settings';
$LANG['config_admin'] = 'Administrator Account';

$LANG['community_install_good'] = '
<p>Congratulations, the installation process is complete!</p>
<p>If at any time you need support, or BIGACE fails to work properly, please remember that <a href="http://forum.bigace.de" target="_blank">help is available</a> if you need it.
<p>Your installation directory is still existing. It\'s a good idea to remove this completely for security reasons.</p>
<p>Now you can <a href="../../">see your newly installed website</a> and begin to use it. You should first make sure you are logged in, after which you will be able to access the administration center.</p>
<br />
<p>Good luck!</p>
<br /><br />
<p><a href="../../">Visit your new website</a></p>';

$LANG['community_install_bad'] 	= 'Problems occured during installation.';
$LANG['community_install_infos']= 'Display System messages...';

$LANG['error_db_connect'] = 'Could not connect to Database Host';
$LANG['error_db_select'] = 'Could not select Database';
$LANG['error_db_create'] = 'Could not create Database.';
$LANG['error_read_dir'] = 'Could not read Directory';
$LANG['error_created_dir'] = 'Could not create Directory';
$LANG['error_removed_dir'] = 'Could not delete Directory';
$LANG['error_copied_file'] = 'Could not copy File';
$LANG['error_remove_file'] = 'Could not delete File';
$LANG['error_close_file'] = 'Could not close File';
$LANG['error_open_file'] = 'Error: Could not open File';
$LANG['error_db_statement'] = 'Error in DB Statement';
$LANG['error_open_cconfig'] = 'Could not open Community Configuration File';
$LANG['error_double_cconfig'] = 'Error: Community already exists!';
$LANG['could_not_find_consumer'] = 'Error: Could not find Community';
