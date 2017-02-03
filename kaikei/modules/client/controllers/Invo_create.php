<?php

class Invo_create extends MY_Controller
{

	/*
	 *  個別請求書データの作成処理
	 *
	 *    > 顧客情報一覧から作成
	 *    > 請求書一覧から作成
	 */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('mess', FALSE);

    }

    // 請求書画面からの新規登録
    public function invoice_iv()
    {

    	$input_post = $this->input->post();

    	$this->load->model('Invoice', 'iv',  TRUE);

    	// 請求書データから元データを取得
    	$get_iv_data = $this->iv->get_iv_seq($input_post['chg_seq']);

    	$this->smarty->assign('info', $get_iv_data[0]);

    	// バリデーション・チェック
    	$this->_set_validation();

    	// 初期値セット
    	$this->_item_set();
    	$this->_ym_item_set();

    	$this->smarty->assign('iv_company', $get_iv_data[0]['iv_company']);

    	$this->smarty->assign('tmp_remark', NULL);
    	$this->smarty->assign('tmp_memo',   NULL);

    	$this->view('invo_create/add_iv.tpl');

    }

    // 請求書情報 内容チェック
    public function add_iv()
    {

    	$input_post = $this->input->post();

    	// バリデーション・チェック
    	$this->_set_validation();
    	if ($this->form_validation->run() == FALSE)
    	{

    		$this->smarty->assign('iv_company_cm', $input_post['iv_company_cm']);

    		$this->smarty->assign('tmp_remark', $input_post['iv_remark']);
    		$this->smarty->assign('tmp_memo',   $input_post['iv_memo']);

    		if ($input_post['iv_remark'] != "")
    		{
    			$this->smarty->assign('tmp_remark', $input_post['iv_remark']);			// 備考を保持
    		}
    		if ($input_post['iv_memo'] != "")
    		{
    			$this->smarty->assign('tmp_memo', $input_post['iv_memo']);				// メモを保持
    		}

    		$this->smarty->assign('info', $input_post);

    		// 初期値セット
    		$this->_item_set();
    		$this->_ym_item_set();

    		$this->view('invo_create/add_iv.tpl');

    	} else {

    		$this->load->model('Invoice',        'iv',  TRUE);
    		$this->load->model('Invoice_detail', 'ivd', TRUE);
    		$this->load->model('Account',        'ac',  TRUE);
    		$this->load->library('lib_invoice');
    		$this->config->load('config_comm');

    		// トランザクション・START
    		$this->db->trans_strict(FALSE);                                         // StrictモードをOFF
    		$this->db->trans_start();                                               // trans_begin

    		// 請求書データの作成
    		$this->_create_invoice($input_post, "C");

    		// トランザクション・COMMIT
    		$this->db->trans_complete();                                            // trans_rollback & trans_commit
    		if ($this->db->trans_status() === FALSE)
    		{
    			log_message('error', 'CLIENT::[Invo_create -> add_iv()]：請求書 個別登録処理 トランザクションエラー');
    		} else {
    			$this->smarty->assign('mess',  "登録が完了しました。");
    		}

    		redirect('/invoicelist/');
//     		$this->view('invoicelist/index.tpl');
    	}

    }

    // 顧客画面からの新規登録
    public function invoice_cm()
    {

    	$input_post = $this->input->post();

    	$this->load->model('Customer', 'cm', TRUE);
    	$this->load->model('Project',  'pj', TRUE);

    	// 顧客情報を取得
    	$get_cm_data = $this->cm->get_cm_seq($input_post['chg_seq']);

    	// 請求書住所が別の場合
    	if ($get_cm_data[0]['cm_flg_iv'] == 1)
    	{
    		$get_cm_data[0]['cm_company']    = $get_cm_data[0]['cm_company_iv'];
    		$get_cm_data[0]['cm_department'] = $get_cm_data[0]['cm_department_iv'];
    		$get_cm_data[0]['cm_person01']   = $get_cm_data[0]['cm_person01_iv'];
    		$get_cm_data[0]['cm_person02']   = $get_cm_data[0]['cm_person02_iv'];
    		$get_cm_data[0]['cm_zip01']      = $get_cm_data[0]['cm_zip01_iv'];
    		$get_cm_data[0]['cm_zip02']      = $get_cm_data[0]['cm_zip02_iv'];
    		$get_cm_data[0]['cm_pref']       = $get_cm_data[0]['cm_pref_iv'];
    		$get_cm_data[0]['cm_addr01']     = $get_cm_data[0]['cm_addr01_iv'];
    		$get_cm_data[0]['cm_addr02']     = $get_cm_data[0]['cm_addr02_iv'];
    		$get_cm_data[0]['cm_buil']       = $get_cm_data[0]['cm_buil_iv'];
    	}

    	$get_cm_data[0]['cm_company_cm'] = $get_cm_data[0]['cm_company'];						// 請求先会社名

    	$this->smarty->assign('info', $get_cm_data[0]);

    	// 受注案件情報を取得
    	$_iv_type = 0;														// 課金方式：：固定=0/成果=1/固+成=2
    	$get_pj_list = $this->pj->get_pj_cm_seq($input_post['chg_seq'], $_iv_type, $_SESSION['c_memGrp'], 'seorank', TRUE);

	    $cnt = 0;
    	if (count($get_pj_list) >= 1)
    	{
	    	$list_detail = array();
	    	$_subtotal = 0;
	    	foreach($get_pj_list as $key => $val)
	    	{

	    		// 契約期間チェック
	    		$date_now = new DateTime();
	    		$date_now = $date_now->modify('first day of last months')->format('Y-m-d');		// 請求データ作成の指定年月の前月1日
	    		$date_str = new DateTime($val['pj_start_date']);
	    		$date_str = $date_str->format('Y-m-d');
	    		$date_end = new DateTime($val['pj_end_date']);
	    		$date_end = $date_end->format('Y-m-d');

	    		if (($date_str <= $date_now) && ($date_now <= $date_end))
	    		{

	    			// 対象データの一時保管
	    			$set_pj_data[$cnt]['pj_keyword'] = $val['pj_keyword'];
	    			$set_pj_data[$cnt]['qty']        = 1;
	    			$set_pj_data[$cnt]['price']      = $val['pj_billing'];
	    			$set_pj_data[$cnt]['pj_billing'] = $val['pj_billing'];

	    			$set_pj_data[$cnt]['pj_url']     = $val['pj_url'];

	    			$cnt++;

	    		}
	    	}
    	}

    	// 空データ作成。2行分
    	for ($i=0; $i<=1; $i++)
    	{
    		// 対象データの一時保管
    		$set_pj_data[$cnt]['pj_keyword'] = "";
    		$set_pj_data[$cnt]['qty']        = 0;
    		$set_pj_data[$cnt]['price']      = 0;
    		$set_pj_data[$cnt]['pj_billing'] = 0;

    		$set_pj_data[$cnt]['pj_url']     = "";

    		$cnt++;
    	}

    	$this->smarty->assign('info_ivd', $set_pj_data);

    	// バリデーション・チェック
    	$this->_set_validation();

    	// 初期値セット
    	$this->_item_set();
    	$this->_ym_item_set();

    	$this->smarty->assign('tmp_remark', NULL);
    	$this->smarty->assign('tmp_memo',   NULL);

    	$this->view('invo_create/add_cm.tpl');

    }

    // 請求書情報 内容チェック
    public function add_cm()
    {

    	$input_post = $this->input->post();

    	// バリデーション・チェック
    	$this->_set_validation();
    	if ($this->form_validation->run() == FALSE)
    	{

    		// 顧客情報セット
    		$this->smarty->assign('tmp_remark', $input_post['iv_remark']);
    		$this->smarty->assign('tmp_memo',   $input_post['iv_memo']);

    		if ($input_post['iv_remark'] != "")
    		{
    			$this->smarty->assign('tmp_remark', $input_post['iv_remark']);			// 備考を保持
    		}
    		if ($input_post['iv_memo'] != "")
    		{
    			$this->smarty->assign('tmp_memo', $input_post['iv_memo']);				// メモを保持
    		}

    		// 文字列置き換え：「iv_」→「cm_」
    		foreach($input_post as $key => $value)
    		{
    			$_set_key = str_replace("iv_", "cm_", $key);
    			$_set_input_post[$_set_key] = $value;
    		}

    		$this->smarty->assign('info', $_set_input_post);

    		// 受注案件情報セット
    		for ($i=0; isset($input_post["ivd_item" . $i]); $i++)
    		{
    			$set_pj_data[$i]['pj_keyword'] = $input_post["ivd_item" .  $i];
    			$set_pj_data[$i]['qty']        = $input_post["ivd_qty" .   $i];
    			$set_pj_data[$i]['price']      = $input_post["ivd_price" . $i];
    			$set_pj_data[$i]['pj_billing'] = $input_post["ivd_total" . $i];

    			$set_pj_data[$i]['pj_url']     = $input_post["ivd_item_url" .   $i];

    		}

    		$this->smarty->assign('info_ivd', $set_pj_data);

    		// 初期値セット
    		$this->_item_set();
    		$this->_ym_item_set();

    		$this->view('invo_create/add_cm.tpl');

    	} else {

    		$this->load->model('Invoice',        'iv',  TRUE);
    		$this->load->model('Invoice_detail', 'ivd', TRUE);
    		$this->load->model('Account',        'ac',  TRUE);
    		$this->load->library('lib_invoice');
    		$this->config->load('config_comm');

    		// トランザクション・START
    		$this->db->trans_strict(FALSE);                                         // StrictモードをOFF
    		$this->db->trans_start();                                               // trans_begin

    		// 請求書データの作成
    		$this->_create_invoice($input_post, "C");

    		// トランザクション・COMMIT
    		$this->db->trans_complete();                                            // trans_rollback & trans_commit
    		if ($this->db->trans_status() === FALSE)
    		{
    			log_message('error', 'CLIENT::[Invo_create -> add_cm()]：請求書 個別登録処理 トランザクションエラー');
    		} else {
    			$this->smarty->assign('mess',  "登録が完了しました。");
    		}

    		redirect('/invoicelist/');

    	}
    }

    // 初期値セット
    private function _item_set()
    {

        // ステータス 選択項目セット
    	$opt_iv_status = array(
    							"0" => "未　発　行",
						);

    	// 課金方式
    	$this->config->load('config_comm');
    	$opt_iv_accounting = $this->config->item('INVOICE_ACCOUNTING_SINGLE');

    	// 回収サイトのセット
    	$opt_iv_collect = $this->config->item('CUSTOMER_CM_COLLECT');

    	// 口座種別のセット
    	$opt_iv_kind = $this->config->item('CUSTOMER_CM_KIND');

    	$this->smarty->assign('options_iv_status',     $opt_iv_status);
    	$this->smarty->assign('options_iv_accounting', $opt_iv_accounting);
    	$this->smarty->assign('options_iv_collect',    $opt_iv_collect);
    	$this->smarty->assign('options_iv_kind',       $opt_iv_kind);

    }

    // 請求データ作成年月 初期値セット
    private function _ym_item_set()
    {

    	// 固定請求年月のセット（過去一年分）
    	$date = new DateTime();
    	$_date_ym = $date->modify('+1 months')->format('Ym');
    	$opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
    	for ($i = 1; $i < 12; $i++) {
    		$_date_ym = $date->modify('-1 months')->format('Ym');
    		$opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
    	}

     	// 成果請求年月のセット
    	$date = new DateTime();
    	for ($i = 1; $i < 12; $i++) {
    		$_date_ym = $date->modify('-1 months')->format('Ym');
    		$opt_date_res[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
    	}

    	$this->smarty->assign('options_date_fix', $opt_date_fix);
    	$this->smarty->assign('options_date_res', $opt_date_res);

    }

    // 請求書データの作成
    private function _create_invoice($input_post, $method = "B")
    {

    	$set_iv_data = array();

    	// 顧客情報からは変換
    	if (isset($input_post['iv_seq']))
    	{
    		$input_post['iv_cm_seq'] = $input_post['iv_seq'];
    	}

    	$set_iv_data = $input_post;

    	$_suffix = 1;
    	$set_iv_data['iv_seq_suffix'] = $_suffix;                                       // 枝番
    	$set_iv_data['iv_status']     = $input_post['iv_status'];                       // ステータス
    	$set_iv_data['iv_reissue']    = 0;                                              // 再発行
    	if ($input_post['iv_status'] == 9)
    	{
    		$set_iv_data['iv_delflg']  = 1;
    	} elseif ($input_post['iv_status'] == 1) {
    		$set_iv_data['iv_reissue'] = 1;

    		$date = new DateTime();
    		$set_iv_data['iv_sales_date'] = $date->format('Y-m-d');                     // 売上日
    	}
    	$set_iv_data['iv_method'] = $method;                                            // 請求書発行方式:一括発行=B,個別発行=C
    	$set_iv_data['iv_issue_yymm'] = $input_post['iv_issue_yymm'];                   // 発行年月

    	// 売上月度計算
    	$_collect_date = $this->lib_invoice->issue_collect($input_post['iv_collect'], $input_post['iv_issue_yymm']);
    	$set_iv_data['iv_salse_yymm'] = $_collect_date['salse_yymm'];					// 売上月度

    	// 請求書発行番号 :: 【LA101-KT-BX001-1611】
    	$_salse_info = $input_post['iv_accounting'];
    	if ($_salse_info == 8)
    	{
    		$_invo_info = "Y";
    	} elseif ($_salse_info == 9) {
    		$_invo_info = "Z";
    	} else {
    		$_invo_info = "X";
    	}

    	// 発行月の発行通番を取得
    	$_invo_serial_num  = $this->lib_invoice->issue_serial_num($input_post['iv_issue_yymm']);

    	$_invo_class      = "C";

    	$set_iv_data['iv_slip_no']    = $this->lib_invoice->issue_num(  $input_post['iv_cm_seq'],
														    			$input_post['iv_issue_yymm'],
														    			$_salse_info,
														    			$_invo_serial_num,
														    			$_invo_info,
														    			$set_iv_data['iv_reissue'],
														    			$_invo_class
								    	);

    	$set_iv_data['iv_remark']     = $input_post['iv_remark'];                       // 備考
    	$set_iv_data['iv_memo']       = $input_post['iv_memo'];                         // メモ

    	// 小計の計算
    	/*
    	 * 請求項目にスペースが入っている行以下は無視！
    	 */
    	$set_iv_data['iv_subtotal'] = 0;
    	for ($i=0; $input_post["ivd_item" . $i] != ""; $i++)
    	{
    		$set_iv_data['iv_subtotal'] = $set_iv_data['iv_subtotal'] + $input_post["ivd_total" . $i];
    	}

    	// 消費税計算
    	$_issue_tax['zeiritsu'] = $this->config->item('INVOICE_TAX');
    	$_issue_tax['zeinuki']  = $this->config->item('INVOICE_TAXOUT');
    	$_issue_tax['hasuu']    = $this->config->item('INVOICE_TAX_CAL');

    	$set_iv_data['iv_tax']  = $this->lib_invoice->cal_tax($set_iv_data['iv_subtotal'], $_issue_tax);

    	// 合計金額計算
    	$set_iv_data['iv_total'] = $set_iv_data['iv_subtotal'] + $set_iv_data['iv_tax'];

    	// 担当営業名を取得
    	if (ctype_digit($input_post['iv_salesman']))
    	{
    		$get_salesman = $this->ac->get_pj_salesman($input_post['iv_salesman'], 'seorank');
    		$set_iv_data['iv_salesman']    = $get_salesman[0]['ac_name01'] . '　' . $get_salesman[0]['ac_name02'];
    		$set_iv_data['iv_salesman_id'] = $input_post['iv_salesman'];
    	}

    	// 不要パラメータ削除
    	unset($set_iv_data["iv_seq"]);
    	unset($set_iv_data["_submit"]);
    	for ($i=0; isset($input_post["ivd_item" . $i]); $i++)
    	{
    		unset($set_iv_data["ivd_item"  . $i]);
    		unset($set_iv_data["ivd_qty"   . $i]);
    		unset($set_iv_data["ivd_price" . $i]);
    		unset($set_iv_data["ivd_total" . $i]);

    		unset($set_iv_data["ivd_item_url" .   $i]);

    	}

    	// tb_invoice 更新
    	$get_iv_seq = $this->iv->insert_invoice($set_iv_data);

    	// tb_invoice_h 作成
    	$set_iv_data['iv_seq'] = $get_iv_seq;
    	$this->iv->insert_invoice_history($set_iv_data);

    	// 明細データ新規レコードの追加
    	for ($i=0; $input_post["ivd_item" . $i] != ""; $i++)
    	{

    		$set_ivd_data = array();
    		$set_ivd_data['ivd_seq_suffix']    = $_suffix;
    		$set_ivd_data['ivd_iv_seq']        = $set_iv_data['iv_seq'];
    		$set_ivd_data['ivd_pj_seq']        = 0;                                        // 案件SEQ=「0」
    		$set_ivd_data['ivd_iv_issue_yymm'] = $set_iv_data['iv_issue_yymm'];
    		$set_ivd_data['ivd_iv_accounting'] = $input_post['iv_accounting'];
    		$set_ivd_data['ivd_item']          = $input_post["ivd_item" . $i];
    		$set_ivd_data['ivd_qty']           = $input_post["ivd_qty" . $i];
    		$set_ivd_data['ivd_price']         = $input_post["ivd_price" . $i];
    		$set_ivd_data['ivd_total']         = $input_post["ivd_total" . $i];

    		$set_ivd_data['ivd_item_url']           = $input_post["ivd_item_url" . $i];

    		$row_id = $this->ivd->insert_invoice_detail($set_ivd_data);

    		$set_ivd_data['ivd_seq']           = $row_id;
    		$this->ivd->insert_invoice_detail_history($set_ivd_data);

    	}
    }

    // フォーム・バリデーションチェック : クライアント追加
    private function _set_validation()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'iv_status',
    					'label'   => 'ステータス選択',
    					'rules'   => 'trim|required|max_length[2]'
    			),
    			array(
    					'field'   => 'iv_accounting',
    					'label'   => '課金方式選択',
    					'rules'   => 'trim|required|max_length[2]'
    			),
    			array(
    					'field'   => 'iv_issue_yymm',
    					'label'   => '発行年月選択',
    					'rules'   => 'trim|required|max_length[6]'
    			),
    			array(
    					'field'   => 'iv_issue_date',
    					'label'   => '発効日指定',
    					'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    			array(
    					'field'   => 'iv_pay_date',
    					'label'   => '振込期日指定',
    					'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    			array(
    					'field'   => 'iv_collect',
    					'label'   => '回収サイト',
    					'rules'   => 'trim|max_length[1]'
    			),
    			array(
    					'field'   => 'iv_company_cm',
    					'label'   => '会社名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'iv_company',
    					'label'   => '会社名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'iv_zip01',
    					'label'   => '郵便番号（3ケタ）',
    					'rules'   => 'trim|required|exact_length[3]|is_numeric'
    			),
    			array(
    					'field'   => 'iv_zip02',
    					'label'   => '郵便番号（4ケタ）',
    					'rules'   => 'trim|required|exact_length[4]|is_numeric'
    			),
    			array(
    					'field'   => 'iv_pref',
    					'label'   => '都道府県',
    					'rules'   => 'trim|required|max_length[4]'
    			),
    			array(
    					'field'   => 'iv_addr01',
    					'label'   => '市区町村',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'iv_addr02',
    					'label'   => '町名・番地',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'iv_buil',
    					'label'   => 'ビル・マンション名など',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'iv_department',
    					'label'   => '所属部署',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'iv_person01',
    					'label'   => '担当者姓',
    					'rules'   => 'trim|required|max_length[20]'
    			),
    			array(
    					'field'   => 'iv_person02',
    					'label'   => '担当者名',
    					'rules'   => 'trim|required|max_length[20]'
    			),
