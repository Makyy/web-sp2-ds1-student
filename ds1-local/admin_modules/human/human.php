<?php
/**
 * Created by PhpStorm.
 * User: Maky
 * Date: 14.05.2019
 * Time: 11:28
 */

namespace ds1\admin_modules\human;


use ds1\admin_modules\human\entity\defect_entity;
use ds1\admin_modules\human\entity\progress_collection;
use ds1\admin_modules\human\entity\progress_entity;

class human extends \ds1\core\ds1_base_model
{
    private function getCurrentDate()
    {
        return date("Y-m-d H:i:s");
    }

    public function addDefectPoint($x, $y, $obyvatelId)
    {
        $humanArray = array(
            "obyvatel_id" => $obyvatelId,
            "datum_zacatek" => $this->getCurrentDate()
        );
        $defectId = $this->DBInsert(TABLE_HUMAN, $humanArray);

        $positionArray = array(
            "souradnice_x" => $x,
            "souradnice_y" => $y,
            "defekt_id" => $defectId
        );
        $this->DBInsert(TABLE_HUMAN_POSITION, $positionArray);

        return $defectId;
    }

    public function getDefectsAndHuman($obyvatelId)
    {
        $where = array($this->DBHelperGetWhereItem("obyvatel_id", $obyvatelId));
        $defectsArray = $this->DBSelectAll(TABLE_HUMAN, "*", $where);

        $whereObyvatel = array($this->DBHelperGetWhereItem('id', $obyvatelId));
        $obyvatelRow = $this->DBSelectOne(TABLE_OBYVATELE, '*', $whereObyvatel);

        $positionsArray = [];
        $progressArray = [];
        foreach ($defectsArray as $defect)
        {
            $where = array($this->DBHelperGetWhereItem('defekt_id', $defect['id']));
            $positionsArray[$defect['id']] = $this->DBSelectOne(TABLE_HUMAN_POSITION, "*", $where);

            $where = array($this->DBHelperGetWhereItem('defekt_obyvatele_id', $defect['id']));
            $progressArray[$defect['id']] = $this->DBSelectOne(TABLE_HUMAN_PROGRESS, "*", $where);
        }

        $entities = [];
        foreach ($defectsArray as $defect)
        {
            $entity = new defect_entity();
            $entity->setDefectValues($defect);
            $entity->setPoziceValues($positionsArray[$defect['id']]);
            $entity->setObyvatelValues($obyvatelRow);

            $entities[] = $entity;
        }

        return $entities;
    }

    public function updateDefectName($defectId, $name)
    {
        $where = array($this->DBHelperGetWhereItem("id", $defectId));
        $data = array("nazev" => $name);

        return $this->DBUpdate(TABLE_HUMAN, $where, $data);
    }

    public function deleteDefectById($defectId)
    {
        $where = array($this->DBHelperGetWhereItem("defekt_obyvatele_id", $defectId));
        $this->DBDelete(TABLE_HUMAN_PROGRESS, $where, "");

        $where = array($this->DBHelperGetWhereItem("defekt_id", $defectId));
        $this->DBDelete(TABLE_HUMAN_POSITION, $where, "");

        $where = array($this->DBHelperGetWhereItem("id", $defectId));
        return $this->DBDelete(TABLE_HUMAN, $where, "");
    }

    /**
     * Vrací kolekci entit s defekty a informacemi o obyvateli
     *
     * @param array $filters : filtrování formulářem, možno podle `datum_zacatek` a `datum_konec`
     * @return array defect_entity
     */
    public function getDefectsList($filters = [])
    {
        // Vytvoření where podmínek podle zadaných filtrů
        $where = array();
        if (!empty($filters))
        {
            if (!empty($filters["datum_zacatek"]))
            {
                $where[] = $this->DBHelperGetWhereItem("datum_zacatek", $filters['datum_zacatek'], '>=');
            }

            if (!empty($filters["datum_konec"]))
            {
                $where[] = $this->DBHelperGetWhereItem("datum_konec", $filters['datum_konec'], '<=');
            }
        } else
        {
            $where[] = $this->DBHelperGetWhereItem("datum_konec", NULL, "IS");
        }

        $defectsResults = $this->DBSelectAll(TABLE_HUMAN, "*", $where, "", array(['column' => 'datum_zacatek', 'sort' => 'asc']));
        $obyvateleResults = [];

        foreach ($defectsResults as $key => $result)
        {
            $where = array($this->DBHelperGetWhereItem('id', $result['obyvatel_id']));
            $obyvateleResults[$result['obyvatel_id']] = $this->DBSelectOne(TABLE_OBYVATELE, "*", $where);
        }

        $defectsCollection = array();
        foreach ($defectsResults as $defect)
        {
            $entity = new defect_entity();
            $entity->setDefectValues($defect);
            $entity->setObyvatelValues($obyvateleResults[$defect['obyvatel_id']]);

            $defectsCollection[] = $entity;
        }

        return $defectsCollection;
    }

    public function addDefectProgress(array $values)
    {
        if (!isset($values['defekt_id']))
        {
            return FALSE;
        }

        $progressArray = array(
            "defekt_obyvatele_id" => $values['defekt_id'],
            "popis" => $values['popis'],
            "stav" => $values['stav'],
            "datum_vytvoreni" => !empty($values['datum_vytvoreni']) ? $values['datum_vytvoreni'] : $this->getCurrentDate()
        );

        return $this->DBInsert(TABLE_HUMAN_PROGRESS, $progressArray);
    }

    public function getDefectsAndProgress($obyvatelId)
    {
        $where = array($this->DBHelperGetWhereItem("obyvatel_id", $obyvatelId));
        $defectsArray = $this->DBSelectAll(TABLE_HUMAN, "*", $where);

        $progressArray = [];
        foreach ($defectsArray as $defect)
        {
            $where = array($this->DBHelperGetWhereItem('defekt_obyvatele_id', $defect['id']));
            $result = $this->DBSelectAll(TABLE_HUMAN_PROGRESS, "*", $where, "", array(['column' => 'datum_vytvoreni', 'sort' => 'asc']));
            if (!empty($result))
            {
                $progressArray[$defect['id']] = $result;
            }
        }

        $entities = [];
        foreach ($progressArray as $progress)
        {
            foreach ($progress as $row)
            {
                $entity = new progress_entity();
                $entity->setValues($row);

                $entities[] = $entity;
            }
        }

        return new progress_collection($entities);
    }

    public function getProgress($id)
    {
        $where = array($this->DBHelperGetWhereItem("id", $id));
        $result = $this->DBSelectOne(TABLE_HUMAN_PROGRESS, "*", $where);

        $entity = new progress_entity();
        $entity->setValues($result);

        return $entity;
    }

    public function updateDefectDetail($values)
    {
        if (!isset($values['defekt_id']))
        {
            return FALSE;
        }

        $data = array(
            "sirka_cm" => $values["sirka_cm"],
            "vyska_cm" => $values["vyska_cm"],
            "barva_text" => $values["barva_text"],
            "barva_hex" => $values["barva_hex"]
        );

        $where = array($this->DBHelperGetWhereItem("defekt_id", $values['defekt_id']));

        return $this->DBUpdate(TABLE_HUMAN_POSITION, $where, $data);
    }
}