<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Serps extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * SEQから登録情報を取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_sd_seq($seq_no)
    {

    	$set_where["sd_seq"] = $seq_no;

    	$query = $this->db->get_where('tb_search_data', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 順位データを取得する : URL完全一致
     *
     * @param    int
     * @return   bool
     */
    public function get_seo_rank0($result_id, $domain, $url)
    {

    	$serach_domain = preg_replace("/^https?:\/\/(www\.)?/i", "", urldecode($url));

    	$sql = 'SELECT
                  se_seq,
                  se_result_id,
                  se_position,
    			  se_url,
    			  se_title,
    			  se_getdate
    			FROM tb_serps
    			WHERE
    			     se_result_id = \'' . $result_id . '\'
    			     AND se_domain = \'' . $domain . '\'
    			     AND se_url like \'%' . $serach_domain . '\'
    			     ORDER BY se_seq ASC LIMIT 1
		';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 順位データを取得する : URL部分一致
     *
     * @param    int
     * @return   bool
     */
    public function get_seo_rank1($result_id, $domain, $url)
    {

    	$serach_domain = preg_replace("/^https?:\/\/(www\.)?/i", "", urldecode($url));

    	$sql = 'SELECT
                  se_seq,
                  se_result_id,
                  se_position,
    			  se_url,
    			  se_title,
    			  se_getdate
    			FROM tb_serps
    			WHERE
    			     se_result_id = \'' . $result_id . '\'
    			     AND se_domain = \'' . $domain . '\'
    			     AND se_url like \'%' . $serach_domain . '%\'
    			     ORDER BY se_seq ASC LIMIT 1
   		';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 順位データを取得する : ドメイン一致
     *
     * @param    int
     * @return   bool
     */
    public function get_seo_rank2($result_id, $domain)
    {

    	$sql = 'SELECT
                  se_seq,
                  se_result_id,
                  se_position,
    			  se_url,
    			  se_title,
    			  se_getdate
    			FROM tb_serps
    			WHERE
    			     se_result_id = \'' . $result_id . '\'
    			     AND se_domain = \'' . urldecode($domain) . '\'
    			     ORDER BY se_seq ASC LIMIT 1
		';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 順位データを取得する : ルートドメイン一致
     *
     * @param    int
     * @return   bool
     */
    public function get_seo_rank3($result_id, $rootdomain)
    {

    	$sql = 'SELECT
                  se_seq,
                  se_result_id,
                  se_position,
    			  se_url,
    			  se_title,
    			  se_getdate
    			FROM tb_serps
    			WHERE
    			     se_result_id = \'' . $result_id . '\'
    			     AND (se_domain = \'' . urldecode($rootdomain) . '\' OR se_domain like \'%' . urldecode($rootdomain) . '\')
    			     ORDER BY se_seq ASC LIMIT 1
   		';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 検索結果のデータを登録
     *
	 * @param    array()
     * @return   int
     */
    public function insert_seach_data($get_serach_data)
    {

    	// データ追加
    	$query = $this->db->insert('tb_serps', $get_serach_data);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']   = 'insert_seach_data';
    	$set_data['lg_detail'] = 'sd_seq = ' . $row_id . ' <= ' . $_last_sql;
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

    	$setData['lg_type'] = 'Serps.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);
    }

}