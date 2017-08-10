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
//         $this->load->library('lib_auth');
//         $this->lib_auth->delete_session('client');

        // バリデーション・チェック
        $this->_set_validation();                                                       // バリデーション設定

        $this->view('topdetail/index.tpl');

    }

    // キーワード詳細
    public function detail()
    {

    	// セッションデータをクリア
//     	$this->load->library('lib_auth');
//     	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

    	if (!isset($input_post['chg_seq']))
    	{
    		show_404();
    	}

		$_kw_seq = $input_post['chg_seq'];

		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Ranking',   'rk', TRUE);
		$this->load->model('Memo',      'me', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);
		$this->load->library('lib_ranking_data');
		$this->load->library('lib_keyword');

		// バリデーション設定
		$this->_set_validation();

		// キーワード設定情報を取得
		$get_kw_data =$this->kw->get_kw_seq($_kw_seq);

		// メモ情報を取得
		$get_me_data =$this->me->get_me_kwseq($_kw_seq);

		// ウォッチリスト情報を取得
		$get_wt_data =$this->wt->get_watchlist_data($_SESSION['c_memSeq'], $get_kw_data[0]['kw_cl_seq'], $_kw_seq);


		// 順位データ情報を取得 ＆ グラフ表示
		//$date2 = new DateTIme();
		//$date1 = new DateTime($get_kw_data[0]['kw_create_date']);
		//$diff_date = $date1->diff($date2);
		//$_date_cnt = $diff_date->format('%a');

		//if ($_date_cnt <= 30)
		//{
		//	// 最低表示日付
		//	$_date_cnt = 30;
		//}
		//$this->lib_ranking_data->get_jqplot_graph($_kw_seq, $_date_cnt);





		// グラフ＆レポート表示期間のセット
// 		$this->config->load('config_status');
// 		$_graph_term = $this->config->item('KEYWORD_GRAPH_TERM');
		$this->lib_ranking_data->get_jqplot_graph($_kw_seq, "0-0");




		// ロケーションセット
		$this->lib_keyword->location_set();

		// 初期値セット
		$this->_item_set();



// 		// 表示期間の日付
// 		$date = new DateTime();
// 		$_start_date   = $date->format('Y-m-d');
// 		$_set_date_cnt = "- " . ($_date_cnt - 1) . " days";
// 		$_end_date     = $date->modify($_set_date_cnt)->format('Y-m-d');

		$this->smarty->assign('info',       $get_kw_data[0]);
		$this->smarty->assign('info_me',    $get_me_data);
		if (empty($get_wt_data))
		{
			$this->smarty->assign('wt_seq', NULL);
		} else {
			$this->smarty->assign('wt_seq', $get_wt_data[0]['wt_seq']);
		}

// 		$this->smarty->assign('start_date', $_start_date);
// 		$this->smarty->assign('end_date',   $_end_date);

		// 「戻る」のページャ先をセット
		$page_cnt = $_SESSION['c_offset'];
		$this->smarty->assign('seach_page_no', $page_cnt);

		// 「戻る」の画面先をセット
		$this->smarty->assign('back_page', $_SESSION['c_back_set']);




    	$this->view('topdetail/detail.tpl');

    }

    // キーワード情報編集
    public function chg()
    {

    	//     	// セッションデータをクリア
    	//     	$this->load->library('lib_auth');
    	//     	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

    	//     	if (!isset($input_post['chg_seq']))
    	//     	{
    	//     		show_404();
    	//     	}

    	// 削除選択時にPWエラーで戻ってきた場合の処理
    	$segments = $this->uri->segment_array();
    	if (isset($segments[3]))
    	{
    		$kw_seq = $segments[3];
    		if (!is_numeric($kw_seq))
    		{
    			//throw new Exception("例外発生！");
    			show_error('指定されたIDは不正です。');
    		}

    	} else {
    		if (!isset($input_post['chg_seq']))
    		{
    			show_404();
    		}

    		// メモ削除の有無判定 ： 無理やりここに削除処理！？
    		if (isset($input_post['back_page']))
    		{
    			$kw_seq = $input_post['kw_seq'];

    			$this->load->model('Memo', 'me', TRUE);
    			$get_me_data =$this->me->get_me_seq($input_post['chg_seq']);

    			// DELETE
    			$this->me->delete_me_seq($input_post['chg_seq']);

    		} else {
    			$kw_seq = $input_post['chg_seq'];
    		}

    		//     		$_kw_seq = $input_post['chg_seq'];
    	}





    	// バリデーション設定
    	$this->_set_validation();

    	// キーワード情報取得
    	$this->load->model('Keyword', 'kw', TRUE);
    	$get_kw_data =$this->kw->get_kw_seq($kw_seq);

    	// メモ情報取得
    	$this->load->model('Memo',    'me', TRUE);
    	$get_me_data =$this->me->get_me_kwseq($kw_seq);

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->location_set();

    	// 設定グループのセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->grouptag_set($get_kw_data[0]['kw_cl_seq'], $get_kw_data[0]['kw_group'], 0);

    	// 設定タグのセット
    	$this->lib_keyword->grouptag_set($get_kw_data[0]['kw_cl_seq'], $get_kw_data[0]['kw_tag'], 1);
    	//     	$data = array(
    	//     			'c_old_group' => $get_kw_data[0]['kw_group'],
    	//     			'c_old_tag'   => $get_kw_data[0]['kw_tag'],
    	//     	);
    	//     	$this->session->set_userdata($data);

    	// 設定情報反映有無セット
    	$this->_reflection_set();

    	$this->smarty->assign('info',     $get_kw_data[0]);
    	$this->smarty->assign('tmp_memo', NULL);
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

    	$this->view('topdetail/chg.tpl');

    }

    // キーワード情報編集
    public function chg_chk()
    {

    	$input_post = $this->input->post();


    	//     	print_r($input_post);
    	//     	print("<br><br>");
    	//     	print_r($_SESSION);
    	//     	print("<br><br>");


    	if ($this->input->post('_submit') == '_back')
    	{
    		redirect('/' . $input_post['back_page'] . '/search/' . $input_post['seach_page_no']);
    		//redirect('/' . $_SESSION['c_back_set'] . '/search/' . $page_cnt);
    	}

    	$this->load->model('Memo', 'me', TRUE);
    	$this->load->library('lib_keyword');

    	// バリデーション設定
    	$this->_set_validation03();
    	if ($this->form_validation->run() == FALSE)
    	{
    		$this->smarty->assign('mess', "<font color=red>項目に入力エラーが発生しました。</font>");

    		// 初期値セット
    		$this->_set_chg_init($input_post);

    		// メモ情報取得
    		$get_me_data =$this->me->get_me_kwseq($input_post['kw_seq']);
    		$this->smarty->assign('tmp_memo', $input_post['kw_memo']);
    		$this->smarty->assign('info_me',  $get_me_data);

    		$this->smarty->assign('info',     $input_post);

    		$this->view('topdetail/chg.tpl');
    		return;

    	} else {

    		// 対象URL + 補正
    		preg_match_all("/\//", $input_post['kw_url'], $cnt_slash) ;
    		if (count($cnt_slash[0]) == 2)
    		{
    			$input_post['kw_url'] = $input_post['kw_url'] . "/";
    		}

    		$set_kw_data = $input_post;
    		unset($set_kw_data['reflection']);
    		unset($set_kw_data['back_page']);
    		unset($set_kw_data['seach_page_no']);
    		unset($set_kw_data['_submit']);

    		// 入力グループ設定チェック
    		if (isset($set_kw_data['kw_group']))
    		{
    			$set_kw_data['kw_group'] = $set_kw_data['kw_group'][0];
    		} else {
    			$set_kw_data['kw_group'] = NULL;
    		}

    		// 入力タグ設定チェック
    		if (!isset($set_kw_data['kw_tag']))
    		{
    			// ダミー入力
    			$set_kw_data['kw_tag'] = NULL;
    		}

    		// ** 旧URL情報を別レコードとして保存するかチェック
    		$this->load->model('Keyword', 'kw', TRUE);
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
    						$this->smarty->assign('mess', "<font color=red>同一URLの設定が存在します。</font>");

    						// 初期値セット
    						$this->_set_chg_init($input_post);

    						$get_me_data =$this->me->get_me_kwseq($set_kw_data['kw_seq']);

    						$this->smarty->assign('info',     $set_kw_data);
    						$this->smarty->assign('tmp_memo', $set_kw_data['kw_memo']);
    						$this->smarty->assign('info_me',  $get_me_data);

    						$this->view('topdetail/chg.tpl');
    						return;
    					}
    				}
    			}
    		}

    		// タグ入力情報を分解＆生成＆セット
    		if ($set_kw_data['kw_tag'] == "")
    		{
    			$this->_tag_set($set_kw_data['kw_cl_seq'], "");
    			$set_kw_data['kw_tag'] = "";
    		} else {

    			if (is_array($set_kw_data['kw_tag']))
    			{
    				$_kw_tag = "";
    				foreach ($set_kw_data['kw_tag'] as $key => $value)
    				{
    					$_kw_tag .= "[" . $value . "]";
    				}
    				$this->_tag_set($set_kw_data['kw_cl_seq'], $_kw_tag);
    			} else {
    				$_kw_tag = $set_kw_data['kw_tag'];
    			}

    			$set_kw_data['kw_tag'] = $_kw_tag;
    		}

    		// バリデーション・チェック
    		$this->_set_validation();

    		$this->load->model('Group_tag', 'gt', TRUE);
    		$this->load->library('lib_rootdomain');

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
    				|| ($set_kw_data['kw_matchtype'] != $get_old_kw_data[0]['kw_matchtype']))
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

