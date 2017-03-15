<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shokai extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 支払先情報SEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_sk_seq($seq_no)
    {

        $set_where["sk_seq"] = $seq_no;

        $query = $this->db->get_where('mt_shokai', $set_where);

        $get_data = $query->result('array');

        return $get_data;

    }

    /**
     * 支払(売上先会社)情報SEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_company_sk_seq($seq_no)
    {

        $set_where["skc_sk_seq"] = $seq_no;

        $query = $this->db->get_where('tb_shokai_company', $set_where);

        $get_data = $query->result('array');

        return $get_data;

    }

    /**
     * 支払先メンバーの取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_shokailist($get_post, $tmp_per_page, $tmp_offset=0)
    {

        // 各SQL項目へセット
        // WHERE
        $set_select["sk_status"]  = $get_post['sk_status'];
        $set_select["sk_company"] = $get_post['sk_company'];

        // ORDER BY
        if ($get_post['orderid'] == 'ASC')
        {
            $set_orderby["sk_seq"] = $get_post['orderid'];
        }else {
            $set_orderby["sk_seq"] = 'DESC';
        }

        // 対象クアカウントメンバーの取得
        $shokai_list = $this->_select_shokailist($set_select, $set_orderby, $tmp_per_page, $tmp_offset);

        return $shokai_list;

    }

    /**
     * 対象クライアントメンバーの取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_shokailist($set_select, $set_orderby, $tmp_per_page, $tmp_offset=0)
    {

        $sql = 'SELECT
                  sk_seq,
                  sk_status,
                  sk_company,
                  sk_person01,
                  sk_person02,
                  sk_tel01,
                  sk_tel02,
                  sk_mail,
                  sk_payment,
                  sk_salesman
                FROM mt_shokai WHERE ';

        // sk_delflg 判定
        if ($set_select["sk_status"] == 2)
        {
            $sql .= ' sk_delflg = 1 ';
        } else {
            $sql .= ' sk_delflg = 0 ';
        }

        // WHERE文 作成
        if ($set_select["sk_status"] != '')
        {
            $sql .= ' AND sk_status = ' . $set_select["sk_status"];
        }
        if ($set_select["sk_company"] != '')
        {
            $sql .= ' AND sk_company LIKE \'%' . $this->db->escape_like_str($set_select['sk_company']) . '%\'';
        }

        // ORDER BY文 作成
        $tmp_firstitem = FALSE;
        foreach ($set_orderby as $key => $val)
        {
            if (isset($val))
            {
                if ($tmp_firstitem == FALSE)
                {
                    $sql .= ' ORDER BY ' . $key . ' ' . $val;
                    $tmp_firstitem = TRUE;
                } else {
                    $sql .= ' , ' . $key . ' ' . $val;
                }
            }
        }

        // 対象全件数を取得
        $query = $this->db->query($sql);
        $shokai_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
        $shokai_list = $query->result('array');

        return array($shokai_list, $shokai_countall);
    }

    /**
     * 対象支払先情報の取得
     *
     * @param    int
     * @param    int
     * @return   array()
     */
    public function get_sk_list($skseq = FALSE)
    {

        $sql = 'SELECT
                    sk_seq,
                    sk_status,
                    sk_tax_out,
                    sk_paycal_fix,
                    sk_paycal_rate,
                    sk_payment,
                    sk_company,
                    sk_salesman,
                    sk_delflg
                FROM mt_shokai WHERE sk_delflg = 0 AND sk_status = 0'
        ;

        // 個別発行に対応
        if ($skseq != FALSE)
        {
            $sql .= ' AND sk_seq = ' . $skseq;
        }

        $sql .= ' ORDER BY sk_seq ASC';

        // クエリー実行
        $query = $this->db->query($sql);
        $shokailist = $query->result('array');

        return $shokailist;

    }


    /**** tb_shokai_company :: 売上先会社情報 ****/

    /**
     * 支払先情報SEQから売上先会社情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_skc_list($seq_no)
    {

        $set_where["skc_sk_seq"] = $seq_no;

        $query = $this->db->get_where('tb_shokai_company', $set_where);

        $get_data = $query->result('array');

        return $get_data;

    }


    /**** tb_shokai_fee :: 支払紹介料情報 ****/

    /**
     * 支払（紹介料）情報SEQから紹介料情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_skf_seq($seq_no)
    {

        $set_where["skf_seq"]   = $seq_no;

        $query = $this->db->get_where('tb_shokai_fee', $set_where);

        $get_data = $query->result('array');

        return $get_data;

    }

    /**
     * 支払先情報SEQから紹介料情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_skf_skseq($seq_no, $sales_yymm)
    {

        $set_where["skf_sk_seq"]   = $seq_no;
        $set_where["skf_pay_yymm"] = $sales_yymm;

        $query = $this->db->get_where('tb_shokai_fee', $set_where);

        $get_data = $query->result('array');

        return $get_data;

    }

    /**
     * 支払先メンバーの取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_shokaifeelist($get_post, $tmp_per_page, $tmp_offset=0)
    {

        // 各SQL項目へセット
        // WHERE
        $set_select_like["skf_pay_no"]  = trim($get_post['skf_pay_no']);
        $set_select_like["skf_sk_company"]  = trim($get_post['skf_sk_company']);

        $set_between["skf_pay_date01"] = str_replace("-", "", trim($get_post['skf_pay_date01']));
        $set_between["skf_pay_date02"] = str_replace("-", "", trim($get_post['skf_pay_date02']));

        // ORDER BY
        if ($get_post['orderid'] == 'ASC')
        {
            $set_orderby["skf_pay_yymm"] = $get_post['orderid'];
        }elseif ($get_post['orderid'] == 'DESC') {
            $set_orderby["skf_pay_yymm"] = $get_post['orderid'];
        }else {
            $set_orderby["skf_pay_yymm"] = 'DESC';
            $set_orderby["skf_seq"]     = 'DESC';
        }
        $set_orderby["skf_seq"] = 'DESC';

        // 対象クアカウントメンバーの取得
        $shokaifee_list = $this->_select_shokaifeelist($set_select_like, $set_between, $set_orderby, $tmp_per_page, $tmp_offset);

        return $shokaifee_list;

    }

    /**
     * 支払（紹介料）情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_shokaifeelist($set_select_like, $set_between, $set_orderby, $tmp_per_page, $tmp_offset=0)
    {

        $sql = 'SELECT
                  skf_seq,
                  skf_status,
                  skf_pay_yymm,
                  skf_issue_date,
                  skf_pay_date,
                  skf_pay_total,
                  skf_pay_tax,
                  skf_payment,
                  skf_pay_no,
                  skf_sk_company
                FROM tb_shokai_fee WHERE ';

        $sql .= ' skf_delflg = 0 ';

        // WHERE_LIKE文 作成
        foreach ($set_select_like as $key => $val)
        {
            if (isset($val) && $val != '')
            {
                $sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
            }
        }

        // BETWEEN文 作成
        $sql .= ' AND `skf_pay_yymm` BETWEEN \'' . $set_between["skf_pay_date01"] . '\' AND \'' . $set_between["skf_pay_date02"] . '\'';

        // ORDER BY文 作成
        $tmp_firstitem = FALSE;
        foreach ($set_orderby as $key => $val)
        {
            if (isset($val) && $val != '')
            {
                if ($tmp_firstitem == FALSE)
                {
                    $sql .= ' ORDER BY ' . $key . ' ' . $val;
                    $tmp_firstitem = TRUE;
                } else {
                    $sql .= ' , ' . $key . ' ' . $val;
                }
            }
        }

        // 対象全件数を取得
        $query = $this->db->query($sql);
        $shokaifee_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
        $shokaifee_list = $query->result('array');

        return array($shokaifee_list, $shokaifee_countall);
    }


    /**** tb_shokai_detail :: 支払紹介料詳細情報 ****/

    /**
     * 支払（紹介料）詳細情報SEQから紹介料情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_skd_skfseq($seq_no)
    {

        $set_where["skd_skf_seq"] = $seq_no;

        $query = $this->db->get_where('tb_shokai_detail', $set_where);

        $get_data = $query->result('array');

        return $get_data;

    }


    /**** tb_project_2 :: 売上情報から各案件の紹介料情報を取得する ****/

    /**
     * 売上情報から各案件の紹介料情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_pj_shokai($pj_seq, $client_no, $db_name='default')
    {

//         // 案件情報seqを取得
//         $sql = 'SELECT
//                     T1.iv_seq,
//                     T1.iv_status,
//                     T1.iv_sales_yymm,
//                     T2.ivd_iv_seq,
//                     T2.ivd_pj_seq,
//                     T2.ivd_status
//                 FROM tb_invoice AS T1
//                     LEFT JOIN tb_invoice_detail AS T2 ON T1.iv_seq = T2.ivd_iv_seq
//                 WHERE T1.iv_seq = ' . $iv_seq;

//         $sql .= ' AND T1.iv_status = 1 AND T2.ivd_status = 0';

//         // クエリー実行
//         $query = $this->db->query($sql);
//         $get_data = $query->result('array');

        // 順位チェックツールDBへ接続
        $tb_name = 'tb_project_' . $client_no;
        $sql = 'SELECT
                    pj_seq,
                    pj_status,
                    pj_paycal_fix,
                    pj_paycal_rate
                FROM ' . $tb_name . ' AS T3 WHERE pj_seq = ' . $pj_seq;

        $sql .= ' AND pj_status = 0';

        // クエリー実行
        $slave_db = $this->load->database($db_name, TRUE);
        $query = $slave_db->query($sql);
        $shokai_info = $query->result('array');

        return $shokai_info;

    }


    /**
     * 支払情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_dlcsv_query($get_post, $tmp_per_page, $tmp_offset=0)
    {

        // 各SQL項目へセット
        // WHERE
        $set_select_like["skf_pay_no"]     = $get_post['skf_pay_no'];
        $set_select_like["skf_sk_company"] = $get_post['skf_sk_company'];

        $set_between["skf_pay_date01"]     = $get_post['skf_pay_date01'];
        $set_between["skf_pay_date02"]     = $get_post['skf_pay_date02'];

        // ORDER BY
        if ($get_post['orderid'] == 'ASC')
        {
            $set_orderby["skf_pay_yymm"] = $get_post['orderid'];
        }elseif ($get_post['orderid'] == 'DESC') {
            $set_orderby["skf_pay_yymm"] = $get_post['orderid'];
        }else {
            $set_orderby["skf_pay_yymm"] = 'DESC';
        }
        $set_orderby["skf_sk_seq"] = 'DESC';

        // 対象アカウントメンバーの取得
        $dlcsv_query = $this->_select_dlcsv_query($set_select_like, $set_between, $set_orderby, $tmp_per_page, $tmp_offset);

        return $dlcsv_query;

    }

    /**
     * 支払情報のCSVダウンロード
     *
     * @param    array() : WHERE句項目
     * @param    array() : WHERE句項目
     * @param    array() : BETWEEN句項目
     * @param    array() : ORDER BY句項目
     * @param    array() : 集計方法
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_dlcsv_query($set_select_like, $set_between, $set_orderby, $tmp_per_page, $tmp_offset=0)
    {

        $sql = 'SELECT
                      skf_seq AS `SEQ`,
                      skf_pay_yymm AS `支払月度`,
                      skf_issue_date AS `発行日`,
                      skf_pay_date AS `振込日`,
                      skf_pay_total AS `振込金額`,
                      skf_pay_tax AS `振込金額消費税`,
                      skf_payment AS `支払サイト`,
                      skf_pay_no AS `支払通知書発行NO`,
                      skf_sk_company AS `支払先会社名`,
                      skf_remark AS `備考`,
                      skf_memo AS `メモ`,
                      skf_status AS `ステータス`
                FROM tb_shokai_fee WHERE skf_delflg = 0 '
        ;

        // WHERE_LIKE文 作成
        foreach ($set_select_like as $key => $val)
        {
            if (isset($val) && $val != '')
            {
                $sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
            }
        }

        // BETWEEN文 作成
        $sql .= ' AND `skf_pay_yymm` BETWEEN \'' . $set_between["skf_pay_date01"] . '\' AND \'' . $set_between["skf_pay_date02"] . '\'';

        // ORDER BY文 作成
        $tmp_firstitem = FALSE;
        foreach ($set_orderby as $key => $val)
        {
            if (isset($val) && $val != '')
            {
                if ($tmp_firstitem == FALSE)
                {
                    $sql .= ' ORDER BY ' . $key . ' ' . $val;
                    $tmp_firstitem = TRUE;
                } else {
                    $sql .= ' , ' . $key . ' ' . $val;
                }
            }
        }
        if ($tmp_firstitem == FALSE)
        {
            $sql .= ' ORDER BY sa_cm_seq DESC';                             // デフォルト
        }

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);

        return $query;

    }

    /**
     * 支払先情報の登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_shokai($setdata)
    {

        // データ追加
        $query = $this->db->insert('mt_shokai', $setdata);
        $_last_sql = $this->db->last_query();

        // 挿入した ID 番号を取得
        $row_id = $this->db->insert_id();

        return $row_id;

        // ログ書き込み
        $set_data['lg_func']      = 'insert_shokai';
        $set_data['lg_detail']    = 'sk_seq = ' . $row_id . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

    }

    /**
     * 支払(売上先会社)情報の登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_shokai_company($setdata)
    {

        // データ追加
        $query = $this->db->insert('tb_shokai_company', $setdata);
        $_last_sql = $this->db->last_query();

        // 挿入した ID 番号を取得
        $row_id = $this->db->insert_id();

        return $row_id;

        // ログ書き込み
        $set_data['lg_func']      = 'insert_shokai_company';
        $set_data['lg_detail']    = 'skc_seq = ' . $row_id . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

    }

    /**
     * 紹介料情報の登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_shokai_fee($setdata)
    {

        // データ追加
        $query = $this->db->insert('tb_shokai_fee', $setdata);
        $_last_sql = $this->db->last_query();

        // 挿入した ID 番号を取得
        $row_id = $this->db->insert_id();

        return $row_id;

        // ログ書き込み
        $set_data['lg_func']      = 'insert_shokai_fee';
        $set_data['lg_detail']    = 'skf_seq = ' . $row_id . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

    }

    /**
     * 紹介料詳細情報の登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_shokai_detail($setdata)
    {

        // データ追加
        $query = $this->db->insert('tb_shokai_detail', $setdata);
        $_last_sql = $this->db->last_query();

        // 挿入した ID 番号を取得
        $row_id = $this->db->insert_id();

        return $row_id;

        // ログ書き込み
        $set_data['lg_func']      = 'insert_shokai_detail';
        $set_data['lg_detail']    = 'skd_seq = ' . $row_id . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

    }

    /**
     * 1レコード更新
     *
     * @param    array()
     * @return   bool
     */
    public function update_shokai($setdata)
    {

        // ステータスの判定
        if ($setdata["sk_status"] == 2)
        {
            $setdata["sk_delflg"] = 1;
        } else {
            $setdata["sk_delflg"] = 0;
        }

        $where = array(
                'sk_seq' => $setdata['sk_seq']
        );

        $result = $this->db->update('mt_shokai', $setdata, $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_func']      = 'update_shokai';
        $set_data['lg_detail']    = 'sk_seq = ' . $setdata['sk_seq'] . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

        return $result;
    }

    /**
     * 1レコード更新
     *
     * @param    array()
     * @return   bool
     */
    public function update_shokai_fee($setdata)
    {

        // ステータスの判定
        if ($setdata["skf_status"] == 9)
        {
            $setdata["skf_delflg"] = 1;
        } else {
            $setdata["skf_delflg"] = 0;
        }

        $where = array(
                'skf_seq' => $setdata['skf_seq']
        );

        $result = $this->db->update('tb_shokai_fee', $setdata, $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_func']      = 'update_shokai_fee';
        $set_data['lg_detail']    = 'skf_seq = ' . $setdata['skf_seq'] . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

        return $result;
    }

    /**
     * 1レコード更新
     *
     * @param    array()
     * @return   bool
     */
    public function update_shokai_detail($setdata)
    {

        $where = array(
                    'skd_seq' => $setdata['skd_seq']
        );

        $result = $this->db->update('tb_shokai_detail', $setdata, $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_func']      = 'update_shokai_detail';
        $set_data['lg_detail']    = 'skd_seq = ' . $setdata['skd_seq'] . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

        return $result;
    }

    /**
     * 支払(売上先会社)情報のレコード削除
     *
     * @param    int
     * @return   int
     */
    public function delete_shokai_company($sk_seq)
    {

        $where = array(
                'skc_sk_seq' => $sk_seq
        );

        $result = $this->db->delete('tb_shokai_company', $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_func']   = 'delete_shokai_company';
        $set_data['lg_detail'] = $_last_sql;
        $this->insert_log($set_data);

        return $result;
    }

    /**
     * 紹介料情報のレコード削除
     *
     * @param    int
     * @return   int
     */
    public function delete_shokai_fee($skf_seq)
    {

        $where = array(
                'skf_seq' => $skf_seq
        );

        $result = $this->db->delete('tb_shokai_fee', $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_func']   = 'delete_shokai_fee';
        $set_data['lg_detail'] = $_last_sql;
        $this->insert_log($set_data);

        return $result;
    }

    /**
     * 紹介料詳細情報のレコード削除
     *
     * @param    int
     * @return   int
     */
    public function delete_shokai_detail($skf_seq)
    {

        $where = array(
                'skd_skf_seq' => $skf_seq
        );

        $result = $this->db->delete('tb_shokai_detail', $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_func']   = 'delete_shokai_detail';
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

        if (isset($_SESSION['c_memSeq'])) {
            $setData['lg_user_id']   = $_SESSION['c_memSeq'];
        } else {
            $setData['lg_user_id']   = "";
        }

        $setData['lg_type'] = 'Shokai.php';
        $setData['lg_ip']   = $this->input->ip_address();

        // データ追加
        $query = $this->db->insert('tb_log', $setData);
    }

}