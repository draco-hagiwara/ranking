<?php

class Pdf_create extends MY_Controller
{

	/*
	 *  請求書ＰＤＦの作成処理
	 *
	 *    > 一覧から複数ＰＤＦ作成
	 *    > 請求書編集から作成
	 */

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

    // 請求書PDF 個別作成
    public function pdf_one()
    {

    	$input_post = $this->input->post();

    	// 「キャンセル」ボタンで更新＆一覧表示！
    	if ($input_post['submit'] == 'submit')
    	{

	    	$this->load->model('Invoice',        'iv',  TRUE);
	    	$this->load->model('Invoice_detail', 'ivd', TRUE);
	    	$this->load->library('Commoninvoice');
	    	$this->config->load('config_comm');

	    	// 請求書データの取得
	    	$get_iv_data = $this->iv->get_iv_seq($input_post['iv_seq']);

	    	// 明細データの取得
	    	$get_ivd_data = $this->ivd->get_iv_seq($input_post['iv_seq'], $get_iv_data[0]['iv_issue_yymm'], $get_iv_data[0]['iv_seq_suffix']);

	    	// バリデーション・チェック
	    	$this->_set_validation();


	    	// トランザクション・START
	    	$this->db->trans_strict(FALSE);                                 		// StrictモードをOFF
	    	$this->db->trans_start();                                       		// trans_begin

	    		// tb_invoice 更新 + 履歴作成
	    		$_slip_no = $this->_chg_invoice($get_iv_data[0], $get_ivd_data);
	    		$get_iv_data[0]['iv_slip_no'] = $_slip_no;

	    	// トランザクション・COMMIT
	    	$this->db->trans_complete();                                    		// trans_rollback & trans_commit
	    	if ($this->db->trans_status() === FALSE)
	    	{
	    		log_message('error', 'CLIENT::[Pdf_create -> pdf_one()]：請求書PDF 個別作成処理 トランザクションエラー');
	    	}

	    	// 雛形PDFのパス取得
	    	$this->load->helper('path');
	    	$list_path = '../public/images/pdf/receipt_list.pdf';
	    	$pdflist_path = set_realpath($list_path);

	    	// インストールパスを取得 :: /var/www/kaikei
	    	$list_path = '../';
	    	$base_path = set_realpath($list_path);

	    	// PDFライブラリ呼出
	    	$this->load->library('pdf');
	    	$this->pdf->pdf_one($get_iv_data[0], $get_ivd_data, $pdflist_path, $base_path);

    	} else {
    		redirect('/invoicelist/');
    	}

    }

    // 請求書PDF 一括作成
    public function pdf_invoice()
    {

    	// 更新対象データの取得
    	$input_post = $this->input->post();

    	// 「キャンセル」ボタンで更新＆一覧表示！
    	if ($input_post['_submit'] == 'submit')
    	{

	    	if (count($input_post) >= 3)
	    	{

		    	$this->load->model('Invoice',        'iv',  TRUE);
		    	$this->load->model('Invoice_detail', 'ivd', TRUE);
		    	$this->load->library('Commoninvoice');
		    	$this->config->load('config_comm');

		    	// 不要パラメータ削除
		    	unset($input_post["iv_issue_yymm"]) ;
		    	unset($input_post["invoice_all"]) ;
		    	unset($input_post["_submit"]) ;

		    	$list_cnt = count($input_post);
		    	$i = 0;
		    	foreach ($input_post as $key => $val)
		    	{

		    		$get_iv_data[$i]  = array();
		    		$get_ivd_data[$i] = array();

			    	// 請求書データの取得
			    	$get_iv_data[$i] = $this->iv->get_iv_seq($val);

			    	// 明細データの取得
			    	$get_ivd_data[$i] = $this->ivd->get_iv_seq($val, $get_iv_data[$i][0]['iv_issue_yymm'], $get_iv_data[$i][0]['iv_seq_suffix']);

			    	// トランザクション・START
			    	$this->db->trans_strict(FALSE);                                 		// StrictモードをOFF
			    	$this->db->trans_start();                                       		// trans_begin

			    		// tb_invoice 更新 + 履歴作成
			    		$_slip_no = $this->_chg_invoice($get_iv_data[$i][0], $get_ivd_data[$i]);
			    		$get_iv_data[$i][0]['iv_slip_no'] = $_slip_no;

			    	// トランザクション・COMMIT
			    	$this->db->trans_complete();                                    		// trans_rollback & trans_commit
			    	if ($this->db->trans_status() === FALSE)
			    	{
			    		log_message('error', 'CLIENT::[Pdf_create -> pdf_one()]：請求書PDF 個別作成処理 トランザクションエラー');
			    	}



			    	log_message('error', "xxxxxxxxxxxxxxxxxxxxxxxxxxx");
			    	log_message('error', count($get_ivd_data[$i]));


					$i++;

		    	}

		    	// バリデーション・チェック
		    	$this->_set_validation();

		    	// 雛形PDFのパス取得
		    	$this->load->helper('path');
		    	$list_path = '../public/images/pdf/receipt_list.pdf';
		    	$pdflist_path = set_realpath($list_path);

		    	// インストールパスを取得 :: /var/www/kaikei
		    	$list_path = '../';
		    	$base_path = set_realpath($list_path);

		    	// PDFライブラリ呼出
		    	$this->load->library('pdf');
	    		$this->pdf->pdf_batch($get_iv_data, $get_ivd_data, $pdflist_path, $base_path, $page_add = TRUE);

	    	}
    	}

    	redirect('/invoicelist/');

    }

