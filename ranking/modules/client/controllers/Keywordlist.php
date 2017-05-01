<?php

class Keywordlist extends MY_Controller
{

    /*
     *  キーワード情報管理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('err_date', FALSE);
        $this->smarty->assign('mess',     FALSE);

    }

    // キーワード検索一覧TOP
    public function index()
    {

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
            $tmp_inputpost = $this->input->post();

            // セッションをフラッシュデータとして保存
            $data = array(
            					'c_offset'     => $tmp_offset,
            );
            $this->session->set_userdata($data);
        } else {
            $tmp_offset = 0;
            $tmp_inputpost = array(
                                'kw_keyword'   => '',
                                'kw_domain'    => '',
            					'kw_status'    => '',
            					'orderid'      => '',
            );

            // セッションをフラッシュデータとして保存
            $data = array(
                                'c_kw_keyword' => '',
                                'c_kw_domain'  => '',
            					'c_kw_status'  => '',
            					'c_orderid'    => '',
            					'c_offset'     => $tmp_offset,
            );
            $this->session->set_userdata($data);
        }

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

        $this->view('keywordlist/index.tpl');

    }

    // 一覧表示
    public function search()
    {

        if ($this->input->post('submit') == '_submit')
        {
            // セッションをフラッシュデータとして保存
            $data = array(
                            'c_kw_keyword' => $this->input->post('kw_keyword'),
                            'c_kw_domain'  => $this->input->post('kw_domain'),
                            'c_kw_status'  => $this->input->post('kw_status'),
                            'c_orderid'    => $this->input->post('orderid'),
            );
            $this->session->set_userdata($data);

            $tmp_inputpost = $this->input->post();
            unset($tmp_inputpost["submit"]);

        } else {
            // セッションからフラッシュデータ読み込み
            $tmp_inputpost['kw_keyword']   = $_SESSION['c_kw_keyword'];
            $tmp_inputpost['kw_domain']    = $_SESSION['c_kw_domain'];
            $tmp_inputpost['kw_status']    = $_SESSION['c_kw_status'];
            $tmp_inputpost['orderid']      = $_SESSION['c_orderid'];
        }

        // バリデーション・チェック
        $this->_set_validation();                                               // バリデーション設定
        $this->form_validation->run();

        // Pagination 現在ページ数の取得：：URIセグメントの取得
        $segments = $this->uri->segment_array();
        if (isset($segments[3]))
        {
            $tmp_offset = $segments[3];
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

        $this->view('keywordlist/index.tpl');

    }

    // キーワード情報追加
    public function add()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');

        $input_post = $this->input->post();

        // バリデーション設定
        $this->_set_validation();

        // 初期値セット
        $this->_item_set();

        // ロケーションセット
        $this->load->library('lib_keyword');
        $this->lib_keyword->location_set();

        // 設定グループのセット
        $this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], "選択なし", 0);

        // 設定タグのセット
        $this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], "", 1);

        $this->smarty->assign('url_match',  3);									// URLマッチタイプデフォルト
        $this->smarty->assign('tmp_memo',   NULL);
        $this->smarty->assign('options_kw', NULL);

        $this->view('keywordlist/add.tpl');

    }

    // キーワード情報確認＆登録
    public function addchk()
    {

        $input_post = $this->input->post();

        $this->load->library('lib_keyword');

        // バリデーション・チェック
        $this->_set_validation02();
        if ($this->form_validation->run() == TRUE)
        {

        	$this->load->model('Location', 'lc', TRUE);
        	$this->load->model('Keyword',  'kw', TRUE);
        	$this->load->library('lib_rootdomain');

        	$set_data_kw = array();

        	$set_data_kw['kw_status']      = 1;
        	$set_data_kw['kw_matchtype']   = $input_post['kw_matchtype'];
        	$set_data_kw['kw_maxposition'] = $input_post['kw_maxposition'];
        	$set_data_kw['kw_trytimes']    = $input_post['kw_trytimes'];
        	$set_data_kw['kw_cl_seq']      = $_SESSION['c_memGrp'];
        	$set_data_kw['kw_ac_seq']      = $_SESSION['c_memSeq'];

        	// 対象URL情報の設定
        	$_tmp_url = $input_post['kw_url'];
        	$set_data_kw['kw_url'] = $_tmp_url;
         	$set_data_kw['kw_domain'] = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $_tmp_url);		// ドメイン

        	$_rootdomain = $this->lib_rootdomain->get_rootdomain($_tmp_url);
        	$set_data_kw['kw_rootdomain'] = $_rootdomain['rootdomain'];										// ルートドメイン

        	// タグ入力情報を分解＆生成＆セット
        	if (isset($input_post['kw_tag']))
        	{

        		$_kw_tag = "";
        		foreach ($input_post['kw_tag'] as $key => $value)
        		{
        			$_kw_tag .= "[" . $value . "]";
        		}
        		$this->_tag_set($set_data_kw['kw_cl_seq'], $_kw_tag);

        		$set_data_kw['kw_tag'] = $_kw_tag;

        	} else {

        		$this->_tag_set($set_data_kw['kw_cl_seq'], "");
        		$set_data_kw['kw_tag'] = "";

        	}

        	// キーワード作成
        	$this->lib_keyword->create_kw_data($input_post, $set_data_kw);

        	/*
        	 * ここは変えた方がいいかも？
        	 * ロジック？ or 仕様？
        	 */
        	// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
        	$this->lib_keyword->update_grooup_info_all($set_data_kw['kw_cl_seq'], 0);

