<?php

class Invoicelist extends MY_Controller
{

    /*
     *  請求書一覧
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('mess', FALSE);

    }

    // 請求書一覧TOP
    public function index()
    {

        // セッションデータをクリア
        $this->load->library('lib_auth');
        $this->lib_auth->delete_session('client');

        // バリデーション・チェック
        $this->_set_validation();                                               // バリデーション設定
        $this->form_validation->run();

        // 1ページ当たりの表示件数
        $this->config->load('config_comm');
        $tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

        // Pagination 現在ページ数の取得：：URIセグメントの取得
        $segments = $this->uri->segment_array();
        if (isset($segments[3]))
        {
            $tmp_offset = $segments[3];
            $tmp_inputpost = $this->input->post();
        } else {
            $tmp_offset = 0;

            // 発行年月 <- 初期値
            $date = new DateTime();
            $_date_ym = $date->format('Ym');

            $tmp_inputpost = array(
                                'iv_slip_no'    => '',
                                'iv_cm_seq'     => '',
                                'iv_company'    => '',
                                'iv_status'     => '',
                                'iv_issue_yymm' => $_date_ym,
                                'orderid'       => '',
            );

            // セッションをフラッシュデータとして保存
            $data = array(
                            'c_iv_slip_no'     => "",
                            'c_iv_cm_seq'      => "",
                            'c_iv_company'     => "",
                            'c_iv_status'      => "",
                            'c_iv_issue_yymm'  => $_date_ym,
                            'c_orderid'        => "",
            );
            $this->session->set_userdata($data);
        }

        // 請求書情報の取得
        $this->load->model('Invoice', 'iv', TRUE);
        list($invoice_list, $invoice_countall) = $this->iv->get_invoicelist($tmp_inputpost, $tmp_per_page, $tmp_offset);

        $this->smarty->assign('list', $invoice_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($invoice_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall', $invoice_countall);

        $this->smarty->assign('seach_iv_slip_no',    $tmp_inputpost['iv_slip_no']);
        $this->smarty->assign('seach_iv_cm_seq',     $tmp_inputpost['iv_cm_seq']);
        $this->smarty->assign('seach_iv_company',    $tmp_inputpost['iv_company']);
        $this->smarty->assign('seach_iv_status',     $tmp_inputpost['iv_status']);
        $this->smarty->assign('seach_iv_issue_yymm', $tmp_inputpost['iv_issue_yymm']);
        $this->smarty->assign('seach_orderid',       $tmp_inputpost['orderid']);

        $this->view('invoicelist/index.tpl');

    }

    // 一覧表示
    public function search()
    {

        if ($this->input->post('submit') == '_submit')
        {
            // セッションをフラッシュデータとして保存
            $data = array(
                            'c_iv_slip_no'    => $this->input->post('iv_slip_no'),
                            'c_iv_cm_seq'     => $this->input->post('iv_cm_seq'),
                            'c_iv_company'    => $this->input->post('iv_company'),
                            'c_iv_status'     => $this->input->post('iv_status'),
                            'c_iv_issue_yymm' => $this->input->post('iv_issue_yymm'),
                            'c_orderid'       => $this->input->post('orderid'),
            );
            $this->session->set_userdata($data);

            $tmp_inputpost = $this->input->post();
            unset($tmp_inputpost["submit"]);

        } else {
            // セッションからフラッシュデータ読み込み
            $tmp_inputpost['iv_slip_no']    = $_SESSION['c_iv_slip_no'];
            $tmp_inputpost['iv_cm_seq']     = $_SESSION['c_iv_cm_seq'];
            $tmp_inputpost['iv_company']    = $_SESSION['c_iv_company'];
            $tmp_inputpost['iv_status']     = $_SESSION['c_iv_status'];
            $tmp_inputpost['iv_issue_yymm'] = $_SESSION['c_iv_issue_yymm'];
            $tmp_inputpost['orderid']       = $_SESSION['c_orderid'];
        }

        // バリデーション・チェック
        $this->_set_validation();                                               // バリデーション設定
        $this->form_validation->run();

        // Pagination 現在ページ数の取得：：URIセグメントの取得
        $segments = $this->uri->segment_array();
        if (isset($segments[3]))
        {
            $tmp_offset = $segments[3];
        } else {
            $tmp_offset = 0;
        }

        // 1ページ当たりの表示件数
        $this->config->load('config_comm');
        $tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

        // アカウントメンバーの取得
        $this->load->model('Invoice', 'iv', TRUE);
        list($invoice_list, $invoice_countall) = $this->iv->get_invoicelist($tmp_inputpost, $tmp_per_page, $tmp_offset);

        $this->smarty->assign('list', $invoice_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($invoice_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall', $invoice_countall);

        $this->smarty->assign('seach_iv_slip_no',    $tmp_inputpost['iv_slip_no']);
        $this->smarty->assign('seach_iv_cm_seq',     $tmp_inputpost['iv_cm_seq']);
        $this->smarty->assign('seach_iv_company',    $tmp_inputpost['iv_company']);
        $this->smarty->assign('seach_iv_status',     $tmp_inputpost['iv_status']);
        $this->smarty->assign('seach_iv_issue_yymm', $tmp_inputpost['iv_issue_yymm']);
        $this->smarty->assign('seach_orderid',       $tmp_inputpost['orderid']);

        $this->view('invoicelist/index.tpl');

    }

    // 請求書情報編集
    public function detail()
    {

        // 更新対象データの取得
        $input_post = $this->input->post();

        $this->load->model('Invoice',        'iv',  TRUE);
        $this->load->model('Invoice_detail', 'ivd', TRUE);

        $get_iv_data = $this->iv->get_iv_seq($input_post['chg_seq']);
        $this->smarty->assign('info', $get_iv_data[0]);

        // 明細データの取得
        $get_ivd_data = $this->ivd->get_iv_seq($input_post['chg_seq'], $get_iv_data[0]['iv_issue_yymm'], $get_iv_data[0]['iv_seq_suffix']);
        $this->smarty->assign('infodetail', $get_ivd_data);

        // バリデーション設定
        $this->_set_validation02();

        // 初期値セット
        $this->_item_set();

        $this->view('invoicelist/detail.tpl');

    }

    // 請求書情報チェック
    public function detailchk()
    {

        $input_post = $this->input->post();

        $this->load->model('Invoice',        'iv',  TRUE);
        $this->load->model('Invoice_detail', 'ivd', TRUE);
        $this->load->library('lib_invoice');
        $this->config->load('config_comm');

        // 請求書データの取得
        $get_iv_data = $this->iv->get_iv_seq($input_post['iv_seq']);

        // 明細データの取得
        $get_ivd_data = $this->ivd->get_iv_seq($input_post['iv_seq'], $get_iv_data[0]['iv_issue_yymm'], $get_iv_data[0]['iv_seq_suffix']);

        // バリデーション・チェック
        $this->_set_validation02();
        if ($this->form_validation->run() == TRUE)
        {

            // トランザクション・START
            $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
            $this->db->trans_start();                                               // trans_begin

            $_suffix = $get_iv_data[0]['iv_seq_suffix'] + 1;
            $get_iv_data[0]['iv_seq']        = $input_post['iv_seq'];
            $get_iv_data[0]['iv_seq_suffix'] = $_suffix;                                    // 枝番

            // ■売上データの更新有無を判定：「未発行」
            if ($input_post['iv_status'] == 0)
            {
                switch( $get_iv_data[0]['iv_status'] ){
                    case 0:     // 「未発行」→「未発行」
                        $get_iv_data[0]['iv_sales_date']  = NULL;
                        break;
                    case 1:     // 「発行済み」→「未発行」

                        // 売上データ削除判定
                        $res_del = $this->_delete_sales($input_post['iv_seq'], $get_iv_data[0]['iv_sales_date']);

                        $get_iv_data[0]['iv_sales_date']  = NULL;                           // 売上日

                        break;
                    default:

                }
            }

            // ■売上データの更新有無を判定：「発行済み」
            if ($input_post['iv_status'] == 1)
            {
                switch( $get_iv_data[0]['iv_status'] ){
                    case 0:     // 「未発行」→「発行済み」
                        $get_iv_data[0]['iv_reissue'] = $get_iv_data[0]['iv_reissue'] + 1;

                        $get_iv_data[0]['iv_sales_date'] = $get_iv_data[0]['iv_issue_date'];        // 売上指定日 = 発行日
//                      $date = new DateTime();
//                      $get_iv_data[0]['iv_sales_date']  = $date->format('Y-m-d');

                        break;
                    case 1:     // 「発行済み」→「発行済み」
                        $get_iv_data[0]['iv_reissue'] = $get_iv_data[0]['iv_reissue'] + 1;

                        break;
                    default:

                }
            }

            // ■売上データの更新有無を判定：「キャンセル」
            if ($input_post['iv_status'] == 9)
            {
                switch( $get_iv_data[0]['iv_status'] ){
                    case 0:     // 「未発行」→「キャンセル」
                        $get_iv_data[0]['iv_sales_date']  = NULL;
                        $get_iv_data[0]['iv_delflg']  = 1;

                        break;
                    case 1:     // 「発行済み」→「キャンセル」

                        // 売上データ削除判定
                        $this->_delete_sales($input_post['iv_seq'], $get_iv_data[0]['iv_sales_date']);

                        $get_iv_data[0]['iv_sales_date']  = NULL;
                        $get_iv_data[0]['iv_delflg']  = 1;

                        break;
                    default:

                }
            }
            $get_iv_data[0]['iv_status']  = $input_post['iv_status'];

            // 請求書発行番号 :: 【LA101-KT-BX001-1611】
            $tmp_sales_info = explode("-", $get_iv_data[0]['iv_slip_no']);
            $_sales_info = $tmp_sales_info[1];
//          $_sales_info = $get_iv_data[0]['iv_accounting'];

            if ($_sales_info == 8)
            {
                $_invo_info = "Y";
            } elseif ($_sales_info == 9) {
                $_invo_info = "Z";
            } else {
                $_invo_info = "X";
            }

            // 発行通番と一括/個別は現行のものを使用
            $_invo_serial_num = substr($get_iv_data[0]['iv_slip_no'], -8,  3);
            $_invo_class      = substr($get_iv_data[0]['iv_slip_no'], -10, 1);
            //$_invo_num = explode("-", $get_iv_data[0]['iv_slip_no']);
            //$_invo_num = substr($_invo_num[2], 2, 4);

            $set_data_iv['iv_slip_no']    = $this->lib_invoice->issue_num(  $get_iv_data[0]['iv_cm_seq'],
                                                                            $get_iv_data[0]['iv_issue_yymm'],
                                                                            $_sales_info,
                                                                            $_invo_serial_num,
                                                                            $_invo_info,
                                                                            $get_iv_data[0]['iv_reissue'],
                                                                            $_invo_class
                                            );


            // 明細データ更新
            $get_iv_data[0]['iv_subtotal'] = 0;
            foreach($get_ivd_data as $key => $val)
            {

                // 金額の再計算
                $_tmp_item_seq = 'seq' . ($key + 1);
                $_tmp_item_qty = 'qty' . ($key + 1);
                if ($val['ivd_seq'] == $input_post[$_tmp_item_seq])
                {
                    $get_ivd_total    = $this->lib_invoice->issue_ivd_total($val, $input_post, $key);
                    $val['ivd_qty']   = $input_post[$_tmp_item_qty];
                    $val['ivd_total'] = $get_ivd_total;

                    // 小計の再計算
                    $get_iv_data[0]['iv_subtotal']   = $get_iv_data[0]['iv_subtotal'] + $get_ivd_total;
                }

                // tb_invoice_detail 更新
                $val['ivd_seq_suffix'] = $_suffix;

                // ステータス
                if ($input_post['iv_status'] == 0)
                {
                    $val['ivd_status']  = 0;
                } elseif ($input_post['iv_status'] == 1) {
                    $val['ivd_status']  = 0;
                } elseif ($input_post['iv_status'] == 9) {
                    $val['ivd_status']  = 1;
                }

                // 不要パラメータ削除
                unset($val["ivd_create_date"]) ;
                unset($val["ivd_update_date"]) ;

                $this->ivd->update_invoice_detail($val);

                // tb_invoice_detail_h 作成
                $this->ivd->insert_invoice_detail_history($val);

            }

            // 請求書データの更新
            $get_iv_data[0]['iv_issue_date'] = $input_post['iv_issue_date'];                // 発効日指定
            $get_iv_data[0]['iv_pay_date']   = $input_post['iv_pay_date'];                  // 振込期日指定
            $get_iv_data[0]['iv_remark']     = $input_post['iv_remark'];                    // 備考
            $get_iv_data[0]['iv_memo']       = $input_post['iv_memo'];                      // メモ

            if ($input_post['ivd_total0'] != 0)                                             // 小計
            {
                $get_iv_data[0]['iv_subtotal']   = $get_iv_data[0]['iv_subtotal'] + $input_post['ivd_total0'];
            }
            if ($input_post['ivd_total1'] != 0)
            {
                $get_iv_data[0]['iv_subtotal']   = $get_iv_data[0]['iv_subtotal'] + $input_post['ivd_total1'];
            }

            // 消費税計算
            $_issue_tax['zeiritsu'] = $this->config->item('INVOICE_TAX');
            $_issue_tax['zeinuki']  = $this->config->item('INVOICE_TAXOUT');
            $_issue_tax['hasuu']    = $this->config->item('INVOICE_TAX_CAL');

            /*
             * 例外対応：「Themis Pte. Limited:181」
             *   もし今後増えるようなら、消費税フラグを追加する！
             */
            if ($get_iv_data[0]['iv_cm_seq'] != 181)
            {
                $get_iv_data[0]['iv_tax'] = $this->lib_invoice->cal_tax($get_iv_data[0]['iv_subtotal'], $_issue_tax);
            } else {
                $get_iv_data[0]['iv_tax'] = 0;
            }

