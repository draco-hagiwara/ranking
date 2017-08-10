<?php

class Topdetail extends MY_Controller
{

    /*
     *  キーワード情報管理 詳細
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('err_date', FALSE);
        $this->smarty->assign('mess',     FALSE);

    }

    // キーワード詳細TOP
    public function index()
    {

        // セッションデータをクリア
        $this->load->library('lib_auth');
        $this->lib_auth->delete_session('client');

        // バリデーション・チェック
        $this->_set_validation();                                                       // バリデーション設定

        $this->view('topdetail/index.tpl');

    }

    /*
     * キーワード情報詳細
     *
     * 順位データをテーブル表示しないためこちらで全期間をカバー
     */
    public function detail()
    {

//     	//セッションデータをクリア
//     	$this->load->library('lib_auth');
//     	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

    	if (!isset($input_post['chg_seq']))
    	{
    		show_404();
    	}

    	 // URIセグメントの取得
    	$segments = $this->uri->segment_array();

    	// グラフ表示日数を指定

    	if ((isset($segments[3]) && $segments[3] == 0)
    			|| (isset($input_post['gp_term']) && $input_post['gp_term'] == 0))
    	{
    		$_date_cnt = 31;
    		$this->smarty->assign('gp_term', 0);
    	}

    	if ((isset($segments[3]) && $segments[3] == 1)
    			|| (isset($input_post['gp_term']) && $input_post['gp_term'] == 1))
    	{
    		$_date_cnt = 93;
    		$this->smarty->assign('gp_term', 1);
    	}

    	if ((isset($segments[3]) && $segments[3] == 2)
    			|| (isset($input_post['gp_term']) && $input_post['gp_term'] == 2))
    	{
    		$_date_cnt = 186;
    		$this->smarty->assign('gp_term', 2);
    		 }

    	if ((isset($segments[3]) && $segments[3] == 3)
    			|| (isset($input_post['gp_term']) && $input_post['gp_term'] == 3))
    	{
    		$_date_cnt = 7;
    		$this->smarty->assign('gp_term', 3);
    	}

    	$_kw_seq = $input_post['chg_seq'];

    	$this->load->model('Keyword',   'kw', TRUE);
    	$this->load->model('Ranking',   'rk', TRUE);
    	$this->load->model('Memo',      'me', TRUE);
    	$this->load->model('Watchlist', 'wt', TRUE);

    	// バリデーション設定
    	$this->_set_validation();

    	// キーワード設定情報を取得
    	$get_kw_data =$this->kw->get_kw_seq($_kw_seq);

    	// メモ情報を取得
    	$get_me_data =$this->me->get_me_kwseq($_kw_seq);

    	// ウォッチリスト情報を取得
    	$get_wt_data =$this->wt->get_watchlist_data($get_kw_data[0]['kw_ac_seq'], $get_kw_data[0]['kw_cl_seq'], $_kw_seq);

    	// 順位データ情報を取得 (31日分) ＆ グラフ表示
    	$this->load->library('lib_ranking_data');
    	$this->lib_ranking_data->get_ranking_graph($_kw_seq, $_date_cnt);

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->location_set();

    	// 表示期間の日付
    	$date = new DateTime();
    	$_start_date   = $date->format('Y-m-d');
    	$_set_date_cnt = "- " . ($_date_cnt - 1) . " days";
    	$_end_date     = $date->modify($_set_date_cnt)->format('Y-m-d');

    	$this->smarty->assign('info',       $get_kw_data[0]);
    	$this->smarty->assign('info_me',    $get_me_data);
    	if (empty($get_wt_data))
    	{
    		$this->smarty->assign('wt_seq', NULL);
    	} else {
    		$this->smarty->assign('wt_seq', $get_wt_data[0]['wt_seq']);
    	}

    	$this->smarty->assign('start_date', $_start_date);
    	$this->smarty->assign('end_date',   $_end_date);

    	// 「戻る」のページャ先をセット
    	$page_cnt = $_SESSION['c_offset'];
    	$this->smarty->assign('seach_page_no', $page_cnt);

    	// 「戻る」の画面先をセット
    	$this->smarty->assign('back_page', $_SESSION['c_back_set']);

    	$this->view('topdetail/detail.tpl');

    }

    // キーワード情報詳細 (1ヶ月)
    public function month01()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

   		$_kw_seq = $input_post['chg_seq'];

    	$this->load->model('Keyword', 'kw', TRUE);
    	$this->load->model('Ranking', 'rk', TRUE);
    	$this->load->model('Memo',    'me', TRUE);

    	// バリデーション設定
    	$this->_set_validation();

    	// キーワード設定情報を取得
    	$get_kw_data =$this->kw->get_kw_seq($_kw_seq);

    	// メモ情報を取得
    	$get_me_data =$this->me->get_me_kwseq($_kw_seq);

    	// 順位データ情報を取得 (31日分) ＆ グラフ表示
    	$this->load->library('lib_ranking_data');
    	$this->lib_ranking_data->get_ranking_graph($_kw_seq, 31);

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->location_set();

    	$this->smarty->assign('info',     $get_kw_data[0]);
    	$this->smarty->assign('info_me',  $get_me_data);