        	// 新規に追加された設定タグをレコード追加
        	if (isset($input_post['kw_tag']))
        	{
	        	foreach ($input_post['kw_tag'] as $key => $value)
	        	{
	        		$get_gt_name = $this->gt->get_gt_name($value, $set_data_kw['kw_cl_seq'], 1);

	        		if (count($get_gt_name) == 0)
	        		{
	        			$set_gt_data['gt_name']   = $value;
	        			$set_gt_data['gt_cl_seq'] = $set_data_kw['kw_cl_seq'];
	        			$set_gt_data['gt_type']   = 1;

	        			// INSERT
	        			$this->gt->insert_group_tag($set_gt_data);
	        		}
	        	}

	        	// 全タグ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
	        	$this->lib_keyword->update_tag_info_all($set_data_kw['kw_cl_seq'], 1);
        	}

        	redirect('/keywordlist/');

        } else {

            $this->smarty->assign('mess', "<font color=red>項目に入力エラーが発生しました。</font>");

        }

        // 初期値セット
        $this->_item_set();

        // キーワードセット
        $opt_kw = '';
        if (isset($input_post['kw_keyword']))
        {
	        foreach ($input_post['kw_keyword'] as $key => $value)
	        {
	        	$opt_kw .= '<option selected="selected" value="' . $value . '">' . $value . '</option>';
	        }
        }
        $this->smarty->assign('options_kw', $opt_kw);

        // ロケーションセット
        if (isset($input_post['kw_location']))
        {
        	$this->lib_keyword->location_set($input_post['kw_location']);
        }

        // 設定グループのセット
        $this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], $input_post['kw_group'], 0);

        // 設定タグのセット
        // タグ入力情報を分解＆生成＆セット
        if (isset($input_post['kw_tag']))
        {

        	$_kw_tag = "";
        	foreach ($input_post['kw_tag'] as $key => $value)
        	{
        		$_kw_tag .= "[" . $value . "]";
        	}
        	$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], $_kw_tag, 1);

        } else {

        	$this->lib_keyword->grouptag_set($_SESSION['c_memGrp'], "", 1);

        }

		$this->smarty->assign('url_match', $input_post['kw_matchtype']);
		$this->smarty->assign('tmp_memo',  $input_post['kw_memo']);

        $this->view('keywordlist/add.tpl');

    }

    // キーワード情報編集
    public function chg()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');

    	$input_post = $this->input->post();

    	// バリデーション設定
    	$this->_set_validation();

    	// キーワード情報取得
    	$this->load->model('Keyword', 'kw', TRUE);
    	$get_kw_data =$this->kw->get_kw_seq($input_post['chg_seq']);

    	// メモ情報取得
    	$this->load->model('Memo',    'me', TRUE);
    	$get_me_data =$this->me->get_me_seq($input_post['chg_seq']);

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
    	$data = array(
		    			'c_old_group' => $get_kw_data[0]['kw_group'],
		    			'c_old_tag'   => $get_kw_data[0]['kw_tag'],
    	 );
    	$this->session->set_userdata($data);

    	$this->smarty->assign('info',     $get_kw_data[0]);
    	$this->smarty->assign('tmp_memo', NULL);
    	$this->smarty->assign('info_me',  $get_me_data);

    	$this->view('keywordlist/chg.tpl');

    }

    // キーワード情報編集確認
    public function chg_conf()
    {

    	$input_post = $this->input->post();

    	// 初期値セット
    	$this->_item_set();

    	// ロケーションセット
    	$this->load->library('lib_keyword');
    	$this->lib_keyword->location_set();

    	// 設定グループのセット
//     	$this->load->library('lib_keyword');
    	$this->lib_keyword->grouptag_set($input_post['kw_cl_seq'], $input_post['kw_group'], 0);

    	// 設定情報反映有無セット
    	$this->_reflection_set();

    	// バリデーション設定
    	$this->_set_validation03();
    	if ($this->form_validation->run() == FALSE)
    	{
    		$this->smarty->assign('mess', "<font color=red>項目に入力エラーが発生しました。</font>");

    		// メモ情報取得
    		$this->load->model('Memo',    'me', TRUE);
    		$get_me_data =$this->me->get_me_seq($input_post['kw_seq']);

    		$this->smarty->assign('info',     $input_post);

    		$this->smarty->assign('tmp_memo', $input_post['kw_memo']);
    		$this->smarty->assign('info_me',  $get_me_data);

    		$this->view('keywordlist/chg.tpl');

    	} else {

    		$this->load->model('Keyword', 'kw', TRUE);

    		// TAGチェック
    		if (!isset($input_post['kw_tag']))
    		{
    			// ダミー入力
    			$input_post['kw_tag'] = "";
    		}

    		// 同一URLのチェック
    		$get_url_data =$this->kw->get_kw_url($input_post);
    		if ((count($get_url_data) > 0) && ($input_post['kw_seq'] != $get_url_data[0]['kw_seq']))
    		{
    			$this->smarty->assign('mess', "<font color=red>同一URLが存在します。</font>");

    			$this->smarty->assign('info',     $input_post);
    			$this->smarty->assign('tmp_memo', $input_post['kw_memo']);

    			$this->view('keywordlist/chg.tpl');
    			return;
    		}

    		$this->smarty->assign('info', $input_post);

    		$this->view('keywordlist/chg_conf.tpl');

    	}

    }

    // キーワード情報編集完了
    public function chg_comp()
    {

    	$input_post = $this->input->post();

    	$set_kw_data = $input_post['info'];
    	unset($set_kw_data['submit']);




//     	print_r($set_kw_data);
//     	print("<br><br>");
//     	exit;



		// タグ入力情報を分解＆生成＆セット
    	if ($set_kw_data['kw_tag'] == "")
    	{
    		$this->_tag_set($set_kw_data['kw_cl_seq'], "");
    		$set_kw_data['kw_tag'] = "";
    	} else {

    		$_kw_tag = "";
    		foreach ($set_kw_data['kw_tag'] as $key => $value)
    		{
    			$_kw_tag .= "[" . $value . "]";
    		}
    		$this->_tag_set($set_kw_data['kw_cl_seq'], $_kw_tag);

    		$set_kw_data['kw_tag'] = $_kw_tag;
    	}

    	// バリデーション・チェック
    	$this->_set_validation();

    	// 「戻る」ボタン押下の場合
    	if ( $this->input->post('back') ) {

    		// 初期値セット
    		$this->_item_set();

    		// ロケーションセット
    		$this->load->library('lib_keyword');
    		$this->lib_keyword->location_set();

    		// メモ情報取得
    		$this->load->model('Memo', 'me', TRUE);
    		$get_me_data =$this->me->get_me_seq($set_kw_data['kw_seq']);

    		$this->smarty->assign('info',     $input_post['info']);
    		$this->smarty->assign('tmp_memo', $input_post['info']['kw_memo']);
    		$this->smarty->assign('info_me',  $get_me_data);

    		$this->view('keywordlist/chg.tpl');
    		return;
    	}

    	$this->load->model('Group_tag', 'gt', TRUE);
    	$this->load->library('lib_keyword');


    	// 設定内容の反映範囲
    	$this->lib_keyword->update_reflection($set_kw_data, $input_post['reflection']);

		/*
		 * ここは変えた方がいいかも？
		 * ロジック？ or 仕様？
		 */
		if ($input_post['reflection'] == 0)
		{

	    	// 新グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
	    	$this->lib_keyword->update_grooup_info($set_kw_data, 0);

	    	// 旧グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
	    	$set_kw_data['kw_group'] = $_SESSION['c_old_group'];
	    	$this->lib_keyword->update_grooup_info($set_kw_data, 0);

		} else {

			// 全グループ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
			$this->lib_keyword->update_grooup_info_all($set_kw_data['kw_cl_seq'], 0);

		}

		// 新規に追加された設定タグをレコード追加
		foreach ($input_post['info']['kw_tag'] as $key => $value)
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

		// 全タグ　を　tb_group_tag　へ INSERT or UPDATE (rootdomain , keyword数)
		$this->lib_keyword->update_tag_info_all($set_kw_data['kw_cl_seq'], 1);

    	redirect('/keywordlist/');

    }

    // ウォッチリストへの登録＆解除
    public function watchlist()
    {

    	$input_post = $this->input->post();

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

    	redirect("/keywordlist/search/$tmp_offset/");
//     	$this->view('keywordlist/index.tpl');

    }

    // Pagination 設定
    private function _get_Pagination($countall, $tmp_per_page)
    {

        $config['base_url']       = base_url() . '/keywordlist/search/';        // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
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

    	// キーワードID 並び替え選択項目セット
    	$arropt_id = array (
    			''     => '-- 選択してください --',
    			'DESC' => '降順',
    			'ASC'  => '昇順',
    	);

    	$this->smarty->assign('options_kw_status', $opt_kw_status);
    	$this->smarty->assign('options_orderid',   $arropt_id);

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

