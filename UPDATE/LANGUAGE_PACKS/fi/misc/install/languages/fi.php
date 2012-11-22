<?php
/**
* Translation file for the Installer.
* Language: finnish
* Copyright (C) Kevin Papst.
*
* For further information go to {@link http://www.bigace.de http://www.bigace.de}.
*
* @author Jenkky
*/

if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Ei voida n&auml;ytt&auml;&auml; erikseen, mene sivulle '.dirname(__FILE__).'/index.php');
}

$LANG['title']             = 'BIGACE '._BIGACE_ID;
$LANG['intro']             = '... ja sis&auml;ll&ouml;n hoitelet helposti!';
$LANG['thanks']            = 'Kiitos ett&auml; valitsit BIGACE CMS!';

// -----------------------------------------------------------------------------
// Navigation
$LANG['menu_title']        = 'Asenna J&auml;rjestelm&auml;';
$LANG['menu_step_2']       = 'Tarkista Asetukset';
$LANG['menu_step_3']       = 'Asennus';
$LANG['menu_step_4']       = 'Luo Yhteis&ouml;';
$LANG['menu_step_5'] 		= 'Asennus onnistui';
$LANG['menu_step'] = 'Vaihe';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Welcome Screen
$LANG['install_state']     = 'Tila';
$LANG['install_begin']     = 'Aloita Asennus';
$LANG['introduction']      = 'Esitys';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Form Tooltip
$LANG['form_tip_close']		= 'Sulje';
$LANG['form_tip_hide']		= "&auml;l&auml; n&auml;yt&auml; t&auml;t&auml; viesti&auml; uudelleen";
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// LANGUAGES - chooser and names for languages
$LANG['language_choose']   = 'Kielivalinnat';
$LANG['language_text']     = 'Valitse asennuksen aikana k&auml;ytett&auml;v&auml; kieli.';
$LANG['language_button']   = 'Vaihda kieli';
 // -----------------------------------------------------------------------------

 $LANG['failure']           = 'Virhe tapahtui';
 $LANG['new']               = 'Uusi';
 $LANG['old']               = 'Vanha';
 $LANG['successfull']       = 'Onnistui';
 $LANG['main_menu']         = 'P&auml;&auml;valikko';
 $LANG['back']              = 'Takaisin';
 $LANG['next']              = 'Seuraava';
 $LANG['state_no_db']       = 'Tietokanta ei vaikuta olevan asennettu!';
 $LANG['state_not_all_db']  = 'Tietokanta Asennus ei vaikuta olevan t&auml;ydellinen!';
 $LANG['state_installed']   = 'Core-J&auml;rjestelm&auml;n asennus onnistui!';
 $LANG['help_title']        = 'Ohje';
 $LANG['help_text']         = 'Saadaksesi lis&auml;tietoa jokaisesta vaiheesta, vie hiiriosoitinta jokaisen tekstikent&auml;n vieress&auml; olevan ohje-ikoonin yli, niin n&auml;et lyhyen opastusviestin. Seuraava ikoni toimii esimerkkin&auml;:';
 $LANG['help_demo']         = 'L&ouml;ysit opastusviestin!';
 $LANG['db_install']        = 'Asenna CMS';
 $LANG['cid_install']       = 'WebSivusto Asetukset';
 $LANG['install_finish']    = 'T&auml;ydellinen Asennus';
 $LANG['db_install_state']  = 'Tila: ';
 $LANG['db_install_help']   = 'T&auml;m&auml; vaihe asentaa CMS Core J&auml;rjestelm&auml;n. Seuraavaksi konfiguroit tietokannan ja ensimm&auml;isen yhteis&ouml;si muutaman asetuksen';

