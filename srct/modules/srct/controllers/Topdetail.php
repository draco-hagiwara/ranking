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

	// キーワード情報削除 (パスワード確認なし)
	public function delchk()
	{

		// URL直打ち禁止
		if ($_SESSION['c_memType'] >= 2)
		{
			show_404();
		}

		$input_post = $this->input->post();


		$_tmp_kw_seq = trim($input_post['kw_seq'], "[");
		$_tmp_kw_seq = trim($_tmp_kw_seq, "]");

		$_tmp_kw_seq = explode(",", $_tmp_kw_seq);

		//     	print_r($_tmp_kw_seq);

		$this->load->model('Keyword',   'kw', TRUE);

		// 一つのseqから仲間3人を見つける！
		$_arr_kw_seq = array();
		foreach ($_tmp_kw_seq as $key => $value)
		{
			$get_kw_data = $this->kw->get_kw_seq($value);

			// ３キーワード（google-PC/Mobile/Yahoo!）情報を取得
			$set_kw_info['kw_cl_seq']      = $get_kw_data[0]['kw_cl_seq'];
			$set_kw_info['kw_url']         = $get_kw_data[0]['kw_url'];
			//$set_kw_info['kw_domain']      = $get_kw_data[0]['kw_domain'];
			//$set_kw_info['kw_rootdomain']  = $get_kw_data[0]['kw_rootdomain'];
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

		if (!isset($input_post['submit']))
		{
			$this->load->model('Account',   'ac', TRUE);
			$this->load->model('Ranking',   'rk', TRUE);
			$this->load->model('Watchlist', 'wt', TRUE);
			$this->load->library('lib_auth');
			$this->load->library('lib_keyword');
			$this->load->library('lib_rootdomain');

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
				$this->lib_keyword->update_tag_info_all($_SESSION['c_memGrp'], 1);
			}

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

		} else {
			$this->smarty->assign('kw_seq', $input_post['kw_seq']);
		}

		redirect('/top/');

	}

	// レポート作成
	public function report()
	{

		$input_post = $this->input->post();

		//     	print_r($input_post);
		//     	print("<br><br>");

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


		//     	print_r($kw_seq_list);


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

		//     	$this->smarty->assign('info',     $get_kw_data[0]);

		$this->smarty->assign('term',  $_term);
		$this->smarty->assign('list_kw',  $_tmp_kw_seq);

		//     	$this->smarty->assign('arr_kwseq',  $input_post['kw_seq']);					// レポートダウンロード用（→セッションで）

		$this->view('topdetail/report.tpl');

	}

	// ajax用ダミーページ : キーワード一覧でのグラフ表示
	public function index_aj_jqPlot()
	{

		$input_post = $this->input->post();

// 		print_r($input_post['kw_seq']);
// 		var_dump($input_post);


		$_arr_kw_seq[0]['kw_seq'] = $input_post['kw_seq'];

		// *** グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($_arr_kw_seq, $_term=0);



		$this->smarty->assign('cnt',  $input_post['cnt']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_jqPlot.tpl');

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
		$tmp_offset = 0;
		$data = array(
				'c_free_keyword' => '',
				'c_free_rd'      => $input_post['kw_rootdomain'],
				'c_free_group'   => '',
				'c_offset'       => $tmp_offset,
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

		list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($tmp_inputpost, $tmp_per_page=0, $tmp_offset, $_tmp_cl);

		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		$this->smarty->assign('tmp_sort', NULL);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_rd.tpl');

	}

	// ajax用ダミーページ : ルートドメイン選択からのキーワード一覧表示
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
		$tmp_offset = 0;
		$data = array(
				'c_free_keyword' => '',
				'c_free_rd'      => '',
				'c_free_group'   => $input_post['kw_group'],
				'c_offset'       => $tmp_offset,
				'c_back_set'     => "searchrank",
		);
		$this->session->set_userdata($data);


		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		$tmp_inputpost['kw_group'] = $input_post['kw_group'];
// 		$tmp_inputpost['free_keyword'] = $input_post['kw_group'];

		list($kw_list, $group_countall, $kw_countall) = $this->kw->get_kw_grouplist($tmp_inputpost, $tmp_per_page=0, $tmp_offset, $_tmp_cl);

		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_group.tpl');

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


		// 		    	print_r($input_post);
// 				    	var_dump($input_post);




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
// 		$kw_seq = $input_post['kw_seq'][0];

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


// 		    	print_r($input_post);
// 		    	var_dump($input_post);




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
// 			Array (
// 					[kw_seq] => [67,64,79]
// 					[kw_status] => 1
// 					[kw_url] => http://www.test7.com/
// 					[kw_group] => Array (
// 							[0] => test )
// 					[kw_matchtype] => 0
// 					[kw_location_id] => 2392
// 					[kw_cl_seq] => 3
// 					[kw_ac_seq] => 11 )

			// キーワード情報の重複チェック:check_keywordに必要データ
// 			kw_cl_seq = ' . $setdata['kw_cl_seq'] . '
// 			AND kw_url = \'' . $setdata['kw_url'] . '\'
// 			AND kw_keyword = \'' . $setdata['kw_keyword'] . '\'
// 			AND kw_matchtype = ' . $setdata['kw_matchtype'] . '
// 			AND kw_searchengine = ' . $setdata['kw_searchengine'] . '
// 			AND kw_device = ' . $setdata['kw_device'] . '
// 			AND kw_location_id = \'' . $setdata['kw_location_id'] . '\'

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

// 			$kw_seq_list = array();
// 			foreach ($_tmp_kw_seq as $key => $val)
// 			{
// 				$kw_seq_list[$key]['kw_seq'] = $val;
// 			}
// 			print_r($kw_seq_list);
// print_r($_tmp_kw_seq);


			// *** 一つのseqから仲間 3人を見つける！
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


// var_dump($arr_kw_pair);
// print_r($arr_kw_pair);


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


// var_dump($set_kw_data);
// print_r($val1['kw_seq']);
// print("/");
// print_r($set_kw_data['kw_seq']);
// print("\n");


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
					//$this->lib_keyword->update_reflection($set_kw_data, $input_post['reflection']);

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
					//if ($input_post['kw_group'][0] != "")
					if (!empty($set_kw_data['kw_group']))
					{
						$get_gt_name = $this->gt->get_gt_name($set_kw_data['kw_group'], $set_kw_data['kw_cl_seq'], 0);
						//$get_gt_name = $this->gt->get_gt_name($input_post['kw_group'][0], $set_kw_data['kw_cl_seq'], 0);

						if (count($get_gt_name) == 0)
						{
							$set_gt_data['gt_name']   = $set_kw_data['kw_group'];
							//$set_gt_data['gt_name']   = $input_post['kw_group'][0];
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


// 			print($_tmp_mess);
// 			print("<br>");
// 			print_r($jsonArray);
// 			print("<br><br>");

			//$this->smarty->assign('mess', $_tmp_mess);

// 			$jsonArray = array(
// 					array(
// 							'title'   => '対象URL',
// 							'message' => '同一URLの設定が存在します。',
// 					),
// 			);

// 			$_tmp_json = sprintf("callback(%s)",json_encode($jsonArray));
// 			$this->smarty->assign('mess', $_tmp_json);
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
		$this->smarty->assign('list', $get_kw_data);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_kwdelete.tpl');

	}

	// ajax用ダミーページ : index_aj_kwdelete --> キーワード削除チェック
	public function index_aj_kwdelete_chk()
	{

		$input_post = $this->input->post();
		// 		$kw_seq = $input_post['kw_seq'][0];

				print_r($input_post['kw_seq']);
				var_dump($input_post);





				echo ("delete_chk_test");


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

	// ajax用ダミーページ : 「ウォッチリスト」昇順&降順 切り替え操作
	public function index_aj_wlsort()
	{

		$input_post = $this->input->post();


		//     	var_dump($input_post);
		//     	print_r($_SESSION['c_free_rd']);



		// バリデーション・チェック
		$this->_set_validation();

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if (empty($_SESSION['c_free_group']))
		{
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($arr_search_item, $tmp_per_page=0, $tmp_offset=0, $_tmp_cl);
		} else {


		}


		//     	var_dump($kw_list);


		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		//     	$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		$this->smarty->assign('tmp_item', $input_post['tmp_item']);
		$this->smarty->assign('tmp_sort', $input_post['tmp_sort']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_sort.tpl');

	}

	// ajax用ダミーページ : 「グループ」昇順&降順 切り替え操作
	public function index_aj_groupsort()
	{

		$input_post = $this->input->post();


		//     	var_dump($input_post);
		//     	print_r($_SESSION['c_free_rd']);



		// バリデーション・チェック
		$this->_set_validation();

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if (empty($_SESSION['c_free_group']))
		{
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($arr_search_item, $tmp_per_page=0, $tmp_offset=0, $_tmp_cl);
		} else {


		}


		//     	var_dump($kw_list);


		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		//     	$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		$this->smarty->assign('tmp_item', $input_post['tmp_item']);
		$this->smarty->assign('tmp_sort', $input_post['tmp_sort']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_sort.tpl');

	}

	// ajax用ダミーページ : 「キーワード」昇順&降順 切り替え操作
	public function index_aj_keywordsort()
	{

		$input_post = $this->input->post();


		//     	var_dump($input_post);
		//     	print_r($_SESSION['c_free_rd']);



		// バリデーション・チェック
		$this->_set_validation();

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if (empty($_SESSION['c_free_group']))
		{
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($arr_search_item, $tmp_per_page=0, $tmp_offset=0, $_tmp_cl);
		} else {


		}


		//     	var_dump($kw_list);


		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		//     	$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		$this->smarty->assign('tmp_item', $input_post['tmp_item']);
		$this->smarty->assign('tmp_sort', $input_post['tmp_sort']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_sort.tpl');

	}

	// ajax用ダミーページ : 「URL」昇順&降順 切り替え操作
	public function index_aj_urlsort()
	{

		$input_post = $this->input->post();


		//     	var_dump($input_post);
		//     	print_r($_SESSION['c_free_rd']);



		// バリデーション・チェック
		$this->_set_validation();

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if (empty($_SESSION['c_free_group']))
		{
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($arr_search_item, $tmp_per_page=0, $tmp_offset=0, $_tmp_cl);
		} else {


		}


		//     	var_dump($kw_list);


		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		//     	$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		$this->smarty->assign('tmp_item', $input_post['tmp_item']);
		$this->smarty->assign('tmp_sort', $input_post['tmp_sort']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_sort.tpl');

	}

	// ajax用ダミーページ : 「location」昇順&降順 切り替え操作
	public function index_aj_locationsort()
	{

		$input_post = $this->input->post();

		// バリデーション・チェック
		$this->_set_validation();

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if (empty($_SESSION['c_free_group']))
		{
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($arr_search_item, $tmp_per_page=0, $tmp_offset=0, $_tmp_cl);
		} else {

		}

		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		//     	$this->smarty->assign('seach_free_keyword', $tmp_inputpost['free_keyword']);

		$this->smarty->assign('tmp_item', $input_post['tmp_item']);
		$this->smarty->assign('tmp_sort', $input_post['tmp_sort']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_sort.tpl');

	}

	// ajax用ダミーページ : 「Google-PC ranking」昇順&降順 切り替え操作
	public function index_aj_gpcsort()
	{

		$input_post = $this->input->post();

		// バリデーション・チェック
		$this->_set_validation();

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if (empty($_SESSION['c_free_group']))
		{
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($arr_search_item, $tmp_per_page=0, $tmp_offset=0, $_tmp_cl);
		} else {

		}

		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		$this->smarty->assign('tmp_item', $input_post['tmp_item']);
		$this->smarty->assign('tmp_sort', $input_post['tmp_sort']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_sort.tpl');

	}

	// ajax用ダミーページ : 「Google-Mobile ranking」昇順&降順 切り替え操作
	public function index_aj_gmosort()
	{

		$input_post = $this->input->post();

		// バリデーション・チェック
		$this->_set_validation();

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if (empty($_SESSION['c_free_group']))
		{
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($arr_search_item, $tmp_per_page=0, $tmp_offset=0, $_tmp_cl);
		} else {

		}

		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		$this->smarty->assign('tmp_item', $input_post['tmp_item']);
		$this->smarty->assign('tmp_sort', $input_post['tmp_sort']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_sort.tpl');

	}

	// ajax用ダミーページ : 「Yahoo!-PC ranking」昇順&降順 切り替え操作
	public function index_aj_ypcsort()
	{

		$input_post = $this->input->post();

		// バリデーション・チェック
		$this->_set_validation();

		// キーワード情報の取得
		$this->load->model('Keyword',   'kw', TRUE);
		$this->load->model('Watchlist', 'wt', TRUE);

		// 一覧作成
		$_tmp_ac = $_SESSION['c_memSeq'];
		$_tmp_cl = $_SESSION['c_memGrp'];

		if (empty($_SESSION['c_free_group']))
		{
			$arr_search_item['free_keyword'] = $_SESSION['c_free_keyword'];
			$arr_search_item['free_rd']      = $_SESSION['c_free_rd'];
			$arr_search_item['free_sort_id'] = $input_post['tmp_item'];
			$arr_search_item['free_sort']    = $input_post['tmp_sort'];

			list($kw_list, $rootdomain_countall, $kw_countall) = $this->kw->get_kw_rootdomainlist($arr_search_item, $tmp_per_page=0, $tmp_offset=0, $_tmp_cl);
		} else {

		}

		$this->smarty->assign('list_kw',  $kw_list);
		$this->smarty->assign('list_cnt', ($kw_countall / 3));

		// グラフ作成（全期間表示=0）
		$this->load->library('lib_searchrank_data');
		$this->lib_searchrank_data->create_rank_graph($kw_list, $_term=0);

		$this->smarty->assign('tmp_item', $input_post['tmp_item']);
		$this->smarty->assign('tmp_sort', $input_post['tmp_sort']);

		// ajax用ダミーページ
		$this->view('topdetail/index_aj_sort.tpl');

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

	// フォーム・バリデーションチェック
	private function _set_validation()
	{

		$rule_set = array(
		);

		$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

	}

}