<!doctype html>
<html lang="cs">
	<head>
		<meta charset="utf-8">
		<title>Automatické přihlášení</title>
	</head>
	<body style="display:table-cell;vertical-align:middle;height:100vh;width:100vw;margin:0;text-align:center">
		<?php
            $url = "https://www.example.com"; // TODO: url Bakalářů
            // parametr user = uživatelské jméno
            // parametr pwd = heslo
            // parametr u = url adresa stránky, na kterou chceme uživatele dostat

            $u = (!empty($_GET["u"]))?$_GET["u"]:"dash";
            if (!empty($_GET["user"]) && !empty($_GET["pwd"])) {
                $uid = $_GET["user"];
                $pwd = $_GET["pwd"];
                $stranka = "wlogin";
                $token = "*login*".$uid."*pwd*".$pwd."*sgn*"."ANDR";
                $resultUrl = $url."/login.aspx?hx=".sha512($token . date("Ymd"), true)."&pm=".$stranka."&ifaceVer=1";
                echo '<iframe class="dnone" onload="location.replace(\''.$url.'/next/'.$u.'.aspx\');" src="'.$resultUrl.'"></iframe>';
                echo '<div class="nacitani"></div>'; // TODO: načítací kolečko/animace
            } else {
                header("Location: $url/next/$u.aspx");
            }

            function sha512($input, $safe = false)
            {
                $hash = base64_encode(hash("sha512", $input, true));
                if ($safe) {
                    $hash = preg_replace('/[\\|\/]/', "_", preg_replace('/\+/', "-", $hash));
                }
                return $hash;
            }
        ?>
	</body>
</html>
