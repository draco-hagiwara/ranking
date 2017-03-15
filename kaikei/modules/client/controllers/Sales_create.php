<?php

class Sales_create extends MY_Controller
{

    /*
     *  売上データの手動作成処理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('mess01', FALSE);
        $this->smarty->assign('mess02', FALSE);

    }

    // 手動：データ作成処理TOP
    public function index()
    {

        // バリデーション・チェック
        $this->_set_validation();

        $this->view('sales_create/index.tpl');

    }

    // 売上データ 手動作成
    public function sales_cal()
    {

        $input_post = $this->input->post();

        // 入力日付のチェック：前日までの日付
        $date = new DateTime();
        $sales_chk_date = $date->format('Y-m-d');
        if ($input_post['sales_date'] >= $sales_chk_date)
        {
            $this->_set_validation01();
            $this->smarty->assign('mess01', "<font color=red>ERROR::本日の日付より以前の日付を指定してください。</font>");
         } else {

            // バリデーション・チェック
            $this->_set_validation01();
            if ($this->form_validation->run() == TRUE)
            {

                $this->load->model('Invoice', 'iv',  TRUE);
                $this->load->model('Sales',   'sa',  TRUE);

                // 該当日付の請求書データを抽出
                $get_sales_data = $this->iv->get_iv_sales($input_post['sales_date']);

                if (count($get_sales_data) >= 0)
                {

                    // トランザクション・START
                    $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
                    $this->db->trans_start();                                               // trans_begin

                    $tmp_iv_seq = "";
                    foreach($get_sales_data as $key => $value)
                    {

                        // 既存データは読み飛ばす
                        if ($tmp_iv_seq != $value['iv_seq'])
                        {
                            $_sales_cnt = $this->sa->get_sales_cnt($value['iv_seq']);
                        }

                        // 新規追加データのみ作成
                        if ($_sales_cnt == 0)
                        {

                            // 売上月の指定：売上月度から指定月の売上に振り分ける
                            $set_sales['sa_cm_seq']      = $value['iv_cm_seq'];
                            $set_sales['sa_iv_seq']      = $value['iv_seq'];
                            $set_sales['sa_pj_seq']      = $value['ivd_pj_seq'];
                            $set_sales['sa_sales_date']  = substr($value['iv_sales_yymm'], 0, 4) . '-' . substr($value['iv_sales_yymm'], 4, 2) . '-01';
                            $set_sales['sa_sales_yymm']  = $value['iv_sales_yymm'];
                            $set_sales['sa_slip_no']     = $value['iv_slip_no'];
                            $set_sales['sa_agency_flg']  = $value['iv_agency_flg'];
                            $set_sales['sa_company']     = $value['iv_company_cm'];
                            $set_sales['sa_total']       = $value['ivd_total'];                 // 消費税抜き売上金額
                            $set_sales['sa_accounting']  = $value['ivd_iv_accounting'];
                            $set_sales['sa_collect']     = $value['iv_collect'];
                            $set_sales['sa_issue_yymm']  = $value['iv_issue_yymm'];
                            $set_sales['sa_issue_date']  = $value['iv_issue_date'];
                            $set_sales['sa_pay_date']    = $value['iv_pay_date'];
                            $set_sales['sa_keyword']     = $value['ivd_item'];
                            $set_sales['sa_salesman']    = $value['iv_salesman'];
                            $set_sales['sa_salesman_id'] = $value['iv_salesman_id'];

                            $this->sa->insert_sales($set_sales);
                        }

                        $tmp_iv_seq = $value['iv_seq'];
                    }

                    // トランザクション・COMMIT
                    $this->db->trans_complete();                                            // trans_rollback & trans_commit
                    if ($this->db->trans_status() === FALSE)
                    {
                        log_message('error', 'CLIENT::[Sales_create -> sales_cal()]：売上データ 手動作成処理 トランザクションエラー');
                        $this->smarty->assign('mess02', "<font color=red>ERROR::売上データの作成に失敗しました。</font>");
                    } else {
                        $this->smarty->assign('mess02', "<font color=blue>売上データが作成されました。</font>");
                    }
                }
            }
        }

        $this->view('sales_create/index.tpl');

    }

    // 債権データ 手動作成
    public function receivable_cal()
    {

        $input_post = $this->input->post();

        // バリデーション・チェック
        $this->_set_validation02();
        if ($this->form_validation->run() == TRUE)
        {

            $this->load->model('Invoice',    'iv', TRUE);
            $this->load->model('Receivable', 'rv', TRUE);

            // 該当日付範囲の請求書データを抽出
            $date = new DateTime($input_post['sales_date01']);
            $sales_date01 = $date->format('Y-m-d 00:00:00');
            $date = new DateTime($input_post['sales_date02']);
            $sales_date02 = $date->format('Y-m-d 23:59:59');

            $get_sales_data = $this->iv->get_iv_receivable($sales_date01, $sales_date02);





            /*
             * 2017.02.07
             *
             * 一回ペンディング？？？？
             * 会計ソフトに任せるか？？
             */
            print_r($get_sales_data);
            exit;




