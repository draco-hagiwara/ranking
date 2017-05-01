<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SEO検索用クラス
 */
class Lib_keyword
{

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
		$CI->load->model('Memo',    'me', TRUE);
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

					if ($set_data['kw_memo'] != "")
					{
						// メモ INSERT
						$CI->me->insert_kw_memo( $set_data['kw_seq'],
												 $set_data['kw_memo'],
												 $set_data['kw_cl_seq'],
												 $set_data['kw_ac_seq']
												);
					}

					break;

			default:

		}

		return;

	}


	/**
	 * グループレコードの「rootdomain数」「キーワード数」を更新する
	 *
	 * @param  array()
	 * @param  int
	 * @return char
	 */
	public static function update_grooup_info($set_data, $gt_type)
	{

		$CI =& get_instance();
		$CI->load->model('Keyword',   'kw', TRUE);
		$CI->load->model('Group_tag', 'gt', TRUE);

		// このグループが設定されているルートドメイン数
		$rootdomain_cnt = $CI->kw->get_rootdomain_cnt($set_data, $gt_type);

		// このグループが設定されているキーワード数
		$keyword_cnt = $CI->kw->get_keyword_cnt($set_data, $gt_type);

		// UPDATE
		$set_gt_data['gt_cl_seq']      = $set_data['kw_cl_seq'];
		$set_gt_data['gt_name']        = $set_data['kw_group'];
		$set_gt_data['gt_domain_cnt']  = $rootdomain_cnt;
		$set_gt_data['gt_keyword_cnt'] = $keyword_cnt;

		$CI->gt->update_gt_cnt($set_gt_data, $gt_type);

		return;

	}

	/**
	 * 全グループレコードの「rootdomain数」「キーワード数」を更新する
	 *
	 * @param  array()
	 * @param  int
	 * @return char
	 */
	public static function update_grooup_info_all($cl_seq, $gt_type)
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
			$rootdomain_cnt = $CI->kw->get_rootdomain_cnt($set_data, $gt_type);

			// このグループが設定されているキーワード数
			$keyword_cnt = $CI->kw->get_keyword_cnt($set_data, $gt_type);

			// UPDATE
			$set_gt_data['gt_cl_seq']      = $set_data['kw_cl_seq'];
			$set_gt_data['gt_name']        = $set_data['kw_group'];
			$set_gt_data['gt_domain_cnt']  = $rootdomain_cnt;
			$set_gt_data['gt_keyword_cnt'] = $keyword_cnt;

			$CI->gt->update_gt_cnt($set_gt_data, $gt_type);

		}

		return;

	}

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
			$rootdomain_cnt = $CI->kw->get_rootdomain_cnt($set_data, $gt_type);

			// このグループが設定されているキーワード数
			$keyword_cnt = $CI->kw->get_keyword_cnt($set_data, $gt_type);

			// UPDATE
			$set_gt_data['gt_cl_seq']      = $set_data['kw_cl_seq'];
			$set_gt_data['gt_name']        = $set_data['kw_tag'];
			$set_gt_data['gt_domain_cnt']  = $rootdomain_cnt;
			$set_gt_data['gt_keyword_cnt'] = $keyword_cnt;

			$CI->gt->update_gt_cnt($set_gt_data, $gt_type);

		}

		return;

	}

	/**
	 * タグリストを成型する
	 *
	 * @param  array()
	 * @param  int
	 * @return char
	 */
	public static function create_mold_tag($gt_list)
	{

		$CI =& get_instance();

		$set_tag_list = "";
		foreach ($gt_list as $key => $value)
		{
			$set_tag_list .= '<li><a href="'
							. 'detail/' . $value['gt_seq'] . '/">'										// 飛び先URL
							. $value['gt_name'] . '<span>'
							. $value['gt_domain_cnt'] . '</span><span1>'
							. $value['gt_keyword_cnt'] . '</span1></a></li>'
			;
		}

		$CI->smarty->assign('tag_list', $set_tag_list);

		return;

	}

	/**
	 * ロケーションのセット
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

		$opt_grouptag = "";
		if ($gt_type == 0)
		{

			foreach ($grouptag_list as $key => $value)
			{
				if ($gt_name == $value['gt_name'])
				{
					$opt_grouptag .= '<option selected="selected" value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
				} else {
					$opt_grouptag .= '<option value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
				}
			}

			$CI->smarty->assign('options_group', $opt_grouptag);

		} else {

			// 画面から選択入力タグのリスト作成
			if ($gt_name != NULL)
			{
				$_arr_tagname = str_replace("[", "" ,str_replace("]", "", explode("][", $gt_name)));

				// 既存タグのリスト作成
				foreach ($grouptag_list as $key => $value)
				{

					if (in_array($value['gt_name'], $_arr_tagname))
					{
						$opt_grouptag .= '<option selected="selected" value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
					} else {
						$opt_grouptag .= '<option value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
					}

				}
			} else {

				// 既存タグのリスト作成
				foreach ($grouptag_list as $key => $value)
				{
					$opt_grouptag .= '<option value="' . $value['gt_name'] . '">' . $value['gt_name'] . '</option>';
				}

			}

			$CI->smarty->assign('options_tag', $opt_grouptag);
			return;

		}
	}


	/**
	 * キーワード作成
	 *
	 * @param  int
	 * @param  char
	 * @param  int
	 * @return char
	 */
	public static function create_kw_data($input_post, $set_data_kw)
	{

		$CI =& get_instance();
// 		$CI->load->model('Keyword', 'kw', TRUE);




		print_r($input_post);
		print("<br><br>");
		print_r($set_data_kw);
		print("<br><br>");
// 		exit;





		// ** 複数キーワード設定
		foreach ($input_post['kw_keyword'] as $key01 => $val_kw)
		{

			$_kw = str_replace("　", " ", $val_kw);;
			$set_data_kw['kw_keyword'] = trim($_kw);

			// ** 複数ロケーション設定
			foreach ($input_post['kw_location'] as $key02 => $val_lo)
			{

				$set_data_kw['kw_location_id'] = $val_lo;
				$location_name = $CI->lc->get_location_id($val_lo);
				$set_data_kw['kw_location_name'] = $location_name[0]['lo_canonical_name'];		// ロケーション(Canonical Name)

				// ** 検索エンジン(Google)をチェック
				if (isset($input_post['chkengine'][0]))
				{
					$set_data_kw['kw_searchengine'] = $input_post['chkengine'][0];

					// 対象デバイス(PC)をチェック
					if (isset($input_post['chkdevice'][0]))
					{
						$set_data_kw['kw_device'] = $input_post['chkdevice'][0];

						// check → UPDATE → INSERT
						$res = $CI->kw->up_insert_keyword($set_data_kw, $input_post['kw_memo']);
					}

					// 対象デバイス(Mobile)をチェック
					if (isset($input_post['chkdevice'][1]))
					{
						$set_data_kw['kw_device'] = $input_post['chkdevice'][1];

						// check → UPDATE → INSERT
						$res =$CI->kw->up_insert_keyword($set_data_kw, $input_post['kw_memo']);
					}
				}

				// ** 検索エンジン(Yahoo!)をチェック
				if (isset($input_post['chkengine'][1]))
				{
					$set_data_kw['kw_searchengine'] = $input_post['chkengine'][1];

					// 対象デバイス(PC)をチェック
					if (isset($input_post['chkdevice'][0]))
					{
						$set_data_kw['kw_device'] = $input_post['chkdevice'][0];

						// check → UPDATE → INSERT
						$res =$CI->kw->up_insert_keyword($set_data_kw, $input_post['kw_memo']);
					}

					// 対象デバイス(Mobile)をチェック
					if (isset($input_post['chkdevice'][1]))
					{
						$set_data_kw['kw_device'] = $input_post['chkdevice'][1];

						// check → UPDATE → INSERT
						$res =$CI->kw->up_insert_keyword($set_data_kw, $input_post['kw_memo']);
					}
				}
			}
		}
	}

}