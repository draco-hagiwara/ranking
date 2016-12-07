<?php

class Data_csvup extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($_SESSION['c_login'] == TRUE)
        {
            $this->smarty->assign('login_chk', TRUE);
            $this->smarty->assign('mem_Type',  $_SESSION['c_memType']);
            $this->smarty->assign('mem_Seq',   $_SESSION['c_memSeq']);
            $this->smarty->assign('mem_Grp',   $_SESSION['c_memGrp']);
            $this->smarty->assign('mem_Name',  $_SESSION['c_memName']);
        } else {
            $this->smarty->assign('login_chk', FALSE);
            $this->smarty->assign('mem_Type',  "");
            $this->smarty->assign('mem_Seq',   "");
            $this->smarty->assign('mem_Grp',   "");

            redirect('/login/');
        }

        $this->smarty->assign('up_mess01', NULL);

    }

    // CSVデータのアップロード処理TOP
    public function index()
    {

    	// バリデーション・チェック
    	$this->_set_validation();

    	$this->view('data_csvup/index.tpl');

    }

    // 顧客データのCSV取込
    public function customer_csvup()
    {

    	$input_post = $this->input->post();

    	$up_errflg = FALSE;
    	$up_mess01 = '';

    	$this->config->load('config_comm');
    	$this->load->library('csvparser');
    	$this->load->library('commonvalidator');

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
    				$this->csvparser->load($_upload_data['full_path'], TRUE, 1000, ',', '"');
    				$_csv_data = $this->csvparser->parse();
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
    						if ($this->commonvalidator->checkRange($val02, 0, 99999999))
    						{
    						} else {
    							$up_mess01 .= $i + 1 . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
    							$up_errflg = TRUE;
    						}
    					}

    					if ($j == 2)
    					{
    						// 数字型＆文字列の長さチェック
    						if ($this->commonvalidator->checkRange($val02, 0, 3))
    						{
    						} else {
    							$up_mess01 .= $i + 1 . "行目:「" . $key02 . "」項目で数字または範囲指定(0～3)エラー。<br>";
    							$up_errflg = TRUE;
    						}
    					}

    					if ($j == 4)
    					{
    						// int型文字列チェック
    						if ($this->commonvalidator->checkInt($val02))
    						{
    							// 文字列の長さチェック
    							if ($this->commonvalidator->checkLength($val02, 0, 9))
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
    						if ($this->commonvalidator->checkDateFormat($val02, 'Y-m-d H:i:s'))
    						{
    						} else {
    							$up_mess01 .= $i + 1 . "行目:「" . $key02 . "」項目で日付時間エラー。<br>";
    							$up_errflg = TRUE;
    						}
    					}

    					if (($j >= 7) && ($val02 != ''))	// 任意
    					{
    						// 日付型チェック
    						if ($this->commonvalidator->checkDateFormat($val02, 'Y-m-d'))
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

    	$this->view('admin/pay_csvup/index.tpl');

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}

