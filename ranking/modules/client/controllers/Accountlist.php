<?php

class Accountlist extends MY_Controller
{

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

        $this->smarty->assign('err_email',  FALSE);
//         $this->smarty->assign('err_passwd', FALSE);
        $this->smarty->assign('mess',       FALSE);

    }

    // アカウント検索一覧TOP
    public function index()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');
//     	$this->load->model('comm_auth', 'comm_auth', TRUE);
//     	$this->comm_auth->delete_session('client');

        // バリデーション・チェック
        $this->_set_validation();												// バリデーション設定
//         $this->form_validation->run();

        // 1ページ当たりの表示件数
        $this->config->load('config_comm');
        $tmp_per_page = 20;
        //$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

        // Pagination 現在ページ数の取得：：URIセグメントの取得
        $segments = $this->uri->segment_array();
        if (isset($segments[3]))
        {
            $tmp_offset = $segments[3];
			$tmp_inputpost = $this->input->post();
        } else {
            $tmp_offset = 0;
			$tmp_inputpost = array(
								'ac_name'    => '',
								'orderid'    => '',
							);

// 			// セッションをフラッシュデータとして保存
// 			$data = array(
// 						'c_ac_name'    => '',
// 						'c_cl_company' => '',
// 			);
// 			$this->session->set_userdata($data);

        }

        // Type別に表示を制限する(管理者以外)
        //$tmp_inputpost['ac_seq']  = $_SESSION['c_memSeq'];
        //$tmp_inputpost['ac_type'] = $_SESSION['c_memType'];

        // アカウントメンバーの取得
        $this->load->model('Account', 'ac', TRUE);
        $tmp_inputpost['ac_cl_seq'] = $_SESSION['c_memGrp'];

        list($account_list, $account_countall) = $this->ac->get_accountlist($tmp_inputpost, $tmp_per_page, $tmp_offset);

        $this->smarty->assign('list', $account_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($account_countall, $tmp_per_page);

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall', $account_countall);

        $this->view('accountlist/index.tpl');

    }

//     // 一覧表示
//     public function search()
//     {

//         // 検索項目の保存が上手くいかない。応急的に対応！
//         if ($this->input->post('submit') == '_submit')
//         {
//             // セッションをフラッシュデータとして保存
//             $data = array(
//                     'c_ac_name'    => $this->input->post('ac_name'),
//                     'c_cl_company' => $this->input->post('cl_company'),
//             );
//             $this->session->set_userdata($data);

//             $tmp_inputpost = $this->input->post();
//             $tmp_inputpost['orderid'] = "";
//             unset($tmp_inputpost["submit"]);

//         } else {
//             // セッションからフラッシュデータ読み込み
//             $tmp_inputpost['ac_name']    = $_SESSION['c_ac_name'];
//             $tmp_inputpost['cl_company'] = $_SESSION['c_cl_company'];
//             $tmp_inputpost['orderid']    = "";
//         }

//         // バリデーション・チェック
//         $this->_set_validation();												// バリデーション設定
//         $this->form_validation->run();

//         // Pagination 現在ページ数の取得：：URIセグメントの取得
//         $segments = $this->uri->segment_array();
//         if (isset($segments[3]))
//         {
//             $tmp_offset = $segments[3];
//         } else {
//             $tmp_offset = 0;
//         }

//         // 1ページ当たりの表示件数
//         $this->config->load('config_comm');
//         $tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

//         // Type別に表示を制限する(管理者以外)
//         //$tmp_inputpost['ac_seq']  = $_SESSION['c_memSeq'];
//         //$tmp_inputpost['ac_type'] = $_SESSION['c_memType'];

//         // アカウントメンバーの取得
//         $this->load->model('Account', 'ac', TRUE);
//         list($account_list, $account_countall) = $this->ac->get_accountlist($tmp_inputpost, $tmp_per_page, $tmp_offset);

//         $this->smarty->assign('list', $account_list);

//         // Pagination 設定
//         $set_pagination = $this->_get_Pagination($account_countall, $tmp_per_page);

//         $this->smarty->assign('set_pagination', $set_pagination['page_link']);
//         $this->smarty->assign('countall', $account_countall);

//         $this->view('accountlist/index.tpl');

