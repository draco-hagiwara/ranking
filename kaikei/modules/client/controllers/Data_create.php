<?php

class Data_create extends MY_Controller
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

    }

    // 請求書一括作成処理TOP
    public function index()
    {

    	// バリデーション・チェック
    	$this->_set_validation();

    	// 初期値セット
    	$this->_ym_item_set();

    	$this->view('data_create/index.tpl');


    }

    // 月額請求書データ 手動作成：固定
    public function fix_cal()
    {

    	$input_post = $this->input->post();

    	$this->load->model('Customer',       'cm',  TRUE);
    	$this->load->model('Project',        'pj',  TRUE);
    	$this->load->model('Invoice',        'iv',  TRUE);
    	$this->load->model('Invoice_detail', 'ivd', TRUE);
    	$this->load->library('Commoninvoice');
    	$this->config->load('config_comm');

    	// バリデーション・チェック
    	$this->_set_validation02();
    	if ($this->form_validation->run() == TRUE)
    	{

    		// 有効な「顧客情報」を抽出
    		$cm_list = $this->cm->get_invoicelist();

    		// トランザクション・START
    		$this->db->trans_strict(FALSE);                                 		// StrictモードをOFF
    		$this->db->trans_start();                                       		// trans_begin

    		// 受注案件情報から個々のデータを取得
    		foreach($cm_list as $key => $value)
    		{

    			$_iv_type = 0;														// 課金方式：：固定=0/成果=1/固+成=2
    			$get_pj_list = $this->pj->get_pj_cm_seq($value['cm_seq'], $_iv_type, $_SESSION['c_memGrp'], 'seorank');

    			$list_detail = array();
    			$_subtotal = 0;
    			$cnt = 0;
    			foreach($get_pj_list as $key => $val)
    			{

					// 契約期間チェック
					$_create_date = substr($input_post['iv_issue_yymm'], 0, 4) . '-' . substr($input_post['iv_issue_yymm'], 4, 2);
	    			$date_now = new DateTime($_create_date);
	    			$date_now = $date_now->modify('last day of last months')->format('Y-m-d');		// 請求データ作成の指定年月の前月末日
	    			$date_str = new DateTime($val['pj_start_date']);
	    			$date_str = $date_str->format('Y-m-d');
	    			$date_end = new DateTime($val['pj_end_date']);
	    			$date_end = $date_end->format('Y-m-d');

	    			if (($date_str <= $date_now) && ($date_now <= $date_end))
	    			{

	    				// 対象データの一時保管
	    				$list_detail[$cnt] = $val;

	    				// 「合計」金額集計
	    				$_subtotal = $_subtotal + $val['pj_billing'];

	    				$cnt++;

	    			}
    			}

    			// 明細データ有無チェック
    			$set_data_iv  = array();
    			if (count($list_detail))
    			{

    				$set_data_iv['iv_cm_seq']         = $value['cm_seq'];
    				$set_data_iv['iv_issue_yymm']     = $input_post['iv_issue_yymm'];							// 発行年月
    				$set_data_iv['iv_subtotal']       = $_subtotal;												// 小計

    				// 消費税計算
    				$_issue_tax['zeiritsu'] = $this->config->item('INVOICE_TAX');
    				$_issue_tax['zeinuki']  = $this->config->item('INVOICE_TAXOUT');
    				$_issue_tax['hasuu']    = $this->config->item('INVOICE_TAX_CAL');

    				$set_data_iv['iv_tax'] = $this->commoninvoice->cal_tax($_subtotal, $_issue_tax);
//     				$set_data_iv['iv_tax']            = $this->_tax_calculation($_subtotal);					// 税額

    				$set_data_iv['iv_total']          = $_subtotal + $set_data_iv['iv_tax'];					// 合計
    				$set_data_iv['iv_issue_date']     = $input_post['iv_issue_date01'];							// 発行日
    				$set_data_iv['iv_pay_date']       = $input_post['iv_pay_date01'];							// 振込期日

    				if ($value['cm_flg_iv'] == 0)																// 発行先住所
    				{
    					$set_data_iv['iv_company']    = $value['cm_company'];
    					$set_data_iv['iv_department'] = $value['cm_department'];
    					$set_data_iv['iv_person01']   = $value['cm_person01'];
    					$set_data_iv['iv_person02']   = $value['cm_person02'];
    					$set_data_iv['iv_zip01']      = $value['cm_zip01'];
    					$set_data_iv['iv_zip02']      = $value['cm_zip02'];
    					$set_data_iv['iv_pref']       = $value['cm_pref'];
    					$set_data_iv['iv_addr01']     = $value['cm_addr01'];
    					$set_data_iv['iv_addr02']     = $value['cm_addr02'];
    					$set_data_iv['iv_buil']       = $value['cm_buil'];
    				} else {
    					$set_data_iv['iv_company']    = $value['cm_company_iv'];
    					$set_data_iv['iv_department'] = $value['cm_department_iv'];
    					$set_data_iv['iv_person01']   = $value['cm_person01_iv'];
    					$set_data_iv['iv_person02']   = $value['cm_person02_iv'];
    					$set_data_iv['iv_zip01']      = $value['cm_zip01_iv'];
    					$set_data_iv['iv_zip02']      = $value['cm_zip02_iv'];
    					$set_data_iv['iv_pref']       = $value['cm_pref_iv'];
    					$set_data_iv['iv_addr01']     = $value['cm_addr01_iv'];
    					$set_data_iv['iv_addr02']     = $value['cm_addr02_iv'];
    					$set_data_iv['iv_buil']       = $value['cm_buil_iv'];
    				}
    				$set_data_iv['iv_remark']         = $value['cm_memo_iv'];									// 備考欄

    				$set_data_iv['iv_bank_cd']        = $value['cm_bank_cd'];									// 入金銀行情報
    				$set_data_iv['iv_bank_nm']        = $value['cm_bank_nm'];
    				$set_data_iv['iv_branch_cd']      = $value['cm_branch_cd'];
    				$set_data_iv['iv_branch_nm']      = $value['cm_branch_nm'];
    				$set_data_iv['iv_kind']           = $value['cm_kind'];
    				$set_data_iv['iv_account_no']     = $value['cm_account_no'];
    				$set_data_iv['iv_account_nm']     = $value['cm_account_nm'];

    				$get_iv_data[0]['iv_sales_date']  = NULL;													// 売上日

    				// 請求書データ : 既存データ有無のチェック
    				$_new_data = FALSE;
    				$get_iv_data = $this->iv->get_iv_cm_seq($value['cm_seq'], $input_post['iv_issue_yymm']);

    				if (count($get_iv_data) == 0)
    				{

    					// 請求書データ : 新規作成
    					$set_data_iv['iv_seq_suffix'] = 1;														// 枝番

    					// 請求書発行番号
    					$_issue_num['issue_num']        = $this->config->item('INVOICE_ISSUE_NUM');				// 接頭語
    					$_issue_num['client_no']        = $_SESSION['c_memGrp'];								// クライアントNO
    					$_issue_num['customer_no']      = $value['cm_seq'];										// 顧客NO
    					$_issue_num['issue_class']      = 1;													// 一括発行=1,個別発行=2
    					$_issue_num['issue_accounting'] = 'A';													// 「通常（固定or成果）:A」のみ
    					$_issue_num['issue_suffix']     = $set_data_iv['iv_seq_suffix'];						// 枝番
    					$_issue_num['issue_yymm']       = $input_post['iv_issue_yymm'];							// 発行年月
    					$_issue_num['issue_re']         = 0;													// 再発行

    					$set_data_iv['iv_slip_no']      = $this->commoninvoice->issue_num($_issue_num);

//     					$set_data_iv['iv_slip_no']    = 'LM'
//     													. $_SESSION['c_memGrp']									// クライアントNO
//     													. '-' . $value['cm_seq']								// 顧客NO
//     													. '-' . '1'												// 一括発行=1,個別発行=2
//     													. 'A'													// 「通常（固定or成果）:A」
//     													. $set_data_iv['iv_seq_suffix']							// 枝番
//     													. '-' . $input_post['iv_issue_yymm'];					// 発行年月

    					$get_iv_seq = $this->iv->insert_invoice($set_data_iv);

    					// 履歴ファイルを作成
    					$set_data_iv['iv_seq'] = $get_iv_seq;
    					$this->iv->insert_invoice_history($set_data_iv);

    					$_new_data = TRUE;

    				} else {

    					// 請求書データ : 既存データ書き換えUPDATE
    					$set_data_iv['iv_seq']        = $get_iv_data[0]['iv_seq'];
    					$set_data_iv['iv_seq_suffix'] = $get_iv_data[0]['iv_seq_suffix'] + 1;
    					$set_data_iv['iv_status']     = 0;
    					if ($get_iv_data[0]['iv_status'] == 9)
    					{
    						$set_data_iv['iv_delflg'] = 0;
    					}

    					// 請求書発行番号
    					$_issue_num['issue_num']        = $this->config->item('INVOICE_ISSUE_NUM');				// 接頭語
    					$_issue_num['client_no']        = $_SESSION['c_memGrp'];								// クライアントNO
    					$_issue_num['customer_no']      = $value['cm_seq'];										// 顧客NO
    					$_issue_num['issue_class']      = 1;													// 一括発行=1,個別発行=2
    					$_issue_num['issue_accounting'] = 'A';													// 「通常（固定or成果）:A」
    					$_issue_num['issue_suffix']     = $set_data_iv['iv_seq_suffix'];						// 枝番
    					$_issue_num['issue_yymm']       = $input_post['iv_issue_yymm'];							// 発行年月
    					$_issue_num['issue_re']         = $get_iv_data[0]['iv_reissue'];						// 再発行

    					$set_data_iv['iv_slip_no']      = $this->commoninvoice->issue_num($_issue_num);

    					$this->iv->update_invoice($set_data_iv);

    					// 履歴ファイルを作成
    					$this->iv->insert_invoice_history($set_data_iv);

    				}

    				// 明細データ作成
    				foreach($list_detail as $key => $val)
    				{

			    		$set_data_ivd = array();

			    		$set_data_ivd['ivd_iv_seq']         = $set_data_iv['iv_seq'];
						$set_data_ivd['ivd_pj_seq']         = $val['pj_seq'];
						$set_data_ivd['ivd_iv_issue_yymm']  = $input_post['iv_issue_yymm'];						// 発行年月
						$set_data_ivd['ivd_iv_accounting']  = $val['pj_accounting'];							// 課金方式
						$set_data_ivd['ivd_item']           = $val['pj_keyword'];								// 請求項目=キーワード
						$set_data_ivd['ivd_qty']            = 1;												// 数量
						$set_data_ivd['ivd_price']          = $val['pj_billing'];								// 単価
						$set_data_ivd['ivd_total']          = $val['pj_billing'];								// 金額

						if ($_new_data == TRUE)
						{

							// 新規
							$set_data_ivd['ivd_seq_suffix'] = 1;
							$get_ivd_seq = $this->ivd->insert_invoice_detail($set_data_ivd);

							// 履歴ファイルを作成
							$set_data_ivd['ivd_seq'] = $get_ivd_seq;
							$this->ivd->insert_invoice_detail_history($set_data_ivd);

						} else {

							$get_ivd_date = $this->ivd->get_iv_id($get_iv_data[0]['iv_seq_suffix'], $set_data_ivd['ivd_iv_seq'], $set_data_ivd['ivd_pj_seq']);

							if (count($get_ivd_date) == 0)
							{

								// 案件が追加された場合

								$set_data_ivd['ivd_seq_suffix'] = $get_iv_data[0]['iv_seq_suffix'] + 1;
								$get_ivd_seq = $this->ivd->insert_invoice_detail($set_data_ivd);

								// 履歴ファイルを作成
								$set_data_ivd['ivd_seq'] = $get_ivd_seq;
								$this->ivd->insert_invoice_detail_history($set_data_ivd);

							} else {

								// 既存：UPDATE
								$set_data_ivd['ivd_seq']        = $get_ivd_date[0]['ivd_seq'];
								$set_data_ivd['ivd_seq_suffix'] = $get_ivd_date[0]['ivd_seq_suffix'] + 1;

								if ($get_iv_data[0]['iv_status'] != 9)
								{
									$set_data_ivd['ivd_status'] = $get_ivd_date[0]['ivd_status'];
								} else {
									$set_data_ivd['ivd_status'] = 0;
								}

								$this->ivd->update_invoice_detail($set_data_ivd);

								// 履歴ファイルを作成
								$this->ivd->insert_invoice_detail_history($set_data_ivd);

							}
						}
    				}
    			}
    		}

    		// トランザクション・COMMIT
    		$this->db->trans_complete();                                    		// trans_rollback & trans_commit
    		if ($this->db->trans_status() === FALSE)
    		{
    			log_message('error', 'CLIENT::[Data_create -> fix_cal()]：固定請求書一括作成処理 トランザクションエラー');
    		}
    	}

    	// 初期値セット
    	$this->_ym_item_set();

    	$this->view('data_create/index.tpl');

    }

    // 月額請求書データ 手動作成：成果報酬
    public function result_cal()
    {

    	$input_post = $this->input->post();

    	print_r($input_post);


    	$this->view('invoicelist/data_create.tpl');

    }

    // 月額請求書データ 手動作成：固定 + 成果報酬
    public function mix_cal()
    {

    	$input_post = $this->input->post();

    	print_r($input_post);


    	$this->view('invoicelist/data_create.tpl');

    }

    // 請求データ作成年月 初期値セット
    private function _ym_item_set()
    {

    	// 固定請求年月のセット <- (当月) から表示（過去一年分）
    	$date = new DateTime();
    	$_date_ym = $date->format('Ym');
    	$opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
    	for ($i = 1; $i < 12; $i++) {
    		$_date_ym = $date->modify('-1 months')->format('Ym');
    		$opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
    	}

     	// 成果請求年月のセット <- (当月 - 1) から表示
    	$date = new DateTime();
    	for ($i = 1; $i < 12; $i++) {
    		$_date_ym = $date->modify('-1 months')->format('Ym');
    		$opt_date_res[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
    	}

    	$this->smarty->assign('options_date_fix', $opt_date_fix);
    	$this->smarty->assign('options_date_res', $opt_date_res);

    }

    // 消費税計算
//     private function _tax_calculation($_subtotal)
//     {

//     	$this->config->load('config_comm');
//     	$_zeiritsu = $this->config->item('INVOICE_TAX');
//     	$_zeinuki  = $this->config->item('INVOICE_TAXOUT');
//     	$_hasuu    = $this->config->item('INVOICE_TAX_CAL');

//     	$zeigaku = $_subtotal * $_zeiritsu;

//     	if ($_zeinuki == 0)														// 税抜計算
//     	{
// 	    	// 端数計算
//     		switch( $_hasuu )
//     		{
//     			case 0:

//     				// 切り上げ
//     				$tax_total = $zeigaku + 0.9;
//     				break;

//     			case 1:

//     				// 切り捨て
//     				break;

//     			case 2:
//     			default:

//     				// 四捨五入
//     				$tax_total = $zeigaku + 0.5;
//     				break;
//     		}

//     		$result = floor( $tax_total );

//     	} else {																// 税込計算

//     		$result = 0;

//     	}

//     	return $result;

//     }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック
    private function _set_validation02()
    {

    	$rule_set = array(
    			array(
    					'field'   => 'iv_issue_date01',
    					'label'   => '発効日指定',
    					'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    			array(
    					'field'   => 'iv_pay_date01',
    					'label'   => '振込期日指定',
    					'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}

