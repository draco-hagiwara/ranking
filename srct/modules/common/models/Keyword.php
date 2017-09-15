<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Keyword extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * キーワードSEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_kw_seq($seq_no)
    {

    	$set_where["kw_seq"] = $seq_no;

    	$query = $this->db->get_where('tb_keyword', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * ３キーワード（google-PC/Mobile/Yahoo!）情報を取得
     *
     * @param    array()
     * @return   array()
     */
    public function get_kw_info($setdata)
    {

    	// 同一データの有無確認
    	$sql = 'SELECT
                  kw_seq,
                  kw_cl_seq,
                  kw_url,
                  kw_domain,
                  kw_rootdomain,
                  kw_keyword,
                  kw_matchtype,
                  kw_searchengine,
                  kw_device,
                  kw_location_id,
                  kw_location_name
                FROM tb_keyword
    			WHERE
    			     kw_status = 1
    			     AND kw_cl_seq = ' . $setdata['kw_cl_seq'] . '
    			     AND kw_url = \'' . $setdata['kw_url'] . '\'
    			     AND kw_keyword = \'' . $setdata['kw_keyword'] . '\'
    			     AND kw_matchtype = ' . $setdata['kw_matchtype'] . '
    			     AND kw_location_id = ' . $setdata['kw_location_id'] . '
    	';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 同一URL/ドメイン/ルートドメイン情報を取得する
     *
     * @param    array()
     * @return   array()
     */
    public function get_domain_info($setdata, $_switch)
    {

    	// 同一データの有無確認
    	$sql = 'SELECT
                  kw_seq,
                  kw_cl_seq
                FROM tb_keyword
    			WHERE
    			     kw_status = 1
    			     AND kw_cl_seq = ' . $setdata['kw_cl_seq'] . '
	    ';

    	if ($_switch == 1)
    	{
    		$sql .= ' AND kw_url = \'' . $setdata['kw_url'] . '\'';
    	} elseif ($_switch == 2) {
    		$sql .= ' AND kw_domain = \'' . $setdata['kw_domain'] . '\'';
    	} elseif ($_switch == 3) {
    		$sql .= ' AND kw_rootdomain = \'' . $setdata['kw_rootdomain'] . '\'';
    	}

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * グループ＆タグが設定されているキーワード数
     *
     * @param    array()
     * @param    int
     * @return   array()
     */
    public function get_keyword_cnt($setdata, $gt_type)
    {

    	// 同一データの有無確認
    	$sql = 'SELECT
	                kw_seq,
	                kw_rootdomain,
	    			kw_keyword
                FROM tb_keyword
    			WHERE
    			    kw_status = 1
    			    AND kw_cl_seq = ' . $setdata['kw_cl_seq'] . '
    	';


    	if ($gt_type == 0)
    	{
    		$sql .= 'AND kw_group = \'' . $setdata['kw_group'] . '\'
    			     GROUP BY kw_keyword
	     		    ';
    	} else {
    		$sql .= 'AND kw_tag LIKE \'%[' . $setdata['kw_tag'] . ']%\'
    			     GROUP BY kw_keyword
	     		    ';
    	}

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$keyword_cnt = $query->num_rows();

    	return $keyword_cnt;

    }

    /**
     * グループ＆タグが設定されているルートドメイン数
     *
     * @param    array()
     * @param    int
     * @return   array()
     */
    public function get_grouptag_cnt($setdata, $gt_type)
    {

    	// 同一データの有無確認
    	$sql = 'SELECT
	                kw_seq,
	                kw_rootdomain,
	    			kw_keyword
                FROM tb_keyword
    			WHERE
    			    kw_status = 1
    			    AND kw_cl_seq = ' . $setdata['kw_cl_seq'] . '
    	';


    	if ($gt_type == 0)
    	{
    		$sql .= 'AND kw_group = \'' . $setdata['kw_group'] . '\'
    			     GROUP BY kw_rootdomain
	     		    ';
    	} else {
    		$sql .= 'AND kw_tag LIKE \'%[' . $setdata['kw_tag'] . ']%\'
    			     GROUP BY kw_rootdomain
	     		    ';
    	}

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$grouptag_cnt = $query->num_rows();

    	return $grouptag_cnt;

    }

    /**
     * ルートドメイン数
     *
     * @param    array()
     * @return   array()
     */
    public function get_rootdomain_cnt($setdata)
    {

    	// 同一データの有無確認
    	$sql = 'SELECT
	                kw_seq,
	                kw_rootdomain
                FROM tb_keyword
    			WHERE
    			    kw_status = 1
    			    AND kw_old_seq IS NULL
    			    AND kw_cl_seq = ' . $setdata['kw_cl_seq'] . '
    				AND kw_rootdomain = \'' . $setdata['kw_rootdomain'] . '\'
    	';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$rootdomain_cnt = $query->num_rows();

    	return $rootdomain_cnt;

    }

    /**
     * 検索用にKeywordデータを抽出
     *
     * @param    int
     * @return   array()
     *
     *
     * 2017.09.01 現在
     *   1日の検索データ取得回数を1回に限定：kw_trytimes=0
     *
     */
    public function get_keyword_data($cnt)
    {

    	// 同一データの有無確認
    	$sql = 'SELECT
                  kw_seq,
    			  kw_old_seq,
    			  kw_cl_seq,
                  kw_keyword,
                  kw_searchengine,
                  kw_device,
                  kw_location_id,
                  kw_location_name,
                  kw_maxposition,
    			  kw_trytimes,
    			  kw_domain,
    			  kw_rootdomain,
    			  kw_url,
    			  kw_matchtype
    			FROM tb_keyword
    			WHERE
    			     kw_status = 1
    			     AND kw_trytimes >= ' . $cnt . '
    			     ORDER BY kw_keyword, kw_searchengine, kw_device, kw_location_id ASC, kw_maxposition DESC
	    ';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * キーワード情報の重複チェック
     *
     * @param    array()
     * @return   int
     */
    public function check_keyword($setdata, $old_seq=NULL, $status=NULL)
    {

    	$sql = 'SELECT
                  kw_seq,
    			  kw_old_seq,
    			  kw_status,
                  kw_cl_seq
                FROM tb_keyword
    			WHERE
    			     kw_cl_seq = ' . $setdata['kw_cl_seq'] . '
    			     AND kw_url = \'' . $setdata['kw_url'] . '\'
    			     AND kw_keyword = \'' . $setdata['kw_keyword'] . '\'
    			     AND kw_matchtype = ' . $setdata['kw_matchtype'] . '
    			     AND kw_searchengine = ' . $setdata['kw_searchengine'] . '
    			     AND kw_device = ' . $setdata['kw_device'] . '
    			     AND kw_location_id = \'' . $setdata['kw_location_id'] . '\'
	    ';

    	if ($old_seq != NULL)
    	{
    		$sql .= ' AND kw_old_seq = ' . $old_seq;
    	}

    	if ($status != NULL)
    	{
    		$sql .= ' AND kw_status = ' . $status;
    	}

    	$sql .= ' ORDER BY kw_seq ASC';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');
    	//     	$get_rows = $query->num_rows();

    	return $get_data;

    }

    /**
     * キーワード情報のURL重複チェック (check_keyword の改良)
     *
     * @param    array()
     * @return   int
     */
    public function check_url($setdata, $old_seq=NULL, $status=NULL)
    {

    	$sql = 'SELECT
                  kw_seq,
    			  kw_old_seq,
    			  kw_status,
                  kw_cl_seq
                FROM tb_keyword
    			WHERE
    			     kw_cl_seq = ' . $setdata['kw_cl_seq'] . '
    			     AND kw_url = \'' . $setdata['kw_url'] . '\'
    			     AND kw_keyword = \'' . $setdata['kw_keyword'] . '\'
    			     AND kw_matchtype = ' . $setdata['kw_matchtype'] . '
    			     AND kw_searchengine = ' . $setdata['kw_searchengine'] . '
    			     AND kw_device = ' . $setdata['kw_device'] . '
    			     AND kw_location_id = \'' . $setdata['kw_location_id'] . '\'
    			     AND kw_seq != ' . $setdata['kw_seq'] . '
	    ';

    	if ($old_seq == NULL)
    	{
    		$sql .= ' AND kw_old_seq is NULL ';
    	} else {
    		$sql .= ' AND kw_old_seq = ' . $old_seq;
    	}

    	if ($status != NULL)
    	{
    		$sql .= ' AND kw_status = ' . $status;
    	}

    	$sql .= ' ORDER BY kw_seq ASC';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');
    	//     	$get_rows = $query->num_rows();

    	return $get_data;

    }


    /**
     * ルートドメイン選択→キーワード情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function get_kw_rootdomainlist($get_item, $tmp_per_page, $tmp_offset=0, $client_no)
    {

    	// 各SQL項目へセット
    	if (isset($get_item['free_rd']))
    	{
    		$set_select["wt_kw_rootdomain"] = $get_item['free_rd'];
    	} else {
    		$set_select["wt_kw_rootdomain"] = NULL;
    	}

    	// ORDER BY
    	if (!empty($get_item['free_sort_id']))
    	{
    		switch ($get_item['free_sort_id'])
    		{
    			case 'wl':
    				$set_orderby = 'T2.wt_seq ' . $get_item['free_sort'] . ', T3.rd_seq ' . $get_item['free_sort'];

    				break;
    			case 'group':
    				$set_orderby = 'kw_group ' . $get_item['free_sort'] . ', T3.rd_seq ' . $get_item['free_sort'];

    				break;
    			case 'keyword':
    				$set_orderby = 'kw_keyword ' . $get_item['free_sort'] . ', T3.rd_seq ' . $get_item['free_sort'];

    				break;
    			case 'url':
    				$set_orderby = 'kw_url ' . $get_item['free_sort'] . ', T3.rd_seq ' . $get_item['free_sort'];

    				break;
    			case 'location':
    				$set_orderby = 'kw_location_name ' . $get_item['free_sort'] . ', T3.rd_seq ' . $get_item['free_sort'];

    				break;
    			case 'gpc':
    				$set_orderby = 'T4.rk_position ' . $get_item['free_sort'] . ', T3.rd_seq ' . $get_item['free_sort'];

    				break;
    			case 'gmo':
    				$set_orderby = 'T4.rk_position ' . $get_item['free_sort'] . ', T3.rd_seq ' . $get_item['free_sort'];

    				break;
    			case 'ypc':
    				$set_orderby = 'T4.rk_position ' . $get_item['free_sort'] . ', T3.rd_seq ' . $get_item['free_sort'];

    				break;
    			default:
    				$get_item['free_sort_id'] = NULL;
    				$set_orderby = NULL;
    		}
    	} else {
    		$get_item['free_sort_id'] = NULL;
    		$set_orderby = NULL;
    	}

    	// 対象情報の取得
    	$kw_list = $this->_select_rootdomainlist($get_item['free_keyword'], $set_select, $set_orderby, $client_no, $get_item['free_sort_id'], $tmp_per_page, $tmp_offset);

    	return $kw_list;

    }

    /**
     * ルートドメイン選択→キーワード情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function _select_rootdomainlist($free_kw, $set_select, $set_orderby, $client_no, $free_sort_id=NULL, $tmp_per_page, $tmp_offset)
    {

    	// ** 左サイド一覧
    	$sql1_1 = 'SELECT
	                kw_seq,
	    		    kw_rootdomain,
    			    T3.rd_seq,
    			    T3.rd_rootdomain
        		  FROM `tb_keyword` LEFT JOIN `tb_rootdomain` as T3 on (`kw_rootdomain` = `rd_rootdomain`)
	    		  WHERE `kw_old_seq` is NULL'
    	;

    	$sql1_1 .= ' AND `kw_status`  = 1';
    	$sql1_1 .= ' AND `kw_cl_seq`  = ' . $client_no;
    	$sql1_1 .= ' AND `rd_cl_seq`  = ' . $client_no;

    	// GROUP BY文 作成
    	$sql1_1 .= ' GROUP BY kw_rootdomain';

    	// ORDER BY文 作成（ルートドメイン一覧の並び）
    	$sql1_1 .= ' ORDER BY T3.rd_seq DESC, kw_seq DESC';

    	// クエリー実行
    	$query = $this->db->query($sql1_1);
    	$catalog_list = $query->result('array');

    	// ** rootdomain を検索
    	$sql1 = 'SELECT
	              kw_seq,
	    		  kw_rootdomain,
    			  T3.rd_seq
	        	FROM `tb_keyword` LEFT JOIN `tb_rootdomain` as T3 on (`kw_rootdomain` = `rd_rootdomain`)
	    		WHERE `kw_old_seq` is NULL'
    	;

    	// ** 該当KW を検索
    	$sql2 = 'SELECT
	              kw_seq,
	              kw_cl_seq,
	              kw_status,
	              kw_url,
	              kw_domain,
                  kw_rootdomain,
                  kw_keyword,
                  kw_matchtype,
                  kw_searchengine,
                  kw_device,
                  kw_location_id,
                  kw_location_name,
                  kw_maxposition,
                  kw_trytimes,
                  kw_group,
                  kw_tag,
    			  T2.wt_seq,
        		  T2.wt_ac_seq,
        		  T3.rd_seq,
				  T3.rd_sitename
        		FROM `tb_keyword` LEFT JOIN `tb_watchlist` as T2 on ((`kw_seq` = `wt_kw_seq`) AND (`wt_ac_seq` = ' . $_SESSION['c_memSeq'] . '))
        		                  LEFT JOIN `tb_rootdomain` as T3 on (`kw_rootdomain` = `rd_rootdomain`)
        		WHERE `kw_old_seq` is NULL'
		;

		// ** 該当KWの当日ランキング を検索
		$date = new DateTime();
		$_tmp_today = $date->format('Y-m-d');
		$sql3 = 'SELECT
	              kw_seq,
	              kw_cl_seq,
	              kw_status,
	              kw_url,
	              kw_domain,
                  kw_rootdomain,
                  kw_keyword,
                  kw_matchtype,
                  kw_searchengine,
                  kw_device,
                  kw_location_id,
                  kw_location_name,
                  kw_maxposition,
                  kw_trytimes,
                  kw_group,
                  kw_tag,
    			  T2.wt_seq,
        		  T2.wt_ac_seq,
        		  T3.rd_seq,
				  T3.rd_sitename,
        		  T4.rk_seq,
				  T4.rk_position
        		FROM `tb_keyword` LEFT JOIN `tb_watchlist` as T2 on ((`kw_seq` = `wt_kw_seq`) AND (`wt_ac_seq` = ' . $_SESSION['c_memSeq'] . '))
        		                  LEFT JOIN `tb_rootdomain` as T3 on (`kw_rootdomain` = `rd_rootdomain`)
        		                  LEFT JOIN `tb_ranking` as T4 on ((`kw_seq` = `rk_kw_seq`) AND (`rk_getdate` = \'' . $_tmp_today . '\'))
        		WHERE `kw_old_seq` is NULL'
		;

		$sql1 .= ' AND `kw_status`  = 1';
		$sql1 .= ' AND `kw_cl_seq`  = ' . $client_no;

		if (!empty($set_select["wt_kw_rootdomain"]))
		{
			$sql1 .= ' AND `kw_rootdomain`  = \'' . $set_select["wt_kw_rootdomain"] . '\'';
		}


		// WHERE文 作成
		if (!empty($free_kw))
		{

			$free_word = str_replace("　", " ", $free_kw);
			$array = explode(" ", $free_word);

			$sql1 .= ' AND ';
			for($i = 0; $i < count($array); $i++){
				$sql1 .= '( kw_keyword LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
				$sql1 .= ' OR kw_url LIKE \'%' .   $this->db->escape_like_str($array[$i]) . '%\'';
				$sql1 .= ' OR kw_group LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\')';

				if ($i < count($array) -1){
					$sql1 .= " AND ";						// 絞り込み
					//$sql1 .= " OR ";						// 部分一致
				}
			}
		}

		// GROUP BY文 作成
		$sql1 .= ' GROUP BY kw_rootdomain';

		// 対象全件数を取得
		$query = $this->db->query($sql1);
		$rootdomain_countall = $query->num_rows();

		// クエリー実行
		$query = $this->db->query($sql1);
		$rootdomain_list = $query->result('array');

		// ** キーワード情報 を検索
		$sql2 .= ' AND `kw_cl_seq`  = ' . $client_no;
		$sql2 .= ' AND `kw_status`  = 1';
		$sql3 .= ' AND `kw_cl_seq`  = ' . $client_no;
		$sql3 .= ' AND `rd_cl_seq`  = ' . $client_no;

		// 検索エンジンとデバイスをセット
		$position_sort = TRUE;
		if ($free_sort_id == "gpc") {
			$sql3 .= ' AND `kw_searchengine`  = 0';
			$sql3 .= ' AND `kw_device`        = 0';
		} elseif ($free_sort_id == "gmo") {
			$sql3 .= ' AND `kw_searchengine`  = 0';
			$sql3 .= ' AND `kw_device`        = 1';
		} elseif ($free_sort_id == "ypc") {
			$sql3 .= ' AND `kw_searchengine`  = 1';
			$sql3 .= ' AND `kw_device`        = 0';
		} else {
			$position_sort = FALSE;
		}

		// WHERE文 作成
		if (!empty($free_kw))
		{
			$free_word = str_replace("　", " ", $free_kw);
			$array = explode(" ", $free_word);

			$sql2 .= ' AND ';
			for($i = 0; $i < count($array); $i++){
				$sql2 .= '( kw_keyword LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
				$sql2 .= ' OR kw_url LIKE \'%' .   $this->db->escape_like_str($array[$i]) . '%\'';
				$sql2 .= ' OR kw_group LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\')';

				if ($i < count($array) -1){
					$sql2 .= " AND ";						// 絞り込み
					//$sql2 .= " OR ";						// 部分一致
				}
			}

			$sql3 .= ' AND ';
			for($i = 0; $i < count($array); $i++){
				$sql3 .= '( kw_keyword LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
				$sql3 .= ' OR kw_url LIKE \'%' .   $this->db->escape_like_str($array[$i]) . '%\'';
				$sql3 .= ' OR kw_group LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\')';

				if ($i < count($array) -1){
					$sql3 .= " AND ";						// 絞り込み
					//$sql2 .= " OR ";						// 部分一致
				}
			}
		}

		// WHERE文(rootdomain) 作成
		if (!empty($rootdomain_list))
		{
			$tmp_firstitem = FALSE;
			foreach ($rootdomain_list as $key => $val)
			{
				if (empty($val['kw_rootdomain']))
				{
					$_tmp_rootdomain = $val['wt_kw_rootdomain'];
				} else {
					$_tmp_rootdomain = $val['kw_rootdomain'];
				}

				if ($tmp_firstitem === FALSE)
				{
					$sql2 .= ' AND ( kw_rootdomain = \'' . $_tmp_rootdomain . '\'';
					$sql3 .= ' AND ( kw_rootdomain = \'' . $_tmp_rootdomain . '\'';
					$tmp_firstitem = TRUE;
				} else {
					$sql2 .= ' OR kw_rootdomain = \'' . $_tmp_rootdomain . '\'';
					$sql3 .= ' OR kw_rootdomain = \'' . $_tmp_rootdomain . '\'';
				}
			}
			$sql2 .= ' ) ';
			$sql3 .= ' ) ';

			// 対象全件数を取得
			if ($position_sort == FALSE)
			{
				$query = $this->db->query($sql2);
				$kw_countall = $query->num_rows();
			} else {
				$query = $this->db->query($sql3);
				$kw_countall = $query->num_rows();
				$kw_countall = $kw_countall * 3;
			}

			// GROUP BY文 作成
			$sql2 .= ' GROUP BY kw_url, kw_domain, kw_rootdomain, kw_keyword, kw_matchtype, kw_location_id';
			$sql3 .= ' GROUP BY kw_url, kw_domain, kw_rootdomain, kw_keyword, kw_matchtype, kw_location_id';

			// ORDER BY文 作成
			if (empty($set_orderby))
			{
				if ($tmp_per_page == 0)
				{
					$sql2 .= ' ORDER BY T3.rd_seq DESC, kw_seq DESC';
					$sql3 .= ' ORDER BY T3.rd_seq DESC, kw_seq DESC';
				} else {
					$sql2 .= ' ORDER BY T3.rd_seq DESC, kw_seq DESC LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;
					$sql3 .= ' ORDER BY T3.rd_seq DESC, kw_seq DESC LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;
				}
			} else {
				if ($tmp_per_page == 0)
				{
					$sql2 .= ' ORDER BY ' . $set_orderby;
					$sql3 .= ' ORDER BY ' . $set_orderby;
				} else {
					$sql2 .= ' ORDER BY ' . $set_orderby . ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;
					$sql3 .= ' ORDER BY ' . $set_orderby . ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;
				}
			}

			// クエリー実行
			if ($position_sort == FALSE)
			{
				$query = $this->db->query($sql2);
			} else {
				$query = $this->db->query($sql3);
			}
			$kw_list = $query->result('array');

		} else {
			$kw_list = array();
			$kw_countall = 0;
		}

		return array($kw_list, $catalog_list, $kw_countall);

    }

    /**
     * グループ選択→キーワード情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function get_kw_grouplist($get_item, $tmp_per_page, $tmp_offset=0, $client_no)
    {

    	// 各SQL項目へセット
    	if (isset($get_item['free_group']))
    	{
    		$set_select["kw_group"] = $get_item['free_group'];
    	} else {
    		$set_select["kw_group"] = NULL;
    	}

    	// ORDER BY
    	if (!empty($get_item['free_sort_id']))
    	{
    		switch ($get_item['free_sort_id'])
    		{
    			case 'wl':
    				$set_orderby = 'T2.wt_seq ' . $get_item['free_sort'];

    				break;
    			case 'group':
    				$set_orderby = 'kw_group ' . $get_item['free_sort'] . ', kw_keyword ' . $get_item['free_sort'];

    				break;
    			case 'keyword':
    				$set_orderby = 'kw_keyword ' . $get_item['free_sort'];

    				break;
    			case 'url':
    				$set_orderby = 'kw_url ' . $get_item['free_sort'] . ', kw_keyword ' . $get_item['free_sort'];

    				break;
    			case 'location':
    				$set_orderby = 'kw_location_name ' . $get_item['free_sort'] . ', kw_keyword ' . $get_item['free_sort'];
    				//$set_orderby = 'kw_location_id ' . $get_item['free_sort'] . ', kw_keyword ' . $get_item['free_sort'];

    				break;
    			case 'gpc':
    				$set_orderby = 'T4.rk_position ' . $get_item['free_sort'] . ', kw_keyword ' . $get_item['free_sort'];

    				break;
    			case 'gmo':
    				$set_orderby = 'T4.rk_position ' . $get_item['free_sort'] . ', kw_keyword ' . $get_item['free_sort'];

    				break;
    			case 'ypc':
    				$set_orderby = 'T4.rk_position ' . $get_item['free_sort'] . ', kw_keyword ' . $get_item['free_sort'];

    				break;
    			default:
    				$get_item['free_sort_id'] = NULL;
    				$set_orderby = NULL;
    		}
    	} else {
    		$get_item['free_sort_id'] = NULL;
    		$set_orderby = NULL;
    	}

    	// 対象情報の取得
    	$kw_list = $this->_select_grouplist($get_item['free_keyword'], $set_select, $set_orderby, $client_no, $get_item['free_sort_id'], $tmp_per_page, $tmp_offset);

    	return $kw_list;

    }

    /**
     * グループ選択→キーワード情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function _select_grouplist($free_kw, $set_select, $set_orderby, $client_no, $free_sort_id=NULL, $tmp_per_page, $tmp_offset=0)
    {

    	// ** 左サイド一覧
    	$sql1_1 = 'SELECT
	                kw_seq,
	    		    kw_group,
    			    T3.gt_seq,
    			    T3.gt_name
	        	  FROM `tb_keyword` RIGHT JOIN `tb_group_tag` as T3 on (`kw_group` = `gt_name`)
	    		  WHERE `kw_old_seq` is NULL'
    	;

    	$sql1_1 .= ' AND `kw_status`  = 1';
    	$sql1_1 .= ' AND `kw_cl_seq`  = ' . $client_no;
    	$sql1_1 .= ' AND `gt_cl_seq`  = ' . $client_no;

    	// GROUP BY文 作成
    	$sql1_1 .= ' GROUP BY kw_group';

    	// ORDER BY文 作成（グループ一覧の並び）
    	$sql1_1 .= ' ORDER BY T3.gt_seq DESC, kw_seq DESC';

    	// クエリー実行
    	$query = $this->db->query($sql1_1);
    	$catalog_list = $query->result('array');


    	// ** group を検索
    	$sql1 = 'SELECT
	              kw_seq,
	    		  kw_group,
    			  T3.gt_seq
	        	FROM `tb_keyword` RIGHT JOIN `tb_group_tag` as T3 on (`kw_group` = `gt_name`)
	    		WHERE `kw_old_seq` is NULL'
    			;

    	// ** 該当KW を検索
    	$sql2 = 'SELECT
	              kw_seq,
	              kw_cl_seq,
	              kw_status,
	              kw_url,
	              kw_domain,
                  kw_rootdomain,
                  kw_keyword,
                  kw_matchtype,
                  kw_searchengine,
                  kw_device,
                  kw_location_id,
                  kw_location_name,
                  kw_maxposition,
                  kw_trytimes,
                  kw_group,
                  kw_tag,
    			  T2.wt_seq,
        		  T2.wt_ac_seq,
        		  T3.gt_seq,
				  T3.gt_name
        		FROM `tb_keyword` LEFT JOIN `tb_watchlist` as T2 on ((`kw_seq` = `wt_kw_seq`) AND (`wt_ac_seq` = ' . $_SESSION['c_memSeq'] . '))
        		                  LEFT JOIN `tb_group_tag` as T3 on (`kw_group` = `gt_name`)
        		WHERE `kw_old_seq` is NULL'
    	;

    	// ** 該当KWの当日ランキング を検索 & ソート
    	$date = new DateTime();
    	$_tmp_today = $date->format('Y-m-d');
    	$sql3 = 'SELECT
	              kw_seq,
	              kw_cl_seq,
	              kw_status,
	              kw_url,
	              kw_domain,
                  kw_rootdomain,
                  kw_keyword,
                  kw_matchtype,
                  kw_searchengine,
                  kw_device,
                  kw_location_id,
                  kw_location_name,
                  kw_maxposition,
                  kw_trytimes,
                  kw_group,
                  kw_tag,
    			  T2.wt_seq,
        		  T2.wt_ac_seq,
        		  T3.gt_seq,
				  T3.gt_name,
        		  T4.rk_seq,
				  T4.rk_position
    			FROM `tb_keyword` LEFT JOIN `tb_watchlist` as T2 on ((`kw_seq` = `wt_kw_seq`) AND (`wt_ac_seq` = ' . $_SESSION['c_memSeq'] . '))
        		                  LEFT JOIN `tb_group_tag` as T3 on (`kw_group` = `gt_name`)
        		                  LEFT JOIN `tb_ranking` as T4 on ((`kw_seq` = `rk_kw_seq`) AND (`rk_getdate` = \'' . $_tmp_today . '\'))
    			WHERE `kw_old_seq` is NULL'
    	;

    	$sql1 .= ' AND `kw_status`  = 1';
    	$sql1 .= ' AND `kw_cl_seq`  = ' . $client_no;

    	if (!empty($set_select["kw_group"]))
    	{
    		$sql1 .= ' AND `kw_group`  = \'' . $set_select["kw_group"] . '\'';
    	}

    	// WHERE文 作成
    	if (!empty($free_kw))
    	{

    		$free_word = str_replace("　", " ", $free_kw);
    		$array = explode(" ", $free_word);

    		$sql1 .= ' AND ';
    		for($i = 0; $i < count($array); $i++){
    			$sql1 .= '( kw_keyword LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql1 .= ' OR kw_url LIKE \'%' .   $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql1 .= ' OR kw_group LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql1 .= ' OR kw_tag LIKE \'%' .   $this->db->escape_like_str($array[$i]) . '%\')';

    			if ($i < count($array) -1){
    				$sql1 .= " AND ";						// 絞り込み
    				//$sql1 .= " OR ";						// 部分一致
    			}
    		}
    	}

    	// GROUP BY文 作成
    	$sql1 .= ' GROUP BY kw_group';

    	// 対象全件数を取得
    	$query = $this->db->query($sql1);
    	$gt_countall = $query->num_rows();

    	// クエリー実行
    	$query = $this->db->query($sql1);
    	$gt_list = $query->result('array');

    	// ** キーワード情報 を検索
    	$sql2 .= ' AND `kw_cl_seq`  = ' . $client_no;
    	$sql2 .= ' AND `gt_cl_seq`  = ' . $client_no;
    	$sql2 .= ' AND `kw_status`  = 1';
    	$sql3 .= ' AND `kw_cl_seq`  = ' . $client_no;
    	$sql3 .= ' AND `gt_cl_seq`  = ' . $client_no;
    	$sql2 .= ' AND `kw_status`  = 1';

    	// 検索エンジンとデバイスをセット
    	$position_sort = TRUE;
    	if ($free_sort_id == "gpc") {
    		$sql3 .= ' AND `kw_searchengine`  = 0';
    		$sql3 .= ' AND `kw_device`        = 0';
    	} elseif ($free_sort_id == "gmo") {
    		$sql3 .= ' AND `kw_searchengine`  = 0';
    		$sql3 .= ' AND `kw_device`        = 1';
    	} elseif ($free_sort_id == "ypc") {
    		$sql3 .= ' AND `kw_searchengine`  = 1';
    		$sql3 .= ' AND `kw_device`        = 0';
    	} else {
    		$position_sort = FALSE;
    	}

    	// WHERE文 作成
    	if (!empty($free_kw))
    	{
    		$free_word = str_replace("　", " ", $free_kw);
    		$array     = explode(" ", $free_word);

    		$sql2 .= ' AND ';
    		for($i = 0; $i < count($array); $i++){
    			$sql2 .= '( kw_keyword LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql2 .= ' OR kw_url LIKE \'%' .   $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql2 .= ' OR kw_group LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql2 .= ' OR kw_tag LIKE \'%' .   $this->db->escape_like_str($array[$i]) . '%\')';

    			if ($i < count($array) -1){
    				$sql2 .= " AND ";						// 絞り込み
    				//$sql2 .= " OR ";						// 部分一致
    			}
    		}

    		$sql3 .= ' AND ';
    		for($i = 0; $i < count($array); $i++){
    			$sql3 .= '( kw_keyword LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql3 .= ' OR kw_url LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql3 .= ' OR kw_group LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\')';

    			if ($i < count($array) -1){
    				$sql3 .= " AND ";						// 絞り込み
    				//$sql2 .= " OR ";						// 部分一致
    			}
    		}

    	} else {
    		// 一覧からのグループ指定の場合
    		if (!empty($set_select["kw_group"]))
    		{
    			$sql2 .= ' AND kw_group = \'' . $set_select["kw_group"] . '\'';
    			$sql3 .= ' AND kw_group = \'' . $set_select["kw_group"] . '\'';
    		}
    	}

    	// WHERE文(group) 作成
    	if (!empty($gt_list))
    	{
    		$tmp_firstitem = FALSE;
    		foreach ($gt_list as $key => $val)
    		{
    			if (empty($val['kw_group']))
    			{
    				$_tmp_group = $val['gt_name'];
    			} else {
    				$_tmp_group = $val['kw_group'];
    			}

    			if ($tmp_firstitem === FALSE)
    			{
    				$sql2 .= ' AND ( kw_group = \'' . $_tmp_group . '\'';
    				$sql3 .= ' AND ( kw_group = \'' . $_tmp_group . '\'';
    				$tmp_firstitem = TRUE;
    			} else {
    				$sql2 .= ' OR kw_group = \'' . $_tmp_group . '\'';
    				$sql3 .= ' OR kw_group = \'' . $_tmp_group . '\'';
    			}
    		}
    		$sql2 .= ' ) ';
    		$sql3 .= ' ) ';

    		// 対象全件数を取得
    		if ($position_sort === FALSE)
    		{
    			$query = $this->db->query($sql2);
    			$kw_countall = $query->num_rows();
    		} else {
    			$query = $this->db->query($sql3);
    			$kw_countall = $query->num_rows();
    			$kw_countall = $kw_countall * 3;
    		}

    		// GROUP BY文 作成
    		$sql2 .= ' GROUP BY kw_url, kw_domain, kw_rootdomain, kw_keyword, kw_matchtype, kw_location_id';
    		$sql3 .= ' GROUP BY kw_url, kw_domain, kw_rootdomain, kw_keyword, kw_matchtype, kw_location_id';

    		// ORDER BY文 作成
    		if (empty($set_orderby))
    		{
    			if ($tmp_per_page == 0)
    			{
	    			$sql2 .= ' ORDER BY T3.gt_seq DESC, kw_seq DESC';
	    			$sql3 .= ' ORDER BY T3.gt_seq DESC, kw_seq DESC';
    			} else {
    				$sql2 .= ' ORDER BY T3.gt_seq DESC, kw_seq DESC LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;
    				$sql3 .= ' ORDER BY T3.gt_seq DESC, kw_seq DESC LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;
    			}
    		} else {
    			if ($tmp_per_page == 0)
    			{
    				$sql2 .= ' ORDER BY ' . $set_orderby;
    				$sql3 .= ' ORDER BY ' . $set_orderby;
    			} else {
    				$sql2 .= ' ORDER BY ' . $set_orderby . ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;
    				$sql3 .= ' ORDER BY ' . $set_orderby . ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;
    			}
    		}

    		// クエリー実行
    		if ($position_sort == FALSE)
    		{
    			$query = $this->db->query($sql2);
    		} else {
    			$query = $this->db->query($sql3);
    		}

    		$kw_list = $query->result('array');

    	} else {
    		$kw_list = array();
    		$kw_countall = 0;
    	}

    	return array($kw_list, $catalog_list, $kw_countall);

    }

    /**
     * CSVダウンロード：TOPキーワード一覧情報の取得
     *
     * @param    array() : 対象KW
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function get_csvdl_top_kwlist($arr_kw_seq, $tmp_per_page, $tmp_offset=0, $client_no)
    {

    	$sql = 'SELECT
	                  kw_seq AS `ID(U)`,
	                  kw_status AS `ステータス(U)`,
	                  kw_url AS `対象URL(U)`,
	                  kw_keyword AS `検索キーワード`,
	                  kw_matchtype AS `URL一致方式(U)`,
	                  kw_location_name AS `Canonical Name`,
	                  kw_group AS `設定グループ(U)`
	        		FROM `tb_keyword`
	        		WHERE `kw_old_seq` is NULL'
    	;

    	$sql .= ' AND `kw_cl_seq`  = ' . $client_no;

    	$i = FALSE;
    	foreach ($arr_kw_seq as $key => $value)
    	{
    		if ($i === FALSE)
    		{
    			$sql .= ' AND (kw_seq = ' . $value;
    			$i = TRUE;
    		} else {
    			$sql .= ' OR kw_seq = ' . $value;
    		}
    	}
    	$sql .= ') ';

    	// クエリー実行
    	$query = $this->db->query($sql);

    	return $query;

    }

    /**
     * CSVダウンロード：レポート情報の取得
     *
     * @param    array()
     * @param    date
     * @param    date
     * @return   array()
     */
    public function get_csvdl_report($arr_kw_pair, $_start_date, $_end_date)
    {

    	$sql = 'SELECT
		          kw_seq,
		          T1.rk_getdate,
    			  T1.rk_position,
		          kw_searchengine,
		          kw_device,
    			  kw_url,
		          kw_domain,
		          kw_rootdomain,
		          kw_keyword,
		          kw_matchtype,
		          kw_location_name
		        FROM tb_keyword LEFT JOIN tb_ranking AS T1 ON (kw_seq = rk_kw_seq)
		        WHERE '
    	;

    	$i = 0;
    	foreach ($arr_kw_pair as $key => $value)
    	{
    		if ($i == 0)
    		{
    			$sql .= '(kw_seq = ' . $value[0]['kw_seq'] . ' OR kw_seq = '. $value[1]['kw_seq'] . ' OR kw_seq = '. $value[2]['kw_seq'];
    		} else {
    			$sql .= ' OR kw_seq = ' . $value[0]['kw_seq'] . ' OR kw_seq = '. $value[1]['kw_seq'] . ' OR kw_seq = '. $value[2]['kw_seq'];
    		}
    		$i++;
    	}
    	$sql .= ') ';

    	$sql .= ' AND T1.rk_getdate BETWEEN \'' . $_start_date . '\' AND \'' . $_end_date . '\'';
    	$sql .= ' ORDER BY kw_seq ASC, T1.rk_seq ASC';

    	// クエリー実行
    	$query = $this->db->query($sql);

    	return $query;

    }

    /**
     * キーワード情報の登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_keyword($setData)
    {

    	// データ追加
    	$query = $this->db->insert('tb_keyword', $setData);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_keyword';
    	$set_data['lg_detail']    = 'kw_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * キーワード情報の更新
     *
     * @param    array()
     * @return   int
     */
    public function update_keyword($setdata)
    {

    	// UPDATE
    	$where = array(
    			'kw_seq' => $setdata['kw_seq']
    	);

    	//     	unset($setdata['kw_memo']);

    	$result = $this->db->update('tb_keyword', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_keyword';
    	$set_data['lg_detail'] = 'kw_seq = ' . $setdata['kw_seq'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);


    	return $result;

    }

    /**
     * キーワード情報の更新＆登録
     *
     * @param    array()
     * @param    text
     * @return   int
     */
    //public function up_insert_keyword($setdata, $kw_memo)
    public function up_insert_keyword($setdata)
    {

    	// 同一データの有無確認
    	$sql = 'SELECT
                  kw_seq,
                  kw_cl_seq,
                  kw_maxposition,
    			  kw_trytimes
                FROM tb_keyword
    			WHERE
    				 kw_status = 1
    			     AND kw_old_seq is NULL
    			     AND kw_cl_seq = ' . $setdata['kw_cl_seq'] . '
    			     AND kw_url = \'' . $setdata['kw_url'] . '\'
    			     AND kw_keyword = \'' . $setdata['kw_keyword'] . '\'
    			     AND kw_matchtype = ' . $setdata['kw_matchtype'] . '
    			     AND kw_searchengine = ' . $setdata['kw_searchengine'] . '
    			     AND kw_device = ' . $setdata['kw_device'] . '
    			     AND kw_location_id = \'' . $setdata['kw_location_id'] . '\'
	    ';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	if (count($get_data) > 0)
    	{

    		if ($setdata['kw_maxposition'] < $get_data[0]['kw_maxposition'])
    		{
    			$setdata['kw_maxposition'] = $get_data[0]['kw_maxposition'];
    		}

    		if ($setdata['kw_trytimes'] < $get_data[0]['kw_trytimes'])
    		{
    			$setdata['kw_trytimes'] = $get_data[0]['kw_trytimes'];
    		}

    		// UPDATE
    		$where = array(
    				'kw_seq' => $get_data[0]['kw_seq']
    		);
    		$result = $this->db->update('tb_keyword', $setdata, $where);
    		$_last_sql = $this->db->last_query();

    		// ログ書き込み
    		$set_data['lg_func']      = 'up_insert_keyword';
    		$set_data['lg_detail']    = 'kw_seq = ' . $get_data[0]['kw_seq'] . ' <= ' . $_last_sql;
    		$this->insert_log($set_data);

    	} else {

    		// INSERT
    		$result = $this->db->insert('tb_keyword', $setdata);
    		$_last_sql = $this->db->last_query();

    		// 挿入した ID 番号を取得
    		$row_id = $this->db->insert_id();

    		// ログ書き込み
    		$set_data['lg_func']      = 'up_insert_keyword';
    		$set_data['lg_detail']    = 'kw_seq = ' . $row_id . ' <= ' . $_last_sql;
    		$this->insert_log($set_data);

    	}

    	return $result;

    }

    /**
     * キーワード情報削除
     *
     * @param    array()
     * @return   int
     */
    public function delete_keyword($kw_seq, $cl_seq)
    {

    	$set_where["kw_seq"]    = $kw_seq;
    	$set_where["kw_cl_seq"] = $cl_seq;

    	$result = $this->db->delete('tb_keyword', $set_where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'delete_keyword';
    	$set_data['lg_detail'] = $_last_sql;
    	$this->insert_log($set_data);

    	return $result;

    }

    /**
     * ログ書き込み
     *
     * @param    array()
     * @return   int
     */
    public function insert_log($setData)
    {

        if (isset($_SESSION['a_memSeq'])) {
    		$setData['lg_user_id']   = $_SESSION['a_memSeq'];
    	} elseif (isset($_SESSION['c_memSeq'])) {
    		$setData['lg_user_id']   = $_SESSION['c_memSeq'];
    	} else {
    		$setData['lg_user_id']   = "";
    	}

    	$setData['lg_type']   = 'Keyword.php';
    	$setData['lg_ip'] = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    	//     	// 挿入した ID 番号を取得
    	//     	$row_id = $this->db->insert_id();
    	//     	return $row_id;
    }

}