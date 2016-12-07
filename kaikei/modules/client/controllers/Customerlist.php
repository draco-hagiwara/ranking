<?php

class Customerlist extends MY_Controller
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

//         $this->smarty->assign('err_clid',   FALSE);
//          $this->smarty->assign('err_status', FALSE);
//          $this->smarty->assign('err_mail',   FALSE);
//         $this->smarty->assign('err_passwd', FALSE);
        $this->smarty->assign('mess', FALSE);

    }

    // 顧客情報検索一覧TOP
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
								'cm_status'  => '',
								'cm_company' => '',
								'orderid'    => '',
			);

			// セッションをフラッシュデータとして保存
			$data = array(
							'c_cm_company' => "",
							'c_cm_status'  => "",
							'c_orderid'    => "",
			);
			$this->session->set_userdata($data);
        }

        // 顧客情報の取得
        $this->load->model('Customer', 'cm', TRUE);
        list($customer_list, $customer_countall) = $this->cm->get_customerlist($tmp_inputpost, $tmp_per_page, $tmp_offset);

        $this->smarty->assign('list', $customer_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($customer_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall', $customer_countall);

        $this->smarty->assign('seach_company', $tmp_inputpost['cm_company']);
        $this->smarty->assign('seach_status',  $tmp_inputpost['cm_status']);
        $this->smarty->assign('seach_orderid', $tmp_inputpost['orderid']);

        $this->view('customerlist/index.tpl');

    }

    // 一覧表示
    public function search()
    {

        // 検索項目の保存が上手くいかない。応急的に対応！
        if ($this->input->post('submit') == '_submit')
        {
            // セッションをフラッシュデータとして保存
            $data = array(
                    		'c_cm_company' => $this->input->post('cm_company'),
                    		'c_cm_status'  => $this->input->post('cm_status'),
                    		'c_orderid'    => $this->input->post('orderid'),
            );
            $this->session->set_userdata($data);

            $tmp_inputpost = $this->input->post();
            unset($tmp_inputpost["submit"]);

        } else {
            // セッションからフラッシュデータ読み込み
            $tmp_inputpost['cm_company'] = $_SESSION['c_cm_company'];
            $tmp_inputpost['cm_status']  = $_SESSION['c_cm_status'];
            $tmp_inputpost['orderid']    = $_SESSION['c_orderid'];
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

        // アカウントメンバーの取得
        $this->load->model('Customer', 'cm', TRUE);
        list($customer_list, $customer_countall) = $this->cm->get_customerlist($tmp_inputpost, $tmp_per_page, $tmp_offset);

        $this->smarty->assign('list', $customer_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($customer_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall', $customer_countall);

        $this->smarty->assign('seach_company', $tmp_inputpost['cm_company']);
        $this->smarty->assign('seach_status',  $tmp_inputpost['cm_status']);
        $this->smarty->assign('seach_orderid', $tmp_inputpost['orderid']);

        $this->view('customerlist/index.tpl');

    }

    // 顧客情報編集
    public function detail()
    {

    	// 更新対象アカウントのデータ取得
    	$input_post = $this->input->post();

    	$this->load->model('Customer', 'cm', TRUE);
    	$cm_data = $this->cm->get_cm_seq($input_post['chg_seq']);

    	$this->smarty->assign('info', $cm_data[0]);

    	// バリデーション設定
    	$this->_set_validation02();

    	// 初期値セット
    	$this->_item_set();

    	// 担当営業セット
    	$this->_sales_item_set();

        $this->view('customerlist/detail.tpl');

    }

    // 顧客情報チェック
    public function detailchk()
    {

    	$input_post = $this->input->post();

    	// バリデーション・チェック
    	$this->_set_validation02();
    	if ($this->form_validation->run() == TRUE)
    	{

    		$this->load->model('Customer', 'cm', TRUE);

		    // 不要パラメータ削除
		    unset($input_post["submit"]) ;

		    // トランザクション・START
		    $this->db->trans_strict(FALSE);                                 		// StrictモードをOFF
		    $this->db->trans_start();                                       		// trans_begin

		    // DB書き込み
		    $this->cm->update_customer($input_post);
		    $this->smarty->assign('mess',  "更新が完了しました。");

		    // ステータス=「一時停止」「解約」：受注案件情報の更新
		    if ($input_post['cm_status'] != 0)
		    {

			    $this->load->model('Project', 'pj', TRUE);

			    // 受注案件情報データの有無チェック
			    $get_pj_list = $this->pj->get_pj_cm_status($input_post['cm_seq'], $_SESSION['c_memGrp'], 'seorank');

			    if (count($get_pj_list))
			    {

			    	// 更新
			    	foreach($get_pj_list as $key => $value)
			    	{

			    		if ($input_post['cm_status'] == 1)
			    		{
			    			$set_pj_data["pj_status"] = 1;
			    			$set_pj_data["pj_invoice_status"] = 1;
			    		} else {
			    			$set_pj_data["pj_status"] = 2;
			    		}
			    		$set_pj_data["pj_seq"]    = $value['pj_seq'];

			    		$this->pj->update_project($set_pj_data, $_SESSION['c_memGrp'], 'seorank');

			    	}

			    }
		    }

		    // トランザクション・COMMIT
		    $this->db->trans_complete();                                    		// trans_rollback & trans_commit
		    if ($this->db->trans_status() === FALSE)
		    {
		    	log_message('error', 'CLIENT::[Customerlist -> detailchk()]：顧客ステータス「解約」処理 トランザクションエラー');
		    }

    	}

    	// 初期値セット
    	$this->_item_set();

    	// 担当営業セット
    	$this->_sales_item_set();

    	// 請求書の別住所有無フラグの判定
    	if (isset($input_post['chkinvoice']))
    	{
    		$input_post['cm_flg_iv'] = 1;
    	} else {
    		$input_post['cm_flg_iv'] = 0;
    	}

    	$this->smarty->assign('info', $input_post);
    	$this->view('customerlist/detail.tpl');

    }

    // 顧客情報 新規登録
    public function add()
    {

    	// バリデーション・チェック
    	$this->_set_validation03();

    	// 初期値セット
    	$this->_item_set();

    	// 担当営業セット
    	$this->_sales_item_set();

    	$this->smarty->assign('tmp_pref',    NULL);
    	$this->smarty->assign('tmp_pref_iv', NULL);
    	$this->smarty->assign('tmp_memo',    NULL);
    	$this->smarty->assign('tmp_memo_iv', NULL);

    	$this->view('customerlist/add.tpl');

    }

    // 顧客情報 内容チェック
    public function addchk()
    {

    	$input_post = $this->input->post();

    	// 初期値セット
    	$this->_item_set();

    	// 担当営業セット
    	$this->_sales_item_set();

    	// バリデーション・チェック
    	$this->_set_validation03();
    	if ($this->form_validation->run() == FALSE)
    	{
    		$this->smarty->assign('tmp_pref',    $input_post['cm_pref']);
    		$this->smarty->assign('tmp_pref_iv', $input_post['cm_pref_iv']);
    		$this->smarty->assign('tmp_memo',    $input_post['cm_memo']);
    		$this->smarty->assign('tmp_memo_iv', $input_post['cm_memo_iv']);

    		if ($input_post['cm_pref'] != "")
    		{
    			$this->smarty->assign('tmp_pref', $input_post['cm_pref']);				// 都道府県を保持
    		}
    	    if ($input_post['cm_pref_iv'] != "")
    		{
    			$this->smarty->assign('tmp_pref_iv', $input_post['cm_pref_iv']);		// 都道府県を保持
    		}
    		if ($input_post['cm_memo'] != "")
    		{
    			$this->smarty->assign('tmp_memo', $input_post['cm_memo']);				// 備考を保持
    		}
    		if ($input_post['cm_memo_iv'] != "")
    		{
    			$this->smarty->assign('tmp_memo_iv', $input_post['cm_memo_iv']);		// 備考を保持
    		}

    	} else {

    		$this->load->model('Customer', 'cm', TRUE);

	    	// 不要パラメータ削除
	    	unset($input_post["_submit"]) ;

	    	// DB書き込み
	    	$_row_id = $this->cm->insert_customer($input_post);

    		$this->smarty->assign('mess',  "登録が完了しました。");

    		redirect('/customerlist/');

    	}

    	$this->smarty->assign('tmp_pref',    $input_post['cm_pref']);
    	$this->smarty->assign('tmp_pref_iv', $input_post['cm_pref_iv']);
    	$this->smarty->assign('tmp_memo',    $input_post['cm_memo']);
    	$this->smarty->assign('tmp_memo_iv', $input_post['cm_memo_iv']);

    	$this->view('customerlist/add.tpl');

    }

    // 顧客情報コピー
    public function cp()
    {

    	// 更新対象アカウントのデータ取得
    	$input_post = $this->input->post();

    	$this->load->model('Customer', 'cm', TRUE);
    	$cm_data = $this->cm->get_cm_seq($input_post['chg_seq']);

    	$this->smarty->assign('info', $cm_data[0]);

    	// バリデーション設定
    	$this->_set_validation02();

    	// 初期値セット
    	$this->_item_set();

    	$this->view('customerlist/copy.tpl');

    }

    // 顧客情報 複写内容チェック
    public function cpchk()
    {

    	$input_post = $this->input->post();

    	// 初期値セット
    	$this->_item_set();

    	// バリデーション・チェック
    	$this->_set_validation03();
    	if ($this->form_validation->run() == FALSE)
    	{
    		$this->smarty->assign('tmp_pref',    $input_post['cm_pref']);
    		$this->smarty->assign('tmp_pref_iv', $input_post['cm_pref_iv']);
    		$this->smarty->assign('tmp_memo',    $input_post['cm_memo']);
    		$this->smarty->assign('tmp_memo_iv', $input_post['cm_memo_iv']);

    		if ($input_post['cm_pref'] != "")
    		{
    			$this->smarty->assign('tmp_pref', $input_post['cm_pref']);				// 都道府県を保持
    		}
    		if ($input_post['cm_pref_iv'] != "")
    		{
    			$this->smarty->assign('tmp_pref_iv', $input_post['cm_pref_iv']);		// 都道府県を保持
    		}
    		if ($input_post['cm_memo'] != "")
    		{
    			$this->smarty->assign('tmp_memo', $input_post['cm_memo']);				// 備考を保持
    		}
    		if ($input_post['cm_memo_iv'] != "")
    		{
    			$this->smarty->assign('tmp_memo_iv', $input_post['cm_memo_iv']);		// 備考を保持
    		}

    	} else {

    		$this->load->model('Customer', 'cm', TRUE);

    		// 不要パラメータ削除
    		unset($input_post["submit"]) ;

    		// DB書き込み
    		$_row_id = $this->cm->insert_customer($input_post);

    		$this->smarty->assign('mess',  "登録が完了しました。");
    	}

    	$this->smarty->assign('tmp_pref',    $input_post['cm_pref']);
    	$this->smarty->assign('tmp_pref_iv', $input_post['cm_pref_iv']);
    	$this->smarty->assign('tmp_memo',    $input_post['cm_memo']);
    	$this->smarty->assign('tmp_memo_iv', $input_post['cm_memo_iv']);

    	//$this->view('customerlist/index.tpl');
    	$this->view('customerlist/add.tpl');

    }

    // Pagination 設定
    private function _get_Pagination($countall, $tmp_per_page)
    {

    	$config['base_url']       = base_url() . '/customerlist/search/';		// ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
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
    	$opt_cm_status = $this->config->item('CUSTOMER_CM_STATUS');

    	// 口座種別のセット
    	$this->config->load('config_comm');
    	$opt_cm_kind = $this->config->item('CUSTOMER_CM_KIND');

    	// 回収サイトのセット
    	$opt_cm_collect = $this->config->item('CUSTOMER_CM_COLLECT');

    	// 顧客情報ID 並び替え選択項目セット
    	$arropt_id = array (
    			''     => '-- 選択してください --',
    			'DESC' => '降順',
    			'ASC'  => '昇順',
    	);

    	$this->smarty->assign('options_cm_status',  $opt_cm_status);
    	$this->smarty->assign('options_cm_kind',    $opt_cm_kind);
    	$this->smarty->assign('options_cm_collect', $opt_cm_collect);
    	$this->smarty->assign('options_orderid',    $arropt_id);

    }

    // 検索項目 初期値セット
    private function _search_set()
    {

        // ステータス 選択項目セット
    	$this->config->load('config_status');
    	$opt_cm_status = $this->config->item('CUSTOMER_CM_STATUS');

    	// 顧客情報ID 並び替え選択項目セット
        $arropt_id = array (
                ''     => '-- 選択してください --',
                'DESC' => '降順',
                'ASC'  => '昇順',
        );

    	$this->smarty->assign('options_cm_status', $opt_cm_status);
        $this->smarty->assign('options_orderid',   $arropt_id);

    }

    // 担当営業セット
    private function _sales_item_set()
    {

    	// 請求書発行対象企業
    	$this->config->load('config_comm');
    	$opt_cl_seq = $this->config->item('PROJECT_CL_SEQ');

    	$this->load->model('Account', 'ac', TRUE);
    	$salesman_list = $this->ac->get_salesman($opt_cl_seq, 'seorank');		// 「ラベンダー」固定 : ac_cl_seq = 2

    	foreach ($salesman_list as $key => $val)
    	{
    		$opt_cm_salesman[$val['ac_seq']] = $val['ac_name01'] . ' ' . $val['ac_name02'];
    	}

    	$this->smarty->assign('options_cm_salesman', $opt_cm_salesman);

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    			array(
    					'field'   => 'cm_company',
    					'label'   => '会社名',
    					'rules'   => 'trim|max_length[50]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック : クライアント更新
    private function _set_validation02()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'cm_status',
    					'label'   => 'ステータス選択',
    					'rules'   => 'trim|required|max_length[2]'
    			),
    			array(
    					'field'   => 'cm_yayoi_cd',
    					'label'   => '顧客コード',
    					'rules'   => 'trim|required|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_yayoi_name',
    					'label'   => '弥生名称',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_company',
    					'label'   => '会社名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_company_kana',
    					'label'   => '会社名カナ',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_zip01',
    					'label'   => '郵便番号（3ケタ）',
    					'rules'   => 'trim|required|exact_length[3]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_zip02',
    					'label'   => '郵便番号（4ケタ）',
    					'rules'   => 'trim|required|exact_length[4]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_pref',
    					'label'   => '都道府県',
    					'rules'   => 'trim|required|max_length[4]'
    			),
    			array(
    					'field'   => 'cm_addr01',
    					'label'   => '市区町村',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_addr02',
    					'label'   => '町名・番地',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_buil',
    					'label'   => 'ビル・マンション名など',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_president01',
    					'label'   => '代表者姓',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_president02',
    					'label'   => '代表者名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_tel01',
    					'label'   => '代表電話番号',
    					'rules'   => 'trim|required|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cm_seturitu',
    					'label'   => '設立年月日',
    					'rules'   => 'trim|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_capital',
    					'label'   => '資本金',
    					'rules'   => 'trim|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_seturitu',
    					'label'   => '設立年月日',
    					'rules'   => 'trim|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_closingdate',
    					'label'   => '決算日',
    					'rules'   => 'trim|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_employee',
    					'label'   => '従業員数',
    					'rules'   => 'trim|max_length[10]'
    			),
    			array(
    					'field'   => 'cm_collect',
    					'label'   => '回収サイト',
    					'rules'   => 'trim|max_length[1]'
    			),
    			array(
    					'field'   => 'cm_credit_chk',
    					'label'   => '与信チェック日',
    					'rules'   => 'trim|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_antisocial_chk',
    					'label'   => '反社チェック日',
    					'rules'   => 'trim|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_credit_max',
    					'label'   => '与信限度額',
    					'rules'   => 'trim|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_trade_no',
    					'label'   => '取引申請番号',
    					'rules'   => 'trim|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_mail',
    					'label'   => 'メールアドレス',
    					'rules'   => 'trim|required|max_length[100]|valid_email'
    			),
    			array(
    					'field'   => 'cm_mailsub',
    					'label'   => 'メールアドレス(サブ)',
    					'rules'   => 'trim|max_length[100]|valid_email'
    			),
    			array(
    					'field'   => 'cm_department',
    					'label'   => '担当所属部署／役職',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_person01',
    					'label'   => '担当者姓',
    					'rules'   => 'trim|required|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_person02',
    					'label'   => '担当者名',
    					'rules'   => 'trim|required|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_tel02',
    					'label'   => '担当者電話番号',
    					'rules'   => 'trim|required|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cm_mobile',
    					'label'   => '担当者携帯番号',
    					'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cm_fax',
    					'label'   => 'FAX番号',
    					'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cm_bank_cd',
    					'label'   => '銀行CD',
    					'rules'   => 'trim|required|max_length[4]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_bank_nm',
    					'label'   => '銀行名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_branch_cd',
    					'label'   => '支店CD',
    					'rules'   => 'trim|required|max_length[3]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_branch_nm',
    					'label'   => '支店名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_kind',
    					'label'   => '口座種別(普通/当座)',
    					'rules'   => 'trim|required|max_length[1]'
    			),
    			array(
    					'field'   => 'cm_account_no',
    					'label'   => '口座番号',
    					'rules'   => 'trim|max_length[10]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_account_nm',
    					'label'   => '口座名義',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_memo',
    					'label'   => '備考',
    					'rules'   => 'trim|max_length[1000]'
    			),
    			array(
    					'field'   => 'cm_memo_iv',
    					'label'   => '請求書：備考',
    					'rules'   => 'trim|max_length[100]'
    			),

    			array(
    					'field'   => 'cm_company_iv',
    					'label'   => '会社名',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_zip01_iv',
    					'label'   => '郵便番号（3ケタ）',
    					'rules'   => 'trim|exact_length[3]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_zip02_iv',
    					'label'   => '郵便番号（4ケタ）',
    					'rules'   => 'trim|exact_length[4]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_pref_iv',
    					'label'   => '都道府県',
    					'rules'   => 'trim|max_length[4]'
    			),
    			array(
    					'field'   => 'cm_addr01_iv',
    					'label'   => '市区町村',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_addr02_iv',
    					'label'   => '町名・番地',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_buil_iv',
    					'label'   => 'ビル・マンション名など',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_department_iv',
    					'label'   => '所属部署',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_person01_iv',
    					'label'   => '担当者姓',
    					'rules'   => 'trim|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_person02_iv',
    					'label'   => '担当者名',
    					'rules'   => 'trim|max_length[20]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

    // フォーム・バリデーションチェック : クライアント追加
    private function _set_validation03()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'cm_status',
    					'label'   => 'ステータス選択',
    					'rules'   => 'trim|required|max_length[2]'
    			),
    			array(
    					'field'   => 'cm_company',
    					'label'   => '会社名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_zip01',
    					'label'   => '郵便番号（3ケタ）',
    					'rules'   => 'trim|required|exact_length[3]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_zip02',
    					'label'   => '郵便番号（4ケタ）',
    					'rules'   => 'trim|required|exact_length[4]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_pref',
    					'label'   => '都道府県',
    					'rules'   => 'trim|required|max_length[4]'
    			),
    			array(
    					'field'   => 'cm_addr01',
    					'label'   => '市区町村',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_addr02',
    					'label'   => '町名・番地',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_buil',
    					'label'   => 'ビル・マンション名など',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_president01',
    					'label'   => '代表者姓',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_president02',
    					'label'   => '代表者名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_tel01',
    					'label'   => '代表電話番号',
    					'rules'   => 'trim|required|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cm_mail',
    					'label'   => 'メールアドレス',
    					'rules'   => 'trim|required|max_length[100]|valid_email'
    			),
    			array(
    					'field'   => 'cm_mailsub',
    					'label'   => 'メールアドレス(サブ)',
    					'rules'   => 'trim|max_length[100]|valid_email'
    			),
    			array(
    					'field'   => 'cm_department',
    					'label'   => '所属部署',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_person01',
    					'label'   => '担当者姓',
    					'rules'   => 'trim|required|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_person02',
    					'label'   => '担当者名',
    					'rules'   => 'trim|required|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_tel02',
    					'label'   => '担当者電話番号',
    					'rules'   => 'trim|required|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cm_mobile',
    					'label'   => '担当者携帯番号',
    					'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cm_fax',
    					'label'   => 'FAX番号',
    					'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cm_bank_cd',
    					'label'   => '銀行CD',
    					'rules'   => 'trim|required|max_length[4]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_bank_nm',
    					'label'   => '銀行名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_branch_cd',
    					'label'   => '支店CD',
    					'rules'   => 'trim|required|max_length[3]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_branch_nm',
    					'label'   => '支店名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_kind',
    					'label'   => '口座種別(普通/当座)',
    					'rules'   => 'trim|required|max_length[1]'
    			),
    			array(
    					'field'   => 'cm_account_no',
    					'label'   => '口座番号',
    					'rules'   => 'trim|required|max_length[10]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_account_nm',
    					'label'   => '口座名義',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_memo',
    					'label'   => '備考',
    					'rules'   => 'trim|max_length[1000]'
    			),
    			array(
    					'field'   => 'cm_memo_iv',
    					'label'   => '請求書：備考',
    					'rules'   => 'trim|max_length[100]'
    			),

    			array(
    					'field'   => 'cm_company_iv',
    					'label'   => '会社名',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_zip01_iv',
    					'label'   => '郵便番号（3ケタ）',
    					'rules'   => 'trim|exact_length[3]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_zip02_iv',
    					'label'   => '郵便番号（4ケタ）',
    					'rules'   => 'trim|exact_length[4]|is_numeric'
    			),
    			array(
    					'field'   => 'cm_pref_iv',
    					'label'   => '都道府県',
    					'rules'   => 'trim|max_length[4]'
    			),
    			array(
    					'field'   => 'cm_addr01_iv',
    					'label'   => '市区町村',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_addr02_iv',
    					'label'   => '町名・番地',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_buil_iv',
    					'label'   => 'ビル・マンション名など',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'cm_department_iv',
    					'label'   => '所属部署',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'cm_person01_iv',
    					'label'   => '担当者姓',
    					'rules'   => 'trim|max_length[20]'
    			),
    			array(
    					'field'   => 'cm_person02_iv',
    					'label'   => '担当者名',
    					'rules'   => 'trim|max_length[20]'
    			),

    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}

