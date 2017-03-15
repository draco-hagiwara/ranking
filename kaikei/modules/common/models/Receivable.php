<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Receivable extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 顧客情報SEQから債権データを取得
     *
     * @param    int
     * @return   bool
     */
    public function get_rv_cm_seq($seq_no)
    {

        $set_where = '`rv_cm_seq` = ' . $seq_no . ' AND `rv_status` = 0';

        $query = $this->db->get_where('tb_receivable', $set_where);

        $get_data = $query->result('array');

        return $get_data;

    }

    /**
     * 債権データ情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_receivablelist($get_post, $tmp_per_page, $tmp_offset=0)
    {

        // 各SQL項目へセット
        // WHERE
        $set_select_like["rv_cm_seq"]    = $get_post['rv_cm_seq'];
        $set_select_like["rv_company"]   = $get_post['rv_company'];
        $set_select_like["rv_salesman"]  = $get_post['rv_salesman'];

        $set_between["rv_total01"]       = $get_post['rv_total01'];
        $set_between["rv_total02"]       = $get_post['rv_total02'];
        $set_between["rv_create_date01"] = $get_post['rv_create_date01'];
        $set_between["rv_create_date02"] = $get_post['rv_create_date02'];

        // ORDER BY
        if ($get_post['orderid'] == 'ASC')
        {
            $set_orderby["rv_seq"] = $get_post['orderid'];
        }elseif ($get_post['orderid'] == 'DESC') {
            $set_orderby["rv_seq"] = $get_post['orderid'];
        }else {
            $set_orderby["rv_seq"] = 'DESC';
        }

        $set_displine["displine"]       = $get_post['displine'];

        // 対象クアカウントメンバーの取得
        $sales_list = $this->_select_receivablelist($set_select_like, $set_between, $set_orderby, $set_displine, $tmp_per_page, $tmp_offset);

        return $sales_list;

    }

    /**
     * 債権データ情報の取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : BETWEEN句項目
     * @param    array() : ORDER BY句項目
     * @param    array() : 集計方法
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_receivablelist($set_select_like, $set_between, $set_orderby, $set_displine, $tmp_per_page, $tmp_offset=0)
    {

        switch( $set_displine["displine"] )
        {
            case 0:     // 通常：債権 表示
                $sql = 'SELECT
                              rv_seq,
                              rv_tax,
                              rv_total,
                              rv_cm_seq,
                              rv_company,
                              rv_salesman_id,
                              rv_salesman,
                              rv_memo,
                              rv_create_date,
                              rv_status
                        FROM tb_receivable WHERE rv_status = 0 '
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
                        $sql .= ' AND `rv_create_date` BETWEEN \'' . $set_between["rv_create_date01"] . '\' AND \'' . $set_between["rv_create_date02"] . '\'';

                        if (($set_between["rv_total01"] != '') && ($set_between["rv_total02"] != ''))
                        {
                            $sql .= ' AND `rv_total` BETWEEN ' . $set_between["rv_total01"] . ' AND ' . $set_between["rv_total02"];
                        } elseif ($set_between["rv_total01"] != '') {
                            $sql .= ' AND `rv_total` BETWEEN ' . $set_between["rv_total01"] . ' AND 9999999999';
                        } elseif ($set_between["rv_total02"] != '') {
                            $sql .= ' AND `rv_total` BETWEEN -9999999999 AND ' . $set_between["rv_total02"];
                        }

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
                            $sql .= ' ORDER BY rv_seq DESC';                                // デフォルト
                        }

                        break;
            case 1:     // 債権履歴 表示

                $sql = 'SELECT
                              rv_seq,
                              rv_sales_date,
                              rv_tax,
                              rv_total,
                              rv_receive_total,
                              rv_cm_seq,
                              rv_company,
                              rv_bank_info,
                              rv_collect,
                              rv_salesman_id,
                              rv_salesman,
                              rv_memo,
                              rv_slip_no,
                              rv_create_date,
                              rv_status
                        FROM tb_receivable_h WHERE rv_status = 0 '
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
                        $sql .= ' AND `rv_create_date` BETWEEN \'' . $set_between["rv_create_date01"] . '\' AND \'' . $set_between["rv_create_date02"] . '\'';

                        if (($set_between["rv_total01"] != '') && ($set_between["rv_total02"] != ''))
                        {
                            $sql .= ' AND `rv_total` BETWEEN ' . $set_between["rv_total01"] . ' AND ' . $set_between["rv_total02"];
                        } elseif ($set_between["rv_total01"] != '') {
                            $sql .= ' AND `rv_total` BETWEEN ' . $set_between["rv_total01"] . ' AND 9999999999';
                        } elseif ($set_between["rv_total02"] != '') {
                            $sql .= ' AND `rv_total` BETWEEN -9999999999 AND ' . $set_between["rv_total02"];
                        }

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
                            $sql .= ' ORDER BY rv_seq DESC';                                // デフォルト
                        }

                        break;
            case 2:     // 入金情報 表示

                        $sql = 'SELECT
                              rv_seq,
                              rv_sales_date,
                              rv_tax,
                              rv_total,
                              rv_receive_total,
                              rv_cm_seq,
                              rv_company,
                              rv_bank_info,
                              rv_collect,
                              rv_salesman_id,
                              rv_salesman,
                              rv_memo,
                              rv_slip_no,
                              rv_create_date,
                              rv_status
                        FROM tb_receivable_h WHERE rv_receive_total > 0 '
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
                        $sql .= ' AND `rv_create_date` BETWEEN \'' . $set_between["rv_create_date01"] . '\' AND \'' . $set_between["rv_create_date02"] . '\'';

                        if (($set_between["rv_total01"] != '') && ($set_between["rv_total02"] != ''))
                        {
                            $sql .= ' AND `rv_receive_total` BETWEEN ' . $set_between["rv_total01"] . ' AND ' . $set_between["rv_total02"];
                        } elseif ($set_between["rv_total01"] != '') {
                            $sql .= ' AND `rv_receive_total` BETWEEN ' . $set_between["rv_total01"] . ' AND 9999999999';
                        } elseif ($set_between["rv_total02"] != '') {
                            $sql .= ' AND `rv_receive_total` BETWEEN -9999999999 AND ' . $set_between["rv_total02"];
                        }

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
                            $sql .= ' ORDER BY rv_seq DESC';                                // デフォルト
                        }

                        break;
            default:
        }

        // 対象全件数を取得
        $query = $this->db->query($sql);
        $receivable_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
        $receivable_list = $query->result('array');

        return array($receivable_list, $receivable_countall);

    }

    /**
     * 債権データ情報のCSVダウンロード
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
        $set_select_like["rv_cm_seq"]    = $get_post['rv_cm_seq'];
        $set_select_like["rv_company"]   = $get_post['rv_company'];
        $set_select_like["rv_salesman"]  = $get_post['rv_salesman'];

        $set_between["rv_total01"]       = $get_post['rv_total01'];
        $set_between["rv_total02"]       = $get_post['rv_total02'];
        $set_between["rv_create_date01"] = $get_post['rv_create_date01'];
        $set_between["rv_create_date02"] = $get_post['rv_create_date02'];

        // ORDER BY
        if ($get_post['orderid'] == 'ASC')
        {
            $set_orderby["rv_seq"] = $get_post['orderid'];
        }elseif ($get_post['orderid'] == 'DESC') {
            $set_orderby["rv_seq"] = $get_post['orderid'];
        }else {
            $set_orderby["rv_seq"] = 'DESC';
        }

        $set_displine["displine"]       = $get_post['displine'];

        // 対象クアカウントメンバーの取得
        $dlcsv_query = $this->_select_dlcsv_query($set_select_like, $set_between, $set_orderby, $set_displine, $tmp_per_page, $tmp_offset);

        return $dlcsv_query;

    }

    /**
     * 債権データ情報のCSVダウンロード
     *
     * @param    array() : WHERE句項目
     * @param    array() : BETWEEN句項目
     * @param    array() : ORDER BY句項目
     * @param    array() : 集計方法
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_dlcsv_query($set_select_like, $set_between, $set_orderby, $set_displine, $tmp_per_page, $tmp_offset=0)
    {

        switch( $set_displine["displine"] )
        {
            case 0:     // 通常：債権 表示
                $sql = 'SELECT
                              rv_seq AS `SEQ`,
                              rv_tax AS `消費税額`,
                              rv_total AS `債権金額`,
                              rv_cm_seq AS `会社CD`,
                              rv_company AS `会社名`,
                              rv_salesman AS `担当営業`,
                              rv_memo AS `メモ`,
                              rv_create_date AS `更新日`,
                              rv_status AS `ステータス`
                        FROM tb_receivable WHERE rv_status = 0 '
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
                        $sql .= ' AND `rv_create_date` BETWEEN \'' . $set_between["rv_create_date01"] . '\' AND \'' . $set_between["rv_create_date02"] . '\'';

                        if (($set_between["rv_total01"] != '') && ($set_between["rv_total02"] != ''))
                        {
                            $sql .= ' AND `rv_total` BETWEEN ' . $set_between["rv_total01"] . ' AND ' . $set_between["rv_total02"];
                        } elseif ($set_between["rv_total01"] != '') {
                            $sql .= ' AND `rv_total` BETWEEN ' . $set_between["rv_total01"] . ' AND 9999999999';
                        } elseif ($set_between["rv_total02"] != '') {
                            $sql .= ' AND `rv_total` BETWEEN -9999999999 AND ' . $set_between["rv_total02"];
                        }

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
                            $sql .= ' ORDER BY rv_seq DESC';                                // デフォルト
                        }

                        break;
            case 1:     // 債権履歴 表示

                $sql = 'SELECT
                              rv_seq AS `SEQ`,
                              rv_sales_date AS `入金日`,
                              rv_receive_total AS `入金金額`,
                              rv_cm_seq AS `会社CD`,
                              rv_company AS `会社名`,
                              rv_bank_info AS `口座情報`,
                              rv_create_date AS `更新日`,
                              rv_status AS `ステータス`
                        FROM tb_receivable_h WHERE rv_status = 0 '
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
                        $sql .= ' AND `rv_create_date` BETWEEN \'' . $set_between["rv_create_date01"] . '\' AND \'' . $set_between["rv_create_date02"] . '\'';

                        if (($set_between["rv_total01"] != '') && ($set_between["rv_total02"] != ''))
                        {
                            $sql .= ' AND `rv_total` BETWEEN ' . $set_between["rv_total01"] . ' AND ' . $set_between["rv_total02"];
                        } elseif ($set_between["rv_total01"] != '') {
                            $sql .= ' AND `rv_total` BETWEEN ' . $set_between["rv_total01"] . ' AND 9999999999';
                        } elseif ($set_between["rv_total02"] != '') {
                            $sql .= ' AND `rv_total` BETWEEN -9999999999 AND ' . $set_between["rv_total02"];
                        }

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
                            $sql .= ' ORDER BY rv_seq DESC';                                // デフォルト
                        }

                        break;
            case 2:     // 入金情報 表示

                $sql = 'SELECT
                              rv_seq AS `SEQ`,
                              rv_sales_date AS `売上日付`,
                              rv_tax AS `消費税額`,
                              rv_total AS `債権金額`,
                              rv_receive_total AS `入金金額`,
                              rv_cm_seq AS `会社CD`,
                              rv_company AS `会社名`,
                              rv_bank_info AS `口座情報`,
                              rv_collect AS `回収サイクル`,
                              rv_salesman AS `担当営業`,
                              rv_memo AS `メモ`,
                              rv_slip_no AS `請求書番号`,
                              rv_create_date AS `更新日`,
                              rv_status AS `ステータス`
                        FROM tb_receivable_h WHERE rv_receive_total > 0 '
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
                        $sql .= ' AND `rv_create_date` BETWEEN \'' . $set_between["rv_create_date01"] . '\' AND \'' . $set_between["rv_create_date02"] . '\'';

                        if (($set_between["rv_total01"] != '') && ($set_between["rv_total02"] != ''))
                        {
                            $sql .= ' AND `rv_receive_total` BETWEEN ' . $set_between["rv_total01"] . ' AND ' . $set_between["rv_total02"];
                        } elseif ($set_between["rv_total01"] != '') {
                            $sql .= ' AND `rv_receive_total` BETWEEN ' . $set_between["rv_total01"] . ' AND 9999999999';
                        } elseif ($set_between["rv_total02"] != '') {
                            $sql .= ' AND `rv_receive_total` BETWEEN -9999999999 AND ' . $set_between["rv_total02"];
                        }

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
                            $sql .= ' ORDER BY rv_seq DESC';                                // デフォルト
                        }

                        break;
            default:
        }

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);

        return $query;

    }

    /**
     * 債権データ 新規登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_receivable($setdata)
    {

        // データ追加
        $query = $this->db->insert('tb_receivable', $setdata);
        $_last_sql = $this->db->last_query();

        // 挿入した ID 番号を取得
        $row_id = $this->db->insert_id();

        // ログ書き込み
        $set_data['lg_func']   = 'insert_receivable';
        $set_data['lg_detail'] = 'rv_seq = ' . $row_id . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

        return $row_id;
    }

    /**
     * 債権履歴データ 新規登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_receivable_history($setdata)
    {

        // データ追加
        $query = $this->db->insert('tb_receivable_h', $setdata);

        // 挿入した ID 番号を取得
        $row_id = $this->db->insert_id();

        return $row_id;
    }

    /**
     * 1レコード更新
     *
     * @param    array()
     * @return   bool
     */
    public function update_receivable($setdata)
    {

        $where = array(
                'rv_seq' => $setdata['rv_seq']
        );

        $result = $this->db->update('tb_receivable', $setdata, $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_func']   = 'update_receivable';
        $set_data['lg_detail'] = 'rv_seq = ' . $setdata['rv_seq'] . ' <= ' . $_last_sql;
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

        $setData['lg_type'] = 'Receivable.php';
        $setData['lg_ip']   = $this->input->ip_address();

        // データ追加
        $query = $this->db->insert('tb_log', $setData);

    }

}