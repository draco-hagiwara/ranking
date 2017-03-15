<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project_detail extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 受注案件SEQから情報を取得する
     *
     * @param    int
     * @param    int  : クライアントSEQ（接続先テーブルを切替）
     * @param    char : 接続先DB
     * @return   array()
     */
    public function get_pj_seq($seq_no, $client_no, $db_name='default')
    {

        $tb_name   = 'tb_project_detail_' . $client_no;
        $set_where["pjd_pj_seq"]    = $seq_no;

        // 接続先DBを選択 ＆ クエリー実行
        if ($db_name == 'default')
        {

            $query = $this->db->get_where($tb_name, $set_where);

        } else {

            $slave_db = $this->load->database($db_name, TRUE);                      // 順位チェックツールDBへ接続

            $query = $slave_db->order_by('pjd_seq', 'ASC')->get_where($tb_name, $set_where);

        }

        $get_data = $query->result('array');

        return $get_data;

    }

    /**
     * 案件詳細情報新規登録
     *
     * @param    array()
     * @param    int  : クライアントSEQ（接続先テーブルを切替）
     * @param    char : 接続先DB
     * @return   int
     */
    public function insert_project_detail($setdata, $client_no, $db_name='default')
    {

        $tb_name = 'tb_project_detail_' . $client_no;

        // 接続先DBを選択 ＆ クエリー実行
        if ($db_name == 'default')
        {

            $query  = $this->db->insert($tb_name, $setdata);
            $_last_sql = $this->db->last_query();
            $row_id = $this->db->insert_id();                                       // 挿入した ID 番号を取得

        } else {

            $slave_db = $this->load->database($db_name, TRUE);                      // 順位チェックツールDBへ接続

            $query = $slave_db->insert($tb_name, $setdata);
            $_last_sql = $slave_db->last_query();
            $row_id = $slave_db->insert_id();

        }

        // ログ書き込み
        $set_data['lg_func']   = 'insert_project_detail';
        $set_data['lg_detail'] = 'pjd_seq = ' . $row_id . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

        return $row_id;
    }

    /**
     * 1レコード更新
     *
     * @param    array()
     * @param    int  : クライアントSEQ（接続先テーブルを切替）
     * @param    char : 接続先DB
     * @return   bool
     */
    public function update_project_detail($setdata, $client_no, $db_name='default')
    {

        $tb_name = 'tb_project_detail_' . $client_no;

        $where = array(
                'pjd_cm_seq'   => $setdata['pjd_cm_seq'],
                'pjd_pj_seq'   => $setdata['pjd_pj_seq'],
                'pjd_order_no' => $setdata['pjd_order_no']
        );

        // 接続先DBを選択 ＆ クエリー実行
        if ($db_name == 'default')
        {

            $result = $this->db->update($tb_name, $setdata, $where);
            $_last_sql = $this->db->last_query();

        } else {

            $slave_db = $this->load->database($db_name, TRUE);                      // 順位チェックツールDBへ接続

            $result = $slave_db->update($tb_name, $setdata, $where);
            $_last_sql = $slave_db->last_query();

        }

        // ログ書き込み
        $set_data['lg_func']      = 'update_project_detail';
        $set_data['lg_detail']    = 'pjd_seq = ' . $setdata['pjd_pj_seq'] . ' <= ' . $_last_sql;
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

        $setData['lg_type'] = 'project_detail.php';
        $setData['lg_ip']   = $this->input->ip_address();

        // データ追加
        $query = $this->db->insert('tb_log', $setData);

    }

}