<?php


namespace ds1\admin_modules\human\entity;

/**
 * Entita pro uchování výsledků z DB pro defekt
 *
 * @package ds1\admin_modules\human\entity
 */
class defect_entity extends base_entity
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

    public function setDefectValues($dbRow)
    {
        $this->setProperties("defekt", $dbRow);
    }

    public function setObyvatelValues($dbRow)
    {
        $this->setProperties("obyvatel", $dbRow);
    }

    public function setPoziceValues($dbRow)
    {
        $this->setProperties('pozice', $dbRow);
    }
}