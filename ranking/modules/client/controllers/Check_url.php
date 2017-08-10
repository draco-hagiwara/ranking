<?php

class Check_url extends MY_Controller
{
//getjsonp.php

	public function index()
	{

// 				print_r($_GET);
// 				print("<br><br>");

// 				$get_file = readfile($_GET["url"]);
// 				print($get_file);
// 				print("<br><br>");
// 				exit;


		//if(isset($_GET["url"])) readfile($_GET["url"]);


// 		if(isset($_GET["url"]) && isset($_GET["cb"])){
// 			header("Content-type: text/javascript;");
// 			echo $_GET["cb"]."( { data : '";
// 			echo str_replace("'","\'", str_replace("\n","\\n",file_get_contents($_GET["url"])));

// 			//ファイルを読込み、改行は\\nに変換、クォートをエスケープ。
// 			echo "'});";
// 		}






		$url = $_GET["url"];
		$xml = file_get_contents($url);
// 		header("Content-type: application/xml; charset=UTF-8");
// 		print $xml;


		$this->smarty->assign('xml_print',         $xml);

		$this->view('check_url/index.tpl');



	}
}