//-----------------------------------------------------------
// Translation for the System installation Dialog
$LANG['db_value_title']    = 'Tietokanta Kytkent&auml;';
$LANG['ext_value_title']   = 'J&auml;rjestelm&auml;n konfigurointi';
$LANG['db_type']           = 'Tietokanta Tyyppi';
$LANG['db_host']           = 'Palvelin/Host';
$LANG['db_database']       = 'Tietokanta nimi';
$LANG['db_user']           = 'K&auml;ytt&auml;j&auml;nimi';
$LANG['db_password']       = 'Salasana';
$LANG['db_prefix']         = 'Taulukko Prefix';
$LANG['mod_rewrite']       = 'Apache MOD-Rewrite';
$LANG['mod_rewrite_yes']   = 'Moduuli aktiivinen / Ei voida k&auml;ytt&auml;&auml; (.htaccess)';
$LANG['mod_rewrite_no']    = 'Ei mahdollinen / Ei tietoa';
$LANG['base_dir']          = 'Root kansio';
// Translation for the System Installation Help Images
$LANG['base_dir_help']     = 'Sy&ouml;t&auml; relatiivinen asennuskansio <B>root kansiosta</B>. J&auml;t&auml; tyhj&auml;ksi asentaaksesi BIGACE web root-kansiossa (http://www.example.com/). <br><b>Oletusarvoa ei useimmiten tarvitse vaihtaa!</b><br>Polku ei saa alkaa / merkill&auml;, mutta viimeinen merkki on oltava /. Esimerkiksi, &quot;http://www.example.com/cms/&quot; osoitteeseen asentaminen, k&auml;ytet&auml;&auml;n arvoa <b>cms/</b>.';
$LANG['mod_rewrite_help']  = '<b>T&auml;m&auml; asennus antaa sinun k&auml;ytt&auml;&auml; yksinkertaisia URL osoitteita!</b><br/>Tarkista ett&auml; valitset oikean asetuksen omalle j&auml;rjestelm&auml;llesi. Jos valitset k&auml;ytt&auml;&auml; rewrite mutta j&auml;rjestelm&auml;si ei tue toimintoa, et pysty k&auml;ytt&auml;m&auml;&auml;n sivuja. Pystyt my&ouml;hemmin vaihtamaan t&auml;m&auml;n asetuksen config-tiedostossa. Jos et tied&auml; varmasti ett&auml; j&auml;rjestelm&auml;si tukee toimintoa, suositellaan ett&auml; j&auml;t&auml;t asetuksen oletusarvona!';
$LANG['db_password']       = 'Salasana';
$LANG['def_language']      = 'Oletuskieli';
$LANG['def_language_help'] = 'Valitse k&auml;ytt&auml;jiesi oletuskieli.';
$LANG['db_type_help']      = 'Valitse k&auml;ytett&auml;v&auml;si tietokanta tyyppi&auml;.<br>Asennus tukee kaikkia n&auml;ytett&auml;vi&auml; tietokanta tyyppi&auml;, mutta t&auml;ll&auml; hetkell&auml; CMS j&auml;rjestelm&auml; tukee <b>ainoastaan MySQL</b> kokonaan.<br>Jos p&auml;&auml;t&auml;t k&auml;ytt&auml;&auml; toista tietokantaa kuin MySQL, on suuri riski ett&auml; CMS j&auml;rjestelm&auml;si ei toimi kunnolla!';
$LANG['db_host_help']      = 'Sy&ouml;t&auml; tietokantasi palvelinnimi tai IP-osoite, (<B>localhost</B> toimii useimmissa tapauksissa!).';
$LANG['db_database_help']  = 'Sy&ouml;t&auml; tietokantasi nimi (t&auml;m&auml;n n&auml;et vasemmassa sarakkeessa phpMyAdmin:ssa).';
$LANG['db_user_help']      = 'Sy&ouml;t&auml; tietokantasi kirjoitus-oikeudella omistavan k&auml;ytt&auml;j&auml;n nimi';
$LANG['db_prefix_help']    = 'Sy&ouml;t&auml; BIGACE taulukkojen prefix. Jos k&auml;yt&auml;t ainutlaatuista nime&auml; erotat helpommin k&auml;ytett&auml;v&auml;t taulukot toisistaan.<BR>Jos et ymm&auml;rr&auml; t&auml;t&auml; asetusta, voit k&auml;ytt&auml;&auml; oletusarvoa.';
$LANG['db_password_help']  = 'Sy&ouml;t&auml; yll&auml; olevan k&auml;ytt&auml;j&auml;n salasana.';
$LANG['db_already_exists'] = 'Tietokanta l&ouml;ytyy, j&auml;tet&auml;&auml;n t&auml;m&auml;n vaiheen v&auml;liin.';

$LANG['htaccess_security']      = 'Apache .htaccess toiminto';
$LANG['htaccess_security_yes']  = 'Allow override aktiivinen (.htaccess)';
$LANG['htaccess_security_no']   = 'Ei mahdollinen / Ei tietoa';
$LANG['htaccess_security_help']	= '<b>T&auml;m&auml; asetus edist&auml;&auml; tietosi turvallisuutta!</b><br/>Varmista ett&auml; palvelimesi tukee toimintoa <b>Override All</b> .htaccess tiedostojen kautta. (asetusta voi my&ouml;s p&auml;&auml;tt&auml;&auml; <B>httpd.conf</B>). Jos et ole varma, k&auml;yt&auml; oletusarvoa!';
//-----------------------------------------------------------

