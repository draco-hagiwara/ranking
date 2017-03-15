<?php

class Shokaifeelist extends MY_Controller
{

    /*
     *  支払紹介料情報処理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('mess', FALSE);

    }

    // 支払紹介料情報一覧TOP
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

            // 支払年月 <- 初期値(当月1日を設定)
            $date = new DateTime();
            $_date_ymd = $date->modify('first day of this months')->format('Y-m');

            $tmp_inputpost = array(
                                'skf_pay_no'       => '',
                                'skf_sk_company'   => '',
                                'skf_pay_date01'   => $_date_ymd,
                                'skf_pay_date02'   => '',
                                'orderid'          => '',
            );

            // セッションをフラッシュデータとして保存
            $data = array(
                                'c_skf_pay_no'     => '',
                                'c_skf_sk_company' => '',
                                'c_skf_pay_date01' => $_date_ymd,
                                'c_skf_pay_date02' => '',
                                'c_orderid'        => '',
            );
            $this->session->set_userdata($data);
        }

        $this->smarty->assign('list', NULL);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination(0, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall', 0);

        $this->smarty->assign('seach_skf_pay_no',     $tmp_inputpost['skf_pay_no']);
        $this->smarty->assign('seach_skf_sk_company', $tmp_inputpost['skf_sk_company']);
        $this->smarty->assign('seach_skf_pay_date01', $tmp_inputpost['skf_pay_date01']);
        $this->smarty->assign('seach_skf_pay_date02', $tmp_inputpost['skf_pay_date02']);
        $this->smarty->assign('seach_orderid',        $tmp_inputpost['orderid']);

        $this->view('shokaifeelist/index.tpl');

    }

    // 一覧表示
    public function search()
    {

        $_form_err = FALSE;

        // 検索ボタン押下 か ページング処理かを判定
        if ($this->input->post('submit') == '_submit')
        {

            // バリデーション・チェック
            $this->_set_validation02();
            if ($this->form_validation->run() == FALSE)
            {

                $_form_err = TRUE;
                $tmp_inputpost = $this->input->post();

                // Pagination 設定
                $set_pagination = $this->_get_Pagination(0, 0);

                $input_post['skf_pay_date01'] = $tmp_inputpost['skf_pay_date01'];
                $input_post['skf_pay_date02'] = $tmp_inputpost['skf_pay_date02'];

                $this->smarty->assign('list', NULL);
                $this->smarty->assign('set_pagination', $set_pagination['page_link']);
                $this->smarty->assign('countall', 0);

            } else {

                $tmp_inputpost = $this->input->post();

                // セッションをフラッシュデータとして保存
                $data = array(
                                'c_skf_pay_no'     => $tmp_inputpost['skf_pay_no'],
                                'c_skf_sk_company' => $tmp_inputpost['skf_sk_company'],
                                'c_skf_pay_date01' => $tmp_inputpost['skf_pay_date01'],
                                'c_skf_pay_date02' => $tmp_inputpost['skf_pay_date02'],
                                'c_orderid'        => $tmp_inputpost['orderid'],
                );
                $this->session->set_userdata($data);

                $input_post['skf_pay_date01'] = $tmp_inputpost['skf_pay_date01'];
                $input_post['skf_pay_date02'] = $tmp_inputpost['skf_pay_date02'];

                unset($tmp_inputpost["submit"]);

                // 振込日指定を整形
                if ($tmp_inputpost['skf_pay_date02'] == '')
                {
                    $date = new DateTime();
                    $tmp_inputpost['skf_pay_date02'] = $date->format('Ym');
                }
            }

        } else {

            // バリデーション・チェック
            $this->_set_validation();                                               // バリデーション設定
//          $this->form_validation->run();

            // セッションからフラッシュデータ読み込み
            $tmp_inputpost['skf_pay_no']     = $_SESSION['c_skf_pay_no'];
            $tmp_inputpost['skf_sk_company'] = $_SESSION['c_skf_sk_company'];
            $tmp_inputpost['skf_pay_date01'] = $_SESSION['c_skf_pay_date01'];
            $tmp_inputpost['skf_pay_date02'] = $_SESSION['c_skf_pay_date02'];
            $tmp_inputpost['orderid']        = $_SESSION['c_orderid'];

            $input_post['skf_pay_date01'] = $_SESSION['c_skf_pay_date01'];
            $input_post['skf_pay_date02'] = $_SESSION['c_skf_pay_date02'];

            // 振込日指定を整形
            if ($tmp_inputpost['skf_pay_date02'] == '')
            {
                $date = new DateTime();
                $tmp_inputpost['skf_pay_date02'] = $date->format('Ym');
            }
        }

        if ($_form_err == FALSE)
        {

            // Pagination 現在ページ数の取得：：URIセグメントの取得
            $segments = $this->uri->segment_array();
            if (isset($segments[3]))
            {
                $tmp_offset = $segments[3];
            } else {
                $tmp_offset = 0;
            }

            // 1ページ当たりの表示件数
            $tmp_per_page = 50;
//          $this->config->load('config_comm');
//          $tmp_per_page = $this->config->item('PAGINATION_PER_PAGE');

            // 支払紹介料情報の取得
            $this->load->model('Shokai', 'sk', TRUE);
            list($shokaifee_list, $shokaifee_countall) = $this->sk->get_shokaifeelist($tmp_inputpost, $tmp_per_page, $tmp_offset);

            $this->smarty->assign('list', $shokaifee_list);

            // Pagination 設定
            $set_pagination = $this->_get_Pagination($shokaifee_countall, $tmp_per_page);

            $this->smarty->assign('set_pagination', $set_pagination['page_link']);
            $this->smarty->assign('countall', $shokaifee_countall);
        }

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('seach_skf_pay_no',     $tmp_inputpost['skf_pay_no']);
        $this->smarty->assign('seach_skf_sk_company', $tmp_inputpost['skf_sk_company']);
        $this->smarty->assign('seach_skf_pay_date01', $input_post['skf_pay_date01']);
        $this->smarty->assign('seach_skf_pay_date02', $input_post['skf_pay_date02']);
        $this->smarty->assign('seach_orderid',        $tmp_inputpost['orderid']);

        $this->view('shokaifeelist/index.tpl');

    }

    // 支払紹介料情報編集
    public function detail()
    {

        // 更新対象データの取得
        $input_post = $this->input->post();

        $this->load->model('Shokai', 'sk', TRUE);

        $get_skf_data = $this->sk->get_skf_seq($input_post['chg_seq']);
        $this->smarty->assign('info', $get_skf_data[0]);

        // 明細データの取得
        $get_skd_data = $this->sk->get_skd_skfseq($input_post['chg_seq']);
        $this->smarty->assign('infodetail', $get_skd_data);

        // バリデーション設定
        $this->_set_validation();

        // 初期値セット
        $this->_item_set();

        // 支払サイトセット
        $this->_pay_item_set($get_skf_data[0]['skf_payment']);

        $this->view('shokaifeelist/detail.tpl');

    }

    // 支払紹介料情報チェック
    public function detailchk()
    {

        $input_post = $this->input->post();

        $this->load->model('Shokai', 'sk', TRUE);
        $this->load->library('lib_shokai');
        $this->load->library('lib_invoice');
        $this->config->load('config_comm');

        // バリデーション・チェック
        $this->_set_validation01();
        if ($this->form_validation->run() == TRUE)
        {
            // 「未発行」から「発行済」へ変更時、または「未発行」時に紹介料を再計算する
            if ((($input_post['skf_status_old'] == 0) && ($input_post['skf_status'] == 1))
                OR (($input_post['skf_status_old'] == 0) && ($input_post['skf_status'] == 0)))
            {
                $skf_pay_total = 0;
                foreach ($input_post['skd_seq'] as $key => $value)
                {
                    $set_skd_data['skd_seq']      = $value;
                    $set_skd_data['skd_sa_total'] = $input_post['skd_sa_total'][$key];
                    $set_skd_data['skd_pay_fix']  = $input_post['skd_pay_fix'][$key];
                    $set_skd_data['skd_pay_rate'] = $input_post['skd_pay_rate'][$key];

                    // 紹介料を計算 :: 固定金額 +（ 料率 × 売上高 ）
                    $_issue_tax['zeiritsu'] = $this->config->item('INVOICE_TAX');
                    $_issue_tax['hasuu']    = $this->config->item('INVOICE_TAX_CAL');
                    $_tmp_pay_total         = $this->lib_shokai->cal_result_total(
                                                                            $set_skd_data['skd_sa_total'],
                                                                            $set_skd_data['skd_pay_rate'],
                                                                            $set_skd_data['skd_pay_fix'],
                                                                            $_issue_tax
                                            );

                    $set_skd_data['skd_pay_subtotal'] = $_tmp_pay_total;

                    // UPDATE
                    $this->sk->update_shokai_detail($set_skd_data);

                    // 紹介料金額を計算
                    $skf_pay_total = $skf_pay_total + $_tmp_pay_total;
                }

                // 消費税計算
                $_issue_tax['zeiritsu']    = $this->config->item('INVOICE_TAX');
                $_issue_tax['hasuu']       = $this->config->item('INVOICE_TAX_CAL');
                if ($input_post['skf_pay_tax'] == 0)
                {
                    $_issue_tax['zeinuki']     = 1;
                } else {
                    $_issue_tax['zeinuki']     = 0;
                }

                $set_skf_data['skf_pay_tax']   = $this->lib_invoice->cal_tax($skf_pay_total, $_issue_tax);
                $set_skf_data['skf_pay_total'] = $skf_pay_total;

            }

            $set_skf_data['skf_seq']    = $input_post['skf_seq'];
            $set_skf_data['skf_status'] = $input_post['skf_status'];
            $set_skf_data['skf_remark'] = $input_post['skf_remark'];
            $set_skf_data['skf_memo']   = $input_post['skf_memo'];

            // UPDATE
            $this->sk->update_shokai_fee($set_skf_data);
        }

        // 更新対象データの取得
        $get_skf_data = $this->sk->get_skf_seq($input_post['skf_seq']);
        $this->smarty->assign('info', $get_skf_data[0]);

        // 明細データの取得
        $get_skd_data = $this->sk->get_skd_skfseq($input_post['skf_seq']);
        $this->smarty->assign('infodetail', $get_skd_data);

        // 初期値セット
        $this->_item_set();

        // 支払サイトセット
        $this->_pay_item_set($get_skf_data[0]['skf_payment']);

        $this->view('shokaifeelist/detail.tpl');

    }

    // CSV ダウンロード
    public function csvdown()
    {

        // セッションからフラッシュデータ読み込み
        $tmp_inputpost['skf_pay_no']     = $_SESSION['c_skf_pay_no'];
        $tmp_inputpost['skf_sk_company'] = $_SESSION['c_skf_sk_company'];
        $tmp_inputpost['skf_pay_date01'] = $_SESSION['c_skf_pay_date01'];
        $tmp_inputpost['skf_pay_date02'] = $_SESSION['c_skf_pay_date02'];
        $tmp_inputpost['orderid']        = $_SESSION['c_orderid'];

        // 振込日指定を整形
        $tmp_inputpost['skf_pay_date01'] = str_replace("-", "", $tmp_inputpost['skf_pay_date01']);;

        if ($tmp_inputpost['skf_pay_date02'] == '')
        {
            $date = new DateTime();
            $tmp_inputpost['skf_pay_date02'] = $date->format('Ym');
        } else {
            $tmp_inputpost['skf_pay_date02'] = str_replace("-", "", $tmp_inputpost['skf_pay_date02']);;
        }

        // 支払紹介料情報リスト＆件数(max1000件)を取得
        $tmp_offset = 0;
        $tmp_per_page = 1000;

        // 支払紹介料情報データの取得
        $this->load->model('Shokai', 'sk', TRUE);
        $query = $this->sk->get_dlcsv_query($tmp_inputpost, $tmp_per_page, $tmp_offset, '0');

        // 作成したヘルパーを読み込む
        $this->load->helper(array('download', 'csvdata'));

        // ヘルパーに追加した関数を呼び出し、CSVデータ取得
        $get_dl_csv = csv_from_result($query);

        $file_name = 'dlcsv_shokaifeelist_' . date('YmdHis') . '.csv';
        force_download($file_name, $get_dl_csv);

    }

    // Pagination 設定
    private function _get_Pagination($countall, $tmp_per_page)
    {

        $config['base_url']       = base_url() . '/shokaifeelist/search/';      // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
        $config['per_page']       = $tmp_per_page;                              // 1ページ当たりの表示件数。
        $config['total_rows']     = $countall;                                  // 総件数。where指定するか？
        //$config['uri_segment']    = 4;                                        // オフセット値がURIパスの何セグメント目とするか設定
        $config['num_links']      = 5;                                          // 現在のページ番号の左右にいくつのページ番号リンクを生成するか設定
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
        $opt_skf_status = $this->config->item('SHOKAI_SKF_STATUS');

        $this->smarty->assign('options_skf_status', $opt_skf_status);

    }

    // 検索項目 初期値セット
    private function _search_set()
    {

        // ID 並び替え選択項目セット
        $opt_orderid = array (
                                ''     => '-- 選択してください --',
                                'DESC' => '降順',
                                'ASC'  => '昇順',
        );

        $this->smarty->assign('options_orderid', $opt_orderid);

    }

    // 支払サイトセット
    private function _pay_item_set($skf_payment)
    {

        // ステータス 選択項目セット
        $this->config->load('config_comm');
        $opt_skf_payment = $this->config->item('SHOKAI_SK_PAYMENY');

         $this->smarty->assign('options_skf_payment', $opt_skf_payment[$skf_payment]);

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
                array(
                        'field'   => 'skf_remark',
                        'label'   => '支払通知書：備考',
                        'rules'   => 'trim|max_length[31]'
                ),
                array(
                        'field'   => 'skf_memo',
                        'label'   => 'メモ',
                        'rules'   => 'trim|max_length[1000]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック
    private function _set_validation01()
    {

        $rule_set = array(
                array(
                        'field'   => 'skd_pay_fix',
                        'label'   => '固定金額',
                        'rules'   => 'trim|max_length[10]|is_numeric'
                ),
                array(
                        'field'   => 'skd_pay_rate',
                        'label'   => '料率',
                        'rules'   => 'trim|decimal|max_length[5]'
                ),
                array(
                        'field'   => 'skf_remark',
                        'label'   => '支払通知書：備考',
                        'rules'   => 'trim|max_length[31]'
                ),
                array(
                        'field'   => 'skf_memo',
                        'label'   => 'メモ',
                        'rules'   => 'trim|max_length[1000]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック : クライアント更新
    private function _set_validation02()
    {
        $rule_set = array(
                array(
                        'field'   => 'skf_pay_no',
                        'label'   => '支払通知書NO',
                        'rules'   => 'trim|max_length[20]'
                ),
                array(
                        'field'   => 'skf_sk_company',
                        'label'   => '支払先会社名',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'skf_pay_date01',
                        'label'   => '開始月',
                        'rules'   => 'trim|regex_match[/^\d{4}\-|\/\d{1,2}+$/]|max_length[7]'
                ),
                array(
                        'field'   => 'skf_pay_date02',
                        'label'   => '終了月',
                        'rules'   => 'trim|regex_match[/^\d{4}\-|\/\d{1,2}+$/]|max_length[7]'
                ),
                array(
                        'field'   => 'orderid',
                        'label'   => 'ID並び替え',
                        'rules'   => 'trim|max_length[4]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}

