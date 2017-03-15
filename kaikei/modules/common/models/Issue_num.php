<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Issue_num extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 発行月の発行通番を取得
     *
     * @param    char : 発行年月
     * @return   int
     */
    public function issue_serial_num($issue_yymm)
    {

        $set_where["in_issue_yymm"] = $issue_yymm;

        $query = $this->db->get_where('tb_issue_num', $set_where);

        $get_data = $query->result('array');

        return $get_data;

    }

    /**
     * 発行通番新規登録
     *
     * @param    char : 発行年月
     * @param    int  : 発行番号
     * @return   int
     */
    public function insert_issue_num($issue_yymm, $serial_num)
    {

        $setdata['in_issue_yymm'] = $issue_yymm;
        $setdata['in_cnt']        = $serial_num;

        // データ追加
        $query = $this->db->insert('tb_issue_num', $setdata);
        $_last_sql = $this->db->last_query();

        // 挿入した ID 番号を取得
        $row_id = $this->db->insert_id();

        // ログ書き込み
        $set_data['lg_func']   = 'insert_issue_num';
        $set_data['lg_detail'] = 'in_seq = ' . $row_id . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

        return $row_id;
    }

    /**
     * 1レコード更新
     *
     * @param    char : 発行年月
     * @param    int  : 発行番号
     * @return   bool
     */
    public function update_issue_num($issue_yymm, $serial_num)
    {

        $setdata['in_issue_yymm'] = $issue_yymm;
        $setdata['in_cnt']        = $serial_num;

        $where = array(
                        'in_issue_yymm' => $setdata['in_issue_yymm']
        );

        $result = $this->db->update('tb_issue_num', $setdata, $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_func']      = 'update_issue_num';
        $set_data['lg_detail']    = $_last_sql;
        $this->insert_log($set_data);

        return $result;
    }

    /**
     * 発行月の発行通番を取得
     *
     * @param    char : 発行年月
     * @return   int
     */
    public function shiharai_serial_num($issue_yymm)
    {

        $set_where["in_shokai_yymm"] = $issue_yymm;

        $query = $this->db->get_where('tb_issue_num', $set_where);

        $get_data = $query->result('array');

        return $get_data;

    }

    /**
     * 発行通番新規登録
     *
     * @param    char : 発行年月
     * @param    int  : 発行番号
     * @return   int
     */
    public function insert_shiharai_num($issue_yymm, $serial_num)
    {

        $setdata['in_shokai_yymm'] = $issue_yymm;
        $setdata['in_cnt']        = $serial_num;

        // データ追加
        $query = $this->db->insert('tb_issue_num', $setdata);
        $_last_sql = $this->db->last_query();

        // 挿入した ID 番号を取得
        $row_id = $this->db->insert_id();

        // ログ書き込み
        $set_data['lg_func']   = 'insert_issue_num';
        $set_data['lg_detail'] = 'in_seq = ' . $row_id . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

        return $row_id;
    }

    /**
     * 1レコード更新
     *
     * @param    char : 発行年月
     * @param    int  : 発行番号
     * @return   bool
     */
    public function update_shiharai_num($issue_yymm, $serial_num)
    {

        $setdata['in_shokai_yymm'] = $issue_yymm;
        $setdata['in_cnt']         = $serial_num;

        $where = array(
                'in_shokai_yymm' => $setdata['in_shokai_yymm']
        );

        $result = $this->db->update('tb_issue_num', $setdata, $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_func']      = 'update_issue_num';
        $set_data['lg_detail']    = $_last_sql;
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

        $setData['lg_type'] = 'Issue_num.php';
        $setData['lg_ip']   = $this->input->ip_address();

        // データ追加
        $query = $this->db->insert('tb_log', $setData);

    }

}