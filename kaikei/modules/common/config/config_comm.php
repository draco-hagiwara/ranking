<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Common SETTINGS
| -------------------------------------------------------------------
*/

// ログインメンバー
$config['LOGIN_CLIENT']   = 'client';                               // クライアント
$config['LOGIN_ADMIN']    = 'admin';                                // 管理者



// ユーザ種類
$config['ACCOUNT_AC_TYPE'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "一般ユーザ",
		"1" => "管理者",
);


// 回収サイト
$config['CUSTOMER_CM_COLLECT'] =
array(
		""  => " -- 選択してください -- ",
		"1" => "月末締め当月末",									// 'last day of this months'
		"2" => "月末締め翌月末",									// 'last day of next months'
		"3" => "月末締め翌々月10日",
		"4" => "月末締め翌々月15日",
		"5" => "月末締め翌々月25日",
		"6" => "月末締め翌々月末",
		"0" => "指定なし",
);

// 口座種別
$config['CUSTOMER_CM_KIND'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "普通",
		"1" => "当座",
);

// クライアント 顧客データCSVアップロード
$config['CUSTOMER_CSV_UPLOAD'] =
array(
		"upload_path"   => '../user_data/csv_up/',					// ドキュメントルートからの相対パス
		"allowed_types" => 'csv',									// 許容するファイルのMIMEタイプを設定
		"overwrite"     => TRUE,									// ファイルは上書き
		"max_size"      => '10000',									// 許容する最大ファイルサイズをKB単位で設定
);



// 案件：消費税有無
$config['PROJECT_PJ_TAXOUT'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "税抜",
		"1" => "税込",
);

// 案件：消費税計算方法
$config['PROJECT_PJ_TAX'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "切り上げ",
		"1" => "切り捨て",
		"2" => "四捨五入",
);

// 案件：課金方式
$config['PROJECT_PJ_ACCOUNTING'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "固定",
		"1" => "成果",
		"2" => "固定+成果",
);

// 案件：対象言語
$config['PROJECT_PJ_LANGUAGE'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "日本語",
		"1" => "英語",
);

// 案件：順位取得対象
$config['PROJECT_PJ_TARGET'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "ヴェニス無効",
		"1" => "スマホ",
		"2" => "地域指定",
);

// 案件：URL一致方式
$config['PROJECT_PJ_URLMATCH'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "完全一致",
		"1" => "前方一致",
		"2" => "ドメイン一致",
);

// // 案件：回収サイクル
// $config['PROJECT_PJ_COLLECT'] =
// array(
// 		""  => " -- 選択してください -- ",
// 		"1" => "月末締め当月末",									// 'last day of this months'
// 		"2" => "月末締め翌月末",									// 'last day of next months'
// 		"3" => "月末締め翌々月10日",
// 		"4" => "月末締め翌々月15日",
// 		"5" => "月末締め翌々月末",
// 		"0" => "指定なし",
// );

// 請求書発行対象企業：ラベンダー固定 >> ac_cl_seq = 2
$config['PROJECT_CL_SEQ']       = '2';




// 請求書発行：消費税
$config['INVOICE_TAX']          = '0.08';

// 請求書発行：消費税計算方法（0=切り上げ / 1=切り捨て / 2=四捨五入）
$config['INVOICE_TAX_CAL']      = '2';

// 請求書発行：消費税有無（0=税抜 / 1=税込）
$config['INVOICE_TAXOUT']       = '0';

// 請求書発行：発行番号（接頭語）
$config['INVOICE_ISSUE_NUM']    = 'LU';

// 案件：課金方式 <- 個別作成時
$config['INVOICE_ACCOUNTING'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "A : 通常（固定or成果）",
		"1" => "B : 前受取",
		"2" => "C : 赤伝票",
);

// 請求書発行方式
$config['INVOICE_METHOD'] =
array(
		""  => " -- 選択してください -- ",
		"0" => "一括発行",
		"1" => "個別発行",
);






// 売上データ 表示方法
$config['SALES_SA_DISPLINE'] =
array(
		"0" => "日別表示",
		"1" => "金額集計(売上日毎)",
		"2" => "金額集計(会社毎)",
		"3" => "金額集計(担当営業毎)",
);

// 売上データ グラフ表示色
$config['SALES_GRAPH_R'] =
array(
		"0" => "54",
		"1" => "0",
		"2" => "255",
		"3" => "255",
		"4" => "0",
		"5" => "0",
		"6" => "255",
		"7" => "95",
		"8" => "106",
		"9" => "165",
);
$config['SALES_GRAPH_G'] =
array(
		"0" => "162",
		"1" => "255",
		"2" => "165",
		"3" => "182",
		"4" => "128",
		"5" => "0",
		"6" => "215",
		"7" => "158",
		"8" => "90",
		"9" => "42",
);
$config['SALES_GRAPH_B'] =
array(
		"0" => "220",
		"1" => "127",
		"2" => "0",
		"3" => "193",
		"4" => "0",
		"5" => "128",
		"6" => "0",
		"7" => "160",
		"8" => "205",
		"9" => "42",
);



// 債権データ 表示方法
$config['RECEIVABLE_RV_DISPLINE'] =
array(
		"0" => "債権     表示",
		"1" => "債権履歴 表示",
		"2" => "入金情報 表示",
);

// 入金データCSVアップロード
$config['RECEIVABLE_CSV_UPLOAD'] =
array(
		"upload_path"   => '../user_data/csv_up/',					// ドキュメントルートからの相対パス
		"allowed_types" => 'csv',									// 許容するファイルのMIMEタイプを設定
		"overwrite"     => TRUE,									// ファイルは上書き
		"max_size"      => '10000',									// 許容する最大ファイルサイズをKB単位で設定
);






// ログイン：管理者クライアントSEQ
$config['LOGIN_CLIENT_SEQ']       = '1';


// ログインロック：失敗回数
$config['LOGIN_LOCK_CNT']         = '5';							// 回数
// ログインロック：制限時間
$config['LOGIN_LOCK_LIMITTIME']   = '120';							// 「分」指定
// ログインロック：解除時間
$config['LOGIN_LOCK_RELEASETIME'] = '120';							// 「分」指定



// ログ：ユーザ種類
$config['LOG_USER_TYPE'] =
array(
		"1" => "System管理者",
		"2" => "Adminユーザ",
		"3" => "Clientユーザ",
		"4" => "会員",
		"5" => "ビジター",
);



// Pagination 設定:1ページ当たりの表示件数
// ※ ～/system/libraries/Pagination.php に不具合あり
$config['PAGINATION_PER_PAGE'] = '25';


/* End of file config_comm.php */
/* Location: ./application/config/config_comm.php */