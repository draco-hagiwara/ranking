<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Common STATUS
| -------------------------------------------------------------------
*/


// アカウントステータス
$config['ACCOUNT_AC_STATUS'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "有効",
		"1" => "無効",
		"9" => "削除",
);




// データ取得の監視
$config['MONITORING_STATUS'] =
array(
		"neutral"     => 0,
		"get_start_g" => 1,
		"get_end_g"   => 2,
		"get_start_y" => 3,
		"get_end_y"   => 4,
		"rank_start"  => 5,
		"rank_end"    => 6,
);


// 検索結果データのステータス
$config['SEARCH_DATA_STATUS'] =
array(
		"success" => 0,
		"error"   => 1,
);

// 検索結果データのステータス
$config['SEARCH_RANKING_FLG'] =
array(
		"no-write" => 0,
		"writing"  => 1,
);







// クライアントステータス
$config['CLIENT_CL_STATUS'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "運用中",
		"1" => "一時停止",
		"8" => "解約",
		"9" => "削除",
);


/* End of file config_status.php */