// Translation for Consumer Installation
// First Dialog
$LANG['error_enter_domain']     = 'Sy&ouml;t&auml; uuden Yhteis&ouml;n domain-nimi.';
$LANG['error_enter_adminuser']  = 'Sy&ouml;t&auml; uuden P&auml;&auml;k&auml;ytt&auml;j&auml;n nimi (v&auml;hint&auml;&auml;n 4 merkki&auml;).';
$LANG['error_enter_adminpass']  = 'Sy&ouml;t&auml; p&auml;&auml;k&auml;ytt&auml;j&auml;n Salasana (v&auml;hint&auml;&auml;n 6 merkki&auml;) ja vahvista alla.';

$LANG['cid_check_failed']   	= 'Tiedostojen oikeudet eiv&auml;t ole riitt&auml;v&auml;t! Korjaa oikeudet ennen kuin jatkat!';
$LANG['cid_domain']         	= 'Yhteis&ouml; Domain';
$LANG['cid_id_help']        	= 'Valitse uuden Yhteis&ouml;n ID (k&auml;ytet&auml;&auml;n vain sis&auml;isesti). Jos p&auml;ivit&auml;t uuteen versioon, sinun tulee k&auml;ytt&auml;&auml; samaa ID:t&auml;.';
$LANG['cid_domain_help']    	= 'Sy&ouml;t&auml; Yhteis&ouml;&auml; kuvastava domain-nimi. Oletusarvo on useimmissa tapauksissa oikea.<br><b>HUOM: &auml;L&auml; K&auml;YT&auml; POLKUA TAI / VIIMEISEN&auml; MERKKIN&auml;!</b>';

$LANG['statistics']         	= 'Tilastot';
$LANG['statistics_on']      	= 'Aktivoi tilasto';
$LANG['statistics_off']     	= 'Ota tilasto k&auml;yt&ouml;st&auml;';
$LANG['statistics_help']    	= 'Valitse haluatko k&auml;ytt&auml;&auml; tilastoa vai ei. Jos aktivoit toimintoa, jokainen sivulataus tallennetaan tietokantaan.';
$LANG['sitename']        		= 'Web-sivuston nimi';
$LANG['sitename_help']   		= 'T&auml;&auml;ll&auml; asetat web-sivuston nimi tai sivun titteli. T&auml;m&auml;n voit asettaa Malleissa ja pystyt helposti vaihtamaan nime&auml; HallintoPaneelissa.';
$LANG['mailserver']        		= 'S&auml;hk&ouml;posti-palvelin';
$LANG['mailserver_help']   		= 'Sy&ouml;t&auml; s&auml;hk&ouml;posti-palvelimesi osoite (esim. smtp.example.com), jota k&auml;ytet&auml;&auml;n s&auml;hk&ouml;posti-l&auml;hetyksess&auml;. Jos haluat k&auml;ytt&auml;&auml; PHP oletus proxya (jaetuissa j&auml;rjestelmiss&auml;), j&auml;t&auml; kentt&auml; tyhj&auml;ksi.';
$LANG['webmastermail']     		= 'S&auml;hk&ouml;posti-osoite';
$LANG['webmastermail_help']		= "Sy&ouml;t&auml; yhteis&ouml;n p&auml;&auml;k&auml;ytt&auml;j&auml;n s&auml;hk&ouml;posti-osoite.";
$LANG['bigace_admin']      		= 'K&auml;ytt&auml;j&auml;nimi';
$LANG['bigace_password']   		= 'Salasana';
$LANG['bigace_check']      		= 'Salasana [Vahvista]';
$LANG['bigace_admin_help'] 		= 'Sy&ouml;t&auml; p&auml;&auml;k&auml;ytt&auml;j&auml;n k&auml;ytt&auml;j&auml;nimi. T&auml;lle k&auml;ytt&auml;j&auml;lle asetetaan t&auml;ydet oikeudet ja h&auml;n pystyy k&auml;ytt&auml;m&auml;&auml;n kaikkia toiminnollisia asetuksia.';
$LANG['bigace_password_help']	= 'Sy&ouml;t&auml; p&auml;&auml;k&auml;ytt&auml;j&auml;n salasana.';
$LANG['bigace_check_help'] 		= 'Vahvista salasana. Jos salasanat eiv&auml;t t&auml;sm&auml;&auml;, t&auml;m&auml; sivu ilmestyy uudestaan.';
$LANG['create_files']      		= 'Tiedostoj&auml;rjestelm&auml; luodaan';
$LANG['save_cconfig']      		= 'Yhteis&ouml; asetukset tallennetaan';
$LANG['added_consumer']    		= 'Yhteis&ouml; lis&auml;tty';
$LANG['added_consumer']    		= 'Olemassa oleva Yhteis&ouml; lis&auml;tty';
$LANG['community_exists']  		= 'Valitussa domainissa on jo olemassa Yhteis&ouml;, valitse toinen domain.';

