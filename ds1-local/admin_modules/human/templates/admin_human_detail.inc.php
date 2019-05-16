<?php
/**
 *   Detail obyvatele
 */

?>

    <div class="container-fluid" id="human-defects">
        <div class="row">
            <div class="col-md-6" style="position: relative">
                <img id="body-img" src="/admin/template/img/defekty_obyvatel/front.jpg" alt="human body" class="img-fluid">

                <?php
                foreach ($defects as $defect ) { ?>

                    <div class="human-label" ratiotop="<?php echo getRatioTop($defect[0]) ?>" ratioleft="<?php echo getRatioLeft($defect[1]) ?>" style="top:<?php echo $defect[0]?>px; left:<?php echo $defect[1]?>px;">
                        <img src="/admin/template/img/defekty_obyvatel/path-top-right.png" alt="defect" >
                        <label><?php echo $defect[2]?></label>
                    </div>

                <?php
                }
                ?>

            </div>
        </div>

        <br/>
    </div>

<script src="/admin/template/js/defekty_obyvatele/defekty.js"></script>

<?php

    function getRatioTop($imgval) {
        $height = 4010;
        return abs($imgval / $height);
    }

    function getRatioLeft($imgval) {
        $width = 1645;
        return abs($imgval / $width);
    }

?>