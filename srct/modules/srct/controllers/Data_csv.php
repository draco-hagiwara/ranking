<?php

class Data_csv extends MY_Controller
{

    /*
     *  ＣＳＶアップロード処理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('up_mess', NULL);
        $this->smarty->assign('up_mess02', NULL);
        $this->smarty->assign('up_mess03', NULL);
        $this->smarty->assign('dl_mess', NULL);

    }

    // 顧客データCSVのアップロード処理TOP
    public function project()
    {

    	// URL直打ち禁止
    	if ($_SESSION['c_memType'] >= 2)
    	{
    		show_404();
    	}

        // バリデーション・チェック
        $this->_set_validation();

        $this->view('data_csv/project_csvup.tpl');

    }

    // キーワード情報データ（ラベンダー専用）のCSV取込
    public function project_csvup()
    {

    	set_time_limit(0);
    	/*
    	 * /opt/lampp/etc/php.ini
    	 *   memory_limit=128M
    	 */
    	ini_set('memory_limit', '128M');

        $input_post = $this->input->post();

        $up_errflg = FALSE;
        $up_mess   = '';

        // **********************************
        $this->load->helper('form');
        // **********************************
        $this->config->load('config_comm');
        $this->load->library('lib_csvparser');
        $this->load->library('lib_validator');


        // CSVファイルのアップロード
        $this->load->library('upload', $this->config->item('PROJECT_CSV_UPLOAD'));

        // CSVファイルの保存
        if ($this->upload->do_upload('kw_data'))
        {
        	$up_mess .= "<br><font color=blue>>> CSVファイルの読み込みに成功しました。</font><br>";
        	$up_mess .= "<br><font color=blue>>> CSVファイルのバリデーションチェックを開始しました。</font><br><br>";
        	$_upload_data = $this->upload->data();
        } else {
        	$up_mess .= "<br><font color=red>>> CSVファイルの読み込みに失敗しました。</font><br><br>";
        	$up_mess .= $this->upload->display_errors(' <p style="color:red;">', '</p>');

        	$this->smarty->assign('up_mess', $up_mess);
        	$this->view('data_csv/project_csvup.tpl');
        	return;
        }

        try{
        	// CSVファイルの読み込み
        	$this->lib_csvparser->load($_upload_data['full_path'], TRUE, 1000, ',', '"');
        	$_csv_data = $this->lib_csvparser->parse();
        } catch (Exception $e){
        	$up_mess .= "<font color=red>エラー発生:" . $e->getMessage() . '</font><br><br>';

        	$this->smarty->assign('up_mess', $up_mess);
        	$this->view('data_csv/project_csvup.tpl');
        	return;
        }

        // CSVファイルのバリデーションチェック
        $i = 0;
        $j = 0;
        foreach ($_csv_data as $key01 => $val01)
        {
        	foreach ($val01 as $key02 => $val02)
        	{
        		$_line_no = $i + 2;
        		if ($j == 1)	// pj_status
        		{
        			// 数字型＆文字列の長さチェック
        			if ($this->lib_validator->checkRange($val02, 0, 2))
        			{
        			} else {
        				$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
        				$up_errflg = TRUE;
        			}
        		}

        		if ($j == 9)	// pj_keyword
        		{
        			// 文字列の長さチェック : max100
        			if ($this->lib_validator->checkLength($val02, 0, 100))
        			{
        			} else {
        				$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.100)エラー。<br>";
        				$up_errflg = TRUE;
        			}
        		}

        		if ($j == 10)	// pj_url
        		{
        			// 文字列の長さチェック : max510
        			if ($this->lib_validator->checkLength($val02, 0, 510))
        			{
        				// URLチェック
        				if ($this->lib_validator->checkUri($val02))
        				{

        				} else {
        					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目でURL形式エラー。<br>";
        					$up_errflg = TRUE;
        				}

        			} else {
        				$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.510)エラー。<br>";
        				$up_errflg = TRUE;
        			}
        		}

        		if ($j == 13)	// pj_accounting
        		{
        			// 数字型＆文字列の長さチェック
        			if ($this->lib_validator->checkRange($val02, 0, 20))
        			{
        			} else {
        				$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
        				$up_errflg = TRUE;
        			}
        		}

        		if ($j == 14)	// pj_url_match
        		{
        			// 数字型＆文字列の長さチェック
        			if ($this->lib_validator->checkRange($val02, 0, 3))
        			{
        			} else {
        				$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
        				$up_errflg = TRUE;
        			}
        		}

        		if ($j == 17)	// pj_engine
        		{
        			// 数字型＆文字列の長さチェック
        			if (($val02 == "00") || ($val02 == "10") || ($val02 == "01") || ($val02 == "11"))
        			{
        			} else {
        				$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
        				$up_errflg = TRUE;
        			}
        		}