//     		if (($set_kw_data['kw_url'] != $get_old_kw_data[0]['kw_url'])
//     				|| ($set_kw_data['kw_matchtype'] != $get_old_kw_data[0]['kw_matchtype']))
//     		{
//     			$get_old_kw_data[0]['kw_status'] = 1;
//     			unset($get_old_kw_data[0]['kw_seq']);

//     			$this->kw->insert_keyword($get_old_kw_data[0]);
//     		}





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

    		// ** 設定内容の反映範囲
    		$this->lib_keyword->update_reflection($set_kw_data, $input_post['reflection']);

//     		// ** 新URL情報と旧URL情報をチェック
//     		$get_url_check = $this->kw->check_url($set_kw_data, $old_seq=NULL, $status=1);
//     		if (count($get_url_check) >= 1)
//     		{
//     			// status を書き換え
//     			$set_kw_data['kw_status'] = 0;
//     			$this->kw->update_keyword($set_kw_data);
//     		} else {
//     			// ** 設定内容の反映範囲
//     			$this->lib_keyword->update_reflection($set_kw_data, $input_post['reflection']);
//     		}








    		// トランザクション・COMMIT
    		$this->db->trans_complete();                                        // trans_rollback & trans_commit
    		if ($this->db->trans_status() === FALSE)
    		{
    			//$this->smarty->assign('mess',  "トランザクションエラーが発生しました。");
    			log_message('error', 'client::[keywordlist->chg_comp()]キーワード編集処理 トランザクションエラー');

    			redirect('/keywordlist/');
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
    		if ($input_post['kw_group'][0] != "")
    		{
    			$get_gt_name = $this->gt->get_gt_name($input_post['kw_group'][0], $set_kw_data['kw_cl_seq'], 0);

    			if (count($get_gt_name) == 0)
    			{
    				$set_gt_data['gt_name']   = $input_post['kw_group'][0];
    				$set_gt_data['gt_cl_seq'] = $set_kw_data['kw_cl_seq'];
    				$set_gt_data['gt_type']   = 0;

    				// INSERT
    				$this->gt->insert_group_tag($set_gt_data);
    			}
    		}

    		// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
    		$this->lib_keyword->update_group_info_all($set_kw_data['kw_cl_seq'], 0);

    		// 新規に追加された設定タグをレコード追加
    		if ($input_post['kw_tag'] != "")
    		{
    			foreach ($input_post['kw_tag'] as $key => $value)
    			{
    				$get_gt_name = $this->gt->get_gt_name($value, $set_kw_data['kw_cl_seq'], 1);

    				if (count($get_gt_name) == 0)
    				{
    					$set_gt_data['gt_name']   = $value;
    					$set_gt_data['gt_cl_seq'] = $set_kw_data['kw_cl_seq'];
    					$set_gt_data['gt_type']   = 1;

    					// INSERT
    					$this->gt->insert_group_tag($set_gt_data);
    				}
    			}
    		}

    		// 全タグ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
    		$this->lib_keyword->update_tag_info_all($set_kw_data['kw_cl_seq'], 1);

    	}

    	// リダイレクト先のページャをセット
    	if (isset($_SESSION['c_offset']))
    	{
    		$page_cnt = $_SESSION['c_offset'];
    	} else {
    		$page_cnt = 0;
    	}

    	redirect('/' . $_SESSION['c_back_set'] . '/search/' . $page_cnt);
    	//     	redirect('/keywordlist/search/' . $page_cnt);

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

    			redirect('/top/');
    		}

    	} else {
    		$this->smarty->assign('kw_seq', $input_post['kw_seq']);
    	}

    	$this->view('topdetail/del_pw.tpl');

    }


    // レポート作成
    public function report()
    {

    	$input_post = $this->input->post();

    	if (isset($input_post['chg_seq']))
    	{

    		// kw_seqをフラッシュデータとして保存
    		$data = array(
    				'c_report_kwseq' => $this->input->post('chg_seq'),
    		);
    		$this->session->set_userdata($data);

    		$report_kwseq = $input_post['chg_seq'];
    		$_term = "1-1";																// デフォルト(今月分(G+Y))
    		//$_term = "2-1";															// デフォルト(前月分(G+Y))

    	} else {

    		// kw_seq を呼び出し
    		$report_kwseq = $_SESSION['c_report_kwseq'];

    		// URIセグメントの取得
    		$segments = $this->uri->segment_array();
    		if (isset($segments[3]) && isset($report_kwseq))
    		{

    			// グラフ＆レポート表示期間のセット
    			$_term = $segments[3];

    		} else {
    			show_404();
    		}

    	}

    	$this->load->model('Keyword',   'kw', TRUE);
    	$this->load->model('Ranking',   'rk', TRUE);
    	$this->load->model('Memo',      'me', TRUE);
    	$this->load->model('Watchlist', 'wt', TRUE);
    	$this->load->library('lib_ranking_data');

    	// バリデーション設定
    	$this->_set_validation();

    	// キーワード設定情報を取得
    	$get_kw_data =$this->kw->get_kw_seq($report_kwseq);

    	// メモ情報を取得
    	$get_me_data =$this->me->get_me_kwseq($report_kwseq);

    	// ウォッチリスト情報を取得
    	$get_wt_data =$this->wt->get_watchlist_data($_SESSION['c_memSeq'], $_SESSION['c_memGrp'], $report_kwseq);

    	// グラフ＆レポート表示期間のセット (今月分(G+Y))
    	$result = $this->lib_ranking_data->get_jqplot_graph($report_kwseq, $_term);

    	if ($result)
    	{
    		$gp_kind = explode("-", $_term);
    		$this->smarty->assign('gp_kind',     $gp_kind[1]);
    	} else {
    		show_404();
    	}

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->location_set();

    	$this->smarty->assign('info',     $get_kw_data[0]);
    	$this->smarty->assign('info_me',  $get_me_data);

    	if (empty($get_wt_data))
    	{
    		$this->smarty->assign('wt_seq', NULL);
    	} else {
    		$this->smarty->assign('wt_seq', $get_wt_data[0]['wt_seq']);
    	}

    	// 「戻る」のページャ先をセット
    	$page_cnt = $_SESSION['c_offset'];
    	$this->smarty->assign('seach_page_no',  $page_cnt);

    	// 「戻る」の画面先をセット
    	$this->smarty->assign('back_page', $_SESSION['c_back_set']);

    	$this->smarty->assign('term',  $_term);
    	$this->view('topdetail/report.tpl');

    }

    // 初期値セット
    private function _item_set()
    {

    	// ステータスのセット
    	$this->config->load('config_status');
    	$opt_kw_status = $this->config->item('KEYWORD_KW_STATUS');
    	$opt_term = $this->config->item('KEYWORD_GRAPH_TERM');

    	// グラフ表示月
    	$date = new DateTime();
    	for ($i=0; $i<=5; $i++)
    	{
    		$_gp_month = $date->modify("-1 month")->format('m');
    		$this->smarty->assign('gp_month' . $i,  $_gp_month);
    	}

    	// 最大取得順位
    	$opt_kw_maxposition = $this->config->item('KEYWORD_KW_MAXPOSITION');

    	// 最大取得順位
    	$opt_kw_trytimes = $this->config->item('KEYWORD_KW_TRYTIMES');

    	$this->smarty->assign('options_kw_status',      $opt_kw_status);
    	$this->smarty->assign('options_kw_maxposition', $opt_kw_maxposition);
    	$this->smarty->assign('options_kw_trytimes',    $opt_kw_trytimes);
    	$this->smarty->assign('options_term',           $opt_term);

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

    // 編集画面での初期化
    private function _set_chg_init($input_post)
    {

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->lib_keyword->location_set();

    	// 設定グループのセット
    	$_input_group = "";
    	if (isset($input_post['kw_group']))
    	{
    		foreach ($input_post['kw_group'] as $key => $value)
    		{
    			$_input_group .= "[" . $value . "]";
    		}
    	}
    	$this->lib_keyword->grouptag_set($input_post['kw_cl_seq'], $_input_group, 0);

    	// 設定タグのセット
    	$_input_tag = "";
    	if (isset($input_post['kw_tag']))
    	{
    		foreach ($input_post['kw_tag'] as $key => $value)
    		{
    			$_input_tag .= "[" . $value . "]";
    		}
    	}
    	$this->lib_keyword->grouptag_set($input_post['kw_cl_seq'], $_input_tag, 1);

    	// 設定情報反映有無セット
    	$this->_reflection_set();

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

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック : フルチェック
    private function _set_validation02()
    {
    	$rule_set = array(
    	//                 array(
    			//                         'field'   => 'kw_status',
    			//                         'label'   => 'ステータス選択',
    			//                         'rules'   => 'trim|required|max_length[1]|is_numeric'
    			//                 ),
    			array(
    					'field'   => 'kw_keyword[]',
    					'label'   => '検索キーワード',
    					'rules'   => 'callback_check_keyword'
    			),
    			array(
    					'field'   => 'kw_url',
    					'label'   => '対象URL',
    					'rules'   => 'trim|required|regex_match[/^(https?)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/]|max_length[510]'
    			),
    			array(
    					'field'   => 'kw_matchtype',
    					'label'   => 'URLマッチタイプ',
    					'rules'   => 'trim|required|is_numeric'
    			),
    			array(
    					'field'   => 'chkengine[]',
    					'label'   => '検索エンジン',
    					'rules'   => 'callback_check_engine'
    			),
    			array(
    					'field'   => 'chkdevice[]',
    					'label'   => '取得対象デバイス',
    					'rules'   => 'callback_check_device'
    			),
    			array(
    					'field'   => 'kw_location[]',
    					'label'   => 'ロケーション',
    					'rules'   => 'callback_check_location'
    			),
    			array(
    					'field'   => 'kw_tag',
    					'label'   => 'タグ設定',
    					'rules'   => 'trim|max_length[1000]'
    			),
    			array(
    					'field'   => 'kw_memo',
    					'label'   => 'メモ',
    					'rules'   => 'trim|max_length[1000]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

    function check_keyword($arr,$max)
    {
    	if (isset($arr))
    	{
    		return TRUE;
    	} else {
    		$this->form_validation->set_message('check_keyword', '検索キーワードは必須選択です。');
    		return FALSE;
    	}
    }

    function check_location($arr,$max)
    {
    	if (isset($arr))
    	{
    		return TRUE;
    	} else {
    		$this->form_validation->set_message('check_location', 'ロケーションは必須選択です。');
    		return FALSE;
    	}
    }

    function check_engine($arr,$max)
    {
    	if (isset($arr))
    	{
    		return TRUE;
    	} else {
    		$this->form_validation->set_message('check_engine', '検索エンジンは必須選択です。');
    		return FALSE;
    	}
    }

    function check_device($arr,$max)
    {
    	if (isset($arr))
    	{
    		return TRUE;
    	} else {
    		$this->form_validation->set_message('check_device', '取得対象デバイスは必須選択です。');
    		return FALSE;
    	}
    }

    // フォーム・バリデーションチェック : フルチェック
    private function _set_validation03()
    {
    	$rule_set = array(
    			array(
    					'field'   => 'kw_status',
    					'label'   => 'ステータス選択',
    					'rules'   => 'trim|required|max_length[1]|is_numeric'
    			),
    			array(
    					'field'   => 'kw_url',
    					'label'   => '対象URL',
    					'rules'   => 'trim|required|regex_match[/^(https?)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/]|max_length[510]'
    			),
    			array(
    					'field'   => 'kw_matchtype',
    					'label'   => 'URLマッチタイプ',
    					'rules'   => 'trim|required|is_numeric'
    			),
    			array(
    					'field'   => 'kw_tag',
    					'label'   => 'タグ設定',
    					'rules'   => 'trim|max_length[1000]'
    			),
    			array(
    					'field'   => 'kw_memo',
    					'label'   => 'メモ設定',
    					'rules'   => 'trim|max_length[1000]'
    			),
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}
