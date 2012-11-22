<?php
// +------------------------------------------------------------------------+
// | BIGACE - a PHP based Web CMS for MySQL                                 |
// +------------------------------------------------------------------------+
// | Copyright (c) Kevin Papst                                              |
// | Web           http://www.bigace.de                                     |
// | Sourceforge   http://sourceforge.net/projects/bigace/                  |
// +------------------------------------------------------------------------+
// | This source file is subject to version 2 or (at your option) any later |
// | version, of the GNU General Public License as published by the Free    |
// | Software Foundation, available at:                                     |
// | http://www.gnu.org/licenses/gpl.html                                   |
// +------------------------------------------------------------------------+
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// +------------------------------------------------------------------------+

/*
 * This file extracts the BIGACE sources and redirects to the main installer afterwards.
 * If something goes wrong (like missing file permissions), detailed error messges will be shown.
 *
 * The Pre-Installer is currently translated to the languages:
 *
 * - German
 * - English
 * - Spanish
 * - Portuguese
 * - Swedish
 * - Finnish
 * - Russian
 *
 * @version $Id$
 * @author Kevin Papst
 */

// #################################################################################
// If you have problems with permissions or dedicated wishes for your file settings,
// uncomment the next lines and set to your favour.

// directory permissions that apply to all created directories
define('DIRECTORY_PERMISSION', 0755); // 0645
// permissions for all created files, should work in most cases out-of-the-box
// define('FILE_PERMISSION', 0644);
// the user mask settings, don't change if you are not sure what this is!
// define('ACCESS_UMASK', 0022);
// #################################################################################


// to run BIGACE php 5 is at least needed
define('PHPVERSION_NEEDED', '5.1');
// we need at least 12-16MB free memory to extract all files from the ZIP Archive!
define('MEMORY_NEEDED', 16);

// whether we have enough memory to run
$memOK = true;

// first check the memory settings and try to fix them
$freeMem = ini_get('memory_limit');
if(!is_null($freeMem) && strlen($freeMem) > 1)
{
    $memInt = (int)substr($freeMem,0,strlen($freeMem)-1);
    if ( $memInt < MEMORY_NEEDED ) {
        @ini_set('memory_limit', MEMORY_NEEDED.'M');

        //now recheck if the new setting could be applied!
        $freeMem = ini_get('memory_limit');
        $memInt = (int)substr($freeMem,0,strlen($freeMem)-1);
        if ( $memInt < MEMORY_NEEDED ) {
            // it didn't, so mark the error flag
            $memOK = false;
        }
    }
}

