<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comm_dashboard extends CI_Model
{

	/*
	 * ダッシュボード専用
	 */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 登録キーワード数のカウント
     *
     * @param    array()
     * @return   int
     */
    public function get_db_kwcount($setdata)
    {

    	// 全登録数
    	$sql = 'SELECT
                  kw_seq,
                  kw_cl_seq
                FROM tb_keyword
    			WHERE
    			     kw_status = 1
    			     AND kw_cl_seq = ' . $setdata['cl_seq'] . '
    			     AND kw_old_seq is NULL
	    ';

    	$query = $this->db->query($sql);
    	$get_cnt = $query->num_rows();

    	// Google登録数
    	$sql = 'SELECT
                  kw_seq,
                  kw_cl_seq
                FROM tb_keyword
    			WHERE
    			     kw_status = 1
    			     AND kw_searchengine = 0
    				 AND kw_cl_seq = ' . $setdata['cl_seq'] . '
    			     AND kw_old_seq is NULL
	    ';

    	$query = $this->db->query($sql);
    	$get_g_cnt = $query->num_rows();

    	// Yahoo!登録数
    	$sql = 'SELECT
                  kw_seq,
                  kw_cl_seq
                FROM tb_keyword
    			WHERE
    			     kw_status = 1
    			     AND kw_searchengine = 1
    				 AND kw_cl_seq = ' . $setdata['cl_seq'] . '
    			     AND kw_old_seq is NULL
	    ';

    	$query = $this->db->query($sql);
    	$get_y_cnt = $query->num_rows();

    	return array($get_cnt, $get_g_cnt, $get_y_cnt);

    }

    /**
     * 登録ルートドメイン数のカウント
     *
     * @param    array()
     * @return   int
     */
    public function get_db_rdcount($setdata)
    {

    	// 全登録数
    	$sql = 'SELECT
                  rd_seq,
                  rd_cl_seq
                FROM tb_rootdomain
    			WHERE
    			     rd_cl_seq = ' . $setdata['cl_seq'] . '
 	    ';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	//$get_data = $query->result('array');
    	$get_cnt = $query->num_rows();

    	return $get_cnt;

    }

    /**
     * 月次キーワード登録数の推移
     *
     * @return   int
     */
    public function get_db_kwtran()
    {

    	// 月別レコード集計
    	$sql = 'SELECT
                  DATE_FORMAT(kw_create_date, \'%Y-%m\') as regist_time,
                  COUNT(*) as count
                FROM tb_keyword
                GROUP BY
                  DATE_FORMAT(kw_create_date, \'%Y%m\')
 	    ';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

}