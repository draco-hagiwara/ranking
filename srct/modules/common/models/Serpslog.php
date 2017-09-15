<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Serpslog extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * API のログを保存
     *
     * @param    array()
     * @return   int
     */
    public function insert_serpslog($set_data)
    {

    	$CI =& get_instance();

    	// SQL作成
    	$sql = "INSERT INTO `tb_serpslog`
    				( `sl_date`, `sl_result_id`, `sl_api_url`, `sl_api_get`, `sl_api_evidence`, `sl_api_getdata`, `sl_api_status`)
    			VALUES ( :sl_date, :sl_result_id, :sl_api_url, :sl_api_getjson, :sl_api_evidence, :sl_api_getdata, :sl_api_status)"
    	;

    	try {
			// SQL実行
	    	$sth = $CI->dbh->prepare($sql);

	    	$ret =$sth->execute($set_data);
	    	if (!$ret) {
	    		log_message('error', "検索ログinsertエラーCD = " . $ret);
	    	}

	    } catch (PDOException $e) {
	    	log_message('error', "検索ログinsertエラーmessage = " . $e->getMessage());
	    }

    	/*
    	 * 使用メモリのチェック
    	 */
    	//list($max) = sscanf(ini_get('memory_limit'), '%dM');
    	//$peak = memory_get_peak_usage(true) / 1024 / 1024;
    	//$used = ((int) $max !== 0)? round((int) $peak / (int) $max * 100, 2): '--';
		//
    	//$message = sprintf("[%s] 【insert_serpslog】Memory peak usage warning: %s %% used. (max: %sM, now: %sM)\n", date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), $used, $max, $peak);
    	//log_message('info', $message);

    	unset($set_data);
    	unset($sql);

    	// 切断
     	$sth = null;

    	return;



    	/*
    	 * API のログを保存
    	 *
    	 * 2017.07.18:Query error: Got a packet bigger than 'max_allowed_packet' bytes - Invalid query: INSERT INTO `tb_serpslog`
    	 */


    	// データ追加
    	$this->db->insert('tb_serpslog', $set_data);

    	//$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	//$row_id = $this->db->insert_id();

    	unset($set_data);

    	//return $row_id;

    }

    /**
     * API のログ情報削除 (31日保持)
     *
     * @param    array()
     * @return   int
     */
    public function delete_serpslog()
    {

    	$date = new DateTime();
    	//$del_date = $date->modify('-31 days')->format('Y-m-d');
    	$del_date = $date->modify('-7 days')->format('Y-m-d');

    	$sql = 'DELETE FROM `tb_serpslog`
    				WHERE `sl_date` < "' . $del_date . '"'
    	;

    	// クエリー実行
    	$result = $this->db->query($sql);

    	return $result;

    }

}