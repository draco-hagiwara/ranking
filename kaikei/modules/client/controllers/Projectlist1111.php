<?php

class Projectlist extends MY_Controller
{

    /*
     *  受注案件情報
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('err_date', FALSE);
        $this->smarty->assign('mess',     FALSE);

    }

    // 受注案件検索一覧TOP
    public function index()
    {

        // セッションデータをクリア
        $this->load->library('lib_auth');
        $this->lib_auth->delete_session('client');

        // バリデーション・チェック
        $this->_set_validation();                                                       // バリデーション設定
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
                                'pj_seq'            => '',
                                'pj_cm_seq'         => '',
                                'pj_cm_company'     => '',
                                'pj_status'         => '',
                                'pj_invoice_status' => '',
                                'pj_accounting'     => '',
                                'pj_salesman'       => '',
                                'orderid'           => '',
            );

            // セッションをフラッシュデータとして保存
            $data = array(
                                'c_pj_seq'            => '',
                                'c_pj_cm_seq'         => '',
                                'c_pj_cm_company'     => '',
                                'c_pj_status'         => '',
                                'c_pj_invoice_status' => '',
                                'c_pj_accounting'     => '',
                                'c_pj_salesman'       => '',
                                'c_orderid'           => '',
                    );
            $this->session->set_userdata($data);
        }

        // 顧客情報の取得
        $this->load->model('Project', 'pj', TRUE);
        list($project_list, $project_countall) = $this->pj->get_projectlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp'], 'projects');

        $this->smarty->assign('list', $project_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($project_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall',       $project_countall);

        // 契約期間チェック
        $date_now = new DateTime();
        $date_chk_str = $date_now->modify('first day of this months')->format('Y-m-d');         // 当月1日
        $date_chk_end = $date_now->modify('last day of this months')->format('Y-m-d');          // 当月末日
        $this->smarty->assign('chk_str',              $date_chk_str);
        $this->smarty->assign('chk_end',              $date_chk_end);

        $this->smarty->assign('seach_seq',            $tmp_inputpost['pj_seq']);
        $this->smarty->assign('seach_cm_seq',         $tmp_inputpost['pj_cm_seq']);
        $this->smarty->assign('seach_cm_company',     $tmp_inputpost['pj_cm_company']);
        $this->smarty->assign('seach_status',         $tmp_inputpost['pj_status']);
        $this->smarty->assign('seach_invoice_status', $tmp_inputpost['pj_invoice_status']);
        $this->smarty->assign('seach_accounting',     $tmp_inputpost['pj_accounting']);
        $this->smarty->assign('seach_salesman',       $tmp_inputpost['pj_salesman']);
        $this->smarty->assign('seach_orderid',        $tmp_inputpost['orderid']);

        $this->view('projectlist/index.tpl');

    }

    // 一覧表示
    public function search()
    {

        // 検索項目の保存が上手くいかない。応急的に対応！
        if ($this->input->post('submit') == '_submit')
        {
            // セッションをフラッシュデータとして保存
            $data = array(
                            'c_pj_seq'            => $this->input->post('pj_seq'),
                            'c_pj_cm_seq'         => $this->input->post('pj_cm_seq'),
                            'c_pj_cm_company'     => $this->input->post('pj_cm_company'),
                            'c_pj_status'         => $this->input->post('pj_status'),
                            'c_pj_invoice_status' => $this->input->post('pj_invoice_status'),
                            'c_pj_accounting'     => $this->input->post('pj_accounting'),
                            'c_pj_salesman'       => $this->input->post('pj_salesman'),
                            'c_orderid'           => $this->input->post('orderid'),
            );
            $this->session->set_userdata($data);

            $tmp_inputpost = $this->input->post();
            unset($tmp_inputpost["submit"]);

        } else {
            // セッションからフラッシュデータ読み込み
            $tmp_inputpost['pj_seq']            = $_SESSION['c_pj_seq'];
            $tmp_inputpost['pj_cm_seq']         = $_SESSION['c_pj_cm_seq'];
            $tmp_inputpost['pj_cm_company']     = $_SESSION['c_pj_cm_company'];
            $tmp_inputpost['pj_status']         = $_SESSION['c_pj_status'];
            $tmp_inputpost['pj_invoice_status'] = $_SESSION['c_pj_invoice_status'];
            $tmp_inputpost['pj_accounting']     = $_SESSION['c_pj_accounting'];
            $tmp_inputpost['pj_salesman']       = $_SESSION['c_pj_salesman'];
            $tmp_inputpost['orderid']           = $_SESSION['c_orderid'];
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

        // 顧客情報の取得
        $this->load->model('Project', 'pj', TRUE);
        list($project_list, $project_countall) = $this->pj->get_projectlist($tmp_inputpost, $tmp_per_page, $tmp_offset, $_SESSION['c_memGrp'], 'projects');

        $this->smarty->assign('list', $project_list);

        // Pagination 設定
        $set_pagination = $this->_get_Pagination($project_countall, $tmp_per_page);

        // 初期値セット
        $this->_search_set();

        $this->smarty->assign('set_pagination', $set_pagination['page_link']);
        $this->smarty->assign('countall',       $project_countall);

        // 契約期間チェック
        $date_now = new DateTime();
        $date_chk_str = $date_now->modify('first day of this months')->format('Y-m-d');         // 当月1日
        $date_chk_end = $date_now->modify('last day of this months')->format('Y-m-d');          // 当月末日
        $this->smarty->assign('chk_str',              $date_chk_str);
        $this->smarty->assign('chk_end',              $date_chk_end);

        $this->smarty->assign('seach_seq',            $tmp_inputpost['pj_seq']);
        $this->smarty->assign('seach_cm_seq',         $tmp_inputpost['pj_cm_seq']);
        $this->smarty->assign('seach_cm_company',     $tmp_inputpost['pj_cm_company']);
        $this->smarty->assign('seach_status',         $tmp_inputpost['pj_status']);
        $this->smarty->assign('seach_invoice_status', $tmp_inputpost['pj_invoice_status']);
        $this->smarty->assign('seach_accounting',     $tmp_inputpost['pj_accounting']);
        $this->smarty->assign('seach_salesman',       $tmp_inputpost['pj_salesman']);
        $this->smarty->assign('seach_orderid',        $tmp_inputpost['orderid']);

        $this->view('projectlist/index.tpl');

    }

    // 受注案件情報編集
    public function detail()
    {

        // 初期値セット
        $this->_item_set();

        // 担当営業セット
        $this->_sales_item_set();

        // ロケーションセット
        $this->_location_item_set();

        // バリデーション設定
        $this->_set_validation02();

        // 更新対象アカウントのデータ取得
        $input_post = $this->input->post();

        $this->load->model('Project', 'pj', TRUE);
        $tmp_pjid = $input_post['chg_seq'];
        $pj_data = $this->pj->get_pj_seq($tmp_pjid, $_SESSION['c_memGrp'], 'projects');

        $this->load->model('Project_detail', 'pjd', TRUE);
        $pjd_data = $this->pjd->get_pj_seq($tmp_pjid, $_SESSION['c_memGrp'], 'projects');

        if (count($pjd_data) != 0)
        {
            foreach ($pjd_data as $key => $value)
            {
                $_item_num = sprintf("%02d", $key);
                $_item = "pjd_rank_str01" . $_item_num;
                $this->smarty->assign($_item, $value['pjd_rank_str']);
                $_item = "pjd_rank_end01" . $_item_num;
                $this->smarty->assign($_item, $value['pjd_rank_end']);
                $_item = "pjd_billing01" . $_item_num;
                $this->smarty->assign($_item, $value['pjd_billing']);
            }
        } else {
            // 初期化
            for($_num=0; $_num<10; $_num++)
            {
                $_item = "pjd_rank_str01" . sprintf("%02d", $_num);
                $this->smarty->assign($_item, 0);
                $_item = "pjd_rank_end01" . sprintf("%02d", $_num);
                $this->smarty->assign($_item, 0);
                $_item = "pjd_billing01" . sprintf("%02d", $_num);
                $this->smarty->assign($_item, 0);
            }
        }

        $this->smarty->assign('info',          $pj_data[0]);

        $this->smarty->assign('renew',         $pj_data[0]['pj_renew_chk']);
        $this->smarty->assign('engine_g',      substr($pj_data[0]['pj_engine'], 0, 1));
        $this->smarty->assign('engine_y',      substr($pj_data[0]['pj_engine'], 1, 1));
        $this->smarty->assign('init_location', $pj_data[0]['pj_location_id']);

        $this->view('projectlist/detail.tpl');

    }

    // 受注案件情報チェック
    public function detailchk()
    {

        $input_post = $this->input->post();

        // バリデーション・チェック
        $this->_set_validation02();                                                         // 管理者
        if ($this->form_validation->run() == FALSE)
        {
        	$this->smarty->assign('init_location', $input_post['pj_location_id']);
        	$this->smarty->assign('mess', "<font color=red>項目に入力エラーが発生しました。</font>");
        } else {

            // 契約期間の判定
            $date_str = new DateTime($input_post['pj_start_date']);
            $date_end = new DateTime($input_post['pj_end_date']);
            if ($date_str > $date_end)
            {
                $this->smarty->assign('err_date', TRUE);
            } else {

            	$this->load->model('Project', 'pj', TRUE);

                // 受注案件情報をセット
                $_set_pj_data = $input_post;

                /*
                 * 検索エンジンの選択では
                 * 選択されたエンジンの場所にビットを立てる（感じ・・・）
                 *   ・G + Y -> "11"
                 *   ・Gのみ -> "10"
                 *   ・Yのみ -> "01"
                 *   ・なし  -> "00"
                 */
                if (isset($input_post['chkengine']))
                {

                    if (in_array(0, $input_post['chkengine']))
                    {
                        $_set_pj_data['pj_engine'] = "1";
                    } else {
                        $_set_pj_data['pj_engine'] = "0";
                    }

                    if (in_array(1, $input_post['chkengine']))
                    {
                        $_set_pj_data['pj_engine'] = $_set_pj_data['pj_engine'] . "1";
                    } else {
                        $_set_pj_data['pj_engine'] = $_set_pj_data['pj_engine'] . "0";
                    }

                } else {
                    $_set_pj_data['pj_engine'] = "00";
                }