$LANG['check_reload']           = 'K&auml;ynnist&auml; esitarkastus uudelleen';
$LANG['check_up']               = 'Esitarkastus';
$LANG['check_up_help']          = 'T&auml;RKE&auml;&auml;: K&auml;ynnist&auml; esitarkasts uudelleen ennenkuin asennat BIGACE:n tai lis&auml;&auml;t Yhteis&ouml;n!';

$LANG['check_yes']              = 'Kyll&auml;';
$LANG['check_no']               = 'Ei';
$LANG['check_on']               = 'On';
$LANG['check_off']              = 'Ei ole';
$LANG['check_status']           = 'Tila';
$LANG['check_setting']          = 'Asetus';
$LANG['check_recommended']      = 'Suositus';
$LANG['check_install_help']     = 'Jos joku osoittimista on punainen, sinun on ensin korjattava Apache tai PHP asetus. Jos et korjaa asetusta, asennuksen tulos on luultavasti v&auml;&auml;r&auml;llinen.';
$LANG['required_settings_title']= 'Pakolliset asetukset';
$LANG['check_settings_title']   = 'Suositeltavat asetukset';
$LANG['check_settings_help']    = 'Seuraavat PHP asetukset suositellaan, jotta BIGACE toimisi parhaiten. <br><br>Vaikka jotkut asetukset eiv&auml;t ole oikein asetettu, BIGACE toimii virheett&ouml;m&auml;sti. Suositellaan kuitenkin n&auml;iden asetusten korjaamista, ennenkuin jatkat asennusta.';
$LANG['check_files_title']      = 'Kansio- ja Tiedosto-oikeudet';
$LANG['check_files_help']       = 'BIGACE tarvitsee kirjoitus-oikeuden seuraaville kansioille ja tiedostoille. Jos n&auml;et &quot;unwriteable&quot; (No), sinun on korjattava oikeudet ennenkuin jatkat asennusta.';

$LANG['config_consumer']        = 'Yhteis&ouml; Asetukset';
$LANG['config_admin']           = 'P&auml;&auml;k&auml;ytt&auml;j&auml;tili';

$LANG['community_install_good'] = '
<p>Onneksi olkoon, asennus on valmis!</p>
<p>Jos tarvitset ohjeistusta, tai jos BIGACE ei toimi odotusten mukaan, muista ett&auml; <a href="http://forum.bigace.de" target="_blank">apua on saatavilla</a>.
<p>Asennuspakettisi sijaitsee viel&auml; palvelimella, turvallisuussyist&auml; t&auml;m&auml; on poistettava.</p>
<p>Voit nyt <a href="../../">katsella uutta sivustoasi</a> ja aloittaa k&auml;ytt&auml;m&auml;&auml;n sit&auml;. Aloita kirjautumalla sis&auml;&auml;n, niin p&auml;&auml;set HallintoPaneeliiin.</p>
<br />
<p>Onnea matkaan!</p>
<br /><br />
<p><a href="../../">K&auml;y web-sivustollasi</a></p>';

$LANG['community_install_bad'] 	= 'Asennuksessa tapahtui virhe.';
$LANG['community_install_infos']= 'N&auml;yt&auml; J&auml;rjestelm&auml; viestej&auml;...';

$LANG['error_db_connect']       = 'VIRHE: Tietokantapalvelimeeseen ei voitu kytke&auml;';
$LANG['error_db_select']        = 'VIRHE: Tietokantaa ei voitu valita';
$LANG['error_db_create']        = 'VIRHE: Tietokantaa ei voitu luoda';
$LANG['error_read_dir']         = 'VIRHE: Kansiota ei voitu lukea';
$LANG['error_created_dir']      = 'VIRHE: Kansiota ei voitu luoda';
$LANG['error_removed_dir']      = 'VIRHE: Kansiota ei voitu poistaa';
$LANG['error_copied_file']      = 'VIRHE: Tiedostoa ei voitu kopioida';
$LANG['error_remove_file']      = 'VIRHE: Tiedostoa ei voitu poistaa';
$LANG['error_close_file']       = 'VIRHE: Tiedostoa ei voitu sulkea';
$LANG['error_open_file']        = 'VIRHE: Tiedostoa ei voitu avata';
$LANG['error_db_statement']     = 'Error in DB Statement';
$LANG['error_open_cconfig']        = 'VIRHE: Yhteis&ouml; konfiguraatio-tiedostoa ei voitu avata';
$LANG['error_double_cconfig']      = 'VIRHE: Yhteis&ouml; on jo olemassa!';
$LANG['could_not_find_consumer']   = 'VIRHE: Yhteis&ouml; ei l&ouml;ytynyt';