// try to increase execution time, extracting the archive needs some time...
@ini_set('max_execution_time', 120);


 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


	$LANGUAGES = array(
    	'en' => array(
            'bigace'                => 'BIGACE Web CMS',
            'description'           => 'This pre-installer script will help to extract BIGACE on your server.',
            'error_memory'          => 'This script cannot run, because your PHP engine has not enough memory available (currently: '.$freeMem.').<br>Please check your PHP setting (file: <u>php.ini</u>) "memory_limit". Its value should be at least "'.MEMORY_NEEDED.'M".',
            'error_memory_sm'       => 'The INI setting could not be modified for script runtime, cause SAFE MODE is activated.<br/>You might encounter problems when running BIGACE in this environment. Please contact the <a href="http://forum.bigace.de/">BIGACE Forum</a>.<br/>Open the <a href="README">README</a> file to get information about a manual installation.',
            'error_no_gz'           => 'The automatic extraction will not work, because your PHP has neither GZ support nor the ZIP modul activated.<br/><br/>Please read the <a href="http://wiki.bigace.de/bigace:installation" target="_blank">Installation Guide</a>.',
            'error_phpversion'      => 'BIGACE requires at least PHP Version '.PHPVERSION_NEEDED.' (yours: '.phpversion().'). Please read the News entry &quot;<a href="http://www.bigace.de/bigace-goes-php-5.html">BIGACE goes PHP 5</a>&quot;.',
            'error_one_subdir'      => 'MORE THAN ONE SUBDIRECTORY IS NOT SUPPORTED!',
            'intro'                 => 'PLEASE WAIT WHILE EXTRACTING FILES TO:',
            'please_wait'           => 'Do not reload, this will need some time!',
            'extracting'            => 'Extracting',
            'extract_button'        => 'INSTALL',
            'extracting_to'         => 'Extracting to',
            'extracting_success'    => 'Extracting was successful',
            'extracting_wait'       => 'Please be patient, this may take some time! You will be redirected automatically.',
            'folder'                => 'Sub-directory',
            'folder_here'           => 'If you want to extract the files here, leave this empty!',
            'folder_info'           => 'Specify a directory, if you want to extract not in the current folder:',
			'multiple_files'		=> 'More than one file is found, that might be a BIGACE installation ZIP.<br>Please select the one you want to install from:',
			'multiple_files_select' => 'Select file to install from',

            'safemode_error'        => 'You have activated <b>Safe Mode</b>!',
            'safemode_info'         => 'BIGACE might not work properly with PHPs activated SAFE MODE, nevertheless the installation script was able to execute.',
            'safemode_patch'        => 'Successful created Safemode Patch.',
            'safemode_next'         => 'Go on with the installation.',
            'safemode_subdir'       => 'You have SAFE MODE activated. The Installer will only work, if you install in a Sub-Directory!',

            'error_extract'         => 'ERRORS OCCURED DURING EXTRACTION.<br>PLEASE FIX THE PROBLEMS AND RELOAD THIS PAGE!',
            'error_rights_startdir' => 'CHANGE THE DIRECTORY ACCESS RIGHTS TO "0777" FOR DIRECTORY',
            'error_safemode_patch'  => 'The script failed, creating the Safemode Patch. Please report this at the <a href="http://forum.bigace.de/" target="_blank">BIGACE Forum</a>.',
            'upload_info'           => 'Could not find a BIGACE installation ZIP File, please upload the BIGACE Install ZIP File with this formular.',
            'upload_choose_file'    => 'Please choose the BIGACE ZIP, that was part of the original download!',
            'upload_btn'            => 'Upload File...',

            'next_title_install'    => '',
            'next_upgrade_link'     => 'Start Upgrade',
            'next_install_teaser'   => 'Click here, if you are not redirected to the Installation',
            'next_upgrade_info'     => "Now you can execute the update.",// and don't forget to apply the manual configuration changes. <br>A link for getting more information is shown at the end of the upgrade process!",
		),
    	'de' => array(
		    'bigace' 			    => 'BIGACE Web CMS',
		    'description'			=> 'Dieser Vor-Installer hilft dabei BIGACE auf dem Server zu entpacken.',
		    'error_memory'		    => 'Dieses Skript kann nur laufen, wenn PHP genügend Speicher zur Verfügung steht (momentan: '.$freeMem.').<br/>Bitte überprüfen Sie ihre PHP Einstellungen (Datei: <u>php.ini</u>) "memory_limit". Dieser Wert sollte mindestens auf "'.MEMORY_NEEDED.'M" stehen.',
		    'error_memory_sm'       => 'PHP INI Einstellungen konnten nicht angepasst werden, da SAFE MODE aktiviert ist.<br/>BIGACE unterstützt dies nicht zu 100%. Bei Fragen wenden Sie sich an das <a href="http://forum.bigace.de/">BIGACE Forum</a>.<br/>Bitte lesen Sie die <a href="README">README</a> Datei um mehr über eine manuelle Installation zu erfahren.',
		    'error_no_gz'		    => 'Das automatische Entpacken funktioniert nicht, da Ihre PHP Installation weder GZ Unterstützung bietet noch das ZIP Modul aktiviert ist.<br/><br/>Bitte lesen Sie die <a href="http://wiki.bigace.de/bigace:installation" target="_blank">Installations Hinweise</a>.',
		    'error_phpversion'		=> 'BIGACE benötigt mindestens PHP Version '.PHPVERSION_NEEDED.' (Sie haben: '.phpversion().'). Bitte lesen Sie dazu die News &quot;<a href="http://www.bigace.de/bigace-goes-php-5.html">BIGACE goes PHP 5</a>&quot;.',
	        'error_one_subdir'      => 'MEHR ALS EIN UNTERVERZEICHNISS WIRD NICHT UNTERSTÜTZT!',
            'intro'                 => 'BITTE WARTEN SIE, WÄHREND DIE DATEIEN ENTPACKT WERDEN NACH:',
            'please_wait'           => 'Bitte nicht neuladen, dies wird einige Zeit in Anspruch nehmen!',
            'extracting'            => 'Entpacke',
            'extract_button'        => 'INSTALLIEREN',
            'extracting_to'         => 'Entpacke nach',
            'extracting_success'    => 'Entpacken war erfolgreich',
            'extracting_wait'       => 'Bitte warten Sie einen Moment, dieser Vorgang kann einige Zeit in Anspruch nehmen! Sie werden automatisch weitergeleitet.',
            'folder'                => 'Unterverzeichnis',
            'folder_here'           => 'Wenn Sie hier entpacken wollen, lassen Sie das Feld einfach leer!',
            'folder_info'           => 'Geben Sie ein Unterverzeichnis an, falls Sie nicht im aktuellen Verzeichnis entpacken wollen:',
			'multiple_files'		=> 'Es wurden mehrere mögliche BIGACE Installationsarchive gefunden.<br>Bitte suchen Sie das aus, das Sie installieren wollen:',
			'multiple_files_select' => 'Suchen Sie die zu verwendende Datei aus',

            'safemode_error'        => 'Ihr PHP läuft mit aktivierter <b>Safe Mode</b> Einstellung!',
            'safemode_info'         => 'BIGACE wird eventuell nicht korrekt funktionieren, da Ihre PHP Installation mit aktiviertem SAFE MODE läuft. Sollten Sie Fragen haben, freuen wir uns diese im <a href="http://forum.bigace.de/" target="_blank">BIGACE Forum</a> beantworten zu können.',
            'safemode_patch'        => 'Safemode Patch wurde erstellt.',
            'safemode_next'         => 'Installation fortführen.',
            'safemode_subdir'       => 'Ihr PHP läuft mit aktivierter <b>Safe Mode</b> Einstellung. Der Installer kann nur korrekt arbeiten, wenn Sie in ein Unterverzeichnis installieren.',

            'error_extract'         => 'ES TRATEN FEHLER WÄHREND DES ENTPACKENS AUF.<br>BITTE BEHEBEN SIE DIE PROBLEME UND LADEN DIESE SEITE ANSCHLIESSEND NEU!',
            'error_rights_startdir' => 'ÄNDERN SIE DIE ZUGRIFFSRECHTE FÜR DIESES VERZEICHNISS (z.B. 0777)',
            'error_safemode_patch'  => 'Es trat ein Fehler beim Erzeugen des Safemode Patches auf. Bitte melden Sie dieses Problem im <a href="http://forum.bigace.de/" target="_blank">BIGACE Forum</a>.',
            'upload_info'           => 'Das BIGACE Installations Archiv (ZIP) konnte nicht gefunden werden. Sie können die Datei in dieses Verzeichnis kopieren oder mit diesem Forum hochladen.',
            'upload_choose_file'    => 'Bitte wählen Sie die BIGACE ZIP Datei, die Teil des originalen Downloads war!',
            'upload_btn'            => 'Datei hochladen...',

			'next_title_install'	=> '',
		  	'next_upgrade_link'		=> 'Starte das Upgrade',
			'next_install_teaser'	=> 'Klicken Sie hier, wenn Sie nicht automatisch zur Installation weitergeleitet werden',
			'next_upgrade_info'		=> "Jetzt können Sie das Upgrade starten.",// Aber vergessen Sie nicht, die notwendigen Konfigurationsänderungen vorzunehmen!<br>Ein Link zu weiteren Informationen wird Ihnen am Ende des Upgrades genannt.",
        ),
		'sv' => array(
		    'bigace' 			    => 'BIGACE Web CMS',
		    'description'			=> 'Den här pre-installern hjälper dig att packa upp BIGACE till din server.',
		    'error_memory'		    => 'Det här skriptet kan inte köras, eftersom PHP motorn inte har tillräckligt med minne tillgängligt (aktuellt: '.$freeMem.').<br>Vänligen kontrollera din PHP inställning (fil: <u>php.ini</u>) "memory_limit". Värdet bör vara minst"'.MEMORY_NEEDED.'M".',
		    'error_memory_sm'       => 'Eftersom SAFE MODE är aktivt, kan inte inställnigarna i INI filerna modifieras.<br/>Du kanske stöter på problem med denna inställning när du kör BIGACE. Vänligen uppsök <a href="http://forum.bigace.de/">BIGACE Forumet</a>.<br/>Öppna <a href="README">README</a> filen för att få information om manuell installation.',
		    'error_no_gz'		    => 'Uppackningen kan inte slutföras eftersom ditt PHP varken har stöd för GZ eller ZIP modulen.<br/><br/>Vänligen läs <a  href="http://wiki.bigace.de/bigace:installation" target="_blank">Installations Guiden</a>.',
		    'error_phpversion'		=> 'BIGACE kräver minst PHP Version '.PHPVERSION_NEEDED.' (din: '.phpversion().'). Vänligen läs nyhetssidan &quot;<a href="http://www.bigace.de/bigace-goes-php-5.html">BIGACE goes PHP 5</a>&quot;.',
	        'error_one_subdir'      => 'MER ÄN EN UNDERKATALOG STÖDS INTE!',
            'intro'                 => 'VÄNLIGEN VÄNTA MEDANS FILERNA PACKAS UPP TILL:',
            'please_wait'           => 'Ladda inte om sidan, det tar lite tid!',
            'extracting'            => 'Extraherar',
            'extract_button'        => 'INSTALLERA',
            'extracting_to'         => 'Extraherar till',
            'extracting_success'    => 'Extrahering lyckades!',
            'extracting_wait'       => 'Vänligen vänta, det här kan ta en liten stund! Nästa sida kommer automatiskt.',
            'folder'                => 'Underkatalog',
            'folder_here'           => 'Om du vill packa upp här, lämnar du fältet tomt!',
            'folder_info'           => 'Ange en katalog, ifall du inte vill använda aktuell katalog:',
			'multiple_files'		=> 'Det finns mer än en fil som skulle kunna vara BIGACE installationen.<br>Vänligen välj den du vill använda:',
			'multiple_files_select' => 'Välj fil att installera från',

            'safemode_error'        => 'Du har <b>Safe Mode</b> aktiverat!',
            'safemode_info'         => 'BIGACE kanske inte fungerar ordentligt med PHP i<b>Safe Mode</b>, Ändå lyckades installations skriptet.',
            'safemode_patch'        => 'Lyckades skapa Safemode Patch.',
            'safemode_next'         => 'Fortsätt med installationen.',
            'safemode_subdir'       => 'Du har SAFE MODE aktiverat. Scriptet kan då bara installera till en underkatalog!',

            'error_extract'         => 'ETT FEL UPPSTOD UNDER EXTRAHERINGEN.<br>VÄNLIGEN RÄTTA TILL PROBLEMEN OCH LADDA OM DEN HÄR SIDAN!',
            'error_rights_startdir' => 'ÄNDRA KATALOGRÄTTIGHETEN TILL "0777" FÖR KATALOGEN',
            'error_safemode_patch'  => 'Skriptet misslyckades, skapar en Safemode Patch. Vänligen rapportera detta i <a href="http://forum.bigace.de/" target="_blank">BIGACE Forumet</a>.',
            'upload_info'           => 'Kunde inte hitta BIGACE installations ZIP Filen, vänligen ladda upp BIGACE Install ZIP Filen via detta Web Formulär!',
            'upload_choose_file'    => 'Vänligen välj BIGACE ZIP filen, som var med i det nedtankade paketet!',
            'upload_btn'            => 'Skicka fil...',

			'next_title_install'	=> '',
        	'next_upgrade_link'		=> 'Starta uppgradering',
			'next_install_teaser'	=> 'Klicka här, ifall du inte automatiskt skickas till installationen',
			'next_upgrade_info'		=> "Nu kan du starta uppgraderingen.",//, men glöm inte att göra de manuella ändringarna.<br>En länk med mer information kommer att visas i slutet av uppgraderings processen!",
        ),
		'fi' => array(
            'bigace'                => 'BIGACE Web CMS',
            'description'           => 'Tämä esi-asennus skripti auttaa sinua kopioimaan BIGACE:n palvelimeesi.',
            'error_memory'          => 'Tätä skriptiä ei voida käynnistää, koska PHP koneella ei ole riittävästi saatavaa muistia (saatavilla: '.$freeMem.').<br>Ole hyvä tarkista PHP asetukset (tiedosto: <u>php.ini</u>) "memory_limit". Arvo on oltava vähintään "'.MEMORY_NEEDED.'M".',
            'error_memory_sm'       => 'INI asetusta ei voitu muokata skriptin ajoon, koska SAFE MODE on aktivoitu.<br/>Virheitä voi ilmetä BIGACE:n ajossa tässä ympäristössä. Ole hyvä ota yhteyttä <a href="http://forum.bigace.de/">BIGACE Foorumissa</a>.<br/>Avaa <a href="README">README</a> tiedosto saadaksesi tietoa manuaalisesta asennuksesta.',
            'error_no_gz'           => 'Automaattista kopioimista ei voida käynnistää, koska PHP asennuksessasi ei ole GZ tukea.<br/><br/>Ole hyvä lue <a href="http://wiki.bigace.de/bigace:installation" target="_blank">Asennus ohjeet</a>.',
            'error_phpversion'      => 'BIGACE vaatii vähintään PHP Versiota '.PHPVERSION_NEEDED.' (nykyinen: '.phpversion().'). Ole hyvä lue lisää kohdassa &quot;<a href="http://www.bigace.de/bigace-goes-php-5.html">BIGACE goes PHP 5</a>&quot;.',
            'error_one_subdir'      => 'ENEMMÄN KUIN YKSI ALA-KANSIO EI OLE TUETTU!',
            'intro'                 => 'OLE HYVÄ ODOTA SILLÄ AIKAA KUN TIEDOSTOT KOPIOIDAAN KANSIOON:',
            'please_wait'           => 'Älä päivitä sivua, tämä kestää hetken!',
            'extracting'            => 'Kopioidaan',
            'extract_button'        => 'ASENNA',
            'extracting_to'         => 'Kopioidaan kansioon',
            'extracting_success'    => 'Kopioiminen onnistui',
            'extracting_wait'       => 'Ole kärsivällinen, tämä voi kestää hetken! Sinut edelleenvälitetään automaattisesti.',
            'folder'                => 'Ala-kansio',
            'folder_here'           => 'Jos haluat kopioida tiedostot tänne, jätä kenttä tyhjäksi!',
            'folder_info'           => 'Syötä kansion nimi, jos et halua kopioida tiedostot nykyiseen olevaan kansioon:',
			'multiple_files'		=> 'More than one file is found, that might be a BIGACE installation ZIP.<br>Please select the one you want to install from:',
			'multiple_files_select' => 'Select file to install from',

            'safemode_error'        => 'Olet aktivoinut <b>Safe Mode</b>!',
            'safemode_info'         => 'BIGACE ei ehkä toimi kunnollisesti PHPn tilassa SAFE MODE, tästä huolimatta skripti onnistui suoriutumaan.',
            'safemode_patch'        => 'Safemode Patch:in luominen onnistui.',
            'safemode_next'         => 'Jatka asennus.',
            'safemode_subdir'       => 'Sinulla on SAFE MODE aktivoitu. Asennus toimii vain, jos asennat ala-kansioon!',

            'error_extract'         => 'VIRHEITÄ ILMEISTYI KOPIOIMISEN YHTEYDESSÄ.<br>OLE HYVÄ KORJAA ONGELMAT JA PÄIVITÄ TÄTÄ SIVUA!',
            'error_rights_startdir' => 'MUUTA KANSION OIKEUDET ARVOON "0777"',
            'error_safemode_patch'  => 'Skripti epäonnistui Safemode Patch:in luomisen yhteydessä. Ole hyvä raportoi asiasta <a href="http://forum.bigace.de/" target="_blank">BIGACE Foorumissa</a>.',
            'upload_info'           => 'BIGACE:n asennukseen tarvittavaa ZIP arkistoa ei löytynyt, ole hyvä lataa BIGACE asennuksen ZIP arkisto tällä lomakkeella.',
            'upload_choose_file'    => 'Ole hyvä valitse BIGACE ZIP arkisto, joka toimitettiin alkuperäisessä latauksessa!',
            'upload_btn'            => 'Lataa Tiedosto...',

            'next_title_install'    => 'Asennus',
            'next_upgrade_link'     => 'Käynnistä Päivitys',
            'next_install_teaser'   => 'Napauta linkkiä jos sinua ei edelleenvälitetä sivulle',
            'next_upgrade_info'     => "Nyt voit käynnistää päivityksen.",//, muista myös asettaa manuaalisesti asetuksien muutokset. <br>Lisää tietoa löydät linkistä, joka näytetään päivityksen loppuvaiheessa!",
		),
        'pt' => array(
            'bigace'                => 'BIGACE Web CMS',
            'description'           => 'Este script de pré instalação irá ajudá-lo a extrair o BIGACE em seu servidor.',
            'error_memory'          => 'Este script não pôde rodar pois seu PHP engine não possui memória suficiente disponível (atual: '.$freeMem.').<br>Por favor cheque as configurações de seu PHP (arquivo: <u>php.ini</u>) "memory_limit". Este valor precisa ser ao menos "'.MEMORY_NEEDED.'M".',
            'error_memory_sm'       => 'A configuração do INI não pôde ser modificada para o tempo de execução do script pois o MODO DE SEGURANÇA está ativado.<br/>Você pode ter problemas para rodar o BIGACE neste ambiente. Por favor contacte o <a href="http://forum.bigace.de/">Fórum do BIGACE</a>.<br/>Abra o arquivo <a href="README">LEIAME</a> para obter informação sobre como instalar manualmente.',
            'error_no_gz'           => 'A extração automática não irá funcionar pois seu PHP não tem suporte para arquivos GZ.<br/><br/>Por favor leia o <a href="http://wiki.bigace.de/bigace:installation" target="_blank">Guia de Instalação</a>.',
            'error_phpversion'      => 'BIGACE requer ao menos PHP Versão '.PHPVERSION_NEEDED.' (yours: '.phpversion().'). Por favor vejas as Novidades &quot;<a href="http://www.bigace.de/bigace-goes-php-5.html">BIGACE muda para PHP 5</a>&quot;.',
            'error_one_subdir'      => 'MAIS DO QUE UM SUBDIRETÓRIO NÃO É SUPORTADO!',
            'intro'                 => 'POR FAVOR ESPERE ENQUANTO OS ARQUIVOS SAO EXTRAIDOS PARA:',
            'please_wait'           => 'Não atualize a página, isto necessita de algum tempo!',
            'extracting'            => 'Extraindo',
            'extract_button'        => 'INSTALAR',
            'extracting_to'         => 'Extraindo para',
            'extracting_success'    => 'Extração concluída com sucesso',
            'extracting_wait'       => 'Por favor seja paciente, isto pode levar algum tempo! Você será redirecionado automaticamente.',
            'folder'                => 'Subdiretório',
            'folder_here'           => 'Se você quiser extarir os arquivos aqui, bigace_install_2.5_RC3.zip deixe este campo vazio!',
            'folder_info'           => 'Especifique um diretório caso não queira extrair para dentro da pasta atual:',
			'multiple_files'		=> 'More than one file is found, that might be a BIGACE installation ZIP.<br>Please select the one you want to install from:',
			'multiple_files_select' => 'Select file to install from',

            'safemode_error'        => 'Você ativou o <b>Modo de Segurança</b>!',
            'safemode_info'         => 'BIGACE pode não funcionar adequadamente com MODO DE SEGURANÇA do PHP ativado, todavia o script de instalação pôde ser executado.',
            'safemode_patch'        => 'Patch de Modo de Segurança criado com sucesso.',
            'safemode_next'         => 'Prossiga com a instalação.',
            'safemode_subdir'       => 'O MODO DE SEGURANÇA está ativado. O Instalador funcionará somente se você instalar em um Subdiretório!',

            'error_extract'         => 'ALGUNS ERROS OCORRERAM DURANTE A EXTRAÇÃO.<br>POR FAVOR CONSERTE OS PROBLEMAS E RECARREGUE ESTA PÁGINA!',
            'error_rights_startdir' => 'ALTERE OS DIREITOS DE ACESSO DO DIRETÓRIO PARA "0777"',
            'error_safemode_patch'  => 'O script falhou em criar o Patch de Modo de segurança. Por favor reporte este problema no <a href="http://forum.bigace.de/" target="_blank">Fórum BIGACE</a>.',
            'upload_info'           => 'Não foi possível encontrar o arquivo ZIP da instalação do BIGACE, por favor faça upload do ZIP de Instalação do BIGACE através deste formulário.',
            'upload_choose_file'    => 'Por favor selecione o arquivo ZIP do BIGACE que faça parte do download original!',
            'upload_btn'            => 'Enviar arquivo...',

            'next_title_install'    => 'Instalação',
            'next_upgrade_link'     => 'Começar a Atualização',
            'next_install_teaser'   => 'Clique no link se você não for redirecionado para',
            'next_upgrade_info'     => "Agora você pode executar o update e não se esqueça de aplicar alterações de configuração manual. <br>Um link para obter maiores informações será mostrado ao final do processo de atualização!",
		),
        /*--------------------------------------------------+
        | Installation Script Translation into Spanish
        | By Tomas Aparicio - h2non/at/rijndael-project.com
        *--------------------------------------------------*/
    	'es' => array(
            'bigace'                => 'BIGACE Web CMS',
            'description'           => 'Este script pre-instalador le ayudar&aacute; a extraer BIGACE en su servidor.',
            'error_memory'          => 'Este script no puede funcionar porque su sistema PHP no tiene la memoria suficiente para llevar este proceso (actualmente: '.$freeMem.').<br>Por favor, chequee su parametro PHP (archivo: <u>php.ini</u>) "memory_limit". Este valor debe estar cómo mínimo en "'.MEMORY_NEEDED.'M".',
            'error_memory_sm'       => 'El atributo INI no puede ser modificado por el script (runtime), la causa es porque el modo a prueba de errores est&aacute; activado.<br/>Esto puede surgir problemas cuando se ejecuta BIGACE en este entorno. 	Por favor, contacte en los <a href="http://forum.bigace.de/">Foros de BIGACE</a>.<br/>Abr&aacute; el fichero <a href="README">README</a> para obtener informaci&oacute;n acerca de una instalaci&oacute;n manual.',
            'error_no_gz'           => 'La extracción automática no funcionará, porque no tiene su PHP no soporta compresi&oacute;n GZ .<br/><br/>Lea la <a href="http://wiki.bigace.de/bigace:installation" target="_blank">Gu&iacute;a de Instalaci&oacute;n</a>.',
            'error_phpversion'      => 'BIGACE requiere c&oacute;mo m&iacute;nimo la versi&oacute;n de PHP '.PHPVERSION_NEEDED.' (su versi&oacute;n: '.phpversion().'). Por favor, lea las noticias entrada &quot;<a href="http://www.bigace.de/bigace-goes-php-5.html">BIGACE goes PHP 5</a>&quot;.',
            'error_one_subdir'      => 'M&Aacute;S DE UN subdirectorio no es compatible!',
            'intro'                 => 'Por favor, espere mientras se realiza la extracci&oacute;n de archivos en:',
            'please_wait'           => 'No recargue la p&aacute;gina, se necesita alg&uacute;n tiempo!',
            'extracting'            => 'Extrayendo',
            'extract_button'        => 'INSTALAR',
            'extracting_to'         => 'Extrayendo a:',
            'extracting_success'    => 'Extracci&oacute;n finalizada con &eacute;xito!',
            'extracting_wait'       => 'Por favor, sea paciente. Esto puede llevar un poco de tiempo. Ser&aacute; redirigido autom&aacute;ticamente.',
            'folder'                => 'Subdirectorio',
            'folder_here'           => 'Si desea extraer los archivos aqu&iacute;, dejelo vacio previamente!',
            'folder_info'           => 'Especifique un directorio, si usted no desea extraer en la carpeta actual:',
			'multiple_files'		=> 'More than one file is found, that might be a BIGACE installation ZIP.<br>Please select the one you want to install from:',
			'multiple_files_select' => 'Select file to install from',

            'safemode_error'        => 'Tiene activado el modo prueba errores (safemode)!',
            'safemode_info'         => 'BIGACE podría no funcionar correctamente con PHP activado Modo a prueba de errores, sin embargo, la secuencia de instalación es capaz de ejecutarla.',
            'safemode_patch'        => 'Parche Modo Prueba (SafeMode) creado con éxito.',
            'safemode_next'         => 'Continuar con la instalaci&oacute;n.',
            'safemode_subdir'       => 'Usted tiene activado MODO SEGURO. El instalador s&oacute;lo funcionar&aacute; si se instala en un subdirectorio!',

            'error_extract'         => 'HAN OCURRIDO ERRORES DURANTE LA EXTRACCI&Oacute;N.<br>POR FAVOR, CORRIGE LOS PROBLEMAS Y RECARGA ESTA P&Aacute;GINA!',
            'error_rights_startdir' => 'CAMBIE LOS PERMISOS DEL DIRECTORIO DE ACCESO A "0777"',
            'error_safemode_patch'  => 'El Script ha fallado creando la ruta de prueba (savemode path). Por favor, reporte este error a los <a href="http://forum.bigace.de/" target="_blank">Foros de BIGACE</a>.',
            'upload_info'           => 'No se puede encontrar el archivo ZIP de la instalaci&oacute;n de BIGACE, por favor, suba el ZIP de instalaci&oacute;n de BIGACE con este formulario.',
            'upload_choose_file'    => 'Por favor, seleccione el ZIP de BIGACE, que fue parte de la descarga original',
            'upload_btn'            => 'Subir Archivo...',

            'next_title_install'    => 'Instalaci&oacute;n',
            'next_upgrade_link'     => 'Empezar Actualizaci&oacute;n',
            'next_install_teaser'   => 'Haga clic en el enlace si no se redirige a',
            'next_upgrade_info'     => "Ahora puede ejecutar la actualizaci&oacute;n y no se olvide de aplicar los cambios de configuraci&oacute;n manual. <br> Para obterner m&aacute;s informaci&oacute;n pulse en el enlace mostrado al final del proceso de actualizaci&oacute;n.",
		),
        'ru' => array(
            'bigace'                => 'Система управления контентом BIGACE',
            'description'           => 'Этот скрипт предварительной установки поможет распаковать BIGACE на Ваш сервер.',
            'error_memory'          => 'Скрипт предварительной установки не может запуститься из-за малого объема доступной PHP памяти (доступно: '.$freeMem.').<br>Пожалуйста внесите исправления в параметр "memory_limit" в настройках PHP (файл: <u>php.ini</u>). Необходимо как минимум "'.MEMORY_NEEDED.'M".',
            'error_memory_sm'       => 'Значения параметров настройки PHP, необходимых для работы предварительного установщика, не могут быть изменены, так как PHP работает в безопасном режиме.<br/>Вы можете столкнуться с трудностями при работе BIGACE. За дополнительных сведений пожалйуста обратитесь на <a href="http://forum.bigace.de/">форум BIGACE</a>.<br/>Просмотрите файл <a href="README">README</a> для получения информации о самостоятельной установке.',
            'error_no_gz'           => 'Автоматическое извлечение системы из архива невозможно из-за отсутствия поддержки GZ или ZIP модуля PHP.<br/><br/>Пожалуйста обратитесь к <a href="http://wiki.bigace.de/bigace:installation" target="_blank">Инструкции по установке</a>.',
            'error_phpversion'      => 'Для работы BIGACE необходима версия PHP не ниже '.PHPVERSION_NEEDED.' (Ваша версия: '.phpversion().'). Подробнее смотрите &quot;<a href="http://www.bigace.de/bigace-goes-php-5.html">BIGACE переходит на PHP 5</a>&quot;.',
            'error_one_subdir'      => 'БОЛЬШЕ ОДНОЙ ПАПКИ НЕ ПОДДЕРЖИВАЕТСЯ!',
            'intro'                 => 'ПОЖАЛУЙСТА ПОДОЖДИТЕ ЗАВЕРШЕНИЯ ИЗВЛЕЧЕНИЯ ФАЙЛОВ В:',
            'please_wait'           => 'Не перезагружайте страницу, для распаковки потребуется некоторое время!',
            'extracting'            => 'Извлечение файлов',
            'extract_button'        => 'УСТАНОВИТЬ',
            'extracting_to'         => 'Извлечение файлов в:',
            'extracting_success'    => 'Распаковка успешно завершена',
            'extracting_wait'       => 'Пожалуйста подождите, установка займет некоторое время! Страница будет обновлена автоматически.',
            'folder'                => 'Папка',
            'folder_here'           => 'Если Вы хотите установить систему в указанную папку, оставьте это поле пустым!',
            'folder_info'           => 'Если Вы не хотите устанавливать систему в указанную папку, то укажите путь для установки:',
            'multiple_files'        => 'Найдено более одного установочного архива системы BIGACE.<br>Пожалуйста выберите какой Вы хотите установить:',
            'multiple_files_select' => 'Выберите архив для установки',

            'safemode_error'        => 'У Вас включен безопасный режим PHP (<b>Safe Mode</b>)!',
            'safemode_info'         => 'BIGACE может работать некорректно при включенном безопасном режиме, тем не менее скрипт предварительной установки может работать в данном режиме.',
            'safemode_patch'        => 'Успешное применение патча для безопасного режима.',
            'safemode_next'         => 'Продолжить установку.',
            'safemode_subdir'       => 'У Вас включен безопасный режим. Установщик может продолжить работу только при условии извлечения файлов в отдельную папку!',

            'error_extract'         => 'ВО ВРЕМЯ ИЗВЛЕЧЕНИЯ ФАЙЛОВ ВОЗНИКЛА ОШИБКА.<br>ПОЖАЛУЙСТА УСТРАНИТЕ ПРОБЛЕМЫ И ОБНОВИТЕ ЭТУ СТРАНИЦУ!',
            'error_rights_startdir' => 'ИЗМЕНИТЕ ПРАВА ДОСТУПА К ПАПКЕ НА "0777"',
            'error_safemode_patch'  => 'При создании патча для безопасного режима произошла ошибка. Пожалуйста сообщите об этом на <a href="http://forum.bigace.de/" target="_blank">форум BIGACE</a>.',
            'upload_info'           => 'Не могу найти установочный архив BIGACE, пожалуйста загрузите данный архив.',
            'upload_choose_file'    => 'Выберите установочный архив BIGACE, который содержался в пакете загруженном с нашего сайта!',
            'upload_btn'            => 'Загрузить файл...',

            'next_title_install'    => '',
            'next_upgrade_link'     => 'Начать обновление',
            'next_install_teaser'   => 'Нажмите сюда, если Вас не произошло автоматическое перенаправление к установке',
            'next_upgrade_info'     => "Теперь Вы можете запустить обновление.",// и не забудьте внести необходимы изменения в конфигурацию. <br>Ссылка для получения дополнительной информации будет указана после завершения обновления!",
        )
    );

    // if safe mode is ebanled, we should NOT use umask!
    define('ENABLED_SAFEMODE', (bool)ini_get('safe_mode') === true);

    define('INSTALL_PARAM_ZIP', 'zipFile');
    define('INSTALL_PARAM_SUBDIR', 'subDir');
    define('INSTALL_PARAM_MODE', 'mode');
    define('INSTALL_MODE_INPUT', 'input');          // shows the formular to fetch sub directory
    define('INSTALL_MODE_EXTRACT', 'extract');      // performs the extraction if all parameter are properly

    define('UPLOAD_FILE_PARAMETER', 'bigacezip');

    define('SCRIPT_NAME_SAFE_MODE', 'install_bigace_safe_mode.php');
    define('ZIP_FILE_BIGACE_NEEDLE', 'bigace_install');
    define('ZIP_FILE_EXTENSION', '.zip');
    define('ZIP_DIR_SEPARATOR', '/');
    define('DIR_SEPARATOR', '/');

    // -------------------------------------------------------
    // Find language to display
    $myLang = 'en';
    $acceptedLangs = get_accept_browser_languages();
    foreach($acceptedLangs AS $tempLang => $tempFactor)
    {
		foreach($LANGUAGES AS $loc => $translations) {
		    if (strpos($tempLang, $loc) === 0) {
                $myLang = $loc;
                break 2;
		    }
        }
    }
    if(!isset($LANGUAGES[$myLang])) // do we need this last fallback?
    	$myLang = 'en';
    $LANGS = $LANGUAGES[$myLang];
    // -------------------------------------------------------

    $mode = INSTALL_MODE_INPUT;
    if(isset($_POST[INSTALL_PARAM_MODE])) {
        $mode = $_POST[INSTALL_PARAM_MODE];
    } else if(isset($_GET[INSTALL_PARAM_MODE])) {
        $mode = $_GET[INSTALL_PARAM_MODE];
    }

    // ------------------------------- UPLOAD BIGACE ZIP -------------------------------
    if(isset($_FILES[UPLOAD_FILE_PARAMETER]) && (!isset($_FILES[UPLOAD_FILE_PARAMETER]['error']) || $_FILES[UPLOAD_FILE_PARAMETER]['error'] == UPLOAD_ERR_OK)) {
        $tmpName = $_FILES[UPLOAD_FILE_PARAMETER]['tmp_name'];
        $newName = dirname(__FILE__) . "/webform_".$_FILES[UPLOAD_FILE_PARAMETER]['name'];
        if(!@move_uploaded_file($_FILES[UPLOAD_FILE_PARAMETER]['tmp_name'], $newName))
        {
            $fh = fopen($tmpName,'rb');
            $daten = fread($fh, filesize($tmpName));
            fclose($fh);

            $fh = fopen($newName,'w');
            fwrite($fh,$daten);
            fclose($fh);
        }
        unset($tmpName);
        unset($newName);
    }
    // ---------------------------------------------------------------------------------

    // ------------------------------- MAKE SURE WE USE THE CORRECT FILENAME -------------------------------
    if(strpos(__FILE__, "index.php") !== false) {
		if(copy(dirname(__FILE__).'/index.php', dirname(__FILE__).'/install_bigace.php')) {
			unlink(dirname(__FILE__).'/index.php');
			header('Location: install_bigace.php');
		}
	}
    // ---------------------------------------------------------------------------------