                // ロケーション(Canonical Name)を取得
                $location_name = $this->pj->get_location_id($input_post['pj_location_id'], 'projects');
                $_set_pj_data['pj_location_name'] = $location_name[0]['lo_canonical_name'];







                // trim(全角/半角スペース)
                $_set_pj_data['pj_keyword'] = trim(mb_convert_kana($input_post['pj_keyword'], "s", 'UTF-8'));
                $_set_pj_data['pj_url']     = trim(mb_convert_kana($input_post['pj_url'], "s", 'UTF-8'));

                // 対象URLから、URLマッチタイプをみて比較対象ドメインを取得する
                /*
                 * 後で実行するSEO順位取得で使用
                 */
                switch ($input_post['pj_url_match'])
                {
                	case 0:
                		// ドメイン一致
                		/*
                		 * ホストだけにして完全一致するか比較
                		 */

                		$_set_pj_data['pj_compare_domain'] = preg_replace("/^https?:\/\/(www\.)?|\/(.*)/i", "", $_set_pj_data['pj_url']);

                		break;

                	case 1:
                		// ルートドメイン一致（サブドメイン含む）
                		/*
                		 * rootdomainと完全一致するか否か
                		 */

                		$this->load->library('Lib_rootdomain');
                		$_rootdomain = $this->lib_rootdomain->get_rootdomain($_set_pj_data['pj_url']);

                		$_set_pj_data['pj_compare_domain'] = $_rootdomain['rootdomain'];

                		break;
                	case 2:
                		// URL完全一致
                		/*
                		 * プロトコル・www取り除いた後に完全一致するか否か
                		 */

                	case 3:
                		// URL部分一致
                		/*
                		 * プロトコル・www取り除いた後に前方一致するかどうか
                		 */

                	default:

                		$_set_pj_data['pj_compare_domain'] = preg_replace("/^https?:\/\/(www\.)?/i", "", $_set_pj_data['pj_url']);

                }













