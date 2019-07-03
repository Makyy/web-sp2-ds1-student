<?php


namespace ds1\admin_modules\human\entity;


class progress_entity extends base_entity
{
    public $id;
    public $defekt_obyvatele_id;
    public $popis;
    public $stav;
    public $datum_vytvoreni;

    // pomocná pro výpis textu stavu
    public $status;

    private $statusArray = [
        0 => 'stejný',
        1 => 'zlepšení',
        2 => 'zhoršení'
    ];

    public function setValues($dbRow)
    {
        $this->setProperties("", $dbRow);

        $this->status = $this->statusArray[$this->stav];
    }
}