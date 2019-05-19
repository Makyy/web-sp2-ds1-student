<div class="container-fluid" ng-app="ds1">
    <div class="card">
        <div class="card-header">
            <div class="pull-left">
                Seznam obyvatel - <?php echo $obyvatele_list_name; ?>
            </div>
            <div class="pull-right">
                <!-- odkaz pro pridani obyvatele -->
                <a href="<?php echo $url_obyvatel_add_prepare;?>" class="btn btn-primary btn-sm"><i class="icon-plus"></i> Přidat obyvatele</a>
            </div>
        </div>
        <div class="card-body" ng-controller="adminSearchObyvatele">

            <div class="row">
                <div class="col-md-12">
                <!-- search form-->
                <form method="post" action="<?php echo $form_search_submit_url;?>">
                        <input type="hidden" name="action" value="<?php echo $form_search_action;?>" />

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="icon-magnifier"></i></span>
                            </div>

                            <!--
                            <input size="50" name="search_string" value="<?php echo $search_string;?>"/>
                            -->
                            <script>
                                window.url_obyvatele_autocomplete = "<?php echo $url_obyvatele_autocomplete;?>";
                                window.search_string = "<?php echo $search_string;?>";
                                window.base_url = "<?php echo $base_url;?>";
                            </script>

                            {{nova_anotace.klicove_slovo}}
                            <!-- input-class=" form-control-small title-field="klicove_slovo" -->
                            <angucomplete-alt
                                    id="autocomplete_klicove_slovo"

                                    input-name="search_string"
                                    style="width: 40%"
                                    initial-value="search.klicove_slovo"

                                    title-field="obyvatele"
                                    placeholder="klíčové slovo"
                                    description-field="autocomplete_desc"

                                    selected-object="autocompleteSelected"
                                    input-changed="autocompleteInputChanged"

                                    override-suggestions="true"
                                    auto-match="true"
                                    minlength="1"
                                    input-class="form-control"
                                    match-class="highlight"
                                    remote-api-handler="callAutocompletePost"
                                    remote-url-data-field="autocomplete_results"
                            ></angucomplete-alt>

                            <div class="input-group-append">
                                <input type="submit" class="btn btn-primary" value="Hledat" />
                            </div>
                        </div>
                        <small>Hledám v ID, příjmení, jméně a zkratce pojišťovny</small>
                </form>
            </div>
            </div>
            <br/>

            <?php
            /*
             * test angularu
             <div>
                <label>Name:</label>
                <input type="text" ng-model="yourName" placeholder="Enter a name here">
                <hr>
                <h1>Hello {{yourName}}!</h1>
            </div>

            <!-- pro otestovani funkce autocomplete - v ds1_autocomplete.js musi byt pole countries -->
            <div angucomplete-alt id="ex1"
                 placeholder="Search countries"
                 maxlength="50"
                 pause="100"
                 selected-object="selectedCountry"
                 local-data="countries"
                 search-fields="name"
                 title-field="name"
                 minlength="1"
                 input-class="form-control form-control-small"
                 match-class="highlight"></div>
                {{nova_anotace.klicove_slovo}}
            */
            ?>

            <div class="row">
                <div class="col-md-12">
            <?php
            // vypis obyvatel
            if ($obyvatele_list != null) {
                //printr($users_list); exit;

                // table-sm - zmensi odsazeni bunek
                echo "<table class='table table-sm table-bordered table-striped table-hover'>";
                echo "<tr>
                                    <th>#</th>
                                    <th>příjmení</th>
                                    <th>jméno</th>
                                    <th>pokoj</th>
                                    <th>věk</th>
                                    <th>datum narození</th>
                                    <th>zkratka pojišťovny</th>
                                    <th>OP platnost DO</th>
                                    <th>&nbsp;</th>
                                </tr>";

                foreach ($obyvatele_list as $obyvatel) {
                    // detail uzivatele
                    $route_params = array();
                    $route_params["action"] = $obyvatel_detail_action;
                    $route_params["obyvatel_id"] = $obyvatel["id"];
                    $url_detail = $this->makeUrlByRoute($route, $route_params);

                    // update prepare
                    $route_params = array();
                    $route_params["action"] = $obyvatel_update_prepare_action;
                    $route_params["obyvatel_id"] = $obyvatel["id"];
                    $url_update_prepare = $this->makeUrlByRoute($route, $route_params);

                    // url na detail pokoje
                    $pokoj_nazev_pom = "";

                    if (isset($obyvatel["pokoj"]["id"])) {
                        $route_params = array();
                        $route_params["action"] = "pokoj_detail_show"; // fixme: tady by to chtelo nejak obecne predat jmeno akce
                        $route_params["pokoj_id"] = $obyvatel["pokoj"]["id"];
                        $url_pokoj_detail = $this->makeUrlByRoute($pokoje_route_name, $route_params);
                        $pokoj_nazev_pom = "<a href=\"$url_pokoj_detail\">$obyvatel[pokoj_nazev]</a>";
                    }
                    else {
                        $pokoj_nazev_pom = $obyvatel["pokoj_nazev"];
                    }


                    echo "<tr>";

                    echo "<td>$obyvatel[id]</td>";
                    echo "<td>$obyvatel[prijmeni]</td>";
                    echo "<td>$obyvatel[jmeno]</td>";
                    echo "<td>$pokoj_nazev_pom</td>";

                    // vek
                    if (isset($obyvatel["vek"])) {
                        echo "<td>$obyvatel[vek]</td>";
                    }
                    else echo "<td>&nbsp;</td>"; // index vek neni k dispozici

                    // toto mi prevede datum do spravneho formatu pro CR
                    echo "<td>".$controller->helperFormatDate($obyvatel["datum_narozeni"])."</td>";
                    echo "<td>$obyvatel[pojistovna_zkratka]</td>";
                    echo "<td>".$controller->helperFormatDate($obyvatel["op_platnost_do"])."</td>";
                    echo "<td>
                                  <a href=\"$url_detail\" class='btn btn-primary btn-sm'><i class=\"icon-layers\"></i></a>
                                  &nbsp;&nbsp;
                                  <a href=\"$url_update_prepare\" class='btn btn-primary btn-sm'><i class=\"icon-pencil\"></i></a>
                                  &nbsp;&nbsp;
                                  <a href=\"$url_add_defect$obyvatel[id]\" class='btn btn-primary btn-sm'><i class=\"icon-plus\"></i></a>
                              </td>";

                    echo "</tr>";
                }

                echo "</table>";
                echo "</div>";

                // stranovani
                echo "<div class=\"row\">
                       <div class=\"col-md-8 offset-md-2 \">";

                echo $pagination_html;

                echo "</div></div>";
                // konec strankovani
            }
            else {
                echo "<div class=\"col-md-12\">";

                        echo "<div class=\"alert alert-danger fade show\" role=\"alert\">
                                Žádní obyvatelé nenalezeni.
                         </div>";

                echo "</div>";
            }


            ?>
        </div>
    </div>
</div>