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
		""  => " -- 選択してください -- ",
		"0" => "営業",
		"1" => "管理者（営業）",
		"2" => "利用者",
);









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



// Pagination 設定:1ページ当たりの表示件数
// ※ ～/system/libraries/Pagination.php に不具合あり
$config['PAGINATION_PER_PAGE'] = '5';


/* End of file config_comm.php */
/* Location: ./application/config/config_comm.php */