<?php

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
$oggi=date("d-M-Y", strtotime("now"));

//if (in_array($oggi, $festivita))
//  {
//  echo "Oggi è festa";
//  }
//else
//  {
//  echo "Devi lavorare";
//  }


//FINE CALCOLO FESTIVITA


//NOTIFICHE PUSH
//Per attivare le notifiche push, configura il presente modulo in base alle tue esigenze
//Importa il file database.sql nel tuo database
//Successivamente attiva sul tuo Server la funzione Cron Job e fai eseguire questa pagina all'orario in cui è previsto l'inizio dell'esposizione dei rifiuti in strada
include 'Telegram.php';
// Set the bot TOKEN
$bot_token = 'XXXXXXXXXXXXXXXXXXXXXXXX'
$text = $telegram->Text();

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

//Fine Calcolo giorno della settimana e Messaggio

//Connessione DB
$servername = "XXXXXXXX";
$username = "XXXXXXXX";
$password = "XXXXXXXXX";
$dbname = "XXXXXXXXXXX";
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
//N.B. RIPORTARE I DATI DB ANCHE NEL FILE index.php
//Fine Connessione DB

//INVIO NOTIFICHE A CHI LO HA RICHIESTO
$sql = "SELECT id_utente FROM differenziatabot WHERE attivo='1'"; //SOLO A CHI HA RICHIESTO LA NOTIFICA VIENE INVIATA
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        $chat_id =$row["id_utente"];
        $reply = $messaggio;
        //Template Messaggio
        $option = [['Verifica 🥂 Festività'],['📦 Carta, Cartone e Cartoncino','🎈🥫 Plastica e Metalli'], ['🍷 Vetro', '🍗  Organico','💡 Indifferenziato'],['🗑️ Altri rifiuti'],['Menu Principale']];
        // Create a permanent custom keyboard
        $keyb = $telegram->buildKeyBoard($option, $onetime = false);
        $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply,'parse_mode' => 'markdown'];


        $telegram->sendMessage($content);
        //Fine Template Messaggio
    }
} else {
    echo "0 results";
}
//FINE INVIO NOTIFICHE A CHI LO HA RICHIESTO
?>
