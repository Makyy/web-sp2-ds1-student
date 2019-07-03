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

class human_controller extends ds1_base_controller
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

        $obyvatelId = $this->loadRequestParam($request, 'obyvatel_id', 'all', null);
        $obyvatel = $obyvatele->adminGetItemByID($obyvatelId);

        // AKCE
        // action - typ akce
        $action = $this->loadRequestParam($request, "action", "all", "");

        // univerzalni content params
        $content_params = array();
        $content_params["base_url"] = $this->webGetBaseUrl();
        $content_params["base_url_link"] = $this->webGetBaseUrlLink();
        $content_params["form_action"] = $this->makeUrlByRoute($this->route, array("obyvatel_id" => $obyvatelId, "action" => "save_points"));
        $content_params["page_number"] = $this->page_number;
        $content_params["route"] = $this->route;        // mam tam orders, je to automaticky z routingu
        $content_params["route_params"] = array();
        $content_params["controller"] = $this;

        // defaultni vysledek akce
        $main_params = array();
        $main_params["result_msg"] = "";
        $main_params["result_ok"] = true;

        // defaultní akce - list se seznamem defektů
        if ($action === "" || $action === "filter")
        {
            $filters = $this->loadRequestParam($request, "filter");

            $content_params["entities"] = $human->getDefectsList($filters);
            $content_params["form_filter_action"] = $this->makeUrlByRoute($this->route, array('action' => 'filter'));
            $content_params["filter"] = $filters;

            $content = $this->renderPhp(DS1_DIR_ADMIN_MODULES_FROM_ADMIN . "human/templates/admin_human_list.inc.php",
                $content_params,
                true);
            // vypsat hlavni template
            $main_params["content"] = $content;

            return $this->renderAdminTemplate($main_params);
        }

        // detail obvyatele
        if ($action === "detail")
        {
            $content_params["defects"] = $human->getDefectPointsByObyvatelId($obyvatelId);
            $content_params["name"] = $obyvatel["jmeno"] . " " . $obyvatel["prijmeni"];

            $content = $this->renderPhp(DS1_DIR_ADMIN_MODULES_FROM_ADMIN . "human/templates/admin_human_detail.inc.php",
                $content_params,
                true);

            $main_params["content"] = $content;

            return $this->renderAdminTemplate($main_params);
        }

        // přidání bodu
        if ($action === "add_point")
        {
            $x = $this->loadRequestParam($request, "x", "post");
            $y = $this->loadRequestParam($request, "y", "post");

            $result = $human->addDefectPoint($x, $y, $obyvatelId);

            $result = $result > 0 ? $result : false;

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

            $this->redirectUser($this->makeUrlByRoute($this->route, array("obyvatel_id" => $obyvatelId)));
        }

        // odstranění defektu z DB
        if ($action === "remove_point")
        {
            $defectId = $this->loadRequestParam($request, "defect_id", "post");

            // ID defektu nebylo vyplněno
            if (empty($defectId))
            {
                return new JsonResponse(false, 200);
            }

            $result = $human->deleteDefectById($defectId);

            return new JsonResponse($result, 200);
        }
    }

}