    	$this->view('topdetail/month01.tpl');

    }

    // キーワード情報詳細 (3ヶ月)
    public function month03()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

//     	// 削除選択時にPWエラーで戻ってきた場合の処理
//     	$segments = $this->uri->segment_array();
//     	if (isset($segments[3]))
//     	{
//     		$_kw_seq = $segments[3];
//     	} else {
    		$_kw_seq = $input_post['chg_seq'];
//     	}

    	$this->load->model('Keyword', 'kw', TRUE);
    	$this->load->model('Ranking', 'rk', TRUE);
    	$this->load->model('Memo',    'me', TRUE);

    	// バリデーション設定
    	$this->_set_validation();

    	// キーワード設定情報を取得
    	$get_kw_data =$this->kw->get_kw_seq($_kw_seq);

    	// メモ情報を取得
    	$get_me_data =$this->me->get_me_kwseq($_kw_seq);

    	// 順位データ情報を取得 (93日分) ＆ グラフ表示
    	$this->load->library('lib_ranking_data');
    	$this->lib_ranking_data->get_ranking_graph($_kw_seq, 93);

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->location_set();

    	$this->smarty->assign('info',     $get_kw_data[0]);
    	$this->smarty->assign('info_me',  $get_me_data);

    	$this->view('topdetail/month03.tpl');

    }

    // キーワード情報詳細 (6ヶ月)
    public function month06()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

    	$_kw_seq = $input_post['chg_seq'];

    	$this->load->model('Keyword', 'kw', TRUE);
    	$this->load->model('Ranking', 'rk', TRUE);
    	$this->load->model('Memo',    'me', TRUE);

    	// バリデーション設定
    	$this->_set_validation();

    	// キーワード設定情報を取得
    	$get_kw_data =$this->kw->get_kw_seq($_kw_seq);

    	// メモ情報を取得
    	$get_me_data =$this->me->get_me_kwseq($_kw_seq);

    	// 順位データ情報を取得 (186日分) ＆ グラフ表示
    	$this->load->library('lib_ranking_data');
    	$this->lib_ranking_data->get_ranking_graph($_kw_seq, 186);

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->location_set();

    	$this->smarty->assign('info',     $get_kw_data[0]);
    	$this->smarty->assign('info_me',  $get_me_data);

    	$this->view('topdetail/month06.tpl');

    }

    // キーワード情報詳細 (1週間)
    public function week01()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

    	$_kw_seq = $input_post['chg_seq'];

    	$this->load->model('Keyword', 'kw', TRUE);
    	$this->load->model('Ranking', 'rk', TRUE);
    	$this->load->model('Memo',    'me', TRUE);

    	// バリデーション設定
    	$this->_set_validation();

    	// キーワード設定情報を取得
    	$get_kw_data =$this->kw->get_kw_seq($_kw_seq);

    	// メモ情報を取得
    	$get_me_data =$this->me->get_me_kwseq($_kw_seq);

    	// 順位データ情報を取得 (7日分) ＆ グラフ表示
    	$this->load->library('lib_ranking_data');
    	$this->lib_ranking_data->get_ranking_graph($_kw_seq, 7);

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->location_set();

    	$this->smarty->assign('info',     $get_kw_data[0]);
    	$this->smarty->assign('info_me',  $get_me_data);

    	$this->view('topdetail/week01.tpl');

    }

    // レポート作成
    public function report()
    {

//     	// セッションデータをクリア
//     	$this->load->library('lib_auth');
//     	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

    	if (!isset($input_post['chg_seq']))
    	{
    		show_404();
    	}

    	$this->load->model('Keyword', 'kw', TRUE);
    	$this->load->model('Ranking', 'rk', TRUE);
    	$this->load->model('Memo',    'me', TRUE);

    	// バリデーション設定
    	$this->_set_validation();

    	// キーワード設定情報を取得
    	$get_kw_data =$this->kw->get_kw_seq($input_post['chg_seq']);

    	// メモ情報を取得
    	$get_me_data =$this->me->get_me_kwseq($input_post['chg_seq']);

    	// 順位データ情報を取得 (31日分) ＆ レポート作成
    	$_nisuu = date('t');
    	$this->load->library('lib_ranking_data');
    	$this->lib_ranking_data->create_report_graph($input_post['chg_seq'], $_nisuu);
    	//$this->lib_ranking_data->create_report_graph($input_post['chg_seq'], 31);

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->location_set();

    	$this->smarty->assign('info',     $get_kw_data[0]);
    	$this->smarty->assign('info_me',  $get_me_data);

    	// 「戻る」のページャ先をセット
    	$page_cnt = $_SESSION['c_offset'];
    	$this->smarty->assign('seach_page_no',  $page_cnt);

    	// 「戻る」の画面先をセット
    	$this->smarty->assign('back_page', $_SESSION['c_back_set']);

    	$this->view('topdetail/report.tpl');

    }

    // 初期値セット
    private function _item_set()
    {

        // ステータスのセット
        $this->config->load('config_status');
        $opt_term = $this->config->item('KEYWORD_TERM');

        $this->smarty->assign('options_term',  $opt_term);

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}

