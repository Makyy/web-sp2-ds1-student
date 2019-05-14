<?php

    $modules_admin = array();

    // ************************************************************************************
    // **************   Modul obyvatele          ******************************************

    // novy modul
    $module = array();
    $module["name"] = "obyvatele";
    $module["title"] = "Obyvatelé";
    $module["route_name"] = "obyvatele";
    $module["route_path"] = "/plugin/$module[name]";
    $module["route"] = array("controller_name" => "obyvatele_controller", "controller_action" => "indexAction");
    $module["settings_file"] = "obyvatele_settings"; // bez koncovky a bez cesty

    // pridat modul
    $modules_admin[] = $module;

    // **************   KONEC Modul obyvatele          ************************************
    // ************************************************************************************

    // ************************************************************************************
    // **************   Modul pokoje        ***********************************************

    // novy modul
    $module = array();
    $module["name"] = "pokoje";
    $module["title"] = "Pokoje";
    $module["route_name"] = "pokoje";
    $module["route_path"] = "/plugin/$module[name]";
    $module["route"] = array("controller_name" => "pokoje_controller", "controller_action" => "indexAction");
    $module["settings_file"] = "pokoje_settings"; // bez koncovky a bez cesty

    // pridat modul
    $modules_admin[] = $module;

    // **************   KONEC Modul pokoje         ****************************************
    // ************************************************************************************

    // ************************************************************************************
    // **************   Modul dokumentace	***********************************************
    // novy modul
    $module = array();
    $module["name"] = "dokumentace_pacient";
    $module["title"] = "Dokumentace (student 1)";
    $module["route_name"] = "dokumentace_pacient";
    $module["route_path"] = "/plugin/$module[name]";
    $module["route"] = array("controller_name" => "dokumentace_pacient_controller", "controller_action" => "indexAction");
    // pridat modul
    $modules_admin[] = $module;
    // **************   KONEC Modul dokumentace        ************************************
    // ************************************************************************************

    // ************************************************************************************
    // **************   Modul správa uživatelů ***********************************************
    // novy modul
    $module = array();
    $module["name"] = "sprava_uzivatelu";
    $module["title"] = "Správa uživatelů (student 1)";
    $module["route_name"] = "sprava_uzivatelu";
    $module["route_path"] = "/plugin/$module[name]";
    $module["route"] = array("controller_name" => "sprava_uzivatelu_controller", "controller_action" => "indexAction");
    // pridat modul
    $modules_admin[] = $module;
    // **************   KONEC Modul správa uživatelů        ************************************
    // ************************************************************************************

    // ************************************************************************************
    // **************   Modul sluzby        ***********************************************

    // novy modul
    $module = array();
    $module["name"] = "sluzby";
    $module["title"] = "Služby (student 2)";
    $module["route_name"] = "sluzby";
    $module["route_path"] = "/plugin/$module[name]";
    $module["route"] = array("controller_name" => "sluzby_controller", "controller_action" => "indexAction");

    // pridat modul
    $modules_admin[] = $module;


    // ************************************************************************************
    // **************   Modul sluzby        ***********************************************

    // novy modul
    $module = array();
    $module["name"] = "human";
    $module["title"] = "Defekty obyvatel";
    $module["route_name"] = "human";
    $module["route_path"] = "/plugin/$module[name]";
    $module["route"] = array("controller_name" => "human_controller", "controller_action" => "indexAction");

    // pridat modul
    $modules_admin[] = $module;

// **************   KONEC Modul sluzby         ****************************************
    // ************************************************************************************


    return $modules_admin;