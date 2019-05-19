<?php
/**
 *   Detail obyvatele
 */

?>

    <div class="container-fluid" id="human-defects">
        <div class="row">
            <div id="body-div" class="col-md-10" style="position: relative">
                <img class="zoom" height="500" id="body-img" originwidth="1645" originheight="4010" src="/admin/template/img/defekty_obyvatel/human-body.jpg" alt="human body" class="img-fluid">

                <?php foreach ($defects as $defect) { ?>
                    <div id="defect-image-group-<?php echo $defect["id"] ?>">
                        <div class="human-label <?php echo getPointDivClass($defect["pos_y"]) ?>"
                             ratiotop="<?php echo getRatioTop($defect["pos_x"]) ?>"
                             ratioleft="<?php echo getRatioLeft($defect["pos_y"]) ?>"
                             style="top:<?php echo $defect["pos_x"] ?>px; left:<?php echo $defect["pos_y"] ?>px;">
                            <img src="<?php echo getPointPicture($defect["pos_y"]) ?>" alt="defect point">
                            <label><?php echo $defect["nazev"] ?></label>
                        </div>
                    </div>
                <?php } ?>

            </div>

            <div class="col-md-2">
                <form class="form" id="defect-form" method="post" action="<?php echo $form_action ?>">
                    <?php $i = 1; foreach ($defects as $defect) { ?>
                        <div class="input-group mt-2" id="defect-group-<?php echo $defect["id"]?>">
                            <label for="<?php echo $defect["id"] ?>"><?php echo $i . "."; $i++; ?></label>
                            <input type="text" class="form-control"
                                   id="<?php echo $defect["id"] ?>"
                                   value="<?php echo $defect["nazev"] ?>"
                                   name="def[<?php echo $defect["id"] ?>]">
                            <a class="btn btn-danger btn-sm ml-1" style="color: #fafafa" id="delete-<?php echo $defect["id"]?>"><i class="fa fa-fw fa-times"></i></a>
                        </div>
                    <?php } ?>

                    <?php if(count($defects)>0)
                        echo '<input type="submit" class="btn btn-success form-control mt-2" value="Uložit">';
                    ?>
                </form>
            </div>
        </div>

        <br/>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/admin/template/js/defekty_obyvatele/resizer.js"></script>
<script src="/admin/template/js/defekty_obyvatele/wheelzoom.js"></script>
<script src="/admin/template/js/defekty_obyvatele/defekty.js"></script>

<?php

    function getRatioTop($imgval) {

        $height = 4010;
        return abs($imgval / $height);
    }

    function getRatioLeft($imgval) {

        $width = 5940;
        return abs($imgval / $width);
    }

    function isOnLeftSide($left){

        $border = 0.5;
        $ratioleft = getRatioLeft($left);
        if ($ratioleft > $border){
            return true;
        }
        else {
            return false;
        }
    }

    function getPointDivClass($left) {

        if (isOnLeftSide($left)){
            return "right-side";
        }
        else {
            return "left-side";
        }
    }

    function getPointPicture($left){

        if (isOnLeftSide($left)){
            return "/admin/template/img/defekty_obyvatel/path-top-left.png";
        }
        else{
            return "/admin/template/img/defekty_obyvatel/path-top-right.png";
        }
    }

?>