                // 契約延長 有無
                if ((isset($input_post['pj_renew_chk'])) && ($input_post['pj_renew_chk'] == 1))
                {
                    $_set_pj_data['pj_renew_chk'] = $input_post['pj_renew_chk'];
                    $_set_pj_data['pj_renew_mm']  = $input_post['pj_renew_mm'];
                } else {
                    $_set_pj_data['pj_renew_chk'] = 0;
                    $_set_pj_data['pj_renew_mm']  = 0;
                }

                $_num = 0;
                for($_num; $_num<10; $_num++)
                {
                    $_item = "pjd_rank_str01" . sprintf("%02d", $_num);
                    $_set_rank_data01[$_num]['str'] = $_set_pj_data[$_item];
                    unset($_set_pj_data[$_item]) ;
                    $_item = "pjd_rank_end01" . sprintf("%02d", $_num);
                    $_set_rank_data01[$_num]['end'] = $_set_pj_data[$_item];
                    unset($_set_pj_data[$_item]) ;
                    $_item = "pjd_billing01" . sprintf("%02d", $_num);
                    $_set_rank_data01[$_num]['bill'] = $_set_pj_data[$_item];
                    unset($_set_pj_data[$_item]) ;
                }

                unset($_set_pj_data["chkengine"]) ;
                unset($_set_pj_data["submit"]) ;

                // トランザクション・START
                $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
                $this->db->trans_start();                                               // trans_begin

                    // DB書き込み
                    $this->pj->update_project($_set_pj_data, $_SESSION['c_memGrp'], 'projects');

                    // 顧客マスタに作成順序書き込み判定
                    $this->_update_timing($_set_pj_data);

                    /*
                    * 成果報酬の際、順位チェックの設定内容を書き込む
                    *   ・G も Y も請求金額は同じ。
                    */
                    if (($input_post['pj_accounting'] == 2) || ($input_post['pj_accounting'] == 3))
                    {

                        $this->load->model('Project_detail', 'pjd', TRUE);

                        // 既存データの有無チェック
                        $get_pjd_data = $this->pjd->get_pj_seq($_set_pj_data['pj_seq'], $_SESSION['c_memGrp'], 'projects');

                        $cnt=0;
                        foreach($_set_rank_data01 as $key => $value)
                        {
                            // DB書き込み
                            $set_pjd_data['pjd_cm_seq']   = $_set_pj_data['pj_cm_seq'];
                            $set_pjd_data['pjd_pj_seq']   = $_set_pj_data['pj_seq'];
                            $set_pjd_data['pjd_rank_str'] = $value['str'];
                            $set_pjd_data['pjd_rank_end'] = $value['end'];
                            $set_pjd_data['pjd_billing']  = $value['bill'];
                            $set_pjd_data['pjd_order_no'] = $cnt;

                            if (count($get_pjd_data) >= 1)
                            {
                                $this->pjd->update_project_detail($set_pjd_data, $_SESSION['c_memGrp'], 'projects');
                            } else {
                                $this->pjd->insert_project_detail($set_pjd_data, $_SESSION['c_memGrp'], 'projects');
                            }

                            $cnt++;
                        }
                    }

