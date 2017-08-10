<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Watchlist extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ウォッチリストSEQから登録情報を取得する
     *
     * @param    int
     * @param    int
     * @param    int
     * @return   array()
     */
    public function get_watchlist_data($ac_seq, $cl_seq, $kw_seq=NULL, $rd_seq=NULL)
    {

    	$set_where["wt_ac_seq"] = $ac_seq;
    	$set_where["wt_cl_seq"] = $cl_seq;

    	if (empty($rd_seq))
    	{
    		$set_where["wt_kw_seq"] = $kw_seq;
    	} else {
    		$set_where["wt_rd_seq"] = $rd_seq;
    	}

    	$query = $this->db->get_where('tb_watchlist', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * ルートドメイン指定のウォッチリストを取得する
     *
     * @param    int
     * @param    int
     * @param    int
     * @return   array()
     */
    public function get_watchlist_domain($ac_seq, $cl_seq, $rd_seq)
    {

    	$sql = 'SELECT
	                  wt_seq,
	                  wt_cl_seq,
	                  wt_ac_seq,
	    			  wt_rd_seq,
    				  wt_kw_rootdomain
	    	    	FROM tb_watchlist
	    			WHERE wt_ac_seq = ' . $ac_seq
	    				. ' AND wt_cl_seq = ' . $cl_seq
	    				. ' AND wt_kw_seq is NULL AND wt_rd_seq = ' . $rd_seq
    	;

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * ウォッチリスト登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_watchlist($setData)
    {

    	// データ追加
    	$query = $this->db->insert('tb_watchlist', $setData);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_watchlist';
    	$set_data['lg_detail']    = 'wt_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }


    /**
     * ウォッチリスト更新
     *
     * @param    array()
     * @return   int
     */
    public function update_watchlist($setdata)
    {

    	// UPDATE
    	$where = array(
    			'wt_seq' => $setdata['wt_seq']
    	);

    	$result = $this->db->update('tb_watchlist', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	return $result;

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_watchlist';
    	$set_data['lg_detail'] = 'wt_seq = ' . $setdata['wt_seq'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * ウォッチリスト削除
     *
     * @param    array()
     * @return   int
     */
    public function delete_watchlist($setdata)
    {

    	$set_where["wt_ac_seq"] = $setdata["wt_ac_seq"];
    	$set_where["wt_cl_seq"] = $setdata["wt_cl_seq"];

    	if (isset($setdata["wt_kw_seq"]))
    	{
    		$set_where["wt_kw_seq"] = $setdata["wt_kw_seq"];
    	} else {
    		$set_where["wt_rd_seq"] = $setdata["wt_rd_seq"];
    	}

    	$result = $this->db->delete('tb_watchlist', $set_where);
    	$_last_sql = $this->db->last_query();

    	return $result;

    	// ログ書き込み
    	$set_data['lg_func']   = 'delete_watchlist';
    	$set_data['lg_detail'] = $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * ウォッチリスト削除
     *
     * @param    array()
     * @return   int
     */
    public function delete_wt_list($kw_seq=NULL, $cl_seq, $rd_seq=NULL)
    {

    	$set_where["wt_cl_seq"] = $cl_seq;

    	if (empty($rd_seq))
    	{
    		$set_where["wt_kw_seq"] = $kw_seq;
    	} else {
    		$set_where["wt_rd_seq"] = $rd_seq;
    	}

    	$result = $this->db->delete('tb_watchlist', $set_where);
    	$_last_sql = $this->db->last_query();

    	return $result;

    	// ログ書き込み
    	$set_data['lg_func']   = 'delete_watchlist';
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

    	$setData['lg_type']   = 'Watchlist.php';
    	$setData['lg_ip'] = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    	//     	// 挿入した ID 番号を取得
    	//     	$row_id = $this->db->insert_id();
    	//     	return $row_id;
    }

}