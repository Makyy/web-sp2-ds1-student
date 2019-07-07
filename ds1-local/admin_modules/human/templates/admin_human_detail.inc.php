<?php
/**
 *   Detail obyvatele
 */
?>
    <div class="container-fluid" id="human-defects">
        <div class="col-md-12 row" style="padding-left: 13px">
            <?php if(isset($name)) echo "<h4>$name</h4>" ?>
        </div>
        <div class="row">
            <div id="body-div" class="col-lg-9 col-md-12 col-sm-12">

                <a class="btn btn-primary btn-sm" id="zoom-out-button" title="Zrušit přiblížení" ><i class="icon-search"></i> ZOOM OUT</a>
                <a class="btn btn-success btn-sm" id="add-point-to-body-btn" title="Přidat defekt" ><i class="icon-plus"></i> DEFECT</a>

                <img class="default-cursor zoom" canaddpoint="false" height="500" id="body-img" originwidth="1645" originheight="4010" src="/admin/template/img/defekty_obyvatel/human-body.jpg" alt="human body" class="img-fluid">

                <?php
                    /** @var \ds1\admin_modules\human\entity\defect_entity $entity */
                    foreach ($entities as $entity) { ?>
                    <div id="defect-image-group-<?php echo $entity->defekt_id ?>">
                        <div class="hidden-label human-label <?php echo getPointDivClass($entity->pozice_souradnice_y) ?>"
                             ratiotop="<?php echo getRatioTop($entity->pozice_souradnice_x) ?>"
                             ratioleft="<?php echo getRatioLeft($entity->pozice_souradnice_y) ?>"
                             x="<?php echo $entity->pozice_souradnice_x ?>" y="<?php echo $entity->pozice_souradnice_y ?>"
                             style="top:<?php echo $entity->pozice_souradnice_x ?>px; left:<?php echo $entity->pozice_souradnice_y ?>px;">
                            <img src="<?php echo getPointPicture($entity->pozice_souradnice_y) ?>" alt="defect point">
                            <label><?php echo $entity->defekt_nazev ?></label>
                        </div>
                    </div>
                <?php } ?>

            </div>

            <div class="col-lg-3 col-md-4 col-sm-12">
                <form class="form" id="defect-form" method="post" action="<?php echo $form_action ?>">
                    <?php $i = 1; foreach ($entities as $entity) { ?>
                        <div class="input-group mt-2" id="defect-group-<?php echo $entity->defekt_id?>">
                            <label style="align-content: center; width: 20px; text-align: center; padding-top: 6px;" for="<?php echo $entity->defekt_id ?>"><?php echo $i . "."; $i++; ?></label>
                            <input type="text" class="form-control point-tb"
                                   id="<?php echo $entity->defekt_id ?>"
                                   value="<?php echo $entity->defekt_nazev ?>"
                                   name="def[<?php echo $entity->defekt_id ?>]">
                            <a class="btn btn-primary btn-sm ml-1" style="padding-top:6px;color: #fafafa" data-toggle="modal" data-target="#prubeh-<?php echo $entity->defekt_id ?>" title="Přidat průběh"><i class="fa fa-fw fa-search"></i></a>
                            <a class="btn btn-danger btn-sm ml-1" style="padding-top:6px;color: #fafafa" id="delete-<?php echo $entity->defekt_id?>" title="Odstranit defekt"><i class="fa fa-fw fa-times"></i></a>

                        </div>
                    <?php } ?>
                    <?php
                        $display = count($entities) > 0 ? '' : 'display: none;';
                    echo '<input type="submit" class="btn btn-primary form-control mt-2" value="Uložit" title="Uložit změny" style="' . $display . '">'
                    ?>

                </form>
        </div>

        <div id="progress-modals">

            <?php
            /**
             * Formulář pro přidávání průběhu k defektu
             */

            /** @var \ds1\admin_modules\human\entity\defect_entity $entity */
            foreach ($entities as $entity)
            {
                echo '<div class="modal" id="prubeh-' . $entity->defekt_id . '">';
                    echo '<div class="modal-dialog">';
                        echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                                echo '<h4 class="modal-title">Detail defektu "'. $entity->defekt_nazev .'"</h4>';
                                echo '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                            echo '</div>';
                            echo '<div class="modal-body">';
                                echo '<h5>Historie průběhu</h5>';
                                echo "<table class='table table-sm table-bordered table-striped table-hover' id='table-progress-{$entity->defekt_id}'>";
                                echo "<thead class='thead-light'>
                                            <tr>
                                                <th>popis</th>
                                                <th>stav</th>
                                                <th>datum</th>
                                            </tr>
                                      </thead><tbody>";
                                /** @var \ds1\admin_modules\human\entity\progress_collection $progress */
                                if(isset($progress) && !empty($progressEntities = $progress->getByDefect($entity->defekt_id)))
                                {
                                    /** @var \ds1\admin_modules\human\entity\progress_entity $progressEntity */
                                    foreach ($progressEntities as $progressEntity)
                                    {
                                        echo "<tr>
                                                <td>{$progressEntity->popis}</td>
                                                <td>{$progressEntity->status}</td>
                                                <td>{$progressEntity->datum_vytvoreni}</td>
                                              </tr>";
                                    }
                                }
                                echo "</tbody></table>";
                                echo "<hr>";

                                echo '<form class="form" action="' . $form_progress_action . '" method="POST" id="form-progress-' . $entity->defekt_id . '">';

                                    echo '<div class="row">';
                                            echo '<div class="col-3">';
                                                echo '<label style="padding-top: 6px;" for="popis">Popis</label>';
                                            echo '</div>';
                                            echo '<div class="col-9">';
                                                echo '<input class="form-control" type="text" name="popis">';
                                            echo '</div>';
                                    echo '</div>';

                                    echo '<div class="row mt-1">';
                                        echo '<div class="col-3">';
                                            echo '<label style="padding-top: 6px;" for="stav">Stav</label>';
                                        echo '</div>';
                                        echo '<div class="col-9">';
                                            echo '<select class="form-control" name="stav" required="required">';
                                                echo '<option value="" disabled selected="selected">-- vyberte stav --</option>';
                                                echo '<option value="0">stejný</option>';
                                                echo '<option value="1">zlepšení</option>';
                                                echo '<option value="2">zhoršení</option>';
                                            echo '</select>';
                                        echo '</div>';
                                    echo '</div>';

                                    echo '<div class="row mt-1">';
                                        echo '<div class="col-3">';
                                            echo '<label style="padding-top: 6px;" for="datum_vytvoreni">Datum</label>';
                                        echo '</div>';
                                        echo '<div class="col-9">';
                                            echo '<input class="form-control" type="date" name="datum_vytvoreni">';
                                        echo '</div>';
                                    echo '</div>';

                                    echo '<input type="hidden" name="defekt_id" value="' . $entity->defekt_id . '">';

                                    echo '<div class="row mt-4">';
                                        echo '<div class="col-3">';
                                            echo '<input type="submit" value="Aktualizovat průběh" class="btn btn-primary">';
                                        echo '</div>';
                                    echo '</div>';

                                echo '</form>';

                                echo '<hr>';

                                echo '<div class="mt-2"><h5>Popis defektu</h5>';

                                    echo '<form class="form" action="' . $form_detail_action . '" method="POST" id="form-detail-' . $entity->defekt_id . '">';

                                        echo '<div class="row">';
                                            echo '<div class="col-3">';
                                                echo '<label style="padding-top: 6px;" for="sirka_cm">Šířka (cm)</label>';
                                            echo '</div>';
                                            echo '<div class="col-9">';
                                                echo '<input class="form-control" type="number" min="0" max="250" name="sirka_cm" value="'. $entity->pozice_sirka_cm .'">';
                                            echo '</div>';
                                        echo '</div>';

                                        echo '<div class="row mt-1">';
                                            echo '<div class="col-3">';
                                                echo '<label style="padding-top: 6px;" for="vyska_cm">Výška (cm)</label>';
                                            echo '</div>';
                                            echo '<div class="col-9">';
                                                echo '<input class="form-control" type="number" min="0" max="250" name="vyska_cm" value="'. $entity->pozice_vyska_cm .'">';
                                            echo '</div>';
                                        echo '</div>';

                                        echo '<div class="row mt-1">';
                                            echo '<div class="col-3">';
                                                echo '<label style="padding-top: 6px;" for="barva_text">Barva</label>';
                                            echo '</div>';
                                            echo '<div class="col-9">';
                                                echo '<input class="form-control" type="text" name="barva_text" value="'. $entity->pozice_barva_text .'">';
                                            echo '</div>';
                                        echo '</div>';

                                        echo '<div class="row mt-1">';
                                            echo '<div class="col-3">';
                                                echo '<label style="padding-top: 6px;" for="barva_hex">Barva Hex</label>';
                                            echo '</div>';
                                            echo '<div class="col-9">';
                                                echo '<input class="form-control" type="color" name="barva_hex" value="'. $entity->pozice_barva_hex .'">';
                                            echo '</div>';
                                        echo '</div>';

                                        echo '<input type="hidden" name="defekt_id" value="' . $entity->defekt_id . '">';

                                        echo '<div class="row mt-4">';
                                            echo '<div class="col-3">';
                                                echo '<input type="submit" value="Aktualizovat detail" class="btn btn-primary">';
                                            echo '</div>';
                                        echo '</div>';

                                    echo '</form>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/admin/template/js/defekty_obyvatele/resizer.js"></script>
<script src="/admin/template/js/defekty_obyvatele/wheelzoom.js"></script>
<script src="/admin/template/js/defekty_obyvatele/defekty.js"></script>

<?php

    function getRatioTop($imgval) {

        $height = 500;//4041;
        return abs($imgval / $height);
    }

    function getRatioLeft($imgval) {

        $width = 735;//5940;
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