                // トランザクション・COMMIT
                $this->db->trans_complete();                                            // trans_rollback & trans_commit
                if ($this->db->trans_status() === FALSE)
                {
                    log_message('error', 'CLIENT::[Projectlist -> addchk()]：受注案件情報確認＆登録処理 トランザクションエラー');
                } else {
                    $this->smarty->assign('mess', "<font color=blue>更新が完了しました。</font>");
                    redirect('/projectlist/');
                }
            }
        }

        // 初期値セット
        $this->_item_set();

        // 担当営業セット
        $this->_sales_item_set();

        // ロケーションセット
        $this->_location_item_set();

        // 成果報酬の値セット
        foreach ($input_post as $key => $value)
        {
            if (strpos($key, "pjd_") !== false)
            {
                $this->smarty->assign($key, array($key => $value));
            }
        }

        $this->smarty->assign('info', $input_post);
        $this->view('projectlist/detail.tpl');

    }

    // 受注案件情報追加
    public function add()
    {

        $input_post = $this->input->post();

        // バリデーション設定
        $this->_set_validation02();

        // 初期値セット
        $this->_item_set();

        // 担当営業セット
        $this->_sales_item_set();

        // ロケーションセット
        $this->_location_item_set();

        // 会社名セット
        $this->load->model('Customer', 'cm', TRUE);
        $cm_data = $this->cm->get_cm_seq($input_post['chg_seq']);

        $this->smarty->assign('pj_cm_seq',     $input_post['chg_seq']);
        $this->smarty->assign('pj_cm_company', $cm_data[0]['cm_company']);
        $this->smarty->assign('pj_salesman',   $cm_data[0]['cm_salesman']);
        $this->smarty->assign('renew',         1);									// 延長有無設定
        $this->smarty->assign('renew_mm',      6);									// 延長期間設定
        $this->smarty->assign('tmp_memo',      NULL);
        $this->smarty->assign('init_location', NULL);

        $this->view('projectlist/add.tpl');

    }

    // 受注案件情報確認＆登録
    public function addchk()
    {

        $input_post = $this->input->post();

        // バリデーション・チェック
        $this->_set_validation02();
        if ($this->form_validation->run() == TRUE)
        {

            // 契約期間の判定
            $date_str = new DateTime($input_post['pj_start_date']);
            $date_end = new DateTime($input_post['pj_end_date']);
            if ($date_str > $date_end)
            {
                $this->smarty->assign('err_date', TRUE);
            } else {

            	$this->load->model('Project', 'pj', TRUE);

                // 受注案件情報をセット
                $_set_pj_data = $input_post;

                /*
                 * 検索エンジンの選択では
                 * 選択されたエンジンの場所にビットを立てる（感じ・・・）
                 *   ・G + Y -> "11"
                 *   ・Gのみ -> "10"
                 *   ・Yのみ -> "01"
                 *   ・なし  -> "00"
                 */
                if (isset($input_post['chkengine']))
                {

                    if (in_array(0, $input_post['chkengine']))
                    {
                        $_set_pj_data['pj_engine'] = "1";
                    } else {
                        $_set_pj_data['pj_engine'] = "0";
                    }

                    if (in_array(1, $input_post['chkengine']))
                    {
                        $_set_pj_data['pj_engine'] = $_set_pj_data['pj_engine'] . "1";
                    } else {
                        $_set_pj_data['pj_engine'] = $_set_pj_data['pj_engine'] . "0";
                    }

                } else {
                    $_set_pj_data['pj_engine'] = "00";
                }

                // 契約延長 有無
                if ((isset($input_post['pj_renew_chk'])) && ($input_post['pj_renew_chk'] == 1))
                {
                    $_set_pj_data['pj_renew_chk'] = $input_post['pj_renew_chk'];
                    $_set_pj_data['pj_renew_mm']  = $input_post['pj_renew_mm'];
                } else {
                    $_set_pj_data['pj_renew_chk'] = 0;
                    $_set_pj_data['pj_renew_mm']  = 0;
                }

                // ロケーション(Canonical Name)を取得
                $location_name = $this->pj->get_location_id($input_post['pj_location_id'], 'projects');
                $_set_pj_data['pj_location_name'] = $location_name[0]['lo_canonical_name'];

                $_num = 0;
                for($_num; $_num<10; $_num++)
                {
                    $_item = "pjd_rank_str01" . sprintf("%02d", $_num);
                    $_set_rank_data01[$_num]['str'] = $_set_pj_data[$_item];
                    unset($_set_pj_data[$_item]) ;
                    $_item = "pjd_rank_end01" . sprintf("%02d", $_num);
                    $_set_rank_data01[$_num]['end'] = $_set_pj_data[$_item];
                    unset($_set_pj_data[$_item]) ;
                    $_item = "pjd_billing01" . sprintf("%02d", $_num);
                    $_set_rank_data01[$_num]['bill'] = $_set_pj_data[$_item];
                    unset($_set_pj_data[$_item]) ;

                    $_set_rank_data01[$_num]['no'] = $_num;                                 // 連番キー
                }
                unset($_set_pj_data["chkengine"]) ;
                unset($_set_pj_data["_submit"]) ;

                // トランザクション・START
                $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
                $this->db->trans_start();                                               // trans_begin

                    // DB書き込み
                    $_pj_seq = $this->pj->insert_project($_set_pj_data, $_SESSION['c_memGrp'], 'projects');

                    // 顧客マスタに作成順序書き込み判定
                    $this->_update_timing($_set_pj_data);

                    /*
                     * 成果報酬の際、順位チェックの設定内容を書き込む
                     *   ・G も Y も請求金額は同じ。
                     */
                    if (($input_post['pj_accounting'] == 2) || ($input_post['pj_accounting'] == 3))
                    {
                        $this->load->model('Project_detail', 'pjd', TRUE);

                        foreach($_set_rank_data01 as $key => $value)
                        {
                            // DB書き込み
                            $set_pjd_data['pjd_cm_seq']   = $_set_pj_data['pj_cm_seq'];
                            $set_pjd_data['pjd_pj_seq']   = $_pj_seq;
                            $set_pjd_data['pjd_rank_str'] = $value['str'];
                            $set_pjd_data['pjd_rank_end'] = $value['end'];
                            $set_pjd_data['pjd_billing']  = $value['bill'];
                            $set_pjd_data['pjd_order_no'] = $value['no'];

                            $this->pjd->insert_project_detail($set_pjd_data, $_SESSION['c_memGrp'], 'projects');
                        }
                    }

                // トランザクション・COMMIT
                $this->db->trans_complete();                                            // trans_rollback & trans_commit
                if ($this->db->trans_status() === FALSE)
                {
                    log_message('error', 'CLIENT::[Projectlist -> addchk()]：受注案件情報確認＆登録処理 トランザクションエラー');
                } else {
                    $this->smarty->assign('mess', "<font color=blue>登録が完了しました。</font>");
                    redirect('/projectlist/');
                }

            }
        } else {
        	$this->smarty->assign('init_location', $input_post['pj_location_id']);
            $this->smarty->assign('mess', "<font color=red>項目に入力エラーが発生しました。</font>");
        }

        // 初期値セット
        $this->_item_set();

        // 担当営業セット
        $this->_sales_item_set();

        // ロケーションセット
        $this->_location_item_set();

        $this->smarty->assign('pj_cm_seq',     $input_post['pj_cm_seq']);
        $this->smarty->assign('pj_cm_company', $input_post['pj_cm_company']);
        $this->smarty->assign('pj_salesman',   $input_post['pj_salesman']);
        if (isset($input_post['pj_renew_chk']))
        {
            $this->smarty->assign('renew',     $input_post['pj_renew_chk']);
        } else {
            $this->smarty->assign('renew',     0);
        }
        $this->smarty->assign('renew_mm',      $input_post['pj_renew_mm']);
        $this->smarty->assign('tmp_memo',      $input_post['pj_memo']);

        $this->view('projectlist/add.tpl');

    }

    // 受注案件情報追加
    public function cp()
    {

        $input_post = $this->input->post();

        // バリデーション設定
        $this->_set_validation02();

        // 受注案件セット
        $this->load->model('Project', 'pj', TRUE);
        $pj_data = $this->pj->get_pj_seq($input_post['chg_seq'], $_SESSION['c_memGrp'], 'projects');

        // 受注案件詳細セット
        $this->load->model('Project_detail', 'pjd', TRUE);
        $pjd_data = $this->pjd->get_pj_seq($pj_data[0]['pj_seq'], $_SESSION['c_memGrp'], 'projects');
        if (count($pjd_data) == 0)
        {
            for($i=0; $i<=9; $i++)
            {
                $this->smarty->assign('pjd_rank_str01' . sprintf("%02d", $i), 0);
                $this->smarty->assign('pjd_rank_end01' . sprintf("%02d", $i), 0);
                $this->smarty->assign('pjd_billing01'  . sprintf("%02d", $i), 0);
            }
        } else {
            $i = 0;
            foreach($pjd_data as $key => $value)
            {
                $this->smarty->assign('pjd_rank_str01' . sprintf("%02d", $i), $value['pjd_rank_str']);
                $this->smarty->assign('pjd_rank_end01' . sprintf("%02d", $i), $value['pjd_rank_end']);
                $this->smarty->assign('pjd_billing01'  . sprintf("%02d", $i), $value['pjd_billing']);

                $i++;
            }
        }

        // 初期値セット
        $this->_item_set();

        // 担当営業セット
        $this->_sales_item_set();

        // ロケーションセット
        $this->_location_item_set();

        // 会社名セット
        $this->load->model('Customer', 'cm', TRUE);
        $cm_data = $this->cm->get_cm_seq($pj_data[0]['pj_cm_seq']);

        $this->smarty->assign('info_pj',       $pj_data[0]);

        $this->smarty->assign('renew',         $pj_data[0]['pj_renew_chk']);
        $this->smarty->assign('engine_g',      substr($pj_data[0]['pj_engine'], 0, 1));
        $this->smarty->assign('engine_y',      substr($pj_data[0]['pj_engine'], 1, 1));

        $this->smarty->assign('pj_cm_seq',     $cm_data[0]['cm_seq']);
        $this->smarty->assign('pj_cm_company', $cm_data[0]['cm_company']);
        $this->smarty->assign('pj_salesman',   $cm_data[0]['cm_salesman']);
        $this->smarty->assign('tmp_memo',      NULL);
        $this->smarty->assign('init_location', $pj_data[0]['pj_location_id']);

        $this->view('projectlist/cp.tpl');

    }

    // 受注案件情報確認＆登録
    public function cpchk()
    {

        $input_post = $this->input->post();

        // バリデーション・チェック
        $this->_set_validation02();
        if ($this->form_validation->run() == TRUE)
        {

            // 契約期間の判定
            $date_str = new DateTime($input_post['pj_start_date']);
            $date_end = new DateTime($input_post['pj_end_date']);
            if ($date_str > $date_end)
            {
                $this->smarty->assign('err_date', TRUE);
            } else {

            	$this->load->model('Project', 'pj', TRUE);

                // 受注案件情報をセット
                $_set_pj_data = $input_post;

                /*
                 * 検索エンジンの選択では
                 * 選択されたエンジンの場所にビットを立てる（感じ・・・）
                 *   ・G + Y -> "11"
                 *   ・Gのみ -> "10"
                 *   ・Yのみ -> "01"
                 *   ・なし  -> "00"
                 */
                if (isset($input_post['chkengine']))
                {

                    if (in_array(0, $input_post['chkengine']))
                    {
                        $_set_pj_data['pj_engine'] = "1";
                    } else {
                        $_set_pj_data['pj_engine'] = "0";
                    }

                    if (in_array(1, $input_post['chkengine']))
                    {
                        $_set_pj_data['pj_engine'] = $_set_pj_data['pj_engine'] . "1";
                    } else {
                        $_set_pj_data['pj_engine'] = $_set_pj_data['pj_engine'] . "0";
                    }

                } else {
                    $_set_pj_data['pj_engine'] = "00";
                }

                // 契約延長 有無
                if ((isset($input_post['pj_renew_chk'])) && ($input_post['pj_renew_chk'] == 1))
                {
                    $_set_pj_data['pj_renew_chk'] = $input_post['pj_renew_chk'];
                    $_set_pj_data['pj_renew_mm']  = $input_post['pj_renew_mm'];
                } else {
                    $_set_pj_data['pj_renew_chk'] = 0;
                    $_set_pj_data['pj_renew_mm']  = 0;
                }

                // ロケーション(Canonical Name)を取得
                $location_name = $this->pj->get_location_id($input_post['pj_location_id'], 'projects');
                $_set_pj_data['pj_location_name'] = $location_name[0]['lo_canonical_name'];

                $_num = 0;
                for($_num; $_num<10; $_num++)
                {
                    $_item = "pjd_rank_str01" . sprintf("%02d", $_num);
                    $_set_rank_data01[$_num]['str'] = $_set_pj_data[$_item];
                    unset($_set_pj_data[$_item]) ;
                    $_item = "pjd_rank_end01" . sprintf("%02d", $_num);
                    $_set_rank_data01[$_num]['end'] = $_set_pj_data[$_item];
                    unset($_set_pj_data[$_item]) ;
                    $_item = "pjd_billing01" . sprintf("%02d", $_num);
                    $_set_rank_data01[$_num]['bill'] = $_set_pj_data[$_item];
                    unset($_set_pj_data[$_item]) ;

                    $_set_rank_data01[$_num]['no'] = $_num;                                 // 連番キー
                }
                unset($_set_pj_data["chkengine"]) ;
                unset($_set_pj_data["_submit"]) ;

                // トランザクション・START
                $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
                $this->db->trans_start();                                               // trans_begin

                // DB書き込み
                $_pj_seq = $this->pj->insert_project($_set_pj_data, $_SESSION['c_memGrp'], 'projects');

                // 顧客マスタに作成順序書き込み判定
                $this->_update_timing($_set_pj_data);

                /*
                 * 成果報酬の際、順位チェックの設定内容を書き込む
                 *   ・G も Y も請求金額は同じ。
                */
                if (($input_post['pj_accounting'] == 2) || ($input_post['pj_accounting'] == 3))
                {
                    $this->load->model('Project_detail', 'pjd', TRUE);

                    foreach($_set_rank_data01 as $key => $value)
                    {
                        // DB書き込み
                        $set_pjd_data['pjd_cm_seq']   = $_set_pj_data['pj_cm_seq'];
                        $set_pjd_data['pjd_pj_seq']   = $_pj_seq;
                        $set_pjd_data['pjd_rank_str'] = $value['str'];
                        $set_pjd_data['pjd_rank_end'] = $value['end'];
                        $set_pjd_data['pjd_billing']  = $value['bill'];
                        $set_pjd_data['pjd_order_no'] = $value['no'];

                        $this->pjd->insert_project_detail($set_pjd_data, $_SESSION['c_memGrp'], 'projects');
                    }
                }

                // トランザクション・COMMIT
                $this->db->trans_complete();                                            // trans_rollback & trans_commit
                if ($this->db->trans_status() === FALSE)
                {
                    log_message('error', 'CLIENT::[Projectlist -> addchk()]：受注案件情報確認＆登録処理 トランザクションエラー');
                } else {
                    $this->smarty->assign('mess', "<font color=blue>複写が完了しました。</font>");
                    redirect('/projectlist/');
                }

            }
        } else {
            $this->smarty->assign('mess', "<font color=red>項目に入力エラーが発生しました。</font>");
        }

        // 初期値セット
        $this->_item_set();

        // 担当営業セット
        $this->_sales_item_set();

        // ロケーションセット
        $this->_location_item_set();

        // 受注案件セット
        $this->smarty->assign('info_pj',       $input_post);

        // 成果報酬の値セット
        foreach ($input_post as $key => $value)
        {
            if (strpos($key, "pjd_") !== false)
            {
                $this->smarty->assign($key, array($key => $value));
            }
        }

        $this->smarty->assign('pj_cm_seq',     $input_post['pj_cm_seq']);
        $this->smarty->assign('pj_cm_company', $input_post['pj_cm_company']);
        $this->smarty->assign('pj_salesman',   $input_post['pj_salesman']);
        $this->smarty->assign('tmp_memo',      $input_post['pj_memo']);
        $this->smarty->assign('init_location', $input_post['pj_location_id']);

        $this->view('projectlist/cp.tpl');

    }

    /**
     *  受注案件の契約延長処理
     *    ・処理月に該当する契約終了日を対象とする
     *    ・指定された延長月数をプラスして、その月の末日を設定する
     */
    public function project_renew()
    {

        // 該当月の契約更新対象受注案件を取得する
        $this->load->library('lib_project');
        $get_project_data  = $this->lib_project->get_renew_data();

        foreach ($get_project_data as $key => $value)
        {
            if ($value['pj_renew_chk'] == 1)
            {
                // 契約終了日からその月の1日を求める
                $date = new DateTime($value['pj_end_date']);
                $date = $date->modify('first day of this months');

                // 延長月数を設定
                $_tmp_month = '+' . $value['pj_renew_mm'] . ' months';

                $value['pj_end_date'] = $date->modify($_tmp_month)->format('Y-m-t');

                // 契約終了日の更新
                $this->pj->update_project($value, $_SESSION['c_memGrp'], 'projects');
            }
        }

        $this->view('projectlist/renew.tpl');

    }

    // Pagination 設定
    private function _get_Pagination($countall, $tmp_per_page)
    {

        $config['base_url']       = base_url() . '/projectlist/search/';        // ページの基本URIパス。「/コントローラクラス/アクションメソッド/」
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

    // 初期値セット
    private function _item_set()
    {

        // ステータスのセット
        $this->config->load('config_status');
        $opt_pj_status = $this->config->item('PROJECT_PJ_STATUS');

        // 案件請求書発行ステータスのセット
        $opt_pj_invoice_status = $this->config->item('PROJECT_PJ_INVOICE_STATUS');

        // 課金方式
        $this->config->load('config_comm');
        $opt_pj_accounting = $this->config->item('INVOICE_ACCOUNTING_BATCH');

        $this->smarty->assign('options_pj_status',  $opt_pj_status);
        $this->smarty->assign('options_pj_iv_type', $opt_pj_invoice_status);
        $this->smarty->assign('options_pj_accounting', $opt_pj_accounting);

    }

    // 検索項目 初期値セット
    private function _search_set()
    {

        // ステータス 選択項目セット
        $this->config->load('config_status');
        $opt_pj_status = $this->config->item('PROJECT_PJ_STATUS');

        // 請求書発行ステータス 選択項目セット
        $this->config->load('config_status');
        $opt_pj_invoice_status = $this->config->item('PROJECT_PJ_INVOICE_STATUS');

        // 課金方式 選択項目セット
        $this->config->load('config_comm');
        $opt_pj_accounting = $this->config->item('PROJECT_PJ_ACCOUNTING');

        // 受注案件ID 並び替え選択項目セット
        $arropt_id = array (
                ''     => '-- 選択してください --',
                'DESC' => '降順',
                'ASC'  => '昇順',
        );

        // 請求書発行対象企業
        $opt_cl_seq = $this->config->item('PROJECT_CL_SEQ');

        $this->load->model('Account', 'ac', TRUE);
        $salesman_list = $this->ac->get_salesman($opt_cl_seq, 'projects');       // 「ラベンダー」固定 : ac_cl_seq = 2

        $opt_pj_salesman[''] = " -- 選択してください -- ";
        foreach ($salesman_list as $key => $val)
        {
            $opt_pj_salesman[$val['ac_seq']] = $val['ac_name01'] . ' ' . $val['ac_name02'];
        }


        $this->smarty->assign('options_pj_status',         $opt_pj_status);
        $this->smarty->assign('options_pj_invoice_status', $opt_pj_invoice_status);
        $this->smarty->assign('options_pj_accounting',     $opt_pj_accounting);
        $this->smarty->assign('options_orderid',           $arropt_id);

        $this->smarty->assign('options_pj_salesman',       $opt_pj_salesman);

    }

    // 担当営業セット
    private function _sales_item_set()
    {

        // 請求書発行対象企業
        $this->config->load('config_comm');
        $opt_cl_seq = $this->config->item('PROJECT_CL_SEQ');

        $this->load->model('Account', 'ac', TRUE);
        $salesman_list = $this->ac->get_salesman($opt_cl_seq, 'projects');       // 「ラベンダー」固定 : ac_cl_seq = 2

        foreach ($salesman_list as $key => $val)
        {
            $opt_pj_salesman[$val['ac_seq']] = $val['ac_name01'] . ' ' . $val['ac_name02'];
        }

        $this->smarty->assign('options_pj_salesman', $opt_pj_salesman);

    }

    // ロケーションセット
    private function _location_item_set()
    {

    	$this->load->model('Project', 'pj', TRUE);
    	$location_list = $this->pj->get_location_list('projects');

    	foreach ($location_list as $key => $value)
    	{
    		$opt_location[$value['lo_criteria_id']] = $value['lo_canonical_name'];
    	}

    	$this->smarty->assign('options_location', $opt_location);

    }

    // 顧客マスタに作成順序書き込み判定
    private function _update_timing($_input_post)
    {

        /*
         * 請求書一括作成時に作成のタイミングにより以下の３パターンに分ける
         *  1 = 前月月末に固定報酬関連の請求書を作成する
         *  2 = 月初にSEO順位が確定した段階で成功報酬関連の請求書を作成する
         *  3 = 月初に代理店関連の請求書を作成する
         */

        // 代理店フラグのチェック
        $this->load->model('Customer', 'cm', TRUE);
        $this->load->model('Project',  'pj', TRUE);

        $this->config->load('config_comm');
        $cm_timing = $this->config->item('CUSTOMER_CM_INVO_TIMING');

        $cm_data = $this->cm->get_cm_seq($_input_post['pj_cm_seq']);

        if (($cm_data[0]['cm_agency_flg'] == 0) && ($cm_data[0]['cm_agency_seq'] == 0))
        {

            // 作成順序書き込み判定
            if (($_input_post['pj_accounting'] == 2) || ($_input_post['pj_accounting'] == 3))
            {
                $cm_data[0]['cm_invo_timing'] = $cm_timing['result'];                           // 成功報酬請求書関連
                $this->cm->update_customer($cm_data[0]);
            } else {
                $cm_data[0]['cm_invo_timing'] = $cm_timing['fix'];                              // 固定報酬請求書関連

                // 既存の受注案件のチェック
                $_iv_type = 2;                                                                  // 成功報酬
                $get_pj_list = $this->pj->get_pj_cm_seq($_input_post['pj_cm_seq'], $_iv_type, $_SESSION['c_memGrp'], 'projects', TRUE);
                if (count($get_pj_list) != 0)
                {
                    $cm_data[0]['cm_invo_timing'] = $cm_timing['result'];
                }

                $_iv_type = 3;                                                                  // 固定 + 成功報酬
                $get_pj_list = $this->pj->get_pj_cm_seq($_input_post['pj_cm_seq'], $_iv_type, $_SESSION['c_memGrp'], 'projects', TRUE);
                if (count($get_pj_list) != 0)
                {
                    $cm_data[0]['cm_invo_timing'] = $cm_timing['result'];
                }

                $this->cm->update_customer($cm_data[0]);
            }

        } else {

            $cm_data[0]['cm_invo_timing'] = $cm_timing['agency'];                               // 代理店請求書関連
            $this->cm->update_customer($cm_data[0]);

        }

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

    // フォーム・バリデーションチェック : フルチェック
    private function _set_validation02()
    {
        $rule_set = array(
                array(
                        'field'   => 'pj_status',
                        'label'   => 'ステータス選択',
                        'rules'   => 'trim|required|max_length[1]|is_numeric'
                ),
                array(
                        'field'   => 'pj_invoice_status',
                        'label'   => '請求書発行ステータス選択',
                        'rules'   => 'trim|required|max_length[1]|is_numeric'
                ),
                array(
                        'field'   => 'pj_orders_ymd',
                        'label'   => '受注年月日',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
                array(
                        'field'   => 'pj_orders_start',
                        'label'   => '契約開始年月日',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
                array(
                        'field'   => 'pj_start_date',
                        'label'   => '契約開始日',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
                array(
                        'field'   => 'pj_end_date',
                        'label'   => '契約終了日',
                        'rules'   => 'trim|required|regex_match[/^\d{4}\-|\/\d{1,2}\-|\/\d{1,2}+$/]|max_length[10]'
                ),
                array(
                        'field'   => 'pj_renew_mm',
                        'label'   => '契約延長 月数指定',
                        'rules'   => 'trim|max_length[2]|is_numeric'
                ),
                array(
                        'field'   => 'pj_keyword',
                        'label'   => '検索キーワード',
                        'rules'   => 'trim|required|max_length[100]'
                ),
                array(
                        'field'   => 'pj_url',
                        'label'   => '対象URL',
                        'rules'   => 'trim|required|regex_match[/^(https?)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/]|max_length[200]'
                ),
                array(
                        'field'   => 'pj_accounting',
                        'label'   => '課金方式',
                        'rules'   => 'trim|required|max_length[2]|is_numeric'
                ),
                array(
                        'field'   => 'pj_billing',
                        'label'   => '固定請求金額',
                        'rules'   => 'trim|required|max_length[10]|is_numeric'
                ),
                array(
                        'field'   => 'pj_salesman',
                        'label'   => '担当営業',
                        'rules'   => 'trim|required|max_length[10]|is_numeric'
                ),
                array(
                        'field'   => 'pj_penalty_cnt',
                        'label'   => 'ペナルティカウント',
                        'rules'   => 'trim|max_length[10]|is_numeric'
                ),
        		array(
        				'field'   => 'pj_paycal_fix',
        				'label'   => '固定金額',
        				'rules'   => 'trim|max_length[10]|is_numeric'
        		),
        		array(
        				'field'   => 'pj_paycal_rate',
        				'label'   => '料率',
        				'rules'   => 'trim|decimal|max_length[5]'
        		),
        		array(
                        'field'   => 'pj_memo',
                        'label'   => '備考',
                        'rules'   => 'trim|max_length[1000]'
                ),
                array(
                        'field'   => 'pj_tag',
                        'label'   => 'タグ設定',
                        'rules'   => 'trim|max_length[100]'
                ),
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}