//     			array(
//     					'field'   => 'iv_bank_cd',
//     					'label'   => '銀行CD',
//     					'rules'   => 'trim|required|max_length[4]|is_numeric'
//     			),
//     			array(
//     					'field'   => 'iv_bank_nm',
//     					'label'   => '銀行名',
//     					'rules'   => 'trim|required|max_length[50]'
//     			),
//     			array(
//     					'field'   => 'iv_branch_cd',
//     					'label'   => '支店CD',
//     					'rules'   => 'trim|required|max_length[3]|is_numeric'
//     			),
//     			array(
//     					'field'   => 'iv_branch_nm',
//     					'label'   => '支店名',
//     					'rules'   => 'trim|required|max_length[50]'
//     			),
//     			array(
//     					'field'   => 'iv_kind',
//     					'label'   => '口座種別(普通/当座)',
//     					'rules'   => 'trim|required|max_length[1]'
//     			),
//     			array(
//     					'field'   => 'iv_account_no',
//     					'label'   => '口座番号',
//     					'rules'   => 'trim|required|max_length[10]|is_numeric'
//     			),
//     			array(
//     					'field'   => 'iv_account_nm',
//     					'label'   => '口座名義',
//     					'rules'   => 'trim|required|max_length[50]'
//     			),
    			array(
    					'field'   => 'iv_tag',
    					'label'   => 'タグ設定',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'iv_memo',
    					'label'   => '備考',
    					'rules'   => 'trim|max_length[1000]'
    			),
    			array(
    					'field'   => 'iv_memo_iv',
    					'label'   => '請求書：備考',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'ivd_item0',
    					'label'   => '請求項目',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'ivd_qty0',
    					'label'   => '数量',
    					'rules'   => 'trim|is_numeric'
    			),
    			array(
    					'field'   => 'ivd_price0',
    					'label'   => '単価',
    					'rules'   => 'trim|is_numeric'
    			),
    			array(
    					'field'   => 'ivd_total0',
    					'label'   => '金額',
    					'rules'   => 'trim|is_numeric'
    			),
    			array(
    					'field'   => 'ivd_url0',
    					'label'   => '対象URL',
    					'rules'   => 'trim|regex_match[/^(https?)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/]|max_length[100]'
    			),
    			array(
    					'field'   => 'ivd_item1',
    					'label'   => '請求項目',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'ivd_qty1',
    					'label'   => '数量',
    					'rules'   => 'trim|is_numeric'
    			),
    			array(
    					'field'   => 'ivd_price1',
    					'label'   => '単価',
    					'rules'   => 'trim|is_numeric'
    			),
    			array(
    					'field'   => 'ivd_total1',
    					'label'   => '金額',
    					'rules'   => 'trim|is_numeric'
    			),
    			array(
    					'field'   => 'ivd_url1',
    					'label'   => '対象URL',
    					'rules'   => 'trim|regex_match[/^(https?)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/]|max_length[100]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}

