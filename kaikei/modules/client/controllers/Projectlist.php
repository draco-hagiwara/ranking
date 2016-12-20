<?php

class Projectlist extends MY_Controller
{

	/*
	 *  受注案件情報
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

        $this->smarty->assign('err_date', FALSE);
        $this->smarty->assign('mess',     FALSE);

    }

    // アカウント検索一覧TOP
    public function index()
    {

    	// セッションデータをクリア
    	$this->load->model('comm_auth', 'comm_auth', TRUE);
    	$this->comm_auth->delete_session('client');

        // バリデーション・チェック
        $this->_set_validation();												// バリデーション設定
        $this->form_validation->run();

        // 1ページ当たりの表示件数
        $this->config->load('config_comm');
        $tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

        // Pagination 現在ページ数の取得：：URIセグメントの取得
        $segments = $this->uri->segment_array();
        if (isset($segments[3]))
        {
            $tmp_offset = $segments[3];
			$tmp_inputpost = $this->input->post();
        } else {
            $tmp_offset = 0;
			$tmp_inputpost = array(
								'pj_seq'            => '',
								'pj_cm_seq'         => '',
								'pj_cm_company'     => '',
								'pj_status'         => '',
								'pj_invoice_status' => '',
								'pj_accounting'     => '',
								'pj_salesman'       => '',
								'orderid'           => '',
			);

			// セッションをフラッシュデータとして保存
			$data = array(
								'c_pj_seq'            => '',
								'c_pj_cm_seq'         => '',
								'c_pj_cm_company'     => '',
								'c_pj_status'         => '',
								'c_pj_invoice_status' => '',
								'c_pj_accounting'     => '',
								'c_pj_salesman'       => '',
								'c_orderid'           => '',
					);
			$this->session->set_userdata($data);
        }

        // 顧客情報の取得
        $this->load->model('Project', 'pj', TRUE);
        list($project_list, $project_countall) = $this->pj->get_projectlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp'], 'seorank');

        $this->smarty->assign('list', $project_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($project_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall',       $project_countall);

        $this->smarty->assign('seach_seq',            $tmp_inputpost['pj_seq']);
        $this->smarty->assign('seach_cm_seq',         $tmp_inputpost['pj_cm_seq']);
        $this->smarty->assign('seach_cm_company',     $tmp_inputpost['pj_cm_company']);
        $this->smarty->assign('seach_status',         $tmp_inputpost['pj_status']);
        $this->smarty->assign('seach_invoice_status', $tmp_inputpost['pj_invoice_status']);
        $this->smarty->assign('seach_accounting',     $tmp_inputpost['pj_accounting']);
        $this->smarty->assign('seach_salesman',       $tmp_inputpost['pj_salesman']);
        $this->smarty->assign('seach_orderid',        $tmp_inputpost['orderid']);

        $this->view('projectlist/index.tpl');

    }

    // 一覧表示
    public function search()
    {

    	// 検索項目の保存が上手くいかない。応急的に対応！
    	if ($this->input->post('submit') == '_submit')
    	{
    		// セッションをフラッシュデータとして保存
    		$data = array(
    				'c_pj_seq'            => $this->input->post('pj_seq'),
    				'c_pj_cm_seq'         => $this->input->post('pj_cm_seq'),
    				'c_pj_cm_company'     => $this->input->post('pj_cm_company'),
    				'c_pj_status'         => $this->input->post('pj_status'),
    				'c_pj_invoice_status' => $this->input->post('pj_invoice_status'),
    				'c_pj_accounting'     => $this->input->post('pj_accounting'),
    				'c_pj_salesman'       => $this->input->post('pj_salesman'),
    				'c_orderid'           => $this->input->post('orderid'),
    		);
    		$this->session->set_userdata($data);

    		$tmp_inputpost = $this->input->post();
    		unset($tmp_inputpost["submit"]);

    	} else {
    		// セッションからフラッシュデータ読み込み
    		$tmp_inputpost['pj_seq']            = $_SESSION['c_pj_seq'];
    		$tmp_inputpost['pj_cm_seq']         = $_SESSION['c_pj_cm_seq'];
    		$tmp_inputpost['pj_cm_company']     = $_SESSION['c_pj_cm_company'];
    		$tmp_inputpost['pj_status']         = $_SESSION['c_pj_status'];
    		$tmp_inputpost['pj_invoice_status'] = $_SESSION['c_pj_invoice_status'];
    		$tmp_inputpost['pj_accounting']     = $_SESSION['c_pj_accounting'];
    		$tmp_inputpost['pj_salesman']       = $_SESSION['c_pj_salesman'];
    		$tmp_inputpost['orderid']           = $_SESSION['c_orderid'];
    	}

    	// バリデーション・チェック
    	$this->_set_validation();												// バリデーション設定
    	$this->form_validation->run();

    	// Pagination 現在ページ数の取得：：URIセグメントの取得
    	$segments = $this->uri->segment_array();
    	if (isset($segments[3]))
    	{
    		$tmp_offset = $segments[3];
    	} else {
    		$tmp_offset = 0;
    	}

    	// 1ページ当たりの表示件数
    	$this->config->load('config_comm');
    	$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

    	// 顧客情報の取得
    	$this->load->model('Project', 'pj', TRUE);
    	list($project_list, $project_countall) = $this->pj->get_projectlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp'], 'seorank');

    	$this->smarty->assign('list', $project_list);

    	// Pagination 設定
    	$set_pagination = $this->_get_Pagination($project_countall, $tmp_per_page);

    	// 初期値セット
    	$this->_search_set();

    	$this->smarty->assign('set_pagination', $set_pagination['page_link']);
    	$this->smarty->assign('countall',       $project_countall);

    	$this->smarty->assign('seach_seq',            $tmp_inputpost['pj_seq']);
    	$this->smarty->assign('seach_cm_seq',         $tmp_inputpost['pj_cm_seq']);
    	$this->smarty->assign('seach_cm_company',     $tmp_inputpost['pj_cm_company']);
    	$this->smarty->assign('seach_status',         $tmp_inputpost['pj_status']);
    	$this->smarty->assign('seach_invoice_status', $tmp_inputpost['pj_invoice_status']);
    	$this->smarty->assign('seach_accounting',     $tmp_inputpost['pj_accounting']);
    	$this->smarty->assign('seach_salesman',       $tmp_inputpost['pj_salesman']);
    	$this->smarty->assign('seach_orderid',        $tmp_inputpost['orderid']);

    	$this->view('projectlist/index.tpl');

    }

    // アカウント情報編集
    public function detail()
    {

    	// 初期値セット
    	$this->_item_set();

    	// 担当営業セット
    	$this->_sales_item_set();

    	// バリデーション設定
    	$this->_set_validation02();

    	// 更新対象アカウントのデータ取得
    	$input_post = $this->input->post();
    	$this->load->model('Project', 'pj', TRUE);

		$tmp_pjid = $input_post['chg_seq'];

    	$pj_data = $this->pj->get_pj_seq($tmp_pjid, $_SESSION['c_memGrp'], 'seorank');

    	$this->smarty->assign('info', $pj_data[0]);

        $this->view('projectlist/detail.tpl');

    }

    // アカウント情報チェック
    public function detailchk()
    {

    	$input_post = $this->input->post();

    	// バリデーション・チェック
		$this->_set_validation02();									// 管理者
    	if ($this->form_validation->run() == FALSE)
    	{
    	} else {

    	    // 契約期間の判定
    		$date_str = new DateTime($input_post['pj_start_date']);
    		$date_end = new DateTime($input_post['pj_end_date']);
    		if ($date_str > $date_end)
    		{
    			$this->smarty->assign('err_date', TRUE);
    		} else {

    			// 不要パラメータ削除
    			unset($input_post["submit"]) ;

    			// DB書き込み
    			$this->load->model('Project', 'pj', TRUE);
    			$this->pj->update_project($input_post, $_SESSION['c_memGrp'], 'seorank');

    			$this->smarty->assign('mess', "更新が完了しました。");

    			redirect('/projectlist/');
    		}
    	}

    	// 初期値セット
    	$this->_item_set();

    	// 担当営業セット
    	$this->_sales_item_set();

     	$this->smarty->assign('info', $input_post);
     	$this->view('projectlist/detail.tpl');

    }

    // アカウント情報追加
    public function add()
    {

    	$input_post = $this->input->post();

    	// バリデーション設定
    	$this->_set_validation02();

    	// 初期値セット
    	$this->_item_set();

    	// 担当営業セット
    	$this->_sales_item_set();

    	// 会社名セット
    	$this->load->model('Customer', 'cm', TRUE);
    	$cm_data = $this->cm->get_cm_seq($input_post['chg_seq']);

    	$this->smarty->assign('pj_cm_seq',     $input_post['chg_seq']);
    	$this->smarty->assign('pj_cm_company', $cm_data[0]['cm_company']);
    	$this->smarty->assign('pj_salesman',   $cm_data[0]['cm_salesman']);
    	$this->smarty->assign('tmp_memo',      NULL);

    	$this->view('projectlist/add.tpl');

    }

    // アカウント情報確認＆登録
    public function addchk()
    {

    	$input_post = $this->input->post();

    	// バリデーション・チェック
    	$this->_set_validation02();
    	if ($this->form_validation->run() == TRUE)
    	{

    		// 契約期間の判定
    		$date_str = new DateTime($input_post['pj_start_date']);
    		$date_end = new DateTime($input_post['pj_end_date']);
    		if ($date_str > $date_end)
    		{
    			$this->smarty->assign('err_date', TRUE);
    		} else {

    			// 不要パラメータ削除
    			unset($input_post["_submit"]) ;

    			// DB書き込み
    			$this->load->model('Project', 'pj', TRUE);
    			$this->pj->insert_project($input_post, $_SESSION['c_memGrp'], 'seorank');

    			$this->smarty->assign('mess', "登録が完了しました。");

    		}
    	}

    	// 初期値セット
    	$this->_item_set();

    	// 担当営業セット
    	$this->_sales_item_set();

    	$this->smarty->assign('pj_cm_seq',     $input_post['pj_cm_seq']);
    	$this->smarty->assign('pj_cm_company', $input_post['pj_cm_company']);
    	$this->smarty->assign('pj_salesman',   $input_post['pj_salesman']);
    	$this->smarty->assign('tmp_memo',      $input_post['pj_memo']);

    	$this->view('projectlist/add.tpl');

    }

    // Pagination 設定
    private function _get_Pagination($countall, $tmp_per_page)
    {

    	$config['base_url']       = base_url() . '/projectlist/search/';		// ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
    	$config['per_page']       = $tmp_per_page;								// 1ページ当たりの表示件数。
    	$config['total_rows']     = $countall;									// 総件数。where指定するか？
    	//$config['uri_segment']    = 4;										// オフセット値がURIパスの何セグメント目とするか設定
    	$config['num_links']      = 5;											//現在のページ番号の左右にいくつのページ番号リンクを生成するか設定
    	$config['full_tag_open']  = '<p class="pagination">';					// ページネーションリンク全体を階層化するHTMLタグの先頭タグ文字列を指定
    	$config['full_tag_close'] = '</p>';										// ページネーションリンク全体を階層化するHTMLタグの閉じタグ文字列を指定
    	$config['first_link']     = '最初へ';									// 最初のページを表すテキスト。
    	$config['last_link']      = '最後へ';									// 最後のページを表すテキスト。
    	$config['prev_link']      = '前へ';										// 前のページへのリンクを表わす文字列を指定
    	$config['next_link']      = '次へ';										// 次のページへのリンクを表わす文字列を指定

    	$this->load->library('pagination', $config);							// Paginationクラス読み込み
    	$set_page['page_link'] = $this->pagination->create_links();

    	return $set_page;

    }

    // 初期値セット
    private function _item_set()
    {

    	// ステータスのセット
    	$this->config->load('config_status');
    	$opt_pj_status = $this->config->item('PROJECT_PJ_STATUS');

    	// 案件請求書発行ステータスのセット
    	$this->config->load('config_status');
    	$opt_pj_invoice_status = $this->config->item('PROJECT_PJ_INVOICE_STATUS');

    	$this->smarty->assign('options_pj_status',  $opt_pj_status);
    	$this->smarty->assign('options_pj_iv_type', $opt_pj_invoice_status);

    }

    // 検索項目 初期値セット
    private function _search_set()
    {

    	// ステータス 選択項目セット
    	$this->config->load('config_status');
    	$opt_pj_status = $this->config->item('PROJECT_PJ_STATUS');

    	// 請求書発行ステータス 選択項目セット
    	$this->config->load('config_status');
    	$opt_pj_invoice_status = $this->config->item('PROJECT_PJ_INVOICE_STATUS');

    	// 課金方式 選択項目セット
    	$this->config->load('config_comm');
    	$opt_pj_accounting = $this->config->item('PROJECT_PJ_ACCOUNTING');

    	// 受注案件ID 並び替え選択項目セット
    	$arropt_id = array (
    			''     => '-- 選択してください --',
    			'DESC' => '降順',
    			'ASC'  => '昇順',
    	);

    	// 請求書発行対象企業
    	$opt_cl_seq = $this->config->item('PROJECT_CL_SEQ');

    	$this->load->model('Account', 'ac', TRUE);
    	$salesman_list = $this->ac->get_salesman($opt_cl_seq, 'seorank');		// 「ラベンダー」固定 : ac_cl_seq = 2

    	$opt_pj_salesman[''] = " -- 選択してください -- ";
    	foreach ($salesman_list as $key => $val)
    	{
    		$opt_pj_salesman[$val['ac_seq']] = $val['ac_name01'] . ' ' . $val['ac_name02'];
    	}


    	$this->smarty->assign('options_pj_status',         $opt_pj_status);
    	$this->smarty->assign('options_pj_invoice_status', $opt_pj_invoice_status);
    	$this->smarty->assign('options_pj_accounting',     $opt_pj_accounting);
    	$this->smarty->assign('options_orderid',           $arropt_id);

    	$this->smarty->assign('options_pj_salesman',       $opt_pj_salesman);

    }

    // 担当営業セット
    private function _sales_item_set()
    {

    	// 請求書発行対象企業
    	$this->config->load('config_comm');
    	$opt_cl_seq = $this->config->item('PROJECT_CL_SEQ');

    	$this->load->model('Account', 'ac', TRUE);
    	$salesman_list = $this->ac->get_salesman($opt_cl_seq, 'seorank');		// 「ラベンダー」固定 : ac_cl_seq = 2

//     	$cnt = 0;
    	foreach ($salesman_list as $key => $val)
    	{
    		$opt_pj_salesman[$val['ac_seq']] = $val['ac_name01'] . ' ' . $val['ac_name02'];
//     		$opt_pj_salesman[$cnt] = $val['ac_name01'] . ' ' . $val['ac_name02'];
//     		$cnt++;
    	}

    	$this->smarty->assign('options_pj_salesman', $opt_pj_salesman);

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック : フルチェック
    private function _set_validation02()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'pj_status',
    					'label'   => 'ステータス選択',
    					'rules'   => 'trim|required|max_length[1]|is_numeric'
    			),
    			array(
    					'field'   => 'pj_invoice_status',
    					'label'   => '請求書発行ステータス選択',
    					'rules'   => 'trim|required|max_length[1]|is_numeric'
    			),
    			array(
    					'field'   => 'pj_orders_ymd',
    					'label'   => '受注年月日',
    					'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    			array(
    					'field'   => 'pj_start_date',
    					'label'   => '契約開始日',
    					'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    			array(
    					'field'   => 'pj_end_date',
    					'label'   => '契約終了日',
    					'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    			array(
    					'field'   => 'pj_keyword',
    					'label'   => '検索キーワード',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'pj_url',
    					'label'   => '対象URL',
    					'rules'   => 'trim|required|regex_match[/^(https?)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/]|max_length[100]'
    			),
    			array(
    					'field'   => 'pj_accounting',
    					'label'   => '課金方式',
    					'rules'   => 'trim|required|max_length[1]|is_numeric'
    			),
    			array(
    					'field'   => 'pj_billing',
    					'label'   => '固定請求金額',
    					'rules'   => 'trim|required|max_length[10]|is_numeric'
    			),
    			array(
    					'field'   => 'pj_salesman',
    					'label'   => '担当営業',
    					'rules'   => 'trim|required|max_length[10]|is_numeric'
    			),
    			array(
    					'field'   => 'pj_memo',
    					'label'   => '備考',
    					'rules'   => 'trim|max_length[1000]'
    			),
    			array(
    					'field'   => 'pj_tag',
    					'label'   => 'タグ設定',
    					'rules'   => 'trim|max_length[100]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}

