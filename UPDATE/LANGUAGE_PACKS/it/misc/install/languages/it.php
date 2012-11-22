<?php
/**
* Translation file for the installer.
* Language: Italian
*
* Copyright (C) Bigace Community.
*
* For further information go to {@link http://www.bigace.de http://www.bigace.de}.
*
* @version $Id$
* @author Fabrizio Lazzeretti
*/

if(!defined('_BIGACE_INSTALL_PARENT_')) {
    die('Not runnable alone, go to '.dirname(__FILE__).'/index.php');
}

$LANG['title'] = 'BIGACE '._BIGACE_ID;
$LANG['intro'] = '... gestisce facilmente i vostri Contenuti!';
$LANG['thanks'] = 'Grazie per aver installato il CMS BIGACE!';

// -----------------------------------------------------------------------------
// Navigation
$LANG['menu_title'] = 'Installazione Sistemi';
$LANG['menu_step_2'] = 'Controlla le impostazioni';
$LANG['menu_step_3'] = 'Installazione';
$LANG['menu_step_4'] = 'Crea una Community';
$LANG['menu_step_5'] = 'Installazione riuscita';
$LANG['menu_step'] = 'Passo';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Welcome Screen
$LANG['install_state'] = 'Status';
$LANG['install_begin'] = 'Inizia Installazione';
$LANG['introduction'] = 'Introduczione';
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// Form Tooltip
$LANG['form_tip_close'] = 'Chiudi';
$LANG['form_tip_hide'] = "Non visualizzare più questo messaggio";
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// LANGUAGES - chooser and names for languages
$LANG['language_choose'] = 'Scelta della lingua';
$LANG['language_text'] = 'Scegli la lingua che viene utilizzata durante il processo di installazione.';
$LANG['language_button'] = 'Cambia lingua';
// -----------------------------------------------------------------------------

$LANG['failure'] = 'Ci sono stati errori';
$LANG['new'] = 'Nuovo';
$LANG['old'] = 'Vecchio';
$LANG['successfull'] = 'Successo';
$LANG['main_menu'] = 'Menu Principale';
$LANG['back'] = 'Indietro';
$LANG['next'] = 'Avanti';
$LANG['state_no_db'] = 'Il database sembra non essere installato!';
$LANG['state_not_all_db'] = 'L\'installazione del database sembra essere incompleta!';
$LANG['state_installed'] = 'Core-System installato con successo!';
$LANG['help_title'] = 'Aiuto';
$LANG['help_text'] = 'Per ulteriori informazioni per ogni passaggio, spostare il mouse sopra l\'icona della Guida dietro ogni campo di input. Un messaggio breve informazione verrà visualizzata. <br> Ad esempio spostare il mouse sopra l\'icona seguente:';
$LANG['help_demo'] = 'Hai trovato il modo giusto di vedere il tuo Aiuto-Info!';
$LANG['db_install'] = 'Installa CMS';
$LANG['cid_install'] = 'Impstazioni Sito Web';
$LANG['install_finish'] = 'Installazione Completa';
$LANG['db_install_state'] = 'Stato: ';
$LANG['db_install_help'] = 'Questo passaggio installa il Core System del CMS. Configura la connessione al database e alcune impostazioni per il tuo primo sito (Community)';

