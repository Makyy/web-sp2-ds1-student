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
    public function addDefectPoint($array){
        return $this->DBInsert(TABLE_HUMAN, $array);
    }

    public function getDefectPointsByObyvatelId($obyvatelId){
        $where = array(
            "obyvatel_id" => $obyvatelId
        );

        return $this->DBSelectAll(TABLE_HUMAN, "pos_x, pos_y, nazev", $where);
    }
}