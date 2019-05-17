<?php
/**
 * Created by PhpStorm.
 * User: Maky
 * Date: 14.05.2019
 * Time: 11:29
 */

namespace ds1\admin_modules\human;


use ds1\admin_modules\obyvatele\obyvatele;
use ds1\core\ds1_base_controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $obyvatele = new obyvatele();
        $obyvatele->SetPDOConnection($this->ds1->GetPDOConnection());

        //TODO: kontroly, že obyvatel existuje
        //TODO: list obyvatel, kterým je možné přiřadit defekt
        $obyvatelId = $this->loadRequestParam($request, 'obyvatel_id', 'all', "");

        // AKCE
        // action - typ akce
        $action = $this->loadRequestParam($request,"action", "all", "");

        // univerzalni content params
        $content_params = array();
        $content_params["base_url"] = $this->webGetBaseUrl();
        $content_params["base_url_link"] = $this->webGetBaseUrlLink();
        $content_params["page_number"] = $this->page_number;
        $content_params["route"] = $this->route;        // mam tam orders, je to automaticky z routingu
        $content_params["route_params"] = array();
        $content_params["controller"] = $this;
        $content_params["defects"] = $human->getDefectPointsByObyvatelId($obyvatelId);

        // defaultni vysledek akce
        $result_msg = "";
        $result_ok = true;
        $content = $this->renderPhp(DS1_DIR_ADMIN_MODULES_FROM_ADMIN . "human/templates/admin_human_detail.inc.php",
                                        $content_params,
                                        true);

        if ($action === "")
        {
            // vypsat hlavni template
            $main_params = array();
            $main_params["content"] = $content;
            $main_params["result_msg"] = $result_msg;
            $main_params["result_ok"] = $result_ok;

            return $this->renderAdminTemplate($main_params);
        }

        // přidání bodu
        if ($action === "add_point")
        {
            $array = array(
                "obyvatel_id" => $obyvatelId,
                "pos_x" => $request->get("x"),
                "pos_y" => $request->get("y"),
                'nazev' => "DEFEKT!"
            );

            // uložení bodíku
            $human->addDefectPoint($array);

            return new JsonResponse(null, 200);
        }
    }

    }