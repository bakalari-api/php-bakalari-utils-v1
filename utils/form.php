<?php
    if (!empty($_POST["user"]) && !empty($_POST["pass"]) && !empty($_POST["url"])) {
        header("Content-type: text/xml");
        function sha512($input, $safe = false)
        {
            $hash = base64_encode(hash("sha512", $input, true));
            if ($safe) {
                $hash = preg_replace('/[\\|\/]/', "_", preg_replace('/\+/', "-", $hash));
            }
            return $hash;
        }

        $user = $_POST["user"];
        $pass = $_POST["pass"];
        $url = $_POST["url"];
        $stranka = !empty($_POST["stranka"])?$_POST["stranka"]:"all";
        $textXml = @file_get_contents($url."/login.aspx?gethx=".$user."&ifaceVer=1");
        if ($textXml !== false) {
            $xmlDoc = new DOMDocument();
            $xmlDoc->loadXML($textXml);
            $typ = $xmlDoc->getElementsByTagName("typ")->item(0)->childNodes->item(0);
            $typ = $xmlDoc->saveXML($typ);
            $ikod = $xmlDoc->getElementsByTagName("ikod")->item(0)->childNodes->item(0);
            $ikod = $xmlDoc->saveXML($ikod);
            $salt = $xmlDoc->getElementsByTagName("salt")->item(0)->childNodes->item(0);
            $salt = $xmlDoc->saveXML($salt);
            $token = "*login*".$user."*pwd*".sha512($salt . $ikod . $typ . $pass)."*sgn*"."ANDR";
            $resultUrl = $url."/login.aspx?hx=".sha512($token . date("Ymd"), true)."&pm=".$stranka."&ifaceVer=1";
            $content = @file_get_contents($resultUrl);
            if ($content !== false) {
                if (isset($_GET["r"])) {
                    header("Location: $resultUrl");
                } elseif (isset($_GET["u"])) {
                    header("Content-type: text/plain");
                    echo $resultUrl."\n".sha512($salt . $ikod . $typ . $pass);
                } else {
                    echo $content;
                }
            } else {
                echo "<error><code>1</code><message>Chyba!</message></error>";
            }
        } else {
            echo "<error><code>2</code><message>Chyba!</message></error>";
        }
    } else {
        ?><!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Bakaláři API</title>
	</head>
	<body style="text-align: center; font-family: Calibri, Arial, sans-serif;">
		<form method="post" style="display: inline-block; text-align: left;">
			<table>
				<tr><td style="text-align: right;"><label for="user">Uživatelské jméno:</label></td><td><input type="text" name="user" id="user"></td></tr>
				<tr><td style="text-align: right;"><label for="pass">Heslo:</label></td><td><input type="password" name="pass" id="pass"></td></tr>
				<tr><td style="text-align: right;"><label for="url">URL serveru Bakalářů:</label></td><td><input type="url" name="url" id="url"></td></tr>
				<tr><td style="text-align: right;"><label for="stranka">Stránka:</label></td><td>
					<select name="stranka" id="stranka">
						<option value="login">login</option>
						<option value="home">home</option>
						<option value="znamky">znamky</option>
						<option value="rozvrh">rozvrh</option>
						<option value="rozvrhnext">rozvrhnext</option>
						<option value="rozvrhakt">rozvrhakt</option>
						<option value="rozvrhperm">rozvrhperm</option>
						<option value="ukoly">ukoly</option>
						<option value="akce">akce</option>
						<option value="suplovani">suplovani</option>
						<option value="predmety">predmety</option>
						<option value="vyuka">vyuka</option>
						<option value="absence">absence</option>
						<option value="pololetni">pololetni</option>
						<option value="predvidac">predvidac</option>
						<option value="timeline">timeline</option>
						<option value="odeslane">odeslane</option>
						<option value="prijate">prijate</option>
						<option value="nastenka">nastenka</option>
						<option value="setread">setread</option>
						<option value="setok">setok</option>
						<option value="komsend">komsend</option>
						<option value="komenslisty">komenslisty</option>
						<option value="komdel">komdel</option>
						<option value="priloha">priloha</option>
						<option value="all" selected="selected">all</option>
						<option value="ucitelrozvrh">ucitelrozvrh</option>
						<option value="ucitelrozvrhnext">ucitelrozvrhnext</option>
						<option value="ucitelrozvrhakt">ucitelrozvrhakt</option>
						<option value="ucitelrozvrhperm">ucitelrozvrhperm</option>
						<option value="ucitelpredmety">ucitelpredmety</option>
						<option value="ucitelakce">ucitelakce</option>
						<option value="ucitelsuplovani">ucitelsuplovani</option>
						<option value="ucitelall">ucitelall</option>
						<option value="classification">classification</option>
						<option value="classificationMarks">classificationMarks</option>
						<option value="tkday">tkday</option>
						<option value="tkedit">tkedit</option>
						<option value="tksave">tksave</option>
						<option value="tksaveabsent">tksaveabsent</option>
						<option value="interfaces">interfaces</option>
					</select>
				</td></tr>
				<tr><td style="text-align: center;" colspan="2"><input type="submit" value="Odeslat"><input type="reset" value="Zrušit"></td></tr>
			</table>
		</form>
	</body>
</html><?php
    } ?>
