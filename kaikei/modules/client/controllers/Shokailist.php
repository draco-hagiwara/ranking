<?php

class Shokailist extends MY_Controller
{

    /*
     *  支払先情報処理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('mess', FALSE);

    }

    // 支払先情報検索一覧TOP
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
            $tmp_inputpost = array(
                                'sk_status'  => '',
                                'sk_company' => '',
                                'orderid'    => '',
            );

            // セッションをフラッシュデータとして保存
            $data = array(
                            'c_sk_company' => "",
                            'c_sk_status'  => "",
                            'c_orderid'    => "",
            );
            $this->session->set_userdata($data);
        }

        // 支払情報の取得
        $this->load->model('Shokai', 'sk', TRUE);
        list($shokai_list, $shokai_countall) = $this->sk->get_shokailist($tmp_inputpost, $tmp_per_page, $tmp_offset);

        $this->smarty->assign('list', $shokai_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($shokai_countall, $tmp_per_page);

        // 初期値セット
        $this->_item_set();
        $this->_search_set();

        // 担当営業セット
        $this->_sales_item_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall',       $shokai_countall);

        $this->smarty->assign('seach_company',  $tmp_inputpost['sk_company']);
        $this->smarty->assign('seach_status',   $tmp_inputpost['sk_status']);
        $this->smarty->assign('seach_orderid',  $tmp_inputpost['orderid']);

        $this->view('shokailist/index.tpl');

    }

    // 一覧表示
    public function search()
    {

        // 検索項目の保存が上手くいかない。応急的に対応！
        if ($this->input->post('submit') == '_submit')
        {
            // セッションをフラッシュデータとして保存
            $data = array(
                            'c_sk_company' => $this->input->post('sk_company'),
                            'c_sk_status'  => $this->input->post('sk_status'),
                            'c_orderid'    => $this->input->post('orderid'),
            );
            $this->session->set_userdata($data);

            $tmp_inputpost = $this->input->post();
            unset($tmp_inputpost["submit"]);

        } else {
            // セッションからフラッシュデータ読み込み
            $tmp_inputpost['sk_company'] = $_SESSION['c_sk_company'];
            $tmp_inputpost['sk_status']  = $_SESSION['c_sk_status'];
            $tmp_inputpost['orderid']    = $_SESSION['c_orderid'];
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
        $this->load->model('Shokai', 'sk', TRUE);
        list($shokai_list, $shokai_countall) = $this->sk->get_shokailist($tmp_inputpost, $tmp_per_page, $tmp_offset);

        $this->smarty->assign('list', $shokai_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($shokai_countall, $tmp_per_page);

        // 初期値セット
        $this->_item_set();
        $this->_search_set();

        // 担当営業セット
        $this->_sales_item_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall',       $shokai_countall);

        $this->smarty->assign('seach_company',  $tmp_inputpost['sk_company']);
        $this->smarty->assign('seach_status',   $tmp_inputpost['sk_status']);
        $this->smarty->assign('seach_orderid',  $tmp_inputpost['orderid']);

        $this->view('shokailist/index.tpl');

    }

    // 支払先情報編集
    public function detail()
    {

        // 更新対象アカウントのデータ取得
        $input_post = $this->input->post();

        $this->load->model('Shokai', 'sk', TRUE);
        $sk_data      = $this->sk->get_sk_seq($input_post['chg_seq']);
        $sk_comp_data = $this->sk->get_company_sk_seq($input_post['chg_seq']);

        // バリデーション設定
        $this->_set_validation02();

        // 初期値セット
        $this->_item_set();

        // 担当営業セット
        $this->_sales_item_set();

        // 売上先 会社名セット
        $this->_customer_set();

        $this->smarty->assign('info',      $sk_data[0]);
        $this->smarty->assign('info_comp', $sk_comp_data);

        $this->view('shokailist/detail.tpl');

    }

    // 支払先情報チェック
    public function detailchk()
    {

        $input_post = $this->input->post();

        // バリデーション・チェック
        $this->_set_validation02();
        if ($this->form_validation->run() == TRUE)
        {

            $this->load->model('Shokai', 'sk', TRUE);

            // 不要パラメータ削除
            unset($input_post["submit"]) ;

            // 入力値を振り分け
            foreach ($input_post as $key => $value)
            {
                if(strpos($key, 'sk_') !== FALSE)
                {
                    $_tmp_set_shokai[$key] = $value;
                } else {
                    $_tmp_set_company = $value;
                }
            }

            // トランザクション・START
            $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
            $this->db->trans_start();                                               // trans_begin

            // 「支払先情報」DB書き込み
            $_row_id = $this->sk->update_shokai($_tmp_set_shokai);

            // 「支払先(売上先会社)情報」DBには、一度該当レコードを削除してから再度書き込み
            $res = $this->sk->delete_shokai_company($_tmp_set_shokai['sk_seq']);
            if ($res)
            {
                $this->load->model('Customer', 'cm', TRUE);

                foreach($_tmp_set_company as $key => $value)
                {

                    // 売上先会社名を取得
                    $get_cm_data = $this->cm->get_cm_seq($value['skc_cm_seq']);
                    $value['skc_cm_company'] = $get_cm_data[0]['cm_company'];

                    $value['skc_sk_seq'] = $_tmp_set_shokai['sk_seq'];
                    $this->sk->insert_shokai_company($value);
                }
            }

            // トランザクション・COMMIT
            $this->db->trans_complete();                                            // trans_rollback & trans_commit
            if ($this->db->trans_status() === FALSE)
            {
                log_message('error', 'CLIENT::[shokailist -> detailchk()]：支払先情報更新処理 トランザクションエラー');
            }

            redirect('/shokailist/');
        } else {

            $sk_comp_data = $input_post["group-cm"];
            $this->smarty->assign('info_comp', $sk_comp_data);

            $this->smarty->assign('mess',  "<font color=red>項目に入力エラーが発生しました。</font>");
        }

        // 初期値セット
        $this->_item_set();

        // 担当営業セット
        $this->_sales_item_set();

        // 売上先 会社名セット
        $this->_customer_set();

        $this->smarty->assign('info', $input_post);
        $this->view('shokailist/detail.tpl');

    }

    // 支払先情報 新規登録
    public function add()
    {

        // バリデーション・チェック
        $this->_set_validation03();

        // 初期値セット
        $this->_item_set();

        // 担当営業セット
        $this->_sales_item_set();

        // 売上先 会社名セット
        $this->_customer_set();

        $this->smarty->assign('tmp_pref', NULL);
        $this->smarty->assign('tmp_memo', NULL);

        $this->view('shokailist/add.tpl');

    }

    // 支払先情報 内容チェック
    public function addchk()
    {

        $input_post = $this->input->post();

        // 初期値セット
        $this->_item_set();

        // 担当営業セット
        $this->_sales_item_set();

        // 売上先 会社名セット
        $this->_customer_set();

        // バリデーション・チェック
        $this->_set_validation03();
        if ($this->form_validation->run() == FALSE)
        {
            $this->smarty->assign('tmp_pref',     $input_post['sk_pref']);
            $this->smarty->assign('tmp_memo',     $input_post['sk_memo']);

            if ($input_post['sk_pref'] != "")
            {
                $this->smarty->assign('tmp_pref', $input_post['sk_pref']);              // 都道府県を保持
            }
            if ($input_post['sk_memo'] != "")
            {
                $this->smarty->assign('tmp_memo', $input_post['sk_memo']);              // 備考を保持
            }

            $this->smarty->assign('mess',  "<font color=red>項目に入力エラーが発生しました。</font>");

        } else {

            $this->load->model('Shokai',   'sk', TRUE);
            $this->load->model('Customer', 'cm', TRUE);

            // 不要パラメータ削除
            unset($input_post["_submit"]) ;
            //unset($input_post["group-cm"]) ;                                          // これだとunsetできない！

            // 入力値を振り分け
            foreach ($input_post as $key => $value)
            {
                if(strpos($key, 'sk_') !== FALSE)
                {
                    $_tmp_set_shokai[$key] = $value;
                } else {
                    $_tmp_set_company = $value;
                }
            }

            // トランザクション・START
            $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
            $this->db->trans_start();                                               // trans_begin

                // 「支払先情報」DB書き込み
                $_row_id = $this->sk->insert_shokai($_tmp_set_shokai);

                // 「支払先(売上先会社)情報」DB書き込み
                foreach($_tmp_set_company as $key => $value)
                {
                    $value['skc_sk_seq'] = $_row_id;

                    // 売上先会社名を取得
                    $get_cm_data = $this->cm->get_cm_seq($value['skc_cm_seq']);
                    $value['skc_cm_company'] = $get_cm_data[0]['cm_company'];

                    $this->sk->insert_shokai_company($value);
                }

            // トランザクション・COMMIT
            $this->db->trans_complete();                                            // trans_rollback & trans_commit
            if ($this->db->trans_status() === FALSE)
            {
                log_message('error', 'CLIENT::[shokailist -> addchk()]：支払先情報新規登録処理 トランザクションエラー');
            }

            redirect('/shokailist/');
        }

        $this->smarty->assign('tmp_pref', $input_post['sk_pref']);
        $this->smarty->assign('tmp_memo', $input_post['sk_memo']);

        $this->view('shokailist/add.tpl');

    }

    // Pagination 設定
    private function _get_Pagination($countall, $tmp_per_page)
    {

        $config['base_url']       = base_url() . '/shokailist/search/';         // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
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

        // ステータスのセット
        $this->config->load('config_status');
        $opt_sk_status = $this->config->item('SHOKAI_SK_STATUS');

        // 口座種別のセット
        $this->config->load('config_comm');
        $opt_sk_kind = $this->config->item('CUSTOMER_CM_KIND');

        // 回収サイトのセット
        $opt_sk_payment = $this->config->item('SHOKAI_SK_PAYMENY');

        // 支払先情報ID 並び替え選択項目セット
        $arropt_id = array (
                ''     => '-- 選択してください --',
                'DESC' => '降順',
                'ASC'  => '昇順',
        );

        $this->smarty->assign('options_sk_status',  $opt_sk_status);
        $this->smarty->assign('options_sk_kind',    $opt_sk_kind);
        $this->smarty->assign('options_sk_payment', $opt_sk_payment);
        $this->smarty->assign('options_orderid',    $arropt_id);

    }

    // 検索項目 初期値セット
    private function _search_set()
    {

        // ステータス 選択項目セット
        $this->config->load('config_status');
        $opt_sk_status = $this->config->item('SHOKAI_SK_STATUS');

        // 支払情報ID 並び替え選択項目セット
        $arropt_id = array (
                            ''     => '-- 選択してください --',
                            'DESC' => '降順',
                            'ASC'  => '昇順',
        );

        $this->smarty->assign('options_sk_status', $opt_sk_status);
        $this->smarty->assign('options_orderid',   $arropt_id);

    }

    // 担当営業セット
    private function _sales_item_set()
    {

        // 請求書発行対象企業
        $this->config->load('config_comm');
        $opt_cl_seq = $this->config->item('PROJECT_CL_SEQ');

        $this->load->model('Account', 'ac', TRUE);
        $salesman_list = $this->ac->get_salesman($opt_cl_seq, 'seorank');       // 「ラベンダー」固定 : ac_cl_seq = 2

        foreach ($salesman_list as $key => $val)
        {
            $opt_sk_salesman[$val['ac_seq']] = $val['ac_name01'] . ' ' . $val['ac_name02'];
        }

        $this->smarty->assign('options_sk_salesman', $opt_sk_salesman);

    }

    // 売上先 会社名セット
    private function _customer_set()
    {

        $this->load->model('Customer', 'cm', TRUE);
        $comp_list = $this->cm->get_shokai_company();

        //$opt_cm_company = array('' => '-- 選択してください --');
        foreach ($comp_list as $key => $val)
        {
            $opt_cm_company[$val['cm_seq']] = $val['cm_company'] . ' (' . $val['cm_seq'] . ')';
        }

        $this->smarty->assign('options_cm_company', $opt_cm_company);

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
                array(
                        'field'   => 'sk_company',
                        'label'   => '会社名',
                        'rules'   => 'trim|max_length[50]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック : 支払先情報更新
    private function _set_validation02()
    {
        $rule_set = array(
                array(
                        'field'   => 'sk_status',
                        'label'   => 'ステータス選択',
                        'rules'   => 'trim|required|max_length[1]|is_numeric'
                ),
                array(
                        'field'   => 'sk_salesman',
                        'label'   => '担当営業',
                        'rules'   => 'trim|required|max_length[2]|is_numeric'
                ),
                //array(
                //        'field'   => 'sk_paycal_fix',
                //        'label'   => '固定金額',
                //        'rules'   => 'trim|max_length[10]|is_numeric'
                //),
                //array(
                //        'field'   => 'sk_paycal_rate',
                //        'label'   => '料率',
                //        'rules'   => 'trim|decimal|max_length[4]'
                //),
                array(
                        'field'   => 'sk_payment',
                        'label'   => '支払サイト',
                        'rules'   => 'trim|required|max_length[1]|is_numeric'
                ),
                array(
                        'field'   => 'sk_company',
                        'label'   => '会社名',
                        'rules'   => 'trim|required|max_length[50]'
                ),
                array(
                        'field'   => 'sk_company_kana',
                        'label'   => '会社名カナ',
                        'rules'   => 'trim|required|max_length[4]|katakana'
                ),
                array(
                        'field'   => 'sk_zip01',
                        'label'   => '郵便番号（3ケタ）',
                        'rules'   => 'trim|exact_length[3]|is_numeric'
                ),
                array(
                        'field'   => 'sk_zip02',
                        'label'   => '郵便番号（4ケタ）',
                        'rules'   => 'trim|exact_length[4]|is_numeric'
                ),
                array(
                        'field'   => 'sk_pref',
                        'label'   => '都道府県',
                        'rules'   => 'trim|max_length[4]'
                ),
                array(
                        'field'   => 'sk_addr01',
                        'label'   => '市区町村',
                        'rules'   => 'trim|max_length[100]'
                ),
                array(
                        'field'   => 'sk_addr02',
                        'label'   => '町名・番地',
                        'rules'   => 'trim|max_length[100]'
                ),
                array(
                        'field'   => 'sk_buil',
                        'label'   => 'ビル・マンション名など',
                        'rules'   => 'trim|max_length[100]'
                ),
                array(
                        'field'   => 'sk_president01',
                        'label'   => '代表者姓',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sk_president02',
                        'label'   => '代表者名',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sk_tel01',
                        'label'   => '代表電話番号',
                        'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
                ),
                array(
                        'field'   => 'sk_mail',
                        'label'   => 'メールアドレス',
                        'rules'   => 'trim|max_length[100]|valid_email'
                ),
                array(
                        'field'   => 'sk_mailsub',
                        'label'   => 'メールアドレス(サブ)',
                        'rules'   => 'trim|max_length[100]|valid_email'
                ),
                array(
                        'field'   => 'sk_department',
                        'label'   => '担当所属部署／役職',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sk_person01',
                        'label'   => '担当者姓',
                        'rules'   => 'trim|max_length[20]'
                ),
                array(
                        'field'   => 'sk_person02',
                        'label'   => '担当者名',
                        'rules'   => 'trim|max_length[20]'
                ),
                array(
                        'field'   => 'sk_tel02',
                        'label'   => '担当者電話番号',
                        'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
                ),
                array(
                        'field'   => 'sk_mobile',
                        'label'   => '担当者携帯番号',
                        'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
                ),
                array(
                        'field'   => 'sk_fax',
                        'label'   => 'FAX番号',
                        'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
                ),
                array(
                        'field'   => 'sk_bank_cd',
                        'label'   => '銀行CD',
                        'rules'   => 'trim|max_length[4]|is_numeric'
                ),
                array(
                        'field'   => 'sk_bank_nm',
                        'label'   => '銀行名',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sk_branch_cd',
                        'label'   => '支店CD',
                        'rules'   => 'trim|max_length[3]|is_numeric'
                ),
                array(
                        'field'   => 'sk_branch_nm',
                        'label'   => '支店名',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sk_kind',
                        'label'   => '口座種別(普通/当座)',
                        'rules'   => 'trim|max_length[1]'
                ),
                array(
                        'field'   => 'sk_account_no',
                        'label'   => '口座番号',
                        'rules'   => 'trim|max_length[10]|is_numeric'
                ),
                array(
                        'field'   => 'sk_account_nm',
                        'label'   => '口座名義',
                        'rules'   => 'trim|max_length[48]|single_eisukana'
                ),
                array(
                        'field'   => 'sk_memo',
                        'label'   => '備考',
                        'rules'   => 'trim|max_length[1000]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

    // フォーム・バリデーションチェック : 支払先情報追加
    private function _set_validation03()
    {
        $rule_set = array(
                array(
                        'field'   => 'sk_status',
                        'label'   => 'ステータス選択',
                        'rules'   => 'trim|required|max_length[1]|is_numeric'
                ),
                array(
                        'field'   => 'sk_salesman',
                        'label'   => '担当営業',
                        'rules'   => 'trim|required|max_length[2]|is_numeric'
                ),
                //array(
                //        'field'   => 'sk_paycal_fix',
                //        'label'   => '固定金額',
                //        'rules'   => 'trim|max_length[10]|is_numeric'
                //),
                //array(
                //        'field'   => 'sk_paycal_rate',
                //        'label'   => '料率',
                //        'rules'   => 'trim|decimal|max_length[4]'
                //),
                //array(
                //      'field'   => 'skc_cm_seq',
                //      'label'   => '売上先会社',
                //      'rules'   => 'trim|required|max_length[3]|is_numeric'
                //),
                array(
                        'field'   => 'sk_company',
                        'label'   => '会社名',
                        'rules'   => 'trim|required|max_length[50]'
                ),
                array(
                        'field'   => 'sk_company_kana',
                        'label'   => '会社名カナ',
                        'rules'   => 'trim|required|max_length[4]|katakana'
                ),
                array(
                        'field'   => 'sk_zip01',
                        'label'   => '郵便番号（3ケタ）',
                        'rules'   => 'trim|exact_length[3]|is_numeric'
                ),
                array(
                        'field'   => 'sk_zip02',
                        'label'   => '郵便番号（4ケタ）',
                        'rules'   => 'trim|exact_length[4]|is_numeric'
                ),
                array(
                        'field'   => 'sk_pref',
                        'label'   => '都道府県',
                        'rules'   => 'trim|max_length[4]'
                ),
                array(
                        'field'   => 'sk_addr01',
                        'label'   => '市区町村',
                        'rules'   => 'trim|max_length[100]'
                ),
                array(
                        'field'   => 'sk_addr02',
                        'label'   => '町名・番地',
                        'rules'   => 'trim|max_length[100]'
                ),
                array(
                        'field'   => 'sk_buil',
                        'label'   => 'ビル・マンション名など',
                        'rules'   => 'trim|max_length[100]'
                ),
                array(
                        'field'   => 'sk_president01',
                        'label'   => '代表者姓',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sk_president02',
                        'label'   => '代表者名',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sk_tel01',
                        'label'   => '代表電話番号',
                        'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
                ),
                array(
                        'field'   => 'sk_mail',
                        'label'   => 'メールアドレス',
                        'rules'   => 'trim|max_length[100]|valid_email'
                ),
                array(
                        'field'   => 'sk_mailsub',
                        'label'   => 'メールアドレス(サブ)',
                        'rules'   => 'trim|max_length[100]|valid_email'
                ),
                array(
                        'field'   => 'sk_department',
                        'label'   => '所属部署',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sk_person01',
                        'label'   => '担当者姓',
                        'rules'   => 'trim|max_length[20]'
                ),
                array(
                        'field'   => 'sk_person02',
                        'label'   => '担当者名',
                        'rules'   => 'trim|max_length[20]'
                ),
                array(
                        'field'   => 'sk_tel02',
                        'label'   => '担当者電話番号',
                        'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
                ),
                array(
                        'field'   => 'sk_mobile',
                        'label'   => '担当者携帯番号',
                        'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
                ),
                array(
                        'field'   => 'sk_fax',
                        'label'   => 'FAX番号',
                        'rules'   => 'trim|regex_match[/^[0-9\-]+$/]|max_length[15]'
                ),
                array(
                        'field'   => 'sk_payment',
                        'label'   => '支払サイト',
                        'rules'   => 'trim|required|max_length[1]|is_numeric'
                ),
                array(
                        'field'   => 'sk_bank_cd',
                        'label'   => '銀行CD',
                        'rules'   => 'trim|max_length[4]|is_numeric'
                ),
                array(
                        'field'   => 'sk_bank_nm',
                        'label'   => '銀行名',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sk_branch_cd',
                        'label'   => '支店CD',
                        'rules'   => 'trim|max_length[3]|is_numeric'
                ),
                array(
                        'field'   => 'sk_branch_nm',
                        'label'   => '支店名',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sk_kind',
                        'label'   => '口座種別(普通/当座)',
                        'rules'   => 'trim|max_length[1]'
                ),
                array(
                        'field'   => 'sk_account_no',
                        'label'   => '口座番号',
                        'rules'   => 'trim|max_length[10]|is_numeric'
                ),
                array(
                        'field'   => 'sk_account_nm',
                        'label'   => '口座名義',
                        'rules'   => 'trim|max_length[48]|single_eisukana'
                        //'rules'   => 'trim|required|max_length[48]|single_katakana'
                ),
                array(
                        'field'   => 'sk_memo',
                        'label'   => '備考',
                        'rules'   => 'trim|max_length[1000]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}

