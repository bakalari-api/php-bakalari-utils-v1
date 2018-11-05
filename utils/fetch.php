<?php

    $bakaURL = "https://www.example.com"; // TODO: url Bakalářů
    // parametr u = uživatelské jméno
    // parametr t = polotoken = sha512($salt . $ikod . $typ . $pass)

    function fetch($url, $cookies = null, $returnCookie = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($cookies) {
            curl_setopt($ch, CURLOPT_COOKIE, implode(';', $cookies));
        }
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $result, 2);
        $end = strpos($header, 'Content-Type');
        $start = strpos($header, 'Set-Cookie');
        $parts = explode('Set-Cookie:', substr($header, $start, $end - $start));
        $cookies = array();
        foreach ($parts as $co) {
            $cd = explode(';', $co);
            if (!empty($cd[0])) {
                $cookies[] = $cd[0];
            }
        }
        curl_close($ch);
        if ($returnCookie) {
            return $cookies;
        }
        $adresa = explode('<div class="addressLine">', $body, 2)[1];
        $adresa = explode('<div class="addressLine">', $adresa, 3);
        $adresa = $adresa[0].$adresa[1];
        $adresa = preg_replace('/<[^>]*>/', '', $adresa);
        $adresa = preg_replace('/[\n\s]+/', ' ', $adresa);
        $adresa = preg_replace('/ Č.p. \/ č.o.:/', '', $adresa);
        $adresa = preg_replace('/ Část obce:/', '', $adresa);
        $adresa = preg_replace('/ (\S*?):/', ',', $adresa);
        $adresa = substr($adresa, 2);
        return $adresa;
    }

    function sha512($input, $safe = false)
    {
        $hash = base64_encode(hash("sha512", $input, true));
        if ($safe) {
            $hash = preg_replace('/[\\|\/]/', "_", preg_replace('/\+/', "-", $hash));
        }
        return $hash;
    }

    if (!empty($_GET["u"]) && !empty($_GET["t"])) {
        $token = "*login*".$_GET["u"]."*pwd*".str_replace(" ", "+", urldecode($_GET["t"]))."*sgn*"."ANDR";
        $ckk = fetch($bakaURL."/login.aspx?hx=".sha512($token . date("Ymd"), true)."&pm=wlogin", null, true);
        echo fetch($bakaURL."/next/login.aspx?ReturnUrl=%2fnext%2fosobni_udaje.aspx", $ckk);
    }
