<?php

class Clientlist extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($_SESSION['a_login'] == TRUE)
        {
            $this->smarty->assign('login_chk', TRUE);
            $this->smarty->assign('mem_type',  $_SESSION['a_memType']);
            $this->smarty->assign('mem_Seq',   $_SESSION['a_memSeq']);
        } else {
            $this->smarty->assign('login_chk', FALSE);
            $this->smarty->assign('mem_type',  "");
            $this->smarty->assign('mem_Seq',   "");

            redirect('/login/');
        }

        $this->smarty->assign('err_clid',   FALSE);
//          $this->smarty->assign('err_status', FALSE);
//          $this->smarty->assign('err_mail',   FALSE);
        $this->smarty->assign('err_passwd', FALSE);
        $this->smarty->assign('mess',       FALSE);

    }

    // アカウント検索一覧TOP
    public function index()
    {

    	// セッションデータをクリア
    	$this->load->model('comm_auth', 'comm_auth', TRUE);
    	$this->comm_auth->delete_session('admin');

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
								'cl_status'  => '',
								'cl_company' => '',
								'orderid'    => '',
			);

			// セッションをフラッシュデータとして保存
			$data = array(
							'a_cl_company' => "",
							'a_cl_status'  => "",
							'a_orderid'    => "",
			);
			$this->session->set_userdata($data);
        }

        // アカウントメンバーの取得
        $this->load->model('Client', 'cl', TRUE);
        list($client_list, $client_countall) = $this->cl->get_clientlist($tmp_inputpost, $tmp_per_page, $tmp_offset);

        $this->smarty->assign('list', $client_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($client_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall', $client_countall);

        $this->smarty->assign('seach_company', $tmp_inputpost['cl_company']);
        $this->smarty->assign('seach_status',  $tmp_inputpost['cl_status']);
        $this->smarty->assign('seach_orderid', $tmp_inputpost['orderid']);

        $this->view('clientlist/index.tpl');

    }

    // 一覧表示
    public function search()
    {

        // 検索項目の保存が上手くいかない。応急的に対応！
        if ($this->input->post('submit') == '_submit')
        {
            // セッションをフラッシュデータとして保存
            $data = array(
                    		'a_cl_company' => $this->input->post('cl_company'),
                    		'a_cl_status'  => $this->input->post('cl_status'),
                    		'a_orderid'    => $this->input->post('orderid'),
            );
            $this->session->set_userdata($data);

            $tmp_inputpost = $this->input->post();
            unset($tmp_inputpost["submit"]);

        } else {
            // セッションからフラッシュデータ読み込み
            $tmp_inputpost['cl_company'] = $_SESSION['a_cl_company'];
            $tmp_inputpost['cl_status']  = $_SESSION['a_cl_status'];
            $tmp_inputpost['orderid']    = $_SESSION['a_orderid'];
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
        $this->load->model('Client', 'cl', TRUE);
        list($client_list, $client_countall) = $this->cl->get_clientlist($tmp_inputpost, $tmp_per_page, $tmp_offset);

        $this->smarty->assign('list', $client_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($client_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall', $client_countall);

        $this->smarty->assign('seach_company', $tmp_inputpost['cl_company']);
        $this->smarty->assign('seach_status',  $tmp_inputpost['cl_status']);
        $this->smarty->assign('seach_orderid', $tmp_inputpost['orderid']);

        $this->view('clientlist/index.tpl');

    }

    // アカウント情報編集
    public function detail()
    {

    	// 更新対象アカウントのデータ取得
    	$input_post = $this->input->post();

    	$this->load->model('Client', 'cl', TRUE);
    	$cl_data = $this->cl->get_cl_seq($input_post['chg_clseq']);

    	$this->smarty->assign('info', $cl_data[0]);

    	// バリデーション設定
    	$this->_set_validation02();

    	// 初期値セット
    	$this->_item_set();

        $this->view('clientlist/detail.tpl');

    }

    // アカウント情報チェック
    public function detailchk()
    {

    	// 初期値セット
    	$this->_item_set();

    	$input_post = $this->input->post();

    	// バリデーション・チェック
    	$this->_set_validation02();
    	if ($this->form_validation->run() == TRUE)
    	{

    		$this->load->model('Client', 'cl', TRUE);

		    // 不要パラメータ削除
		    unset($input_post["submit"]) ;

		    // DB書き込み
		    $this->cl->update_client($input_post);
		    $this->smarty->assign('mess',  "更新が完了しました。");


// 		    // ステータス変更は詳細をログに出力
//     	    $log_data['lg_user_type'] = "2";
//     	    $log_data['lg_type']      = 'client_status_chg';
//     	    $log_data['lg_func']      = 'clientlist_detailchk';
//     	    $log_data['lg_detail']    = 'cl_id = ' . $input_post['cl_id']
//     	    	                            . ' / status_chg = ' . $input_post['cl_status']
//     	    	                            ;
//     	    $this->cl->insert_log($log_data);

    	}

    	// 初期値セット
    	$this->_item_set();

    	$this->smarty->assign('info', $input_post);
    	$this->view('clientlist/detail.tpl');

    }





    // アカウント情報編集
    public function add()
    {

    	// バリデーション・チェック
    	$this->_set_validation03();

    	$this->smarty->assign('tmp_pref', NULL);
    	$this->smarty->assign('tmp_memo', NULL);

    	$this->view('clientlist/add.tpl');

    }



    // アカウント情報編集
    public function addchk()
    {

    	$input_post = $this->input->post();

    	// バリデーション・チェック
    	$this->_set_validation03();
    	if ($this->form_validation->run() == FALSE)
    	{
    		$this->smarty->assign('tmp_pref', NULL);
    		$this->smarty->assign('tmp_memo', NULL);
    		if ($input_post['cl_pref'] != "")
    		{
    			$this->smarty->assign('tmp_pref', $input_post['cl_pref']);				// 都道府県を保持
    		}
    		if ($input_post['cl_memo'] != "")
    		{
    			$this->smarty->assign('tmp_memo', $input_post['cl_memo']);				// 備考を保持
    		}

    	} else {

    		$this->load->model('Client',  'cl', TRUE);
    		$this->load->model('Account', 'ac', TRUE);

    		// メールアドレスの重複チェック
    		if ($this->ac->check_loginid($input_post['cl_id']))
    		{

    			$this->smarty->assign('err_clid',   TRUE);
    			$this->smarty->assign('err_passwd', FALSE);

    		} else {

    			// パスワード再入力チェック
    			if ($input_post['cl_pw'] !== $input_post['retype_password'])
    			{

    				$this->smarty->assign('err_clid',   FALSE);
    				$this->smarty->assign('err_passwd', TRUE);

    			} else {

    				// トランザクション・START
    				$this->db->trans_strict(FALSE);                                     // StrictモードをOFF
    				$this->db->trans_start();                                           // trans_begin

	    				// アカウント情報セット
	    				$set_ac['ac_id'] = $input_post['cl_id'];
	    				$set_ac['ac_pw'] = $input_post['cl_pw'];

	    				// 不要パラメータ削除
	    				unset($input_post["retype_password"]) ;
	    				unset($input_post["_submit"]) ;

	    				// DB書き込み
	    				$_row_id = $this->cl->insert_client($input_post);

	    				// DB書き込み
	    				$set_ac['ac_type'] = 1;
	    				$set_ac['ac_cl_seq'] = $_row_id;
	    				$this->ac->insert_account($set_ac);

    				// トランザクション・COMMIT
    				$this->db->trans_complete();                                        // trans_rollback & trans_commit
    				if ($this->db->trans_status() === FALSE)
    				{
    					$this->smarty->assign('mess',  "トランザクションエラーが発生しました。");
    					log_message('error', 'ADMIN::[Clientlist->addchk()]クライアント新規登録処理 トランザクションエラー');
    				} else {
    					$this->smarty->assign('mess',  "更新が完了しました。");
    				}
     			}
     		}
    	}

    	$this->smarty->assign('tmp_pref', $input_post['cl_pref']);
    	$this->smarty->assign('tmp_memo', $input_post['cl_memo']);

    	//$this->view('clientlist/index.tpl');
    	$this->view('clientlist/add.tpl');

    }






    // Pagination 設定
    private function _get_Pagination($client_countall, $tmp_per_page)
    {

    	$config['base_url']       = base_url() . '/clientlist/search/';			// ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
    	$config['per_page']       = $tmp_per_page;								// 1ページ当たりの表示件数。
    	$config['total_rows']     = $client_countall;							// 総件数。where指定するか？
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

    	// ステータス 選択項目セット
    	$arropt_status = array (
    			'0' => '運 用 中',
    			'1' => '一時停止',
    			'8' => '解　　約',
    			'9' => '削　　除',
    	);

    	$this->smarty->assign('options_cl_status',  $arropt_status);

    }

    // 検索項目 初期値セット
    private function _search_set()
    {

        // ステータス 選択項目セット
        $arropt_status = array (
                ''  => '-- 選択してください --',
                '0' => '運 用 中',
                '1' => '一時停止',
        		'8' => '解　　約',
        );

    	// クライアントID 並び替え選択項目セット
        $arropt_id = array (
                ''     => '-- 選択してください --',
                'DESC' => '降順',
                'ASC'  => '昇順',
        );

    	$this->smarty->assign('options_cl_status',  $arropt_status);
    	$this->smarty->assign('options_orderid',    $arropt_id);

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    			array(
    					'field'   => 'cl_company',
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
    					'field'   => 'cl_contract_str',
    					'label'   => '契約開始日',
    					'rules'   => 'trim|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    			array(
    					'field'   => 'cl_contract_end',
    					'label'   => '契約終了日',
    					'rules'   => 'trim|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
//     			array(
//     					'field'   => 'cl_id',
//     					'label'   => 'ログインID',
//     					'rules'   => 'trim|required|max_length[50]|valid_email'
//     			),
//     			array(
//     					'field'   => 'cl_pw',
//     					'label'   => 'パスワード',
//     					'rules'   => 'trim|required|regex_match[/^[\x21-\x7e]+$/]|min_length[8]|max_length[50]|matches[retype_password]'
//     			),
//     			array(
//     					'field'   => 'retype_password',
//     					'label'   => 'パスワード再入力',
//     					'rules'   => 'trim|required|regex_match[/^[\x21-\x7e]+$/]|min_length[8]|max_length[50]|matches[cl_pw]'
//     			),
    			array(
    					'field'   => 'cl_company',
    					'label'   => '会社名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cl_zip01',
    					'label'   => '郵便番号（3ケタ）',
    					'rules'   => 'trim|required|exact_length[3]|is_numeric'
    			),
    			array(
    					'field'   => 'cl_zip02',
    					'label'   => '郵便番号（4ケタ）',
    					'rules'   => 'trim|required|exact_length[4]|is_numeric'
    			),
    			array(
    					'field'   => 'cl_pref',
    					'label'   => '都道府県',
    					'rules'   => 'trim|required|max_length[4]'
    			),
    			array(
    					'field'   => 'cl_addr01',
    					'label'   => '市区町村',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'cl_addr02',
    					'label'   => '町名・番地',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'cl_buil',
    					'label'   => 'ビル・マンション名など',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'cl_president01',
    					'label'   => '代表者姓',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cl_president02',
    					'label'   => '代表者名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cl_tel01',
    					'label'   => '代表電話番号',
    					'rules'   => 'trim|required|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cl_department',
    					'label'   => '所属部署',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'cl_person01',
    					'label'   => '担当者姓',
    					'rules'   => 'trim|required|max_length[20]'
    			),
    			array(
    					'field'   => 'cl_person02',
    					'label'   => '担当者名',
    					'rules'   => 'trim|required|max_length[20]'
    			),
    			array(
    					'field'   => 'cl_tel02',
    					'label'   => '担当者電話番号',
    					'rules'   => 'trim|required|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cl_mobile',
    					'label'   => '担当者携帯番号',
    					'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cl_fax',
    					'label'   => 'FAX番号',
    					'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cl_mail',
    					'label'   => 'メールアドレス',
    					'rules'   => 'trim|required|max_length[100]|valid_email'
    			),
    			array(
    					'field'   => 'cl_mailsub',
    					'label'   => 'メールアドレス(サブ)',
    					'rules'   => 'trim|max_length[100]|valid_email'
    			),
    			array(
    					'field'   => 'cl_memo',
    					'label'   => '備考',
    					'rules'   => 'trim|max_length[1000]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

    // フォーム・バリデーションチェック : クライアント追加
    private function _set_validation03()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'cl_contract_str',
    					'label'   => '契約開始日',
    					'rules'   => 'trim|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    			array(
    					'field'   => 'cl_contract_end',
    					'label'   => '契約終了日',
    					'rules'   => 'trim|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
    			),
    			array(
    					'field'   => 'cl_company',
    					'label'   => '会社名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cl_zip01',
    					'label'   => '郵便番号（3ケタ）',
    					'rules'   => 'trim|required|exact_length[3]|is_numeric'
    			),
    			array(
    					'field'   => 'cl_zip02',
    					'label'   => '郵便番号（4ケタ）',
    					'rules'   => 'trim|required|exact_length[4]|is_numeric'
    			),
    			array(
    					'field'   => 'cl_pref',
    					'label'   => '都道府県',
    					'rules'   => 'trim|required|max_length[4]'
    			),
    			array(
    					'field'   => 'cl_addr01',
    					'label'   => '市区町村',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'cl_addr02',
    					'label'   => '町名・番地',
    					'rules'   => 'trim|required|max_length[100]'
    			),
    			array(
    					'field'   => 'cl_buil',
    					'label'   => 'ビル・マンション名など',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'cl_president01',
    					'label'   => '代表者姓',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cl_president02',
    					'label'   => '代表者名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'cl_tel01',
    					'label'   => '代表電話番号',
    					'rules'   => 'trim|required|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cl_mail',
    					'label'   => 'メールアドレス',
    					'rules'   => 'trim|required|max_length[100]|valid_email'
    			),
    			array(
    					'field'   => 'cl_mailsub',
    					'label'   => 'メールアドレス(サブ)',
    					'rules'   => 'trim|max_length[100]|valid_email'
    			),
    			array(
    					'field'   => 'cl_department',
    					'label'   => '所属部署',
    					'rules'   => 'trim|max_length[50]'
    			),
    			array(
    					'field'   => 'cl_person01',
    					'label'   => '担当者姓',
    					'rules'   => 'trim|required|max_length[20]'
    			),
    			array(
    					'field'   => 'cl_person02',
    					'label'   => '担当者名',
    					'rules'   => 'trim|required|max_length[20]'
    			),
    			array(
    					'field'   => 'cl_tel02',
    					'label'   => '担当者電話番号',
    					'rules'   => 'trim|required|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cl_mobile',
    					'label'   => '担当者携帯番号',
    					'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cl_fax',
    					'label'   => 'FAX番号',
    					'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
    			),
    			array(
    					'field'   => 'cl_id',
    					'label'   => 'ログインID',
    					'rules'   => 'trim|required|max_length[50]|valid_email'
    			),
    			array(
    					'field'   => 'cl_pw',
    					'label'   => 'パスワード',
    					'rules'   => 'trim|required|regex_match[/^[\x21-\x7e]+$/]|min_length[8]|max_length[50]|matches[retype_password]'
    			),
    			array(
    					'field'   => 'retype_password',
    					'label'   => 'パスワード再入力',
    					'rules'   => 'trim|required|regex_match[/^[\x21-\x7e]+$/]|min_length[8]|max_length[50]|matches[cl_pw]'
    			),
    			array(
    					'field'   => 'cl_memo',
    					'label'   => '備考',
    					'rules'   => 'trim|max_length[1000]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}

