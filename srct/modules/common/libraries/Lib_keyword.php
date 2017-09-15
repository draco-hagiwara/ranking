<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SEO検索用クラス
 */
class Lib_keyword
{

	/**************************************************************************/
	/**
	 * 設定内容の他のレコードに反映
	 *
	 * @param  array()
	 * @param  int
	 * @return char
	 */
	public static function update_reflection($set_data, $reflection)
	{

		$CI =& get_instance();

		$CI->load->model('Keyword', 'kw', TRUE);
		$CI->load->library('lib_rootdomain');

		switch ($reflection)
		{
			case 3:
					// 同一ルートドメイン配下を更新

					$_rootdomain = $CI->lib_rootdomain->get_rootdomain($set_data['kw_url']);
					$set_data['kw_rootdomain'] = $_rootdomain['rootdomain'];

			case 2:
					// 同一ドメイン配下を更新

					$set_data['kw_domain'] = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $set_data['kw_url']);

			case 1:
					// 同一URL配下を更新

					$get_domain_data = $CI->kw->get_domain_info($set_data, $reflection);

					if (count($get_domain_data) > 0)
					{
						foreach ($get_domain_data as $key => $value)
						{
							$value['kw_maxposition'] = $set_data['kw_maxposition'];
							$value['kw_trytimes']    = $set_data['kw_trytimes'];
							$value['kw_group']       = $set_data['kw_group'];
							$value['kw_tag']         = $set_data['kw_tag'];

							$CI->kw->update_keyword($value);
						}
					}

			case 0:
					// 該当URLのみ更新

					$set_data['kw_domain'] = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $set_data['kw_url']);

					$_rootdomain = $CI->lib_rootdomain->get_rootdomain($set_data['kw_url']);
					$set_data['kw_rootdomain'] = $_rootdomain['rootdomain'];

					$CI->kw->update_keyword($set_data);

					break;

