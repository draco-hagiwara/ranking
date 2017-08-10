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
		$this->_set_validation();                                                       // バリデーション設定
		$this->form_validation->run();

		// 1ページ当たりの表示件数
		$this->config->load('config_comm');
		$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

		if (isset($_POST['_back']))
		{
			// 検索条件を保持 :: 「戻る」ボタン処理
			$tmp_inputpost['kw_keyword']      = $_SESSION['c_kw_keyword'];
			$tmp_inputpost['kw_domain']       = $_SESSION['c_kw_domain'];

			$tmp_inputpost['kw_group']        = $_SESSION['c_kw_group'];
			$tmp_inputpost['kw_tag']          = $_SESSION['c_kw_tag'];
			$tmp_inputpost['kw_status']       = $_SESSION['c_kw_status'];
			$tmp_inputpost['watchlist']       = $_SESSION['c_watchlist'];
			$tmp_inputpost['kw_ac_seq']       = $_SESSION['c_kw_ac_seq'];

			$tmp_inputpost['kw_matchtype']    = $_SESSION['c_kw_matchtype'];
			$tmp_inputpost['kw_searchengine'] = $_SESSION['c_kw_searchengine'];
			$tmp_inputpost['kw_device']       = $_SESSION['c_kw_device'];
			$tmp_inputpost['kw_location_id']  = $_SESSION['c_kw_location_id'];

			$tmp_inputpost['orderid']         = $_SESSION['c_orderid'];

		} else {
			// セッションデータをクリア
			$this->load->library('lib_auth');
			$this->lib_auth->delete_session('client');

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
						'c_back_set'   => "top",
				);
				$this->session->set_userdata($data);
			} else {
				$tmp_offset = 0;
				$tmp_inputpost = array(
						'kw_keyword'      => '',
						'kw_domain'       => '',
						'kw_group'        => '',
						'kw_tag'          => '',
						'kw_status'       => 1,
						'orderid'         => '',
						'watchlist'       => '0',
						'kw_ac_seq'       => $_SESSION['c_memSeq'],

						'kw_matchtype'    => '',
						'kw_searchengine' => 0,
						'kw_device'       => 0,
						'kw_location_id'  => '',
				);

				// セッションをフラッシュデータとして保存
				$data = array(
						'c_kw_keyword'      => '',
						'c_kw_domain'       => '',
						'c_kw_group'        => '',
						'c_kw_tag'          => '',
						'c_kw_status'       => 1,
						'c_orderid'         => '',
						'c_watchlist'       => '0',
						'c_kw_ac_seq'       => $_SESSION['c_memSeq'],

						'c_kw_matchtype'    => '',
						'c_kw_searchengine' => 0,
						'c_kw_device'       => 0,
						'c_kw_location_id'  => '',

						'c_offset'          => $tmp_offset,
						'c_back_set'        => "top",
				);
				$this->session->set_userdata($data);
			}
		}

		// キーワード情報の取得
		$this->load->model('Keyword', 'kw', TRUE);
		list($kw_list, $kw_countall) = $this->kw->get_kw_toplist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp']);

		$this->smarty->assign('list', $kw_list);

		// 順位データ情報を取得 (31日分) ＆ グラフ表示
		$this->load->library('lib_ranking_data');
		$cnt_date = 31;
		$this->lib_ranking_data->create_ranking_graph($kw_list, $cnt_date);

		// Pagination 設定
		$set_pagination = $this->_get_Pagination($kw_countall, $tmp_per_page);

		// 初期値セット
		$this->_search_set();

		$this->smarty->assign('set_pagination',    $set_pagination['page_link']);
		$this->smarty->assign('countall',          $kw_countall);

		$this->smarty->assign('seach_kw_keyword',   $tmp_inputpost['kw_keyword']);
		$this->smarty->assign('seach_kw_domain',    $tmp_inputpost['kw_domain']);
		$this->smarty->assign('seach_kw_group',     $tmp_inputpost['kw_group']);
		$this->smarty->assign('seach_kw_tag',       $tmp_inputpost['kw_tag']);
		$this->smarty->assign('seach_kw_status',    $tmp_inputpost['kw_status']);
		$this->smarty->assign('seach_orderid',      $tmp_inputpost['orderid']);
		$this->smarty->assign('seach_watchlist',    $tmp_inputpost['watchlist']);
		$this->smarty->assign('seach_accountlist',  $tmp_inputpost['kw_ac_seq']);

		$this->smarty->assign('seach_matchtype',    $tmp_inputpost['kw_matchtype']);
		$this->smarty->assign('seach_searchengine', $tmp_inputpost['kw_searchengine']);
		$this->smarty->assign('seach_device',       $tmp_inputpost['kw_device']);
		$this->smarty->assign('seach_location_id',  $tmp_inputpost['kw_location_id']);

		$date = new DateTime();
		$_start_date   = $date->format('Y-m-d');
		$_set_cnt_date = "- " . ($cnt_date - 1) . " days";
		$_end_date     = $date->modify($_set_cnt_date)->format('Y-m-d');

		$this->smarty->assign('start_date',        $_start_date);
		$this->smarty->assign('end_date',          $_end_date);

		$this->view('top/index.tpl');

	}

	// 一覧表示
	public function search()
	{

		if ($this->input->post('submit') == '_submit')
		{
			// セッションをフラッシュデータとして保存
			$data = array(
					'c_kw_keyword'      => $this->input->post('kw_keyword'),
					'c_kw_domain'       => $this->input->post('kw_domain'),
					'c_kw_group'        => $this->input->post('kw_group'),
					'c_kw_tag'          => $this->input->post('kw_tag'),
					'c_kw_status'       => $this->input->post('kw_status'),
					'c_orderid'         => $this->input->post('orderid'),
					'c_watchlist'       => $this->input->post('watchlist'),
					'c_kw_ac_seq'       => $this->input->post('kw_ac_seq'),
					'c_kw_matchtype'    => $this->input->post('kw_matchtype'),
					'c_kw_searchengine' => $this->input->post('kw_searchengine'),
					'c_kw_device'       => $this->input->post('kw_device'),
					'c_kw_location_id'  => $this->input->post('kw_location_id'),
			);
			$this->session->set_userdata($data);

			$tmp_inputpost = $this->input->post();
			unset($tmp_inputpost["submit"]);

		} else {
			// セッションからフラッシュデータ読み込み
			$tmp_inputpost['kw_keyword']      = $_SESSION['c_kw_keyword'];
			$tmp_inputpost['kw_domain']       = $_SESSION['c_kw_domain'];
			$tmp_inputpost['kw_group']        = $_SESSION['c_kw_group'];
			$tmp_inputpost['kw_tag']          = $_SESSION['c_kw_tag'];
			$tmp_inputpost['kw_status']       = $_SESSION['c_kw_status'];
			$tmp_inputpost['orderid']         = $_SESSION['c_orderid'];
			$tmp_inputpost['watchlist']       = $_SESSION['c_watchlist'];
			$tmp_inputpost['kw_ac_seq']       = $_SESSION['c_kw_ac_seq'];

			$tmp_inputpost['kw_matchtype']    = $_SESSION['c_kw_matchtype'];
			$tmp_inputpost['kw_searchengine'] = $_SESSION['c_kw_searchengine'];
			$tmp_inputpost['kw_device']       = $_SESSION['c_kw_device'];
			$tmp_inputpost['kw_location_id']  = $_SESSION['c_kw_location_id'];
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

		// キーワード情報の取得
		$this->load->model('Keyword', 'kw', TRUE);
		list($kw_list, $kw_countall) = $this->kw->get_kw_toplist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp']);

		$this->smarty->assign('list', $kw_list);

		// 順位データ情報を取得 (31日分) ＆ グラフ表示
		$this->load->library('lib_ranking_data');
		$cnt_date = 31;
		$this->lib_ranking_data->create_ranking_graph($kw_list, $cnt_date);

		// Pagination 設定
		$set_pagination = $this->_get_Pagination($kw_countall, $tmp_per_page);

		// 初期値セット
		$this->_search_set();

		$this->smarty->assign('set_pagination',     $set_pagination['page_link']);
		$this->smarty->assign('countall',           $kw_countall);

		$this->smarty->assign('seach_kw_keyword',   $tmp_inputpost['kw_keyword']);
		$this->smarty->assign('seach_kw_domain',    $tmp_inputpost['kw_domain']);
		$this->smarty->assign('seach_kw_group',     $tmp_inputpost['kw_group']);
		$this->smarty->assign('seach_kw_tag',       $tmp_inputpost['kw_tag']);
		$this->smarty->assign('seach_kw_status',    $tmp_inputpost['kw_status']);
		$this->smarty->assign('seach_orderid',      $tmp_inputpost['orderid']);
		$this->smarty->assign('seach_watchlist',    $tmp_inputpost['watchlist']);
		$this->smarty->assign('seach_accountlist',  $tmp_inputpost['kw_ac_seq']);

		$this->smarty->assign('seach_matchtype',    $tmp_inputpost['kw_matchtype']);
		$this->smarty->assign('seach_searchengine', $tmp_inputpost['kw_searchengine']);
		$this->smarty->assign('seach_device',       $tmp_inputpost['kw_device']);
		$this->smarty->assign('seach_location_id',  $tmp_inputpost['kw_location_id']);

		$date = new DateTime();
		$_start_date   = $date->format('Y-m-d');
		$_set_cnt_date = "- " . ($cnt_date - 1) . " days";
		$_end_date     = $date->modify($_set_cnt_date)->format('Y-m-d');

		$this->smarty->assign('start_date',         $_start_date);
		$this->smarty->assign('end_date',           $_end_date);

		$this->view('top/index.tpl');

	}

	// ウォッチリストへの登録＆解除
	public function watchlist()
	{

		$input_post = $this->input->post();

		if (!isset($input_post['chg_seq']))
		{
			show_404();
		}

		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// キーワード設定情報を取得
		$get_kw_data = $this->kw->get_kw_seq($input_post['chg_seq']);

		// ウォッチリスト情報有無をチェック
		$set_wt_data['wt_ac_seq']        = $get_kw_data[0]['kw_ac_seq'];
		$set_wt_data['wt_cl_seq']        = $get_kw_data[0]['kw_cl_seq'];
		$set_wt_data['wt_kw_seq']        = $get_kw_data[0]['kw_seq'];
		$set_wt_data['wt_kw_rootdomain'] = $get_kw_data[0]['kw_rootdomain'];
		$get_wt_data = $this->wt->get_watchlist_data($get_kw_data[0]['kw_ac_seq'], $get_kw_data[0]['kw_cl_seq'], $get_kw_data[0]['kw_seq']);

		if (count($get_wt_data) == 0)
		{
			// 新規登録
			$this->wt->insert_watchlist($set_wt_data);
		} else {
			// 削除
			$this->wt->delete_watchlist($set_wt_data);
		}

		// セッションからフラッシュデータ読み込み
		$tmp_inputpost['kw_keyword']   = $_SESSION['c_kw_keyword'];
		$tmp_inputpost['kw_domain']    = $_SESSION['c_kw_domain'];
		$tmp_inputpost['kw_status']    = $_SESSION['c_kw_status'];
		$tmp_inputpost['orderid']      = $_SESSION['c_orderid'];

		// バリデーション・チェック
		$this->_set_validation();                                               // バリデーション設定

		// Pagination 現在ページ数の取得：：URIセグメントの取得
		$tmp_offset = $_SESSION['c_offset'];

		// 1ページ当たりの表示件数
		$this->config->load('config_comm');
		$tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

		// キーワード情報の取得
		$this->load->model('Keyword', 'kw', TRUE);
		list($kw_list, $kw_countall) = $this->kw->get_keywordlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp']);

		$this->smarty->assign('list', $kw_list);

		// 順位データ情報を取得 (31日分) ＆ グラフ表示
		$this->load->library('lib_ranking_data');
		$cnt_date = 31;
		$this->lib_ranking_data->create_ranking_graph($kw_list, $cnt_date);

		// Pagination 設定
		$set_pagination = $this->_get_Pagination($kw_countall, $tmp_per_page);

		// 初期値セット
		$this->_search_set();

		$this->smarty->assign('set_pagination',   $set_pagination['page_link']);
		$this->smarty->assign('countall',         $kw_countall);

		$this->smarty->assign('seach_kw_keyword', $tmp_inputpost['kw_keyword']);
		$this->smarty->assign('seach_kw_domain',  $tmp_inputpost['kw_domain']);
		$this->smarty->assign('seach_kw_status',  $tmp_inputpost['kw_status']);
		$this->smarty->assign('seach_orderid',    $tmp_inputpost['orderid']);

		$date = new DateTime();
		$_start_date   = $date->format('Y-m-d');
		$_set_cnt_date = "- " . ($cnt_date - 1) . " days";
		$_end_date     = $date->modify($_set_cnt_date)->format('Y-m-d');

		$this->smarty->assign('start_date',       $_start_date);
		$this->smarty->assign('end_date',         $_end_date);

		redirect("/top/search/$tmp_offset/");
		//     	$this->view('top/index.tpl');

	}

	// Pagination 設定
	private function _get_Pagination($countall, $tmp_per_page)
	{

		$config['base_url']       = base_url() . '/top/search/';        // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
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

		// URL一致方式 選択項目セット
		$opt_matchtype = $this->config->item('KEYWORD_KW_MATCHTYPE');

		// 検索エンジン 選択項目セット
		$opt_searchengine = $this->config->item('KEYWORD_KW_ENGINE');

		// デバイス 選択項目セット
		$opt_device = $this->config->item('KEYWORD_KW_DEVICE');

		// Canonical Name 選択項目セット
		$this->load->library('lib_keyword');
		$opt_location = $this->lib_keyword->search_location();

		// キーワードID 並び替え選択項目セット
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

		// アカウントリスト 表示有無選択項目セット
		$this->load->model('Account', 'ac', TRUE);
		$get_ac_list = $this->ac->get_search_ac($_SESSION['c_memGrp']);
		$arropt_account[0] = "すべて表示";
		foreach ($get_ac_list as $key => $val)
		{
			$arropt_account[$val['ac_seq']] = $val['ac_name01'] . ' ' . $val['ac_name02'];
		}

		$this->smarty->assign('options_kw_status',    $opt_kw_status);

		$this->smarty->assign('options_matchtype',    $opt_matchtype);
		$this->smarty->assign('options_searchengine', $opt_searchengine);
		$this->smarty->assign('options_device',       $opt_device);
		$this->smarty->assign('options_location',     $opt_location);

		$this->smarty->assign('options_orderid',      $arropt_id);
		$this->smarty->assign('options_watchlist',    $arropt_watch);
		$this->smarty->assign('options_accountlist',  $arropt_account);

	}

	// 設定タグのセット
	private function _tag_set($cl_seq, $kw_tag)
	{

		// タグ情報取得
		$this->load->model('Group_tag', 'gt', TRUE);
		$tag_list =$this->gt->get_gt_clseq($cl_seq, 1);

		$opt_tag = "";
		foreach ($tag_list as $key => $value)
		{

			$comp_tag = "[" . $value['gt_name'] . "]";
			if (strpos($kw_tag, $comp_tag) !== false)
			{
				$opt_tag .= '<option selected="selected" value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
			} else {
				$opt_tag .= '<option value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
			}

		}

		$this->smarty->assign('options_kw_tag', $opt_tag);

	}

	// 設定情報反映有無セット
	private function _reflection_set()
	{

		// ステータスのセット
		$this->config->load('config_status');
		$opt_reflection = $this->config->item('KEYWORD_REFLECTION');

		$this->smarty->assign('options_reflection',  $opt_reflection);

	}

	// フォーム・バリデーションチェック
	private function _set_validation()
	{

		$rule_set = array(
		);

		$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

	}


//     public function __construct()
//     {
//         parent::__construct();

//         $this->load->library('lib_auth');
//         $this->lib_auth->check_session();

//     }

//     // ログイン 初期表示
//     public function index()
//     {

//     	// セッションデータをクリア
//     	$this->load->library('lib_auth');
//     	$this->lib_auth->delete_session('client');

//     	$this->_set_validation();

//         $this->view('top/index.tpl');

//     }

//     // フォーム・バリデーションチェック
//     private function _set_validation()
//     {
//     	$rule_set = array(
//     	);

//     	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
//     }

}
