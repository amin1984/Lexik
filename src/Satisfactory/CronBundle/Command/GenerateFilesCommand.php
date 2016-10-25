<?php

namespace Satisfactory\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFilesCommand extends ContainerAwareCommand
{

    protected $_in_folder;
    protected $_out_folder;

    protected function configure()
    {
        $this->setName("cron:parsefiles")
            ->setDescription("Parse data txt files to json for mongo");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->_in_folder = $this->getContainer()->get('kernel')->getRootDir() . '/../datas/in/';
        $this->_out_folder = $this->getContainer()->get('kernel')->getRootDir() . '/../datas/out/';



        $handle = opendir($this->_in_folder);
        $j = 0;
        $write_handle = fopen($this->_out_folder . 'import_'.$j.'.js', 'w');
        $exception_handle = fopen($this->_out_folder.'exceptions.txt', 'w');
        $correspondanceId= null;
        while (($file = readdir($handle)) !== false) {
            if (!preg_match('/(.*)\.csv/', $file))
                continue;
            switch($file) {
                case 'Table_SOFIA_pour_ETL.csv':
                    $headers = array("PRM", "RAISON_SOCIALE_PRM", "DR_CLIENT", "SEGMENT_TECH", "CIVILITE_INTERLOC", "NOM_INTERLOC", "PRENOM_INTERLOC",
                        "TEL_PRO_INTERLOC", "TEL_MOB_INTERLOC", "EMAIL_INTERLOC", "ROLE_INTERLOC", "FONCTION_INTERLOC", "qualif_telpro",
                        "qualif_telmob", "Qualification_mail", "QUALIF EMAIL + TEL");
					$correspondenceId = 47;
                    break;
                case 'Table_Agence_pour_ETL.csv':
                    $headers = array("Code_insee","type_agence","lib_agence");
					$correspondenceId = 50;
                    break;
                case 'Table_Odigo.csv':
                    $headers = array("REFERENCE","PDL","CATEGORIE","SEGMENT","CIVILITE","NOM","PRENOM","SOCIETE","EMAIL","PRECISION_ADRESSE",
                        "NUMEROS_NOM_RUE","NUMERO_TEL_FIXE","NUMERO_TEL_PORT","INSEE","CODE_POSTAL","NOM_COMMUNE","DR_TRIGRAM",
                        "FONCTION","MARCHE","ORIGINE");
					$correspondenceId = 48;
                    break;
                case 'Table-GCP-pour-ETL.csv':
                    $headers = array("PDL Compteur P","Client","Adresse","Complément","Code postal","Commune","Pays","Civilité client",
                        "Nom client","Prénom client","Téléphone client","Fax client","Mail client");
					$correspondenceId = 49;
                    break;
                case 'okok.csv':
                    $headers = array("Code_insee","type_agence","lib_agence");
					$correspondenceId = 50;
                    break;
                default:
                    $headers = array();
                    $correspondenceId = null;
                    break;
            }
            $output->writeLn('Traitement du fichier '.$file.' at '.date('H:i:s'));

            $read_handle = fopen($this->_in_folder . $file, 'r');
            $i = 0;
            while ($line = fgets($read_handle)) {
                $i++;
                if (strpos($file, 'Coordonnees client 253') === 0 && $i <= 1) {
                    continue;
                }

                if (strpos($file, 'Coordonnees client 198') === 0 && $i <= 2) {
                    continue;
                }

//                $line = utf8_encode($line);
                $line = preg_replace("/[\n\r]/", "", $line);
                $fields = explode(",", $line,3);
                if(isset($fields[2]))
                    $fields[2] = str_replace (",", "", $fields[2]);
                $input = array();
                if (count($fields) != count($headers)) {
//                    $output->writeLn($line);
                    $output->writeLn('Problem headers with line '.$i.' '.count($fields));
                    fputs($exception_handle, $line . "\n");
                    $j++;
                    continue;
                }

                $out = '{';
                foreach ($fields as $k => $field) {
                    $out .= '"'.$headers[$k].'": "'.$field.'",';
//                    echo $headers[$k]." : ".$field."\r\n";
                }
                $out .= '"createdBy": "Anais MARTIN",';
                $out .= '"createdAt": ISODate("2016-06-26T12:03:01.0Z")';
                $out .= '}';
                $out = 'db.getCollection("TDC_'.$correspondenceId.'").insert(' . $out . ');';


                fputs($write_handle, $out . "\n");


                $j++;
                if ($j % 10000 == 0) {
                    fclose($write_handle);
                    $write_handle = fopen($this->_out_folder.'import_'.($j/10000).'.js', 'w');
                }
            }

            $output->writeLn('Count elements : '.$i.' at '.date('H:i:s'));

            fclose($read_handle);
            rename($this->_in_folder . $file, $this->_in_folder . 'archives/' . $file);
        }
        fclose($write_handle);
        fclose($exception_handle);
    }

}