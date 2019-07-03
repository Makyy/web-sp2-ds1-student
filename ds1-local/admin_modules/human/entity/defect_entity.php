<?php


namespace ds1\admin_modules\human\entity;

/**
 * Entita pro uchování výsledků z DB pro defekt
 *
 * @package ds1\admin_modules\human\entity
 */
class defect_entity
{
    // Tabulka ds1_defekt_obavatele
    public $defekt_id;
    public $defekt_obyvatel_id;
    public $defekt_nazev;
    public $defekt_popis;
    public $defekt_datum_zacatek;
    public $defekt_datum_konec;

    // Tabulka ds1_obyvatel
    public $obyvatel_id;
    public $obyvatel_jmeno;
    public $obyvatel_prijmeni;
    public $obyvatel_rodne_prijmeni;
    public $obyvatel_datum_narozeni;
    public $obyvatel_tituly_pred;
    public $obyvatel_tituly_za;
    public $obyvatel_rodne_cislo;
    public $obyvatel_misto_narozeni;
    public $obyvatel_pojistovna_zkratka;
    public $obyvatel_cislo_pojistence;
    public $obyvatel_adresa_ulice;
    public $obyvatel_adresa_cp;
    public $obyvatel_adresa_mesto;
    public $obyvatel_op;
    public $obyvatel_op_platnost_do;
    public $obyvatel_stav;

    // Tabulka ds1_defekty_obyvatele_pozice
    public $pozice_id;
    public $pozice_defekt_id;
    public $pozice_souradnice_x;
    public $pozice_souradnice_y;
    public $pozice_otoceni_stupnu;
    public $pozice_zoom;
    public $pozice_popis;
    public $pozice_sirka_cm;
    public $pozice_vyska_cm;
    public $pozice_barva_text;
    public $pozice_barva_hex;

    // Tabulka ds1_defekty_obyvatele_prubeh
    public $prubeh_id;
    public $prubeh_defekt_obyvatele_id;
    public $prubeh_popis;
    public $prubeh_stav;
    public $prubeh_datum_vytvoreni;

    public function setDefectValues($dbRow)
    {
        $this->setValues("defekt", $dbRow);
    }

    public function setObyvatelValues($dbRow)
    {
        $this->setValues("obyvatel", $dbRow);
    }

    public function setPoziceValues($dbRow)
    {
        $this->setValues('pozice', $dbRow);
    }

    public function setPrubehValues($dbRow)
    {
        $this->setValues('prubeh', $dbRow);
    }

    private function setValues($prefix, $dbRow)
    {
        foreach ($dbRow as $key => $value)
        {
            $propertyKey = $prefix . '_' . $key;
            if (property_exists($this, $propertyKey))
            {
                $this->{$propertyKey} = $value;
            }
        }
    }

}