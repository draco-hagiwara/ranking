<?php

class Keyworddetail extends MY_Controller
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
//         $this->load->library('lib_auth');
//         $this->lib_auth->delete_session('client');

        // バリデーション・チェック
        $this->_set_validation();                                                       // バリデーション設定

        $this->view('keyworddetail/index.tpl');

    }

    // キーワード情報詳細
    public function detail()
    {

//     	// セッションデータをクリア
//     	$this->load->library('lib_auth');
//     	$this->lib_auth->delete_session('client');



//     	print_r($_SESSION);


    	$input_post = $this->input->post();

    	// 削除選択時にPWエラーで戻ってきた場合の処理
    	$segments = $this->uri->segment_array();
    	if (isset($segments[3]))
    	{
    		$_kw_seq = $segments[3];
    		if (!is_numeric($_kw_seq))
    		{
    			//throw new Exception("例外発生！");
    			show_error('指定されたIDは不正です。');
    		}

    	} else {
    		if (!isset($input_post['chg_seq']))
    		{
    			show_404();
    		}

    		$_kw_seq = $input_post['chg_seq'];
    	}

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

    	$this->view('keyworddetail/detail.tpl');

    }

    // キーワード情報チェック
    public function detailchk()
    {

        $input_post = $this->input->post();

        // 前ページへ戻る
        if (isset($input_post['_back']))
        {
        	redirect('/keywordlist/');
        }

        // メモの削除
        if (isset($input_post['chg_seq']))
        {
        	$this->load->model('Keyword', 'kw', TRUE);
        	$this->load->model('Ranking', 'rk', TRUE);
        	$this->load->model('Memo',    'me', TRUE);

        	$get_me_data =$this->me->get_me_seq($input_post['chg_seq']);
        	$_kw_seq = $get_me_data[0]['me_kw_seq'];

        	// DELETE
        	$this->me->delete_me_seq($input_post['chg_seq']);

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

	        $this->view('keyworddetail/detail.tpl');
        }

    }

    // キーワード情報削除 (パスワード確認あり)
    public function del_pw()
    {

    	$input_post = $this->input->post();

    	if (!isset($input_post['submit']))
    	{
    		$segments = $this->uri->segment_array();

    		$this->load->model('Account',   'ac', TRUE);
    		$this->load->model('Keyword',   'kw', TRUE);
    		$this->load->model('Ranking',   'rk', TRUE);
    		$this->load->model('Memo',      'me', TRUE);
    		$this->load->model('Watchlist', 'wt', TRUE);
    		$this->load->library('lib_auth');
    		$this->load->library('lib_keyword');
    		$this->load->library('lib_rootdomain');

    		$get_ac_data = $this->ac->get_ac_seq($_SESSION['c_memSeq']);

    		// パスワードのチェック
    		$res = $this->lib_auth->_check_password($segments[4], $get_ac_data[0]['ac_pw']);
    		if ($res == TRUE)
    		{
    			//print('入力されたログインID（メールアドレス）またはパスワードが間違っています。');
    			$this->smarty->assign('kw_seq', $segments[3]);
    		} else {

    			// トランザクション・START
    			$this->db->trans_strict(FALSE);                                     // StrictモードをOFF
    			$this->db->trans_start();                                           // trans_begin

	    			// ルートドメインの削除準備
	    			$get_kw_info = $this->kw->get_kw_seq($segments[3]);

	    			// DELETE：キーワード
	    			$this->kw->delete_keyword($segments[3], $_SESSION['c_memGrp']);

	    			// DELETE：ランキング
	    			$this->rk->delete_ranking($segments[3], $_SESSION['c_memGrp']);

	    			// DELETE：メモ
	    			$this->me->delete_memo($segments[3], $_SESSION['c_memGrp']);

	    			// DELETE：ウォッチリスト
	    			$this->wt->delete_wt_list($segments[3], $_SESSION['c_memGrp']);

	    			// グループ＆タグの再集計
	    			$this->lib_keyword->update_group_info_all($_SESSION['c_memGrp'], 0);
	    			$this->lib_keyword->update_tag_info_all($_SESSION['c_memGrp'], 1);

    			// トランザクション・COMMIT
    			$this->db->trans_complete();                                        // trans_rollback & trans_commit
    			if ($this->db->trans_status() === FALSE)
    			{
    				//$this->smarty->assign('mess',  "トランザクションエラーが発生しました。");
    				log_message('error', 'client::[keyworddetail->del_pw()]キーワード削除(PW)処理 トランザクションエラー');
    			} else {
    				//$this->smarty->assign('mess',  "更新が完了しました。");
    			}

    			// ルートドメインの削除有無
    			$this->lib_rootdomain->get_rootdomain_del($get_kw_info[0]['kw_cl_seq'], $get_kw_info[0]['kw_rootdomain']);

    			redirect('/keywordlist/');
    		}

    	} else {
    		$this->smarty->assign('kw_seq', $input_post['kw_seq']);
    	}

    	$this->view('keyworddetail/del_pw.tpl');

    }

    // レポート作成
    public function report()
    {

    	// セッションデータをクリア
//     	$this->load->library('lib_auth');
//     	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

    	if (isset($input_post['_back']))
    	{
    		redirect('/keywordlist/');
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
    	$this->load->library('lib_ranking_data');
    	$this->lib_ranking_data->create_report_graph($input_post['chg_seq'], 31);

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->location_set();

    	$this->smarty->assign('info',     $get_kw_data[0]);
    	$this->smarty->assign('info_me',  $get_me_data);

    	$this->view('keyworddetail/report.tpl');

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

