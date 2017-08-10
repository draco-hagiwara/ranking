<?php

class Top extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->library('lib_auth');
		$this->lib_auth->check_session();

		$this->smarty->assign('err_date', FALSE);
		$this->smarty->assign('mess',     FALSE);

	}

	// TOP
	public function index()
	{

		// バリデーション・チェック
		$this->_set_validation();

		// フリーキーワード検索セット
		$tmp_inputpost = array(
								'free_keyword'      => '',
		);

// 		// セッションをフラッシュデータとして保存
// 		$tmp_offset = 0;
		$data = array(
						'c_free_keyword' => '',
						'c_free_rd'      => '',
						'c_free_group'   => '',
		);
		$this->session->set_userdata($data);

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);




		// URIセグメントの取得
		$segments = $this->uri->segment_array();
		if (isset($segments[3]))
		{
			$tmp_tabs = $segments[3];

		} else {
			$tmp_tabs = "rd";
		}

		// セッションをフラッシュデータとして保存
		$tmp_offset = 0;
		$data = array(
				'c_free_keyword' => '',
				'c_tabs'         => $tmp_tabs,
				'c_offset'       => $tmp_offset,
		);
		$this->session->set_userdata($data);


		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if ($tmp_tabs == "rd")
		{

			// *** ルートドメイン一覧の作成
			list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($tmp_inputpost, $tmp_per_page=0, $tmp_offset, $_tmp_cl);

		} else {

			// *** グループ一覧の作成
			list($kw_list, $group_countall, $kw_countall) = $this->kw->get_kw_grouplist($tmp_inputpost, $tmp_per_page=0, $tmp_offset, $_tmp_cl);



// 			$this->load->model('Group_tag', 'gt', TRUE);


// 			$_set_gt_data['gt_name']   = NULL;
// 			$_set_gt_data["orderid"]   = 'DESC';
// 			$_set_gt_data['gt_type']   = 0;
// 			$_set_gt_data['gt_cl_seq'] = $_SESSION['c_memGrp'];

// 			list($kw_list, $gt_countall, $kw_countall) = $this->gt->get_gtlist($_set_gt_data, $tmp_per_page=0, $tmp_offset);

		}




		//print($kw_countall);


		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));					// 三人トリオ

		// *** グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);








		// 初期値セット
		$this->_item_set();

		// ロケーションセット
		$this->load->library('lib_keyword');
		$this->lib_keyword->location_set();

		// 設定グループのセット
		$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], "", 0);

		// 設定タグのセット
		$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], "", 1);

		$this->smarty->assign('url_match',  3);									// URLマッチタイプデフォルト
		$this->smarty->assign('options_kw', NULL);
		$this->smarty->assign('tabs',       $tmp_tabs);							// 左サイド：「ルートドメイン」or「グループ」選択有無

		$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		$this->view('top/index.tpl');

	}

	// 一覧表示
	public function search()
	{

		$input_post = $this->input->post();

		// 画面遷移のチェック
		if ((isset($input_post['submit'])) && ($input_post['submit'] == '_submit'))
		{
			// セッションをフラッシュデータとして保存
			$data = array(
					'c_free_keyword' => $this->input->post('free_keyword'),
					'c_free_rd'      => '',
					'c_free_group'   => '',
			);
			$this->session->set_userdata($data);

			$tmp_inputpost = $this->input->post();
			unset($tmp_inputpost["submit"]);

			// セッションからフラッシュデータ読み込み
			$tmp_tabs = $_SESSION['c_tabs'];

		} else {

			if (!isset($_SESSION['c_free_keyword']))
			{

				// セッションからフラッシュデータ読み込み
				$tmp_inputpost = array(
						'free_keyword'      => $_SESSION['c_free_keyword'],
				);

			} else {
				redirect('/searchrank/');
			}
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
		$tmp_per_page = 0;
		//$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);



		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if ($tmp_tabs == "rd")
		{

			// *** ルートドメイン一覧の作成
			list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($tmp_inputpost, $tmp_per_page=0, $tmp_offset, $_tmp_cl);

		} else {

			// *** グループ一覧の作成
			list($kw_list, $group_countall, $kw_countall) = $this->kw->get_kw_grouplist($tmp_inputpost, $tmp_per_page=0, $tmp_offset, $_tmp_cl);

		}




		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));					// 三人トリオ


		// 		// *** ルートドメイン一覧の作成
		// 		$_tmp_ac = $_SESSION['c_memSeq'];
		// 		$_tmp_cl = $_SESSION['c_memGrp'];
		// 		list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($tmp_inputpost, $tmp_per_page=0, $tmp_offset, $_tmp_cl);

		// 		$this->smarty->assign('list_kw',  $kw_list);
		// 		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// *** グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);


		// *** グループ一覧の作成





		// 初期値セット
		$this->_item_set();

		// ロケーションセット
		$this->load->library('lib_keyword');
		$this->lib_keyword->location_set();

		// 		// 設定グループのセット
		// 		$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], "", 0);

		// 		// 設定タグのセット
		// 		$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], "", 1);

		$this->smarty->assign('url_match',  3);									// URLマッチタイプデフォルト
		$this->smarty->assign('options_kw', NULL);
		$this->smarty->assign('tabs',       $tmp_tabs);

		$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		$this->view('top/index.tpl');

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


	// フォーム・バリデーションチェック
	private function _set_validation()
	{

		$rule_set = array(
		);

		$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

	}

}
