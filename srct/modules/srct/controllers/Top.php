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

		// セッションデータをクリア
		$this->load->library('lib_auth');
		$this->lib_auth->delete_session('client');

		// バリデーション・チェック
		$this->_set_validation();

		// フリーキーワード検索セット
		$tmp_inputpost = array(
								'free_keyword' => '',
		);

 		/*
 		 * ここではすべてのデータを初期化する
 		 */
		// セッションをフラッシュデータとして保存
		$data = array(
						'c_free_keyword' => '',						// フリーキーワード入力文字
						'c_free_rd'      => '',						// 左サイド:ルートドメイン一覧から対象ルートドメイン名選択
						'c_free_group'   => '',						// 左サイド:グループ一覧から対象グループ名選択
						'c_free_sort_id' => '',						// キーワード一覧:ヘッダ項目クリック=>item
						'c_free_sort'    => '',						// キーワード一覧:ヘッダ項目クリック=>ASC or DESC
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
						'c_tabs'         => $tmp_tabs,				// 左サイド:「ルートドメイン」「グループ」選択有無
						'c_offset'       => $tmp_offset,
						'c_pages'        => "",
		);
		$this->session->set_userdata($data);


		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		// 1ページ当たりの表示件数
		$this->config->load('config_comm');
		$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

		if ($tmp_tabs == "rd")
		{

			// *** ルートドメイン一覧の作成
			list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_rootdomainlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_tmp_cl);

		} else {

			// *** グループ一覧の作成
			list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_grouplist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_tmp_cl);

		}

		$this->smarty->assign('list_kw',      $kw_list);
		$this->smarty->assign('list_cnt',     ($kw_countall / 3));					// 三人トリオ
		$this->smarty->assign('list_catalog', $catalog_list);

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

		$this->smarty->assign('url_match',  3);									// URLマッチタイプデフォルト
		$this->smarty->assign('options_kw', NULL);
		$this->smarty->assign('tabs',       $tmp_tabs);							// 左サイド：「ルートドメイン」or「グループ」選択有無
		$this->smarty->assign('tmp_item',   NULL);
		$this->smarty->assign('tmp_sort',   NULL);

		$this->smarty->assign('per_page',   $tmp_per_page);

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
							//'c_free_rd'      => '',
							//'c_free_group'   => '',
			);
			$this->session->set_userdata($data);

			/*
			 * ここでは、左サイド(RDとGP)での選択を継承する
			 */
			$tmp_inputpost = array(
							'free_keyword' => $this->input->post('free_keyword'),
							'free_sort_id' => $_SESSION['c_free_sort_id'],
							'free_sort'    => $_SESSION['c_free_sort'],
							'free_rd'      => $_SESSION['c_free_rd'],
							'free_group'   => $_SESSION['c_free_group'],
			);

			unset($tmp_inputpost["submit"]);

			// セッションからフラッシュデータ読み込み
			$tmp_tabs = $_SESSION['c_tabs'];

		} else {

			if (!isset($_SESSION['c_free_keyword']))
			{

				// セッションからフラッシュデータ読み込み
				$tmp_inputpost = array(
							'free_keyword'   => $_SESSION['c_free_keyword'],
				);

			} else {
				redirect('/top/');
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

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if ($tmp_tabs == "rd")
		{

			// *** ルートドメイン一覧の作成
			list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_rootdomainlist($tmp_inputpost, $tmp_per_page=0, $tmp_offset, $_tmp_cl);

		} else {

			// *** グループ一覧の作成
			list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_grouplist($tmp_inputpost, $tmp_per_page=0, $tmp_offset, $_tmp_cl);

		}

		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));					// 三人トリオ
		$this->smarty->assign('list_catalog', $catalog_list);

		// *** グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		// 初期値セット
		$this->_item_set();

		// ロケーションセット
		$this->load->library('lib_keyword');
		$this->lib_keyword->location_set();

		$this->smarty->assign('url_match',  3);									// URLマッチタイプデフォルト
		$this->smarty->assign('options_kw', NULL);
		$this->smarty->assign('tabs',       $tmp_tabs);
		$this->smarty->assign('tmp_item',   NULL);
		$this->smarty->assign('tmp_sort',   NULL);

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
