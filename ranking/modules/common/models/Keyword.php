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
     * 旧SEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_kw_oldseq($seq_no)
    {

    	$set_where["kw_old_seq"] = $seq_no;

    	$query = $this->db->get_where('tb_keyword', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * キーワードSEQからキーワード情報とメモ情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_kw_seq_memo($seq_no)
    {

    	$sql = 'SELECT
                  T1.kw_seq,
                  T1.kw_status,
                  T1.kw_url,
                  T1.kw_domain,
                  T1.kw_rootdomain,
                  T1.kw_keyword,
                  T1.kw_matchtype,
                  T1.kw_searchengine,
                  T1.kw_device,
                  T1.kw_location_id,
                  T1.kw_location_name,
                  T1.kw_maxposition,
                  T1.kw_trytimes,
                  T1.kw_group,
                  T1.kw_tag,
                  T1.kw_cl_seq,
                  T1.kw_ac_seq,
                  T2.me_memo,
                  T2.me_create_date
                FROM tb_keyword AS T1 LEFT JOIN tb_memo AS T2 ON T1.kw_seq = T2.me_kw_seq
    			WHERE
    			     T1.kw_seq = ' . $seq_no
	     		;

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 設定グループから情報を取得する
     *
     * @param    array()
     * @return   array()
     */
    public function get_kw_group($setdata)
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
    			     AND kw_group = \'' . $setdata['kw_group'] . '\'
	     		';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 設定タグから情報を取得する
     *
     * @param    array()
     * @return   array()
     */
    public function get_kw_tag($setdata)
    {

    	// 同一データの有無確認
    	$sql = 'SELECT
                  kw_seq,
                  kw_cl_seq,
    			  kw_tag
                FROM tb_keyword
    			WHERE
    			     kw_status = 1
    			     AND kw_cl_seq = ' . $setdata['kw_cl_seq'] . '
    			     AND kw_tag LIKE \'%[' . $setdata['kw_tag'] . ']%\'
	     		';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * キーワードURLから登録情報を取得する
     *
     * @param    array()
     * @return   array()
     */
    public function get_kw_url($setdata)
    {

    	// 同一データの有無確認
    	$sql = 'SELECT
                  kw_seq,
                  kw_cl_seq,
                  kw_maxposition,
    			  kw_trytimes
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

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 検索用にKeywordデータを抽出
     *
     * @param    int
     * @return   array()
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
//     	$get_data = $query->result('array');
    	$grouptag_cnt = $query->num_rows();

    	return $grouptag_cnt;

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
     * キーワード情報(keywordlist)の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function get_keywordlist($get_post, $tmp_per_page, $tmp_offset=0, $client_no)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select_like["kw_keyword"] = $get_post['kw_keyword'];
    	$set_select_like["kw_rootdomain"]  = $get_post['kw_domain'];
    	//$set_select_like["kw_domain"]  = $get_post['kw_domain'];

    	$set_select["kw_status"]       = $get_post['kw_status'];
    	$set_select["kw_cl_seq"]       = $client_no;

    	// ORDER BY
    	if ($get_post['orderid'] != '')
    	{
    		$set_orderby["kw_seq"]           = $get_post['orderid'];
    	}else {
    		// デフォルト設定
    		$set_orderby["kw_keyword"]       = 'ASC';
    		$set_orderby["kw_matchtype"]     = 'ASC';
    		$set_orderby["kw_searchengine"]  = 'ASC';
    		$set_orderby["kw_device"]        = 'ASC';
    		$set_orderby["kw_location_name"] = 'ASC';
    		//$set_orderby["kw_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$kw_list = $this->_select_kwlist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset, $client_no);

    	return $kw_list;

    }

    /**
     * キーワード情報(keywordlist)の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function _select_kwlist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0, $client_no)
    {

    	// ** rootdomain を検索
    	$sql = 'SELECT
                  kw_seq,
                  kw_cl_seq,
                  kw_ac_seq,
    			  kw_rootdomain
    	    	FROM tb_keyword WHERE kw_cl_seq = ' . $client_no . ' AND kw_old_seq is NULL'
    	;

    	if ($set_select["kw_status"] !== '')
    	{
    		$sql .= ' AND `kw_status`  = ' . $set_select["kw_status"];
    	}

    	// WHERE文 作成
    	foreach ($set_select_like as $key => $val)
    	{
    		if (isset($val) && $val !== '')
    		{
    			$sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
    		}
    	}

     	// ORDER BY文 作成
    	$sql .= ' GROUP BY kw_rootdomain';

    	// ORDER BY文 作成
    	if (isset($set_orderby['kw_seq']))
    	{
    		$sql .= ' ORDER BY kw_seq ' . $set_orderby['kw_seq'];
    	}else {
    		$sql .= ' ORDER BY kw_rootdomain ASC';                                    // デフォルト
    	}

    	// 対象全件数を取得
    	$query = $this->db->query($sql);
    	$rootdomain_countall = $query->num_rows();

//     	print($sql);
//     	print("<br><br>");
//     	print($rootdomain_countall);
//     	print("<br><br>");



    	// LIMIT ＆ OFFSET 値をセット
    	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$rootdomain_list = $query->result('array');



//     	print($sql);
//     	print("<br><br>");



    	// ** キーワード情報 を検索
    	$sql = 'SELECT
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
    			  T2.wt_seq
                FROM tb_keyword LEFT JOIN tb_watchlist as T2 on (kw_seq = wt_kw_seq)
    			WHERE kw_old_seq is NULL AND kw_cl_seq = ' . $client_no
    			//FROM tb_keyword WHERE kw_cl_seq = ' . $client_no
    			//WHERE kw_cl_seq = ' . $client_no
		;

        if ($set_select["kw_status"] !== '')
        {
        	$sql .= ' AND `kw_status`  = ' . $set_select["kw_status"];
        }

        // WHERE文 作成
        foreach ($set_select_like as $key => $val)
        {
        	if (isset($val) && $val !== '')
        	{
            	$sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
            }
        }

        // WHERE文(rootdomain) 作成
        $i = 0;
        foreach ($rootdomain_list as $key => $val)
        {
        	if ($i === 0)
        	{
        		$sql .= ' AND ( kw_rootdomain LIKE \'%' . $val['kw_rootdomain'] . '%\'';
        	} else {
       			$sql .= ' OR kw_rootdomain LIKE \'%' . $val['kw_rootdomain'] . '%\'';
        	}
        	++$i;
        }
        if ($i !== 0)
        {
        	$sql .= ' ) ';
        }

        // ORDER BY文 作成
        $tmp_firstitem = FALSE;
        foreach ($set_orderby as $key => $val)
        {
        	if (isset($val) && $val !== '')
            {
            	if ($tmp_firstitem == FALSE)
                {
                	//$sql .= ' ORDER BY ' . $key . ' ' . $val;
                	$sql .= ' ORDER BY  kw_rootdomain ASC, ' . $key . ' ' . $val;
                    $tmp_firstitem = TRUE;
                } else {
                    $sql .= ' , ' . $key . ' ' . $val;
                }
            }
        }
        if ($tmp_firstitem === FALSE)
        {
        	$sql .= ' ORDER BY kw_rootdomain ASC, kw_keyword DESC';                                    // デフォルト
        }

        // クエリー実行
        $query = $this->db->query($sql);
        $kw_list = $query->result('array');


//         print($sql);
//         print("<br><br>");


        return array($kw_list, $rootdomain_countall);
        //return array($kw_list, $kw_countall);

    }

    /**
     * キーワード情報(TOP)の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function get_kw_toplist($get_post, $tmp_per_page, $tmp_offset=0, $client_no)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select_like["kw_keyword"] = $get_post['kw_keyword'];
    	$set_select_like["kw_url"]     = $get_post['kw_domain'];
    	//$set_select_like["kw_domain"]  = $get_post['kw_domain'];
    	$set_select_like["kw_group"]   = $get_post['kw_group'];
    	$set_select_like["kw_tag"]     = $get_post['kw_tag'];

    	$set_select["kw_ac_seq"]       = $get_post['kw_ac_seq'];
    	$set_select["kw_status"]       = $get_post['kw_status'];
    	$set_select["kw_cl_seq"]       = $client_no;
    	$set_select["kw_matchtype"]    = $get_post['kw_matchtype'];
    	$set_select["kw_searchengine"] = $get_post['kw_searchengine'];
    	$set_select["kw_device"]       = $get_post['kw_device'];
    	$set_select["kw_location_id"]  = $get_post['kw_location_id'];


    	if ($get_post['watchlist'] === '0')
    	{
    		$set_select["wt_seq"]      = 0;
    	} else {
    		$set_select["wt_seq"]      = 1;
    	}

    	// ORDER BY
    	if ($get_post['orderid'] === 'ASC')
    	{
    		$set_orderby["kw_seq"] = $get_post['orderid'];
    	}else {
    		// デフォルト設定
//     		$set_orderby["kw_keyword"]       = 'ASC';
//     		$set_orderby["kw_matchtype"]     = 'ASC';
//     		$set_orderby["kw_searchengine"]  = 'ASC';
//     		$set_orderby["kw_device"]        = 'ASC';
//     		$set_orderby["kw_location_name"] = 'ASC';
    		$set_orderby["kw_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$kw_list = $this->_select_kwtoplist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset, $client_no);

    	return $kw_list;

    }

    /**
     * キーワード情報(TOP)の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function _select_kwtoplist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0, $client_no)
    {

        // ** キーワード情報 を検索
        $sql = 'SELECT
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
    			  T2.wt_seq
                FROM tb_keyword LEFT JOIN tb_watchlist as T2 on (kw_seq = wt_kw_seq)
    			WHERE kw_old_seq is NULL AND kw_cl_seq = ' . $client_no
        ;

        $sql .= ' AND `kw_status`  = ' . $set_select["kw_status"];

		if ($set_select["kw_matchtype"] !== '')
		{
			$sql .= ' AND `kw_matchtype`  = ' . $set_select["kw_matchtype"];
		}

		if ($set_select["kw_searchengine"] !== '')
		{
			$sql .= ' AND `kw_searchengine`  = ' . $set_select["kw_searchengine"];
		}

		if ($set_select["kw_device"] !== '')
		{
			$sql .= ' AND `kw_device`  = ' . $set_select["kw_device"];
		}

		if ($set_select["kw_location_id"] !== '')
		{
			$sql .= ' AND `kw_location_id`  = ' . $set_select["kw_location_id"];
		}

        if ($set_select["kw_ac_seq"] !== "0")
        {
        	$sql .= ' AND `kw_ac_seq`  = ' . $set_select["kw_ac_seq"];
        }

        if ($set_select["wt_seq"] === 1)
        {
        	$sql .= ' AND `wt_seq` IS NOT NULL ';
        }

        // WHERE文 作成
        foreach ($set_select_like as $key => $val)
        {
        	if (isset($val) && $val !== '')
        	{
        		$sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
        	}
        }

        // ORDER BY文 作成
        $tmp_firstitem = FALSE;
        foreach ($set_orderby as $key => $val)
        {
        	if (isset($val) && $val !== '')
        	{
        		if ($tmp_firstitem === FALSE)
        		{
        			$sql .= ' ORDER BY ' . $key . ' ' . $val;
        			//$sql .= ' ORDER BY  kw_rootdomain ASC, ' . $key . ' ' . $val;

        			$tmp_firstitem = TRUE;
        		} else {
        			$sql .= ' , ' . $key . ' ' . $val;
        		}
        	}
        }

//         if ($tmp_firstitem == FALSE)
//         {
//         	$sql .= ' ORDER BY kw_rootdomain ASC, kw_searchengine ASC, kw_keyword DESC';       // デフォルト

//         	$set_orderby["kw_keyword"]       = 'ASC';
//         	$set_orderby["kw_matchtype"]     = 'ASC';
//         	$set_orderby["kw_searchengine"]  = 'ASC';
//         	$set_orderby["kw_device"]        = 'ASC';
//         	$set_orderby["kw_location_name"] = 'ASC';

//         }



// 		print($sql);
// 		print("<br><br>");



        // 対象全件数を取得
        $query = $this->db->query($sql);
        $kw_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
        $kw_list = $query->result('array');

        return array($kw_list, $kw_countall);

    }

    /**
     * グループ設定情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_kw_grouplist($get_post, $tmp_per_page, $tmp_offset=0)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select_like["kw_keyword"] = $get_post['kw_keyword'];
    	$set_select_like["kw_domain"]  = $get_post['kw_domain'];

    	$set_select["kw_group"]        = $get_post['kw_group'];
    	$set_select["kw_status"]       = $get_post['kw_status'];
    	$set_select["kw_cl_seq"]       = $get_post['kw_cl_seq'];

    	// ORDER BY
    	if ($get_post['orderid'] == 'ASC')
    	{
    		$set_orderby["kw_seq"] = $get_post['orderid'];
    	}else {
    		$set_orderby["kw_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$kw_list = $this->_select_grouplist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset);

    	return $kw_list;

    }

    /**
     * グループ設定情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_grouplist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0)
    {

    	$sql = 'SELECT
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
                  kw_tag
                FROM tb_keyword WHERE kw_cl_seq = ' . $set_select["kw_cl_seq"] . ' AND kw_group = \'' . $set_select["kw_group"] . '\''
        ;

        if ($set_select["kw_status"] != '')
        {
        	$sql .= ' AND `kw_status`  = ' . $set_select["kw_status"];
        }

        // WHERE文 作成
        foreach ($set_select_like as $key => $val)
        {
        	if (isset($val) && $val != '')
        	{
        		$sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
        	}
        }

        // ORDER BY文 作成
        $tmp_firstitem = FALSE;
        foreach ($set_orderby as $key => $val)
        {
        	if (isset($val) && $val != '')
        	{
        		if ($tmp_firstitem == FALSE)
        		{
        			$sql .= ' ORDER BY  kw_rootdomain ASC, ' . $key . ' ' . $val;
        			$tmp_firstitem = TRUE;
        		} else {
        			$sql .= ' , ' . $key . ' ' . $val;
        		}
        	}
        }
        if ($tmp_firstitem == FALSE)
        {
        	$sql .= ' ORDER BY kw_rootdomain ASC, kw_keyword DESC';                                    // デフォルト
        }

        // 対象全件数を取得
        $query = $this->db->query($sql);
        $kw_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
        $kw_list = $query->result('array');

        return array($kw_list, $kw_countall);
    }

    /**
     * タグ設定情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_kw_taglist($get_post, $tmp_per_page, $tmp_offset=0)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select_like["kw_keyword"] = $get_post['kw_keyword'];
    	$set_select_like["kw_domain"]  = $get_post['kw_domain'];

    	$set_select["kw_tag"]          = $get_post['kw_tag'];
    	$set_select["kw_status"]       = $get_post['kw_status'];
    	$set_select["kw_cl_seq"]       = $get_post['kw_cl_seq'];

    	// ORDER BY
    	if ($get_post['orderid'] == 'ASC')
    	{
    		$set_orderby["kw_seq"] = $get_post['orderid'];
    	}else {
    		$set_orderby["kw_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$kw_list = $this->_select_taglist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset);

    	return $kw_list;

    }

    /**
     * タグ設定情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_taglist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0)
    {

    	$sql = 'SELECT
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
                  kw_tag
                FROM tb_keyword WHERE kw_cl_seq = ' . $set_select["kw_cl_seq"]
                . ' AND kw_tag LIKE \'%[' . $this->db->escape_like_str($set_select["kw_tag"]) . ']%\''
        ;

        if ($set_select["kw_status"] != '')
        {
        	$sql .= ' AND `kw_status`  = ' . $set_select["kw_status"];
        }

        // WHERE文 作成
        foreach ($set_select_like as $key => $val)
        {
        	if (isset($val) && $val != '')
        	{
        		$sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
        	}
        }

        // ORDER BY文 作成
        $tmp_firstitem = FALSE;
        foreach ($set_orderby as $key => $val)
        {
        	if (isset($val) && $val != '')
        	{
        		if ($tmp_firstitem == FALSE)
        		{
        			$sql .= ' ORDER BY  kw_rootdomain ASC, ' . $key . ' ' . $val;
        			$tmp_firstitem = TRUE;
        		} else {
        			$sql .= ' , ' . $key . ' ' . $val;
        		}
        	}
        }
        if ($tmp_firstitem == FALSE)
        {
        	$sql .= ' ORDER BY kw_rootdomain ASC, kw_keyword DESC';                                    // デフォルト
        }

        // 対象全件数を取得
        $query = $this->db->query($sql);
        $kw_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
        $kw_list = $query->result('array');

        return array($kw_list, $kw_countall);

    }

    /**
     * ルートドメイン設定情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_kw_rootdomainlist($get_post, $tmp_per_page, $tmp_offset=0)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select_like["kw_keyword"] = $get_post['kw_keyword'];
    	$set_select_like["kw_domain"]  = $get_post['kw_domain'];

    	$set_select["kw_rootdomain"]   = $get_post['kw_rootdomain'];
    	$set_select["kw_status"]       = $get_post['kw_status'];
    	$set_select["kw_cl_seq"]       = $get_post['kw_cl_seq'];

    	// ORDER BY
    	if ($get_post['orderid'] == 'ASC')
    	{
    		$set_orderby["kw_seq"] = $get_post['orderid'];
    	}else {
    		// デフォルト設定
    		$set_orderby["kw_keyword"]       = 'ASC';
    		$set_orderby["kw_matchtype"]     = 'ASC';
    		$set_orderby["kw_searchengine"]  = 'ASC';
    		$set_orderby["kw_device"]        = 'ASC';
    		$set_orderby["kw_location_name"] = 'ASC';
    		//$set_orderby["kw_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$kw_list = $this->_select_rootdomainlist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset);

    	return $kw_list;

    }

    /**
     * ルートドメイン設定情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_rootdomainlist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0)
    {

    	$sql = 'SELECT
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
                  kw_tag
                FROM tb_keyword WHERE kw_cl_seq = ' . $set_select["kw_cl_seq"]
                . ' AND kw_old_seq is NULL
                	AND kw_rootdomain = \'' . $this->db->escape_like_str($set_select["kw_rootdomain"]) . '\''
        ;

        if ($set_select["kw_status"] != '')
        {
        	$sql .= ' AND `kw_status`  = ' . $set_select["kw_status"];
        }

        // WHERE文 作成
        foreach ($set_select_like as $key => $val)
        {
        	if (isset($val) && $val != '')
        	{
        		$sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
        	}
        }

        // ORDER BY文 作成
        $tmp_firstitem = FALSE;
        foreach ($set_orderby as $key => $val)
        {
        	if (isset($val) && $val != '')
        	{
        		if ($tmp_firstitem == FALSE)
        		{
        			$sql .= ' ORDER BY  kw_rootdomain ASC, ' . $key . ' ' . $val;
        			$tmp_firstitem = TRUE;
        		} else {
        			$sql .= ' , ' . $key . ' ' . $val;
        		}
        	}
        }
        if ($tmp_firstitem == FALSE)
        {
        	$sql .= ' ORDER BY kw_rootdomain ASC, kw_keyword DESC';                                    // デフォルト
        }

        // 対象全件数を取得
        $query = $this->db->query($sql);
        $kw_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
        $kw_list = $query->result('array');

        return array($kw_list, $kw_countall);

    }

    /**
     * キーワード情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function get_csvdl_list($get_post, $tmp_per_page, $tmp_offset=0, $client_no)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select_like["kw_keyword"] = $get_post['kw_keyword'];
    	$set_select_like["kw_domain"]  = $get_post['kw_domain'];

    	$set_select["kw_status"]       = $get_post['kw_status'];
    	$set_select["kw_cl_seq"]       = $client_no;

    	// ORDER BY
    	if ($get_post['orderid'] == 'ASC')
    	{
    		$set_orderby["kw_seq"] = $get_post['orderid'];
    	}else {
    		$set_orderby["kw_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$query = $this->_select_dllist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset, $client_no);
//     	$kw_list = $this->_select_dllist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset, $client_no);

    	return $query;
//     	return $kw_list;

    }

    /**
     * キーワード情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function _select_dllist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0, $client_no)
    {

    	$sql = 'SELECT
                  kw_seq AS `seq`,
                  kw_status AS `ステータス`,
                  kw_url AS `対象URL`,
                  kw_domain AS `ドメイン`,
                  kw_rootdomain AS `ルートドメイン`,
                  kw_keyword AS `検索キーワード`,
                  kw_matchtype AS `URL一致方式`,
                  kw_searchengine AS `検索エンジン選択`,
                  kw_device AS `デバイス選択`,
                  kw_location_name AS `Canonical Name`,
                  kw_maxposition AS `最大取得順位`,
                  kw_trytimes AS `データ取得回数`,
                  kw_group AS `設定グループ`,
                  kw_tag AS `設定タグ`
                FROM tb_keyword WHERE kw_cl_seq = ' . $client_no
        ;

        if ($set_select["kw_status"] != '')
        {
        	$sql .= ' AND `kw_status`  = ' . $set_select["kw_status"];
        }

        // WHERE文 作成
        foreach ($set_select_like as $key => $val)
        {
        	if (isset($val) && $val != '')
        	{
        		$sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
        	}
        }

        // ORDER BY文 作成
        $tmp_firstitem = FALSE;
        foreach ($set_orderby as $key => $val)
        {
        	if (isset($val) && $val != '')
        	{
        		if ($tmp_firstitem == FALSE)
        		{
        			$sql .= ' ORDER BY  kw_rootdomain ASC, ' . $key . ' ' . $val;
        			$tmp_firstitem = TRUE;
        		} else {
        			$sql .= ' , ' . $key . ' ' . $val;
        		}
        	}
        }
        if ($tmp_firstitem == FALSE)
        {
        	$sql .= ' ORDER BY kw_rootdomain ASC, kw_keyword DESC';                                    // デフォルト
        }

        // 対象全件数を取得
        $query = $this->db->query($sql);
        $kw_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
//         $kw_list = $query->result('array');

        return $query;
//         return array($kw_list, $kw_countall);

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
     * キーワード情報の更新＆登録
     *
     * @param    array()
     * @param    text
     * @return   int
     */
    public function up_insert_keyword($setdata, $kw_memo)
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

//     	print("<br><br>");
//     	print($sql);



    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	if (count($get_data) > 0)
    	{

//     		print("<br><br>");
//     		print_r($setdata);

//     		print("<br><br>");
//     		print_r($get_data);

//     		print("<br><br>");
//     		print_r($setdata['kw_maxposition']);
//     		print(" > ");
//     		print_r($get_data[0]['kw_maxposition']);

//     		print("<br><br>");
//     		print_r($setdata['kw_trytimes']);
//     		print(" > ");
//     		print_r($get_data[0]['kw_trytimes']);
//     		print("<br><br>");




    		if (($setdata['kw_maxposition'] > $get_data[0]['kw_maxposition'])
    			|| ($setdata['kw_trytimes'] > $get_data[0]['kw_trytimes']))
    		{
//     			print("<br>更新<br>");

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



    			// tb_memo へ書き込み
    			if ($kw_memo != "")
    			{
    				$this->_insert_kw_memo($get_data[0]['kw_seq'], $kw_memo, $setdata['kw_cl_seq'], $setdata['kw_ac_seq']);
    			}



    			// ログ書き込み
    			$set_data['lg_func']      = 'up_insert_keyword';
    			$set_data['lg_detail']    = 'kw_seq = ' . $get_data[0]['kw_seq'] . ' <= ' . $_last_sql;
    			$this->insert_log($set_data);

    		} else {
//     			print("<br>現行<br>");

    			$result = TRUE;
    		}
    	} else {
//     		print("<br>挿入<br>");

//     		print_r($setdata);
//     		print("<br><br>");
//     		exit;


    		// INSERT
    		$result = $this->db->insert('tb_keyword', $setdata);
    		$_last_sql = $this->db->last_query();

    		// 挿入した ID 番号を取得
    		$row_id = $this->db->insert_id();


    		// tb_memo へ書き込み
    		if ($kw_memo != "")
    		{
    			$this->_insert_kw_memo($row_id, $kw_memo, $setdata['kw_cl_seq'], $setdata['kw_ac_seq']);
    		}



    		// ログ書き込み
    		$set_data['lg_func']      = 'up_insert_keyword';
    		$set_data['lg_detail']    = 'kw_seq = ' . $row_id . ' <= ' . $_last_sql;
    		$this->insert_log($set_data);

    	}

    	return $result;

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

    	unset($setdata['kw_memo']);

    	$result = $this->db->update('tb_keyword', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_keyword';
    	$set_data['lg_detail'] = 'kw_seq = ' . $setdata['kw_seq'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);


    	return $result;

    }

    /**
     * キーワード情報の更新
     *
     * @param    array()
     * @return   int
     */
    public function update_kw_all($setdata, $gt_flg)
    {

    	// UPDATE
    	if ($gt_flg === 0)
    	{
    		$sql = 'UPDATE `tb_keyword` SET `kw_group`= \''
    				. $setdata['gt_name'] . '\''
    				. ' WHERE `kw_cl_seq`= ' . $setdata['kw_cl_seq']
    				. ' AND `kw_group`= \'' . $setdata['old_gt_name'] . '\''
    		;
    	} else {
    		$sql = 'UPDATE `tb_keyword` SET `kw_group`= \''
    				. $setdata['gt_name'] . '\''
    				. ' WHERE `kw_cl_seq`= ' . $setdata['kw_cl_seq']
    				. ' AND `kw_group`= \'' . $setdata['old_gt_name'] . '\''
    		;
    	}

    	$query = $this->db->query($sql);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_kw_all';
    	$set_data['lg_detail'] = $_last_sql;
    	$this->insert_log($set_data);

    	return;

    }

    /**
     * メモ情報の書き込み
     *
     * @param    int
     * @param    text
     * @param    int
     * @param    int
     * @return   int
     */
    public function _insert_kw_memo($kw_seq, $kw_memo, $cl_seq, $ac_seq)
    {

    	// データ追加
    	$setData['me_kw_seq'] = $kw_seq;
    	$setData['me_memo']   = $kw_memo;
    	$setData['me_cl_seq'] = $cl_seq;
    	$setData['me_ac_seq'] = $ac_seq;

    	$query = $this->db->insert('tb_memo', $setData);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_client';
    	$set_data['lg_detail']    = 'cl_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

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