<?php
/**
 *   Detail obyvatele
 */

?>
<div class="container-fluid" id="human-defects">
    <div class="row">
        <div class="col-md-6">
            <img src="/admin/template/img/defekty_obyvatel/front.jpg" alt="bolístky" class="img-fluid">

             <?php
                foreach ($defects as $defect ) { ?>
                    <div class="human-label" style="top:<?php echo $defect[0]?>vw; right:<?php echo $defect[1]?>vw;">


                        <img src="/admin/template/img/defekty_obyvatel/path-top-right.png" >
                        <label><?php echo $defect[2]?></label>
                    </div>
            <?php
                }
             ?>


        </div>
    </div>

    <br/>
</div>

<?php
    function getLabelImage($top, $right) {
        $height = 4010/2;
        $width = 1645/2;

        if ($top > $height && $width > $right ) {
            return "path_top_left";
        }

        if ($top > $height && $width < $right ) {
            return "path_top_right";
        }

        if ($top < $height && $width > $right ) {
            return "path_bottom_left";
        }

        if ($top < $height && $width < $right ) {
            return "path_bottom_right";
        }
    }

?>