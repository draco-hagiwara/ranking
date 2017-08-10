<?php

class Taglist extends MY_Controller
{

    /*
     *  タグ管理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('err_date', FALSE);
        $this->smarty->assign('mess01',   FALSE);
        $this->smarty->assign('mess02',   FALSE);

    }

    // タグ検索一覧TOP
    public function index()
    {


//     	print_r($_SESSION);


    	// セッションデータをクリア
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

    		// セッションをフラッシュデータとして保存
    		$data = array(
    				'c_offset'     => $tmp_offset,
    				'c_back_set'   => "taglist",
    		);
    		$this->session->set_userdata($data);
    	} else {
    		$tmp_offset = 0;
    		$tmp_inputpost = array(
    				'gt_name' => '',
    				'orderid' => '',
    		);

    		// セッションをフラッシュデータとして保存
    		$data = array(
    				'c_gt_name' => "",
    				'c_orderid' => "",
		    		'c_offset'   => $tmp_offset,
		    		'c_back_set' => "taglist",
    		);
    		$this->session->set_userdata($data);
    	}

    	// タグ情報の取得
    	$this->load->model('Group_tag', 'gt', TRUE);
    	$_set_gt_data = $tmp_inputpost;
    	$_set_gt_data['gt_type']   = 1;
    	$_set_gt_data['gt_cl_seq'] = $_SESSION['c_memGrp'];
    	list($gt_list, $gt_countall) = $this->gt->get_gtlist($_set_gt_data, $tmp_per_page, $tmp_offset);

    	// タグ情報を整形
//     	$this->load->library('lib_keyword');
//     	$this->lib_keyword->create_mold_tag($gt_list);
    	$this->smarty->assign('list', $gt_list);

    	// Pagination 設定
    	$set_pagination = $this->_get_Pagination($gt_countall, $tmp_per_page);

    	// 初期値セット
    	$this->_search_set();

    	$this->smarty->assign('set_pagination', $set_pagination['page_link']);
    	$this->smarty->assign('countall', $gt_countall);

    	$this->smarty->assign('seach_gtname',  $tmp_inputpost['gt_name']);
    	$this->smarty->assign('seach_orderid', $tmp_inputpost['orderid']);

    	$this->view('taglist/index.tpl');

    }




    // タグ検索一覧TOP
    public function tag_test()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');
//     	$this->load->model('comm_auth', 'comm_auth', TRUE);
//     	$this->comm_auth->delete_session('client');

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
    		$tmp_inputpost = array(
    				'gt_name' => '',
    				'orderid' => '',
    		);

    		// セッションをフラッシュデータとして保存
    		$data = array(
    				'c_gt_name' => "",
    				'c_orderid' => "",
    		);
    		$this->session->set_userdata($data);
    	}

    	// タグ情報の取得
    	$this->load->model('Group_tag', 'gt', TRUE);
    	$_set_gt_data = $tmp_inputpost;
    	$_set_gt_data['gt_type']   = 1;
    	$_set_gt_data['gt_cl_seq'] = $_SESSION['c_memGrp'];
    	list($gt_list, $gt_countall) = $this->gt->get_gtlist($_set_gt_data, $tmp_per_page, $tmp_offset);

    	// タグ情報を整形
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->create_mold_tag($gt_list);
    	//     	$this->smarty->assign('list', $gt_list);

    	// Pagination 設定
    	$set_pagination = $this->_get_Pagination($gt_countall, $tmp_per_page);

    	// 初期値セット
    	$this->_search_set();

    	$this->smarty->assign('set_pagination', $set_pagination['page_link']);
    	$this->smarty->assign('countall', $gt_countall);

    	$this->smarty->assign('seach_gtname',  $tmp_inputpost['gt_name']);
    	$this->smarty->assign('seach_orderid', $tmp_inputpost['orderid']);

    	$this->view('taglist/tag_test.tpl');

    }





    // 一覧表示
    public function search()
    {

    	if ($this->input->post('submit') == '_submit')
    	{
    		// セッションをフラッシュデータとして保存
    		$data = array(
    				'c_gt_name' => $this->input->post('gt_name'),
    				'c_orderid' => $this->input->post('orderid'),
    		);
    		$this->session->set_userdata($data);

    		$tmp_inputpost = $this->input->post();
    		unset($tmp_inputpost["submit"]);

    	} else {
    		// セッションからフラッシュデータ読み込み
    		$tmp_inputpost['gt_name'] = $_SESSION['c_gt_name'];
    		$tmp_inputpost['orderid'] = $_SESSION['c_orderid'];
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

    	// セッションをフラッシュデータとして保存
    	$data = array(
    			'c_offset'     => $tmp_offset,
    	);
    	$this->session->set_userdata($data);

    	// 1ページ当たりの表示件数
    	$this->config->load('config_comm');
    	$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

    	// タグ情報の取得
    	$this->load->model('Group_tag', 'gt', TRUE);
    	$_set_gt_data = $tmp_inputpost;
    	$_set_gt_data['gt_type']   = 1;
    	$_set_gt_data['gt_cl_seq'] = $_SESSION['c_memGrp'];
    	list($gt_list, $gt_countall) = $this->gt->get_gtlist($_set_gt_data, $tmp_per_page, $tmp_offset);
    	$this->smarty->assign('list', $gt_list);

    	// タグ情報を整形
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->create_mold_tag($gt_list);

    	// Pagination 設定
    	$set_pagination = $this->_get_Pagination($gt_countall, $tmp_per_page);

    	// 初期値セット
    	$this->_search_set();

    	$this->smarty->assign('set_pagination', $set_pagination['page_link']);
    	$this->smarty->assign('countall', $gt_countall);

    	$this->smarty->assign('seach_gtname',  $tmp_inputpost['gt_name']);
    	$this->smarty->assign('seach_orderid', $tmp_inputpost['orderid']);

    	$this->view('taglist/index.tpl');

    }

    // グループ別順位データ一覧
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
	    				'gt_name'   => '',
	    				'orderid'   => '',
	    				'gt_seq'    => $this->input->post('chg_seq'),
    		);

    		// セッションをフラッシュデータとして保存
    		$data = array(
	    				'c_gt_name'    => "",
	    				'c_orderid'    => "",
	    				'c_gt_seq'     => $this->input->post('chg_seq'),
			    		'c_kw_keyword' => "",
			    		'c_kw_domain'  => "",
			    		'c_kw_status'  => "",
    		);
    		$this->session->set_userdata($data);
    	}

    	// タグ情報の取得
    	$this->load->model('Group_tag', 'gt', TRUE);
    	$get_gt_data = $this->gt->get_gt_seq($_SESSION['c_gt_seq']);

    	// 該当タグが設定してあるキーワード情報を取得
    	$_set_kw_data['kw_cl_seq']  = $_SESSION['c_memGrp'];
    	$_set_kw_data['kw_tag']     = $get_gt_data[0]['gt_name'];
    	$_set_kw_data['kw_keyword'] = NULL;
    	$_set_kw_data['kw_domain']  = NULL;
    	$_set_kw_data['kw_status']  = 1;
    	$_set_kw_data['orderid']    = NULL;

    	$this->load->model('Keyword', 'kw', TRUE);
    	list($kw_list, $kw_countall) = $this->kw->get_kw_taglist($_set_kw_data, $tmp_per_page, $tmp_offset);

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

    	$this->smarty->assign('gt_name',       $get_gt_data[0]['gt_name']);
    	$this->smarty->assign('gt_seq',        $get_gt_data[0]['gt_seq']);
    	$this->smarty->assign('seach_keyword', $_set_kw_data['kw_keyword']);
    	$this->smarty->assign('seach_domain',  $_set_kw_data['kw_domain']);
    	$this->smarty->assign('seach_orderid', $_set_kw_data['orderid']);
    	$this->smarty->assign('start_date',    $_start_date);
    	$this->smarty->assign('end_date',      $_end_date);

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

    	$this->view('taglist/detail.tpl');

    }

    // タグ別順位データ一覧
    public function detail_search()
    {

    	if ($this->input->post('submit') == '_submit')
    	{
    		// セッションをフラッシュデータとして保存
    		$data = array(
		    				'c_kw_keyword' => "",
		    				'c_kw_domain'  => '',
		    				'c_orderid'    => "",
    		);
    		$this->session->set_userdata($data);

    		$tmp_inputpost = $this->input->post();
    		unset($tmp_inputpost["submit"]);

    	} else {
    		// セッションからフラッシュデータ読み込み
    		$tmp_inputpost['kw_keyword'] = $_SESSION['c_kw_keyword'];
    		$tmp_inputpost['kw_domain']  = $_SESSION['c_kw_domain'];
    		$tmp_inputpost['orderid']    = $_SESSION['c_orderid'];
    	}

    	// バリデーション・チェック
    	$this->_set_validation();                                               // バリデーション設定

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

    	// タグ情報の取得
    	$this->load->model('Group_tag', 'gt', TRUE);
    	$get_gt_data = $this->gt->get_gt_seq($_SESSION['c_gt_seq']);

    	// 該当タグが設定してあるキーワード情報を取得
    	$_set_kw_data['kw_cl_seq']  = $_SESSION['c_memGrp'];
    	$_set_kw_data['kw_tag']     = $get_gt_data[0]['gt_name'];
    	$_set_kw_data['kw_keyword'] = $tmp_inputpost['kw_keyword'];
    	$_set_kw_data['kw_domain']  = $tmp_inputpost['kw_domain'];
    	$_set_kw_data['kw_status']  = NULL;
    	$_set_kw_data['orderid']    = $tmp_inputpost['orderid'];

    	$this->load->model('Keyword', 'kw', TRUE);
    	list($kw_list, $kw_countall) = $this->kw->get_kw_taglist($_set_kw_data, $tmp_per_page, $tmp_offset);

    	$this->smarty->assign('list', $kw_list);

    	// 順位データ情報を取得 (31日分) ＆ グラフ表示
    	$this->load->library('lib_ranking_data');
    	$this->lib_ranking_data->create_ranking_graph($kw_list, $cnt_date=31);
//     	$this->load->model('Ranking', 'rk', TRUE);

//     	$cnt_date = 31;																// 表示期間。後にconfigで定義。
    	$date = new DateTime();
    	$_start_date = $date->format('Y-m-d');
    	$_set_cnt_date = "- " . ($cnt_date - 1) . " days";
    	$_end_date   = $date->modify($_set_cnt_date)->format('Y-m-d');

//     	$this->load->library('lib_ranking_data');
//     	foreach ($kw_list as $key => $value)
//     	{
//     		$this->lib_ranking_data->get_ranking_graph($value['kw_seq'], $cnt_date);
// //     		$this->_get_ranking_graph($value['kw_seq'], $cnt_date);
//     	}

    	// Pagination 設定
    	$set_pagination = $this->_get_Pagination01($kw_countall, $tmp_per_page);

    	// 初期値セット
    	$this->_search_set();

    	$this->smarty->assign('set_pagination', $set_pagination['page_link']);
    	$this->smarty->assign('countall', $kw_countall);

    	$this->smarty->assign('gt_name',       $get_gt_data[0]['gt_name']);
    	$this->smarty->assign('gt_seq',        $get_gt_data[0]['gt_seq']);
    	$this->smarty->assign('seach_keyword', $_set_kw_data['kw_keyword']);
    	$this->smarty->assign('seach_domain',  $_set_kw_data['kw_domain']);
    	$this->smarty->assign('seach_orderid', $_set_kw_data['orderid']);
    	$this->smarty->assign('start_date',    $_start_date);
    	$this->smarty->assign('end_date',      $_end_date);

    	$this->view('taglist/detail.tpl');

    }

    // タグ登録＆更新TOP
    public function add()
    {

    	// セッションデータをクリア
//     	$this->load->library('lib_auth');
//     	$this->lib_auth->delete_session('client');

    	// バリデーション設定
    	$this->_set_validation();

//     	// 設定タグのセット
//     	$this->load->library('lib_keyword');
//     	$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], NULL, 1);

//     	$this->smarty->assign('disp01', FALSE);
    	$this->smarty->assign('tmp_new_memo', NULL);

    	$this->view('taglist/add.tpl');

    }

    // タグ情報追加
    public function add_comp()
    {

    	$input_post = $this->input->post();

    	// バリデーション設定
    	$this->_set_validation02();
    	if ($this->form_validation->run() == TRUE)
    	{

    		$this->load->model('Group_tag', 'gt', TRUE);

    		$_tagname = str_replace("　", " ", $input_post['new_name']);;
    		$set_gt_data['gt_name']   = trim($_tagname);
//     		$set_gt_data['gt_name']   = $input_post['new_name'];

    		// 重複チェック
    		$get_gt_name = $this->gt->get_gt_name($set_gt_data['gt_name'], $_SESSION['c_memGrp'], 1);
    		if (empty($get_gt_name))
    		{

	    		$set_gt_data['gt_memo']   = $input_post['new_memo'];
	    		$set_gt_data['gt_cl_seq'] = $_SESSION['c_memGrp'];
	    		$set_gt_data['gt_type']   = 1;

	    		// INSERT
	    		$this->gt->insert_group_tag($set_gt_data);

	    		$this->smarty->assign('mess02', "<font color=blue>タグ名が追加されました。</font>");

    		} else {
    			$this->smarty->assign('mess02', "<font color=red>ERROR::同一タグ名が既に存在します。</font>");
    		}

    	}

    	// 設定タグのセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], NULL, 1);

//     	$this->smarty->assign('disp01', FALSE);
    	$this->smarty->assign('tmp_new_memo', $input_post['new_memo']);

    	$this->view('taglist/add.tpl');

    }

    // タグ情報編集
    public function chg()
    {

    	$input_post = $this->input->post();

    	if (!isset($input_post['gt_name']))
    	{
    		redirect('/taglist/chg/');
    	}

    	// バリデーション設定
    	$this->_set_validation();

    	// 設定タグのセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], NULL, 1);
//     	$this->_group_set($_SESSION['c_memGrp'], $input_post['gt_name']);

    	// メモ欄読み込み
    	$this->load->model('Group_tag', 'gt', TRUE);
    	$get_gt_data = $this->gt->get_gt_name($input_post['gt_name'], $_SESSION['c_memGrp'], 1);

    	$this->smarty->assign('gt_seq',   $get_gt_data[0]['gt_seq']);
    	$this->smarty->assign('gt_name',  $input_post['gt_name']);
    	$this->smarty->assign('disp01',   TRUE);
    	$this->smarty->assign('tmp_memo', $get_gt_data[0]['gt_memo']);
    	$this->smarty->assign('tmp_new_memo', NULL);

    	$this->view('taglist/chg.tpl');

    }

    // タグ情報編集
    public function chg_comp()
    {

    	$input_post = $this->input->post();



//     	print_r($input_post);
//     	print("<br><br>");
//     	exit;


    	// バリデーション設定
    	$this->_set_validation01();
    	if ($this->form_validation->run() == TRUE)
    	{

    		$this->load->model('Group_tag',  'gt', TRUE);
    		$this->load->model('Keyword',    'kw', TRUE);
    		$this->load->model('Rootdomain', 'rd', TRUE);

    		if ($input_post['submit'] == '_change')
    		{

    			$_tagname = str_replace("　", " ", $input_post['gt_name']);;
    			$set_gt_data['gt_name']     = trim($_tagname);

    			// 重複チェック
    			$get_gt_name = $this->gt->get_gt_name($set_gt_data['gt_name'], $_SESSION['c_memGrp'], 1);
    			if (empty($get_gt_name) || ($set_gt_data['gt_name'] == $input_post['old_gt_name']))
    			{

    				// UPDATE
    				$set_gt_data['gt_cl_seq']   = $_SESSION['c_memGrp'];
    				$set_gt_data['old_gt_name'] = $input_post['old_gt_name'];
    				$set_gt_data['gt_memo']     = $input_post['gt_memo'];
    				$this->gt->update_gt_name($set_gt_data, $type=1);

    				// タグ(キーワード情報)一括書き換え
    				$set_kw_data['kw_cl_seq']   = $_SESSION['c_memGrp'];
    				$set_kw_data['kw_tag']      = $input_post['old_gt_name'];
    				$get_tag_data = $this->kw->get_kw_tag($set_kw_data);

    				if (!empty($get_tag_data))
    				{
    					foreach ($get_tag_data as $key => $value)
    					{
    						$_org_word = "[" . $input_post['old_gt_name'] . "]";
    						$_chg_word = "[" . $input_post['gt_name'] . "]";
    						$get_tag_data[$key]['kw_tag'] = str_replace($_org_word, $_chg_word, $value['kw_tag']);

    						$this->kw->update_keyword($get_tag_data[$key]);
    					}
    				}

    				// タグ(ルートドメイン情報)一括書き換え
    				$set_rd_data['rd_cl_seq']   = $_SESSION['c_memGrp'];
    				$set_rd_data['rd_tag']      = $input_post['old_gt_name'];
    				$set_rd_data['old_gt_name'] = $input_post['old_gt_name'];
    				$get_tag_data = $this->rd->get_rd_tag($set_rd_data);

    				if (!empty($get_tag_data))
    				{
    					foreach ($get_tag_data as $key => $value)
    					{
    						$_org_word = "[" . $input_post['old_gt_name'] . "]";
    						$_chg_word = "[" . $input_post['gt_name'] . "]";
    						$set_rd_data['rd_tag'] = str_replace($_org_word, $_chg_word, $value['rd_tag']);

    						$set_rd_data['rd_seq'] = $value['rd_seq'];

    						$this->rd->update_rd_tag($set_rd_data);
    					}
    				}

    				$this->smarty->assign('mess01', "<font color=blue>タグ名またはその内容が更新されました。</font>");

    			} else {

    				$this->smarty->assign('mess01', "<font color=red>ERROR::同一タグ名が既に存在します。</font>");

    			}

    		} elseif ($input_post['submit'] == '_delete') {

    			// DELETE
    			$set_gt_data['gt_cl_seq']   = $_SESSION['c_memGrp'];
    			$set_gt_data['old_gt_name'] = $input_post['old_gt_name'];
    			$this->gt->delete_group_tag($set_gt_data, $type=1);

    			// タグ(キーワード情報)一括削除
    			$set_kw_data['kw_cl_seq']   = $_SESSION['c_memGrp'];
    			$set_kw_data['kw_tag']      = $input_post['old_gt_name'];
    			$get_tag_data = $this->kw->get_kw_tag($set_kw_data);

    			if (!empty($get_tag_data))
    			{
    				// タグ名の場合は置き換え
    				foreach ($get_tag_data as $key => $value)
    				{
    					$_org_word = "[" . $input_post['old_gt_name'] . "]";
    					$_chg_word = "";
    					$get_tag_data[$key]['kw_tag'] = str_replace($_org_word, $_chg_word, $value['kw_tag']);

    					$this->kw->update_keyword($get_tag_data[$key]);
    				}
    			}

    			// タグ(ルートドメイン情報)一括削除
    			$set_rd_data['rd_cl_seq']   = $_SESSION['c_memGrp'];
    			$set_rd_data['rd_tag']      = $input_post['old_gt_name'];
    			$set_rd_data['old_gt_name'] = $input_post['old_gt_name'];
    			$get_tag_data = $this->rd->get_rd_tag($set_rd_data);

    			if (!empty($get_tag_data))
    			{
    				// タグ名の場合は置き換え
    				foreach ($get_tag_data as $key => $value)
    				{
    					$_org_word = "[" . $input_post['old_gt_name'] . "]";
    					$_chg_word = "";
    					$set_rd_data['rd_tag'] = str_replace($_org_word, $_chg_word, $value['rd_tag']);

    					$set_rd_data['rd_seq'] = $value['rd_seq'];

    					$this->rd->update_rd_tag($set_rd_data);
    				}
    			}

    			$this->smarty->assign('mess01', "<font color=blue>「" . $input_post['old_gt_name'] . "」タグ名が削除されました。</font>");

    		}else {

    		}
    	}

    	// 設定タグのセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], NULL, 1);
//     	$this->_group_set($_SESSION['c_memGrp'], $input_post['gt_name']);

    	$this->smarty->assign('gt_seq',   $input_post['gt_seq']);
    	$this->smarty->assign('gt_name',  $input_post['gt_name']);
    	$this->smarty->assign('disp01',   FALSE);
    	$this->smarty->assign('tmp_memo', $input_post['gt_memo']);
    	$this->smarty->assign('tmp_new_memo', NULL);

    	$this->view('taglist/chg.tpl');

    }

    // Pagination 設定
    private function _get_Pagination($countall, $tmp_per_page)
    {

    	$config['base_url']       = base_url() . '/taglist/search/';        // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
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

    // Pagination 設定：グループ内検索
    private function _get_Pagination01($countall, $tmp_per_page)
    {

    	$config['base_url']       = base_url() . '/taglist/detail_search/';   // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
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

    	// ステータスのセット
    	$this->config->load('config_status');
    	$opt_kw_status = $this->config->item('KEYWORD_KW_STATUS');

    	// 最大取得順位
    	$opt_kw_maxposition = $this->config->item('KEYWORD_KW_MAXPOSITION');

    	// 最大取得順位
    	$opt_kw_trytimes = $this->config->item('KEYWORD_KW_TRYTIMES');

    	$this->smarty->assign('options_kw_status',  $opt_kw_status);
    	$this->smarty->assign('options_kw_maxposition', $opt_kw_maxposition);
    	$this->smarty->assign('options_kw_trytimes', $opt_kw_trytimes);

    }

    // 検索項目 初期値セット
    private function _search_set()
    {

    	// ステータス 選択項目セット
    	$this->config->load('config_status');
    	$opt_kw_status = $this->config->item('KEYWORD_KW_STATUS');

    	// キーワードID 並び替え選択項目セット
    	$arropt_id = array (
    			''     => '-- 選択してください --',
    			'DESC' => '降順',
    			'ASC'  => '昇順',
    	);

    	$this->smarty->assign('options_kw_status', $opt_kw_status);
    	$this->smarty->assign('options_orderid',   $arropt_id);

    }

//     // 設定グループのセット
//     private function _group_set($cl_seq, $gt_name)
//     {

//     	// グループ情報取得
//     	$this->load->model('Group_tag', 'gt', TRUE);
//     	$group_list =$this->gt->get_gt_clseq($cl_seq, 0);

//     	$opt_group = "";
//     	foreach ($group_list as $key => $value)
//     	{
//     		if ($gt_name == $value['gt_name'])
//     		{
//     			$opt_group .= '<option selected="selected" value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
//     		} else {
//     			$opt_group .= '<option value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
//     		}
//     	}

//     	$this->smarty->assign('options_gt_group', $opt_group);

//     }

//     // 順位データ集計
//     private function _get_ranking_graph($kw_seq, $cnt_date)
//     {

//     	// 順位データ情報を取得 (31日分)
//     	$date = new DateTime();
//     	$_start_date = $date->format('Y-m-d');
//     	$_set_cnt_date = "- " . $cnt_date . " days";
//     	$_end_date   = $date->modify($_set_cnt_date)->format('Y-m-d');

//     	$get_rk_data = $this->rk->get_kw_seq($kw_seq, $_start_date, $_end_date);

//     	$_cnt_rk = 0;														// 順位データの配列カウンター
//     	$_x_data[$kw_seq] = "x";											// X軸データ（日付）用配列。"x"は接頭語として後で外す。
//     	$_y_data[$kw_seq] = "y";											// Y軸データ（順位）用配列。"y"は接頭語として後で外す。
//     	for ($cnt = $cnt_date; $cnt > 0; $cnt--)
//     	{

//     		$_getdate = $date->modify('+1 days')->format('Y-m-d');
//     		$_x_data[$kw_seq] .= ',' . $date->format('d');

//     		if ((isset($get_rk_data[$_cnt_rk])) && ($get_rk_data[$_cnt_rk]['rk_getdate'] == $_getdate))
//     		{

//     			// 順位が300位以内
//     			if ($get_rk_data[$_cnt_rk]['rk_position'] <= 300)
//     			{
//     				$_y_data[$kw_seq] .=  ',' . $get_rk_data[$_cnt_rk]['rk_position'];
//     			} else {
//     				$_y_data[$kw_seq] .=  ',' . "";
//     			}

//     			$_cnt_rk++;

//     		} else {
//     			$_y_data[$kw_seq] .=  ',';
//     		}
//     	}

//     	// グラフ用データ
//     	$_x_data[$kw_seq] = str_replace("x,", "", $_x_data[$kw_seq]);
//     	$_y_data[$kw_seq] = str_replace("y,", "", $_y_data[$kw_seq]);
//     	$this->smarty->assign('x_data[$kw_seq]', $_x_data[$kw_seq]);
//     	$this->smarty->assign('y_data[$kw_seq]', $_y_data[$kw_seq]);

//     	$_tbl_x_data[$kw_seq] = explode(",", $_x_data[$kw_seq]);
//     	$_tbl_y_data[$kw_seq] = explode(",", $_y_data[$kw_seq]);

//     	// テーブル用データ
//     	$this->smarty->assign('tbl_x_data' . $kw_seq, $_tbl_x_data[$kw_seq]);
//     	$this->smarty->assign('tbl_y_data' . $kw_seq, $_tbl_y_data[$kw_seq]);

//     }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック : グループ更新
    private function _set_validation01()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'gt_memo',
    					'label'   => 'メモ',
    					'rules'   => 'trim|max_length[1000]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

    // フォーム・バリデーションチェック : グループ追加
    private function _set_validation02()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'new_name',
    					'label'   => 'タグ名',
    					'rules'   => 'trim|required|max_length[50]'
    			),
    			array(
    					'field'   => 'new_memo',
    					'label'   => 'メモ',
    					'rules'   => 'trim|max_length[1000]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}