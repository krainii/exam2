<?php
AddEventHandler("main", "OnBeforeEventAdd", array("Ex2", "feedback_2_51"));
AddEventHandler("main", "OnBuildGlobalMenu", array("Ex2", "content_editor_menu_2_95"));


class Ex2 {
    function feedback_2_51 (&$event, &$lid, &$arFields) {
        if ($event == "FEEDBACK_FORM") {
            global $USER;
            if ($USER->isAuthorized()) {
                $arFields["AUTHOR"] = GetMessage("EX2_51_AUTH_USER", array(
                    "#ID#" => $USER->GetID(),
                    "#LOGIN#" => $USER->GetLogin(),
                    "#NAME#" =>  $USER->GetFullName(),
                    "#NAME_FORM#" => $arFields["AUTHOR"]
                    )
                );
            } else {
                $arFields["AUTHOR"] = GetMessage("EX2_51_NO_AUTH_USER", array(
                        "#NAME_FORM#" => $arFields["AUTHOR"]
                    )
                );
            }
            CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => GetMessage("EX2_51_REPLACEMENT"),
                "MODULE_ID" => "main",
                "ITEM_ID" => $event,
                "DESCRIPTION" => GetMessage("EX2_51_REPLACEMENT") . '-' . $arFields["AUTHOR"],
            ));
        }
    }
    function content_editor_menu_2_95(&$aGlobalMenu, &$aModuleMenu) {
        $bIsAdmin = false;
        $bIsManager = false;
        global $USER;
        $oUserGroup = CUSER::GetuserGroupList($USER->GetID());
        $iContentGroupID = CGroup::GetList(
            $by = "c_sort",
            $order = "asc",
            array(
                "STRING_ID" => "content_editor"
            )
        )->Fetch()["ID"];
        while ($arGroup = $oUserGroup -> Fetch()) {
            if ($arGroup["GROUP_ID"] == 1) {
                $bIsAdmin = true;
            }

            if ($arGroup["GROUP_ID"] == $iContentGroupID) {
                $bIsManager = true;
            }
        }
        if (!$bIsAdmin && $bIsManager) {
            foreach ($aModuleMenu as $key => $arItem) {
                if ($arItem["items_id"] == "menu_iblock_/news") {
                    $aModuleMenu = [$arItem];

                    foreach ($arItem["items"] as $childItem) {

                        if ($childItem["items_id"] == "menu_iblock_/news/1") {
                            $aModuleMenu[0]["items"] = [$childItem];
                            break;
                        }
                    }
                    break;
                }
            }
            $aGlobalMenu  = ["global_menu_content" => $aGlobalMenu["global_menu_content"]];
        }
    }
}