    // tb_invoice 更新 + 履歴作成
    private function _chg_invoice($get_iv_data, $get_ivd_data)
    {

    	// データをセット
    	$set_data_iv = $get_iv_data;

    	// 売上データ作成有無の判定
    	if ($get_iv_data['iv_status'] == 0)
    	{
    		// 「未発行」→「発行済」
    		$date = new DateTime();
    		$set_data_iv['iv_sales_date'] = $date->format('Y-m-d');								// 売上日
    	}

    	$set_data_iv['iv_status']     = 1;														// ステータス：「発行済」

    	$set_data_iv['iv_seq_suffix'] = $get_iv_data['iv_seq_suffix'] + 1;						// 履歴カウント
    	$set_data_iv['iv_reissue']    = $get_iv_data['iv_reissue'] + 1;							// 発行カウント

    	// 請求書発行番号
    	$_issue_num['issue_num']      = $this->config->item('INVOICE_ISSUE_NUM');				// 接頭語
    	$_issue_num['client_no']      = $_SESSION['c_memGrp'];									// クライアントNO
    	$_issue_num['customer_no']    = $set_data_iv['iv_cm_seq'];								// 顧客NO
    	$_issue_num['issue_class']    = $get_iv_data['iv_method'];                        		// 一括発行=1,個別発行=2
    	if ($get_iv_data['iv_accounting'] == 0)
    	{
    		$_issue_num['issue_accounting'] = 'A';												// 「通常（固定or成果）:A」/「前受取:B」/「赤伝票:C」
    	} elseif ($get_iv_data['iv_accounting'] == 1) {
    		$_issue_num['issue_accounting'] = 'B';
    	} elseif ($get_iv_data['iv_accounting'] == 2) {
    		$_issue_num['issue_accounting'] = 'C';
    	} else {
    		$_issue_num['issue_accounting'] = 'x';
    	}
    	$_issue_num['issue_suffix']   = $set_data_iv['iv_seq_suffix'];							// 枝番
    	$_issue_num['issue_yymm']     = $get_iv_data['iv_issue_yymm'];							// 発行年月
    	$_issue_num['issue_re']       = $set_data_iv['iv_reissue'];								// 再発行

    	$set_data_iv['iv_slip_no']    = $this->commoninvoice->issue_num($_issue_num);

    	// 不要パラメータ削除
    	unset($set_data_iv["iv_create_date"]) ;
    	unset($set_data_iv["iv_update_date"]) ;

    	// 請求書データ : 既存データ書き換えUPDATE
    	$this->iv->update_invoice($set_data_iv);

    	// 履歴ファイルを作成
    	$this->iv->insert_invoice_history($set_data_iv);

    	// 明細データ作成
    	foreach($get_ivd_data as $key => $val)
    	{

    		// データをセット
    		$set_data_ivd = $val;

    		$set_data_ivd['ivd_seq_suffix'] = $val['ivd_seq_suffix'] + 1;
    		$set_data_ivd['ivd_status'] = 0;

    		// 請求書データ : 既存データ書き換えUPDATE
    		$this->ivd->update_invoice_detail($set_data_ivd);

    		// 履歴ファイルを作成
    		$this->ivd->insert_invoice_detail_history($set_data_ivd);

    	}

    	// 請求書番号を返す
    	return $set_data_iv['iv_slip_no'];
    }

    // 初期値セット
    private function _item_set()
    {

    	// ステータス 選択項目セット
    	$this->config->load('config_status');
    	$opt_iv_status = $this->config->item('PROJECT_IV_STATUS');

    	// 課金方式
    	$this->config->load('config_comm');
    	$opt_iv_accounting = $this->config->item('INVOICE_ACCOUNTING');

    	// 口座種別のセット
    	$opt_iv_kind = $this->config->item('CUSTOMER_CM_KIND');

    	$this->smarty->assign('options_iv_status',     $opt_iv_status);
    	$this->smarty->assign('options_iv_accounting', $opt_iv_accounting);
    	$this->smarty->assign('options_iv_kind',       $opt_iv_kind);

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}

