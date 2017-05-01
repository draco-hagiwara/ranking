<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Location extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * tb_locationからロケーション情報を取得する
     *
     * @return   array()
     */
    public function get_location_list()
    {

    	$sql = 'SELECT
                  lo_criteria_id,
                  lo_canonical_name
                FROM tb_location'
        ;

        $sql .= ' ORDER BY lo_criteria_id ASC';

        // クエリー実行
        $query = $this->db->query($sql);

        $get_data = $query->result('array');

        return $get_data;

    }

    /**
     * tb_locationから個別ロケーション情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_location_id($location_id)
    {

    	$sql = 'SELECT
                  lo_criteria_id,
                  lo_canonical_name
                FROM tb_location WHERE lo_criteria_id = ' . $location_id
				;

		$query = $this->db->query($sql);

        $get_data = $query->result('array');

        return $get_data;

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

    	$setData['lg_type']   = 'Location.php';
    	$setData['lg_ip'] = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    	//     	// 挿入した ID 番号を取得
    	//     	$row_id = $this->db->insert_id();
    	//     	return $row_id;
    }

}