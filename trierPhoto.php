<?PHP

if ($handle = opendir('.')) {


    $file_log_out_str = 'trierPhotos_'.date('YmdHis').'.log';
    $file_log_out = fopen($file_log_out_str, 'w') or die ("Impossible d'ouvrir le fichier ".$file_log_out_str);

    while (false !== ($entry = readdir($handle))) {

        unset($mime_type);
        unset($file_exif);
        unset($dateTimeOriginal);
        unset($dateTimeOriginal_explode);
        $traitement_ok = false;

	$message='';
        $file_img_ok = false;
        $file_vid_ok = false;
        $file_exif_ok = false;
        $file_nameimg_ok = false;
        $file_namevid_ok = false;
        $file_date_ok = false;

        if ($entry != "." && $entry != "..") {
            
            // Nom du fichier
            /**
            if (file_exists($entry)) {
                return true;
            } else {
                return false;
            }
            */

            // Lecture du type de fichier
            $mime_type = explode("/", mime_content_type($entry));

            // Traitement autorisé
            if ($mime_type[0]=="video" || $mime_type[0]=="image") {
                $traitement_ok = true;
            }


            // Afficher le nom du fichier
            if ($traitement_ok) {
                $message .=  "\n";
                $message .= $entry."\n";
            }


            // Lecture de l'EXIF
            if ($mime_type[0]=="image" && $mime_type[1]=="jpeg") {
                $file_exif = exif_read_data($entry, 'EXIF');
            }

            // Récupération de la date originale depuis EXIF
            if ($traitement_ok && !isset($dateTimeOriginal)) {
                if (isset($file_exif) && is_array($file_exif) && array_key_exists('DateTimeOriginal', $file_exif)) {
                    $dateTimeOriginal_explode = explode(":", $file_exif['DateTimeOriginal']);
                    if (isset($dateTimeOriginal_explode)) {
                        $dateTimeOriginal['y'] = $dateTimeOriginal_explode[0];
                        $dateTimeOriginal['m'] = $dateTimeOriginal_explode[1];
                        $dateTimeOriginal['d'] = explode(" ", $dateTimeOriginal_explode[2])[0];
                    }
                }
		$message .= "     Traitement sur date EXIF";
                
            }

            // Lecture du nom du fichier
            if ($traitement_ok && !isset($dateTimeOriginal)) {
                // Vérification de la structure du nom
                if (    (substr($entry,0,4)=="IMG-") || (substr($entry,0,4)=="VID_") ||
                        (substr($entry,0,4)=="IMG_") || (substr($entry,0,4)=="VID-") )
                {
                    $dateTimeOriginal['y']=substr($entry,4,4);
                    $dateTimeOriginal['m']=substr($entry,8,2);
                    $dateTimeOriginal['d']=substr($entry,10,2);
   
                }

                if (    (substr($entry,0,11)=="Screenshot_") )
                {
                    $dateTimeOriginal['y']=substr($entry,11,4);
                    $dateTimeOriginal['m']=substr($entry,15,2);
                    $dateTimeOriginal['d']=substr($entry,17,2);

                }

		$message .= "Traitement sur nom du fichier";

            }
            
           // Vérification des dates récupérées
           if ($traitement_ok && 
                isset($dateTimeOriginal) && is_array($dateTimeOriginal)) {
                if (isset($dateTimeOriginal) && is_array($dateTimeOriginal) && ($dateTimeOriginal['m'] < 01 || $dateTimeOriginal['m'] >12)) {
                    unset($dateTimeOriginal);
                }
                if (isset($dateTimeOriginal) && is_array($dateTimeOriginal) && ($dateTimeOriginal['d'] < 01 || $dateTimeOriginal['d'] >31)) {
                    unset($dateTimeOriginal);
                }
                if (isset($dateTimeOriginal) && is_array($dateTimeOriginal) && ($dateTimeOriginal['y'] < 1900 || $dateTimeOriginal['d'] >2021)) {
                    unset($dateTimeOriginal);
                }
            }


            // Récupération de la date de modification
            if ($traitement_ok && !isset($dateTimeOriginal)) {
                $dateTimeOriginal_explode = explode(":", date("Y:m:d", filemtime($entry)));
                if (isset($dateTimeOriginal_explode)) {
                    $dateTimeOriginal['y'] = $dateTimeOriginal_explode[0];
                    $dateTimeOriginal['m'] = $dateTimeOriginal_explode[1];
                    $dateTimeOriginal['d'] = $dateTimeOriginal_explode[2];
                }
            }
            
            // Création des dossiers et déplacement du fichier
            if ($traitement_ok && isset($dateTimeOriginal) && is_array($dateTimeOriginal)) {
                $rep_dest = $dateTimeOriginal['y'] ."/". $dateTimeOriginal['m'] ."/". $dateTimeOriginal['d'];
                if (!file_exists($rep_dest)) {
                    if (!mkdir($rep_dest, 0777, true)) {
                        die('Echec lors de la création des répertoires...');
                    }
                }

		$file_dest = $rep_dest."/".$entry;
		if (!file_exists($file_dest)) {
        	        rename($entry, $file_dest);
			$message .= "     => ".$file_dest."\n";
		} else {
			$md5_file_src = md5_file($entry);
			$md5_file_dst = md5_file($file_dest);
			if ($md5_file_src == $md5_file_dst) {
				$message .= "     [ERREUR] ".$file_dest." existe déjà ! Destruction car même empreinte $md5_file_src / $md5_file_dst\n";
				unlink($entry);
			} else {
				$message .= "     [ERREUR] ".$file_dest." existe déjà !\n";
			}
			
		}
		fwrite($file_log_out, $message);
		echo $message;
            }


        }
    }

    closedir($handle);

    fclose($file_log_out);

}
