<?php
/**
 * Created by PhpStorm.
 * User: Maky
 * Date: 14.05.2019
 * Time: 11:29
 */

namespace ds1\admin_modules\human;


use ds1\core\ds1_base_controller;
use Symfony\Component\HttpFoundation\Request;

class human_controller  extends ds1_base_controller
{
    public function indexAction(Request $request, $page = "")
    {
        // zavolat metodu rodice, ktera provede obecne hlavni kroky a nacte parametry
        parent::indexAction($request, $page);

        // KONTROLA ZABEZPECENI - pro jistotu
        // test, jestli je uzivatel prihlasen, pokud NE, tak redirect na LOGIN
        $this->checkAdminLogged();

        // objekt pro praci s obyvateli
        $human = new human();
        $human->SetPDOConnection($this->ds1->GetPDOConnection());

        // AKCE
        // action - typ akce
        $action = $this->loadRequestParam($request,"action", "all", "obyvatele_list_all");
        //echo "action: ".$action;

        // vyhledavaci string nemam
        $search_string = $this->loadRequestParam($request,"search_string", "all", "");

        // nacist obyvatele, pokud mam
        $obyvatel_id = $this->loadRequestParam($request,"obyvatel_id", "all", -1);


        // univerzalni content params
        $content_params = array();
        $content_params["base_url"] = $this->webGetBaseUrl();
        $content_params["base_url_link"] = $this->webGetBaseUrlLink();
        $content_params["page_number"] = $this->page_number;
        $content_params["route"] = $this->route;        // mam tam orders, je to automaticky z routingu
        $content_params["route_params"] = array();
        $content_params["controller"] = $this;


        $defects [] = [1, 5, "Something"];
        //$defects [] = [-85, -33, "Hořííííííí"];
        $defects [] = [20, 20, "nové"];

        $content_params["defects"] = $defects;
        $content = "";

        // defaultni vysledek akce
        $result_msg = "";
        $result_ok = true;
        $content = $this->renderPhp(DS1_DIR_ADMIN_MODULES_FROM_ADMIN . "human/templates/admin_human_detail.inc.php",
                                        $content_params,
                                        true);

        // vypsat hlavni template
        $main_params = array();
        $main_params["content"] = $content;
        $main_params["result_msg"] = $result_msg;
        $main_params["result_ok"] = $result_ok;

        return $this->renderAdminTemplate($main_params);
        //return new Response("Controller pro obyvatele.");
    }

    }