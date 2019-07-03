<?php
/**
 * Created by PhpStorm.
 * User: Maky
 * Date: 14.05.2019
 * Time: 11:28
 */

namespace ds1\admin_modules\human;


use ds1\admin_modules\human\entity\defect_entity;

class human  extends \ds1\core\ds1_base_model
{
    public function addDefectPoint($x, $y, $obyvatelId)
    {
        $humanArray = array(
            "obyvatel_id" => $obyvatelId,
            "datum_zacatek" => date("Y-m-d H:i:s")
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

    public function getDefectPointsByObyvatelId($obyvatelId)
    {
        $where = array($this->DBHelperGetWhereItem("obyvatel_id", $obyvatelId));

        $defectsArray = $this->DBSelectAll(TABLE_HUMAN, "*", $where);

        // Assign to associative array by defect ID
        // instead of:
        //      array:
        //          [0] => row
        //          [1] => row
        //
        // it'll be:
        //      array:
        //          [id] => row
        //          [id] => row
        foreach ($defectsArray as $k => $defect)
        {
            $defectsArray[$defect["id"]] = $defect;
            unset($defectsArray[$k]);
        }

        $positionsArray = [];
        foreach ($defectsArray as $defect)
        {
            $where = array($this->DBHelperGetWhereItem('defekt_id', $defect['id']));
            $positionsArray[] = $this->DBSelectOne(TABLE_HUMAN_POSITION, "*", $where);
        }

        return [
            'defects' => $defectsArray,
            'positions' => $positionsArray
        ];
    }

    public function updateDefectName($defectId, $name)
    {
        $where = array($this->DBHelperGetWhereItem("id", $defectId));
        $data = array("nazev" => $name);

        return $this->DBUpdate(TABLE_HUMAN, $where, $data);
    }

    public function deleteDefectById($defectId)
    {
        $where = array($this->DBHelperGetWhereItem("defekt_id", $defectId));
        $this->DBDelete(TABLE_HUMAN_POSITION, $where, "");

        $where = array($this->DBHelperGetWhereItem("id", $defectId));
        return $this->DBDelete(TABLE_HUMAN, $where, "");
    }

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

        $defectsResults = $this->DBSelectAll(TABLE_HUMAN, "*", $where);
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
}