//-----------------------------------------------------------
// Translation for the System installation Dialog
$LANG['db_value_title'] = 'Connessione al Database';
$LANG['ext_value_title'] = 'Configurazione del Sistema';
$LANG['db_type'] = 'Tipo di Database';
$LANG['db_host'] = 'Server/Host';
$LANG['db_database'] = 'Database';
$LANG['db_user'] = 'Utente';
$LANG['db_password'] = 'Password';
$LANG['db_prefix'] = 'Prefisso Tabelle';
$LANG['mod_rewrite'] = 'Apache MOD-Rewrite';
$LANG['mod_rewrite_yes'] = 'Modul active / Usage possible (.htaccess)';
$LANG['mod_rewrite_no'] = 'Non possibile / Non Conosco';
$LANG['base_dir'] = 'Cartella di Base';
// Translation for the System Installation Help Images
$LANG['base_dir_help'] = 'Immettere la directory di installazione principale. Lascia vuoto se installato nella cartella principale o root dir (http://www.example.com/). valore <br> aggiornamento automatico calcolato dovrebbe essere corretto! </ b> <br> Il percorso non deve iniziare con /, ma finire con /. Per l\'installazione ad esempio in "http://www.example.com/cms/", il valore "cms" <b> / </ b> è corretto.';
$LANG['mod_rewrite_help'] = '<b>Questa impostazione consente URL amichevoli! </ b><br/> Assicurati di scegliere la giusta impostazione. Se si sceglie di utilizzo possibile, senza Supporto di Riscrittura, il sistema potrebbe non essere consultabile. Questa impostazione è configurabile tramite una voce Config. Se non sei sicuro lascia questa impostazione come è!';
$LANG['db_password'] = 'Password';
$LANG['def_language'] = 'Lingua Predefinita';
$LANG['def_language_help'] = 'Scegli la lingua predefinita per il CMS.';
$LANG['db_type_help'] = 'Scegli il tipo di database che intendi utilizzare. <br> L\'installazione supporta tutti i database elencati, ma il cuore del sistema <b> attualmente supporta solo MySQL </ b> al 100%. <br> Se decidi di utilizzare un database diverso da MySQL, è a tuo rischio!';
$LANG['db_host_help'] = 'Immettere il nome del server dove è installato il database (prova ad usare <b> localhost</b>, che spesso funziona!).';
$LANG['db_database_help'] = 'Inserisci il nome del database (ad esempio lo stesso che vedete nel riquadro di sinistra di phpMyAdmin).';
$LANG['db_user_help'] = 'Inserire il nome utente che ha permesso di scrittura per il database.';
$LANG['db_prefix_help'] = 'Inserisci il prefisso per le tabelle del database BIGACE. Usando un nome univoco, saranno sempre direttamente identificabili. Se non si capisce il significato di questo, utilizzare il valore di default.';
$LANG['db_password_help'] = 'Immettere la password per l\'utente di sopra iscritto.';
$LANG['db_already_exists'] = 'La banca dati sembra essere installato correttamente, ometto questo step di installazione.';

$LANG['htaccess_security'] = 'Apache .htaccess Feature';
$LANG['htaccess_security_yes'] = 'Consenti l\'override attivo (.htaccess)';
$LANG['htaccess_security_no'] = 'Non possibile / Non Conosco';
$LANG['htaccess_security_help']	= '<b>Si tratta di una impostazione per la sicurezza dei tuoi dati! </ B> <br/> Assicurati che il tuo server consente <b>Override tutto</ b>. Htaccess. Se non sei sicuro lascia questa impostazione, come è!';
//-----------------------------------------------------------

// Translation for Consumer Installation
// First Dialog
$LANG['error_enter_domain'] = 'Inserisci un dominio corretto, dove la nuova community sarà disponibile.';
$LANG['error_enter_adminuser'] = 'Si prega di inserire un nome per il nuovo account Administrator (almeno 4 caratteri).';
$LANG['error_enter_adminpass'] = 'Si prega di inserire una password per il nuovo account Administrator (almeno 6 caratteri) e verifica sotto.';

$LANG['cid_check_failed'] = 'Controllo dei tuoi permessi dei file non riuscito! Devi correggerli prima di continuare!';
$LANG['cid_domain'] = 'Dominio della Community';
$LANG['cid_id_help'] = 'Scegli l\'ID per la nuova comunità (che saranno utilizzati solo interno). Se avete intenzione di migrare un systema esistente, l\'idea migliore è quella di scegliere l\'ID del tua vecchia Community.';
$LANG['cid_domain_help'] = 'Immettere il nome dominio, che sarà associato alla nuova Community. Il valore rilevato automaticamente dovrebbe essere corretto. <br> <b> NOTA: NON IMMETTERE UN PERCORSO O UNA SLASH IN CODA!</b>';

$LANG['statistics'] = 'Statistiche';
$LANG['statistics_on'] = 'Attiva statistiche';
$LANG['statistics_off'] = 'Disattiva statistiche';
$LANG['statistics_help'] = 'Scegliere se si desidera attivare le statistiche o meno. Se le attivi, per ogni pagina chiamata verrà eseguita una chiamata di scrittura al Database .';
$LANG['sitename'] = 'Nome del Sito';
$LANG['sitename_help'] = 'Inserisci il nome o il titolo della pagina. Questo valore può essere utilizzato in Modelli e facilmente essere cambiato nell\'Amministrazione.';
$LANG['mailserver'] = 'Server di Posta';
$LANG['mailserver_help'] = 'Inserisci la tua Server di Posta (mail.yourdomain.com), che invierà le tue email. Lasciare vuoto se si usa il proxy di default di PHP (per i sistemi più comuni).';
$LANG['webmastermail'] = 'Indirizzo Email';
$LANG['webmastermail_help'] = "Inserisci l'indirizzo email per l'account amministratore della tua nuova community.";
$LANG['bigace_admin'] = 'Nome utente';
$LANG['bigace_password'] = 'Password';
$LANG['bigace_check'] = 'Password [ri-digita]';
$LANG['bigace_admin_help'] = 'Immettere il nome utente per l\'account di amministratore. Questo amministratore gestirà tutte le autorizzazioni per gli oggetti e le funzioni amministrative.';
$LANG['bigace_password_help'] = 'Inserisci la password per l\'account di amministratore.';
$LANG['bigace_check_help'] = 'Si prega di verificare la password scelta. Se le password inserite non corrispondono, si tornerà qui.';
$LANG['create_files'] = 'Creazione Filesystem';
$LANG['save_cconfig'] = 'Salva Configurazione della Community';
$LANG['added_consumer'] = 'Community Aggiunta';
$LANG['added_consumer'] = 'Aggiunta Community esistente';
$LANG['community_exists'] = 'Il consumatore è già esistente per il dato dominio, immettere un diverso dominio.';