            // 合計金額計算
            $get_iv_data[0]['iv_total'] = $get_iv_data[0]['iv_subtotal'] + $get_iv_data[0]['iv_tax'];

            // 不要パラメータ削除
            unset($get_iv_data[0]["iv_create_date"]) ;
            unset($get_iv_data[0]["iv_update_date"]) ;

            // tb_invoice 更新
            $this->iv->update_invoice($get_iv_data[0]);

            // tb_invoice_h 作成
            $this->iv->insert_invoice_history($get_iv_data[0]);

            // 明細データ新規レコードの追加
            if ($input_post['ivd_total0'] != 0)
            {

                $set_data_ivd = array();
                $set_data_ivd['ivd_seq_suffix']    = $_suffix;
                $set_data_ivd['ivd_iv_seq']        = $get_iv_data[0]['iv_seq'];
                $set_data_ivd['ivd_pj_seq']        = 0;                                     // 案件SEQ=「0」
                $set_data_ivd['ivd_iv_issue_yymm'] = $get_iv_data[0]['iv_issue_yymm'];
                $set_data_ivd['ivd_iv_accounting'] = 12;                                    // その他
                $set_data_ivd['ivd_item']          = $input_post['ivd_item0'];
                $set_data_ivd['ivd_qty']           = $input_post['ivd_qty0'];
                $set_data_ivd['ivd_price']         = $input_post['ivd_price0'];
                $set_data_ivd['ivd_total']         = $input_post['ivd_total0'];
                $set_data_ivd['ivd_item_url']      = $input_post['ivd_item_url0'];

                $row_id = $this->ivd->insert_invoice_detail($set_data_ivd);

                $set_data_ivd['ivd_seq']           = $row_id;
                $this->ivd->insert_invoice_detail_history($set_data_ivd);
            }

