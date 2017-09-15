<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group_tag extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * クライアントSEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_gt_clseq($cl_seq, $type)
    {


    	$set_where["gt_type"]   = $type;
    	$set_where["gt_cl_seq"] = $cl_seq;

    	$this->db->order_by('gt_seq', 'DESC');
    	$query = $this->db->get_where('tb_group_tag', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;
    }

    /**
     * name から登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_gt_name($tg_name, $cl_seq, $type)
    {


    	$set_where["gt_name"]   = $tg_name;
    	$set_where["gt_type"]   = $type;
    	$set_where["gt_cl_seq"] = $cl_seq;

    	//     	$this->db->order_by('gt_seq', 'DESC');
    	$query = $this->db->get_where('tb_group_tag', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * グループ＆タグ登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_group_tag($setData)
    {

    	// データ追加
    	$query = $this->db->insert('tb_group_tag', $setData);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_group_tag';
    	$set_data['lg_detail']    = 'tg_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * グループ＆タグ情報の更新
     *
     * @param    array()
     * @return   int
     */
    public function update_gt_cnt($setdata, $type)
    {

    	// UPDATE
    	$where = array(
    			'gt_seq'    => $setdata['gt_seq'],
    			'gt_cl_seq' => $setdata['gt_cl_seq'],
    			'gt_name'   => $setdata['gt_name'],
    	);

    	$result = $this->db->update('tb_group_tag', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	return;

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_gt_cnt';
    	$set_data['lg_detail'] = 'gt_name = ' . $setdata['gt_name'] . ' <= ' . $_last_sql;
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

    	$setData['lg_type'] = 'Group_tag.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    	//     	// 挿入した ID 番号を取得
    	//     	$row_id = $this->db->insert_id();
    	//     	return $row_id;
    }

}