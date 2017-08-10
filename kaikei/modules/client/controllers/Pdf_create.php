<?php

class Pdf_create extends MY_Controller
{

    /*
     *  請求書ＰＤＦの作成処理
     *
     *    > 一覧から複数ＰＤＦ作成
     *    > 請求書編集から作成
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

    }

    // 請求書PDF 個別作成
    public function pdf_one()
    {

        $input_post = $this->input->post();

        // 「キャンセル」ボタンで更新＆一覧表示！
        if ($input_post['submit'] == 'submit')
        {

            $this->load->model('Invoice',        'iv',  TRUE);
            $this->load->model('Invoice_detail', 'ivd', TRUE);
            $this->load->library('lib_invoice');
            $this->config->load('config_comm');

            // 請求書データの取得
            $get_iv_data[0] = $this->iv->get_iv_seq($input_post['iv_seq']);

            // 明細データの取得
            $get_ivd_data[0] = $this->ivd->get_iv_seq($input_post['iv_seq'], $get_iv_data[0][0]['iv_issue_yymm'], $get_iv_data[0][0]['iv_seq_suffix']);

            // バリデーション・チェック
            $this->_set_validation();


            // トランザクション・START
            $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
            $this->db->trans_start();                                               // trans_begin

                // tb_invoice 更新 + 履歴作成
                $_slip_no = $this->_chg_invoice($get_iv_data[0][0], $get_ivd_data[0]);
                $get_iv_data[0][0]['iv_slip_no'] = $_slip_no;

            // トランザクション・COMMIT
            $this->db->trans_complete();                                            // trans_rollback & trans_commit
            if ($this->db->trans_status() === FALSE)
            {
                log_message('error', 'CLIENT::[Pdf_create -> pdf_one()]：請求書PDF 個別作成処理 トランザクションエラー');
            }

            // 雛形PDFのパス取得
            $this->load->helper('path');
            $list_path = '../public/images/pdf/receipt_list.pdf';
            $pdflist_path = set_realpath($list_path);

            // インストールパスを取得 :: /var/www/kaikei
            $list_path = '../';
            $base_path = set_realpath($list_path);

            // PDFライブラリ呼出
            $this->load->library('lib_pdf_invoice');
//          $this->lib_pdf_invoice->pdf_one($get_iv_data[0], $get_ivd_data, $pdflist_path, $base_path);
            $this->lib_pdf_invoice->create_pdf($get_iv_data, $get_ivd_data, $pdflist_path, $base_path);


        } else {
            redirect('/invoicelist/');
        }

    }

    // 請求書PDF 一括作成
    public function pdf_invoice()
    {

        // 更新対象データの取得
        $input_post = $this->input->post();


//         print_r($input_post);
//         exit;



        // 「キャンセル」ボタンで更新＆一覧表示！
        if ($input_post['_submit'] == 'submit')
        {

            if (count($input_post) >= 3)
            {

                $this->load->model('Invoice',        'iv',  TRUE);
                $this->load->model('Invoice_detail', 'ivd', TRUE);
                $this->load->library('lib_invoice');
                $this->config->load('config_comm');

                // 不要パラメータ削除
                unset($input_post["iv_issue_yymm"]) ;
                unset($input_post["invoice_all"]) ;
                unset($input_post["_submit"]) ;

                $list_cnt = count($input_post);
                $i = 0;
                foreach ($input_post as $key => $val)
                {

                    $get_iv_data[$i]  = array();
                    $get_ivd_data[$i] = array();

                    // 請求書データの取得
                    $get_iv_data[$i]  = $this->iv->get_iv_seq($val);

                    // 明細データの取得
                    $get_ivd_data[$i] = $this->ivd->get_iv_seq($val, $get_iv_data[$i][0]['iv_issue_yymm'], $get_iv_data[$i][0]['iv_seq_suffix']);

                    // トランザクション・START
                    $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
                    $this->db->trans_start();                                               // trans_begin

                        // tb_invoice 更新 + 履歴作成
                        $_slip_no = $this->_chg_invoice($get_iv_data[$i][0], $get_ivd_data[$i]);
                        $get_iv_data[$i][0]['iv_slip_no'] = $_slip_no;

                    // トランザクション・COMMIT
                    $this->db->trans_complete();                                            // trans_rollback & trans_commit
                    if ($this->db->trans_status() === FALSE)
                    {
                        log_message('error', 'CLIENT::[Pdf_create -> pdf_one()]：請求書PDF 個別作成処理 トランザクションエラー');
                    }

                    $i++;

                }

                // バリデーション・チェック
                $this->_set_validation();

                // 雛形PDFのパス取得
                $this->load->helper('path');
                $list_path = '../public/images/pdf/receipt_list.pdf';
                $pdflist_path = set_realpath($list_path);

                // インストールパスを取得 :: /var/www/kaikei
                $list_path = '../';
                $base_path = set_realpath($list_path);

                // PDFライブラリ呼出
                $this->load->library('lib_pdf_invoice');
                $this->lib_pdf_invoice->create_pdf($get_iv_data, $get_ivd_data, $pdflist_path, $base_path);
//              $this->lib_pdf_invoice->pdf_batch($get_iv_data, $get_ivd_data, $pdflist_path, $base_path);
//              $this->lib_pdf_invoice->pdf_batch($get_iv_data, $get_ivd_data, $pdflist_path, $base_path, $page_add = TRUE);
//              $this->lib_pdf_invoice->create_pdf_multi($get_iv_data, $get_ivd_data, $pdflist_path, $base_path);


            }
        }

        redirect('/invoicelist/');

    }

    // tb_invoice 更新 + 履歴作成
    private function _chg_invoice($get_iv_data, $get_ivd_data)
    {

        // データをセット
        $set_data_iv = $get_iv_data;

        // 売上データ作成有無の判定
        if ($get_iv_data['iv_status'] == 0)
        {
            // 「未発行」→「発行済」：売上指定日 = 発行日
            $set_data_iv['iv_sales_date'] = $get_iv_data['iv_issue_date'];                          // 売上指定日
//          $date = new DateTime();
//          $set_data_iv['iv_sales_date'] = $date->format('Y-m-d');                                 // 売上日
        }

        $set_data_iv['iv_status']     = 1;                                                          // ステータス：「発行済」

        $set_data_iv['iv_seq_suffix'] = $get_iv_data['iv_seq_suffix'] + 1;                          // 履歴カウント
        $set_data_iv['iv_reissue']    = $get_iv_data['iv_reissue'] + 1;                             // 発行カウント

        // 請求書発行番号 :: 【LA101-KT-BX001-1611】
        $tmp_sales_info = explode("-", $get_iv_data['iv_slip_no']);
        switch( $tmp_sales_info[1] )
        {
            case "KT":
                $_sales_info =  0;
                break;
            case "SK":
                $_sales_info =  2;
                break;
            case "MA":
                $_sales_info =  7;
                break;
            case "AF":
                $_sales_info =  10;
                break;
            case "KK":
                $_sales_info =  11;
                break;
            default:
                $_sales_info =  12;
                break;
        }

        $_invo_info = substr($tmp_sales_info[2], 1, 1);
//      if ($_sales_info == 8)
//      {
//          $_invo_info = "Y";
//      } elseif ($_sales_info == 9) {
//          $_invo_info = "Z";
//      } else {
//          $_invo_info = "X";
//      }

        $_invo_serial_num = substr($get_iv_data['iv_slip_no'], -8,  3);
        $_invo_class      = substr($get_iv_data['iv_slip_no'], -10, 1);

        $set_data_iv['iv_slip_no']    = $this->lib_invoice->issue_num(  $set_data_iv['iv_cm_seq'],
                                                                        $get_iv_data['iv_issue_yymm'],
                                                                        $_sales_info,
                                                                        $_invo_serial_num,
                                                                        $_invo_info,
                                                                        $set_data_iv['iv_reissue'],
                                                                        $_invo_class
                                        );

        // 不要パラメータ削除
        unset($set_data_iv["iv_create_date"]) ;
        unset($set_data_iv["iv_update_date"]) ;

        // 請求書データ : 既存データ書き換えUPDATE
        $this->iv->update_invoice($set_data_iv);

        // 履歴ファイルを作成
        $this->iv->insert_invoice_history($set_data_iv);

        // 明細データ作成
        foreach($get_ivd_data as $key => $val)
        {

            // データをセット
            $set_data_ivd = $val;

            $set_data_ivd['ivd_seq_suffix'] = $val['ivd_seq_suffix'] + 1;
            $set_data_ivd['ivd_status'] = 0;

            // 請求書データ : 既存データ書き換えUPDATE
            $this->ivd->update_invoice_detail($set_data_ivd);

            // 履歴ファイルを作成
            $this->ivd->insert_invoice_detail_history($set_data_ivd);

        }

        // 請求書番号を返す
        return $set_data_iv['iv_slip_no'];
    }

//     // 初期値セット
//     private function _item_set()
//     {

//      // ステータス 選択項目セット
//      $this->config->load('config_status');
//      $opt_iv_status = $this->config->item('PROJECT_IV_STATUS');

//      // 課金方式
//      $this->config->load('config_comm');
//      $opt_iv_accounting = $this->config->item('INVOICE_ACCOUNTING');

//      // 口座種別のセット
//      $opt_iv_kind = $this->config->item('CUSTOMER_CM_KIND');

//      $this->smarty->assign('options_iv_status',     $opt_iv_status);
//      $this->smarty->assign('options_iv_accounting', $opt_iv_accounting);
//      $this->smarty->assign('options_iv_kind',       $opt_iv_kind);

//     }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}

