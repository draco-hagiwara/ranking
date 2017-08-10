<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Common SETTINGS
| -------------------------------------------------------------------
*/

// ログインメンバー
$config['LOGIN_CLIENT']   = 'client';                     // クライアント
$config['LOGIN_ADMIN']    = 'admin';                      // 管理者



// SEOユーザ種類
$config['ACCOUNT_AC_TYPE'] =
array(
		"0" => "利用者",
		"1" => "管理者",
		//"9" => "クライアント管理者",
);




/*
 * 検索結果取得API 関連パラメタ
 */
$config['API_URL'] = 'http://www.api-platform.info/SERPs.php';				// URL
$config['API_CODE'] = 'HI14tkWzAUZUj0DMwHpN53jJ7TehFg2h';					// 固定
$config['API_SEARCH_ENGINE'] =												// 検索エンジン
array(
		"0" => "google",
		"1" => "yahoo",
);
$config['API_DEVICE'] =														// 取得対象デバイス
array(
		"0" => "pc",
		"1" => "mobile",
);
$config['API_PAGE'] = 														// 取得ページ数 x 10
array(
		"0" => 10,
		"1" => 20,
		"2" => 30,
);


$config['API_CASHE'] = 0;													// キャッシュを使用する場合の有効範囲（秒）・デフォルト=0
$config['API_DEBAG'] = 0;													// 1=デバッグ（var_dumpで配列出力）








// ログイン：管理者クライアントSEQ
$config['LOGIN_CLIENT_SEQ']       = '1';


// ログインロック：失敗回数
$config['LOGIN_LOCK_CNT']         = '5';									// 回数)
// ログインロック：制限時間
$config['LOGIN_LOCK_LIMITTIME']   = '120';									// 「分」指定
// ログインロック：解除時間
$config['LOGIN_LOCK_RELEASETIME'] = '120';									// 「分」指定



// ログ：ユーザ種類
$config['LOG_USER_TYPE'] =
array(
		"1" => "System管理者",
		"2" => "Adminユーザ",
		"3" => "Clientユーザ",
		"4" => "会員",
		"5" => "ビジター",
);





// キーワード情報：受注データCSVアップロード
$config['PROJECT_CSV_UPLOAD'] =
array(
		"upload_path"   => '../user_data/csv_up/',                    // ドキュメントルートからの相対パス
		"allowed_types" => 'csv',                                     // 許容するファイルのMIMEタイプを設定
		"overwrite"     => TRUE,                                      // ファイルは上書き
		"max_size"      => '10000',                                   // 許容する最大ファイルサイズをKB単位で設定
);

// キーワード情報：CSVアップロード
$config['KWLIST_CSV_UPLOAD'] =
array(
		"upload_path"   => '../user_data/csv_up/',                    // ドキュメントルートからの相対パス
		"allowed_types" => 'csv',                                     // 許容するファイルのMIMEタイプを設定
		"overwrite"     => TRUE,                                      // ファイルは上書き
		"max_size"      => '10000',                                   // 許容する最大ファイルサイズをKB単位で設定
);

// Location Criteria情報：CSVアップロード
$config['CRITERIA_CSV_UPLOAD'] =
array(
		"upload_path"   => '../user_data/csv_up/',                    // ドキュメントルートからの相対パス
		"allowed_types" => 'csv',                                     // 許容するファイルのMIMEタイプを設定
		"overwrite"     => TRUE,                                      // ファイルは上書き
		"max_size"      => '10000',                                   // 許容する最大ファイルサイズをKB単位で設定
);




// メール送信：管理者
$config['MAIL_ADMIN_ADDR'] = 's_hagiwara@dracoexpress.com';
$config['MAIL_SEND_ADDR']  = 'auto-reply@ranking.dev.local';




// Pagination 設定:1ページ当たりの表示件数
// ※ ～/system/libraries/Pagination.php に不具合あり
$config['PAGINATION_PER_PAGE'] = '10';


/* End of file config_comm.php */
/* Location: ./application/config/config_comm.php */