        		if ($j == 22)	// pj_salesman
        		{
        			// 数字型＆文字列の長さチェック
        			if ($this->lib_validator->checkRange($val02, 0, 9999999999))
        			{
        			} else {
        				$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
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
        	$up_mess .= "<br><font color=red>>> CSVファイルのバリデーションチェックに失敗しました。</font><br>";

        	$this->smarty->assign('up_mess', $up_mess);
        	$this->view('data_csv/project_csvup.tpl');
        	return;
        } else {
        	$up_mess .= "<font color=blue>>> CSVファイルのバリデーションチェックに成功しました。</font><br>";
        }

        // CSVファイルでのUPDATE
        $this->load->model('Location', 'lc', TRUE);
        $this->load->model('Keyword',  'kw', TRUE);
        $this->load->library('lib_rootdomain');

        $cnt = 0;
        foreach ($_csv_data as $key => $value)
        {

        	/*
        	 *
        	 * 2017.04.20
        	 * 初回データ移行時のバージョンとなります。
        	 * 　↓
        	 * 運用後は適時変更が必要！
        	 *
        	 */
        	// 「キーワード情報」更新
        	if ($value['pj_accounting'] <= 3)
        	{

	        	$set_csv_data = array();

	        	if ($value['pj_status'] == 0)
	        	{
	        		$set_csv_data['kw_status'] = 1;													// ステータス
	        	} else {
	        		$set_csv_data['kw_status'] = 0;
	        	}

	        	// 対象URL + 補正
	        	preg_match_all("/\//", $value['pj_url'], $cnt_slash) ;
	        	if (count($cnt_slash[0]) == 2)
	        	{
	        		$set_csv_data['kw_url'] = $value['pj_url'] . "/";
	        	} else {
	        		$set_csv_data['kw_url'] = $value['pj_url'];
	        	}

	        	$set_csv_data['kw_domain']        = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $value['pj_url']);	// ドメイン

	        	$_rootdomain = $this->lib_rootdomain->get_rootdomain($value['pj_url']);
	        	$set_csv_data['kw_rootdomain']    = $_rootdomain['rootdomain'];						// ルートドメイン

	        	$_kw = str_replace("　", " ", $value['pj_keyword']);;
	        	$set_csv_data['kw_keyword']       = trim($_kw);										// 検索キーワード

	       		$set_csv_data['kw_matchtype']     = 3;												// URL一致方式

	        	if ($value['pj_engine'] == "10")
	        	{
	        		$set_csv_data['kw_searchengine'] = 0;											// 検索エンジン選択
	        	} elseif ($value['pj_engine'] == "01") {
	        		$set_csv_data['kw_searchengine'] = 1;
	        	} else {
	        		$set_csv_data['kw_searchengine'] = 9;
	        	}

	        	$set_csv_data['kw_device']        = 0;												// デバイス選択 : PC

	        	$set_csv_data['kw_location_id']   = 2392;											// Criteria ID
	        	$set_csv_data['kw_location_name'] = "Japan";										// Canonical Name
	        	$set_csv_data['kw_maxposition']   = 0;												// 最大取得順位
	        	$set_csv_data['kw_trytimes']      = 2;												// データ取得回数

	        	$set_csv_data['kw_cl_seq']        = $_SESSION['c_memGrp'];
	        	$set_csv_data['kw_ac_seq']        = $_SESSION['c_memSeq'];

	        	// 既存データのチェック後、INSERT or UPDATE
	        	if ($set_csv_data['kw_searchengine'] === 9)
	        	{
	        		$set_csv_data['kw_searchengine'] = 0;

	        		$this->kw->up_insert_keyword($set_csv_data);
	        		$cnt++;

	        		$set_csv_data['kw_searchengine'] = 1;

	        		$this->kw->up_insert_keyword($set_csv_data);
	        		$cnt++;
	        	} else {
        			$this->kw->up_insert_keyword($set_csv_data);
        			$cnt++;
	        	}

	        	// ルートドメイン数のカウント＆更新
	        	$this->lib_rootdomain->get_rootdomain_chg($set_csv_data['kw_cl_seq'], $set_csv_data['kw_rootdomain']);

        	}

        	unset($set_csv_data);
        }

        $up_mess .= "<br><font color=blue>>> CSVファイルによる更新が完了しました。 　　　：　" . $cnt .  " 件</font>";
        log_message('info', 'client::[Data_csv->project_csvup()]キーワード情報データ（ラベンダー専用）のCSV取込 CSVファイルによる更新が完了しました');

        // バリデーション・チェック
        $this->_set_validation();                                            // バリデーション設定

        $this->smarty->assign('up_mess', $up_mess);

