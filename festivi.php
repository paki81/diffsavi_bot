<?php
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
        list($ita_date,$confronto_festivo,$anno,$giorno_settimana)=explode('__',date('d-m-Y__d-m__Y__w',$day));
        //SE GIORNO FESTIVO LO INSERISCO NELLA NOSTRA LISTA.
        if (inFestivita($confronto_festivo,$feste)) {      
            $festivita[]=$giorni[$giorno_settimana] . "  " .$ita_date;
            $etichette[$giorni[$giorno_settimana] . "  " .$ita_date]=$feste[$confronto_festivo];
        } else {
            //CONTROLLO CHE NON SIA PASQUETTA.
            $march21=date("$anno-03-21");
            $gPasquetta=easter_days($anno)+1;
            $dataPasquetta = date("d-m-Y",strtotime(date("Y-m-d", strtotime($march21)) . " +$gPasquetta day"));
            if($ita_date==$dataPasquetta) {
                $festivita[]=$giorni[1] . ": " .$ita_date;
                $etichette[$giorni[1] . ": " .$ita_date]="Pasquetta";
            }
        }
        //VADO AVANTI DI UN GIORNO
        $day+=86400;
    }

$fest=array();
    //STAMPO I GIORNI FESTIVI IN UNA TABELLA
// stampo a video una lista coi giorni festivi italiani
echo '<h1>Giorni festivi ' . $anno . '</h1>';
echo '<ul>';
foreach ($festivita as $giorno){
  echo '<li><strong>' . $etichette[$giorno] . '</strong>: ' .$giorno . '</li>';
    $fest[]=$etichette[$giorno]. " - " .$giorno;
    
}
echo '</ul>'; 

    
$oggifestivo=date("d-m", strtotime("now"));


    if ( inFestivita($oggifestivo, $feste) ){
        echo "Festivo - ";
        foreach ($feste as $key => $value){
	       if (stristr($key, $oggifestivo))
		      echo "Oggi è  $value \n";
        }
    } else {
        echo "Non Festivo ".$oggifestivo;

    }



?>									  		
