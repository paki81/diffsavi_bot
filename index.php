<?php
include 'Telegram.php';

//include 'feste.php';

date_default_timezone_set('Europe/Rome');

// CALCOLO FESTIVITA
    //PRENDO IN INPUT L'ANNO DA ELABORARE. 
    //SE VUOTO PRENDO L'ANNO IN CORSO
    $anno_input=$_GET['anno_in']!=''?$_GET['anno_in']:date("Y");
    
    //FUNZIONE PER LA RICERCA DI UNA DATA NELLA LISTA DELLE FESTIVITA'
    function inFestivita($data, $dateFestivi){
        foreach($dateFestivi as $d=>$v){
    	    if($d==$data)
                return true;
        }
        return false;
    }
    //INIZIALIZZIAMO LE FESTIVITA' CON LA LORO ETICHETTA.
    $feste = Array(
        "01-01"=>"Capodanno",
        "06-01"=>"Epifania", 
        "25-04"=>"Liberazione", 
        "01-05"=>"Festa Lavoratori", 
        "02-06"=>"Festa della Repubblica", 
        "15-08"=>"Ferragosto", 
        "01-11"=>"Tutti Santi", 
        "08-12"=>"Immacolata", 
        "25-12"=>"Natale", 
        "26-12"=>"St. Stefano"); 
		
    $giorni = array('Domenica','Lunedi','Martedi','Mercoledi','Giovedi','Venerdi','Sabato');
	
    //ARRAY FINALE CON LE DATE DEI FESTIVI
    $festivita=array();
  
    $start=strtotime("$anno_input-01-01");
    $stop=strtotime("$anno_input-12-31");

    $day=$start;
    while($day <= $stop) {
        list($ita_date,$confronto_festivo,$anno,$giorno_settimana)=explode('__',date('d-M-Y__d-m__Y__w',$day));
        //SE GIORNO FESTIVO LO INSERISCO NELLA NOSTRA LISTA.
        if (inFestivita($confronto_festivo,$feste)) {      
            $festivita[]=$ita_date;
        } else {
            //CONTROLLO CHE NON SIA PASQUETTA.
            $march21=date("$anno-03-21");
            $gPasquetta=easter_days($anno)+1;
            $dataPasquetta = date("d-M-Y",strtotime(date("Y-m-d", strtotime($march21)) . " +$gPasquetta day"));
            if($ita_date==$dataPasquetta) {
                $festivita[]=$ita_date;
            }
        }
        //VADO AVANTI DI UN GIORNO
        $day+=86400;
    }



//FINE CALCOLO FESTIVITA


// Imposta TOKEN Telegram
$bot_token = 'XXXXXXXXXXX';
// Instances the class
$telegram = new Telegram($bot_token);
/* If you need to manually take some parameters
*  $result = $telegram->getData();
*  $text = $result["message"] ["text"];
*  $chat_id = $result["message"] ["chat"]["id"];
*/
// Take text and chat_id from the message
$text = $telegram->Text();
$chat_id = $telegram->ChatID();
$firstname = $telegram->FirstName();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Connessione DB
$servername = "XXXXXX";
$username = "XXXXXX";
$password = "XXXXX";
$dbname = "XXXXXXX";
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    echo "<p>DB connection error.</p>";
}else{
    echo "<p>DB connection OK.</p>";
}

//N.B. RIPORTARE I DATI DB ANCHE IN FONDO ALLA SEZIONE NOTIFICHE, NELLA SEZIONE /start qui sotto e nel file push.php
//Fine Connessione DB

// QUESTA PARTE APPARE SOLO ALLA PRIMA ATTIVAZIONE DEL BOT
if ($text == '/start'){
    $option = [['🗑️ Che rifiuti posso buttare oggi?'],['📅 Calendario','📄 Materiali'], ['ℹ️ Informazioni utili','📬 Notifiche'], ['Savignano Irpino non è la tua città?'], ['Crediti']];
    // Create a permanent custom keyboard
    $keyb = $telegram->buildKeyBoard($option, $onetime = false);
    $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb,'parse_mode' => 'markdown', 'text' => "♻️ Ciao ".$firstname.", sono *Diffy*!\n\nTi aiuterò con la raccolta differenziata di [Savignano Irpino]!\n\n_Come posso aiutarti?_"];
    $telegram->sendMessage($content);

    //Memorizza chatID in DB
    //SOSTITUIRE differenziatabot CON IL NOME DEL DB SCELTO
    $sql = "INSERT INTO differenziatabot (id_utente,attivo) VALUES ($chat_id, '1')";
    //IN AUTOMATICO SARANNO ATTIVE LE NOTIFICHE PER TUTTI. CAMBIARE IL VALORE 1 in 0 QUI SOPRA PER RENDERE DISATTIVE DI DEFAULT
    if (mysqli_query($conn, $sql)) {
    echo "New record created successfully";
    } else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
    //Fine Memorizza chatID in DB
}
//FINE /start

