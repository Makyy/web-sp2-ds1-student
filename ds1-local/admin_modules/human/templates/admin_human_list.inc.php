<div class="container-fluid" ng-app="ds1">
    <div class="card">
        <div class="card-header">
            <div class="pull-left">
                Seznam defektů <?php if((isset($filter['datum_konec']) && empty($filter['datum_konec'])) || empty($filter)) echo '- aktivní'; ?>
                <form method="post" action="<?php echo $form_filter_action; ?>">
                    <th>Datum od: &nbsp;</th><input type="date" value="<?php echo $filter['datum_zacatek']; ?>" name="filter[datum_zacatek]">
                    <th>Datum do: &nbsp;</th><input type="date" value="<?php echo $filter['datum_konec']; ?>" name="filter[datum_konec]">
                    <input type="submit" class="btn btn-primary btn-sm" value="Filtrovat" />
                </form>
<!--                --><?php //echo $info_tabulka_zobrazeno ?>
            </div>
            <div class="pull-right">
                <!-- odkaz pro pridani dokumentace -->
<!--                <a href="--><?php //echo $url_pridat_role;?><!--" class="btn btn-primary btn-sm"><i class="icon-plus"></i> Přidělit / upravit roli</a>-->
            </div>
        </div>
        <div class="card-body">

                <?php
                // vypis informací uživatelích systému
                if ($entities != null)
                {
                    echo "<table class='table table-sm table-bordered table-striped table-hover'>";
                    echo "<tr>
                                    <th>#</th>
                                    <th>jméno</th>
                                    <th>příjmení</th>
                                    <th>název</th>
                                    <th>popis</th>
                                    <th>datum začátek</th>
                                    <th>datum konec</th>
                                    <th>akce</th>
                                </tr>";
                    /** @var \ds1\admin_modules\human\entity\defect_entity $entity */
                    foreach($entities as $entity){
                        echo "<tr>";
                        echo "<td>$entity->defekt_id</td>";
                        echo "<td>$entity->obyvatel_jmeno</td>";
                        echo "<td>$entity->obyvatel_prijmeni</td>";
                        echo "<td>$entity->defekt_nazev</td>";
                        echo "<td>$entity->defekt_popis</td>";
                        echo "<td>$entity->defekt_datum_zacatek</td>";
                        echo "<td>$entity->defekt_datum_konec</td>";

                        // detail dokumentace
                        $route_params = array();
                        $route_params["action"] = 'detail';
                        $route_params["obyvatel_id"] = $entity->defekt_obyvatel_id;
                        $route_params["login_uzivatel"] = $uzivatel_info["login"];
                        $url_detail = $this->makeUrlByRoute($route, $route_params);

                        echo "<td><a href=\"$url_detail\" class='btn btn-primary btn-sm'><i class=\"icon-pencil\"></i></a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                    echo "<div class=\"row\">
                       <div class=\"col-md-8 offset-md-2 \">";
                    echo "</div></div>";
                }
                else {
                    echo "<div class=\"col-md-12\">";
                    echo "<div class=\"alert alert-danger fade show\" role=\"alert\">
                               Žádné defekty nebyly nalezeny.
                         </div>";
                    echo "</div>";
                }
                // konec vypis dat
                ?>

        </div>
    </div>