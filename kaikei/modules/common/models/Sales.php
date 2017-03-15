<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 該当する売上データの件数を取得する
     *
     * @param    int
     * @return   int
     */
    public function get_sales_cnt($iv_seq)
    {

        $set_where["sa_iv_seq"] = $iv_seq;

        $query = $this->db->get_where('tb_sales', $set_where);

        $sales_count = $query->num_rows();

        return $sales_count;

    }

    /**
     * 売上データ情報の取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_saleslist($get_post, $tmp_per_page, $tmp_offset=0)
    {

        // 各SQL項目へセット
        // WHERE
        $set_select_like["sa_slip_no"]  = trim($get_post['sa_slip_no']);
        $set_select_like["sa_keyword"]  = trim($get_post['sa_keyword']);
        $set_select_like["sa_company"]  = trim($get_post['sa_company']);
        $set_select_like["sa_salesman"] = trim($get_post['sa_salesman']);

        $set_select["sa_collect"]       = $get_post['sa_collect'];
        $set_select["sa_accounting"]    = $get_post['sa_accounting'];

        $set_between["sa_sales_date01"] = str_replace("-", "", trim($get_post['sa_sales_date01']));
        $set_between["sa_sales_date02"] = str_replace("-", "", trim($get_post['sa_sales_date02']));

        // ORDER BY
        if ($get_post['orderid'] == 'ASC')
        {
            $set_orderby["sa_sales_yymm"] = $get_post['orderid'];
        }elseif ($get_post['orderid'] == 'DESC') {
            $set_orderby["sa_sales_yymm"] = $get_post['orderid'];
        }else {
            $set_orderby["sa_sales_yymm"] = 'DESC';
            $set_orderby["sa_cm_seq"]     = 'DESC';
        }
        $set_orderby["sa_cm_seq"] = 'DESC';

        $set_displine["displine"]       = $get_post['displine'];

        // 対象クアカウントメンバーの取得
        $sales_list = $this->_select_saleslist($set_select, $set_select_like, $set_between, $set_orderby, $set_displine, $tmp_per_page, $tmp_offset);

        return $sales_list;

    }

    /**
     * 売上データ情報の取得
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
    public function _select_saleslist($set_select, $set_select_like, $set_between, $set_orderby, $set_displine, $tmp_per_page, $tmp_offset=0)
    {

        switch( $set_displine["displine"] )
        {
            case 0:     // 通常：日別表示
                $sql = 'SELECT
                              sa_seq,
                              sa_sales_date,
                              sa_sales_yymm,
                              sa_cm_seq,
                              sa_iv_seq,
                              sa_slip_no,
                              sa_total,
                              sa_company,
                              sa_collect,
                              sa_salesman,
                              sa_status
                        FROM tb_sales WHERE sa_status = 0 '
                ;

                break;
            case 1:     // 金額集計：売上日毎

                $sql = 'SELECT
                              sa_sales_date,
                              sa_sales_yymm,
                              SUM(sa_total) as sum_total
                        FROM tb_sales WHERE sa_status = 0 '
                ;

                break;
            case 2:     // 金額集計：会社毎

                $sql = 'SELECT
                              sa_seq,
                              sa_cm_seq,
                              sa_company,
                              sa_collect,
                              sa_salesman,
                              sa_status,
                              SUM(sa_total) as sum_total
                        FROM tb_sales WHERE sa_status = 0 '
                ;

                break;
            case 3:     // 金額集計：担当営業毎
                $sql = 'SELECT
                              sa_salesman,
                              SUM(sa_total) as sum_total
                        FROM tb_sales WHERE sa_status = 0 '
                ;

                break;
            default:
        }

        // WHERE文 作成
        if ($set_select["sa_collect"] != '')
        {
            $sql .= ' AND `sa_collect`    = ' . $set_select["sa_collect"];
        }
        if ($set_select["sa_accounting"] != '')
        {
            $sql .= ' AND `sa_accounting` = ' . $set_select["sa_accounting"];
        }

        // WHERE_LIKE文 作成
        foreach ($set_select_like as $key => $val)
        {
            if (isset($val) && $val != '')
            {
                $sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
            }
        }

        // BETWEEN文 作成
        $sql .= ' AND `sa_sales_yymm` BETWEEN \'' . $set_between["sa_sales_date01"] . '\' AND \'' . $set_between["sa_sales_date02"] . '\'';

        switch( $set_displine["displine"] )
        {
            case 0:     // 通常：日別表示

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

                break;

            case 1:     // 金額集計：売上日毎

                $sql .= ' GROUP BY sa_sales_yymm';

                break;
            case 2:     // 金額集計：会社毎

                $sql .= ' GROUP BY sa_cm_seq';

                break;
            case 3:     // 金額集計：担当営業毎

                $sql .= ' GROUP BY sa_salesman';

                break;
            default:
        }

        // 対象全件数を取得
        $query = $this->db->query($sql);
        $sales_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
        $sales_list = $query->result('array');

        return array($sales_list, $sales_countall);

    }

    /**
     * 売上データ情報の取得
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
        $set_select_like["sa_slip_no"]  = $get_post['sa_slip_no'];
        $set_select_like["sa_cm_seq"]   = $get_post['sa_cm_seq'];
        $set_select_like["sa_company"]  = $get_post['sa_company'];
        $set_select_like["sa_salesman"] = $get_post['sa_salesman'];

        $set_select["sa_collect"]       = $get_post['sa_collect'];

        $set_between["sa_sales_date01"] = $get_post['sa_sales_date01'];
        $set_between["sa_sales_date02"] = $get_post['sa_sales_date02'];

        // ORDER BY
        if ($get_post['orderid'] == 'ASC')
        {
            $set_orderby["sa_sales_date"] = $get_post['orderid'];
        }elseif ($get_post['orderid'] == 'DESC') {
            $set_orderby["sa_sales_date"] = $get_post['orderid'];
        }else {
            $set_orderby["sa_sales_date"] = 'DESC';
        }
        $set_orderby["sa_cm_seq"] = 'DESC';

        $set_displine["displine"]       = $get_post['displine'];

        // 対象クアカウントメンバーの取得
        $dlcsv_query = $this->_select_dlcsv_query($set_select, $set_select_like, $set_between, $set_orderby, $set_displine, $tmp_per_page, $tmp_offset);

        return $dlcsv_query;

    }

    /**
     * 売上データ情報のCSVダウンロード
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
    public function _select_dlcsv_query($set_select, $set_select_like, $set_between, $set_orderby, $set_displine, $tmp_per_page, $tmp_offset=0)
    {

        switch( $set_displine["displine"] )
        {
            case 0:     // 通常：日別表示
                $sql = 'SELECT
                              sa_seq AS `SEQ`,
                              sa_sales_date AS `売上日`,
                              sa_cm_seq AS `会社CD`,
                              sa_company AS `会社名`,
                              sa_iv_seq AS `案件CD`,
                              sa_slip_no AS `請求書発行NO`,
                              sa_total AS `売上金額`,
                              sa_collect AS `回収サイト`,
                              sa_salesman AS `担当営業`,
                              sa_status AS `ステータス`
                        FROM tb_sales WHERE sa_status = 0 '
                        ;

                        break;
            case 1:     // 金額集計：売上日毎

                $sql = 'SELECT
                              sa_sales_date AS `売上日`,
                              SUM(sa_total) as sum_total
                        FROM tb_sales WHERE sa_status = 0 '
                        ;

                        break;
            case 2:     // 金額集計：会社毎

                $sql = 'SELECT
                              sa_seq AS `SEQ`,
                              sa_cm_seq AS `会社CD`,
                              sa_company AS `会社名`,
                              sa_collect AS `回収サイト`,
                              sa_salesman AS `担当営業`,
                              SUM(sa_total) as sum_total
                        FROM tb_sales WHERE sa_status = 0 '
                        ;

                        break;
            case 3:     // 金額集計：担当営業毎
                $sql = 'SELECT
                              sa_salesman AS `担当営業`,
                              SUM(sa_total) as sum_total
                        FROM tb_sales WHERE sa_status = 0 '
                        ;

                        break;
            default:
        }

        // WHERE文 作成
        if ($set_select["sa_collect"] != '')
        {
            $sql .= ' AND `sa_collect`  = ' . $set_select["sa_collect"];
        }

        // WHERE_LIKE文 作成
        foreach ($set_select_like as $key => $val)
        {
            if (isset($val) && $val != '')
            {
                $sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
            }
        }

        // BETWEEN文 作成
        $sql .= ' AND `sa_sales_date` BETWEEN \'' . $set_between["sa_sales_date01"] . '\' AND \'' . $set_between["sa_sales_date02"] . '\'';

        switch( $set_displine["displine"] )
        {
            case 0:     // 通常：日別表示

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

                break;

            case 1:     // 金額集計：売上日毎

                $sql .= ' GROUP BY sa_sales_date';

                break;
            case 2:     // 金額集計：会社毎

                $sql .= ' GROUP BY sa_cm_seq';

                break;
            case 3:     // 金額集計：担当営業毎

                $sql .= ' GROUP BY sa_salesman';

                break;
            default:
        }

//      // 対象全件数を取得
//      $query = $this->db->query($sql);
//      $sales_countall = $query->num_rows();

        // LIMIT ＆ OFFSET 値をセット
        $sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

        // クエリー実行
        $query = $this->db->query($sql);
//      $sales_list = $query->result('array');

        return $query;

    }

    /**
     * 売上データ情報（全体）の取得
     *
     * @param    date : 売上日（開始）
     * @param    date : 売上日（終了）
     * @return   int
     */
    public function get_sales_monthly($start_date, $end_day)
    {

        $sql = 'SELECT
                    sa_seq,
                    SUM(sa_total) as sum_total
                    FROM tb_sales WHERE sa_status = 0 '
        ;

        // BETWEEN文 作成
        $sql .= ' AND `sa_sales_date` BETWEEN \'' . $start_date . '\' AND \'' . $end_day . '\'';

        // クエリー実行
        $query = $this->db->query($sql);
        $sales_list = $query->result('array');

        if ($sales_list[0]['sum_total'] == 0)
        {
            return 0;
        } else {
            return $sales_list[0]['sum_total'];
        }

    }

    /**
     * 売上データ情報（営業別）の取得
     *
     * @param    int
     * @param    date : 売上日（開始）
     * @param    date : 売上日（終了）
     * @return   int
     */
    public function get_sales_salesman($ac_seq, $start_date, $end_day)
    {

        $sql = 'SELECT
                sa_seq,
                SUM(sa_total) as sum_total
                FROM tb_sales WHERE sa_status = 0 '
                ;

        // WHERE文 作成
        $sql .= ' AND `sa_salesman_id` = ' . $ac_seq;

        // BETWEEN文 作成
        $sql .= ' AND `sa_sales_date` BETWEEN \'' . $start_date . '\' AND \'' . $end_day . '\'';

        // クエリー実行
        $query = $this->db->query($sql);
        $sales_list = $query->result('array');

        if ($sales_list[0]['sum_total'] == 0)
        {
            return 0;
        } else {
            return $sales_list[0]['sum_total'];
        }

    }

    /**
     * 売上データ情報（課金方式別）の取得
     *
     * @param    int
     * @param    date : 売上日（開始）
     * @param    date : 売上日（終了）
     * @return   int
     */
    public function get_sales_accounting($sa_accounting, $start_date, $end_day)
    {

        $sql = 'SELECT
                sa_seq,
                sa_accounting,
                SUM(sa_total) as sum_total
                FROM tb_sales WHERE sa_status = 0 '
        ;

        // WHERE文 作成
        $sql .= ' AND `sa_accounting` = ' . $sa_accounting;

        // BETWEEN文 作成
        $sql .= ' AND `sa_sales_date` BETWEEN \'' . $start_date . '\' AND \'' . $end_day . '\'';

        // クエリー実行
        $query = $this->db->query($sql);
        $sales_list = $query->result('array');

        if ($sales_list[0]['sum_total'] == 0)
        {
            return 0;
        } else {
            return $sales_list[0]['sum_total'];
        }

    }

    /**
     * 支払通知書：売上データ情報の取得
     *
     * @param    int
     * @param    char : date売上日
     * @return   int
     */
    public function get_shokailist($cm_seq, $sales_yymm)
    {

        $sql = 'SELECT
                        sa_seq,
                        sa_sales_date,
                        sa_sales_yymm,
                        sa_slip_no,
                        sa_company,
                        sa_total,
                        sa_accounting,
                        sa_collect,
                        sa_keyword,
                        sa_cm_seq,
                        sa_iv_seq,
                        sa_pj_seq,
                        sa_salesman_id,
                        sa_salesman
                FROM tb_sales WHERE sa_status = 0 '
        ;

        // WHERE文 作成
        $sql .= ' AND `sa_cm_seq` = ' . $cm_seq . ' AND `sa_sales_yymm` = ' . $sales_yymm;

        // クエリー実行
        $query = $this->db->query($sql);
        $sales_list = $query->result('array');

        return $sales_list;

    }

    /**
     * 売上データ新規登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_sales($setdata)
    {

        // データ追加
        $query = $this->db->insert('tb_sales', $setdata);
        $_last_sql = $this->db->last_query();

        // 挿入した ID 番号を取得
        $row_id = $this->db->insert_id();

        // ログ書き込み
        $set_data['lg_func']   = 'insert_sales';
        $set_data['lg_detail'] = 'sa_seq = ' . $row_id . ' <= ' . $_last_sql;
        $this->insert_log($set_data);

        return $row_id;
    }

    /**
     * 売上データのレコード削除
     *
     * @param    int
     * @return   int
     */
    public function delete_sales($iv_seq)
    {

        $where = array(
                'sa_iv_seq' => $iv_seq
        );

        $result = $this->db->delete('tb_sales', $where);
        $_last_sql = $this->db->last_query();

        // ログ書き込み
        $set_data['lg_func']   = 'delete_sales';
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

        if (isset($_SESSION['a_memSeq'])) {
            $setData['lg_user_id']   = $_SESSION['a_memSeq'];
        } elseif (isset($_SESSION['c_memSeq'])) {
            $setData['lg_user_id']   = $_SESSION['c_memSeq'];
        } else {
            $setData['lg_user_id']   = "";
        }

        $setData['lg_type'] = 'Sales.php';
        $setData['lg_ip']   = $this->input->ip_address();

        // データ追加
        $query = $this->db->insert('tb_log', $setData);

    }

}