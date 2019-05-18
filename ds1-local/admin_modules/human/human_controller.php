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
    const PLUGIN_URL = "plugin/human";

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
        $content_params["form_action"] = $this->webGetBaseUrlLink() . self::PLUGIN_URL . "?obyvatel_id=" . $obyvatelId . "&action=save_points";
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

        // vypsat hlavni template
        $main_params = array();
        $main_params["content"] = $content;
        $main_params["result_msg"] = $result_msg;
        $main_params["result_ok"] = $result_ok;

        if ($action === "")
        {
            return $this->renderAdminTemplate($main_params);
        }

        // přidání bodu
        if ($action === "add_point")
        {
            $x = $this->loadRequestParam($request, "x", "post");
            $y = $this->loadRequestParam($request, "y", "post");

            $result = $human->addDefectPoint($x, $y, $obyvatelId);

            // vrací ID vloženého záznamu
            return new JsonResponse($result, 200);
        }

        // uložení názvů defektů
        if ($action === "save_points")
        {
            $formValues = $this->loadRequestParam($request, "def", "post");
            foreach ($formValues as $key => $value)
            {
                $human->updateDefectName($key, $value);
            }

            $this->redirectUser($this->webGetBaseUrlLink() . self::PLUGIN_URL . "?obyvatel_id=" . $obyvatelId);
        }
    }

    }