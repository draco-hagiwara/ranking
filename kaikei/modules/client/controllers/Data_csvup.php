<?php

class Data_csvup extends MY_Controller
{

	/*
	 *  ＣＳＶアップロード処理
	 */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

//         if ($_SESSION['c_login'] == TRUE)
//         {
//             $this->smarty->assign('login_chk', TRUE);
//             $this->smarty->assign('mem_Type',  $_SESSION['c_memType']);
//             $this->smarty->assign('mem_Seq',   $_SESSION['c_memSeq']);
//             $this->smarty->assign('mem_Grp',   $_SESSION['c_memGrp']);
//             $this->smarty->assign('mem_Name',  $_SESSION['c_memName']);
//         } else {
//             $this->smarty->assign('login_chk', FALSE);
//             $this->smarty->assign('mem_Type',  "");
//             $this->smarty->assign('mem_Seq',   "");
//             $this->smarty->assign('mem_Grp',   "");

//             redirect('/login/');
//         }

        $this->smarty->assign('up_mess01', NULL);

    }

    // 顧客データCSVのアップロード処理TOP
    public function customer()
    {

    	// バリデーション・チェック
    	$this->_set_validation();

    	$this->view('data_csvup/customer_csvup.tpl');

    }

    // 顧客データのCSV取込
    public function customer_csvup()
    {

    	$input_post = $this->input->post();

    	$up_errflg = FALSE;
    	$up_mess01 = '';

    	$this->config->load('config_comm');
    	$this->load->library('lib_csvparser');
    	$this->load->library('lib_validator');

    	switch ($input_post['_submit'])
    	{
    		case 'submit':

    			// CSVファイルのアップロード
    			$this->load->library('upload', $this->config->item('CUSTOMER_CSV_UPLOAD'));

    			// CSVファイルの保存
    			if ($this->upload->do_upload('cm_data'))
    			{
    				$up_mess01 .= ">> CSVファイルのアップロードに成功しました。<br>";
    				$_upload_data = $this->upload->data();
    			} else {
    				$up_mess01 .= ">> CSVファイルのアップロードに失敗しました。<br>";
    				$up_mess01 .= $this->upload->display_errors(' <p style="color:red;">', '</p>');

    				break;
    			}

    			try{
    				// CSVファイルの読み込み
    				$this->lib_csvparser->load($_upload_data['full_path'], TRUE, 1000, ',', '"');
    				$_csv_data = $this->lib_csvparser->parse();
    			} catch (Exception $e){
    				$up_mess03 .= "エラー発生:" . $e->getMessage();
    				break;
    			}

    			// CSVファイルのバリデーションチェック
    			$i = 0;
    			$j = 0;
    			foreach ($_csv_data as $key01 => $val01)
    			{
    				// 0:[案件ID],1:[クライアントID],2:[支払状況],3:[獲得ポイント],4:[調整ポイント],5:[領収(支払)金額],6:[納品日],7:[請求(支払)予定日],8:[領収(支払)日]
    				foreach ($val01 as $key02 => $val02)
    				{
    					if (($j <= 1) OR ($j == 3) OR ($j == 5))
    					{
    						// 数字型＆文字列の長さチェック
    						if ($this->lib_validator->checkRange($val02, 0, 99999999))
    						{
    						} else {
    							$up_mess01 .= $i + 1 . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
    							$up_errflg = TRUE;
    						}
    					}

    					if ($j == 2)
    					{
    						// 数字型＆文字列の長さチェック
    						if ($this->lib_validator->checkRange($val02, 0, 3))
    						{
    						} else {
    							$up_mess01 .= $i + 1 . "行目:「" . $key02 . "」項目で数字または範囲指定(0～3)エラー。<br>";
    							$up_errflg = TRUE;
    						}
    					}

    					if ($j == 4)
    					{
    						// int型文字列チェック
    						if ($this->lib_validator->checkInt($val02))
    						{
    							// 文字列の長さチェック
    							if ($this->lib_validator->checkLength($val02, 0, 9))
    							{
    							} else {
    								$up_mess01 .= $i + 1 . "行目:「" . $key02 . "」項目で文字数エラー。<br>";
    								$up_errflg = TRUE;
    							}
    						} else {
    							$up_mess01 .= $i + 1 . "行目:「" . $key02 . "」項目でint数字エラー。<br>";
    							$up_errflg = TRUE;
    						}
    					}

    					if (($j == 6) && ($val02 != ''))	// 任意
    					{
    						// 日付時間型チェック
    						if ($this->lib_validator->checkDateFormat($val02, 'Y-m-d H:i:s'))
    						{
    						} else {
    							$up_mess01 .= $i + 1 . "行目:「" . $key02 . "」項目で日付時間エラー。<br>";
    							$up_errflg = TRUE;
    						}
    					}

    					if (($j >= 7) && ($val02 != ''))	// 任意
    					{
    						// 日付型チェック
    						if ($this->lib_validator->checkDateFormat($val02, 'Y-m-d'))
    						{
    						} else {
    							$up_mess01 .= $i + 1 . "行目:「" . $key02 . "」項目で日付エラー。<br>";
    							$up_errflg = TRUE;
    						}
    					}
    					$j++;
    				}
    				$i++;
    				$j = 0;
    			}

    			if ($up_errflg == TRUE)
    			{
    				$up_mess01 .= ">> CSVファイルのバリデーションチェックに失敗しました。<br>";
    				break;
    			} else {
    				$up_mess01 .= ">> CSVファイルのバリデーションチェックに成功しました。<br>";
    			}

    			// CSVファイルでのUPDATE
    			$this->load->model('Project', 'pj', TRUE);
    			$cnt = 0;
    			foreach ($_csv_data as $key01 => $val01)
    			{

    				// 「案件情報」更新
    				$set_update_data = array();
    				$set_update_data['pj_id']              = $val01['案件ID'];							// 案件ID
    				//$set_update_data['pj_wi_point']      = $val01['獲得ポイント'];					// 獲得ポイント
    				$set_update_data['pj_wi_point_adjust'] = $val01['調整ポイント'];					// 調整ポイント
    				$set_update_data['pj_delivery_date']   = $val01['納品日'];							// 納品日
    				$set_update_data['pj_pay_status']      = $val01['支払状況'];						// 請求状況
    				$set_update_data['pj_pay_money']       = $val01['領収(支払)金額'];					// 請求金額
    				if ($val01['請求(支払)予定日'] != '')
    				{
    					$set_update_data['pj_pay_schedule']    = $val01['請求(支払)予定日'];			// 請求(予定)日
    				}
    				if ($val01['領収(支払)日'] != '')
    				{
    					$set_update_data['pj_pay_date']        = $val01['領収(支払)日'];				// 領収日
    				}
    				$set_update_data['pj_creator_id']      = $this->session->userdata('a_personalID');	// 作成者ID
    				$time = time();
    				$set_update_data['pj_update_date'] = date("Y-m-d H:i:s", $time);                    // 更新日

    				// UPDATE <- 'tb_project'
    				$this->pj->update_pj_posting($set_update_data);

    				$cnt++;
    			}

    			$up_mess01 .= ">> CSVファイルによる更新が完了しました。 " . $cnt . "件<br>";

    			break;

    		default:
    	}

    	// バリデーション・チェック
    	$this->_set_validation();                                            // バリデーション設定
    	//$this->form_validation->run();

    	$this->smarty->assign('up_mess01', $up_mess01);

    	$this->view('data_csvup/customer_csvup.tpl');

    }

    // 入金データCSVのアップロード処理TOP
    public function receive()
    {

    	// バリデーション・チェック
    	$this->_set_validation();

    	$this->view('data_csvup/receive_csvup.tpl');

    }

    // 入金データのCSV取込
    public function receive_csvup()
    {



    	print_r($_FILES['upfile']);
    	print("<br><br>");


    	// パラメータを正しい構造で受け取った時のみ実行
    	if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error'])) {

    		try {

    			/* ファイルアップロードエラーチェック */
    			switch ($_FILES['upfile']['error']) {
    				case UPLOAD_ERR_OK:         // =0
    					// エラー無し
    					break;
    				case UPLOAD_ERR_NO_FILE:    // =4
    					// ファイル未選択
    					throw new RuntimeException('File is not selected');
    				case UPLOAD_ERR_INI_SIZE:   // =1
    				case UPLOAD_ERR_FORM_SIZE:  // =2
    					// 許可サイズを超過
    					throw new RuntimeException('File is too large');
    				default:
    					throw new RuntimeException('Unknown error');
    			}

    			$tmp_name = $_FILES['upfile']['tmp_name'];
    			$detect_order = 'ASCII,JIS,UTF-8,CP51932,SJIS-win';
    			setlocale(LC_ALL, 'ja_JP.UTF-8');

    			/* 文字コードを変換してファイルを置換 */
    			$buffer = file_get_contents($tmp_name);
    			if (!$encoding = mb_detect_encoding($buffer, $detect_order, true)) {
    				// 文字コードの自動判定に失敗
    				unset($buffer);
    				throw new RuntimeException('Character set detection failed');
    			}
    			file_put_contents($tmp_name, mb_convert_encoding($buffer, 'UTF-8', $encoding));
    			unset($buffer);

    			/* トランザクション処理 */    		// トランザクション・START
	    		$this->db->trans_strict(FALSE);                                 		// StrictモードをOFF
	    		$this->db->trans_start();                                       		// trans_begin

//     			$db = DbManager::getConnection();
//     			$db->beginTransaction();

    			try {
    				$cnt = 0;
    				$fp = fopen($tmp_name, 'rb');
    				while ($row = fgetcsv($fp, 256, " ")) {								// デリミタ：「半角スペース」で判定
    					if ($cnt == 0) {
    						// 1行目はタイトルのためスキップ
    						$cnt = 1;
    						continue;
    					}
    					if ($row === array(null)) {
    						// 空行はスキップ
    						continue;
    					}



    					print_r($row);
    					print("<br>");




//     					if (count($row) !== 10) {
//     						// カラム数が異なる無効なフォーマット
//     						throw new RuntimeException('Invalid column detected');
//     					}
//     					if ($row["0"] == "" || $row["1"] == "3" | $row["4"] == "") {
//     						// 必須項目チェック
//     						throw new RuntimeException($cnt . 'row :: Invalid column not SPACE');
//     					} else {

//     						$params["values"]['dm_flg' ]          = 2 ;                             // ステータス
//     						$params["values"]['dm_server' ]       = $row["1"] ;                     // サーバ (123,A,B)
//     						$params["values"]['dm_serverid' ]     = $row["0"] ;                     // サーバ番号
//     						$params["values"]['dm_domain_mng' ]   = $row["6"] ;                     // ドメイン管理会社
//     						$params["values"]['dm_domain' ]       = $row["4"] ;                     // ドメイン
//     						$params["values"]['dm_url' ]          = "http://" . $row["4"] . "/" ;   // URL
//     						$params["values"]['dm_keyword' ]      = $row["3"] ;                     // キーワード
//     						$params["values"]['dm_tpl_no' ]       = $row["7"] ;                     // テンプレート番号
//     						$params["values"]['dm_tpl_color' ]    = $row["8"] ;                     // テンプレート色
//     						$params["values"]['dm_pr' ]           = "0" ;                           // PR
//     						$params["values"]['dm_ftp_folder' ]   = "WinSCP" ;                      // FTPフォルダ
//     						$params["values"]['dm_in_link1' ]     = $row["5"] ;                     // 被リンクページ１
//     						$params["values"]['dm_client_url1' ]  = $row["13"] ;                    // toクライアントサイト１
//     						$params["values"]['dm_linkup_date' ]  = $row["14"] ;                    // リンク貼り日
//     						$params["values"]['dm_linkdel_date' ] = $row["12"] ;                    // リンク集削除
//     						$params["values"]['dm_in_link2' ]     = $row["10"] ;                    // 被リンクページ２
//     						$params["values"]['dm_authori_url2' ] = $row["11"] ;                    // toオーソリティサイト２
//     						$params["values"]['dm_in_link3' ]     = $row["15"] ;                    // 被リンクページ３
//     						$params["values"]['dm_authori_url3' ] = $row["16"] ;                    // toオーソリティサイト３
//     						$params["values"]['dm_comment' ]      = $row["17"] ;                    // 備考
//     						$params["values"]['dm_reg_date'  ]    = date("Y-m-d H:i:s") ;
//     						$params["values"]['dm_mod_date'  ]    = date("Y-m-d H:i:s") ;

//     						$strError = $db->insert( 'dm_master' , $params["values"] ) ;
//     						// $result = Lib_Domain_management::insert($params);
//     						$executed = $cnt;
//     					}

    					$executed = $cnt;
    					$cnt++;

    				}

    				if (!feof($fp)) {
    					// ファイルポインタが終端に達していなければエラー
    					throw new RuntimeException('CSV parsing error');
    				}
    				fclose($fp);
//     				$db->commit();

    				// トランザクション・COMMIT
    				$this->db->trans_complete();                                    		// trans_rollback & trans_commit
    				if ($this->db->trans_status() === FALSE)
    				{
    					log_message('error', 'CLIENT::[Data_csvup -> receive_csvup()]：入金データ 読み込み処理 トランザクションエラー');
    				}


    			} catch (Exception $e) {
    				fclose($fp);

//     				$db->rollBack();
    				// トランザクション・COMMIT
    				$this->db->trans_complete();                                    		// trans_rollback & trans_commit
    				if ($this->db->trans_status() === FALSE)
    				{
    					log_message('error', 'CLIENT::[Data_csvup -> receive_csvup()]：入金データ 読み込み処理 トランザクションエラー');
    				}

    				throw $e;
    			}

    			/* 結果メッセージをセット */
    			if (isset($executed)) {
    				// 1回以上実行された
    				$up_mess01 = array('green', $cnt . ' row :: Import successful');
    			} else {
    				// 1回も実行されなかった
    				$up_mess01 = array('black', 'There were nothing to import');
    			}

    		} catch (Exception $e) {

    			/* エラーメッセージをセット */
    			$up_mess01 = array('red', $e->getMessage());

    		}
    	}


    	// バリデーション・チェック
    	$this->_set_validation();                                            // バリデーション設定
    	//$this->form_validation->run();

    	$this->smarty->assign('up_mess01', $up_mess01);

    	$this->view('data_csvup/receive_csvup.tpl');

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}