            if ($input_post['ivd_total1'] != 0)
            {

                $set_data_ivd = array();
                $set_data_ivd['ivd_seq_suffix']    = $_suffix;
                $set_data_ivd['ivd_iv_seq']        = $get_iv_data[0]['iv_seq'];
                $set_data_ivd['ivd_pj_seq']        = 0;                                     // 案件SEQ=「0」
                $set_data_ivd['ivd_iv_issue_yymm'] = $get_iv_data[0]['iv_issue_yymm'];
                $set_data_ivd['ivd_iv_accounting'] = 12;                                    // その他
                $set_data_ivd['ivd_item']          = $input_post['ivd_item1'];
                $set_data_ivd['ivd_qty']           = $input_post['ivd_qty1'];
                $set_data_ivd['ivd_price']         = $input_post['ivd_price1'];
                $set_data_ivd['ivd_total']         = $input_post['ivd_total1'];
                $set_data_ivd['ivd_item_url']      = $input_post['ivd_item_url1'];

                $row_id = $this->ivd->insert_invoice_detail($set_data_ivd);

                $set_data_ivd['ivd_seq']           = $row_id;
                $this->ivd->insert_invoice_detail_history($set_data_ivd);

            }

            // トランザクション・COMMIT
            $this->db->trans_complete();                                            // trans_rollback & trans_commit
            if ($this->db->trans_status() === FALSE)
            {
                log_message('error', 'CLIENT::[Invoicelist -> detailchk()]：請求書 更新処理 トランザクションエラー');
            } else {
                $this->smarty->assign('mess',  "<font color=blue>更新が完了しました。</font>");
            }
        } else {
            $this->smarty->assign('mess',  "<font color=red>項目に入力エラーが発生しました。</font>");
        }

        // 初期値セット
        $this->_item_set();

        $get_iv_data = $this->iv->get_iv_seq($input_post['iv_seq']);
        $this->smarty->assign('info', $get_iv_data[0]);

        // 明細データの取得
        $get_ivd_data = $this->ivd->get_iv_seq($input_post['iv_seq'], $get_iv_data[0]['iv_issue_yymm'], $get_iv_data[0]['iv_seq_suffix']);
        $this->smarty->assign('infodetail', $get_ivd_data);

        $this->smarty->assign('info', $get_iv_data[0]);
        $this->smarty->assign('infodetail', $get_ivd_data);

        $this->view('invoicelist/detail.tpl');

    }

    // 履歴表示
    public function historychk()
    {

        // 更新対象データの取得
        $input_post = $this->input->post();

        if (isset($input_post['chg_seq']))
        {

            $_chg_seq = $input_post['chg_seq'];

            // セッションをフラッシュデータとして保存
            $data = array(
                            'c_chg_seq'    => $this->input->post('chg_seq'),
                    );
            $this->session->set_userdata($data);

        } else {
            // セッションからフラッシュデータ読み込み
            $_chg_seq    = $_SESSION['c_chg_seq'];
        }

        // バリデーション・チェック
        $this->_set_validation();                                               // バリデーション設定

        // Pagination 現在ページ数の取得：：URIセグメントの取得
        $segments = $this->uri->segment_array();
        if (isset($segments[3]))
        {
            $tmp_offset = $segments[3];
        } else {
            $tmp_offset = 0;
        }

        // 1ページ当たりの表示件数
        $this->config->load('config_comm');
        $tmp_per_page = 1;
//      $tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

        // アカウントメンバーの取得
        $this->load->model('Invoice', 'iv', TRUE);
        $this->load->model('Invoice_detail', 'ivd', TRUE);

        $get_iv_data = $this->iv->get_iv_seq($_chg_seq);

        $set_iv_data['iv_seq'] = $get_iv_data[0]['iv_seq'];
        $set_iv_data['iv_issue_yymm'] = $get_iv_data[0]['iv_issue_yymm'];

        list($history_list, $history_countall) = $this->iv->get_historylist($set_iv_data, $tmp_per_page, $tmp_offset);

        $ivd_history_list = $this->ivd->get_ivd_history($history_list['iv_seq'], $history_list['iv_seq_suffix']);

        $this->smarty->assign('list',   $history_list);
        $this->smarty->assign('list_d', $ivd_history_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination_h($history_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall', $history_countall);

        $this->view('invoicelist/history.tpl');

    }

    // Pagination 設定
    private function _get_Pagination($countall, $tmp_per_page)
    {

        $config['base_url']       = base_url() . '/invoicelist/search/';        // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
        $config['per_page']       = $tmp_per_page;                              // 1ページ当たりの表示件数。
        $config['total_rows']     = $countall;                                  // 総件数。where指定するか？
        //$config['uri_segment']    = 4;                                        // オフセット値がURIパスの何セグメント目とするか設定
        $config['num_links']      = 5;                                          //現在のページ番号の左右にいくつのページ番号リンクを生成するか設定
        $config['full_tag_open']  = '<p class="pagination">';                   // ページネーションリンク全体を階層化するHTMLタグの先頭タグ文字列を指定
        $config['full_tag_close'] = '</p>';                                     // ページネーションリンク全体を階層化するHTMLタグの閉じタグ文字列を指定
        $config['first_link']     = '最初へ';                                   // 最初のページを表すテキスト。
        $config['last_link']      = '最後へ';                                   // 最後のページを表すテキスト。
        $config['prev_link']      = '前へ';                                     // 前のページへのリンクを表わす文字列を指定
        $config['next_link']      = '次へ';                                     // 次のページへのリンクを表わす文字列を指定

        $this->load->library('pagination', $config);                            // Paginationクラス読み込み
        $set_page['page_link'] = $this->pagination->create_links();

        return $set_page;

    }

    // Pagination 設定 : 履歴表示
    private function _get_Pagination_h($countall, $tmp_per_page)
    {

        $config['base_url']       = base_url() . '/invoicelist/historychk/';    // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
        $config['per_page']       = $tmp_per_page;                              // 1ページ当たりの表示件数。
        $config['total_rows']     = $countall;                                  // 総件数。where指定するか？
        //$config['uri_segment']    = 4;                                        // オフセット値がURIパスの何セグメント目とするか設定
        $config['num_links']      = 10;                                         //現在のページ番号の左右にいくつのページ番号リンクを生成するか設定
        $config['full_tag_open']  = '<p class="pagination">';                   // ページネーションリンク全体を階層化するHTMLタグの先頭タグ文字列を指定
        $config['full_tag_close'] = '</p>';                                     // ページネーションリンク全体を階層化するHTMLタグの閉じタグ文字列を指定
        $config['first_link']     = '最初へ';                                   // 最初のページを表すテキスト。
        $config['last_link']      = '最後へ';                                   // 最後のページを表すテキスト。
        $config['prev_link']      = '前へ';                                     // 前のページへのリンクを表わす文字列を指定
        $config['next_link']      = '次へ';                                     // 次のページへのリンクを表わす文字列を指定

        $this->load->library('pagination', $config);                            // Paginationクラス読み込み
        $set_page['page_link'] = $this->pagination->create_links();

        return $set_page;

    }

    // 初期値セット
    private function _item_set()
    {

        // ステータス 選択項目セット
        $this->config->load('config_status');
        $opt_iv_status = $this->config->item('PROJECT_IV_STATUS');

        // 課金方式
//      $this->config->load('config_comm');
//      $opt_iv_accounting = $this->config->item('INVOICE_ACCOUNTING');

        // 回収サイトのセット
        $opt_iv_collect = $this->config->item('CUSTOMER_CM_COLLECT');

        // 口座種別のセット
        $opt_iv_kind = $this->config->item('CUSTOMER_CM_KIND');

        $this->smarty->assign('options_iv_status',     $opt_iv_status);
//      $this->smarty->assign('options_iv_accounting', $opt_iv_accounting);
        $this->smarty->assign('options_iv_collect',    $opt_iv_collect);
        $this->smarty->assign('options_iv_kind',       $opt_iv_kind);

    }

    // 検索項目 初期値セット
    private function _search_set()
    {

        // ステータス 選択項目セット
        $this->config->load('config_status');
        $opt_iv_status = $this->config->item('PROJECT_IV_STATUS');

        // 固定請求年月のセット（過去一年分）
        $date = new DateTime();
        $_date_ym = $date->modify('last day of next months')->format('Ym');
//      $_date_ym = $date->format('Ym');
        $opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
        for ($i = 1; $i < 12; $i++) {
            $_date_ym = $date->modify('-1 months')->format('Ym');
            $opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
        }

        // ID 並び替え選択項目セット
        $arropt_id = array (
                ''     => '-- 選択してください --',
                'DESC' => '降順',
                'ASC'  => '昇順',
        );

        $this->smarty->assign('options_iv_status', $opt_iv_status);
        $this->smarty->assign('options_date_fix',  $opt_date_fix);
        $this->smarty->assign('options_orderid',   $arropt_id);

    }

    // 請求データ作成年月 初期値セット
    private function _ym_item_set()
    {

        // 固定請求年月のセット（過去一年分）
        $date = new DateTime();
        $_date_ym = $date->format('Ym');
        $opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月度';
        for ($i = 1; $i < 12; $i++) {
            $_date_ym = $date->modify('-1 months')->format('Ym');
            $opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月度';
        }

        // 成果請求年月のセット
        $date = new DateTime();
        for ($i = 1; $i < 12; $i++) {
            $_date_ym = $date->modify('-1 months')->format('Ym');
            $opt_date_res[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月度';
        }

        $this->smarty->assign('options_date_fix', $opt_date_fix);
        $this->smarty->assign('options_date_res', $opt_date_res);

    }

    // 売上データ削除判定
    private function _delete_sales($iv_seq, $sales_date)
    {

        $date = new DateTime();
        $_today = $date->format('Y-m-d');

        // 売上日付が当日以前の日付を削除する
        if ($_today > $sales_date)
        {
            $this->load->model('Sales', 'sa', TRUE);
            $res = $this->sa->delete_sales($iv_seq);
        }

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック : クライアント更新
    private function _set_validation02()
    {
        $rule_set = array(
                array(
                        'field'   => 'iv_status',
                        'label'   => 'ステータス',
                        'rules'   => 'trim|required|max_length[1]|is_numeric'
                ),
                array(
                        'field'   => 'iv_issue_date',
                        'label'   => '発効日指定',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
                array(
                        'field'   => 'iv_pay_date',
                        'label'   => '振込期日指定',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
                array(
                        'field'   => 'ivd_item1',
                        'label'   => '請求項目',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'ivd_qty1',
                        'label'   => '数量',
                        'rules'   => 'trim|is_numeric'
                ),
                array(
                        'field'   => 'ivd_price1',
                        'label'   => '単価',
                        'rules'   => 'trim|is_numeric'
                ),
                array(
                        'field'   => 'ivd_total1',
                        'label'   => '金額',
                        'rules'   => 'trim|is_numeric'
                ),
                array(
                        'field'   => 'ivd_item2',
                        'label'   => '請求項目',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'ivd_qty2',
                        'label'   => '数量',
                        'rules'   => 'trim|is_numeric'
                ),
                array(
                        'field'   => 'ivd_price2',
                        'label'   => '単価',
                        'rules'   => 'trim|is_numeric'
                ),
                array(
                        'field'   => 'ivd_total2',
                        'label'   => '金額',
                        'rules'   => 'trim|is_numeric'
                ),
                array(
                        'field'   => 'iv_remark',
                        'label'   => '請求書：備考',
                        'rules'   => 'trim|max_length[100]'
                ),
                array(
                        'field'   => 'iv_memo',
                        'label'   => '備考',
                        'rules'   => 'trim|max_length[1000]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}