$LANG['check_reload'] = 'Esegui pre-check di nuovo';
$LANG['check_up']               = 'Pre-Check';
$LANG['check_up_help'] = 'ATTENZIONE: Si prega di effettuare il pre-check, prima di iniziare l\'installazione BIGACE o aggiungere una nuova Community!';

$LANG['required_empty_dirs'] = 'Cartelle Richieste';
$LANG['empty_dirs_description'] = 'Le Cartelle seguenti sono richiesti dalla BIGACE, ma non può essere creata automaticamente. Si prega di crearle manualmente:';
$LANG['check_yes'] = 'Si';
$LANG['check_no'] = 'No';
$LANG['check_on'] = 'Acceso';
$LANG['check_off'] = 'Spento';
$LANG['check_status'] = 'Stato';
$LANG['check_setting'] = 'Impostazione';
$LANG['check_recommended'] = 'Consigliato';
$LANG['check_install_help'] = 'Se uno dei flag è colorato di rosso, si deve aggiustare / correggere la configurazione Apache e PHP. Se non lo fai, probabilmente può risultare in una installazione corrotta.';
$LANG['required_settings_title']= 'Impostazioni Necessarie';
$LANG['check_settings_title'] = 'Impostazioni Necessarie';
$LANG['check_settings_help'] = 'Le seguenti impostazioni PHP sono consigliate, per offrire un lavoro regolare BIGACE. <br> Il CMS dovrebbe funzionare anche se alcune delle impostazioni non corrispondono. Tuttavia si consiglia, di risolvere qualsiasi problema, prima di procedere con l\'installazione.';
$LANG['check_files_title'] = 'Cartella - e Permessi ai File';
$LANG['check_files_help'] = 'Per un funzionamento corretto, BIGACE esige permessi di scrittura per le seguenti directory e file. Se vedi un punto rosso, è necessario sistemare l\'autorizzazione prima di continuare.';

$LANG['config_consumer'] = 'Impostazioni della Community';
$LANG['config_admin'] = 'Account Amministratore';

$LANG['community_install_good'] = '
<p>Congratulazioni, il processo di installazione è completo! </ p>
<p> Se in qualsiasi momento avete bisogno di sostegno, o BIGACE non riesce a funzionare correttamente, ricordate che aiutano <a href="http://forum.bigace.de" target="_blank"> è disponibile </ a> se ne avete bisogno.
<p> La directory di installazione è ancora esistente. È una buona idea  rimuovere del tutto questa cartella per motivi di sicurezza. </ p>
<p> Ora potete vedere il tuo <a href="../../"> sito appena installato </ a> e cominciare a usarlo. È necessario assicurarsi aver effettuato l\'accesso, dopo di che sarete in grado di accedere al centro di amministrazione. </ p>
<br />
<p> Buona fortuna! </ p>
<br /> <br />
<p> href="../../"> Visita il tuo nuovo sito web</a></p>';

$LANG['community_install_bad'] 	= 'Verificati dei problemi durante l\'installazione.';
$LANG['community_install_infos']= 'Visualizza Messagi di Sistema...';

$LANG['error_db_connect'] = 'Impossibile connettersi al Host del database';
$LANG['error_db_select'] = 'Impossibile selezionare Database';
$LANG['error_db_create'] = 'Impossibile creare il database.';
$LANG['error_read_dir'] = 'Impossibile leggere la directory';
$LANG['error_created_dir'] = 'Impossibile creare la directory';
$LANG['error_removed_dir'] = 'Impossibile eliminare Directory';
$LANG['error_copied_file'] = 'Impossibile copiare il file';
$LANG['error_remove_file'] = 'Impossibile eliminare il file';
$LANG['error_close_file'] = 'Impossibile chiudere il file';
$LANG['error_open_file'] = 'Errore: Impossibile aprire il file';
$LANG['error_db_statement'] = 'Errore nel DB Statement';
$LANG['error_open_cconfig'] = 'Impossibile aprire il file di configurazione della Community';
$LANG['error_double_cconfig'] = 'Errore: Community già esistente!';
$LANG['could_not_find_consumer'] = 'Errore: Impossibile trovare Community';
