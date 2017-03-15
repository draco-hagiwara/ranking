<?php

class Shokaifee_create extends MY_Controller
{

    /*
     *  紹介料計算処理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('mess', FALSE);

    }

    // 紹介料計算処理TOP
    public function index()
    {

        // バリデーション・チェック
        $this->_set_validation();

        // 初期値セット
        $this->_ym_item_set();

        $this->view('shokaifee_create/index.tpl');

    }

    // 紹介料計算 一括作成
    public function fix_cal()
    {

        $input_post = $this->input->post();

        $this->load->model('Customer', 'cm', TRUE);
        $this->load->model('Sales',    'sa', TRUE);
        $this->load->model('Shokai',   'sk', TRUE);
        $this->load->library('lib_invoice');
        $this->load->library('lib_shokai');
        $this->config->load('config_comm');

        // バリデーション・チェック
        if ($input_post['_submit'] == "save_oly")
        {
            $this->_set_validation03();
        } else {
            $this->_set_validation02();
        }

        if ($this->form_validation->run() == TRUE)
        {

            // 有効な「支払先情報」を抽出
            if ($input_post['_submit'] == "save_oly")
            {
                $sk_list = $this->sk->get_sk_list($input_post['skd_cm_seq01']);
                $invo_class = "C";
            } else {
                $sk_list = $this->sk->get_sk_list();
                $invo_class = "B";
            }

            if (count($sk_list) == 0)
            {
                $this->smarty->assign('mess', "<font color=red>対象データが存在しません。</font>");

                // 初期値セット
                $this->_ym_item_set();
                $this->view('shokaifee_create/index.tpl');
                return;
            }

            // 発行月の発行通番を取得
            $_skf_pay_no  = $this->lib_shokai->shiharai_serial_num($input_post['issue_yymm']);
            $_serial_num_start = $_skf_pay_no;
            $_serial_num       = $_skf_pay_no;

            // トランザクション・START
            $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
            $this->db->trans_start();                                               // trans_begin

            // 抽出された支払先情報から、売上先会社の情報を抽出する
            $set_skf = array();
            foreach($sk_list as $key => $value)
            {

                // 売上年月を取得
                $sales_yymm = $this->lib_shokai->issue_collect($value['sk_payment'], $input_post['issue_yymm']);

                // ** 売上先会社を取得
                $skc_list = $this->sk->get_skc_list($value['sk_seq']);

                // 売上先会社毎の売上高を取得
                $skf_pay_total = 0;
                $set_skd = array();
                $i = 0;
                foreach($skc_list as $key_skc => $val_skc)
                {

                    // ** 売上情報から該当会社の情報を取得
                    $sales_list = $this->sa->get_shokailist($val_skc['skc_cm_seq'], $sales_yymm['sales_yymm']);

                    if (count($sales_list) >= 1)
                    {
                        foreach($sales_list as $key_sales => $val_sales)
                        {
                            // ** 紹介料詳細情報をセット
                            $set_skd[$i]['skd_sa_seq']         = $val_sales['sa_seq'];
                            $set_skd[$i]['skd_sa_sales_date']  = $val_sales['sa_sales_date'];
                            $set_skd[$i]['skd_sa_sales_yymm']  = $val_sales['sa_sales_yymm'];
                            $set_skd[$i]['skd_sa_slip_no']     = $val_sales['sa_slip_no'];
                            $set_skd[$i]['skd_sa_company']     = $val_sales['sa_company'];
                            $set_skd[$i]['skd_sa_total']       = $val_sales['sa_total'];
                            $set_skd[$i]['skd_sa_accounting']  = $val_sales['sa_accounting'];
                            $set_skd[$i]['skd_sa_collect']     = $val_sales['sa_collect'];
                            $set_skd[$i]['skd_sa_keyword']     = $val_sales['sa_keyword'];
                            $set_skd[$i]['skd_sa_salesman_id'] = $val_sales['sa_salesman_id'];
                            $set_skd[$i]['skd_sa_salesman']    = $val_sales['sa_salesman'];
                            $set_skd[$i]['skd_cm_seq']         = $val_sales['sa_cm_seq'];
                            $set_skd[$i]['skd_iv_seq']         = $val_sales['sa_iv_seq'];

                            // 売上情報から各案件の紹介料情報を取得する
                            $shokai_info = $this->sk->get_pj_shokai($val_sales['sa_pj_seq'], $_SESSION['c_memGrp'], 'seorank');

                            // 紹介料を計算 :: 固定金額 +（ 料率 × 売上高 ）
                            $_issue_tax['zeiritsu'] = $this->config->item('INVOICE_TAX');
                            $_issue_tax['hasuu']    = $this->config->item('INVOICE_TAX_CAL');
                            $_tmp_pay_total         = $this->lib_shokai->cal_result_total(
                            		                                                        $val_sales['sa_total'],
                                                                                            $shokai_info[0]['pj_paycal_rate'],
                                                                                            $shokai_info[0]['pj_paycal_fix'],
                                                                                            $_issue_tax
                            );

                            $set_skd[$i]['skd_pj_seq']         = $shokai_info[0]['pj_seq'];
                            $set_skd[$i]['skd_pay_fix']        = $shokai_info[0]['pj_paycal_fix'];
                            $set_skd[$i]['skd_pay_rate']       = $shokai_info[0]['pj_paycal_rate'];
                            $set_skd[$i]['skd_pay_subtotal']   = $_tmp_pay_total;

                            // 紹介料金額を計算
                            $skf_pay_total = $skf_pay_total + $_tmp_pay_total;

                            $i++;
                        }
                    }
                }

                // 紹介料支払レコードの有無チェック
                $get_skf_data = $this->sk->get_skf_skseq($value['sk_seq'], $sales_yymm['sales_yymm']);
                if (count($get_skf_data) >= 1)
                {
                    // レコード削除
                    $this->sk->delete_shokai_fee($get_skf_data[0]['skf_seq']);
                    $this->sk->delete_shokai_detail($get_skf_data[0]['skf_seq']);
                }

                // ** 紹介料情報をセット
                $set_skf['skf_sk_seq']     = $value['sk_seq'];
                $set_skf['skf_pay_yymm']   = $sales_yymm['sales_yymm'];
                $set_skf['skf_issue_date'] = $input_post['skf_issue_date01'];
                $set_skf['skf_pay_date']   = $sales_yymm['pay_date'];
                $set_skf['skf_pay_total']  = $skf_pay_total;
                $set_skf['skf_payment']    = $value['sk_payment'];
                $set_skf['skf_sk_company'] = $value['sk_company'];
                $set_skf['skf_account_nm'] = $this->config->item('SHOKAI_SKF_ACCOUNTNM');;

                // 消費税計算
                $_issue_tax['zeiritsu']    = $this->config->item('INVOICE_TAX');
                $_issue_tax['hasuu']       = $this->config->item('INVOICE_TAX_CAL');
                $_issue_tax['zeinuki']     = $value['sk_tax_out'];
                $set_skf['skf_pay_tax']    = $this->lib_invoice->cal_tax($skf_pay_total, $_issue_tax);

                // 支払通知書番号
                $set_skf['skf_pay_no']     = $this->lib_shokai->shiharai_num(
                                                                                $value['sk_seq'],
                                                                                $input_post['issue_yymm'],
                                                                                $_serial_num,
                                                                                $invo_class
                );

                // ** 紹介料情報の書き込み
                $skf_seq = $this->sk->insert_shokai_fee($set_skf);

                // ** 紹介料詳細情報の書き込み
                foreach($set_skd as $key_skd => $val_skd)
                {
                    $val_skd['skd_skf_seq'] = $skf_seq;
                    $skd_seq = $this->sk->insert_shokai_detail($val_skd);
                }

                $_serial_num = $_serial_num + 1;

            }

            // 発行通番の書き込み
            if ($_serial_num_start != $_serial_num)
            {
                $this->lib_shokai->shiharai_serial_num_update($input_post['issue_yymm'], $_serial_num);
            }

            // トランザクション・COMMIT
            $this->db->trans_complete();                                            // trans_rollback & trans_commit
            if ($this->db->trans_status() === FALSE)
            {
                log_message('error', 'CLIENT::[Shokaifee_create -> fix_cal()]：紹介料一括作成処理 トランザクションエラー');
            } else {
                $this->smarty->assign('mess',  "<font color=blue>紹介料計算が完了しました。</font>");
            }
        }

        // 初期値セット
        $this->_ym_item_set();

        $this->view('shokaifee_create/index.tpl');

    }

    // 紹介料計算作成年月 初期値セット
    private function _ym_item_set()
    {

        // 翌月年月のセット <- (当月) から表示（過去一年分）
        /*
         * DateTimeで"+1"や"-1"を指定すると、30-31日や2月の計算でおかしくなる可能性があるので注意！
         */
        $date = new DateTime();
        $_date_ym = $date->modify('first day of next months')->format('Ym');
        $opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
        for ($i = 1; $i < 12; $i++) {
            $_date_ym = $date->modify('-1 months')->format('Ym');
            $opt_date_fix[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
        }

        // 当月年月のセット <- (当月 - 1) から表示
        $date = new DateTime();
        $_date_ym = $date->format('Ym');
        $opt_date_res[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
        for ($i = 1; $i < 12; $i++) {
            $_date_ym = $date->modify('-1 months')->format('Ym');
            $opt_date_res[$_date_ym] = substr($_date_ym, 0, 4) . '年' . substr($_date_ym, 4, 2) . '月分';
        }

        $this->smarty->assign('options_issue_yymm', $opt_date_res);

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック
    private function _set_validation02()
    {

        $rule_set = array(
                array(
                        'field'   => 'skf_issue_date01',
                        'label'   => '発効日指定',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
                array(
                        'field'   => 'skd_cm_seq01',
                        'label'   => '支払先情報番号',
                        'rules'   => 'trim|max_length[0]|is_numeric'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック
    private function _set_validation03()
    {

        $rule_set = array(
                array(
                        'field'   => 'skf_issue_date01',
                        'label'   => '発効日指定',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
                array(
                        'field'   => 'skd_cm_seq01',
                        'label'   => '支払先情報番号',
                        'rules'   => 'trim|required|max_length[10]|is_numeric'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}

