<?php

class Saleslist extends MY_Controller
{

    /*
     *  売上データ
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('mess', FALSE);

    }

    // 売上データ一覧TOP
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

            // 発行年月 <- 初期値(当月1日を設定)
            $date = new DateTime();
            $_date_ymd = $date->modify('first day of this months')->format('Y-m');
//             $_date_ymd = $date->modify('first day of this months')->format('Y-m-d');

            $tmp_inputpost = array(
                                'sa_slip_no'      => '',
                                'sa_keyword'      => '',
                                'sa_company'      => '',
                                'sa_salesman'     => '',
                                'sa_sales_date01' => $_date_ymd,
                                'sa_sales_date02' => '',
                                'sa_collect'      => '',
                                'sa_accounting'   => '',
                                'displine'        => 1,
                                'orderid'         => '',
            );

            // セッションをフラッシュデータとして保存
            $data = array(
                                'c_sa_slip_no'      => '',
                                'c_sa_keyword'       => '',
                                'c_sa_company'      => '',
                                'c_sa_salesman'     => '',
                                'c_sa_sales_date01' => $_date_ymd,
                                'c_sa_sales_date02' => '',
                                'c_sa_collect'      => '',
                                'c_sa_accounting'   => '',
                                'c_displine'        => 1,
                                'c_orderid'         => '',
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

        $this->smarty->assign('seach_sa_slip_no',      $tmp_inputpost['sa_slip_no']);
        $this->smarty->assign('seach_sa_keyword',      $tmp_inputpost['sa_keyword']);
        $this->smarty->assign('seach_sa_company',      $tmp_inputpost['sa_company']);
        $this->smarty->assign('seach_sa_salesman',     $tmp_inputpost['sa_salesman']);
        $this->smarty->assign('seach_sa_sales_date01', $tmp_inputpost['sa_sales_date01']);
        $this->smarty->assign('seach_sa_sales_date02', $tmp_inputpost['sa_sales_date02']);
        $this->smarty->assign('seach_sa_collect',      $tmp_inputpost['sa_collect']);
        $this->smarty->assign('seach_sa_accounting',   $tmp_inputpost['sa_accounting']);
        $this->smarty->assign('seach_displine',        $tmp_inputpost['displine']);
        $this->smarty->assign('seach_orderid',         $tmp_inputpost['orderid']);

        $this->view('saleslist/index.tpl');

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

                $input_post['sa_sales_date01'] = $tmp_inputpost['sa_sales_date01'];
                $input_post['sa_sales_date02'] = $tmp_inputpost['sa_sales_date02'];

                $this->smarty->assign('list', NULL);
                $this->smarty->assign('set_pagination', $set_pagination['page_link']);
                $this->smarty->assign('countall', 0);

            } else {

                $tmp_inputpost = $this->input->post();

                // セッションをフラッシュデータとして保存
                $data = array(
                                'c_sa_slip_no'      => $tmp_inputpost['sa_slip_no'],
                                'c_sa_keyword'      => $tmp_inputpost['sa_keyword'],
                                'c_sa_company'      => $tmp_inputpost['sa_company'],
                                'c_sa_salesman'     => $tmp_inputpost['sa_salesman'],
                                'c_sa_sales_date01' => $tmp_inputpost['sa_sales_date01'],
                                'c_sa_sales_date02' => $tmp_inputpost['sa_sales_date02'],
                                'c_sa_collect'      => $tmp_inputpost['sa_collect'],
                                'c_sa_accounting'   => $tmp_inputpost['sa_accounting'],
                                'c_displine'        => $tmp_inputpost['displine'],
                                'c_orderid'         => $tmp_inputpost['orderid'],
                );
                $this->session->set_userdata($data);

                $input_post['sa_sales_date01'] = $tmp_inputpost['sa_sales_date01'];
                $input_post['sa_sales_date02'] = $tmp_inputpost['sa_sales_date02'];

                unset($tmp_inputpost["submit"]);

                // 売上日指定を整形
                if ($tmp_inputpost['sa_sales_date02'] == '')
                {
                    $date = new DateTime();
                    $tmp_inputpost['sa_sales_date02'] = $date->format('Ym');
                }
//              $date = new DateTime($tmp_inputpost['sa_sales_date01']);
//              $tmp_inputpost['sa_sales_date01'] = $date->format('Y-m-d 00:00:00');

//              if ($tmp_inputpost['sa_sales_date02'] == '')
//              {
//                  $date = new DateTime();
//                  $tmp_inputpost['sa_sales_date02'] = $date->format('Y-m-d 23:59:59');
//              } else {
//                  $date = new DateTime($tmp_inputpost['sa_sales_date02']);
//                  $tmp_inputpost['sa_sales_date02'] = $date->modify('last day of this months')->format('Y-m-d 23:59:59');
//              }
            }

        } else {

            // バリデーション・チェック
            $this->_set_validation();                                               // バリデーション設定
//          $this->form_validation->run();

            // セッションからフラッシュデータ読み込み
            $tmp_inputpost['sa_slip_no']      = $_SESSION['c_sa_slip_no'];
            $tmp_inputpost['sa_keyword']      = $_SESSION['c_sa_keyword'];
            $tmp_inputpost['sa_company']      = $_SESSION['c_sa_company'];
            $tmp_inputpost['sa_salesman']     = $_SESSION['c_sa_salesman'];
            $tmp_inputpost['sa_sales_date01'] = $_SESSION['c_sa_sales_date01'];
            $tmp_inputpost['sa_sales_date02'] = $_SESSION['c_sa_sales_date02'];
            $tmp_inputpost['sa_collect']      = $_SESSION['c_sa_collect'];
            $tmp_inputpost['sa_accounting']   = $_SESSION['c_sa_accounting'];
            $tmp_inputpost['displine']        = $_SESSION['c_displine'];
            $tmp_inputpost['orderid']         = $_SESSION['c_orderid'];

            $input_post['sa_sales_date01'] = $_SESSION['c_sa_sales_date01'];
            $input_post['sa_sales_date02'] = $_SESSION['c_sa_sales_date02'];

            // 売上日指定を整形
            if ($tmp_inputpost['sa_sales_date02'] == '')
            {
                $date = new DateTime();
                $tmp_inputpost['sa_sales_date02'] = $date->format('Ym');
            }
//          $date = new DateTime($tmp_inputpost['sa_sales_date01']);
//          $tmp_inputpost['sa_sales_date01'] = $date->format('Y-m-d 00:00:00');

//          if ($tmp_inputpost['sa_sales_date02'] == '')
//          {
//              $date = new DateTime();
//              $tmp_inputpost['sa_sales_date02'] = $date->format('Y-m-d 23:59:59');
//          } else {
//              $date = new DateTime($tmp_inputpost['sa_sales_date02']);
//              $tmp_inputpost['sa_sales_date02'] = $date->modify('last day of this months')->format('Y-m-d 23:59:59');
//          }

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

            // 売上データの取得
            $this->load->model('Sales', 'sa', TRUE);
            list($sales_list, $sales_countall) = $this->sa->get_saleslist($tmp_inputpost, $tmp_per_page, $tmp_offset);

            $this->smarty->assign('list', $sales_list);

            // Pagination 設定
            $set_pagination = $this->_get_Pagination($sales_countall, $tmp_per_page);

            $this->smarty->assign('set_pagination', $set_pagination['page_link']);
            $this->smarty->assign('countall', $sales_countall);
        }

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('seach_sa_slip_no',      $tmp_inputpost['sa_slip_no']);
        $this->smarty->assign('seach_sa_keyword',      $tmp_inputpost['sa_keyword']);
        $this->smarty->assign('seach_sa_company',      $tmp_inputpost['sa_company']);
        $this->smarty->assign('seach_sa_salesman',     $tmp_inputpost['sa_salesman']);
        $this->smarty->assign('seach_sa_sales_date01', $input_post['sa_sales_date01']);
        $this->smarty->assign('seach_sa_sales_date02', $input_post['sa_sales_date02']);
        $this->smarty->assign('seach_sa_collect',      $tmp_inputpost['sa_collect']);
        $this->smarty->assign('seach_sa_accounting',   $tmp_inputpost['sa_accounting']);
        $this->smarty->assign('seach_displine',        $tmp_inputpost['displine']);
        $this->smarty->assign('seach_orderid',         $tmp_inputpost['orderid']);

        $this->view('saleslist/index.tpl');

    }

    // CSV ダウンロード
    public function csvdown()
    {

        // セッションからフラッシュデータ読み込み
        $tmp_inputpost['sa_slip_no']      = $_SESSION['c_sa_slip_no'];
        $tmp_inputpost['sa_keyword']      = $_SESSION['c_sa_keyword'];
        $tmp_inputpost['sa_company']      = $_SESSION['c_sa_company'];
        $tmp_inputpost['sa_salesman']     = $_SESSION['c_sa_salesman'];
        $tmp_inputpost['sa_sales_date01'] = $_SESSION['c_sa_sales_date01'];
        $tmp_inputpost['sa_sales_date02'] = $_SESSION['c_sa_sales_date02'];
        $tmp_inputpost['sa_collect']      = $_SESSION['c_sa_collect'];
        $tmp_inputpost['sa_accounting']   = $_SESSION['c_sa_accounting'];
        $tmp_inputpost['displine']        = $_SESSION['c_displine'];
        $tmp_inputpost['orderid']         = $_SESSION['c_orderid'];

        // 売上日指定を整形
        $date = new DateTime($tmp_inputpost['sa_sales_date01']);
        $tmp_inputpost['sa_sales_date01'] = $date->format('Y-m-d 00:00:00');

        if ($tmp_inputpost['sa_sales_date02'] == '')
        {
            $date = new DateTime();
            $tmp_inputpost['sa_sales_date02'] = $date->format('Y-m-d 23:59:59');
        } else {
            $date = new DateTime($tmp_inputpost['sa_sales_date02']);
            $tmp_inputpost['sa_sales_date02'] = $date->format('Y-m-d 23:59:59');
        }

        // 請求リスト＆件数(max1000件)を取得
        $tmp_offset = 0;
        $tmp_per_page = 1000;

        // 売上データの取得
        $this->load->model('Sales', 'sa', TRUE);
        $query = $this->sa->get_dlcsv_query($tmp_inputpost, $tmp_per_page, $tmp_offset, '0');

        // 作成したヘルパーを読み込む
        $this->load->helper(array('download', 'csvdata'));

        // ヘルパーに追加した関数を呼び出し、CSVデータ取得
        $get_dl_csv = csv_from_result($query);

        $file_name = 'dlcsv_saleslist_' . date('YmdHis') . '.csv';
        force_download($file_name, $get_dl_csv);

    }

    // Pagination 設定
    private function _get_Pagination($countall, $tmp_per_page)
    {

        $config['base_url']       = base_url() . '/saleslist/search/';          // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
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

//     // 初期値セット
//     private function _item_set()
//     {

//         // ステータス 選択項目セット
//      $this->config->load('config_status');
//      $opt_iv_status = $this->config->item('PROJECT_IV_STATUS');

//      // 課金方式
//      $this->config->load('config_comm');
//      $opt_iv_accounting = $this->config->item('INVOICE_ACCOUNTING');

//      // 回収サイトのセット
//      $opt_iv_collect = $this->config->item('CUSTOMER_CM_COLLECT');

//      // 口座種別のセット
//      $opt_iv_kind = $this->config->item('CUSTOMER_CM_KIND');

//      $this->smarty->assign('options_iv_status',     $opt_iv_status);
//      $this->smarty->assign('options_iv_accounting', $opt_iv_accounting);
//      $this->smarty->assign('options_iv_collect',    $opt_iv_collect);
//      $this->smarty->assign('options_iv_kind',       $opt_iv_kind);

//     }

    // 検索項目 初期値セット
    private function _search_set()
    {

        // 回収サイト 選択項目セット
        $this->config->load('config_comm');
        $opt_sa_collect = $this->config->item('CUSTOMER_CM_COLLECT');

        // 課金方式 選択項目セット
        $this->config->load('config_comm');
        $opt_sa_accounting = $this->config->item('SALES_ACCOUNTING');

        // 売上データ 表示単位
        $opt_displine = $this->config->item('SALES_SA_DISPLINE');

        // ID 並び替え選択項目セット
        $opt_orderid = array (
                ''     => '-- 選択してください --',
                'DESC' => '降順',
                'ASC'  => '昇順',
        );

        $this->smarty->assign('options_sa_collect',    $opt_sa_collect);
        $this->smarty->assign('options_sa_accounting', $opt_sa_accounting);
        $this->smarty->assign('options_displine',      $opt_displine);
        $this->smarty->assign('options_orderid',       $opt_orderid);

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
                        'field'   => 'sa_slip_no',
                        'label'   => '請求書NO',
                        'rules'   => 'trim|max_length[20]'
                ),
                array(
                        'field'   => 'sa_keyword',
                        'label'   => 'KW',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sa_company',
                        'label'   => '会社名',
                        'rules'   => 'trim|max_length[50]'
                ),
                array(
                        'field'   => 'sa_salesman',
                        'label'   => '担当営業',
                        'rules'   => 'trim|max_length[40]'
                ),
                array(
                        'field'   => 'sa_sales_date01',
                        'label'   => '開始月',
                        'rules'   => 'trim|regex_match[/^\d{4}\-|\/\d{1,2}+$/]|max_length[7]'
                ),
                array(
                        'field'   => 'sa_sales_date02',
                        'label'   => '終了月',
                        'rules'   => 'trim|regex_match[/^\d{4}\-|\/\d{1,2}+$/]|max_length[7]'
                ),
                array(
                        'field'   => 'sa_collect',
                        'label'   => '回収サイト',
                        'rules'   => 'trim|max_length[1]'
                ),
                array(
                        'field'   => 'sa_accounting',
                        'label'   => '課金方式',
                        'rules'   => 'trim|max_length[2]'
                ),
                array(
                        'field'   => 'displine',
                        'label'   => '表示単位',
                        'rules'   => 'trim|max_length[1]'
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

