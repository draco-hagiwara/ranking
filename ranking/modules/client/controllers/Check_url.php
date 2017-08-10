<?php

class Check_url extends MY_Controller
{
//getjsonp.php

	public function __construct()
	{
		parent::__construct();

		$this->load->library('lib_auth');
		$this->lib_auth->check_session();


	}

	public function index()
	{


		$option = [
				CURLOPT_RETURNTRANSFER => true, //文字列として返す
				CURLOPT_TIMEOUT        => 3, // タイムアウト時間
		];

		$url = $_POST['check_url'];

		$ch = curl_init($url);
		curl_setopt_array($ch, $option);

		$json    = curl_exec($ch);
		$info    = curl_getinfo($ch);
		$errorNo = curl_errno($ch);

// 		print(CURLE_OK);
// 		print("/");
// 		print($errorNo);
// 		print("/");
// 		print_r($info['http_code']);
// 		print("/\n");


		// OK以外はエラーなので空白配列を返す
		if ($errorNo !== CURLE_OK) {
			// 詳しくエラーハンドリングしたい場合はerrorNoで確認
			// タイムアウトの場合はCURLE_OPERATION_TIMEDOUT


// 			print($errorNo);
// 			print("\n");

			$this->smarty->assign('curl_code', $errorNo);
			//return [];
		} else {
			$this->smarty->assign('curl_code', $errorNo);
		}

		// 200以外のステータスコードは失敗とみなし空配列を返す
		if ($info['http_code'] !== 200) {


// 			print_r($info['http_code']);
// 			print("\n");

			$this->smarty->assign('http_code', $info['http_code']);
			//return [];
		} else {
			$this->smarty->assign('http_code', $info['http_code']);
		}

		// 文字列から変換
		//$jsonArray = json_decode($json, true);

		//return $jsonArray;

		$this->view('check_url/index.tpl');



	}
}