//MENU PRINCIPALE
if ($text == 'Menu Principale'){
    $option = [['🗑️ Che rifiuti posso buttare oggi?'],['📅 Calendario','📄 Materiali'], ['ℹ️ Informazioni utili','📬 Notifiche'], ['Savignano Irpino non è la tua città?'], ['Crediti']];
    // Create a permanent custom keyboard
    $keyb = $telegram->buildKeyBoard($option, $onetime = false);
    $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb,'parse_mode' => 'markdown', 'text' => "♻️ Ciao ".$firstname.", sono *Diffy*!\n\n_Come posso aiutarti oggi?_"];
    $telegram->sendMessage($content);
}
//FINE MENU PRINCIPALE

//CALENDARIO
if ($text == '📅 Calendario') {
  $option = [['Lunedì'], ['Martedì'], ['Mercoledì'], ['Giovedì'], ['Venerdì'], ['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Seleziona il giorno della settimana"];
  $telegram->sendMessage($content);
}

//Giorni della settimana

//Se venissero apportate modifice, riportare le stesse anche sul file push.php
if ($text == 'Lunedì') {
  $option = [['🍗  Umido','📦 Carta, Cartone e Cartoncino'], ['📅 Calendario'],['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'parse_mode' => 'markdown', 'text' => "*Lunedì*\n\n_Puoi portare fuori:\n\n_🍗 *Umido*\n\n*Esposizione*\ndalle ore 08:00 alle 12:00\n\nIl secondo e il quarto Lunedì del mese puoi anche portare fuori:\n\n📦 *Carta, Cartone e Cartoncino*\n\n*Esposizione*\n_dalle ore 12:00 alle ore 18:00_"];
  $telegram->sendMessage($content);
}
if ($text == 'Martedì') {
  $option = [['💡 Indifferenziato'], ['📅 Calendario'],['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'parse_mode' => 'markdown', 'text' => "*Martedì*\n\n💡  *Indifferenziato*\n\n*Esposizione*\n_dalle ore 12:00 alle 18:00_"];
  $telegram->sendMessage($content);
}
if ($text == 'Mercoledì') {
  $option = [['🎈🥫 Plastica e Metalli'], ['📅 Calendario'],['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'parse_mode' => 'markdown', 'text' => "Mercoledì\n\n🎈🥫 *Plastica e Metalli*\n\nIl ritiro della plastica e dei metalli come lattine, alluminio e acciaio è previsto il 2° e il 4° mercoledì di ogni mese.\n\n*Esposizione*\n_dalle ore 12:00 alle 18.00_"];
  $telegram->sendMessage($content);
}
if ($text == 'Giovedì') {
  $option = [['ℹ️ Informazioni utili'], ['📅 Calendario'],['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'parse_mode' => 'markdown', 'text' => "*Giovedì*\n\n*Nessun Ritiro*\n\n*Esposizione*\n_non c'è raccolta differenziata porta a porta._"];
  $telegram->sendMessage($content);
}
if ($text == 'Venerdì') {
  $option = [['🍗  Umido','🍷 Vetro'], ['📅 Calendario'],['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'parse_mode' => 'markdown', 'text' => "*Venerdì*\n\n_Puoi portare fuori:\n\n_🍗 *Umido*\n\n*Esposizione*\ndalle ore 08:00 alle 12:00\n\nIl secondo e il quarto Venerdì del mese puoi anche portare fuori:\n\n🍷 *Vetro*\n\n*Esposizione*\n_dalle ore 12:00 alle ore 18:00_"];
  $telegram->sendMessage($content);
}
if ($text == 'Sabato') {
  $option = [['ℹ️ Informazioni utili'], ['📅 Calendario'],['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'parse_mode' => 'markdown', 'text' => "*Sabato*\n\n*Nessun Ritiro*\n\n*Esposizione*\n_non c'è raccolta differenziata porta a porta._"];
  $telegram->sendMessage($content);
}
if ($text == 'Domenica') {
  $option = [['ℹ️ Informazioni utili'], ['📅 Calendario'],['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'parse_mode' => 'markdown', 'text' => "*Domenica*\n\n*Nessun Ritiro*\n\n*Esposizione*\n_non c'è raccolta differenziata porta a porta._"];
  $telegram->sendMessage($content);
}
//FINE CALENDARIO

//MATERIALI
if ($text == '📄 Materiali') {
  $option = [['📦 Carta, Cartone e Cartoncino','🎈🥫 Plastica e Metalli'], ['🍷 Vetro', '🍗  Umido','💡 Indifferenziato'],['🗑️ Altri rifiuti'],['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "Seleziona il materiale"];
  $telegram->sendMessage($content);
}
if ($text == '📦 Carta, Cartone e Cartoncino') {
  //DESCRIZIONE
    $reply = "\xF0\x9F\x93\xA6 Carta, Cartone";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
  //OK
    $reply = "\xE2\x9C\x85 AMMESSI \nSacchetti di carta\nScatole\nImballaggi di cartone e cartoncino\nCarta da pacchi pulita\nCartoni per bevande e prodotti alimentari\nGiornali\nRiviste\nQuaderni";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
    //NO
    $reply = "\xE2\x9D\x8C NON AMMESSI\nCarta sporca\nFazzolettini e tovaglioli\nCartoni della pizza sporchi\nScontrini fiscali di carta termica\nCarta chimica per fax\nCarta oleata\nCarta plastificata";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
}
if ($text == '🎈🥫 Plastica e Metalli') {
  //DESCRIZIONE
    $reply = "🎈🥫 Plastica e Metalli";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
  //OK
    $reply = "\xE2\x9C\x85 AMMESSI\nBottiglie e flaconi di plastica\nBuste e pellicole in plastica\nVaschette e vasetti di yogurth in plastica\nPiatti e bicchieri in plastica\nBombolette spray (vuote)\nTubetti, lattine e vaschette in alluminio\nFogli sottili, scatolette, barattoli e altri contenitori metallici\nTappi a corona, chiusure e coperchi\nLatte per olio";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
    //NO
    $reply = "\xE2\x9D\x8C NON AMMESSI\nPosate di plastica\nGiocattoli\nPenne e pennarelli\nSpazzolini da denti\nOggetti in metallo\nPentole e posate\nFil di ferro";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
}
if ($text == '🍷 Vetro') {
  //DESCRIZIONE
    $reply = "\xF0\x9F\x8D\xB7 Vetro";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
  //OK
    $reply = "\xE2\x9C\x85 AMMESSI\nBottiglie\nVasetti";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
    //NO
    $reply = "\xE2\x9D\x8C NON AMMESSI\nSpecchi\nCeramica\nPorcellana\nLampadine\nNeon\nLastre di vetro";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
}
if ($text == '💡 Indifferenziato') {
  //DESCRIZIONE
    $reply = "💡 Indifferenziato";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
  //OK
    $reply = "\xE2\x9C\x85 AMMESSI\nPosate di plastica\nPannolini\nMusicassette e VHS\nCarta carbone\nCarta plastificata\nCocci di ceramica, porcellana\nTerracotta\nCristalli e lastre di vetro\nGomma";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
    //NO
    $reply = "\xE2\x9D\x8C NON AMMESSI\nTutti i materiali riciclabili\nPile e farmaci\nMateriale edile\nBatterie auto\nSfalci di potatura\nApparecchiature elettroniche\nMateriali tossici e pericolosi";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
}
if ($text == '🍗  Umido') {
  //DESCRIZIONE
    $reply = "🍗  Umido";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
  //OK
    $reply = "\xE2\x9C\x85 AMMESSI\nAvanzi di cucina cotti e crudi\nScarti di frutta e verdura\nResidui di pane\nGusci di uova e ossa\nFondi di caffè\nFiltri di tè\nSegatura e trucioli\nFazzoletti di carta unti\nAvanzi di carne, pesce, salumi\nCeneri di bracieri spente";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
    //PANNOLINI E pannoloni
    $reply = "🚼 Pannolini e pannoloni vanno esposti nei rifiuti indifferenziati.";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
    //NO
    $reply = "\xE2\x9D\x8C NON AMMESSI\nPiatti e bicchieri di carta\nCarcasse di animali\nOlio di frittura\nPannolini ed assorbenti\nGrandi quantità di ossa e gusci di frutti di mare\nCibi ancora caldi";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
}

if ($text == '🗑️ Altri rifiuti') {
  //DESCRIZIONE
    $reply = "🗑️ Altri rifiuti";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
  //OK
    $reply = "*INGOMBRANTI:* Il ritiro a domicilio degli ingombranti è *gratuito*. Per usufruire di questo servizio e prenotare il ritiro chiamare il *Numero Verde 840-068477*.\n*Lun/Ven* dalle *08:00* alle *16:00*.\n\n*PILE E FARMACI:* I rifiuti particolari, come le *pile esauste e i farmaci scaduti*, devono essere conferiti presso gli *appositi contenitori* localizzati presso EMPORIO 1993 per le pile esauste e la farmacia Rossi per i farmaci scaduti.\nPer la guida completa visita: \nhttp://www.irpiniambiente.it/tipologie-rifiuto.html";
    $content = ['chat_id' => $chat_id, 'text' => $reply, 'parse_mode' => 'markdown'];
    $telegram->sendMessage($content);
    //NO
    $reply = "\xE2\x9D\x8C NON AMMESSI\nSanitari\nMateriale edile\nMateriale ferroso\nMateriali tossici e pericolosi";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
}
//FINE MATERIALI

//INFORMAZIONI UTILI
if ($text == 'ℹ️ Informazioni utili') {
    $reply = "*Suggerimenti forniti dal Comune:*\n\n☎️*Irpinia Ambiente Ingombranti:* [840068477]\n_Per ritiro gratuito ingombranti e apparecchiature elettriche ed elettroniche._\n\n\n📍 *CENTRO DI RACCOLTA*\n\n\xF0\x9F\x93\xB1 *ISOLA Ecologica:* [3456154406]\n*c.da Camporeale*\n_Ariano Irpino (Av)_\n🗺️ [https://goo.gl/maps/wQt4M2thGZLKhbzL7]\n\n🕘 *ORARIO DI CONFERIMENTO*\n\n_Dal lunedì al sabato_\ndalle 10.00 alle 18.00\n\nDomenica CHIUSO";
    $content = ['chat_id' => $chat_id, 'text' => $reply, 'parse_mode' => 'markdown'];
    $telegram->sendMessage($content);
}
//FINE INFORMAZIONI UTILI


//CREDITI
if ($text == 'Crediti') {
    $reply = "Questo Bot Telegram non è in alcun modo affiliato al Comune di Savignano Irpino o all'azienda preposta alla raccolta.\n\nE' un semplice strumento creato da Pasquale M. per i cittadini di Savignano e gli ospiti del comune, che trae informazioni dal sito istituzionale del comune e dell'azienda preposta al ritiro:\n [http://comune.savignano.av.it/], [http://www.irpiniambiente.it]";
    $content = ['chat_id' => $chat_id, 'text' => $reply];
    $telegram->sendMessage($content);
}
//FINE CREDITI

//NOTIFICHE
if ($text == '📬 Notifiche') {
  $option = [['Si','No'], /*['No'],*/['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'parse_mode' => 'markdown', 'text' => "*NOTIFICHE*\n\nVuoi ricevere una notifica dal *Lunedì al Venerdì* per conoscere quali rifiuti devi esporre?"];
  $telegram->sendMessage($content);
}
if ($text == 'Si') {
  $option = [['📬 Notifiche'],['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'parse_mode' => 'markdown', 'text' => "*Grazie per aver attivato le notifiche*\n\nPotrai cambiare idea in qualunque momento."];
  $telegram->sendMessage($content);

  //Notifiche in DB
  $sql = "UPDATE differenziatabot SET attivo = '1' WHERE id_utente = $chat_id";

  if (mysqli_query($conn, $sql)) {
  echo "New record created successfully";
  } else {
  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }

  mysqli_close($conn);
  //Fine Memorizza chatID in DB
}
if ($text == 'No') {
  $option = [['📬 Notifiche'],['Menu Principale']];
  // Create a permanent custom keyboard
  $keyb = $telegram->buildKeyBoard($option, $onetime = false);
  $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'parse_mode' => 'markdown', 'text' => "*Non riceverai notifiche*\n\nPotrai cambiare idea in qualunque momento."];
  $telegram->sendMessage($content);

  //Notifiche in DB
  $sql = "UPDATE differenziatabot SET attivo = '0' WHERE id_utente = $chat_id";

  if (mysqli_query($conn, $sql)) {
  echo "New record created successfully";
  } else {
  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }

  mysqli_close($conn);
  //Fine Memorizza chatID in DB
}
//FINE NOTIFICHE

//Che rifiuti posso buttare oggi?
if ($text == '🗑️ Che rifiuti posso buttare oggi?'){
    $option = [['📅 Calendario','📄 Materiali'],['Verifica 🥂 Festività'],['ℹ️ Informazioni utili'],['Menu Principale']];

    //Calcolo giorno della settimana e Messaggio
    $gds=date(D);
    $oggi=date("d-M-Y", strtotime("now"));
    $secondMondayPaper=date("d-M-Y", strtotime("second monday of this month"));
    $fourthMondayPaper=date("d-M-Y", strtotime("fourth monday of this month"));
    $secondFridayGlass=date("d-M-Y", strtotime("second friday of this month"));
    $fourthFridayGlass=date("d-M-Y", strtotime("fourth friday of this month"));
    $secondWedPlastic=date("d-M-Y", strtotime("second wednesday of this month"));
    $fourthWedPlastic=date("d-M-Y", strtotime("fourth wednesday of this month"));
    
//    $testday=date("d-M-Y", strtotime("first saturday of this month"));
    
    switch ($gds) {
        case "Mon":
            if ($oggi == $secondMondayPaper){
                $messaggio = "*Lunedì*\n\n*Attenzione, oggi è il secondo Lunedì del mese*\n\npuoi portare fuori:\n\n🍗 *Umido*\n\n*Esposizione*\n_dalle ore 08:00 alle 12.00_\n\ne nel pomeriggio:\n\n📦 *Carta, Cartone e Cartoncino*\n\n*Esposizione*\n_dalle ore 12:00 alle ore 18:00_";
            } elseif ($oggi == $fourthMondayPaper) {
                $messaggio = "*Lunedì*\n\n*_Attenzione, oggi è il quarto Lunedì del mese_.\n\nOggi puoi portare fuori:*\n\n🍗 *Umido*\n\n*Esposizione*\n_dalle ore 08:00 alle 12.00_\n\nPuoi anche portare fuori:*\n\n📦 *Carta, Cartone e Cartoncino*\n\n*Esposizione*\n_dalle ore 12:00 alle ore 18:00_";
            }  else {
                $messaggio = "*Lunedì*\n\n*Oggi puoi portare fuori:*\n\n🍗 *Umido*\n\n*Esposizione*\n_dalle ore 08:00 alle 12.00_";
            }
            break;
        case "Tue":
            $messaggio = "*Martedì*\n\n*Oggi puoi portare fuori:*\n\n💡 *Indifferenziato*\n\n*Esposizione*\n_dalle ore 12:00 alle 18:00_";
            break;
        case "Wed":
//          $messaggio = "*Mercoledì*\n\n*Oggi puoi portare fuori:*\n\n🎈🥫 *Plastica e Metalli*\n\n*Esposizione*\n_dalle ore 13:00_";
            if ($oggi == $secondWedPlastic){
                $messaggio = "*Mercoledì*\n\n*Attenzione, oggi è il secondo Mercoledì del mese*\n\npuoi portare fuori:\n\n🎈🥫 *Plastica e Metalli*\n\n*Esposizione*\n_dalle ore 12:00 alle 18:00_";
            } elseif ($oggi == $fourthWedPlastic) {
                $messaggio = "*Mercoledì*\n\n*Attenzione, oggi è il quarto Mercoledì del mese*\n\npuoi portare fuori:\n\n🎈🥫 *Plastica e Metalli*\n\n*Esposizione*\n_dalle ore 12:00 alle 18:00_";
            }              
            break;
        case "Thu":
            $messaggio = "*Giovedì* ⛔️ 🚛\n\n*Oggi non c'è raccolta differenziata porta a porta.*";
            break;
        case "Fri":
//          $messaggio = "*Venerdì*\n\n*Oggi puoi portare fuori:*\n\n🍗 *Umido*\n\n*Esposizione*\n_dalle ore 08:00 alle 12.00_";
            if ($oggi == $secondFridayGlass){
                $messaggio = "*Venerdì*\n\n*Attenzione, oggi è il secondo Venerdì del mese*\n\npuoi portare fuori:\n\n🍗 *Umido*\n\n*Esposizione*\n_dalle ore 08:00 alle 12.00_\n\ne nel pomeriggio:\n\n🍷 *Vetro*\n\n*Esposizione*\n_dalle ore 12:00 alle ore 18:00_";
            } elseif ($oggi == $fourthFridayGlass) {
                $messaggio = "*Venerdì*\n\n*Attenzione, oggi è il quarto Venerdì del mese*\n\npuoi portare fuori:\n\n🍗 *Umido*\n\n*Esposizione*\n_dalle ore 08:00 alle 12.00_\n\ne nel pomeriggio:\n\n🍷 *Vetro*\n\n*Esposizione*\n_dalle ore 12:00 alle ore 18:00_";
            }  else {
                $messaggio = "*Venerdì*\n\n*Oggi puoi portare fuori:*\n\n🍗 *Umido*\n\n*Esposizione*\n_dalle ore 08:00 alle 12.00_";
            }            
            break;
        case "Sat":
            $messaggio = "*Oggi è Sabato* ⛔️ 🚛\n\n*Non c'è raccolta differenziata porta a porta.*\n";          
            break;
        case "Sun":
            $messaggio = "*Oggi è Domenica* ⛔️ 🚛\n\n*Non c'è raccolta differenziata porta a porta.*\n";
            break;
        default:
            break;
    }
    //Fine Calcolo giorno della settimana e Messaggio
    
    $oggifestivo=date("d-m", strtotime("now"));
    $festa="";
    
    foreach ($feste as $key => $value){
	       if (stristr($key, $oggifestivo))
		      $festa = " è  $value";
        }
    
    if ( in_array($oggi, $festivita) || $gds=="Sun" )
    {
    $messaggio = "*Attenzione festivo* ⛔️ $festa 🚛\n\n*Durante le festività viene garantita la raccolta dell'Umido ma non degli altri materiali previsti per quel giorno.*\n\n".$messaggio;
    }
    else
    {
    $messaggio = $messaggio;
    }
    
    // Create a permanent custom keyboard
    $keyb = $telegram->buildKeyBoard($option, $onetime = false);
    $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb,'parse_mode' => 'markdown', 'text' => $messaggio];
    $telegram->sendMessage($content);
}
//Fine Che rifiuti posso buttare oggi?



//NOMECITTA non è la tua città?
if ($text == 'Savignano Irpino non è la tua città?'){
    $option = [['🗑️ Che rifiuti posso buttare oggi?'],['📅 Calendario','📄 Materiali'], ['ℹ️ Informazioni utili','📬 Notifiche'], ['Savignano Irpino non è la tua città?'], ['Crediti']];
    // Create a permanent custom keyboard
    $keyb = $telegram->buildKeyBoard($option, $onetime = false);
    $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb,'parse_mode' => 'markdown', 'text' => "♻️ *".$firstname."*, Savignano non è la tua città?\n\nPer il momento questo servizio è solo per questo comune. "];
    $telegram->sendMessage($content);
}
//Fine NOMECITTA non è la tua città?

if ($text == 'Verifica 🥂 Festività'){
    $option = [['🗑️ Che rifiuti posso buttare oggi?'],['📅 Calendario','📄 Materiali'], ['ℹ️ Informazioni utili','📬 Notifiche'], ['Savignano Irpino non è la tua città?'], ['Crediti']];    
    
    $oggi=date("d-M-Y", strtotime("now"));
    $oggifestivo=date("d-m", strtotime("now"));
    $gds=date(D);
    $festa="";
    
    foreach ($feste as $key => $value){
	       if (stristr($key, $oggifestivo))
		      $festa = " è  $value";
        }
    
    if ( in_array($oggi, $festivita) || $gds=="Sun" )
    {
    $messaggio = "*Oggi è festivo* ⛔️ $festa 🚛\n\n*Durante le festività viene garantita la raccolta dell'Umido ma non degli altri materiali previsti per quel giorno.*\n";
    }
    else
    {
    $messaggio = "*Non è un giorno festivo oggi* 🚛\n\n*Il ritiro porta a porta dovrebbe essere regolare a meno che non sia Sabato, Domenica o Giovedì  🤔.*\n";
    }
    
    // Create a permanent custom keyboard
    $keyb = $telegram->buildKeyBoard($option, $onetime = false);
    $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb,'parse_mode' => 'markdown', 'text' => "♻️ ".$firstname." ".$messaggio];
    $telegram->sendMessage($content);
}
?>
