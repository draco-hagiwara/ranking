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
        $this->smarty->assign('dl_mess', NULL);

    }

    // 顧客データCSVのアップロード処理TOP
    public function project()
    {

        // バリデーション・チェック
        $this->_set_validation();

        $this->view('data_csv/project_csvup.tpl');

    }

    // 顧客データのCSV取込
    public function project_csvup()
    {

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

	        	$set_csv_data['kw_url']           = $value['pj_url'];								// 対象URL

	        	$set_csv_data['kw_domain']        = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $value['pj_url']);	// ドメイン

	        	$_rootdomain = $this->lib_rootdomain->get_rootdomain($value['pj_url']);
	        	$set_csv_data['kw_rootdomain']    = $_rootdomain['rootdomain'];						// ルートドメイン

	        	$_kw = str_replace("　", " ", $value['pj_keyword']);;
	        	$set_csv_data['kw_keyword']       = trim($_kw);										// 検索キーワード

	       		$set_csv_data['kw_matchtype']     = 3;												// URL一致方式
//        		$set_csv_data['kw_matchtype'] = $value['pj_url_match'];

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
	        	if ($set_csv_data['kw_searchengine'] == 9)
	        	{
	        		$set_csv_data['kw_searchengine'] = 0;
	        		if ($set_csv_data['kw_searchengine'] == 9)
	        		{
	        			$set_csv_data['kw_searchengine'] = 0;
	        			$this->kw->up_insert_keyword($set_csv_data, "");
	        			$cnt++;

	        			$set_csv_data['kw_searchengine'] = 1;
	        			$this->kw->up_insert_keyword($set_csv_data, "");
	        			$cnt++;
	        		} else {
	        			$this->kw->up_insert_keyword($set_csv_data, "");
	        			$cnt++;
	        		}

	        		$set_csv_data['kw_searchengine'] = 1;
	        		if ($set_csv_data['kw_searchengine'] == 9)
	        		{
	        			$set_csv_data['kw_searchengine'] = 0;
	        			$this->kw->up_insert_keyword($set_csv_data, "");
	        			$cnt++;

	        			$set_csv_data['kw_searchengine'] = 1;
	        			$this->kw->up_insert_keyword($set_csv_data, "");
	        			$cnt++;
	        		} else {
	        			$this->kw->up_insert_keyword($set_csv_data, "");
	        			$cnt++;
	        		}
	        	} else {
	        		if ($set_csv_data['kw_searchengine'] == 9)
	        		{
	        			$set_csv_data['kw_searchengine'] = 0;
	        			$this->kw->up_insert_keyword($set_csv_data, "");
	        			$cnt++;

	        			$set_csv_data['kw_searchengine'] = 1;
	        			$this->kw->up_insert_keyword($set_csv_data, "");
	        			$cnt++;
	        		} else {
	        			$this->kw->up_insert_keyword($set_csv_data, "");
	        			$cnt++;
	        		}
	        	}
        	}
        }

        $up_mess .= "<br><font color=blue>>> CSVファイルによる更新が完了しました。 　　　：　" . $cnt .  " 件</font>";

        // バリデーション・チェック
        $this->_set_validation();                                            // バリデーション設定
        //$this->form_validation->run();

        $this->smarty->assign('up_mess', $up_mess);

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

//         $this->smarty->assign('dl_mess', "<br><font color=blue>>> CSVダウンロードが完了しました。</font>");

        $this->view('keywordlist/index.tpl');

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}