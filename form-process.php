<?php

$apiUrlTest = "https://intouch2.acema.cz/intouch/api/";
$apiUrlProd = "https://intouch.acema.cz/intouch/api/";
$apiLogin = "api";
$apiPass = "Acema2020";

$mailjetApikeyPublic = "a42e3f3701c8109f6e1e37e8ee4bbf74";
$mailjetApikeyPrivate = "9292c2b1a3b0ee022cbdcad38d27866f";
$mailjetSmsToken = "f2e73ef592d545c7bd5b5b6fbd0e90bb";

$errorMSG = "";

if (empty($_POST["type"])) {
    $errorMSG = "Něco se pokazilo :(";
} else {
    $type = $_POST["type"];
}

if (empty($_POST["name"])) {
    $errorMSG = "Jméno je povinná informace";
} else {
    $name = explode(" ", trim($_POST["name"]));
}

if (empty($_POST["email"])) {
    $errorMSG .= "Email je nezbytné vyplnit ";
} else {
    $email = $_POST["email"];
}

if (empty($_POST["phone"])) {
    $errorMSG .= "Telefon je nezbytné vyplnit ";
} else {
    $phone = $_POST["phone"];
}
$adress = $_POST["adress"];

$option = $_POST["option"];
if ($option == "Dum") {
    $optionId = 2;
} elseif($option == "Byt") {
    $optionId = 1;
} else {
    $optionId = 0;
}

$consent1 = $_POST["consent1"];
$consent2 = $_POST["consent2"];

if($errorMSG == "") {

    $dataParty = array(
        "type" => "INDIVIDUAL",
        "language" => "cs",
        "enteredFrom" => "www.unidebt.cz",
        "company" => null,
        "firstname" => $name[0] ? $name[0] : "",
        "surname" => $name[1] ? $name[1] : "",
        "ownerUser" => "admin",
        "ownerGroup" => "ACEMA",
        "eaddresses" => array(
            array(
                "type" => 1,
                "value" => $email
            ),
            array(
                "type" => 0,
                "value" => $phone
            )
        )
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataParty));
    curl_setopt($curl, CURLOPT_URL, $apiUrlTest . "parties/import");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "$apiLogin:$apiPass");
    $result = curl_exec($curl);
    if (!$result) {
        $errorMSG = "Něco se pokazilo :(";
    }
    curl_close($curl);
    $resultStd = json_decode($result);

    $dataOpportunity = array(
        "name" => "Žádost o půjčku",
        "stage" => "dvpr",
        "responsible" => "admin",
        "customer" => isset($resultStd->id) ? (int)$resultStd->id : null,
        //"castka" => "apro",
        "typ_formulare" => 1,
        "vzkaz" => "Jméno: " . $dataParty["firstname"] . " " . $dataParty["surname"] . " | Email: " . $email . " | Telefon: " . $phone,
        "ucel" => $type,
        "origin" => "www.unidebt.cz",
        "vlozeno" => date("Y-m-d") . "T" . date("H:i:s"),
        //"comment" => null,
        //"utm_campaign" => $utm_campaign,
        //"utm_content" => $utm_content,
        //"utm_medium" => $utm_medium,
        //"utm_source" => $utm_source,
        //"utm_term" => $utm_term,
    );

    if ($type == 130) {
        $dataOpportunity["nemovitost"] = $optionId;
        $dataOpportunity["vzkaz"] .= " | Adresa nemovitosti: " . $adress;
    } else if ($type == 160) {
        $dataOpportunity["nemovitost"] = $optionId;
        $dataOpportunity["vzkaz"] .= " | Adresa nemovitosti: " . $adress;
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($dataOpportunity));
    curl_setopt($curl, CURLOPT_URL, $apiUrlTest . "opportunities");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "$apiLogin:$apiPass");
    $result = curl_exec($curl);
    if (!$result) {
        $errorMSG = "Něco se pokazilo :(((()";
    }
    curl_close($curl);
    $resultStd = json_decode($result);

    if (isset($resultStd->problems)) {
        //if(strstr($resultStd->problems[0]->message, "SQLIntegrityConstraintViolationException") != -1) {
        //    $errorMSG = "Omlouváme se, ale Váš kontakt již v databázi evidujeme.";
        //} else {
            $errorMSG = "Něco se pokazilo :(";
        //}
    }


    $data = json_encode(array(
        "Messages" => array(
            array(
                "To" => array(
                    array(
                        "Email" => $email
                    ),
                ),
                "TemplateID" => 1629071,
                "TemplateLanguage"=> true,
                "Variables" => array(
                    "product" => $dataOpportunity["name"],
                    "name" => $dataParty["firstname"] . " " . $dataParty["surname"],
                    "email" => $email,
                    "phone" => $phone,
                )
            )
        )
    ));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.mailjet.com/v3.1/send");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERPWD, $mailjetApikeyPublic.":".$mailjetApikeyPrivate);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($ch), true);
}

if ($errorMSG == "") {
    echo "success";
} else {
    echo $errorMSG;
}

?>