            if (count($get_sales_data) >= 0)
            {

                // トランザクション・START
                $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
                $this->db->trans_start();                                               // trans_begin

                $_cm_seq_b = "";
                foreach($get_sales_data as $key => $value)
                {

                    // 債権履歴データへ書き込み
                    $set_rvd = array();
                    $set_rvd['rv_sales_date']  = $value['iv_sales_date'];
                    $set_rvd['rv_tax']         = $value['iv_tax'];
                    $set_rvd['rv_total']       = $value['iv_total'];
                    $set_rvd['rv_cm_seq']      = $value['iv_cm_seq'];
                    $set_rvd['rv_company']     = $value['iv_company'];
                    $set_rvd['rv_collect']     = $value['iv_collect'];
                    $set_rvd['rv_salesman_id'] = $value['iv_salesman_id'];
                    $set_rvd['rv_salesman']    = $value['iv_salesman'];
                    $set_rvd['rv_memo']        = $value['iv_memo'];
                    $set_rvd['rv_iv_seq']      = $value['iv_seq'];
                    $set_rvd['rv_slip_no']     = $value['iv_slip_no'];

                    $this->rv->insert_receivable_history($set_rvd);

                    // 債権データのtotal金額の集計＆一時保存 << 顧客毎
                    $_cm_seq_a = $value['iv_cm_seq'];
                    if ($_cm_seq_b == $_cm_seq_a)
                    {
                        $set_rv_total[$value['iv_cm_seq']]       = $set_rv_total[$value['iv_cm_seq']] + $value['iv_total'];
                        $set_rv_tax[$value['iv_cm_seq']]         = $set_rv_tax[$value['iv_cm_seq']]   + $value['iv_tax'];
                    } else {
                        $set_rv_total[$value['iv_cm_seq']]       = $value['iv_total'];
                        $set_rv_tax[$value['iv_cm_seq']]         = $value['iv_tax'];

                        $set_rv_cm_seq[$value['iv_cm_seq']]      = $value['iv_cm_seq'];
                        $set_rv_company[$value['iv_cm_seq']]     = $value['iv_company'];
                        $set_rv_salesman_id[$value['iv_cm_seq']] = $value['iv_salesman_id'];
                        $set_rv_salesman[$value['iv_cm_seq']]    = $value['iv_salesman'];
                        $set_rv_memo[$value['iv_cm_seq']]        = $value['iv_memo'];

                        $_cm_seq_b = $value['iv_cm_seq'];
                    }
                }

                // 債権データ作成
                foreach($set_rv_cm_seq as $key => $value)
                {

                    $set_rv_data = array();
                    $set_rvd     = array();

                    // 既存債権データの読み込み
                    $get_receivable_data = $this->rv->get_rv_cm_seq($value);

                    // 債権データにセット
                    $set_rv_data['rv_cm_seq']      = $set_rv_cm_seq[$value];
                    $set_rv_data['rv_company']     = $set_rv_company[$value];
                    $set_rv_data['rv_salesman_id'] = $set_rv_salesman_id[$value];
                    $set_rv_data['rv_salesman']    = $set_rv_salesman[$value];
                    $set_rv_data['rv_memo']        = $set_rv_memo[$value];

                    $set_rvd['rv_cm_seq']          = $set_rv_cm_seq[$value];
                    $set_rvd['rv_company']         = $set_rv_company[$value];

                    if (count($get_receivable_data) == 0)
                    {

                        // 債権total金額の再計算
                        $set_rv_data['rv_total'] = $set_rv_total[$value];
                        $set_rv_data['rv_tax']   = $set_rv_tax[$value];

                        // 債権データに書き込み：INSERT
                        $this->rv->insert_receivable($set_rv_data);

                        // 債権履歴データに書き込み：INSERT
                        $set_rvd['rv_total']     = $set_rv_data['rv_total'];
                        $set_rvd['rv_tax']       = $set_rv_data['rv_tax'];
                        $this->rv->insert_receivable_history($set_rvd);

                    } elseif (count($get_receivable_data) == 1) {

                        // 債権total金額の再計算
                        $set_rv_data['rv_total'] = $get_receivable_data[0]['rv_total'] + $set_rv_total[$value];
                        $set_rv_data['rv_tax']   = $get_receivable_data[0]['rv_tax']   + $set_rv_tax[$value];

                        // 債権データを更新：UPDATE
                        $set_rv_data['rv_seq']   = $get_receivable_data[0]['rv_seq'];
                        $this->rv->update_receivable($set_rv_data);

                        // 債権履歴データに書き込み：INSERT
                        $set_rvd['rv_total']     = $set_rv_data['rv_total'];
                        $set_rvd['rv_tax']       = $set_rv_data['rv_tax'];
                        $this->rv->insert_receivable_history($set_rvd);

                    } else {
                        // エラーリスト:重複データ
                        log_message('error', 'CLIENT::[Sales_create -> receivable_cal()]：重複データエラーリスト' . $value);
                    }
                }

                // トランザクション・COMMIT
                $this->db->trans_complete();                                            // trans_rollback & trans_commit
                if ($this->db->trans_status() === FALSE)
                {
                    log_message('error', 'CLIENT::[Sales_create -> receivable_cal()]：債権データ 手動作成処理 トランザクションエラー');
                    log_message('error', 'CLIENT::[Sales_create -> receivable_cal()]：エラー rv_cm_seq' . $set_rvd['rv_cm_seq']);
                    log_message('error', 'CLIENT::[Sales_create -> receivable_cal()]：エラー rv_cm_seq' . $set_rv_data['rv_cm_seq'] . '::' . $set_rv_data['rv_company']);
                }
            }
        }

        $this->view('sales_create/index.tpl');

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック
    private function _set_validation01()
    {

        $rule_set = array(
                array(
                        'field'   => 'sales_date',
                        'label'   => '売上日付',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック
    private function _set_validation02()
    {

        $rule_set = array(
                array(
                        'field'   => 'sales_date01',
                        'label'   => '売上日',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
                array(
                        'field'   => 'sales_date02',
                        'label'   => '売上日',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}