?>
<html>
<head>
    <title>BIGACE Web CMS</title>
    <style type="text/css">
    body, td, p, th { font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size:16px; }
    body { width:80%; margin:auto; background-color:#ddd; }
    .header { color:#fff; height:80px; margin:10px auto;background-color: #003441; padding: 20px 0px 0px 20px; font-size: 28px; border: 1px solid #000; }
    .header div {font-size:16px;margin-top:10px;}
    .header span {display:block;font-size:16px;margin-top:10px;}
    .name {font-weight:bold;font-size:22px; }
    .updateBox { margin-top:10px; width:100%;background-color:#ffffff;border:1px solid #999999;  }
    h1 { margin:0px; font-size:20px; }
    h2 { margin:0 0 6px 0; font-size:22px; }
    textarea { width: 500px; height: 250px; border:1px solid #000000; padding:3px; }
    .error { color:red; font-weight:bold; }
    .title { padding:3px; background-color:#dddddd; border:1px solid #999999; }
    .intro { font-weight:bold; }
    div.inserted {padding:30px 0px 30px 20px; background-color:#b6756e;border:1px solid #ddd; margin:0 15px 0 15px; }
    p {margin:15px;}
    #progressBarDiv { margin-bottom: 20px; }
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="author" content="Kevin Papst" />
    <meta name="copyright" content="BIGACE Web CMS - http://www.bigace.de/" />
</head>
<body>
    <h1 class="header"><?php echo $LANGS['bigace']; ?><span><?php echo $LANGS['description']; ?></span></h1>
    <div class="updateBox">

    <?php

$canExecutePreInstall = true;

if(!$memOK){
	echo '<p class="error">'.$LANGS['error_memory'].'</p>';
    if(ini_get('safe_mode'))
        echo '<p class="error">'.$LANGS['error_memory_sm'].'</p>';
    $canExecutePreInstall = false;
}
else if(!function_exists('gzinflate') && !function_exists('zip_open'))
{
  echo '<p class="error">'.$LANGS['error_no_gz'].'</p>';
  $canExecutePreInstall = false;
}
else if(version_compare(phpversion(), PHPVERSION_NEEDED, ">=") === false)
{
  echo '<p class="error">'.$LANGS['error_phpversion'].'</p>';
  $canExecutePreInstall = false;
}

if($canExecutePreInstall)
{
    $performInstall = false;
    $zipFile = (isset($_POST[INSTALL_PARAM_ZIP]) ? $_POST[INSTALL_PARAM_ZIP] : (isset($_GET[INSTALL_PARAM_ZIP]) ? $_GET[INSTALL_PARAM_ZIP] : ''));
    $subDir = (isset($_POST[INSTALL_PARAM_SUBDIR]) ? $_POST[INSTALL_PARAM_SUBDIR] : (isset($_GET[INSTALL_PARAM_ZIP]) ? $_GET[INSTALL_PARAM_SUBDIR] : ''));

    $subDir = str_replace('..', '', $subDir);
    $subDir = str_replace('//', '/', $subDir);

    // perform the install
    if($mode == INSTALL_MODE_EXTRACT && (!ENABLED_SAFEMODE || $subDir != ''))
    {
        if($zipFile != '')
        {

            $performInstall = true;

            // fix path if only path separator was supplied
            if($subDir == DIR_SEPARATOR) {
                $subDir = '';
            }

            if(strlen(trim($subDir)) > 0)
            {
                // if no path sepearator was submitted, append one at the end
                if(strpos($subDir, DIR_SEPARATOR) === false) {
                    $subDir .= DIR_SEPARATOR;
                }

                // check if only one separator was supplied
                $check = explode(DIR_SEPARATOR, trim($subDir));
                //print_r($check);
                if(count($check) > 2) {
                    $subDir = $check[0] . DIR_SEPARATOR;
                    $performInstall = false;
                    echo '<br><span class="error">'.$LANGS['error_one_subdir'].'</span><br><br>';
                }
            }
        }
    }

    $extractTo = dirname(__FILE__) . DIR_SEPARATOR . $subDir;

    // check for proper set file rights
    if($performInstall)
    {
        $checkForProperRights = $extractTo;
        if(!file_exists($extractTo))
        {
            $checkForProperRights = dirname(__FILE__);
        }

        if(!is_writeable($checkForProperRights)) {
            echo '<br><span class="error">'.$LANGS['error_rights_startdir'].':<br>'.$checkForProperRights.'</span><br><br>';
            $performInstall = false;
        }
    }

    if($performInstall && $zipFile != '')
    {
        if(ENABLED_SAFEMODE && basename(__FILE__) != SCRIPT_NAME_SAFE_MODE)
        {
            $fh = @fopen(__FILE__,'r');
            $daten = @fread($fh, filesize(__FILE__));
            @fclose($fh);

            $fh = @fopen(SCRIPT_NAME_SAFE_MODE,'w');
            @fwrite($fh,$daten);
            @fclose($fh);

            if(filesize(__FILE__) == filesize(SCRIPT_NAME_SAFE_MODE)) {

                    $fh = @fopen($zipFile,'r');
                    $daten = @fread($fh, filesize($zipFile));
                    @fclose($fh);

                    $fh = @fopen('safemode_'.$zipFile,'w');
                    @fwrite($fh,$daten);
                    @fclose($fh);

                if(filesize($zipFile) == filesize('safemode_'.$zipFile))
                    $zipFile = 'safemode_'.$zipFile;

                mkdir($extractTo, DIRECTORY_PERMISSION);

                $link = SCRIPT_NAME_SAFE_MODE . '?' . INSTALL_PARAM_MODE . '=' . INSTALL_MODE_EXTRACT . '&' . INSTALL_PARAM_SUBDIR . '=' . $subDir . '&' . INSTALL_PARAM_ZIP . '=' . $zipFile;

                echo '<p class="error">'.$LANGS['safemode_error'].'</p>';
                echo '<p>'.$LANGS['safemode_info'].'</p>';
                echo '<script type="text/javascript"> location.href= \''.$link.'\'; </script>';
                echo '<p>'.$LANGS['safemode_patch'].'</p>';
                echo '<a href="'.$link.'">'.$LANGS['safemode_next'].'</a>';
            } else {
                echo '<p class="error">'.$LANGS['error_safemode_patch'].'</p>';
            }
            exit;
        }

        // save messages within these arrays
        $errors = array();

      	if(defined("ACCESS_UMASK")) {
        	$oldUmask = umask(ACCESS_UMASK);
      	}


        if(!file_exists($extractTo)) {
            @mkdir($extractTo,DIRECTORY_PERMISSION);
        }

        if(file_exists($extractTo))
        {
            ?>
            <p class="intro">
                <?php echo $LANGS['intro']; ?>
                <br/><?php echo $extractTo; ?>
            </p>
            <div id="progressBarDiv">
                <p><?php echo $LANGS['please_wait']; ?>
            	<br/><br/>
                <i><?php echo $LANGS['extracting']; ?> <span id="dotter">...</span></i></p>
            </div>
            <script language="javascript">
            <!--
                function writeDot() {
                    document.getElementById('dotter').innerHtml += '.';
                    window.setTimeout('writeDot()', 100);
                }
                writeDot();
            // -->
            </script>
            <?php

            // send html to browser
            flush();

            // and now finally extract that stuff!
            if(function_exists('zip_open')) {
                $errors = unzip_phpzip($zipFile, $extractTo);
            }
            else {
                $errors = unzip_simpleunzip($zipFile, $extractTo);
            }
        }
        else {
            // FIXME critical problem, stop extraction
        }

        // stop progressbar
        ?>
        <script language="javascript">
        <!--
        document.getElementById('progressBarDiv').style.display='none';
        // -->
        </script>
        <?php
        if(count($errors) > 0)
        {
            ?>
            <div class="inserted">
                <h2>Errors:</h2>
                <?php

                foreach($errors AS $msg)
                    echo $msg . "<br/>";


                ?>
            </div>
            <?php
    		echo '<p class="error">'.$LANGS['error_extract'].'</p>';
        } // errors occured
        else
        {
			echo '<div class="inserted">'.$LANGS['extracting_success'].'!</div>';

			if (file_exists($extractTo."upgrade.php"))
			{
				$nextStep = "upgrade.php";
				?>
				<p class="error">
				    <?php echo $LANGS['next_upgrade_info']; ?>
				</p>
                <br><br><a href="<?php echo $subDir . $nextStep; ?>"><?php echo $LANGS['next_upgrade_link']; ?></a>
				<?php
			}
			else if (file_exists($extractTo."misc/install/index.php") )
			{
				$nextStep = "misc/install/index.php";
				?>
	            <p><a href="<?php echo $subDir . $nextStep; ?>">
	            <?php echo $LANGS['next_install_teaser']; ?> <?php echo $LANGS['next_title_install']; ?></a></p>
		        <script language="JavaScript">
		        function nextStep() {
		            location.href = '<?php echo $subDir . $nextStep; ?>';
		        }
		        window.setTimeout('nextStep()', 2000);
		        </script>
				<?php
			}
			else
			{
				$nextStep = "index.php";
				?>
	            <p><a href="<?php echo $subDir . $nextStep; ?>">Upgraded system</a></p>
		        <script language="JavaScript">
		        function nextStep() {
		            location.href = '<?php echo $subDir . $nextStep; ?>';
		        }
		        window.setTimeout('nextStep()', 500);
		        </script>
				<?php
			}
        }

        ?>

        <?php
      	if(defined("ACCESS_UMASK") && isset($oldUmask)) {
        	umask($oldUmask);
      	}

    }
    else
    {
        // #########################################################################
        // Select ZIP and start extraction screen
        // #########################################################################
        $zipFiles = findZIPNames( dirname(__FILE__) );

        if(count($zipFiles) == 0)
        {
                echo '<p>'.$LANGS['upload_info'].'</p>';
                echo '<form action="'.$_SERVER['PHP_SELF'].'" method="POST" enctype="multipart/form-data">';
                echo '<p>'.$LANGS['upload_choose_file'].'</p>';
                echo '<input type="file" name="'.UPLOAD_FILE_PARAMETER.'" />';
                echo '<br/><input type="submit" value="'.$LANGS['upload_btn'].'" />';
                echo '</form>';
        }
        else {
        ?>
            <form action="" method="post">
            <input type="hidden" name="<?php echo INSTALL_PARAM_MODE; ?>" value="<?php echo INSTALL_MODE_EXTRACT; ?>">
            <?php
            if(count($zipFiles) == 1) {
                echo '<input type="hidden" name="'.INSTALL_PARAM_ZIP.'" value="'.$zipFiles[0].'">' . "\n";
            } else if(count($zipFiles) > 1) {
                echo $LANGS['multiple_files'];
                echo '<br><br>';
                echo '<b>'.$LANGS['multiple_files_select'].':</b> <select name="'.INSTALL_PARAM_ZIP.'">';
                foreach($zipFiles AS $zipFilename)
                    echo '<option value="'.$zipFilename.'">'.$zipFilename.'</option>';
                echo '</select>';
                echo '<br><br>';
            }


            echo '<p>';
            echo $LANGS['folder_info'];
            echo '<br />';
            echo dirname(__FILE__);
            echo '</p>';

            if(!ENABLED_SAFEMODE)
            {
                echo '<div class="inserted">' . $LANGS['folder_here'] . '<br /><br />';
            }
            else {
                echo '<p class="error">'.$LANGS['safemode_subdir'].'</p>
                        <div class="inserted">';
                if($subDir == '')
                    $subDir = 'cms/';
            }
            ?>
            <b><?php echo $LANGS['folder']; ?>:</b>
            <input type="text" onKeypress="document.getElementById('dirAddon').innerHTML = this.value"
                onKeydown="document.getElementById('dirAddon').innerHTML = this.value"
                onKeyup="document.getElementById('dirAddon').innerHTML = this.value"
                name="<?php echo INSTALL_PARAM_SUBDIR; ?>" value="<?php echo $subDir; ?>">
            <button type="submit" onclick="this.enabled = false;return true;"><?php echo $LANGS['extract_button']; ?></button>
            </div>

            <script type="text/javascript">
            document.write('<p><u><?php echo $LANGS['extracting_to']; ?>:</u> <?php echo dirname(__FILE__); ?>/');
            document.write('<span id="dirAddon"></span></p>');
            </script>

            <p><?php echo $LANGS['extracting_wait']; ?></p>
            </form>
        <?php
        }
    }
}
    ?>
    </div>
</body>
</html>
<?php

 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


    function findZIPNames($directory)
    {
        $filenames = array();
        if(is_dir($directory))
        {
    		$handle=opendir($directory);
    		while (false !== ($file = readdir ($handle))) {
    			if($file != "." && $file != ".." && is_file($directory . '/' . $file)) {
        			if (strpos($file, ZIP_FILE_BIGACE_NEEDLE) === false || strpos($file, ZIP_FILE_EXTENSION) === false) {
    		    	    //echo $file . '<br>';
    		    	} else {
    		    	    $filenames[] = $file;
    		    	}
    			}
    		}
    		closedir($handle);
    	} else {
            echo '<p>Problem reading files from directory: ' . $directory . '.';
            if(ENABLED_SAFEMODE)
            	echo '<br><b>You are running PHP with activated SAFE MODE which is considered a "broken" security measure. Contact us in the <a href="http://forum.bigace.de/" target="_blank">BIGACE Forum</a></b>.';
            echo '</p>';
        }
        return $filenames;
    }

    // nasty workaround to get installer script up and running with php 4 as well
    function array_combine_php4($arr1, $arr2) {

        if(function_exists('array_combine'))
            return array_combine($arr1, $arr2);

        $out = array();

        $arr1 = array_values($arr1);
        $arr2 = array_values($arr2);

        foreach($arr1 as $key1 => $value1) {
            $out[(string)$value1] = $arr2[$key1];
        }

        return $out;
    }

    function get_accept_browser_languages()
    {
	    $langs = array();
	    if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strlen($_SERVER['HTTP_ACCEPT_LANGUAGE']) > 0)
	    {
		    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			    // break up string into pieces (languages and q factors)
			    preg_match_all('/([a-z]{2}(-[a-z]{2})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);

			    if (count($lang_parse[1])) {
				    // create a list like "en" => 0.8
				    $langs = array_combine_php4($lang_parse[1], $lang_parse[4]);

				    // set default to 1 for any without q factor
				    foreach ($langs as $lang => $val) {
				        if ($val === '') $langs[$lang] = 1;
				    }

				    // sort list based on value
				    arsort($langs, SORT_NUMERIC);
			    }
		    }
	    }
	    return $langs;
    }


    // taken from system/admin/plugins/includes/updates_functions.php
    function unzip_simpleunzip($zipName, $extractTo)
    {
        $errors = array();

        $unzip = new SimpleUnzip($zipName);

        foreach($unzip->Entries as $oI)
        {
            if($oI->Error == 0)
            {
                $fullpath = $extractTo . $oI->Path . DIR_SEPARATOR;

                clearstatcache();

                if(!file_exists($fullpath))
                {
                    $paths = explode(ZIP_DIR_SEPARATOR,$oI->Path);
                    $last =  '';
                    foreach($paths AS $pathElement) {
                        $last .= $pathElement . DIR_SEPARATOR;
                        clearstatcache();
                        if(!file_exists($extractTo . $last))
                        {
                            if(!mkdir($extractTo.$last,DIRECTORY_PERMISSION))
                                $errors[] = 'Could not create directory: ' . $extractTo.$last;
                            // TODO: test with existing directorys and files
                            //if(!chmod($extractTo.$last, DIRECTORY_PERMISSION))
                            //    $errors[] = 'Could not change Access Rights: ' . $extractTo.$last;
                        }
                    }
                }

                $filename = $fullpath . $oI->Name;

                if($handle = @fopen($filename, 'wb')) {
                    if($oI->Data != null && $oI->Data != '') {
                        if(!@fwrite($handle, $oI->Data))
                            $errors[] = 'Problems writing to file: ' . $filename;
                    }
                    @fclose($handle);
                    if(defined('FILE_PERMISSION')) {
	                    // try to set proper access rights
	                    if(!@chmod($filename,FILE_PERMISSION))
	                        $errors[] = 'Problem setting permission for: ' . $filename;
                    }
                }
                else {
                    $errors[] = 'Failed to open file: ' . $filename;
                }
            }
            else {
                $errors[] = 'Problems extracting: ' . $oI->ErrorMsg . '('.$oI->Path.DIR_SEPARATOR.$oI->Name.')';
            }
        }
        return $errors;
    }

    function unzip_phpzip($zipName, $extractTo)
    {
        $errors = array();

        $zip = zip_open($zipName);
        if($zip === false)
            $errors[] = "Could not open ZIP file";

        if($zip !== false)
        {
            while($zipEntry = zip_read($zip))
            {
                $name = zip_entry_name($zipEntry);

                $fullpath = $extractTo . $name;

                $pos = strrpos($name, ZIP_DIR_SEPARATOR);
                $isDir = ($pos !== false && $pos == (strlen($name)-1));

                clearstatcache();
                if($isDir)
                {
                    if(!file_exists($fullpath)) {
                        $paths = explode(ZIP_DIR_SEPARATOR, $name);
                        $last =  '';
                        foreach($paths AS $pathElement) {
                            $last .= $pathElement . DIR_SEPARATOR;
                            clearstatcache();
                            if(!file_exists($extractTo . $last))
                            {
                                if(!mkdir($extractTo.$last,DIRECTORY_PERMISSION))
                                    $errors[] = 'Could not create directory: ' . $extractTo.$last;
                            }
                        }
                    }
                }
                else
                {
                    if(zip_entry_open($zip, $zipEntry, "r"))
                    {
                        $contents = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));

                        if($handle = @fopen($fullpath, 'wb')) {
                            if($contents != null && $contents != '') {
                                if(!@fwrite($handle, $contents))
                                    $errors[] = 'Failed to write file: ' . $fullpath;
                            }
                            @fclose($handle);
                            if(defined('FILE_PERMISSION')) {
	                            // try to set proper access rights
	                            if(!@chmod($fullpath,FILE_PERMISSION))
	                                $errors[] = 'Problem setting permission for: ' . $fullpath;
                            }
                        }
                        else {
                            $errors[] = 'Failed to open file: ' . $fullpath;
                        }
                        zip_entry_close($zipEntry);
                    }
                }
            }
            zip_close($zip);
        }

        return $errors;
    }

 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


    /**
     *  ZIP file unpack classes. Contributed to the phpMyAdmin project.
     *
     *  @category   phpPublic
     *  @package    File-Formats-ZIP
     *  @subpackage Unzip
     *  @filesource unzip.lib.php
     *  @version    1.0.1
     *
     *  @author     Holger Boskugel <vbwebprofi@gmx.de>
     *  @copyright  Copyright  2003, Holger Boskugel, Berlin, Germany
     *  @license    http://opensource.org/licenses/gpl-license.php GNU Public License
     *
     *  @history
     *  2003-12-02 - HB : Patched : naming bug : Time/Size of file
     *                    Added   : ZIP file comment
     *                    Added   : Check BZIP2 support of PHP
     *  2003-11-29 - HB * Initial version
     */

    /**
     *  Unzip class, which retrieves entries from ZIP files.
     *
     *  Supports only the compression modes
     *  -  0 : Stored,
     *  -  8 : Deflated and
     *  - 12 : BZIP2
     *
     *  Based on :<BR>
     *  <BR>
     *  {@link http://www.pkware.com/products/enterprise/white_papers/appnote.html
     *  * Official ZIP file format}<BR>
     *  {@link http://msdn.microsoft.com/library/en-us/w98ddk/hh/w98ddk/storage_5l4m.asp
     *  * Microsoft DOS date/time format}
     *
     *  @category   phpPublic
     *  @package    File-Formats-ZIP
     *  @subpackage Unzip
     *  @version    1.0.1
     *  @author     Holger Boskugel <vbwebprofi@gmx.de>
     *  @uses       SimpleUnzipEntry
     *  @example    example.unzip.php Two examples
     */
    class SimpleUnzip {
// 2003-12-02 - HB >
        /**
         *  Array to store file entries
         *
         *  @var    string
         *  @access public
         *  @see    ReadFile()
         *  @since  1.0.1
         */
        var $Comment = '';
// 2003-12-02 - HB <

        /**
         *  Array to store file entries
         *
         *  @var    array
         *  @access public
         *  @see    ReadFile()
         *  @since  1.0
         */
        var $Entries = array();

        /**
         *  Name of the ZIP file
         *
         *  @var    string
         *  @access public
         *  @see    ReadFile()
         *  @since  1.0
         */
        var $Name = '';

        /**
         *  Size of the ZIP file
         *
         *  @var    integer
         *  @access public
         *  @see    ReadFile()
         *  @since  1.0
         */
        var $Size = 0;

        /**
         *  Time of the ZIP file (unix timestamp)
         *
         *  @var    integer
         *  @access public
         *  @see    ReadFile()
         *  @since  1.0
         */
        var $Time = 0;

        /**
         *  Contructor of the class
         *
         *  @param  string      File name
         *  @return SimpleUnzip Instanced class
         *  @access public
         *  @uses   SimpleUnzip::ReadFile() Opens file on new if specified
         *  @since  1.0
         */
        function SimpleUnzip($in_FileName = '') {
            if($in_FileName !== '') {
                SimpleUnzip::ReadFile($in_FileName);
            }
        } // end of the 'SimpleUnzip' constructor

        /**
         *  Counts the entries
         *
         *  @return integer Count of ZIP entries
         *  @access public
         *  @uses   $Entries
         *  @since  1.0
         */
        function Count() {
            return count($this->Entries);
        } // end of the 'Count()' method

        /**
         *  Gets data of the specified ZIP entry
         *
         *  @param  integer Index of the ZIP entry
         *  @return mixed   Data for the ZIP entry
         *  @uses   SimpleUnzipEntry::$Data
         *  @access public
         *  @since  1.0
         */
        function GetData($in_Index) {
            return $this->Entries[$in_Index]->Data;
        } // end of the 'GetData()' method

        /**
         *  Gets an entry of the ZIP file
         *
         *  @param  integer             Index of the ZIP entry
         *  @return SimpleUnzipEntry    Entry of the ZIP file
         *  @uses   $Entries
         *  @access public
         *  @since  1.0
         */
        function GetEntry($in_Index) {
            return $this->Entries[$in_Index];
        } // end of the 'GetEntry()' method

        /**
         *  Gets error code for the specified ZIP entry
         *
         *  @param  integer     Index of the ZIP entry
         *  @return integer     Error code for the ZIP entry
         *  @uses   SimpleUnzipEntry::$Error
         *  @access public
         *  @since   1.0
         */
        function GetError($in_Index) {
            return $this->Entries[$in_Index]->Error;
        } // end of the 'GetError()' method

        /**
         *  Gets error message for the specified ZIP entry
         *
         *  @param  integer     Index of the ZIP entry
         *  @return string      Error message for the ZIP entry
         *  @uses   SimpleUnzipEntry::$ErrorMsg
         *  @access public
         *  @since  1.0
         */
        function GetErrorMsg($in_Index) {
            return $this->Entries[$in_Index]->ErrorMsg;
        } // end of the 'GetErrorMsg()' method

        /**
         *  Gets file name for the specified ZIP entry
         *
         *  @param  integer     Index of the ZIP entry
         *  @return string      File name for the ZIP entry
         *  @uses   SimpleUnzipEntry::$Name
         *  @access public
         *  @since  1.0
         */
        function GetName($in_Index) {
            return $this->Entries[$in_Index]->Name;
        } // end of the 'GetName()' method

        /**
         *  Gets path of the file for the specified ZIP entry
         *
         *  @param  integer     Index of the ZIP entry
         *  @return string      Path of the file for the ZIP entry
         *  @uses   SimpleUnzipEntry::$Path
         *  @access publicdiv
         *  @since  1.0
         */
        function GetPath($in_Index) {
            return $this->Entries[$in_Index]->Path;
        } // end of the 'GetPath()' method

        /**
         *  Gets file time for the specified ZIP entry
         *
         *  @param  integer     Index of the ZIP entry
         *  @return integer     File time for the ZIP entry (unix timestamp)
         *  @uses   SimpleUnzipEntry::$Time
         *  @access public
         *  @since  1.0
         */
        function GetTime($in_Index) {
            return $this->Entries[$in_Index]->Time;
        } // end of the 'GetTime()' method

        /**
         *  Reads ZIP file and extracts the entries
         *
         *  @param  string              File name of the ZIP archive
         *  @return array               ZIP entry list (see also class variable {@link $Entries $Entries})
         *  @uses   SimpleUnzipEntry    For the entries
         *  @access public
         *  @since  1.0
         */
        function ReadFile($in_FileName) {
            $this->Entries = array();

            // Get file parameters
            $this->Name = $in_FileName;
            $this->Time = filemtime($in_FileName);
            $this->Size = filesize($in_FileName);

            // Read file
            $oF = fopen($in_FileName, 'rb');
            $vZ = fread($oF, $this->Size);
            fclose($oF);

// 2003-12-02 - HB >
            // Cut end of central directory
            $aE = explode("\x50\x4b\x05\x06", $vZ);

            // Easiest way, but not sure if format changes
            //$this->Comment = substr($aE[1], 18);

            // Normal way
            $aP = unpack('x16/v1CL', $aE[1]);
            $this->Comment = substr($aE[1], 18, $aP['CL']);

            // Translates end of line from other operating systems
            $this->Comment = strtr($this->Comment, array("\r\n" => "\n",
                                                         "\r"   => "\n"));
// 2003-12-02 - HB <

            // Cut the entries from the central directory
            $aE = explode("\x50\x4b\x01\x02", $vZ);
            // Explode to each part
            $aE = explode("\x50\x4b\x03\x04", $aE[0]);
            // Shift out spanning signature or empty entry
            array_shift($aE);

            // Loop through the entries
            foreach($aE as $vZ) {
                $aI = array();
                $aI['E']  = 0;
                $aI['EM'] = '';
                // Retrieving local file header information
                $aP = unpack('v1VN/v1GPF/v1CM/v1FT/v1FD/V1CRC/V1CS/V1UCS/v1FNL', $vZ);
                // Check if data is encrypted
                $bE = ($aP['GPF'] && 0x0001) ? TRUE : FALSE;
                $nF = $aP['FNL'];

                // Special case : value block after the compressed data
                if($aP['GPF'] & 0x0008) {
                    $aP1 = unpack('V1CRC/V1CS/V1UCS', substr($vZ, -12));

                    $aP['CRC'] = $aP1['CRC'];
                    $aP['CS']  = $aP1['CS'];
                    $aP['UCS'] = $aP1['UCS'];

                    $vZ = substr($vZ, 0, -12);
                }

                // Getting stored filename
                $aI['N'] = substr($vZ, 26, $nF);

                if(substr($aI['N'], -1) == '/') {
                    // is a directory entry - will be skipped
                    continue;
                }

                // Truncate full filename in path and filename
                $aI['P'] = dirname($aI['N']);
                $aI['P'] = $aI['P'] == '.' ? '' : $aI['P'];
                $aI['N'] = basename($aI['N']);

                $vZ = substr($vZ, 26 + $nF);

                if(strlen($vZ) != $aP['CS']) {
                  $aI['E']  = 1;
                  $aI['EM'] = 'Compressed size is not equal with the value in header information.';
                }
                else {
                    if($bE) {
                        $aI['E']  = 5;
                        $aI['EM'] = 'File is encrypted, which is not supported from this class.';
                    }
                    else {
                        switch($aP['CM']) {
                            case 0: // Stored
                                // Here is nothing to do, the file ist flat.
                                break;

                            case 8: // Deflated
                                $vZ = gzinflate($vZ);
                                break;

                            case 12: // BZIP2
// 2003-12-02 - HB >
                                if(! extension_loaded('bz2')) {
                                    if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                                      @dl('php_bz2.dll');
                                    }
                                    else {
                                      @dl('bz2.so');
                                    }
                                }

                                if(extension_loaded('bz2')) {
// 2003-12-02 - HB <
                                    $vZ = bzdecompress($vZ);
// 2003-12-02 - HB >
                                }
                                else {
                                    $aI['E']  = 7;
                                    $aI['EM'] = "PHP BZIP2 extension not available.";
                                }
// 2003-12-02 - HB <

                                break;

                            default:
                              $aI['E']  = 6;
                              $aI['EM'] = "De-/Compression method {$aP['CM']} is not supported.";
                        }

// 2003-12-02 - HB >
                        if(! $aI['E']) {
// 2003-12-02 - HB <
                            if($vZ === FALSE) {
                                $aI['E']  = 2;
                                $aI['EM'] = 'Decompression of data failed.';
                            }
                            else {
                                if(strlen($vZ) != $aP['UCS']) {
                                    $aI['E']  = 3;
                                    $aI['EM'] = 'Uncompressed size is not equal with the value in header information.';
                                }
                                else {
                                    if(crc32($vZ) != $aP['CRC']) {
                                        $aI['E']  = 4;
                                        $aI['EM'] = 'CRC32 checksum is not equal with the value in header information.';
                                    }
                                }
                            }
// 2003-12-02 - HB >
                        }
// 2003-12-02 - HB <
                    }
                }

                $aI['D'] = $vZ;

                // DOS to UNIX timestamp
                $aI['T'] = mktime(($aP['FT']  & 0xf800) >> 11,
                                  ($aP['FT']  & 0x07e0) >>  5,
                                  ($aP['FT']  & 0x001f) <<  1,
                                  ($aP['FD']  & 0x01e0) >>  5,
                                  ($aP['FD']  & 0x001f),
                                  (($aP['FD'] & 0xfe00) >>  9) + 1980);

                $this->Entries[] = &new SimpleUnzipEntry($aI);
            } // end for each entries

            return $this->Entries;
        } // end of the 'ReadFile()' method
    } // end of the 'SimpleUnzip' class

    /**
     *  Entry of the ZIP file.
     *
     *  @category   phpPublic
     *  @package    File-Formats-ZIP
     *  @subpackage Unzip
     *  @version    1.0
     *  @author     Holger Boskugel <vbwebprofi@gmx.de>
     *  @example    example.unzip.php Two examples
     */
    class SimpleUnzipEntry {
        /**
         *  Data of the file entry
         *
         *  @var    mixed
         *  @access public
         *  @see    SimpleUnzipEntry()
         *  @since  1.0
         */
        var $Data = '';

        /**
         *  Error of the file entry
         *
         *  - 0 : No error raised.<BR>
         *  - 1 : Compressed size is not equal with the value in header information.<BR>
         *  - 2 : Decompression of data failed.<BR>
         *  - 3 : Uncompressed size is not equal with the value in header information.<BR>
         *  - 4 : CRC32 checksum is not equal with the value in header information.<BR>
         *  - 5 : File is encrypted, which is not supported from this class.<BR>
         *  - 6 : De-/Compression method ... is not supported.<BR>
         *  - 7 : PHP BZIP2 extension not available.
         *
         *  @var    integer
         *  @access public
         *  @see    SimpleUnzipEntry()
         *  @since  1.0
         */
        var $Error = 0;

        /**
         *  Error message of the file entry
         *
         *  @var    string
         *  @access public
         *  @see    SimpleUnzipEntry()
         *  @since  1.0
         */
        var $ErrorMsg = '';

        /**
         *  File name of the file entry
         *
         *  @var    string
         *  @access public
         *  @see    SimpleUnzipEntry()
         *  @since  1.0
         */
        var $Name = '';

        /**bigace_install_2.5_RC3.zip
         *  File path of the file entry
         *
         *  @var    string
         *  @access public
         *  @see    SimpleUnzipEntry()
         *  @since  1.0
         */
        var $Path = '';

        /**
         *  File time of the file entry (unix timestamp)
         *
         *  @var    integer
         *  @access public
         *  @see    SimpleUnzipEntry()
         *  @since  1.0
         */
        var $Time = 0;

        /**
         *  Contructor of the class
         *
         *  @param  array               Entry datas
         *  @return SimpleUnzipEntry    Instanced class
         *  @access public
         *  @since  1.0
         */
        function SimpleUnzipEntry($in_Entry) {
            $this->Data     = $in_Entry['D'];
            $this->Error    = $in_Entry['E'];
            $this->ErrorMsg = $in_Entry['EM'];
            $this->Name     = $in_Entry['N'];
            $this->Path     = $in_Entry['P'];
            $this->Time     = $in_Entry['T'];
        } // end of the 'SimpleUnzipEntry' constructor
    } // end of the 'SimpleUnzipEntry' class

?>