//     }

    // アカウント情報編集
    public function detail()
    {

    	// 初期値セット
    	$this->_item_set();

    	// バリデーション設定
    	$this->_set_validation();

    	// 更新対象アカウントのデータ取得
    	$input_post = $this->input->post();

    	if ($_SESSION['c_memType'] == 0)
    	{
    		$tmp_acid = $_SESSION['c_memSeq'];
    	} else {
    		$tmp_acid = $input_post['ac_uniq'];
    	}

    	$this->load->model('Account', 'ac', TRUE);
    	$ac_data = $this->ac->get_ac_seq($tmp_acid, TRUE);

    	$this->smarty->assign('info', $ac_data[0]);

        $this->view('accountlist/detail.tpl');

    }

    // アカウント情報チェック
    public function detailchk()
    {

    	// 初期値セット
    	$this->_item_set();

    	$input_post = $this->input->post();

    	// バリデーション・チェック
    	// 本人確認
    	if ($_SESSION['c_memType'] == 1)
    	{
    		$this->_set_validation02();									// 管理者
    	} else {
    		$this->_set_validation03();
    	}

    	if ($this->form_validation->run() == TRUE)
    	{

	    	$this->load->model('Account', 'ac', TRUE);

	    	// 不要パラメータ削除
	    	if ((isset($input_post['ac_pw'])) && ($input_post['ac_pw'] == ""))
	    	{
	    		$pw = FALSE;
	    		unset($input_post["ac_pw"]) ;
	    	} elseif (!isset($input_post['ac_pw'])) {
	    		$pw = FALSE;
	    	} else {
	    		$pw = TRUE;
	    	}
	    	unset($input_post["submit"]) ;

	    	if ($_SESSION['c_memSeq'] == $input_post['ac_seq'])
	    	{

		    	// DB書き込み
		    	$this->ac->update_account($input_post, $pw);

		    	$this->smarty->assign('mess',  "更新が完了しました。");

	    	} else {

	    		if ($input_post['ac_status'] == 9)
	    		{
	    			$input_post['ac_delflg'] = 1;
	    		}

	    		// DB書き込み (PW更新なし)
	    		$this->ac->update_account($input_post, $pw);

	    		$this->smarty->assign('mess',  "更新が完了しました。");
	    	}

    	}

     	$this->smarty->assign('info', $input_post);
     	$this->view('accountlist/detail.tpl');

    }

    // アカウント情報追加
    public function add()
    {

    	// バリデーション設定
    	$this->_set_validation();

    	// 初期値セット
    	$this->_item_set();

    	$this->view('accountlist/add.tpl');

    }

    // アカウント情報確認＆登録
    public function addchk()
    {

    	$input_post = $this->input->post();

    	// バリデーション・チェック
    	$this->_set_validation04();
    	if ($this->form_validation->run() == TRUE)
    	{

    		$this->load->model('Account', 'ac', TRUE);

    		// メールアドレス＆ログインIDの重複チェック
    		if ($this->ac->check_loginid($input_post['ac_id']))
    		{

    			$this->smarty->assign('err_clid',   TRUE);

    		} else {

   				$this->smarty->assign('err_clid',   FALSE);

    			// 不要パラメータ削除
    			unset($input_post["_submit"]) ;

    			// DB書き込み
    			$input_post["ac_cl_seq"] = $_SESSION['c_memGrp'];
    			$this->ac->insert_account($input_post);

    			$this->smarty->assign('mess',  "更新が完了しました。");
    			redirect('/accountlist/');

    		}
    	}

    	// 初期値セット
    	$this->_item_set();

    	$this->view('accountlist/add.tpl');

    }

    // Pagination 設定
    private function _get_Pagination($account_countall, $tmp_per_page)
    {

    	$config['base_url']       = base_url() . '/accountlist/search/';		// ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
    	$config['per_page']       = $tmp_per_page;								// 1ページ当たりの表示件数。
    	$config['total_rows']     = $account_countall;							// 総件数。where指定するか？
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
    	$opt_ac_status = $this->config->item('ACCOUNT_AC_STATUS');

    	// SEOユーザのセット
    	$this->config->load('config_comm');
    	$opt_ac_type = $this->config->item('ACCOUNT_AC_TYPE');

    	$this->smarty->assign('options_ac_status', $opt_ac_status);
    	$this->smarty->assign('options_ac_type',   $opt_ac_type);

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

     // フォーム・バリデーションチェック : 管理者による更新チェック
     private function _set_validation02()
     {
     	$rule_set = array(
		     	array(
		     			'field'   => 'ac_type',
		     			'label'   => '管理種類選択',
		     			'rules'   => 'trim|required|is_numeric|max_length[2]'
		     	),
		     	array(
		     			'field'   => 'ac_status',
		     			'label'   => 'ステータス選択',
		     			'rules'   => 'trim|required|is_numeric|max_length[2]'
		     	),
		     	array(
		     			'field'   => 'ac_department',
		     			'label'   => '所属部署',
		     			'rules'   => 'trim|max_length[50]'
		     	),
		     	array(
		     			'field'   => 'ac_name01',
		     			'label'   => '担当者姓',
		     			'rules'   => 'trim|required|max_length[50]'
		     	),
		     	array(
		     			'field'   => 'ac_name02',
		     			'label'   => '担当者名',
		     			'rules'   => 'trim|required|max_length[50]'
		     	),
		     	array(
		     			'field'   => 'ac_id',
		     			'label'   => 'ログインID',
		     			'rules'   => 'trim|required|max_length[50]|valid_email'
		     	),
		     	array(
		     			'field'   => 'ac_pw',
		     			'label'   => 'パスワード',
		     			'rules'   => 'trim|regex_match[/^[\x21-\x7e]+$/]|min_length[8]|max_length[50]'
		     	),
		     	array(
		     			'field'   => 'ac_keyword',
		     			'label'   => 'キーワード',
		     			'rules'   => 'trim|required|is_numeric|max_length[1]'
		     	),
		     	array(
		     			'field'   => 'ac_group',
		     			'label'   => 'グループ',
		     			'rules'   => 'trim|required|is_numeric|max_length[1]'
		     	),
		     	array(
		     			'field'   => 'ac_tag',
		     			'label'   => 'タグ',
		     			'rules'   => 'trim|required|is_numeric|max_length[1]'
		     	),
     	);

     	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
     }

    // フォーム・バリデーションチェック : 本人チェック
    private function _set_validation03()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'ac_department',
    					'label'   => '所属部署',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'ac_name01',
    					'label'   => '担当者姓',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'ac_name02',
    					'label'   => '担当者名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'ac_id',
    					'label'   => 'ログインID',
    					'rules'   => 'trim|required|max_length[100]|valid_email'
    			),
    			array(
    					'field'   => 'ac_pw',
    					'label'   => 'パスワード',
    					'rules'   => 'trim|regex_match[/^[\x21-\x7e]+$/]|min_length[8]|max_length[50]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

    // フォーム・バリデーションチェック : 管理者による新規登録チェック
    private function _set_validation04()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'ac_type',
    					'label'   => '管理種類選択',
    					'rules'   => 'trim|required|is_numeric|max_length[2]'
    			),
    			array(
    					'field'   => 'ac_status',
    					'label'   => 'ステータス選択',
    					'rules'   => 'trim|required|is_numeric|max_length[2]'
    			),
    			array(
    					'field'   => 'ac_department',
    					'label'   => '所属部署',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'ac_name01',
    					'label'   => '担当者姓',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'ac_name02',
    					'label'   => '担当者名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'ac_id',
    					'label'   => 'ログインID',
    					'rules'   => 'trim|required|max_length[50]|valid_email'
    			),
    			array(
    					'field'   => 'ac_pw',
    					'label'   => 'パスワード',
    					'rules'   => 'trim|required|regex_match[/^[\x21-\x7e]+$/]|min_length[8]|max_length[50]'
    			),
		    	array(
		    			'field'   => 'ac_keyword',
		    			'label'   => 'キーワード',
		    			'rules'   => 'trim|required|is_numeric|max_length[1]'
		    	),
		    	array(
		    			'field'   => 'ac_group',
		    			'label'   => 'グループ',
		    			'rules'   => 'trim|required|is_numeric|max_length[1]'
		    	),
		    	array(
		    			'field'   => 'ac_tag',
		    			'label'   => 'タグ',
		    			'rules'   => 'trim|required|is_numeric|max_length[1]'
		    	),
    	 );

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}

