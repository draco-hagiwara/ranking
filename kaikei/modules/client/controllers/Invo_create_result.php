<?php

class Invo_create_result extends MY_Controller
{

    /*
     *  請求書データの一括作成処理（成功報酬関連）
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

    }

    // 請求書一括作成処理TOP
    public function index()
    {

    	// バリデーション・チェック
    	$this->_set_validation();

    	// 初期値セット
    	$this->_ym_item_set();

    	$this->view('invo_create_fix/index.tpl');

    }

    // 月額請求書データ 一括作成：成果報酬 + 固定報酬
    public function result_cal()
    {

    	$input_post = $this->input->post();

    	$this->load->model('Customer',       'cm',  TRUE);
    	$this->load->model('Account',        'ac',  TRUE);
    	$this->load->model('Project',        'pj',  TRUE);
    	$this->load->model('Project_detail', 'pjd', TRUE);
    	$this->load->model('Invoice',        'iv',  TRUE);
    	$this->load->model('Invoice_detail', 'ivd', TRUE);
    	$this->load->library('lib_invoice');
    	$this->config->load('config_comm');

    	// バリデーション・チェック
    	$this->_set_validation02();
    	if ($this->form_validation->run() == TRUE)
    	{

    		// 有効な「顧客情報」を抽出
    		$opt_timing = $this->config->item('CUSTOMER_CM_INVO_TIMING');
    		if ($input_post['_submit'] == "save_oly")
    		{
    			$cm_list = $this->cm->get_ivlist($opt_timing['result'], $input_post['iv_cm_seq02']);
    		} else {
    			$cm_list = $this->cm->get_ivlist($opt_timing['result']);
    		}

    		// 発行月の発行通番を取得
    		$_invo_serial_num  = $this->lib_invoice->issue_serial_num($input_post['iv_issue_yymm']);
    		$_serial_num_start = $_invo_serial_num;

    		// トランザクション・START
    		$this->db->trans_strict(FALSE);                                 		// StrictモードをOFF
    		$this->db->trans_start();                                       		// trans_begin

    		// 受注案件情報から個々のデータを取得
    		foreach($cm_list as $key => $value)
    		{

    			/*
    			 * ここからは、順位チェックツール作成後に見直しが必要！
    			 */

    			if ($input_post['_submit'] == "save_oly")
    			{
    				$get_pj_list = $this->cm->get_ivlist_result($value['cm_seq'], TRUE);
    				$_invo_class = "C";
    			} else {
    				$get_pj_list = $this->cm->get_ivlist_result($value['cm_seq'], FALSE);
    				$_invo_class = "B";
    			}

    			$list_detail = array();												// 有効データ
    			$_subtotal = 0;														// 合計金額集計
    			$_salse_info = "";													// 売上種別
    			$_tmp_salesinfo = FALSE;
    			$_invo_info  = "";													// 請求種別
    			$cnt = 0;
    			foreach($get_pj_list as $key => $val)
    			{

    				// 契約期間チェック
    				$_project_date = $this->lib_invoice->issue_project_date($input_post['iv_issue_yymm'], $val['pj_start_date'], $val['pj_end_date'], $cm_list[0]['cm_collect']);

    				if (($_project_date['str'] <= $_project_date['now']) && ($_project_date['now'] <= $_project_date['end']))
    				{

    					// 対象データの一時保管
    					$list_detail[$cnt] = $val;

    					/*
    					 * 売上種別の判定：全て混合で出力するとの仕様なので、判定が面倒なのでpj_accounting順に設定していく。
    					 * 請求種別：前受と赤伝をチェック
    					 */
    					if ($_tmp_salesinfo == FALSE)
    					{
	    					$_salse_info = $val['pj_accounting'];
	    					if ($val['pj_accounting'] == 8)
	    					{
	    						$_invo_info = "Y";
	    					} elseif ($val['pj_accounting'] == 9) {
	    						$_invo_info = "Z";
	    					} else {
	    						$_invo_info = "X";
	    					}

	    					$_tmp_salesinfo = TRUE;
	    				}

    					/*
    					 * ここは別途ロジックを組み直し
    					 */
    					// 「合計」金額集計
    					if ($val['pj_accounting'] == 2)
    					{
    						$_subtotal = $_subtotal + $val['pjd_billing'];
    					} elseif ($val['pj_accounting'] == 3)  {
    						$_subtotal = $_subtotal + $val['pj_billing'] + $val['pjd_billing'];
    					} else {
    						$_subtotal = $_subtotal + $val['pj_billing'];
    					}

    					$cnt++;

    				}
    			}

