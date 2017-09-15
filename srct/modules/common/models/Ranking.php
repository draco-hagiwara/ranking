<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ranking extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 順位データを取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_ranking_kw($kw_seq, $start_date)
    {

    	$sql = 'SELECT
                  rk_seq,
                  rk_cl_seq,
                  rk_kw_seq,
    			  rk_se_seq,
    			  rk_position
    			FROM tb_ranking
    			WHERE
    			     rk_kw_seq = ' . $kw_seq . '
    			     AND rk_getdate = \'' . $start_date . '\'
	    ';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * キーワードSEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_kw_seq($seq_no, $start_date, $end_date)
    {

    	$sql = 'SELECT
                  rk_seq,
                  rk_cl_seq,
                  rk_kw_seq,
    			  rk_se_seq,
    			  rk_getdate,
    			  rk_position
    			FROM tb_ranking
    			WHERE
    			     rk_kw_seq = ' . $seq_no . '
    			     AND rk_getdate BETWEEN  \'' . $end_date . '\' AND \'' . $start_date . '\'
    			     ORDER BY rk_seq ASC
	    ';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 旧キーワードSEQから順位取得対象SEQを取得する
     *
     * @param    date
     * @return   array()
     */
    public function get_kw_old_seq($today_date, $old_seq=NULL)
    {

    	$sql = 'SELECT
                  rk_seq,
                  rk_cl_seq,
                  rk_kw_seq,
    			  rk_se_seq,
    			  rk_getdate,
    			  rk_position
    			FROM tb_ranking
    			WHERE
    			     rk_getdate = \'' . $today_date . '\'

	    ';

    	if ($old_seq == NULL)
    	{
    		$sql .= ' AND rk_kw_old_seq is NULL ';
    	}

    	$sql .= ' ORDER BY rk_seq ASC';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * キーワードSEQから旧SEQを検索し対象となる全ての登録情報を取得する
     *
     * @param    date
     * @return   array()
     */
    public function get_top_rankingdata($kw_seq, $today_date)
    {

    	$sql = 'SELECT
                  rk_seq,
                  rk_cl_seq,
                  rk_kw_seq,
    			  rk_se_seq,
    			  rk_getdate,
    			  rk_result_id,
    			  rk_position
    			FROM tb_ranking
    			WHERE
    			     rk_getdate = \'' . $today_date . '\'
    			     AND (rk_kw_seq = ' . $kw_seq . ' OR rk_kw_old_seq = '. $kw_seq . ')
    			     ORDER BY rk_position ASC LIMIT 1
	    ';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 該当ランキング情報の取得 (取得日付)
     *
     * @param    date    : 取得日付
     * @param    int     : クライアントSEQ
     * @param    int     : 取得単位 (0:全件, 1:一部, 2:個別)
     * @return   array()
     */
    public function get_rk_getdatelist($rk_getdate, $client_no, $kind, $kw_seq=NULL)
    {

    	$sql = 'SELECT
                  rk_seq,
                  rk_cl_seq,
                  rk_kw_seq,
    			  rk_kw_old_seq,
    			  rk_se_seq,
    			  rk_result_id,
    			  rk_se_seq_re,
    			  rk_result_id_re,
    			  rk_position,
    			  rk_position_org,
    			  rk_ranking_url,
    			  rk_ranking_title,
    			  rk_getdate
    			FROM tb_ranking
    			WHERE
    			     rk_getdate = \'' . $rk_getdate . '\''
        ;

        if (!is_null($client_no ))
        {
        	$sql .= ' AND rk_cl_seq = ' . $client_no;
        }

        // WHERE文 作成
        if ($kind === 1)
        {
        	// 一部データ
        	$sql .= ' AND rk_position = 90009';
        	//$sql .= ' AND ((rk_position = 9999) OR (rk_position = 90009))';
        } elseif ($kind === 2) {
        	// 個別データ
        	$sql .= ' AND rk_kw_seq = ' . $kw_seq;
        } else {

        }

        $sql .= ' ORDER BY rk_seq ASC';

        // 対象全件数を取得
        $query = $this->db->query($sql);
        $kw_countall = $query->num_rows();

        // クエリー実行
        $query = $this->db->query($sql);
        $rank_list = $query->result('array');

        return $rank_list;

    }

    /**
     * 順位データ登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_ranking($setData)
    {

    	// データ追加
    	$query = $this->db->insert('tb_ranking', $setData);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_ranking';
    	$set_data['lg_detail']    = 'rk_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * 順位データの更新
     *
     * @param    array()
     * @return   int
     */
    public function update_ranking($setdata)
    {

    	// UPDATE
    	$where = array(
    			'rk_seq' => $setdata['rk_seq']
    	);

    	$result = $this->db->update('tb_ranking', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	return $result;

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_ranking';
    	$set_data['lg_detail'] = 'rk_seq = ' . $setdata['rk_seq'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * 順位データの更新
     *
     * @param    array()
     * @return   int
     */
    public function update_ranking_kwseq($setdata, $kw_seq)
    {

    	// UPDATE
    	$where = array(
    			'rk_kw_seq' => $kw_seq
    	);

    	$result = $this->db->update('tb_ranking', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_ranking_kwseq';
    	$set_data['lg_detail'] = 'rk_kw_seq = ' . $kw_seq . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $result;

    }

    /**
     * 順位データ情報削除
     *
     * @param    array()
     * @return   int
     */
    public function delete_ranking($kw_seq, $cl_seq)
    {

    	$set_where["rk_kw_seq"] = $kw_seq;
    	$set_where["rk_cl_seq"] = $cl_seq;

    	$result = $this->db->delete('tb_ranking', $set_where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'delete_ranking';
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

    	$setData['lg_type'] = 'Ranking.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);
    }

}