			default:

		}

		return;
	}

	/**************************************************************************/
	/**
	 * 全グループレコードの「rootdomain数」「キーワード数」を更新する
	 *
	 * @param  array()
	 * @param  int
	 * @return char
	 */
	public static function update_group_info_all($cl_seq, $gt_type)
	{

		$CI =& get_instance();
		$CI->load->model('Keyword',   'kw', TRUE);
		$CI->load->model('Group_tag', 'gt', TRUE);


		$get_group_data = $CI->gt->get_gt_clseq($cl_seq, $gt_type);

		$set_data['kw_cl_seq'] = $cl_seq;
		foreach ($get_group_data as $key => $value)
		{
			$set_data['kw_group'] = $value['gt_name'];
			$set_data['kw_tag']   = $value['gt_name'];

			// このグループが設定されているルートドメイン数
			$rootdomain_cnt = $CI->kw->get_grouptag_cnt($set_data, $gt_type);

			// このグループが設定されているキーワード数
			$keyword_cnt = $CI->kw->get_keyword_cnt($set_data, $gt_type);

			// UPDATE
			$set_gt_data['gt_seq']         = $value['gt_seq'];
			$set_gt_data['gt_cl_seq']      = $set_data['kw_cl_seq'];
			$set_gt_data['gt_name']        = $set_data['kw_group'];
			$set_gt_data['gt_domain_cnt']  = $rootdomain_cnt;
			$set_gt_data['gt_keyword_cnt'] = $keyword_cnt;

			$CI->gt->update_gt_cnt($set_gt_data, $gt_type);

		}

		return;

	}

	/**************************************************************************/
	/**
	 * 全タグレコードの「rootdomain数」「キーワード数」を更新する
	 *
	 * @param  array()
	 * @param  int
	 * @return char
	 */
	public static function update_tag_info_all($cl_seq, $gt_type)
	{

		$CI =& get_instance();
		$CI->load->model('Keyword',   'kw', TRUE);
		$CI->load->model('Group_tag', 'gt', TRUE);


		$get_tag_data = $CI->gt->get_gt_clseq($cl_seq, $gt_type);

		$set_data['kw_cl_seq'] = $cl_seq;
		foreach ($get_tag_data as $key => $value)
		{
			$set_data['kw_group'] = $value['gt_name'];
			$set_data['kw_tag']   = $value['gt_name'];

			// このグループが設定されているルートドメイン数
			$rootdomain_cnt = $CI->kw->get_grouptag_cnt($set_data, $gt_type);

			// このグループが設定されているキーワード数
			$keyword_cnt = $CI->kw->get_keyword_cnt($set_data, $gt_type);

			// UPDATE
			$set_gt_data['gt_seq']         = $value['gt_seq'];
			$set_gt_data['gt_cl_seq']      = $set_data['kw_cl_seq'];
			$set_gt_data['gt_name']        = $set_data['kw_tag'];
			$set_gt_data['gt_domain_cnt']  = $rootdomain_cnt;
			$set_gt_data['gt_keyword_cnt'] = $keyword_cnt;

			$CI->gt->update_gt_cnt($set_gt_data, $gt_type);

		}

		return;

	}

	/**************************************************************************/
	/**
	 * ロケーションのセット (登録＆更新用)
	 *
	 * @param  int
	 * @return
	 */
	public static function location_set($select_location = NULL)
	{

		$CI =& get_instance();
		$CI->load->model('Location', 'lc', TRUE);

		$location_list = $CI->lc->get_location_list();

		// 既存ロケーションのリスト作成
		$opt_location = "";
		foreach ($location_list as $key => $value)
		{
			$opt_location .= '<option value="' . $value['lo_criteria_id'] . '">' . $value['lo_canonical_name'] . '</option>';
		}

		// 画面から選択入力ロケーションのリスト作成
		if ($select_location != NULL)
		{

			foreach ($select_location as $key => $value)
			{
				$location_name = $CI->lc->get_location_id($value);
				$opt_location .= '<option selected="selected" value="' . $value . '">' . $location_name[0]['lo_canonical_name'] . '</option>';
			}

		}

		$CI->smarty->assign('options_location', $opt_location);

	}

	/**************************************************************************/
	/**
	 * 設定グループ＆タグのセット
	 *
	 * @param  int
	 * @param  char
	 * @param  int
	 * @return char
	 */
	public static function grouptag_set($cl_seq, $gt_name, $gt_type=0)
	{

		$CI =& get_instance();
		$CI->load->model('Group_tag', 'gt', TRUE);


		// グループorタグ情報取得
		$grouptag_list = $CI->gt->get_gt_clseq($cl_seq, $gt_type);

		// 画面から選択入力タグのリスト作成
		$opt_grouptag = "";
		if ($gt_name != NULL)
		{

			$_arr_tagname = str_replace("[", "" ,str_replace("]", "", explode("][", $gt_name)));

			// 入力リスト作成
			foreach ($_arr_tagname as $key => $value)
			{
				$opt_grouptag .= '<option selected="selected" value="' . $value . '">' . $value . '</option>';
			}

			// 既存リスト作成
			foreach ($grouptag_list as $key => $value)
			{
				if (!in_array($value['gt_name'], $_arr_tagname))
				{
					$opt_grouptag .= '<option value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
				}
			}

		} else {

			// 既存リスト作成
			foreach ($grouptag_list as $key => $value)
			{
				$opt_grouptag .= '<option value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
			}
		}

		// グループとタグに振り分け
		if ($gt_type == 0)
		{
			$CI->smarty->assign('options_group', $opt_grouptag);
		} else {
			$CI->smarty->assign('options_tag', $opt_grouptag);
		}
	}

	/**************************************************************************/
	/**
	 * キーワード作成 (検索エンジン＆デバイス＆取得順位＆取得回数を固定に変更)
	 *
	 * 2017.07.24 Chg
	 * ・google.co.jp(PC) / google.co.jp(Mobile) / yahoo.co.jp(PC)
	 * ・最大取得順位 ... Google=300 / Yahoo=100
	 * ・1日の取得回数 ... 1回
	 *
	 * @param  int
	 * @param  char
	 * @param  int
	 * @return char
	 */
	public static function create_kw_data($input_post, $set_data_kw)
	{

		$CI =& get_instance();

		// ** 複数キーワード設定
		foreach ($input_post['kw_keyword'] as $key01 => $val_kw)
		{

			$_kw = str_replace("　", " ", $val_kw);;
			$set_data_kw['kw_keyword'] = trim($_kw);

			// ** 複数ロケーション設定
			foreach ($input_post['kw_location'] as $key02 => $val_lo)
			{

				if (is_numeric($val_lo))
				{
					$set_data_kw['kw_location_id'] = $val_lo;
					$location_name = $CI->lc->get_location_id($val_lo);
					$set_data_kw['kw_location_name'] = $location_name[0]['lo_canonical_name'];		// ロケーション(Canonical Name)
				} else {
					$set_data_kw['kw_location_name'] = $val_lo;
					$location_name = $CI->lc->get_location_name($val_lo);
					$set_data_kw['kw_location_id'] = $location_name[0]['lo_criteria_id'];			// ロケーション(Criteria ID)
				}

				/*
				 * 以下、条件を固定で作成
				 * ・google.co.jp(PC) / google.co.jp(Mobile) / yahoo.co.jp(PC)
				 * ・最大取得順位 ... Google=300 / Yahoo=100
				 * ・1日の取得回数 ... 1回
				 */


				// ** 検索エンジン(Google => PC) 作成
				$set_data_kw['kw_searchengine'] = 0;
				$set_data_kw['kw_device']       = 0;
				$set_data_kw['kw_maxposition']  = 2;
				$set_data_kw['kw_trytimes']     = 0;

				// 追加＆更新
				// check → UPDATE → INSERT
				$res = $CI->kw->up_insert_keyword($set_data_kw);

				// 旧キーワードデータ(kw_ole_seq)のチェック
				$CI->lib_keyword->_old_kw_check($set_data_kw);

				// ** 検索エンジン(Google => Mobile) 作成
				$set_data_kw['kw_searchengine'] = 0;
				$set_data_kw['kw_device']       = 1;
				$set_data_kw['kw_maxposition']  = 2;
				$set_data_kw['kw_trytimes']     = 0;

				// 追加＆更新
				// check → UPDATE → INSERT
				$res = $CI->kw->up_insert_keyword($set_data_kw);

				// 旧キーワードデータ(kw_ole_seq)のチェック
				$CI->lib_keyword->_old_kw_check($set_data_kw);

				// ** 検索エンジン(Yahoo! => PC) 作成
				$set_data_kw['kw_searchengine'] = 1;
				$set_data_kw['kw_device']       = 0;
				$set_data_kw['kw_maxposition']  = 0;
				$set_data_kw['kw_trytimes']     = 0;

				// 追加＆更新
				// check → UPDATE → INSERT
				$res = $CI->kw->up_insert_keyword($set_data_kw);

				// 旧キーワードデータ(kw_ole_seq)のチェック
				$CI->lib_keyword->_old_kw_check($set_data_kw);
			}
		}

		return $res;
	}

	/**************************************************************************/
	/**
	 * キーワード作成
	 *
	 * @param  array()
	 * @return char
	 */
	public static function _old_kw_check($set_data_kw)
	{

		$CI =& get_instance();

		$get_kw_check = $CI->kw->check_keyword($set_data_kw, $old_seq=NULL, $status=1);

		if (count($get_kw_check) >= 2)
		{
			// status を書き換え
			$get_kw_check[0]['kw_status'] = 0;
			$CI->kw->update_keyword($get_kw_check[0]);

			// 順位データの引継ぎする？
			$set_rk_data['rk_kw_seq'] = $get_kw_check[1]['kw_seq'];

			$CI->load->model('Ranking', 'rk', TRUE);
			$CI->rk->update_ranking_kwseq($set_rk_data, $get_kw_check[0]['kw_seq']);

		}
	}

}