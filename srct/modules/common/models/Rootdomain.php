<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rootdomain extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ルートドメインSEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_rd_seq($seq_no)
    {

    	$set_where["rd_seq"] = $seq_no;

    	$query = $this->db->get_where('tb_rootdomain', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 設定タグから情報を取得する
     *
     * @param    array()
     * @return   array()
     */
    public function get_rd_tag($setdata)
    {

    	// 同一データの有無確認
    	$sql = 'SELECT
                  rd_seq,
                  rd_cl_seq,
    			  rd_tag
                FROM tb_rootdomain
    			WHERE
    			     rd_cl_seq = ' . $setdata['rd_cl_seq'] . '
    			     AND rd_tag LIKE \'%[' . $setdata['rd_tag'] . ']%\'
	     		';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * ルートドメイン情報の重複チェック
     *
     * @param    array()
     * @return   int
     */
    public function check_rootdomain($setdata)
    {

    	$sql = 'SELECT
                  rd_seq,
                  rd_cl_seq
                FROM tb_rootdomain
    			WHERE
    			     rd_cl_seq = ' . $setdata['rd_cl_seq'] . '
    			     AND rd_rootdomain = \'' . $setdata['rd_rootdomain'] . '\'
	    ';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	//$get_rows = $query->num_rows();
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * ルートドメイン情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function get_rootdomainlist($get_post, $tmp_per_page, $tmp_offset=0, $member)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select_like["rd_rootdomain"] = $get_post['rd_rootdomain'];
    	$set_select_like["rd_sitename"]   = $get_post['rd_sitename'];
    	$set_select_like["rd_group"]      = $get_post['rd_group'];
    	$set_select_like["rd_tag"]        = $get_post['rd_tag'];

    	if ($get_post['watchlist'] == 0)
    	{
    		$set_select["wt_seq"]      = 0;
    	} else {
    		$set_select["wt_seq"]      = 1;
    	}

    	// ORDER BY
    	if ($get_post['orderid'] == 'ASC')
    	{
    		$set_orderby["rd_seq"] = $get_post['orderid'];
    	}else {
    		// デフォルト設定
    		$set_orderby["rd_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$rd_list = $this->_select_rootdomainlist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset, $member);

    	return $rd_list;

    }

    /**
     * ルートドメイン情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @param    int     : クライアントSEQ
     * @return   array()
     */
    public function _select_rootdomainlist($set_select, $set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0, $member)
    {

        // ** ルートドメイン情報 を検索
        $sql = 'SELECT
                  rd_seq,
                  rd_cl_seq,
                  rd_rootdomain,
                  rd_sitename,
        		  rd_keyword_cnt,
        		  T2.wt_seq,
        		  T2.wt_ac_seq
        		FROM `tb_rootdomain` LEFT JOIN `tb_watchlist` as T2 on ((rd_seq = wt_rd_seq) AND (wt_ac_seq = ' . $member['account'] . '))
    			WHERE rd_cl_seq = ' . $member['group']
        ;

        // WHERE文 作成
        if ($set_select["wt_seq"] == 1)
        {
        	$sql .= ' AND `wt_seq` IS NOT NULL ';
        }

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
        	if (isset($val))
        	{
        		if ($tmp_firstitem == FALSE)
        		{
        			$sql .= ' ORDER BY ' . $key . ' ' . $val;
        			$tmp_firstitem = TRUE;
        		} else {
        			$sql .= ' , ' . $key . ' ' . $val;
        		}
        	}
        }

        // 対象全件数を取得
        $query = $this->db->query($sql);
        $rd_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
        $rd_list = $query->result('array');

        return array($rd_list, $rd_countall);

    }

    /**
     * ルートドメイン情報の登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_rootdomain($setData)
    {

    	// データ追加
    	$query = $this->db->insert('tb_rootdomain', $setData);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_rootdomain';
    	$set_data['lg_detail']    = 'rd_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * ルートドメイン情報の更新
     *
     * @param    array()
     * @return   int
     */
    public function update_rootdomain($setdata)
    {

    	// UPDATE
    	$where = array(
    			'rd_seq' => $setdata['rd_seq']
    	);

    	unset($setdata['rd_memo']);

    	$result = $this->db->update('tb_rootdomain', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	return $result;

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_rootdomain';
    	$set_data['lg_detail'] = 'rd_seq = ' . $setdata['rd_seq'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * ルートドメイン情報のグループ一括更新
     *
     * @param    array()
     * @return   int
     */
    public function update_rd_grp($setdata)
    {

    	// UPDATE
    	$sql = 'UPDATE `tb_rootdomain` SET `rd_group`= \''
    			. $setdata['gt_name'] . '\''
    			. ' WHERE `rd_cl_seq`= ' . $setdata['rd_cl_seq']
    			. ' AND `rd_group`= \'' . $setdata['old_gt_name'] . '\''
    	;

    	$query = $this->db->query($sql);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_rd_all';
    	$set_data['lg_detail'] = $_last_sql;
    	$this->insert_log($set_data);

    	return;

    }

    /**
     * ルートドメイン情報のタグ一括更新
     *
     * @param    array()
     * @return   int
     */
    public function update_rd_tag($setdata)
    {

    	// UPDATE
    	$sql = 'UPDATE `tb_rootdomain` SET `rd_tag`= \''
    			. $setdata['rd_tag'] . '\''
    			. ' WHERE `rd_seq`= ' . $setdata['rd_seq']
    	;

    	$query = $this->db->query($sql);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_rd_all';
    	$set_data['lg_detail'] = $_last_sql;
    	$this->insert_log($set_data);

    	return;

    }

    /**
     * ルートドメイン情報削除
     *
     * @param    array()
     * @return   int
     */
    public function delete_rootdomain($rd_seq, $cl_seq, $rootdomain)
    {

    	if ($rd_seq == NULL)
    	{
	    	$set_where["rd_cl_seq"]     = $cl_seq;
	    	$set_where["rd_rootdomain"] = $rootdomain;
	    } else {
    		$set_where["rd_seq"]        = $rd_seq;
    	}

    	$result = $this->db->delete('tb_rootdomain', $set_where);
    	$_last_sql = $this->db->last_query();

    	return $result;

    	// ログ書き込み
    	$set_data['lg_func']   = 'delete_rootdomain';
    	$set_data['lg_detail'] = $_last_sql;
    	$this->insert_log($set_data);

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

    	$setData['lg_type']   = 'Rootdomain.php';
    	$setData['lg_ip'] = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    	//     	// 挿入した ID 番号を取得
    	//     	$row_id = $this->db->insert_id();
    	//     	return $row_id;
    }

}