        $this->view('data_csv/project_csvup.tpl');

    }

    // キーワード情報データのCSV取込
    public function kwlist_csvup()
    {

    	require_once '/var/www/ranking/vendor/autoload.php';
    	//require_once '/var/www/ranking/vendor/zendframework/zend-validator/src/Hostname.php';
    	//require_once '/var/www/ranking/vendor/zendframework/zend-validator/src/AbstractValidator.php';
    	//require_once '/var/www/ranking/vendor/zendframework/zend-stdlib/src/StringUtils.php';
    	//$validator = new Zend_Validate_Hostname();
    	//$validator = new Hostname();
    	$validator = new Zend\Validator\Hostname();

    	/*
    	 * ベンチマーク
    	 * https://github.com/devster/ubench
    	 */
    	require_once '/var/www/ranking/vendor/ubench/Ubench.php';
    	$bench = new Ubench;

    	//処理時間の計測開始
    	$bench->start();

    	//set_time_limit(0);
    	/*
    	 * /opt/lampp/etc/php.ini
    	 *   memory_limit=1024M
    	 */
    	ini_set('memory_limit', '128M');

    	$input_post = $this->input->post();

    	$up_errflg = FALSE;
    	$up_mess   = '';

    	// **********************************
    	$this->load->helper('form');
    	// **********************************
    	$this->config->load('config_comm');
    	$this->load->library('lib_csvparser');
    	$this->load->library('lib_validator');

    	// CSVファイルのアップロード
    	$this->load->library('upload', $this->config->item('KWLIST_CSV_UPLOAD'));

    	// CSVファイルの保存
    	if ($this->upload->do_upload('kw_data'))
    	{
    		$up_mess .= "<br><font color=blue>>> CSVファイルの読み込みに成功しました。</font><br>";
    		$up_mess .= "<br><font color=blue>>> CSVファイルのバリデーションチェックを開始しました。</font><br><br>";
    		$_upload_data = $this->upload->data();
    	} else {
    		$up_mess .= "<br><font color=red>>> ERROR:CSVファイルの読み込みに失敗しました。</font><br><br>";
    		$up_mess .= $this->upload->display_errors(' <p style="color:red;">', '</p>');

    		$this->smarty->assign('up_mess02', $up_mess);
    		$this->view('data_csv/project_csvup.tpl');
    		return;
    	}

    	try{
    		// CSVファイルの読み込み
    		$this->lib_csvparser->load($_upload_data['full_path'], TRUE, 1000, ',', '"');
    		$_csv_data = $this->lib_csvparser->parse();
    	} catch (Exception $e){
    		$up_mess .= "<font color=red>エラー発生:" . $e->getMessage() . '</font><br><br>';

    		$this->smarty->assign('up_mess02', $up_mess);
    		$this->view('data_csv/project_csvup.tpl');
    		return;
    	}

    	/*
    	 * アップロード件数を100件に制限
    	 */
    	if (count($_csv_data) >= 101)
    	{
    		$up_mess .= "<br><font color=red>>> ERROR:対象レコード件数が100件を超えています。</font><br><br>";
    		$up_mess .= $this->upload->display_errors(' <p style="color:red;">', '</p>');

    		$this->smarty->assign('up_mess02', $up_mess);
    		$this->view('data_csv/project_csvup.tpl');
    		return;
    	}

    	// CSVファイルのバリデーションチェック
    	$i = 0;
    	$j = 0;
    	foreach ($_csv_data as $key01 => $val01)
    	{
    		foreach ($val01 as $key02 => $val02)
    		{
    			$_line_no = $i + 2;

    			if ($j === 0)	// seq : kw_seq
    			{
    				if ($val02 != "")
    				{
	    				// 数字型＆文字列の長さチェック
	    				if ($this->lib_validator->checkRange($val02, 0, 4294967295))
	    				{
	    				} else {
	    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
	    					$up_errflg = TRUE;
	    				}
    				}
    			}

    			if ($j === 1)	// seq : kw_cl_seq
    			{
    				if ($val02 != "")
    				{
    					// 数字型＆文字列の長さチェック
    					if ($this->lib_validator->checkRange($val02, 0, 4294967295))
    					{
    					} else {
    						$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
    						$up_errflg = TRUE;
    					}
    				}
    			}

    			if ($j === 2)	// ステータス : kw_status
    			{
    				// 入力文字のチェック＆変換
    				if ($val02 == "無効")
    				{
    					$_csv_data[$key01]['ステータス'] = 0;
    				} elseif ($val02 == "有効") {
    					$_csv_data[$key01]['ステータス'] = 1;
    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で入力文字指定エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 3)	// 対象URL : kw_url
    			{
    				// 文字列の長さチェック : max510
    				if ($this->lib_validator->checkLength($val02, 0, 510))
    				{
    					// URLチェック
    					if ($this->lib_validator->checkUri($val02))
    					{

    						/*
    						 * 国際化ドメイン対応チェック
    						 * zendframework/zend-validator を使用
    						 */
    						$chk_domain = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $val02);
    						if ($validator->isValid($chk_domain)) {
    							// ホスト名は正しい形式のようです
    						} else {
    							// 不正な形式なので、理由を表示します
    							foreach ($validator->getMessages() as $message) {
    								$up_mess .= $_line_no . "行目:「" . $key02 . "」項目でエラー。" . $message . "<br>";
    								$up_errflg = TRUE;
    							}
    						}

    					} else {
    						$up_mess .= $_line_no . "行目:「" . $key02 . "」項目でURL形式エラー。<br>";
    						$up_errflg = TRUE;
    					}

    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.510)エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 4)	// ドメイン : kw_domain
    			{
    				// 文字列の長さチェック : max100
    				if ($this->lib_validator->checkLength($val02, 0, 255))
    				{

    					/*
    					 * ここは未使用。
    					 * 上記「kw_url」から「kw_domain」求めている。
    					 * zendframework/zend-validator
    					 */

    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.255)エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 5)	// ルートドメイン : kw_rootdomain
    			{
    				// 文字列の長さチェック : max100
    				if ($this->lib_validator->checkLength($val02, 0, 255))
    				{

    					/*
    					 * ここは未使用。
    					 * 上記「kw_url」から「kw_rootdomain」求めている。
    					 * zendframework/zend-validator
    					 */

    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.255)エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 6)	// 検索キーワード : kw_keyword
    			{
    				// 文字列の長さチェック : max100
    				if ($this->lib_validator->checkLength($val02, 0, 100))
    				{
    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.100)エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 7)	// URL一致方式 : kw_matchtype
    			{
    				// 入力文字のチェック＆変換
    				if ($val02 == "完全一致")
    				{
    					$_csv_data[$key01]['URL一致方式'] = 0;
    				} elseif ($val02 == "前方一致") {
    					$_csv_data[$key01]['URL一致方式'] = 1;
    				} elseif ($val02 == "ドメイン一致") {
    					$_csv_data[$key01]['URL一致方式'] = 2;
    				} elseif ($val02 == "ルートドメイン一致") {
    					$_csv_data[$key01]['URL一致方式'] = 3;
    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で入力文字指定エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 8)	// 検索エンジン選択 : kw_searchengine
    			{
    				// 入力文字のチェック＆変換
    				if ($val02 == "Google")
    				{
    					$_csv_data[$key01]['検索エンジン選択'] = 0;
    				} elseif ($val02 == "Yahoo!") {
    					$_csv_data[$key01]['検索エンジン選択'] = 1;
    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で入力文字指定エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 9)	// デバイス選択 : kw_device
    			{
    				// 入力文字のチェック＆変換
    				if ($val02 == "ＰＣ版")
    				{
    					$_csv_data[$key01]['デバイス選択'] = 0;
    				} elseif ($val02 == "モバイル版") {
    					$_csv_data[$key01]['デバイス選択'] = 1;
    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で入力文字指定エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 10)	// seq : kw_location_id
    			{
    				if ($val02 != "")
    				{
    					// 数字型＆文字列の長さチェック
    					if ($this->lib_validator->checkRange($val02, 0, 4294967295))
    					{
    					} else {
    						$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
    						$up_errflg = TRUE;
    					}
    				}
    			}

    			if ($j === 11)	// Canonical Name : kw_location_name
    			{
    				// 文字列の長さチェック : max100
    				if ($this->lib_validator->checkLength($val02, 0, 100))
    				{
    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.100)エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 12)	// 最大取得順位 : kw_maxposition
    			{
    				// 数字型＆文字列の長さチェック
    				if ($this->lib_validator->checkRange($val02, 0, 2))
    				{
    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 13)	// データ取得回数 : kw_trytimes
    			{
    				// 数字型＆文字列の長さチェック
    				if ($this->lib_validator->checkRange($val02, 0, 2))
    				{
    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 14)	// 設定グループ : kw_group
    			{
    				// 文字列の長さチェック : max100
    				if ($this->lib_validator->checkLength($val02, 0, 50))
    				{
    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.50)エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

    			if ($j === 15)	// 設定タグ : kw_tag
    			{
    				// 文字列の長さチェック : max100
    				if ($this->lib_validator->checkLength($val02, 0, 1000))
    				{
    				} else {
    					$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.1000)エラー。<br>";
    					$up_errflg = TRUE;
    				}
    			}

				if ($j === 16)	// seq : rd_seq
    			{
    				if ($val02 != "")
    				{
    					// 数字型＆文字列の長さチェック
    					if ($this->lib_validator->checkRange($val02, 0, 4294967295))
    					{
    					} else {
    						$up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
    						$up_errflg = TRUE;
    					}
    				}
    			}

    			$j++;
    		}
    		$i++;
    		$j = 0;
    	}

    	if ($up_errflg == TRUE)
    	{
    		$up_mess .= "<br><font color=red>>> ERROR:CSVファイルのバリデーションチェックに失敗しました。</font><br>";

    		$this->smarty->assign('up_mess02', $up_mess);
    		$this->view('data_csv/project_csvup.tpl');
    		return;
    	} else {
    		$up_mess .= "<font color=blue>>> CSVファイルのバリデーションチェックに成功しました。</font><br>";
    	}


//     	echo memory_get_usage(true);

    	//処理時間の計測終了
    	$bench->end();
    	//処理時間
    	print("処理時間・・・");
    	echo $bench->getTime();
    	print("<br>メモリ使用量・・・");
    	//メモリ使用量(memory_get_usage(true))
    	echo $bench->getMemoryUsage();
    	print("<br>メモリ最大値・・・");
    	//最大値(memory_get_peak_usage(true))
    	echo $bench->getMemoryPeak();
    	print("<br><br>");



    	//処理時間の計測開始
    	$bench->start();



    	// CSVファイルでのUPDATE
    	$this->load->model('Location',  'lc', TRUE);
    	$this->load->model('Keyword',   'kw', TRUE);
    	$this->load->model('Group_tag', 'gt', TRUE);
    	$this->load->library('lib_keyword');
    	$this->load->library('lib_rootdomain');

    	$cnt = 0;
    	$line_cnt = 1;
    	 foreach ($_csv_data as $key => $value)
    	{
    		$set_csv_data = array();

    		$set_csv_data['kw_status'] = $value['ステータス'];

    		// 対象URL + 補正
    		preg_match_all("/\//", $value['対象URL'], $cnt_slash) ;
    		if (count($cnt_slash[0]) == 2)
    		{
    			$set_csv_data['kw_url'] = $value['対象URL'] . "/";
    		} else {
    			$set_csv_data['kw_url'] = $value['対象URL'];
    		}

    		// 対象URL から自動作成
    		$set_csv_data['kw_domain'] = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $set_csv_data['kw_url']);

    		$_rootdomain = $this->lib_rootdomain->get_rootdomain($set_csv_data['kw_url']);
    		$set_csv_data['kw_rootdomain'] = $_rootdomain['rootdomain'];

    		$_kw = str_replace("　", " ", $value['検索キーワード']);;
    		$set_csv_data['kw_keyword'] = trim($_kw);

    		$set_csv_data['kw_matchtype']    = $value['URL一致方式'];
    		$set_csv_data['kw_searchengine'] = $value['検索エンジン選択'];
    		$set_csv_data['kw_device']       = $value['デバイス選択'];

    		$get_location_data = $this->lc->get_location_name($value['Canonical Name']);
    		if (empty($get_location_data))
    		{
    			$up_mess .= "<br><font color=red>>> ERROR:ロケーション名が見つかりませんでした。 　：　" . $line_cnt . "行目 => " . $value['Canonical Name'] . "</font>";
    			++$line_cnt;
    			continue;
    		} else {
    			$set_csv_data['kw_location_id']   = $get_location_data[0]['lo_criteria_id'];
    			$set_csv_data['kw_location_name'] = $get_location_data[0]['lo_canonical_name'];
    		}

    		$set_csv_data['kw_maxposition'] = $value['最大取得順位'];
    		$set_csv_data['kw_trytimes']    = $value['データ取得回数'];
    		$set_csv_data['kw_group']       = $value['設定グループ'];

    		// タグ入力情報を分解＆生成＆セット
    		$set_csv_data['kw_tag'] = "";
    		if ($value['設定タグ'] != "")
    		{

    			$_tmp_tag = str_replace("[", "" ,str_replace("]", "", explode("][", $value['設定タグ'])));
    			foreach ($_tmp_tag as $key1 => $val1)
    			{
    				$set_csv_data['kw_tag'] .= "[" . $val1 . "]";
    			}

    			/*
    			 * []で括ってないとデータがおかしくなる。
    			 * もし、エラーが多いようなら以下でセットするか？
    			 */
    			//preg_match("/\[.+?\]/", $value['設定タグ'], $cnt_match);
    		}

    		$set_csv_data['kw_cl_seq']      = $_SESSION['c_memGrp'];
    		$set_csv_data['kw_ac_seq']      = $_SESSION['c_memSeq'];

    		if ($value['seq'] == "")
    		{
    			// 新規作成
    			$result = $this->kw->up_insert_keyword($set_csv_data);
    		} else {
				// 更新
    			$set_csv_data['kw_seq'] = $value['seq'];

    			// 同一URLのチェック
    			$get_kw_check = $this->kw->check_url($set_csv_data, $old_seq=NULL, $status=1);
    			if (count($get_kw_check) >= 1)
    			{
    				/*
    				 * 旧kw_seq が存在する場合もチェックが入るので、その場合は画面から更新をかけてください。
    				 */
    				$up_mess .= "<br><font color=red>>> ERROR:同一URLが存在します。 　：　" . $line_cnt . "行目 => " . $set_csv_data['kw_url'] . "</font>";
    				++$line_cnt;
    				continue;
    			}

    			// ** 旧URL情報を別レコードとして保存 → ルートドメインの削除
    			$get_old_kw_data =$this->kw->get_kw_seq($value['seq']);

    			// UPDATE
    			$result = $this->kw->update_keyword($set_csv_data);
    		}
    		if ($result == FALSE)
    		{
    			$up_mess .= "<br><font color=red>>> ERROR:データの追加または更新に失敗しました。 　：　" . $line_cnt . "行目 => " . $set_csv_data['kw_url'] . "</font>";
    			++$line_cnt;
    			continue;
    		} else {
    			++$cnt;
    			++$line_cnt;
    		}

    		// ルートドメイン数のカウント＆更新
    		$this->lib_rootdomain->get_rootdomain_chg($set_csv_data['kw_cl_seq'], $set_csv_data['kw_rootdomain']);
    		if (!empty($get_old_kw_data))
    		{
    			// ルートドメインの削除有無
    			$this->lib_rootdomain->get_rootdomain_del($get_old_kw_data[0]['kw_cl_seq'], $get_old_kw_data[0]['kw_rootdomain']);
    		}

    		/*
    		 * ここは変えた方がいいかも？
    		 * ロジック？ or 仕様？
    		 */
    		// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
    		if ($set_csv_data['kw_group'] != "")
    		{
    			$get_gt_name = $this->gt->get_gt_name($set_csv_data['kw_group'], $set_csv_data['kw_cl_seq'], 0);

    			if (count($get_gt_name) == 0)
    			{
    				$set_gt_data['gt_name']   = $set_csv_data['kw_group'];
    				$set_gt_data['gt_cl_seq'] = $set_csv_data['kw_cl_seq'];
    				$set_gt_data['gt_type']   = 0;

    				// INSERT
    				$this->gt->insert_group_tag($set_gt_data);
    			}
    		}
    		// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
    		$this->lib_keyword->update_group_info_all($set_csv_data['kw_cl_seq'], 0);

    		// 新規に追加された設定タグをレコード追加
    		if (isset($_tmp_tag))
    		{
    			foreach ($_tmp_tag as $key02 => $val02)
    			{
    				$get_gt_name = $this->gt->get_gt_name($val02, $set_csv_data['kw_cl_seq'], 1);

    				if (count($get_gt_name) == 0)
    				{
    					$set_gt_data['gt_name']   = $val02;
    					$set_gt_data['gt_cl_seq'] = $set_csv_data['kw_cl_seq'];
    					$set_gt_data['gt_type']   = 1;

    					// INSERT
    					$this->gt->insert_group_tag($set_gt_data);
    				}
    			}
    		}
    		// 全タグ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
    		$this->lib_keyword->update_tag_info_all($set_csv_data['kw_cl_seq'], 1);

    		unset($set_gt_data);
    		unset($set_csv_data);
    		unset($get_gt_name);
    		unset($value);
    		unset($val01);
    		unset($val02);
    		unset($_tmp_tag);

    		// 512M のように M で指定されている前提なのでアレでごめんなさい
    		list($max) = sscanf(ini_get('memory_limit'), '%dM');
    		$peak = memory_get_peak_usage(true) / 1024 / 1024;
    		$used = ((int) $max !== 0)? round((int) $peak / (int) $max * 100, 2): '--';
    		if ($used > 80) {
    			$message = sprintf("[%s] Memory peak usage warning: %s %% used. (max: %sM, now: %sM)\n", date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $used, $max, $peak);
    			log_message('error', $message);
    			break;
    		}
    	}

//     	echo memory_get_usage(true);

    	//処理時間の計測終了
    	$bench->end();
    	//処理時間
    	print("処理時間・・・");
    	echo $bench->getTime();
    	print("<br>メモリ使用量・・・");
    	 //メモリ使用量(memory_get_usage(true))
    	echo $bench->getMemoryUsage();
    	print("<br>メモリ最大値・・・");
    	//最大値(memory_get_peak_usage(true))
    	echo $bench->getMemoryPeak();
    	print("<br><br>");

    	$up_mess .= "<br><font color=blue>>> CSVファイルによる更新が完了しました。 　　　：　" . $cnt .  " 件</font>";
    	log_message('info', 'client::[Data_csv->kwlist_csvup()]キーワード情報データのCSV取込 CSVファイルによる更新が完了しました');

    	// バリデーション・チェック
    	$this->_set_validation();                                            // バリデーション設定
    	//$this->form_validation->run();

    	$this->smarty->assign('up_mess02', $up_mess);

    	$this->view('data_csv/project_csvup.tpl');

   	}

   	// Location criteria情報データのCSV取込
   	public function criteria_csvup()
   	{

   		/*
   		 * 基準ファイル
   		 * https://developers.google.com/adwords/api/docs/appendix/geotargeting
   		 */

   		$input_post = $this->input->post();

   		$up_errflg = FALSE;
   		$up_mess   = '';

   		// **********************************
   		$this->load->helper('form');
   		// **********************************
   		$this->config->load('config_comm');
   		$this->load->library('lib_csvparser');
   		$this->load->library('lib_validator');

   		// CSVファイルのアップロード
   		$this->load->library('upload', $this->config->item('CRITERIA_CSV_UPLOAD'));

   		// CSVファイルの保存
   		if ($this->upload->do_upload('criteria_data'))
   		{
   			$up_mess .= "<br><font color=blue>>> CSVファイルの読み込みに成功しました。</font><br>";
   			$up_mess .= "<br><font color=blue>>> CSVファイルのバリデーションチェックを開始しました。</font><br><br>";
   			$_upload_data = $this->upload->data();
   		} else {
   			$up_mess .= "<br><font color=red>>> CSVファイルの読み込みに失敗しました。</font><br><br>";
   			$up_mess .= $this->upload->display_errors(' <p style="color:red;">', '</p>');

   			$this->smarty->assign('up_mess03', $up_mess);
   			$this->view('data_csv/project_csvup.tpl');
   			return;
   		}

   		try{
   			// CSVファイルの読み込み
   			$this->lib_csvparser->load($_upload_data['full_path'], TRUE, 1000, ',', '"');
   			$_csv_data = $this->lib_csvparser->parse();
   		} catch (Exception $e){
   			$up_mess .= "<font color=red>エラー発生:" . $e->getMessage() . '</font><br><br>';

   			$this->smarty->assign('up_mess03', $up_mess);
   			$this->view('data_csv/project_csvup.tpl');
   			return;
   		}

   		/*
   		 * ここではバリデーションチェックは行わない
   		 */

   		// CSVファイルでのUPDATE
   		$this->load->model('Location',  'lc', TRUE);

   		$cnt = 0;
   		$line_cnt = 1;
   		foreach ($_csv_data as $key => $value)
   		{
   			$set_csv_data = array();

   			$set_csv_data['lo_criteria_id']    = $value['Criteria ID'];
   			$set_csv_data['lo_name']           = $value['Name'];
   			$set_csv_data['lo_canonical_name'] = $value['Canonical Name'];
   			$set_csv_data['lo_parent_id']      = $value['Parent ID'];
   			$set_csv_data['lo_country_code']   = $value['Country Code'];
   			$set_csv_data['lo_target_type']    = $value['Target Type'];
   			$set_csv_data['lo_status']         = $value['Status'];

			$result = $this->lc->up_insert_criteria($set_csv_data, "");
   			if ($result == FALSE)
   			{
   				$up_mess .= "<br><font color=red>>> データの追加または更新に失敗しました。 　：　" . $line_cnt . " => " . $set_csv_data['kw_url'] . "</font>";
   				++$line_cnt;
   				continue;
   			} else {
   				++$cnt;
   				++$line_cnt;
   			}

   			unset($set_csv_data);
   		}

   		$up_mess .= "<br><font color=blue>>> CSVファイルによる更新が完了しました。 　　　：　" . $cnt .  " 件</font>";
   		log_message('info', 'client::[Data_csv->criteria_csvup()]Location criteria情報データのCSV取込 CSVファイルによる更新が完了しました');

   		// バリデーション・チェック
   		$this->_set_validation();

   		$this->smarty->assign('up_mess03', $up_mess);

   		$this->view('data_csv/project_csvup.tpl');

   	}

    // キーワード管理情報CSV 全件ダウンロード
    public function kwlist_csvdown()
    {

        // 件数(max1000件)を取得。とりあえず制限をかけておきます
        $tmp_offset   = 0;
        $tmp_per_page = 1000;

        // セッションからフラッシュデータ読み込み
        $tmp_inputpost['kw_keyword']   = $_SESSION['c_kw_keyword'];
        $tmp_inputpost['kw_domain']    = $_SESSION['c_kw_domain'];
        $tmp_inputpost['kw_status']    = $_SESSION['c_kw_status'];
        $tmp_inputpost['orderid']      = $_SESSION['c_orderid'];

        // キーワード情報の取得
        $this->load->model('Keyword', 'kw', TRUE);
        $query = $this->kw->get_csvdl_list($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp']);

        // 作成したヘルパーを読み込む
        $this->load->helper(array('download', 'csvdata'));

        // ヘルパーに追加した関数を呼び出し、CSVデータ取得
        $get_dl_csv = csv_from_result($query);

        $file_name = 'dlcsv_kwlist_' . date('YmdHis') . '.csv';
        force_download($file_name, $get_dl_csv);

        $this->view('keywordlist/index.tpl');
    }

    // キーワード情報CSV ダウンロード
    public function toplist_csvdown()
    {

    	// 件数(max1000件)を取得。とりあえず制限をかけておきます
    	$tmp_offset   = 0;
    	$tmp_per_page = 1000;

    	// セッションからフラッシュデータ読み込み
    	$tmp_inputpost = array(
    			'free_keyword'      => $_SESSION['c_free_keyword'],

    			'kw_keyword'        => $_SESSION['c_kw_keyword'],
    			'kw_domain'         => $_SESSION['c_kw_domain'],
    			'kw_group'          => $_SESSION['c_kw_group'],
    			'kw_tag'            => $_SESSION['c_kw_tag'],

    			'kw_matchtype'      => $_SESSION['c_kw_matchtype'],
    			'kw_searchengine'   => $_SESSION['c_kw_searchengine'],
    			'kw_device'         => $_SESSION['c_kw_device'],
    			'kw_status'         => $_SESSION['c_kw_status'],
    			'orderid'           => $_SESSION['c_orderid'],
    			'watch_kw'          => $_SESSION['c_watch_kw'],
    			'watch_domain'      => $_SESSION['c_watch_domain'],
    	);


    	// TOP:検索キーワード情報の取得
    	$this->load->model('Keyword', 'kw', TRUE);
    	$query = $this->kw->get_csvdl_toplist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp']);


    	// 作成したヘルパーを読み込む
    	$this->load->helper(array('download', 'csvdata'));

    	// ヘルパーに追加した関数を呼び出し、CSVデータ取得
    	$get_dl_csv = csv_toplist_result($query);

    	$file_name = 'dlcsv_toplist_' . date('YmdHis') . '.csv';
    	//force_download($file_name, $get_dl_csv);

    	// S-JIS変換を行う場合
    	$get_sjis_csv = mb_convert_encoding($get_dl_csv,"SJIS", "UTF-8");
    	force_download($file_name, $get_sjis_csv);

    	redirect('/top/search/');

    }

    // レポート：キーワード情報CSV ダウンロード（複数キーワード指定に対応）
    public function report_csvdown()
    {

    	$input_post = $this->input->post();

    	$this->load->model('Keyword', 'kw', TRUE);

    	// kw_seq & 表示期間を呼び出し
    	$report_kwseq = $_SESSION['c_report_kwseq'];
    	$_term        = $_SESSION['c_report_term'];

    	$_tmp_kw_seq = explode(",", $report_kwseq);

    	// *** 一つのseqから仲間 3人を見つける！
    	$_arr_kw_seq = array();
    	foreach ($_tmp_kw_seq as $key => $value)
    	{
    		$get_kw_pair = $this->kw->get_kw_seq($value);

    		// ３キーワード（google-PC/Mobile/Yahoo!）情報を取得
    		$set_kw_pair['kw_cl_seq']      = $get_kw_pair[0]['kw_cl_seq'];
    		$set_kw_pair['kw_url']         = $get_kw_pair[0]['kw_url'];
    		$set_kw_pair['kw_keyword']     = $get_kw_pair[0]['kw_keyword'];
    		$set_kw_pair['kw_matchtype']   = $get_kw_pair[0]['kw_matchtype'];
    		$set_kw_pair['kw_location_id'] = $get_kw_pair[0]['kw_location_id'];

    		$arr_kw_pair[$key] =$this->kw->get_kw_info($set_kw_pair);
    	}

    	$_start_date     = $input_post['start_date'];
    	$_end_date       = $input_post['end_date'];

    	// キーワード情報の取得
    	$query = $this->kw->get_csvdl_report($arr_kw_pair, $_start_date, $_end_date);

    	// 作成したヘルパーを読み込む
    	$this->load->helper(array('download', 'csvdata'));

    	// ヘルパーに追加した関数を呼び出し、CSVデータ取得
    	$get_dl_csv = csv_from_result($query);

    	$_url = preg_replace("/^https?:\/\/(www\.)?|/i", "", $arr_kw_pair[0][0]['kw_url']);
    	$_url = str_replace("/", "_", $_url);
    	$_url = str_replace(".", "_", $_url);

    	$_tmp_location_name =  explode(",", $arr_kw_pair[0][0]['kw_location_name']);

    	$file_name = 'dlreport_' . $arr_kw_pair[0][0]['kw_keyword'] . '_' . $_url . '_' . $_tmp_location_name[0] . '_' . date('YmdHis') . '.csv';
    	force_download($file_name, $get_dl_csv);									// ここは連続でできない！

    	redirect('/topdetail/report/' . $_term);

    }

    // レポート：キーワード情報CSV ダウンロード
    public function report_csvdownxxxxx()
    {

    	$input_post = $this->input->post();

    	$_searchengine = $input_post['kw_searchengine'];
    	$_url = preg_replace("/^https?:\/\/(www\.)?|/i", "", $input_post['kw_url']);
    	$_url = str_replace("/", "_", $_url);
    	$_url = str_replace(".", "_", $_url);

    	$this->load->model('Keyword', 'kw', TRUE);

    	// グラフ＆テーブルデータの取得 (Google & Yahoo!) 同時の場合
    	$kw_seq01 = $input_post['kw_seq'];
    	if ($input_post['gp_kind'] == 1)
    	{
    		$get_kw_data = $this->kw->get_kw_seq($kw_seq01);


    		// 同一キーワードの存在チェックから、もう片方のkw_seqを求める
    		$set_kw_data = array();
    		$set_kw_data['kw_cl_seq']       = $get_kw_data[0]['kw_cl_seq'];
    		$set_kw_data['kw_url']          = $get_kw_data[0]['kw_url'];
    		$set_kw_data['kw_keyword']      = $get_kw_data[0]['kw_keyword'];
    		$set_kw_data['kw_matchtype']    = $get_kw_data[0]['kw_matchtype'];
    		$set_kw_data['kw_device']       = $get_kw_data[0]['kw_device'];
    		$set_kw_data['kw_location_id']  = $get_kw_data[0]['kw_location_id'];
    		$set_kw_data['kw_searchengine'] = 0;
    		if ($get_kw_data[0]['kw_searchengine'] == 0)
    		{
    			$set_kw_data['kw_searchengine'] = 1;
    		}

    		$get_kw_row = $this->kw->get_kw_url($set_kw_data);
    		if (!empty($get_kw_row))
    		{
    			$kw_seq02 = $get_kw_row[0]['kw_seq'];
    		} else {
    			$kw_seq02 = NULL;
    		}

    		$_searchengine = "";
    	}

    	// キーワード情報の取得
    	$query = $this->kw->get_csvdl_report($input_post, $kw_seq01, $kw_seq02);

    	// 作成したヘルパーを読み込む
    	$this->load->helper(array('download', 'csvdata'));

    	// ヘルパーに追加した関数を呼び出し、CSVデータ取得
    	$get_dl_csv = csv_from_result($query);

    	$file_name = 'dlcsv_' . $input_post['kw_keyword'] . $_searchengine . '_' . $_url . date('YmdHis') . '.csv';
    	//$file_name = 'dlcsv_report_' . date('YmdHis') . '.csv';
    	force_download($file_name, $get_dl_csv);

    	redirect('/topdetail/report/' . $input_post['term']);
    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}