    			// 明細データ有無チェック
    			$set_data_iv  = array();
    			if (count($list_detail))
    			{

    				$set_data_iv['iv_cm_seq']         = $value['cm_seq'];
    				$set_data_iv['iv_subtotal']       = $_subtotal;												// 小計

    				// 消費税計算
    				$_issue_tax['zeiritsu'] = $this->config->item('INVOICE_TAX');
    				$_issue_tax['zeinuki']  = $this->config->item('INVOICE_TAXOUT');
    				$_issue_tax['hasuu']    = $this->config->item('INVOICE_TAX_CAL');
    				$set_data_iv['iv_tax']  = $this->lib_invoice->cal_tax($_subtotal, $_issue_tax);				// 税額

    				$set_data_iv['iv_total']          = $_subtotal + $set_data_iv['iv_tax'];					// 合計
    				$set_data_iv['iv_issue_date']     = $input_post['iv_issue_date01'];							// 発行日
    				$set_data_iv['iv_collect']        = $value['cm_collect'];									// 回収サイト

    				// 振込期日＆売上月度計算
    				$_collect_date = $this->lib_invoice->issue_collect($value['cm_collect'], $input_post['iv_issue_yymm']);

    				$set_data_iv['iv_pay_date']       = $_collect_date['pay_date'];								// 振込期日
    				$set_data_iv['iv_issue_yymm']     = $input_post['iv_issue_yymm'];							// 発行年月
    				$set_data_iv['iv_salse_yymm']     = $_collect_date['salse_yymm'];							// 売上月度

    				if ($value['cm_flg_iv'] == 0)																// 発行先住所
    				{
    					$set_data_iv['iv_company_cm'] = $value['cm_company'];
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
    					$set_data_iv['iv_company_cm'] = $value['cm_company'];
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

    				$get_iv_data[0]['iv_sales_date']  = NULL;													// 売上日

    				// 担当営業名を取得
    				$get_salesman = $this->ac->get_pj_salesman($value['cm_salesman'], 'seorank');
    				$set_data_iv['iv_salesman']      = $get_salesman[0]['ac_name01'] . '　' . $get_salesman[0]['ac_name02'];
    				$set_data_iv['iv_salesman_id']   = $value['cm_salesman'];

    				// 請求書データ : 既存データ有無のチェック
    				$_new_data = FALSE;
    				$get_iv_data = $this->iv->get_iv_cm_seq($value['cm_seq'], $input_post['iv_issue_yymm']);

    				/*
    				 * 個別作成時は新規で請求書番号を発行して作成する
    				 */
//     				if (count($get_iv_data) == 0)
    				if ((count($get_iv_data) == 0) || ($input_post['_submit'] == "save_oly"))
    				{

    					// 請求書データ : 新規作成
    					$set_data_iv['iv_seq_suffix'] = 1;														// 枝番
    					$_invo_re                     = 0;      												// 再発行

    					// 請求書発行番号 :: 【LA101-KT-BX001-1611】
    					$set_data_iv['iv_slip_no']    = $this->lib_invoice->issue_num($value['cm_seq'],
														    							$input_post['iv_issue_yymm'],
														    							$_salse_info,
														    							$_invo_serial_num,
														    							$_invo_info,
														    							$_invo_re,
    																					$_invo_class
    					);

    					$get_iv_seq = $this->iv->insert_invoice($set_data_iv);

    					// 履歴ファイルを作成
    					$set_data_iv['iv_seq'] = $get_iv_seq;
    					$this->iv->insert_invoice_history($set_data_iv);

    					// 発行通番カウントアップ
    					$_invo_serial_num++;

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

    					// 発行通番は現行のものを使用
    					$_invo_num = explode("-", $get_iv_data[0]['iv_slip_no']);
    					$_invo_num = substr($_invo_num[2], 2, 4);

    					// 請求書発行番号 :: 【LA101-KT-BX001-1611】
    					$set_data_iv['iv_slip_no']    = $this->lib_invoice->issue_num($value['cm_seq'],
														    							$input_post['iv_issue_yymm'],
														    							$_salse_info,
														    							$_invo_num,
														    							$_invo_info,
														    							$get_iv_data[0]['iv_reissue'],
    																					$_invo_class
    					);

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
    					$set_data_ivd['ivd_item_url']       = $val['pj_url'];									// 請求項目=キーワードURL

    					if (($val['pj_accounting'] == 2) || ($val['pj_accounting'] == 3))						// 請求項目=コメントその他
    					{
    						if ($val['pjd_rank_str'] == $val['pjd_rank_end'])
    						{
    							$set_data_ivd['ivd_item_comment'] = "ランクイン範囲（" . $val['pjd_rank_str'] . "位）";
    						} else {
    							$set_data_ivd['ivd_item_comment'] = "ランクイン範囲（" . $val['pjd_rank_str'] . "～" . $val['pjd_rank_end'] . "位）";
    						}

    						/*
    						 * ここにランクイン回数が加わる！
    						 */
    					}

//     					$set_data_ivd['ivd_qty']            = 1;												// 数量

    					if ($val['pj_accounting'] == 2)
    					{
    						// 日数を計算
    						$_create_date = substr($input_post['iv_issue_yymm'], 0, 4) . '-' . substr($input_post['iv_issue_yymm'], 4, 2);
    						$date = new DateTime($_create_date);
    						$_nisuu = date($date->modify('-1 months')->format('t'));							// 前月の日数
    						$set_data_ivd['ivd_qty']        = $_nisuu;											// 日数（数量）

    						$set_data_ivd['ivd_price']      = $val['pjd_billing'];								// 単価
    						$set_data_ivd['ivd_total']      = $set_data_ivd['ivd_price'];						// 金額
    					} elseif ($val['pj_accounting'] == 3)  {
    						// 日数を計算
    						$_create_date = substr($input_post['iv_issue_yymm'], 0, 4) . '-' . substr($input_post['iv_issue_yymm'], 4, 2);
    						$date = new DateTime($_create_date);
    						$_nisuu = date($date->modify('-1 months')->format('t'));							// 前月の日数
    						$set_data_ivd['ivd_qty']        = $_nisuu;											// 日数（数量）

    						$set_data_ivd['ivd_price']      = $val['pj_billing'] + $val['pjd_billing'];
    						$set_data_ivd['ivd_total']      = $set_data_ivd['ivd_price'];
    						$_subtotal = $_subtotal + $val['pj_billing'] + $val['pjd_billing'];
    					} else {
    						$set_data_ivd['ivd_qty']        = 1;												// 数量

    						$set_data_ivd['ivd_price']      = $val['pj_billing'];
    						$set_data_ivd['ivd_total']      = $set_data_ivd['ivd_price'];
    					}

    					// 担当営業名を取得
    					$set_data_ivd['ivd_pj_salesman']    = $val['pj_salesman'];

    					// 請求書データの作成
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

    		// 発行通番の書き込み
    		if ($_serial_num_start != $_invo_serial_num)
    		{
    			$this->lib_invoice->issue_serial_num_update($input_post['iv_issue_yymm'], $_invo_serial_num);
    		}

    		// トランザクション・COMMIT
    		$this->db->trans_complete();                                    		// trans_rollback & trans_commit
    		if ($this->db->trans_status() === FALSE)
    		{
    			log_message('error', 'CLIENT::[Invo_create_result -> result_cal()]：成功報酬請求書一括作成処理 トランザクションエラー');
    		}

    	}

    	// 初期値セット
    	$this->_ym_item_set();

    	$this->view('invo_create_fix/index.tpl');

    }

    // 請求データ作成年月 初期値セット
    private function _ym_item_set()
    {

    	// 固定請求年月のセット <- (当月) から表示（過去一年分）
    	$date = new DateTime();
    	$_date_ym = $date->modify('+1 months')->format('Ym');
//     	$_date_ym = $date->format('Ym');
    	$opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
    	for ($i = 1; $i < 12; $i++) {
    		$_date_ym = $date->modify('-1 months')->format('Ym');
    		$opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
    	}

     	// 成果請求年月のセット <- (当月 - 1) から表示
    	$date = new DateTime();
    	$_date_ym = $date->format('Ym');
    	$opt_date_res[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
    	for ($i = 1; $i < 12; $i++) {
    		$_date_ym = $date->modify('-1 months')->format('Ym');
    		$opt_date_res[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
    	}

    	$this->smarty->assign('options_date_fix', $opt_date_fix);
    	$this->smarty->assign('options_date_res', $opt_date_res);

    }

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
    					'field'   => 'iv_cm_seq02',
    					'label'   => '顧客番号',
    					'rules'   => 'trim|max_length[10]|is_numeric'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}

