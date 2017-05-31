<?php

class Rootdomainlist extends MY_Controller
{

    /*
     *  ルートドメイン情報管理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

//         $this->smarty->assign('err_date', FALSE);
        $this->smarty->assign('mess',     FALSE);

    }

    // ルートドメイン検索一覧TOP
    public function index()
    {



//     	print_r($_SERVER);
//     	print("<br><br>");



        // セッションデータをクリア
        $this->load->library('lib_auth');
        $this->lib_auth->delete_session('client');

        // バリデーション・チェック
        $this->_set_validation();                                                       // バリデーション設定
        $this->form_validation->run();

        // 1ページ当たりの表示件数
        $this->config->load('config_comm');
        $tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

        // Pagination 現在ページ数の取得：：URIセグメントの取得
        $segments = $this->uri->segment_array();
        if (isset($segments[3]))
        {
            $tmp_offset = $segments[3];
            if (!is_numeric($tmp_offset))
            {
            	//throw new Exception("例外発生！");
            	show_error('指定されたIDは不正です。');
            }

            $tmp_inputpost = $this->input->post();

            // セッションをフラッシュデータとして保存
            $data = array(
            					'c_offset'     => $tmp_offset,
            					'c_back_set'   => "rootdomainlist",
            );
            $this->session->set_userdata($data);
        } else {
            $tmp_offset = 0;
            $tmp_inputpost = array(
                                'rd_rootdomain' => '',
                                'rd_sitename'   => '',
			            		'rd_group'      => '',
			            		'rd_tag'        => '',
			            		'orderid'       => '',
            					'watchlist'     => '',
            );

            // セッションをフラッシュデータとして保存
            $data = array(
                                'c_rd_rootdomain' => '',
                                'c_rd_sitename'   => '',
			            		'c_rd_group'      => '',
			            		'c_rd_tag'        => '',
			            		'c_orderid'       => '',
            					'c_watchlist'     => '',
            					'c_offset'        => $tmp_offset,
            					'c_back_set'      => "rootdomainlist",
            );
            $this->session->set_userdata($data);
        }

        // ルートドメイン情報の取得
        $this->load->model('Rootdomain', 'rd', TRUE);
        list($rd_list, $rd_countall) = $this->rd->get_rootdomainlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp']);

        $this->smarty->assign('list', $rd_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($rd_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination',      $set_pagination['page_link']);
        $this->smarty->assign('countall',            $rd_countall);

        $this->smarty->assign('seach_rd_rootdomain', $tmp_inputpost['rd_rootdomain']);
        $this->smarty->assign('seach_rd_sitename',   $tmp_inputpost['rd_sitename']);
        $this->smarty->assign('seach_rd_group',      $tmp_inputpost['rd_group']);
        $this->smarty->assign('seach_rd_tag',        $tmp_inputpost['rd_tag']);
        $this->smarty->assign('seach_orderid',       $tmp_inputpost['orderid']);
        $this->smarty->assign('seach_watchlist',     $tmp_inputpost['watchlist']);

        $this->view('rootdomainlist/index.tpl');

    }

    // 一覧表示
    public function search()
    {

        if ($this->input->post('submit') == '_submit')
        {
            // セッションをフラッシュデータとして保存
            $data = array(
                            'c_rd_rootdomain' => $this->input->post('rd_rootdomain'),
                            'c_rd_sitename'   => $this->input->post('rd_sitename'),
		            		'c_rd_group'      => $this->input->post('rd_group'),
		            		'c_rd_tag'        => $this->input->post('rd_tag'),
		            		'c_orderid'       => $this->input->post('orderid'),
            				'c_watchlist'     => $this->input->post('watchlist'),
            );
            $this->session->set_userdata($data);

            $tmp_inputpost = $this->input->post();
            unset($tmp_inputpost["submit"]);

        } else {
            // セッションからフラッシュデータ読み込み
            $tmp_inputpost['rd_rootdomain']   = $_SESSION['c_rd_rootdomain'];
            $tmp_inputpost['rd_sitename']     = $_SESSION['c_rd_sitename'];
            $tmp_inputpost['rd_group']        = $_SESSION['c_rd_group'];
            $tmp_inputpost['rd_tag']          = $_SESSION['c_rd_tag'];
            $tmp_inputpost['orderid']         = $_SESSION['c_orderid'];
            $tmp_inputpost['watchlist']       = $_SESSION['c_watchlist'];
        }

        // バリデーション・チェック
        $this->_set_validation();                                               // バリデーション設定
        $this->form_validation->run();

        // Pagination 現在ページ数の取得：：URIセグメントの取得
        $segments = $this->uri->segment_array();
        if (isset($segments[3]))
        {
            $tmp_offset = $segments[3];
            if (!is_numeric($tmp_offset))
            {
            	//throw new Exception("例外発生！");
            	show_error('指定されたIDは不正です。');
            }

        } else {
            $tmp_offset = 0;
        }

        // セッションをフラッシュデータとして保存
        $data = array(
        					'c_offset'     => $tmp_offset,
        );
        $this->session->set_userdata($data);

        // 1ページ当たりの表示件数
        $this->config->load('config_comm');
        $tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

        // ルートドメイン情報の取得
        $this->load->model('Rootdomain', 'rd', TRUE);
        list($rd_list, $rd_countall) = $this->rd->get_rootdomainlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp']);

        $this->smarty->assign('list', $rd_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($rd_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination',   $set_pagination['page_link']);
        $this->smarty->assign('countall',         $rd_countall);


        $this->smarty->assign('seach_rd_rootdomain', $tmp_inputpost['rd_rootdomain']);
        $this->smarty->assign('seach_rd_sitename',   $tmp_inputpost['rd_sitename']);
        $this->smarty->assign('seach_rd_group',      $tmp_inputpost['rd_group']);
        $this->smarty->assign('seach_rd_tag',        $tmp_inputpost['rd_tag']);
        $this->smarty->assign('seach_orderid',       $tmp_inputpost['orderid']);
        $this->smarty->assign('seach_watchlist',     $tmp_inputpost['watchlist']);

        $this->view('rootdomainlist/index.tpl');

    }

    // ルートドメイン内キーワード別順位データ一覧
    public function detail()
    {

//     	// セッションデータをクリア
//     	$this->load->library('lib_auth');
//     	$this->lib_auth->delete_session('client');

    	// バリデーション・チェック
    	$this->_set_validation();												// バリデーション設定

    	// 1ページ当たりの表示件数
    	$this->config->load('config_comm');
    	$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

    	// Pagination 現在ページ数の取得：：URIセグメントの取得
    	$segments = $this->uri->segment_array();
    	if (isset($segments[3]))
    	{
    		$tmp_offset = $segments[3];
    		if (!is_numeric($tmp_offset))
    		{
    			//throw new Exception("例外発生！");
    			show_error('指定されたIDは不正です。');
    		}

    		$tmp_inputpost = $this->input->post();
    	} else {
    		$tmp_offset = 0;
    		$tmp_inputpost = $this->input->post();

    		if (!isset($tmp_inputpost['chg_seq']))
    		{
    			show_404();
    		}

    		$tmp_inputpost = array(
    				'kw_keyword' => '',
    				'kw_domain'  => '',
    				'kw_status'  => 1,
    				'orderid'    => '',
    				'rd_seq'     => $this->input->post('chg_seq'),
    		);

    		// セッションをフラッシュデータとして保存
    		$data = array(
    				'c_kw_keyword' => "",
    				'c_kw_domain'  => "",
    				'c_kw_status'  => 1,
    				'c_orderid'    => "",
    				'c_rd_seq'     => $this->input->post('chg_seq'),
    		);
    		$this->session->set_userdata($data);
    	}

    	// ルートドメイン情報取得
    	$this->load->model('Rootdomain', 'rd', TRUE);
    	$get_rd_data =$this->rd->get_rd_seq($_SESSION['c_rd_seq']);

    	// 該当ルートドメインが設定してあるキーワード情報を取得
    	$_set_kw_data['kw_cl_seq']     = $get_rd_data[0]['rd_cl_seq'];
    	$_set_kw_data['kw_rootdomain'] = $get_rd_data[0]['rd_rootdomain'];
    	$_set_kw_data['kw_keyword']    = NULL;
    	$_set_kw_data['kw_domain']     = NULL;
    	$_set_kw_data['kw_status']     = $_SESSION['c_kw_status'];
    	$_set_kw_data['orderid']       = NULL;

    	$this->load->model('Keyword', 'kw', TRUE);
    	list($kw_list, $kw_countall) = $this->kw->get_kw_rootdomainlist($_set_kw_data, $tmp_per_page, $tmp_offset);

    	$this->smarty->assign('list', $kw_list);

    	// 順位データ情報を取得 (31日分) ＆ グラフ表示
    	$this->load->library('lib_ranking_data');
    	$this->lib_ranking_data->create_ranking_graph($kw_list, $cnt_date=31);

    	$date = new DateTime();
    	$_start_date = $date->format('Y-m-d');
    	$_set_cnt_date = "- " . ($cnt_date - 1) . " days";
    	$_end_date   = $date->modify($_set_cnt_date)->format('Y-m-d');

    	// Pagination 設定
    	$set_pagination = $this->_get_Pagination01($kw_countall, $tmp_per_page);

    	// 初期値セット
    	$this->_search_set();

    	$this->smarty->assign('set_pagination', $set_pagination['page_link']);
    	$this->smarty->assign('countall',       $kw_countall);

    	$this->smarty->assign('rd_seq',         $get_rd_data[0]['rd_seq']);
    	$this->smarty->assign('rd_rootdomain',  $get_rd_data[0]['rd_rootdomain']);
    	$this->smarty->assign('seach_keyword',  $_set_kw_data['kw_keyword']);
    	$this->smarty->assign('seach_domain',   $_set_kw_data['kw_domain']);
    	$this->smarty->assign('seach_orderid',  $_set_kw_data['orderid']);
    	$this->smarty->assign('start_date',     $_start_date);
    	$this->smarty->assign('end_date',       $_end_date);

    	// 「戻る」のページャ先をセット
    	if (isset($_SESSION['c_offset']))
    	{
    		$page_cnt = $_SESSION['c_offset'];
    	} else {
    		$page_cnt = 0;
    	}
    	$this->smarty->assign('seach_page_no', $page_cnt);

    	// 「戻る」の画面先をセット
    	$this->smarty->assign('back_page', $_SESSION['c_back_set']);

    	$this->view('rootdomainlist/detail.tpl');

	}

	// ルートドメイン内キーワード別順位データ一覧
	public function detail_search()
	{

		if ($this->input->post('submit') == '_submit')
		{
			// セッションをフラッシュデータとして保存
			$data = array(
					'c_kw_keyword' => "",
					'c_kw_domain'  => "",
					'c_kw_status'  => 1,
					'c_orderid'    => "",
// 					'c_rd_seq'     => "",
			);
			$this->session->set_userdata($data);

			$tmp_inputpost = $this->input->post();
			$tmp_inputpost['kw_status']  = 1;
			unset($tmp_inputpost["submit"]);

		} else {
			// セッションからフラッシュデータ読み込み
			$tmp_inputpost['kw_keyword'] = $_SESSION['c_kw_keyword'];
			$tmp_inputpost['kw_domain']  = $_SESSION['c_kw_domain'];
			$tmp_inputpost['kw_status']  = $_SESSION['c_kw_status'];
			$tmp_inputpost['orderid']    = $_SESSION['c_orderid'];
// 			$tmp_inputpost['rd_seq']     = $_SESSION['c_rd_seq'];
		}

		// バリデーション・チェック
		$this->_set_validation();                                               // バリデーション設定

		// Pagination 現在ページ数の取得：：URIセグメントの取得
		$segments = $this->uri->segment_array();
		if (isset($segments[3]))
		{
			$tmp_offset = $segments[3];
			if (!is_numeric($tmp_offset))
			{
				//throw new Exception("例外発生！");
				show_error('指定されたIDは不正です。');
			}

		} else {
			$tmp_offset = 0;
		}

		// 1ページ当たりの表示件数
		$this->config->load('config_comm');
		$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

		// ルートドメイン情報取得
		$this->load->model('Rootdomain', 'rd', TRUE);
		$get_rd_data =$this->rd->get_rd_seq($_SESSION['c_rd_seq']);

		// 該当ルートドメインが設定してあるキーワード情報を取得
		$_set_kw_data['kw_cl_seq']     = $get_rd_data[0]['rd_cl_seq'];
		$_set_kw_data['kw_rootdomain'] = $get_rd_data[0]['rd_rootdomain'];
		$_set_kw_data['kw_keyword']    = $tmp_inputpost['kw_keyword'];
		$_set_kw_data['kw_domain']     = $tmp_inputpost['kw_domain'];
		$_set_kw_data['kw_status']     = $tmp_inputpost['kw_status'];
		$_set_kw_data['orderid']       = $tmp_inputpost['orderid'];

		$this->load->model('Keyword', 'kw', TRUE);
		list($kw_list, $kw_countall) = $this->kw->get_kw_rootdomainlist($_set_kw_data, $tmp_per_page, $tmp_offset);

		$this->smarty->assign('list', $kw_list);

		// 順位データ情報を取得 (31日分) ＆ グラフ表示
		$this->load->library('lib_ranking_data');
		$this->lib_ranking_data->create_ranking_graph($kw_list, $cnt_date=31);

		$date = new DateTime();
		$_start_date = $date->format('Y-m-d');
		$_set_cnt_date = "- " . ($cnt_date - 1) . " days";
		$_end_date   = $date->modify($_set_cnt_date)->format('Y-m-d');

		// Pagination 設定
		$set_pagination = $this->_get_Pagination01($kw_countall, $tmp_per_page);

		// 初期値セット
		$this->_search_set();

		$this->smarty->assign('set_pagination', $set_pagination['page_link']);
		$this->smarty->assign('countall', $kw_countall);

		$this->smarty->assign('rd_seq',         $get_rd_data[0]['rd_seq']);
		$this->smarty->assign('rd_rootdomain',  $get_rd_data[0]['rd_rootdomain']);
		$this->smarty->assign('seach_keyword',  $_set_kw_data['kw_keyword']);
		$this->smarty->assign('seach_domain',   $_set_kw_data['kw_domain']);
		$this->smarty->assign('seach_orderid',  $_set_kw_data['orderid']);
		$this->smarty->assign('start_date',     $_start_date);
		$this->smarty->assign('end_date',       $_end_date);

		$this->view('rootdomainlist/detail.tpl');

	}

    // ルートドメイン情報編集
    public function chg()
    {

    	// セッションデータをクリア
//     	$this->load->library('lib_auth');
//     	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

    	if (!isset($input_post['chg_seq']))
    	{
    		show_404();
    	}

    	// バリデーション設定
    	$this->_set_validation();

    	// ルートドメイン情報取得
    	$this->load->model('Rootdomain', 'rd', TRUE);
    	$get_rd_data =$this->rd->get_rd_seq($input_post['chg_seq']);

    	// 初期値セット
    	$this->_item_set();

    	// 設定グループのセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->grouptag_set($get_rd_data[0]['rd_cl_seq'], $get_rd_data[0]['rd_group'], 0);

    	// 設定タグのセット
    	$this->lib_keyword->grouptag_set($get_rd_data[0]['rd_cl_seq'], $get_rd_data[0]['rd_tag'], 1);

    	$data = array(
	    			'c_old_group' => $get_rd_data[0]['rd_group'],
	    			'c_old_tag'   => $get_rd_data[0]['rd_tag'],
    	);
    	$this->session->set_userdata($data);

    	$this->smarty->assign('info', $get_rd_data[0]);

    	// 「戻る」のページャ先をセット
    	if (isset($_SESSION['c_offset']))
    	{
    		$page_cnt = $_SESSION['c_offset'];
    	} else {
    		$page_cnt = 0;
    	}
    	$this->smarty->assign('seach_page_no', $page_cnt);

    	$this->view('rootdomainlist/chg.tpl');

    }

    // ルートドメイン情報編集確認
    public function chg_chk()
    {

    	$input_post = $this->input->post();

    	// 初期値セット
    	$this->_item_set();

    	// 「戻る」のページャ先をセット
    	if (isset($_SESSION['c_offset']))
    	{
    		$page_cnt = $_SESSION['c_offset'];
    	} else {
    		$page_cnt = 0;
    	}


    	$this->load->model('Rootdomain', 'rd', TRUE);
    	$this->load->model('Group_tag',  'gt', TRUE);
    	$this->load->library('lib_keyword');

    	// バリデーション設定
    	$this->_set_validation02();
    	if ($this->form_validation->run() == FALSE)
    	{
    		$this->smarty->assign('mess', "<font color=red>項目に入力エラーが発生しました。</font>");

    		// ルートドメイン情報取得
    		$get_rd_data =$this->rd->get_rd_seq($input_post['rd_seq']);

    		// 設定グループのセット
    		$this->lib_keyword->grouptag_set($get_rd_data[0]['rd_cl_seq'], $get_rd_data[0]['rd_group'], 0);

    		// 設定タグのセット
    		$this->lib_keyword->grouptag_set($get_rd_data[0]['rd_cl_seq'], $get_rd_data[0]['rd_tag'], 1);

    		$this->smarty->assign('info',     $input_post);

    		$this->view('rootdomainlist/chg.tpl');
    		return;

    	} else {

    		$set_rd_data = $input_post;
    		unset($set_rd_data['_submit']);

    		// グループ入力情報を分解＆生成＆セット
    		if (empty($set_rd_data['rd_group']))
    		{
    			$this->_group_set($set_rd_data['rd_cl_seq'], "");
    			$set_rd_data['rd_group'] = "";
    		} else {

    			$_rd_group = "";
    			foreach ($set_rd_data['rd_group'] as $key => $value)
    			{
    				$_rd_group .= "[" . $value . "]";
    			}
    			$this->_group_set($set_rd_data['rd_cl_seq'], $_rd_group);

    			$set_rd_data['rd_group'] = $_rd_group;
    		}


    		// タグ入力情報を分解＆生成＆セット
    		if (empty($set_rd_data['rd_tag']))
    		{
    			$this->_tag_set($set_rd_data['rd_cl_seq'], "");
    			$set_rd_data['rd_tag'] = "";
    		} else {

    			$_rd_tag = "";
    			foreach ($set_rd_data['rd_tag'] as $key => $value)
    			{
    				$_rd_tag .= "[" . $value . "]";
    			}
    			$this->_tag_set($set_rd_data['rd_cl_seq'], $_rd_tag);

    			$set_rd_data['rd_tag'] = $_rd_tag;
    		}

    		// UPDATE
    		$this->rd->update_rootdomain($set_rd_data);

    		// 新規に追加された設定グループをレコード追加
    		if (!empty($input_post['rd_group']))
    		{
    			foreach ($input_post['rd_group'] as $key => $value)
    			{
    				$get_gt_name = $this->gt->get_gt_name($value, $set_rd_data['rd_cl_seq'], 0);

    				if (count($get_gt_name) == 0)
    				{
    					$set_gt_data['gt_name']   = $value;
    					$set_gt_data['gt_cl_seq'] = $set_rd_data['rd_cl_seq'];
    					$set_gt_data['gt_type']   = 0;

    					// INSERT
    					$this->gt->insert_group_tag($set_gt_data);
    				}
    			}
    		}

    		// 新規に追加された設定タグをレコード追加
    		if (!empty($input_post['rd_tag']))
    		{
    			foreach ($input_post['rd_tag'] as $key => $value)
    			{
    				$get_gt_name = $this->gt->get_gt_name($value, $set_rd_data['rd_cl_seq'], 1);

    				if (count($get_gt_name) == 0)
    				{
    					$set_gt_data['gt_name']   = $value;
    					$set_gt_data['gt_cl_seq'] = $set_rd_data['rd_cl_seq'];
    					$set_gt_data['gt_type']   = 1;

    					// INSERT
    					$this->gt->insert_group_tag($set_gt_data);
    				}
    			}
    		}

    		redirect('/rootdomainlist/search/' . $page_cnt);
    	}
    }


//     // ルートドメイン情報編集確認
//     public function chg_conf()
//     {

//     	$input_post = $this->input->post();

//     	 // 初期値セット
//     	$this->_item_set();

//     	// バリデーション設定
//     	$this->_set_validation02();
//     	if ($this->form_validation->run() == FALSE)
//     	{
//     		$this->smarty->assign('mess', "<font color=red>項目に入力エラーが発生しました。</font>");

//     		$this->smarty->assign('info',     $input_post);

//     		$this->view('rootdomainlist/chg.tpl');

//     	} else {


//     		// 設定グループチェック
//     		if (!isset($input_post['rd_group']))
//     		{
//     			// ダミー入力
//     			$input_post['rd_group'] = NULL;
//     		}

//     		// 設定TAGチェック
//     		if (!isset($input_post['rd_tag']))
//     		{
//     			// ダミー入力
//     			$input_post['rd_tag'] = NULL;
//     		}



//     		$this->smarty->assign('info', $input_post);

//     		$this->view('rootdomainlist/chg_conf.tpl');

//     	}

//     }

//     // ルートドメイン情報編集完了
//     public function chg_comp()
//     {

//     	$input_post = $this->input->post();

//     	$set_rd_data = $input_post['info'];
//     	unset($set_rd_data['submit']);

//     	// グループ入力情報を分解＆生成＆セット
//     	if ($set_rd_data['rd_group'] == "")
//     	{
//     		$this->_group_set($set_rd_data['rd_cl_seq'], "");
//     		$set_rd_data['rd_group'] = "";
//     	} else {

//     		$_rd_group = "";
//     		foreach ($set_rd_data['rd_group'] as $key => $value)
//     		{
//     			$_rd_group .= "[" . $value . "]";
//     		}
//     		$this->_group_set($set_rd_data['rd_cl_seq'], $_rd_group);

//     		$set_rd_data['rd_group'] = $_rd_group;
//     	}


//     	// タグ入力情報を分解＆生成＆セット
//     	if ($set_rd_data['rd_tag'] == "")
//     	{
//     		$this->_tag_set($set_rd_data['rd_cl_seq'], "");
//     		$set_rd_data['rd_tag'] = "";
//     	} else {

//     		$_rd_tag = "";
//     		foreach ($set_rd_data['rd_tag'] as $key => $value)
//     		{
//     			$_rd_tag .= "[" . $value . "]";
//     		}
//     		$this->_tag_set($set_rd_data['rd_cl_seq'], $_rd_tag);

//     		$set_rd_data['rd_tag'] = $_rd_tag;
//     	}

//     	// バリデーション・チェック
//     	$this->_set_validation();

//     	// 「戻る」ボタン押下の場合
//     	if ( $this->input->post('back') ) {

//     		// 初期値セット
//     		$this->_item_set();

//     		$this->smarty->assign('info',     $input_post['info']);

//     		$this->view('rootdomainlist/chg.tpl');
//     		return;
//     	}

//     	$this->load->model('Rootdomain', 'rd', TRUE);
//     	$this->load->model('Group_tag',  'gt', TRUE);


//     	// UPDATE
//     	$this->rd->update_rootdomain($set_rd_data);

//     	// 新規に追加された設定グループをレコード追加
//     	if ($input_post['info']['rd_group'] != "")
//     	{
//     		foreach ($input_post['info']['rd_group'] as $key => $value)
//     		{
//     			$get_gt_name = $this->gt->get_gt_name($value, $set_rd_data['rd_cl_seq'], 0);

//     			if (count($get_gt_name) == 0)
//     			{
//     				$set_gt_data['gt_name']   = $value;
//     				$set_gt_data['gt_cl_seq'] = $set_rd_data['rd_cl_seq'];
//     				$set_gt_data['gt_type']   = 0;

//     				// INSERT
//     				$this->gt->insert_group_tag($set_gt_data);
//     			}
//     		}
//     	}


//     	// 新規に追加された設定タグをレコード追加
//     	if ($input_post['info']['rd_tag'] != "")
//     	{
//     		foreach ($input_post['info']['rd_tag'] as $key => $value)
//     		{
//     			$get_gt_name = $this->gt->get_gt_name($value, $set_rd_data['rd_cl_seq'], 1);

//     			if (count($get_gt_name) == 0)
//     			{
//     				$set_gt_data['gt_name']   = $value;
//     				$set_gt_data['gt_cl_seq'] = $set_rd_data['rd_cl_seq'];
//     				$set_gt_data['gt_type']   = 1;

//     				// INSERT
//     				$this->gt->insert_group_tag($set_gt_data);
//     			}
//     		}
//     	}


//     	redirect('/rootdomainlist/');

//     }

    // ウォッチリストへの登録＆解除
    public function watchlist()
    {

    	$input_post = $this->input->post();

    	if (!isset($input_post['chg_seq']))
    	{
    		show_404();
    	}

    	$this->load->model('Rootdomain', 'rd', TRUE);
    	$this->load->model('Watchlist', 'wt', TRUE);

    	// ルートドメイン設定情報を取得
    	$get_rd_data =$this->rd->get_rd_seq($input_post['chg_seq']);

    	// ウォッチリスト情報有無をチェック
    	$set_wt_data['wt_ac_seq']        = $_SESSION['c_memSeq'];
    	$set_wt_data['wt_cl_seq']        = $get_rd_data[0]['rd_cl_seq'];
    	$set_wt_data['wt_rd_seq']        = $get_rd_data[0]['rd_seq'];
    	$set_wt_data['wt_kw_rootdomain'] = $get_rd_data[0]['rd_rootdomain'];
    	$get_wt_data = $this->wt->get_watchlist_data($set_wt_data['wt_ac_seq'], $set_wt_data['wt_cl_seq'], NULL, $get_rd_data[0]['rd_seq']);

    	if (empty($get_wt_data))
    	{
    		// 新規登録
    		$this->wt->insert_watchlist($set_wt_data);
    	} else {
    		// 削除
    		$this->wt->delete_watchlist($set_wt_data);
    	}

    	// セッションからフラッシュデータ読み込み
    	$tmp_inputpost['rd_rootdomain']  = $_SESSION['c_rd_rootdomain'];
    	$tmp_inputpost['rd_sitename']    = $_SESSION['c_rd_sitename'];
    	$tmp_inputpost['orderid']        = $_SESSION['c_orderid'];
    	$tmp_inputpost['watchlist']      = $_SESSION['c_watchlist'];

    	// バリデーション・チェック
    	$this->_set_validation();                                               // バリデーション設定

    	// Pagination 現在ページ数の取得：：URIセグメントの取得
    	$tmp_offset = $_SESSION['c_offset'];

    	// 1ページ当たりの表示件数
    	$this->config->load('config_comm');
    	$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');


    	// ルートドメイン情報の取得
    	$this->load->model('Rootdomain', 'rd', TRUE);
    	list($rd_list, $rd_countall) = $this->rd->get_rootdomainlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp']);

    	$this->smarty->assign('list', $rd_list);

    	// Pagination 設定
    	$set_pagination = $this->_get_Pagination($rd_countall, $tmp_per_page);

    	// 初期値セット
    	$this->_search_set();

    	$this->smarty->assign('set_pagination',      $set_pagination['page_link']);
    	$this->smarty->assign('countall',            $rd_countall);

    	$this->smarty->assign('seach_rd_rootdomain', $tmp_inputpost['rd_rootdomain']);
    	$this->smarty->assign('seach_rd_sitename',   $tmp_inputpost['rd_sitename']);
    	$this->smarty->assign('seach_orderid',       $tmp_inputpost['orderid']);
    	$this->smarty->assign('seach_watchlist',     $tmp_inputpost['watchlist']);

    	redirect("/rootdomainlist/search/$tmp_offset/");
    	//$this->view('rootdomainlist/index.tpl');

    }










    // Pagination 設定
    private function _get_Pagination($countall, $tmp_per_page)
    {

    	$config['base_url']       = base_url() . '/rootdomainlist/search/';     // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
    	$config['per_page']       = $tmp_per_page;                              // 1ページ当たりの表示件数。
    	$config['total_rows']     = $countall;                                  // 総件数。where指定するか？
    	//$config['uri_segment']    = 4;                                        // オフセット値がURIパスの何セグメント目とするか設定
    	$config['num_links']      = 5;                                          // 現在のページ番号の左右にいくつのページ番号リンクを生成するか設定
    	$config['full_tag_open']  = '<p class="pagination">';                   // ページネーションリンク全体を階層化するHTMLタグの先頭タグ文字列を指定
    	$config['full_tag_close'] = '</p>';                                     // ページネーションリンク全体を階層化するHTMLタグの閉じタグ文字列を指定
    	$config['first_link']     = '最初へ';                                   // 最初のページを表すテキスト。
    	$config['last_link']      = '最後へ';                                   // 最後のページを表すテキスト。
    	$config['prev_link']      = '前へ';                                     // 前のページへのリンクを表わす文字列を指定
    	$config['next_link']      = '次へ';                                     // 次のページへのリンクを表わす文字列を指定

    	$this->load->library('pagination', $config);                            // Paginationクラス読み込み
    	$set_page['page_link'] = $this->pagination->create_links();

    	return $set_page;

    }

    // Pagination 設定：グループ内検索
    private function _get_Pagination01($countall, $tmp_per_page)
    {

    	$config['base_url']       = base_url() . '/rootdomainlist/detail_search/';   // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
    	$config['per_page']       = $tmp_per_page;                              // 1ページ当たりの表示件数。
    	$config['total_rows']     = $countall;                                  // 総件数。where指定するか？
    	//$config['uri_segment']    = 4;                                        // オフセット値がURIパスの何セグメント目とするか設定
    	$config['num_links']      = 5;                                          //現在のページ番号の左右にいくつのページ番号リンクを生成するか設定
    	$config['full_tag_open']  = '<p class="pagination">';                   // ページネーションリンク全体を階層化するHTMLタグの先頭タグ文字列を指定
    	$config['full_tag_close'] = '</p>';                                     // ページネーションリンク全体を階層化するHTMLタグの閉じタグ文字列を指定
    	$config['first_link']     = '最初へ';                                   // 最初のページを表すテキスト。
    	$config['last_link']      = '最後へ';                                   // 最後のページを表すテキスト。
    	$config['prev_link']      = '前へ';                                     // 前のページへのリンクを表わす文字列を指定
    	$config['next_link']      = '次へ';                                     // 次のページへのリンクを表わす文字列を指定

    	$this->load->library('pagination', $config);                            // Paginationクラス読み込み
    	$set_page['page_link'] = $this->pagination->create_links();

    	return $set_page;

    }

    // 初期値セット
    private function _item_set()
    {

//     	// ステータスのセット
//     	$this->config->load('config_status');
//     	$opt_rd_status = $this->config->item('KEYWORD_rd_STATUS');

//     	// 最大取得順位
//     	$opt_kw_maxposition = $this->config->item('KEYWORD_KW_MAXPOSITION');

//     	// 最大取得順位
//     	$opt_kw_trytimes = $this->config->item('KEYWORD_KW_TRYTIMES');

//     	$this->smarty->assign('options_kw_status',  $opt_kw_status);
//     	$this->smarty->assign('options_kw_maxposition', $opt_kw_maxposition);
//     	$this->smarty->assign('options_kw_trytimes', $opt_kw_trytimes);

    }

    // 検索項目 初期値セット
    private function _search_set()
    {

    	// ルートドメインID 並び替え選択項目セット
    	$arropt_id = array (
    			''     => '-- 選択してください --',
    			'DESC' => '降順',
    			'ASC'  => '昇順',
    	);

    	// ウォッチリスト 表示有無選択項目セット
    	$arropt_watch = array (
    			'0'  => '-- 選択してください --',
    			'1'  => '表示する',
    	);

    	$this->smarty->assign('options_orderid',   $arropt_id);
    	$this->smarty->assign('options_watchlist', $arropt_watch);

    }

    // 設定グループのセット
    private function _group_set($cl_seq, $rd_group)
    {

    	// タグ情報取得
    	$this->load->model('Group_tag', 'gt', TRUE);
    	$group_list =$this->gt->get_gt_clseq($cl_seq, 0);

    	$opt_group = "";
    	foreach ($group_list as $key => $value)
    	{

    		$comp_group = "[" . $value['gt_name'] . "]";
    		if (strpos($rd_group, $comp_group) !== false)
    		{
    			$opt_group .= '<option selected="selected" value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
    		} else {
    			$opt_group .= '<option value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
    		}

    	}

    	$this->smarty->assign('options_group', $opt_group);

    }

    // 設定タグのセット
    private function _tag_set($cl_seq, $rd_tag)
    {

    	// タグ情報取得
    	$this->load->model('Group_tag', 'gt', TRUE);
    	$tag_list =$this->gt->get_gt_clseq($cl_seq, 1);

    	$opt_tag = "";
    	foreach ($tag_list as $key => $value)
    	{

    		$comp_tag = "[" . $value['gt_name'] . "]";
    		if (strpos($rd_tag, $comp_tag) !== false)
    		{
    			$opt_tag .= '<option selected="selected" value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
    		} else {
    			$opt_tag .= '<option value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
    		}

    	}

    	$this->smarty->assign('options_tag', $opt_tag);

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック : 編集チェック
    private function _set_validation02()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'rd_sitename',
    					'label'   => 'サイト名',
    					'rules'   => 'trim|max_length[100]'
    			),
    			array(
    					'field'   => 'rd_group',
    					'label'   => '設定グループ',
    					'rules'   => 'trim|max_length[48]'
    			),
    			array(
    					'field'   => 'rd_tag',
    					'label'   => 'タグ設定',
    					'rules'   => 'trim|max_length[900]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}
