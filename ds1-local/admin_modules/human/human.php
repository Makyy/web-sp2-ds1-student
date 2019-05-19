<?php
/**
 * Created by PhpStorm.
 * User: Maky
 * Date: 14.05.2019
 * Time: 11:28
 */

namespace ds1\admin_modules\human;


class human  extends \ds1\core\ds1_base_model
{
    public function addDefectPoint($x, $y, $obyvatelId)
    {
        $array = array(
            "pos_x" => $x,
            "pos_y" => $y,
            "obyvatel_id" => $obyvatelId
        );

        return $this->DBInsert(TABLE_HUMAN, $array);
    }

    public function getDefectPointsByObyvatelId($obyvatelId)
    {
        $where = array($this->DBHelperGetWhereItem("obyvatel_id", $obyvatelId));

        return $this->DBSelectAll(TABLE_HUMAN, "*", $where);
    }

    public function updateDefectName($defectId, $name)
    {
        $where = array($this->DBHelperGetWhereItem("id", $defectId));
        $data = array("nazev" => $name);

        return $this->DBUpdate(TABLE_HUMAN, $where, $data);
    }

    public function deleteDefectById($defectId)
    {
        $where = array($this->DBHelperGetWhereItem("id", $defectId));

        return $this->DBDelete(TABLE_HUMAN, $where, "");
    }
}