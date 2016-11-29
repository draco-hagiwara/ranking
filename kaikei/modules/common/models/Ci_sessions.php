<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ci_sessions extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * セッションデータの削除
     *
     * @param    timestamp
     * @return   int
     */
    public function destroy_session($del_time)
    {

    	// 対象データの削除処理
    	$sql = 'DELETE FROM ci_sessions WHERE timestamp <= ?';

    	$values = array(
    			$del_time,
    	);

    	$result = $this->db->query($sql, $values);

    	// ログ書き込み
    	$set_data['lg_func']      = 'destroy_session';
    	$set_data['lg_detail']    = '';
    	$this->insert_log($set_data);

        return;
    }

    /**
     * ログ書き込み
     *
     * @param    array()
     * @return   int
     */
    public function insert_log($setData)
    {

    	$setData['lg_user_id'] = "";
    	$setData['lg_type']    = 'Ci_sessions.php';
    	$setData['lg_ip'] = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    	//     	// 挿入した ID 番号を取得
    	//     	$row_id = $this->db->insert_id();
    	//     	return $row_id;
    }

}