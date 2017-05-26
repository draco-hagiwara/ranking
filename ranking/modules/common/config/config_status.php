<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Common STATUS
| -------------------------------------------------------------------
*/


// アカウントステータス
$config['ACCOUNT_AC_STATUS'] =
array(
		"0" => "有効",
		"1" => "無効",
		"9" => "削除",
);


// クライアントステータス
$config['CLIENT_CL_STATUS'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "運用中",
		"1" => "一時停止",
		"8" => "解約",
		"9" => "削除",
		//"99" => "管理者",
);


// キーワード管理:ステータス
$config['KEYWORD_KW_STATUS'] =
array(
		"1" => "有効",
		"0" => "無効",
);

// キーワード管理:URLマッチタイプ
$config['KEYWORD_KW_MATCHTYPE'] =
array(
		"0" => "完全一致",
		"1" => "前方一致",
		"2" => "ドメイン一致",
		"3" => "ルートドメイン一致",
);

// キーワード管理:検索エンジン
$config['KEYWORD_KW_ENGINE'] =
array(
		"0" => "Google",
		"1" => "Yahoo!",
);

// キーワード管理:対象デバイス
$config['KEYWORD_KW_DEVICE'] =
array(
		"0" => "ＰＣ版",
		"1" => "モバイル版",
);

// キーワード管理:最大取得順位
$config['KEYWORD_KW_MAXPOSITION'] =
array(
		"0" => "100件",
		"1" => "200件",
		"2" => "300件",
);

// キーワード管理:1日の取得回数
$config['KEYWORD_KW_TRYTIMES'] =
array(
		"0" => "１回",
		"1" => "２回",
		"2" => "３回",
);

// キーワード管理:設定情報の反映有無
$config['KEYWORD_REFLECTION'] =
array(
		"0" => "反映させない",
		"1" => "同一URL配下に反映させる",
		"2" => "同一ドメイン配下に反映させる",
		"3" => "同一ルートドメイン配下に反映させる",
);




// キーワードTOP:グラフ表示期間
$config['KEYWORD_TERM'] =
array(
		"0" => "1ヶ月間",
		"1" => "3ヶ月間",
		"2" => "6ヶ月間",
		"3" => "1週間",
);







// データ取得の監視
$config['MONITORING_STATUS'] =
array(
		"neutral" => 0,
		"start"   => 1,
		"end"     => 2,
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




// グループ＆タグのステータス
$config['GROUPTAG_GT_TYPE'] =
array(
		"group" => 0,
		"tag"   => 1,
);



// イレギュラー処理でのデータ書換えステータス
$config['GROUPTAG_GT_TYPE'] =
array(
		"0" => "全順位データ書換(UPDATE)",
		"1" => "不足分の順位データ書換(UPDATE)",
		"2" => "個別指定での順位データ書換(UPDATE)",
		"3" => "全順位データ書換(INSERT)",
);



/* End of file config_status.php */
