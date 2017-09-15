<?php

class Topdetail extends MY_Controller
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

	}

	// レポート作成
	public function report()
	{

		$input_post = $this->input->post();

		if (isset($input_post['kw_seq']))
		{

			$report_kwseq = $input_post['kw_seq'];
			$_term = "1";																// デフォルト(今月分)

			// kw_seq & 表示期間をフラッシュデータとして保存
			$data = array(
							'c_report_kwseq' => $report_kwseq,
							'c_report_term'  => $_term,
			);
			$this->session->set_userdata($data);

		} else {

			// kw_seq を呼び出し
			$report_kwseq = $_SESSION['c_report_kwseq'];

			// URIセグメントの取得
			$segments = $this->uri->segment_array();
			if (isset($segments[3]) && isset($report_kwseq))
			{

				// グラフ＆レポート表示期間のセット
				$_term = $segments[3];

				// 表示期間をフラッシュデータとして保存
				$data = array(
							'c_report_term'  => $_term,
				);
				$this->session->set_userdata($data);

			} else {
				show_404();
			}
		}

		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Ranking',   'rk', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);
		$this->load->library('lib_searchrank_data');

		// バリデーション設定
		$this->_set_validation();

		$_tmp_kw_seq = trim($report_kwseq, "[");
		$_tmp_kw_seq = trim($_tmp_kw_seq,  "]");

		$_tmp_kw_seq = explode(",", $_tmp_kw_seq);

		$kw_seq_list = array();
		foreach ($_tmp_kw_seq as $key => $val)
		{
			$kw_seq_list[$key]['kw_seq'] = $val;
		}

		// *** グラフ作成
		$this->lib_searchrank_data->create_rank_graph($kw_seq_list, $_term);

		// 一つのseqから仲間3人を見つける！
		$cnt = 0;
		$_arr_kw_seq = array();
		foreach ($_tmp_kw_seq as $key => $value)
		{
			$get_kw_data = $this->kw->get_kw_seq($value);

			$this->smarty->assign('info'. $cnt, $get_kw_data[0]);

			$cnt++;
		}

		// 初期値セット
		$this->_item_set();

		// ロケーションセット
		$this->load->library('lib_keyword');
		$this->lib_keyword->location_set();

		$this->smarty->assign('term',  $_term);
		$this->smarty->assign('list_kw',  $_tmp_kw_seq);

		$this->view('topdetail/report.tpl');

	}

	// ロケーション（地域）一覧
	public function location_list()
	{

		$this->load->model('Location', 'lo', TRUE);

		$get_lo_list = $this->lo->get_location_list();

		$this->smarty->assign('list', $get_lo_list);
		$this->view('topdetail/locationlist.tpl');

	}

	// ajax用ダミーページ : ルートドメイン選択からのキーワード一覧表示
	public function index_aj_rd()
	{

		$input_post = $this->input->post();

		// バリデーション・チェック
		$this->_set_validation();

		// フリーキーワード検索セット
		$tmp_inputpost = array(
				'free_keyword'   => '',
		);

		// セッションをフラッシュデータとして保存
		//$tmp_offset = 0;
		$data = array(
						'c_free_keyword' => '',
						'c_free_rd'      => $input_post['kw_rootdomain'],
						'c_free_group'   => '',
						//'c_offset'       => $tmp_offset,
						'c_back_set'     => "searchrank",
		);
		$this->session->set_userdata($data);

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		$tmp_inputpost['free_keyword'] = $input_post['kw_rootdomain'];

		// 1ページ当たりの表示件数
		$tmp_per_page = 0;

		// ページング
		$tmp_offset = 0;

		list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_rootdomainlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_tmp_cl);

		$this->smarty->assign('list_kw',      $kw_list);
		$this->smarty->assign('list_cnt',     ($kw_countall / 3));
		$this->smarty->assign('list_catalog', $catalog_list);

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		if (empty($_SESSION['c_tabs']))
		{
			$tmp_tabs = "rd";
		} else {
			$tmp_tabs = $_SESSION['c_tabs'];
		}
		$this->smarty->assign('tabs',     $tmp_tabs);
		$this->smarty->assign('tmp_item', NULL);
		$this->smarty->assign('tmp_sort', NULL);

		$this->smarty->assign('per_page',   $tmp_per_page);

		// ajax用ダミーページ
		if ($input_post['area'] == "left")
		{
			$this->view('topdetail/index_l_table.tpl');
		} else {
			$this->view('topdetail/index_r_table.tpl');
		}

	}

	// ajax用ダミーページ : グループ選択からのキーワード一覧表示
	public function index_aj_group()
	{

		$input_post = $this->input->post();

		// バリデーション・チェック
		$this->_set_validation();

		// フリーキーワード検索セット
		$tmp_inputpost = array(
				'free_keyword'   => '',
		);

		// セッションをフラッシュデータとして保存
		//$tmp_offset = 0;
		$data = array(
						'c_free_keyword' => '',
						'c_free_rd'      => '',
						'c_free_group'   => $input_post['kw_group'],
						'c_back_set'     => "searchrank",
		);
		$this->session->set_userdata($data);

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		$tmp_inputpost['free_group'] = $input_post['kw_group'];

		// 1ページ当たりの表示件数
		$tmp_per_page = 0;

		// ページング
		$tmp_offset = 0;

		list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_grouplist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_tmp_cl);

		$this->smarty->assign('list_kw',      $kw_list);
		$this->smarty->assign('list_cnt',     ($kw_countall / 3));
		$this->smarty->assign('list_catalog', $catalog_list);

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		if (empty($_SESSION['c_tabs']))
		{
			$tmp_tabs = "rd";
		} else {
			$tmp_tabs = $_SESSION['c_tabs'];
		}
		$this->smarty->assign('tabs',     $tmp_tabs);

		$this->smarty->assign('tmp_item',   NULL);
		$this->smarty->assign('tmp_sort',   NULL);

		$this->smarty->assign('per_page',   $tmp_per_page);

		// ajax用ダミーページ
		if ($input_post['area'] == "left")
		{
			$this->view('topdetail/index_l_table.tpl');
		} else {
			$this->view('topdetail/index_r_table.tpl');
		}

	}

	// ajax用ダミーページ : キーワード一覧表示を順次読み込む
	public function index_aj_bottom()
	{

		$input_post = $this->input->post();

		// バリデーション・チェック
		$this->_set_validation();

		// フリーキーワード検索セット
		$tmp_inputpost = array(
								'free_keyword' => $_SESSION['c_free_keyword'],
								'free_sort_id' => $_SESSION['c_free_sort_id'],
								'free_sort'    => $_SESSION['c_free_sort'],
								'free_rd'      => $_SESSION['c_free_rd'],
								'free_group'   => $_SESSION['c_free_group'],
		);

		// 1ページ当たりの表示件数
		$this->config->load('config_comm');
		$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

		// ページングデータとして保存
		$tmp_offset = $input_post['offset'] * $tmp_per_page;
		$data = array(
								'c_offset'     => $tmp_offset,
								'c_pages'      => $input_post['offset'],
		);
		$this->session->set_userdata($data);

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		// 個別にて「ルートドメイン」or「グループ」を選択した場合は、順次読込を行わない
		if (($_SESSION['c_free_rd'] == "") && ($_SESSION['c_free_group'] == ""))
		{

			if ($_SESSION['c_tabs'] == "rd")
			{
				//$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
				//$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
				//$arr_search_item['free_sort_id'] = $_SESSION['c_free_sort_id'];
				//$arr_search_item['free_sort']    = $_SESSION['c_free_sort'];

				list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_rootdomainlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_tmp_cl);
			} else {
				//$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
				//$arr_search_item['free_group']   = $_SESSION['c_free_group'];
				//$arr_search_item['free_sort_id'] = $_SESSION['c_free_sort_id'];
				//$arr_search_item['free_sort']    = $_SESSION['c_free_sort'];

				list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_grouplist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_tmp_cl);
			}

			// *** グラフ作成（全期間表示=0）
			$this->load->library('lib_searchrank_data');
			$rank_comp_cnt = $this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

			$jsonArray1 = array();
			$jsonArray2 = array();
			foreach ($kw_list as $key => $value)
			{

				$arr_location = explode(",", $value['kw_location_name']);

				$jsonArray2 = array(
									'offset'            => $tmp_offset,
									'kw_seq'            => $value['kw_seq'],
									'kw_url'            => $value['kw_url'],
									'kw_domain'         => $value['kw_domain'],
									'kw_keyword'        => $value['kw_keyword'],
									'kw_matchtype'      => $value['kw_matchtype'],
									'kw_location_name'  => $value['kw_location_name'],
									'kw_location_short' => $arr_location[0],					// ロケーション名の ,(最左端)を求める
									'kw_group'          => $value['kw_group'],
									'wt_seq'            => $value['wt_seq'],

									'comp_today00'      => $rank_comp_cnt[$key][$key][0]['comp_today'],
									'comp_yesterday00'  => $rank_comp_cnt[$key][$key][0]['comp_yesterday'],
									'comp_week00'       => $rank_comp_cnt[$key][$key][0]['comp_week'],
									'comp_month00'      => $rank_comp_cnt[$key][$key][0]['comp_month'],
									'comp_today01'      => $rank_comp_cnt[$key][$key][1]['comp_today'],
									'comp_yesterday01'  => $rank_comp_cnt[$key][$key][1]['comp_yesterday'],
									'comp_today10'      => $rank_comp_cnt[$key][$key][2]['comp_today'],
									'comp_yesterday10'  => $rank_comp_cnt[$key][$key][2]['comp_yesterday'],
				);

				array_push($jsonArray1, $jsonArray2);
			}

		} else {

			// 空データを送信
			$jsonArray1 = array();

		}

		$_tmp_json = sprintf("callback(%s)",json_encode($jsonArray1));

		$this->smarty->assign('mess', $_tmp_json);

		$this->view('topdetail/index_aj_bottom.tpl');

	}

	// ajax用ダミーページ : キーワード一覧でのグラフ表示
	public function index_aj_jqPlot()
	{

		$input_post = $this->input->post();

		$_arr_kw_seq[0]['kw_seq'] = $input_post['kw_seq'];

		// *** グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($_arr_kw_seq, $_term=0);

		$this->smarty->assign('cnt',  $input_post['cnt']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_jqPlot.tpl');

	}

	// ajax用ダミーページ : 「KWヘッダ部」の昇順&降順 切り替え操作（ソート共通処理）
	public function index_aj_kwsort()
	{

		$input_post = $this->input->post();

		// セッションをフラッシュデータとして保存
		$data = array(
				'c_free_sort_id' => $input_post['tmp_item'],
				'c_free_sort'    => $input_post['tmp_sort'],
		);
		$this->session->set_userdata($data);

		// バリデーション・チェック
		$this->_set_validation();

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 左サイドのタブ選択
		if (empty($_SESSION['c_tabs']))
		{
			$tmp_tabs = "rd";
		} else {
			$tmp_tabs = $_SESSION['c_tabs'];
		}

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		// 個別にて「ルートドメイン」or「グループ」を選択した場合は、順次読込を行わない
		if (($_SESSION['c_free_rd'] == "") && ($_SESSION['c_free_group'] == ""))
		{
			// 1ページ当たりの表示件数
			$this->config->load('config_comm');
			$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

			// ページング
			$tmp_offset = 0;

		} else {
			$tmp_per_page = 0;
			$tmp_offset = 0;
		}

		if ((empty($_SESSION['c_free_group'])) && ($_SESSION['c_tabs'] == "rd"))
		{
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_rootdomainlist($arr_search_item, $tmp_per_page, $tmp_offset, $_tmp_cl);

		} else {
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_group']   = $_SESSION['c_free_group'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_grouplist($arr_search_item, $tmp_per_page, $tmp_offset, $_tmp_cl);

		}

		$this->smarty->assign('list_kw',      $kw_list);
		$this->smarty->assign('list_cnt',     ($kw_countall / 3));
		$this->smarty->assign('list_catalog', $catalog_list);

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		$this->smarty->assign('tmp_item', $input_post['tmp_item']);
		$this->smarty->assign('tmp_sort', $input_post['tmp_sort']);
		$this->smarty->assign('tabs',     $tmp_tabs);

		$this->smarty->assign('per_page', $tmp_per_page);

		// ajax用ダミーページ
		if ($input_post['area'] == "left")
		{
			$this->view('topdetail/index_l_table.tpl');
		} else {
			$this->view('topdetail/index_r_table.tpl');
		}

	}

	// ajax用ダミーページ : 「ランキングヘッダ部」の昇順&降順 切り替え操作（ソート共通処理）
	public function index_aj_ranksort()
	{

		$input_post = $this->input->post();

		// セッションをフラッシュデータとして保存
		$data = array(
				'c_free_sort_id' => $input_post['tmp_item'],
				'c_free_sort'    => $input_post['tmp_sort'],
		);
		$this->session->set_userdata($data);

		// バリデーション・チェック
		$this->_set_validation();

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 左サイドのタブ選択
		if (empty($_SESSION['c_tabs']))
		{
			$tmp_tabs = "rd";
		} else {
			$tmp_tabs = $_SESSION['c_tabs'];
		}

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		// 1ページ当たりの表示件数
		$this->config->load('config_comm');
		$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

		// ページング
		$tmp_offset = 0;
		if (isset($_SESSION['c_pages']))
		{
			$tmp_per_page = $tmp_per_page * ($_SESSION['c_pages'] + 1);
		} else {
			$tmp_per_page = $tmp_per_page;
		}

		if ((empty($_SESSION['c_free_group'])) && ($_SESSION['c_tabs'] == "rd"))
		{
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_rootdomainlist($arr_search_item, $tmp_per_page, $tmp_offset, $_tmp_cl);

		} else {
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_group']   = $_SESSION['c_free_group'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_grouplist($arr_search_item, $tmp_per_page, $tmp_offset, $_tmp_cl);
		}

		$this->smarty->assign('list_kw',      $kw_list);
		$this->smarty->assign('list_cnt',     ($kw_countall / 3));
		$this->smarty->assign('list_catalog', $catalog_list);

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		$this->smarty->assign('tmp_item', $input_post['tmp_item']);
		$this->smarty->assign('tmp_sort', $input_post['tmp_sort']);
		$this->smarty->assign('tabs',     $tmp_tabs);

		$this->smarty->assign('per_page',   $tmp_per_page);

		// ajax用ダミーページ
		if ($input_post['area'] == "left")
		{
			$this->view('topdetail/index_l_table.tpl');
		} else {
			$this->view('topdetail/index_r_table.tpl');
		}

	}

	// ajax用ダミーページ : TOP画面を更新（追加/編集/削除での共通処理）
	public function index_aj_area()
	{

		$input_post = $this->input->post();

		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		$_tmp_ac  = $_SESSION['c_memSeq'];
		$_tmp_cl  = $_SESSION['c_memGrp'];

		// 左サイドのタブ選択
		if (empty($_SESSION['c_tabs']))
		{
			$tmp_tabs = "rd";
		} else {
			$tmp_tabs = $_SESSION['c_tabs'];
		}

		// フリーキーワード検索有無＆セット
		if (empty($_SESSION['c_free_keyword']))
		{
			if ($tmp_tabs == "rd")
			{
				$tmp_free_keyword = $_SESSION['c_free_rd'];
			} else {
				$tmp_free_keyword = $_SESSION['c_free_group'];
			}
		} else {
			$tmp_free_keyword = $_SESSION['c_free_keyword'];
		}

		// ソート有無＆セット
		if (!empty($_SESSION['c_free_sort_id']))
		{
			$tmp_free_sort_id = $_SESSION['c_free_sort_id'];
			$tmp_free_sort    = $_SESSION['c_free_sort'];
		} else {
			$tmp_free_sort_id = NULL;
			$tmp_free_sort    = NULL;
		}

		// キーワード情報の取得
		if ($tmp_tabs == "rd")
		{
			$tmp_inputpost = array(
					'free_rd'      => $_SESSION['c_free_rd'],
					'free_keyword' => $tmp_free_keyword,
					'free_sort_id' => $tmp_free_sort_id,
					'free_sort'    => $tmp_free_sort,
			);

			// *** ルートドメイン一覧の作成
			list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_rootdomainlist($tmp_inputpost, $tmp_per_page=0, $tmp_offset=0, $_tmp_cl);
		} else {
			$tmp_inputpost = array(
					'free_group' => $_SESSION['c_free_group'],
					'free_keyword' => $tmp_free_keyword,
					'free_sort_id' => $tmp_free_sort_id,
					'free_sort'    => $tmp_free_sort,
			);

			// *** グループ一覧の作成
			list($kw_list, $catalog_list, $kw_countall) = $this->kw->get_kw_grouplist($tmp_inputpost, $tmp_per_page=0, $tmp_offset=0, $_tmp_cl);
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
		$this->smarty->assign('tabs',       $tmp_tabs);

		$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		// バリデーション設定
		$this->_set_validation();

		// ajax用ダミーページ
		if ($input_post['area'] == "left")
		{
			$this->view('topdetail/index_l_table.tpl');
		} else {
			if (!empty($_SESSION['c_free_sort_id']))
			{
				$this->smarty->assign('tmp_item', $_SESSION['c_free_sort_id']);
				$this->smarty->assign('tmp_sort', $_SESSION['c_free_sort']);
			} else {
				$this->smarty->assign('tmp_item', NULL);
				$this->smarty->assign('tmp_sort', NULL);
			}

			$this->view('topdetail/index_r_table.tpl');
		}
	}

	// ajax用ダミーページ : キーワード編集からの編集画面表示
	public function index_aj_kwinsert()
	{

		// バリデーション設定
		$this->_set_validation();

		// 初期値セット
		$this->_item_set();

		// ロケーションセット
		$this->load->library('lib_keyword');
		$this->lib_keyword->location_set();

		// 設定グループのセット
		$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], "", 0);

		$this->smarty->assign('url_match',  3);									// URLマッチタイプデフォルト
		$this->smarty->assign('options_kw', NULL);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_kwinsert.tpl');

	}

	// ajax用ダミーページ : index_aj_kwupdate --> キーワード編集チェック
	public function index_aj_kwinsert_chk()
	{

		$input_post = $this->input->post();

		$this->load->library('lib_validator');

		$_tmp_mess = NULL;
		$jsonArray = array();

		// 部分チェック：対象URL
		if ((isset($input_post['key_item'])) && ($input_post['key_item'] == "kw_url"))
		{
			if (empty($input_post['name']))
			{
				$_tmp_mess = "対象URL の文字数エラー";
				$jsonArray = array(
								array(
										'title'   => '対象URL',
										'message' => '対象URL は必須入力項目です。',
								),
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['name'], 1, 510) == FALSE)
				{
					$_tmp_mess = "対象URL の文字数エラー";
					$jsonArray = array(
									array(
											'title'   => '対象URL',
											'message' => '対象URL の文字数エラー。',
									),
					);
					}

				// URLチェック
				if ($this->lib_validator->checkUri($input_post['name']) == FALSE)
				{
					$_tmp_mess .= "対象URL の形式エラー";
					$jsonArray = array(
									array(
											'title'   => '対象URL',
											'message' => '対象URL の形式エラー。',
									),
					);
				}
			}
		}

		// 全体チェック：検索キーワード
		if (isset($input_post['kw_keyword']))
		{
			if (empty($input_post['kw_keyword']))
			{
				$_tmp_mess = "検索キーワード の文字数エラー";
				array_push($jsonArray,
								array(
										'title'   => '検索キーワード',
										'message' => '検索キーワード は必須入力項目です。',
								)
				);
			} else {
				foreach ($input_post['kw_keyword'] as $key => $value)
				{
					// 文字数チェック
					if ($this->lib_validator->checkLength($value, 1, 50) == FALSE)
					{
						$_tmp_mess = "検索キーワード の文字数エラー";
						array_push($jsonArray,
										array(
												'title'   => '検索キーワード',
												'message' => '検索キーワード の文字数エラー。',
										)
						);
					}
				}
			}
		}

		// 全体チェック：対象URL
		if (isset($input_post['kw_url']))
		{
			if (empty($input_post['kw_url']))
			{
				$_tmp_mess = "対象URL の文字数エラー";
				array_push($jsonArray,
								array(
										'title'   => '対象URL',
										'message' => '対象URL は必須入力項目です。',
								)
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['kw_url'], 1, 510) == FALSE)
				{
					$_tmp_mess = "対象URL の文字数エラー";
					array_push($jsonArray,
									array(
											'title'   => '対象URL',
											'message' => '対象URL の文字数エラー。',
									)
					);
				}

				// URLチェック
				if ($this->lib_validator->checkUri($input_post['kw_url']) == FALSE)
				{
					$_tmp_mess .= "対象URL の形式エラー";
					array_push($jsonArray,
									array(
											'title'   => '対象URL',
											'message' => '対象URL の形式エラー。',
									)
					);
				}
			}
		}

		// 全体チェック：ロケーション指定
		if (isset($input_post['kw_location']))
		{
			if (empty($input_post['kw_location']))
			{
				$_tmp_mess = "ロケーション指定 の文字数エラー";
				array_push($jsonArray,
								array(
										'title'   => 'ロケーション指定',
										'message' => 'ロケーション指定 は必須入力項目です。',
								)
				);
			} else {
				foreach ($input_post['kw_location'] as $key => $value)
				{

					// 文字数チェック
					if ($this->lib_validator->checkLength($value, 1, 20) == FALSE)
					{
						$_tmp_mess = "ロケーション指定 の文字数エラー";
						array_push($jsonArray,
										array(
												'title'   => 'ロケーション指定 ',
												'message' => 'ロケーション指定 の文字数エラー。',
										)
						);
					}
				}
			}
		}

		// エラーチェック
		/*
		 *
		 * ここで入力エラーが無かった場合、キーワード追加処理を行う
		 *
		 */
		if (isset($input_post['kw_keyword']) && ($_tmp_mess == NULL))
		{
			$this->load->model('Location',   'lc', TRUE);
			$this->load->model('Keyword',    'kw', TRUE);
			$this->load->model('Group_tag',  'gt', TRUE);
			$this->load->library('lib_keyword');
			$this->load->library('lib_rootdomain');

			$set_data_kw = array();

			$set_data_kw['kw_status']    = 1;
			$set_data_kw['kw_matchtype'] = $input_post['kw_matchtype'];
			$set_data_kw['kw_cl_seq']    = $_SESSION['c_memGrp'];
			$set_data_kw['kw_ac_seq']    = $_SESSION['c_memSeq'];

			// 対象URL情報の設定
			preg_match_all("/\//", $input_post['kw_url'], $cnt_slash) ;										// 対象URL + 補正
			if (count($cnt_slash[0]) == 2)
			{
				$_tmp_url = $input_post['kw_url'] . "/";
			} else {
				$_tmp_url = $input_post['kw_url'];
			}
			$set_data_kw['kw_url'] = $_tmp_url;

			$set_data_kw['kw_domain'] = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $_tmp_url);		// ドメイン

			$_rootdomain = $this->lib_rootdomain->get_rootdomain($_tmp_url);
			$set_data_kw['kw_rootdomain'] = $_rootdomain['rootdomain'];										// ルートドメイン

			// グループ入力情報をセット
			if (!empty($input_post['kw_group']))
			{
				$set_data_kw['kw_group']   = $input_post['kw_group'][0];
			} else {
				$set_data_kw['kw_group']   = "";
			}

			// キーワード作成
			$this->lib_keyword->create_kw_data($input_post, $set_data_kw);

			// ルートドメイン数のカウント＆更新
			$this->lib_rootdomain->get_rootdomain_chg($set_data_kw['kw_cl_seq'], $set_data_kw['kw_rootdomain']);

			/*
			 * ここは変えた方がいいかも？
			 * ロジック？ or 仕様？
			 */
			// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
			if (!empty($input_post['kw_group']))
			{
				$get_gt_name = $this->gt->get_gt_name($input_post['kw_group'][0], $set_data_kw['kw_cl_seq'], 0);

				if (count($get_gt_name) == 0)
				{
					$set_gt_data['gt_name']   = $input_post['kw_group'][0];
					$set_gt_data['gt_cl_seq'] = $set_data_kw['kw_cl_seq'];
					$set_gt_data['gt_type']   = 0;

					// INSERT
					$this->gt->insert_group_tag($set_gt_data);
				}

				// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
				$this->lib_keyword->update_group_info_all($set_data_kw['kw_cl_seq'], 0);
			}

			if ($_tmp_mess == NULL)
			{
				$this->smarty->assign('mess', TRUE);
				$jsonArray = array(
								array(
										'title'   => 'success_insert',
										'message' => '',
							),
				);

			} else {
				$this->smarty->assign('mess', $_tmp_mess);
			}

		} else {
			$this->smarty->assign('mess', $_tmp_mess);
		}

		$_tmp_json = sprintf("callback(%s)",json_encode($jsonArray));
		$this->smarty->assign('mess', $_tmp_json);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_kwinsert_chk.tpl');

	}

	// ajax用ダミーページ : キーワード編集からの編集画面表示
	public function index_aj_kwupdate()
	{

		$input_post = $this->input->post();

		// バリデーション設定
		$this->_set_validation();

		// キーワード情報取得
		$this->load->model('Keyword', 'kw', TRUE);

		$arr_kwseq = "";												// 次画面（編集チェック）にkw_seq（配列）を渡す
		$_tmp_keyword = array();
		foreach ($input_post['kw_seq'] as $key => $value)
		{
			$get_kw_data[$key] = $this->kw->get_kw_seq($value);
			$arr_kwseq .= $value . ",";

			// 対象キーワード名を保存（複数対応）
			$_tmp_keyword[] = $get_kw_data[$key][0]['kw_keyword'] . "（" . $get_kw_data[$key][0]['kw_url'] . "）";
		}
		$arr_kwseq = "[" . rtrim($arr_kwseq, ",") . "]";

		// 初期値セット
		$this->_item_set();

		// ロケーションセット
		$this->load->library('lib_keyword');
		$this->lib_keyword->location_set();

		// 設定グループのセット
		$this->load->library('lib_keyword');
		$this->lib_keyword->grouptag_set($get_kw_data[0][0]['kw_cl_seq'], $get_kw_data[0][0]['kw_group'], 0);

		// 設定タグのセット
		$this->lib_keyword->grouptag_set($get_kw_data[0][0]['kw_cl_seq'], $get_kw_data[0][0]['kw_tag'], 1);

		$this->smarty->assign('arr_kw_seq',  $arr_kwseq);
		$this->smarty->assign('arr_keyword', $_tmp_keyword);
		$this->smarty->assign('info',        $get_kw_data[0][0]);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_kwupdate.tpl');

	}

	// ajax用ダミーページ : index_aj_kwupdate --> キーワード編集チェック
	public function index_aj_kwupdate_chk()
	{

		$input_post = $this->input->post();

		$this->load->library('lib_validator');

		$_tmp_mess = NULL;
		$jsonArray = array();

		// 部分チェック：対象URL
		if ((isset($input_post['key_item'])) && ($input_post['key_item'] == "kw_url"))
		{
			if (empty($input_post['name']))
			{
				$_tmp_mess = "対象URL の文字数エラー";
				$jsonArray = array(
						array(
								'title'   => '対象URL',
								'message' => '対象URL は必須入力項目です。',
						),
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['name'], 1, 510) == FALSE)
				{
					$_tmp_mess = "対象URL の文字数エラー";
					$jsonArray = array(
							array(
									'title'   => '対象URL',
									'message' => '対象URL の文字数エラー。',
							),
					);
				}

				// URLチェック
				if ($this->lib_validator->checkUri($input_post['name']) == FALSE)
				{
					$_tmp_mess .= "対象URL の形式エラー";
					$jsonArray = array(
							array(
									'title'   => '対象URL',
									'message' => '対象URL の形式エラー。',
							),
					);
				}
			}
		}

		// 全体チェック：対象URL
		if (isset($input_post['kw_url']))
		{
			if (empty($input_post['kw_url']))
			{
				$_tmp_mess = "対象URL の文字数エラー";
				$jsonArray = array(
						array(
								'title'   => '対象URL',
								'message' => '対象URL は必須入力項目です。',
						),
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['kw_url'], 1, 510) == FALSE)
				{
					$_tmp_mess = "対象URL の文字数エラー";
					$jsonArray = array(
							array(
									'title'   => '対象URL',
									'message' => '対象URL の文字数エラー。',
							),
					);
				}

				// URLチェック
				if ($this->lib_validator->checkUri($input_post['kw_url']) == FALSE)
				{
					$_tmp_mess .= "対象URL の形式エラー";
					$jsonArray = array(
							array(
									'title'   => '対象URL',
									'message' => '対象URL の形式エラー。',
							),
					);
				}
			}
		}

		// エラーチェック
		/*
		 *
		 * ここで入力エラーが無かった場合、キーワード更新処理を行う
		 *
		 */
		if (isset($input_post['kw_seq']) && ($_tmp_mess == NULL))
		{

			$this->load->model('Keyword',   'kw', TRUE);
			$this->load->model('Group_tag', 'gt', TRUE);
			$this->load->library('lib_keyword');
			$this->load->library('lib_rootdomain');

			// **** 参考 *****************
				// ajaxデータ
			// 	Array (
			// 			[kw_seq] => [67,64,79]
			// 			[kw_status] => 1
			// 			[kw_url] => http://www.test7.com/
			// 			[kw_group] => Array (
			// 							[0] => test )
			// 			[kw_matchtype] => 0
			// 			[kw_location_id] => 2392
			// 			[kw_cl_seq] => 3
			// 			[kw_ac_seq] => 11 )
			// *****************************

			// *** ajaxでの取得値
			$set_kw_data['kw_status']    = $input_post['kw_status'];
			$set_kw_data['kw_url']       = $input_post['kw_url'];
			$set_kw_data['kw_matchtype'] = $input_post['kw_matchtype'];

			// 対象URL & 補正
			preg_match_all("/\//", $set_kw_data['kw_url'], $cnt_slash) ;
			if (count($cnt_slash[0]) == 2)
			{
				$set_kw_data['kw_url'] = $input_post['kw_url'] . "/";
			}

			// 入力グループ設定チェック
			if (empty($input_post['kw_group']))
			{
				$set_kw_data['kw_group'] = NULL;
			} else {
				$set_kw_data['kw_group'] = $input_post['kw_group'][0];
			}

			// *** 選択されたkw_seqを分解
			$_tmp_kw_seq = trim($input_post['kw_seq'], "[");
			$_tmp_kw_seq = trim($_tmp_kw_seq, "]");
			$_tmp_kw_seq = explode(",", $_tmp_kw_seq);

			// *** 一つのseqから仲間 3人を見つける！
			/*
			 * /controllers/Data_csv.php => toplist_csvup_chk()
			 * に同等！
			 *
			 */
			$_arr_kw_seq = array();
			foreach ($_tmp_kw_seq as $key => $value)
			{
				$get_kw_pair = $this->kw->get_kw_seq($value);

				// ３キーワード（google-PC/Mobile/Yahoo!）情報を取得
				$set_kw_pair['kw_cl_seq']      = $get_kw_pair[0]['kw_cl_seq'];
				$set_kw_pair['kw_url']         = $get_kw_pair[0]['kw_url'];
				$set_kw_pair['kw_keyword']     = $get_kw_pair[0]['kw_keyword'];
				$set_kw_pair['kw_matchtype']   = $get_kw_pair[0]['kw_matchtype'];
				$set_kw_pair['kw_location_id'] = $get_kw_pair[0]['kw_location_id'];

				$arr_kw_pair =$this->kw->get_kw_info($set_kw_pair);

				// *** ここから個別にキーワード情報のチェックを行う
				foreach ($arr_kw_pair as $key1 => $val1)
				{
					//array_push($_arr_kw_seq, $val1['kw_seq']);

					// 不足分のキーワード情報を取得
					$get_kw_data = $this->kw->get_kw_seq($val1['kw_seq']);

					$set_kw_data['kw_keyword']      = $get_kw_data[0]['kw_keyword'];
					$set_kw_data['kw_searchengine'] = $get_kw_data[0]['kw_searchengine'];
					$set_kw_data['kw_device']       = $get_kw_data[0]['kw_device'];
					$set_kw_data['kw_location_id']  = $get_kw_data[0]['kw_location_id'];
					$set_kw_data['kw_cl_seq']       = $get_kw_data[0]['kw_cl_seq'];

					$set_kw_data['kw_seq']          = $get_kw_data[0]['kw_seq'];

					// ** 旧URL情報を別レコードとして保存するかチェック
					$get_old_kw_data =$this->kw->get_kw_seq($set_kw_data['kw_seq']);

					if (($set_kw_data['kw_url'] != $get_old_kw_data[0]['kw_url'])
							|| ($set_kw_data['kw_matchtype'] != $get_old_kw_data[0]['kw_matchtype']))
					{

						// 同一URLのチェック
						$get_kw_check = $this->kw->check_keyword($set_kw_data, $old_seq=NULL, $status=1);

						if (count($get_kw_check) >= 1)
						{
							foreach ($get_kw_check as $key => $value)
							{
								if (($value['kw_old_seq'] == NULL) && ($value['kw_status'] == 1))
								{
									//$_tmp_mess .= "同一URLの設定が存在します。";
									$jsonArray = array(
													array(
															'title'   => '対象URL',
															'message' => '同一URLの設定が存在します。',
													),
									);


									goto err_label;									// gotoラベル。ここは後で考えよう！
								}
							}
						}
					}

					// トランザクション・START
					$this->db->trans_strict(FALSE);                                     // StrictモードをOFF
					$this->db->trans_start();                                           // trans_begin

					// 「有効」と「無効」で処理を分けるか？
					//if ($input_post['kw_status'] == 0)

					/*
					 * URL書き換えは、基本303(または301)の場合以外の使用は順位データがおかしくなる可能性あり？
					 * 順位データの引継ぎする？
					 */

					// ** 旧URL情報を別レコードとして保存
					$get_old_kw_data[0]['kw_old_seq'] = $get_old_kw_data[0]['kw_seq'];
					$get_old_kw_data[0]['kw_group']   = NULL;
					$get_old_kw_data[0]['kw_tag']     = NULL;

					if (($set_kw_data['kw_url'] == $get_old_kw_data[0]['kw_url'])
							&& ($set_kw_data['kw_matchtype'] != $get_old_kw_data[0]['kw_matchtype']))
					{

						// URLマッチタイプのみ変更は、UPDATE。
						$set_matchtype_data['kw_seq']       = $set_kw_data['kw_seq'];
						$set_matchtype_data['kw_matchtype'] = $set_kw_data['kw_matchtype'];
						$this->kw->update_keyword($set_matchtype_data);

					} elseif ($set_kw_data['kw_url'] != $get_old_kw_data[0]['kw_url']) {

						// 対象URLが変更された場合は、旧URLレコードを作成する。 INSERT。
						$get_old_kw_data[0]['kw_status'] = 1;
						unset($get_old_kw_data[0]['kw_seq']);

						$this->kw->insert_keyword($get_old_kw_data[0]);
					}

					// 旧URLの重複チェック
					$get_url_check = $this->kw->check_url($set_kw_data, $set_kw_data['kw_seq'], $status=1);
					if (count($get_url_check) >= 1)
					{
						// status を書き換え
						foreach ($get_url_check as $key => $value)
						{
							$get_url_check[$key]['kw_status'] = 0;
							$this->kw->update_keyword($get_url_check[$key]);
						}
					}

					// ** 設定内容の反映範囲：
					//   「他キーワードへの反映」選択 → 仕様変更でとりあえず「0：反映させない」固定とする
					$this->lib_keyword->update_reflection($set_kw_data, 0);

					// トランザクション・COMMIT
					$this->db->trans_complete();                                        // trans_rollback & trans_commit
					if ($this->db->trans_status() === FALSE)
					{
						log_message('error', 'client::[keywordlist->chg_comp()]キーワード編集処理 トランザクションエラー');

						//$_tmp_mess .= "キーワード編集処理 トランザクションエラー。";
						$jsonArray = array(
										array(
												'title'   => '結果',
												'message' => 'キーワード編集処理 トランザクションエラー。',
										),
						);


						goto err_label;													// gotoラベル。ここは後で考えよう！
					} else {
						//$this->smarty->assign('mess',  "更新が完了しました。");
					}

					// ルートドメイン数のカウント＆更新
					$get_kw_info = $this->kw->get_kw_seq($set_kw_data['kw_seq']);
					$this->lib_rootdomain->get_rootdomain_chg($get_kw_info[0]['kw_cl_seq'], $get_kw_info[0]['kw_rootdomain']);

					if (!empty($get_old_kw_data))
					{
						// ルートドメインの削除有無
						$this->lib_rootdomain->get_rootdomain_del($get_old_kw_data[0]['kw_cl_seq'], $get_old_kw_data[0]['kw_rootdomain']);
					}

					/*
					 * ここは変えた方がいいかも？
					 * ロジック？ or 仕様？
					 */
					// 新規に追加された設定グループをレコード追加
					if (!empty($set_kw_data['kw_group']))
					{
						$get_gt_name = $this->gt->get_gt_name($set_kw_data['kw_group'], $set_kw_data['kw_cl_seq'], 0);

						if (count($get_gt_name) == 0)
						{
							$set_gt_data['gt_name']   = $set_kw_data['kw_group'];
							$set_gt_data['gt_cl_seq'] = $set_kw_data['kw_cl_seq'];
							$set_gt_data['gt_type']   = 0;

							// INSERT
							$this->gt->insert_group_tag($set_gt_data);
						}
					}

					// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
					$this->lib_keyword->update_group_info_all($set_kw_data['kw_cl_seq'], 0);

				}
			}

			if ($_tmp_mess == NULL)
			{
				$this->smarty->assign('mess', TRUE);
				$jsonArray = array(
						array(
								'title'   => 'success_update',
								'message' => '',
						),
				);

			} else {
				$this->smarty->assign('mess', $_tmp_mess);
			}

		} else {
err_label:																// gotoラベル

		}

		$_tmp_json = sprintf("callback(%s)",json_encode($jsonArray));
		$this->smarty->assign('mess', $_tmp_json);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_kwupdate_chk.tpl');

	}

	// ajax用ダミーページ : キーワード削除からの削除画面表示
	public function index_aj_kwdelete()
	{

		$input_post = $this->input->post();

		$this->load->model('Keyword',   'kw', TRUE);

		$arr_kwseq = "";
		foreach ($input_post['kw_seq'] as $key => $value)
		{
			$get_kw_data[$key] = $this->kw->get_kw_seq($value);
			$arr_kwseq .= $value . ",";
		}
		$arr_kwseq = "[" . rtrim($arr_kwseq, ",") . "]";

		// バリデーション設定
		$this->_set_validation();

		$this->smarty->assign('arr_kw_seq', $arr_kwseq);
		$this->smarty->assign('list',       $get_kw_data);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_kwdelete.tpl');

	}

	// ajax用ダミーページ : index_aj_kwdelete --> キーワード削除チェック
	public function index_aj_kwdelete_chk()
	{

		$input_post = $this->input->post();

		$_tmp_mess = NULL;
		$jsonArray = array();

		if (isset($input_post['kw_seq']))
		{

			$this->load->model('Keyword',   'kw', TRUE);
			$this->load->model('Account',   'ac', TRUE);
			$this->load->model('Ranking',   'rk', TRUE);
			$this->load->model('Watchlist', 'wt', TRUE);
			$this->load->library('lib_auth');
			$this->load->library('lib_keyword');
			$this->load->library('lib_rootdomain');

			// *** ajaxでの取得値
			$set_kw_data['kw_cl_seq'] = $input_post['kw_cl_seq'];

			// *** 選択されたkw_seqを分解
			$_tmp_kw_seq = trim($input_post['kw_seq'], "[");
			$_tmp_kw_seq = trim($_tmp_kw_seq, "]");
			$_tmp_kw_seq = explode(",", $_tmp_kw_seq);

			// *** 一つのseqから仲間 3人を見つける！
			$_arr_kw_seq = array();
			foreach ($_tmp_kw_seq as $key => $value)
			{
				$get_kw_data = $this->kw->get_kw_seq($value);

				// ３キーワード（google-PC/Mobile/Yahoo!）情報を取得
				$set_kw_info['kw_cl_seq']      = $get_kw_data[0]['kw_cl_seq'];
				$set_kw_info['kw_url']         = $get_kw_data[0]['kw_url'];
				$set_kw_info['kw_keyword']     = $get_kw_data[0]['kw_keyword'];
				$set_kw_info['kw_matchtype']   = $get_kw_data[0]['kw_matchtype'];
				$set_kw_info['kw_location_id'] = $get_kw_data[0]['kw_location_id'];

				$get_kw_info =$this->kw->get_kw_info($set_kw_info);

				// 配列に追加
				foreach ($get_kw_info as $key1 => $val)
				{
					array_push($_arr_kw_seq, $val['kw_seq']);
				}
			}

			$get_ac_data = $this->ac->get_ac_seq($_SESSION['c_memSeq']);

			// トランザクション・START
			$this->db->trans_strict(FALSE);                                     // StrictモードをOFF
			$this->db->trans_start();                                           // trans_begin

			foreach ($_arr_kw_seq as $key => $value)
			{
				// ルートドメインの削除準備
				$get_kw_info = $this->kw->get_kw_seq($value);

				// DELETE：キーワード
				$this->kw->delete_keyword($value, $_SESSION['c_memGrp']);

				// DELETE：ランキング
				$this->rk->delete_ranking($value, $_SESSION['c_memGrp']);

				// DELETE：ウォッチリスト
				$this->wt->delete_wt_list($value, $_SESSION['c_memGrp']);

				// グループ＆タグの再集計
				$this->lib_keyword->update_group_info_all($_SESSION['c_memGrp'], 0);
				//$this->lib_keyword->update_tag_info_all($_SESSION['c_memGrp'], 1);
			}

			// トランザクション・COMMIT
			$this->db->trans_complete();                                        // trans_rollback & trans_commit
			if ($this->db->trans_status() === FALSE)
			{
				log_message('error', 'client::[keyworddetail->del_pw()]キーワード削除(PW)処理 トランザクションエラー');

				$_tmp_mess .= "キーワード削除処理 トランザクションエラー。";
				$jsonArray = array(
								array(
										'title'   => '結果',
										'message' => 'キーワード編集処理 トランザクションエラー。',
								),
				);
			} else {
				$jsonArray = array(
								array(
										'title'   => 'success_delete',
										'message' => '',
								),
				);

				// ルートドメインの削除有無
				$this->lib_rootdomain->get_rootdomain_del($get_kw_info[0]['kw_cl_seq'], $get_kw_info[0]['kw_rootdomain']);
			}
		} else{
			$_tmp_mess .= "キーワード取得エラー。";
			$jsonArray = array(
					array(
							'title'   => '結果',
							'message' => 'キーワード取得エラー。',
					),
			);
		}

		if ($_tmp_mess == NULL)
		{
			$this->smarty->assign('mess', TRUE);

		} else {
			$this->smarty->assign('mess', $_tmp_mess);
		}

		$_tmp_json = sprintf("callback(%s)",json_encode($jsonArray));
		$this->smarty->assign('mess', $_tmp_json);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_kwdelete_chk.tpl');

	}

	// キーワード：ウォッチリストへの登録＆解除 (ajax対応)
	public function index_aj_watchlist()
	{

		$_chg_seq   = filter_input( INPUT_POST, "chg_seq" );

		if (empty($_chg_seq))
		{
			show_404();
		}

		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// キーワード設定情報を取得
		$get_kw_data = $this->kw->get_kw_seq($_chg_seq);

		// ウォッチリスト情報有無をチェック
		$set_wt_data['wt_ac_seq']        = $_SESSION['c_memSeq'];
		$set_wt_data['wt_cl_seq']        = $get_kw_data[0]['kw_cl_seq'];
		$set_wt_data['wt_kw_seq']        = $get_kw_data[0]['kw_seq'];
		$set_wt_data['wt_kw_rootdomain'] = $get_kw_data[0]['kw_rootdomain'];
		$get_wt_data = $this->wt->get_watchlist_data($_SESSION['c_memSeq'], $get_kw_data[0]['kw_cl_seq'], $get_kw_data[0]['kw_seq']);

		if (count($get_wt_data) == 0)
		{
			// 新規登録
			$this->wt->insert_watchlist($set_wt_data);
		} else {
			// 削除
			$this->wt->delete_watchlist($set_wt_data);
		}

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_watchlist.tpl');

	}

	// アカウント一覧
	public function accountlist()
	{

		$input_post = $this->input->post();

		// バリデーション設定
		$this->_set_validation();

		// アカウントメンバーの取得
		$this->load->model('Account', 'ac', TRUE);

		$tmp_inputpost = array(
				'ac_seq'    => $_SESSION['c_memSeq'],
				'ac_cl_seq' => $_SESSION['c_memGrp'],
				'orderid'   => '',
		);

		list($account_list, $account_countall) = $this->ac->get_accountlist($tmp_inputpost, $tmp_per_page=0, $tmp_offset=0);

		$this->smarty->assign('list', $account_list);
		$this->smarty->assign('countall', $account_countall);

		$this->view('topdetail/accountlist.tpl');

	}

	// ajax用ダミーページ : アカウント一覧
	public function index_aj_accountlist()
	{

		$input_post = $this->input->post();

		// バリデーション設定
		$this->_set_validation();

		// アカウントメンバーの取得
		$this->load->model('Account', 'ac', TRUE);

		$tmp_inputpost = array(
				'ac_seq'    => $_SESSION['c_memSeq'],
				'ac_cl_seq' => $_SESSION['c_memGrp'],
				'orderid'   => '',
		);

		list($account_list, $account_countall) = $this->ac->get_accountlist($tmp_inputpost, $tmp_per_page=0, $tmp_offset=0);

		$this->smarty->assign('list', $account_list);
		$this->smarty->assign('countall', $account_countall);

		$this->view('topdetail/index_account_table.tpl');

	}

	// ajax用ダミーページ : アカウント追加画面表示
	public function index_aj_acinsert()
	{

		// バリデーション設定
		$this->_set_validation();

		// 初期値セット
		$this->_item_account_set();

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_acinsert.tpl');

	}

	// ajax用ダミーページ : index_aj_acinsert --> アカウント追加チェック
	public function index_aj_acinsert_chk()
	{

		$input_post = $this->input->post();

		$this->load->library('lib_validator');

		$_tmp_mess = NULL;
		$jsonArray = array();

		// 部分チェック：対象URL

		// 全体チェック：所属部署
		if (isset($input_post['ac_department']))
		{
			// 文字数チェック
			if ($this->lib_validator->checkLength($input_post['ac_department'], 0, 50) == FALSE)
			{
				$_tmp_mess = "所属部署 の文字数エラー";
				array_push($jsonArray,
						array(
								'title'   => 'department',
								'message' => '所属部署 の文字数エラー。',
						)
				);
			}
		}

		// 全体チェック：担当者姓
		if (isset($input_post['ac_name01']))
		{
			if (empty($input_post['ac_name01']))
			{
				$_tmp_mess = "担当者姓 の文字数エラー";
				array_push($jsonArray,
						array(
								'title'   => 'name01',
								'message' => '担当者姓 は必須入力項目です。',
						)
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['ac_name01'], 1, 50) == FALSE)
				{
					$_tmp_mess = "担当者姓 の文字数エラー";
					array_push($jsonArray,
							array(
									'title'   => 'name01',
									'message' => '担当者姓 の文字数エラー。',
							)
					);
				}
			}
		}

		// 全体チェック：担当者名
		if (isset($input_post['ac_name02']))
		{
			if (empty($input_post['ac_name02']))
			{
				$_tmp_mess = "担当者名 の文字数エラー";
				array_push($jsonArray,
						array(
								'title'   => 'name02',
								'message' => '担当者名 は必須入力項目です。',
						)
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['ac_name02'], 1, 50) == FALSE)
				{
					$_tmp_mess = "担当者名 の文字数エラー";
					array_push($jsonArray,
							array(
									'title'   => 'name02',
									'message' => '担当者名 の文字数エラー。',
							)
					);
				}
			}
		}

		// 全体チェック：メールアドレス＆ログインID
		if (isset($input_post['ac_id']))
		{
			if (empty($input_post['ac_id']))
			{
				$_tmp_mess = "メールアドレス＆ログインID の文字数エラー";
				array_push($jsonArray,
						array(
								'title'   => 'id',
								'message' => 'メールアドレス＆ログインID は必須入力項目です。',
						)
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['ac_id'], 1, 50) == FALSE)
				{
					$_tmp_mess = "メールアドレス＆ログインID の文字数エラー";
					array_push($jsonArray,
							array(
									'title'   => 'id',
									'message' => 'メールアドレス＆ログインID の文字数エラー。',
							)
					);
				}

				// Mailチェック
				if ($this->lib_validator->checkMailAddress($input_post['ac_id']) == FALSE)
				{
					$_tmp_mess .= "メールアドレス＆ログインID の形式エラー";
					array_push($jsonArray,
							array(
									'title'   => 'id',
									'message' => 'メールアドレス＆ログインID の形式エラー。',
							)
					);
				}
			}
		}

		// 全体チェック：パスワード
		if (isset($input_post['ac_pw']))
		{
			if (empty($input_post['ac_pw']))
			{
				$_tmp_mess = "パスワード の文字数エラー";
				array_push($jsonArray,
						array(
								'title'   => 'pw',
								'message' => 'パスワード は必須入力項目です。',
						)
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['ac_pw'], 8, 50) == FALSE)
				{
					$_tmp_mess = "パスワード の文字数エラー";
					array_push($jsonArray,
							array(
									'title'   => 'pw',
									'message' => 'パスワード の文字数エラー。',
							)
					);
				}

				// パスワードチェック：半角英数字・記号のみ
				if ($this->lib_validator->checkpw($input_post['ac_pw']) == FALSE)
				{
					$_tmp_mess .= "パスワード の形式エラー";
					array_push($jsonArray,
							array(
									'title'   => 'pw',
									'message' => 'パスワード の形式エラー (半角英数字・記号のみ)。',
							)
					);
				}
			}
		}

		// 全体チェック：キーワード権限
		if (isset($input_post['ac_keyword']))
		{
			// 文字数チェック
			if ($this->lib_validator->checkLength($input_post['ac_keyword'], 0, 1) == FALSE)
			{
				$_tmp_mess = "キーワード権限 の選択エラー";
				array_push($jsonArray,
						array(
								'title'   => 'radio_keyword',
								'message' => 'キーワード権限 の選択エラー。',
						)
				);
			}
		} else {
			$_tmp_mess = "キーワード権限 の文字数エラー";
			array_push($jsonArray,
					array(
							'title'   => 'radio_keyword',
							'message' => 'キーワード権限 は必須選択項目です。',
					)
			);
		}

		// 全体チェック：グループ権限
		if (isset($input_post['ac_group']))
		{
			// 文字数チェック
			if ($this->lib_validator->checkLength($input_post['ac_group'], 0, 1) == FALSE)
			{
				$_tmp_mess = "グループ権限 の選択エラー";
				array_push($jsonArray,
						array(
								'title'   => 'radio_group',
								'message' => 'グループ権限 の選択エラー。',
						)
				);
			}
		} else {
			$_tmp_mess = "グループ権限 の文字数エラー";
			array_push($jsonArray,
					array(
							'title'   => 'radio_group',
							'message' => 'グループ権限 は必須選択項目です。',
					)
			);

		}

		// エラーチェック
		/*
		*
		* ここで入力エラーが無かった場合、キーワード追加処理を行う
		*
		*/
		if ($_tmp_mess == NULL)
		{

			$this->load->model('Account', 'ac', TRUE);

			// メールアドレス＆ログインIDの重複チェック
			if ($this->ac->check_loginid($input_post['ac_id']))
			{
				$_tmp_mess .= "メールアドレス＆ログインID の重複エラー";
				array_push($jsonArray,
								array(
										'title'   => 'id',
										'message' => '入力されたメールアドレス＆ログインID は既に使用されています。',
								)
				);
			} else {
				$this->smarty->assign('err_clid',   FALSE);

				// DB書き込み
				$input_post["ac_cl_seq"] = $_SESSION['c_memGrp'];
				$this->ac->insert_account($input_post);
			}

			if ($_tmp_mess == NULL)
			{
				$this->smarty->assign('mess', TRUE);
				$jsonArray = array(
						array(
								'title'   => 'success_insert',
								'message' => '',
						),
				);

			} else {
				$this->smarty->assign('mess', $_tmp_mess);
			}

		} else {
			$this->smarty->assign('mess', $_tmp_mess);
		}

		$_tmp_json = sprintf("callback(%s)",json_encode($jsonArray));
		$this->smarty->assign('mess', $_tmp_json);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_acinsert_chk.tpl');

	}

	// ajax用ダミーページ : アカウント編集からの編集画面表示
	public function index_aj_acupdate()
	{

		$input_post = $this->input->post();

		// 初期値セット
		$this->_item_account_set();

		// バリデーション設定
		$this->_set_validation();

		// 更新対象アカウントのデータ取得
		$input_post = $this->input->post();

		$tmp_acseq = $input_post['ac_seq'];

		$this->load->model('Account', 'ac', TRUE);
		$ac_data = $this->ac->get_ac_seq($tmp_acseq, TRUE);

		$this->smarty->assign('info', $ac_data[0]);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_acupdate.tpl');

	}

	// ajax用ダミーページ : index_aj_acupdate --> アカウント編集チェック
	public function index_aj_acupdate_chk()
	{

		$input_post = $this->input->post();

		$this->load->library('lib_validator');

		$_tmp_mess = NULL;
		$jsonArray = array();

		// 部分チェック：対象URL

		// 全体チェック：所属部署
		if (isset($input_post['ac_department']))
		{
			// 文字数チェック
			if ($this->lib_validator->checkLength($input_post['ac_department'], 0, 50) == FALSE)
			{
				$_tmp_mess = "所属部署 の文字数エラー";
				array_push($jsonArray,
								array(
										'title'   => 'department',
										'message' => '所属部署 の文字数エラー。',
								)
				);
			}
		}

		// 全体チェック：担当者姓
		if (isset($input_post['ac_name01']))
		{
			if (empty($input_post['ac_name01']))
			{
				$_tmp_mess = "担当者姓 の文字数エラー";
				array_push($jsonArray,
								array(
										'title'   => 'name01',
										'message' => '担当者姓 は必須入力項目です。',
								)
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['ac_name01'], 1, 50) == FALSE)
				{
					$_tmp_mess = "担当者姓 の文字数エラー";
					array_push($jsonArray,
									array(
											'title'   => 'name01',
											'message' => '担当者姓 の文字数エラー。',
									)
					);
				}
			}
		}

		// 全体チェック：担当者名
		if (isset($input_post['ac_name02']))
		{
			if (empty($input_post['ac_name02']))
			{
				$_tmp_mess = "担当者名 の文字数エラー";
				array_push($jsonArray,
								array(
										'title'   => 'name02',
										'message' => '担当者名 は必須入力項目です。',
								)
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['ac_name02'], 1, 50) == FALSE)
				{
					$_tmp_mess = "担当者名 の文字数エラー";
					array_push($jsonArray,
									array(
											'title'   => 'name02',
											'message' => '担当者名 の文字数エラー。',
									)
					);
				}
			}
		}

		// 全体チェック：パスワード
		if (isset($input_post['ac_pw']))
		{
			if (empty($input_post['ac_pw']))
			{
				$_tmp_mess = "パスワード の文字数エラー";
				array_push($jsonArray,
								array(
										'title'   => 'pw',
										'message' => 'パスワード は必須入力項目です。',
								)
				);
			} else {
				// 文字数チェック
				if ($this->lib_validator->checkLength($input_post['ac_pw'], 8, 50) == FALSE)
				{
					$_tmp_mess = "パスワード の文字数エラー";
					array_push($jsonArray,
									array(
											'title'   => 'pw',
											'message' => 'パスワード の文字数エラー。',
									)
					);
				}

				// パスワードチェック：半角英数字・記号のみ
				if ($this->lib_validator->checkpw($input_post['ac_pw']) == FALSE)
				{
					$_tmp_mess .= "パスワード の形式エラー";
					array_push($jsonArray,
									array(
											'title'   => 'pw',
											'message' => 'パスワード の形式エラー (半角英数字・記号のみ)。',
									)
					);
				}
			}
		}

		// エラーチェック
		/*
		*
		* ここで入力エラーが無かった場合、アカウント更新処理を行う
		*
		*/
		if ($_tmp_mess == NULL)
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

			if ($_SESSION['c_memSeq'] == $input_post['ac_seq'])
			{

				// DB書き込み
				$this->ac->update_account($input_post, $pw);

			} else {

				if ($input_post['ac_status'] == 9)
				{
					$input_post['ac_delflg'] = 1;
				}

				// DB書き込み (PW更新なし)
				$this->ac->update_account($input_post, $pw);

			}

			if ($_tmp_mess == NULL)
			{
				$this->smarty->assign('mess', TRUE);
				$jsonArray = array(
						array(
								'title'   => 'success_update',
								'message' => '',
						),
				);

			} else {
				$this->smarty->assign('mess', $_tmp_mess);
			}
		}

		$_tmp_json = sprintf("callback(%s)",json_encode($jsonArray));
		$this->smarty->assign('mess', $_tmp_json);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_acupdate_chk.tpl');

	}

	// 初期値セット
	private function _item_set()
	{

		// ステータスのセット
		$this->config->load('config_status');
		$opt_kw_status = $this->config->item('KEYWORD_KW_STATUS');

		$this->smarty->assign('options_kw_status',  $opt_kw_status);

		// グラフ表示月
		$date = new DateTime();
		for ($i=0; $i<=5; $i++)
		{
			$_gp_month = $date->modify("-1 month")->format('m');
			$this->smarty->assign('gp_month' . $i,  $_gp_month);
		}
	}

	// 初期値セット
	private function _item_account_set()
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

}