<?php

class Data_csvup extends MY_Controller
{

    /*
     *  ＣＳＶアップロード処理
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

        $this->smarty->assign('up_mess', NULL);
        $this->smarty->assign('dl_mess', NULL);

    }

    // 顧客データCSVのアップロード処理TOP
    public function customer()
    {

        // バリデーション・チェック
        $this->_set_validation();

        $this->view('data_csvup/customer_csvup.tpl');

    }

    // 顧客データのCSV取込
    public function customer_csvup()
    {

        $input_post = $this->input->post();

        $up_errflg = FALSE;
        $up_mess   = '';

        $this->config->load('config_comm');
        $this->load->library('lib_csvparser');
        $this->load->library('lib_validator');

        switch ($input_post['_submit'])
        {
            case 'submit':

                // CSVファイルのアップロード
                $this->load->library('upload', $this->config->item('CUSTOMER_CSV_UPLOAD'));

                // CSVファイルの保存
                if ($this->upload->do_upload('cm_data'))
                {
                    $up_mess .= "<br><font color=blue>>> CSVファイルの読み込みに成功しました。</font><br>";
                    $up_mess .= "<br><font color=blue>>> CSVファイルのバリデーションチェックを開始しました。</font><br><br>";
                    $_upload_data = $this->upload->data();
                } else {
                    $up_mess .= "<br><font color=red>>> CSVファイルの読み込みに失敗しました。</font><br><br>";
                    $up_mess .= $this->upload->display_errors(' <p style="color:red;">', '</p>');

                    break;
                }

                try{
                    // CSVファイルの読み込み
                    $this->lib_csvparser->load($_upload_data['full_path'], TRUE, 1000, ',', '"');
                    $_csv_data = $this->lib_csvparser->parse();
                } catch (Exception $e){
                    $up_mess .= "<font color=red>エラー発生:" . $e->getMessage() . '</font><br><br>';
                    break;
                }

                // CSVファイルのバリデーションチェック
                $i = 0;
                $j = 0;
                foreach ($_csv_data as $key01 => $val01)
                {
                    foreach ($val01 as $key02 => $val02)
                    {
                        $_line_no = $i + 2;
                        if (($j == 0) && ($val02 != ''))
                        {
                            // 数字型＆文字列の長さチェック
                            if ($this->lib_validator->checkRange($val02, 0, 9999999999))
                            {
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

                        if (($j == 3) OR ($j == 55))
                        {
                            // 数字型＆文字列の長さチェック
                            if ($this->lib_validator->checkRange($val02, 0, 9999999999))
                            {
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

                        if (($j == 1) OR ($j == 2) OR ($j == 26) OR ($j == 36) OR ($j == 54) OR ($j == 56))
                        {
                            // 数字型＆文字列の長さチェック
                            if ($this->lib_validator->checkRange($val02, 0, 99))
                            {
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定(0～3)エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

                        if (($j == 25) && ($val02 != ''))
                        {
                            // 数字型＆文字列の長さチェック
                            if ($this->lib_validator->checkRange($val02, 0, 10))
                            {
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目で数字または範囲指定エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

                        if (($j == 12) OR ($j == 13))
                        {
                            // int型文字列チェック
                            if ($this->lib_validator->checkInt($val02))
                            {
                                // 文字列の長さチェック
                                if ($j == 12)
                                {
                                    $_tmp_no = 3;
                                } else {
                                    $_tmp_no = 4;
                                }
                                if ($this->lib_validator->checkLength($val02, 0, $_tmp_no))
                                {
                                } else {
                                    $up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数エラー。<br>";
                                    $up_errflg = TRUE;
                                }
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目でint数字エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

                        if ($j == 14)
                        {
                            // 文字列の長さチェック : max4
                            if ($this->lib_validator->checkLength($val02, 0, 4))
                            {
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.4)エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

                        if (($j == 10) OR ($j == 11))
                        {
                            // 文字列の長さチェック : max20
                            if ($this->lib_validator->checkLength($val02, 0, 20))
                            {
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.20)エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

                        if (($j == 4) OR ($j == 6) OR ($j == 7) OR ($j == 8) OR ($j == 53))
                        {
                            // 文字列の長さチェック : max50
                            if ($this->lib_validator->checkLength($val02, 0, 50))
                            {
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.50)エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

                        if (($j == 15) OR ($j == 16))
                        {
                            // 文字列の長さチェック : max100
                            if ($this->lib_validator->checkLength($val02, 0, 100))
                            {
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目で文字数(max.100)エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

                        if ($j == 32)
                        {
                            // メールアドレス形チェック
                            if ($this->lib_validator->checkMailAddress($val02))
                            {
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目でメールアドレスの形式エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

                        if ($j == 53)
                        {
                            // 半角英数記号カナ チェック
                            if ($this->lib_validator->single_eisukana($val02))
                            {
                            } else {
                                $up_mess .= $_line_no . "行目:「" . $key02 . "」項目で半角英数記号カナ エラー。<br>";
                                $up_errflg = TRUE;
                            }
                        }

//                      if (($j == 6) && ($val02 != ''))    // 任意
//                      {
//                          // 日付時間型チェック
//                          if ($this->lib_validator->checkDateFormat($val02, 'Y-m-d H:i:s'))
//                          {
//                          } else {
//                              $up_mess .= $i + 1 . "行目:「" . $key02 . "」項目で日付時間エラー。<br>";
//                              $up_errflg = TRUE;
//                          }
//                      }

//                      if (($j >= 7) && ($val02 != ''))    // 任意
//                      {
//                          // 日付型チェック
//                          if ($this->lib_validator->checkDateFormat($val02, 'Y-m-d'))
//                          {
//                          } else {
//                              $up_mess .= $i + 1 . "行目:「" . $key02 . "」項目で日付エラー。<br>";
//                              $up_errflg = TRUE;
//                          }
//                      }

                        $j++;
                    }
                    $i++;
                    $j = 0;
                }

                if ($up_errflg == TRUE)
                {
                    $up_mess .= "<br><font color=red>>> CSVファイルのバリデーションチェックに失敗しました。</font><br>";
                    break;
                } else {
                    $up_mess .= "<font color=blue>>> CSVファイルのバリデーションチェックに成功しました。</font><br>";
                }

                // CSVファイルでのUPDATE
                $this->load->model('Customer', 'cm', TRUE);
                $cnt_new = 0;
                $cnt_up  = 0;
                foreach ($_csv_data as $key => $value)
                {

                    // 「顧客情報」更新
                    $set_csv_data = array();
                    $set_csv_data['cm_seq']            = $value['顧客ID'];
                    $set_csv_data['cm_status']         = $value['ステータス'];
                    $set_csv_data['cm_agency_flg']     = $value['代理店親フラグ'];
                    $set_csv_data['cm_agency_seq']     = $value['代理店親cm_seq'];
                    $set_csv_data['cm_yayoi_name']     = $value['弥生名称'];
                    $set_csv_data['cm_company_kana']   = $value['会社名カナ'];
                    $set_csv_data['cm_company']        = $value['会社名'];
                    $set_csv_data['cm_president01']    = $value['代表者姓'];
                    $set_csv_data['cm_president02']    = $value['代表者名'];
                    $set_csv_data['cm_department']     = $value['担当者部署'];
                    $set_csv_data['cm_person01']       = $value['担当者姓'];
                    $set_csv_data['cm_person02']       = $value['担当者名'];
                    $set_csv_data['cm_zip01']          = $value['郵便番号1'];
                    $set_csv_data['cm_zip02']          = $value['郵便番号2'];
                    $set_csv_data['cm_pref']           = $value['都道府県'];
                    $set_csv_data['cm_addr01']         = $value['住所1'];
                    $set_csv_data['cm_addr02']         = $value['住所2'];
                    $set_csv_data['cm_buil']           = $value['住所3'];
                    $set_csv_data['cm_tel01']          = $value['代表者TEL'];
                    $set_csv_data['cm_tel02']          = $value['担当者TEL'];
                    $set_csv_data['cm_mobile']         = $value['携帯TEL'];
                    $set_csv_data['cm_seturitu']       = $value['設立年月日'];
                    $set_csv_data['cm_capital']        = $value['資本金'];
                    $set_csv_data['cm_closingdate']    = $value['決算日'];
                    $set_csv_data['cm_employee']       = $value['従業員数'];
                    $set_csv_data['cm_pub_company']    = $value['上場フラグ'];
                    $set_csv_data['cm_collect']        = $value['回収サイト'];
                    $set_csv_data['cm_credit_chk']     = $value['与信チェック日'];
                    $set_csv_data['cm_antisocial_chk'] = $value['反社チェック日'];
                    $set_csv_data['cm_credit_max']     = $value['与信限度額'];
                    $set_csv_data['cm_trade_no']       = $value['取引申請番号'];
                    $set_csv_data['cm_fax']            = $value['FAX'];
                    $set_csv_data['cm_mail']           = $value['メール'];
                    $set_csv_data['cm_mailsub']        = $value['メールサブ'];
                    $set_csv_data['cm_memo']           = $value['備考'];
                    $set_csv_data['cm_memo_iv']        = $value['請求書：備考'];
                    $set_csv_data['cm_flg_iv']         = $value['請求書：有無フラグ'];
                    $set_csv_data['cm_company_iv']     = $value['請求書：会社名'];
                    $set_csv_data['cm_department_iv']  = $value['請求書：部署'];
                    $set_csv_data['cm_person01_iv']    = $value['請求書：担当者姓'];
                    $set_csv_data['cm_person02_iv']    = $value['請求書：担当者名'];
                    $set_csv_data['cm_zip01_iv']       = $value['請求書：郵便番号1'];
                    $set_csv_data['cm_zip02_iv']       = $value['請求書：郵便番号2'];
                    $set_csv_data['cm_pref_iv']        = $value['請求書：都道府県'];
                    $set_csv_data['cm_addr01_iv']      = $value['請求書：住所1'];
                    $set_csv_data['cm_addr02_iv']      = $value['請求書：住所2'];
                    $set_csv_data['cm_buil_iv']        = $value['請求書：住所3'];
                    $set_csv_data['cm_bank_cd']        = $value['銀行CD'];
                    $set_csv_data['cm_bank_nm']        = $value['銀行名'];
                    $set_csv_data['cm_branch_cd']      = $value['支店CD'];
                    $set_csv_data['cm_branch_nm']      = $value['支店名'];
                    $set_csv_data['cm_kind']           = $value['口座種別(普通/当座)'];
                    $set_csv_data['cm_account_no']     = $value['口座番号'];
                    $set_csv_data['cm_account_nm']     = $value['口座名義'];
                    $set_csv_data['cm_invo_timing']    = $value['請求書一括作成順序'];
                    $set_csv_data['cm_salesman']       = $value['担当営業'];
                    $set_csv_data['cm_delflg']         = $value['削除フラグ'];

                    if ($value['顧客ID'] == '')
                    {
                        // insert
                        $this->cm->insert_customer($set_csv_data);
                        $cnt_new++;
                    } else {
                        // UPDATE
                        $this->cm->update_customer($set_csv_data);
                        $cnt_up++;
                    }
                }

                $up_mess .= "<br><font color=blue>>> CSVファイルによる更新が完了しました。 　　　：　" . $cnt_up .  " 件</font>";
                $up_mess .= "<br><font color=blue>>> CSVファイルによる新規登録が完了しました。 　：　" . $cnt_new . " 件</font>";

                break;

            default:
        }

        // バリデーション・チェック
        $this->_set_validation();                                            // バリデーション設定
        //$this->form_validation->run();

        $this->smarty->assign('up_mess', $up_mess);

        $this->view('data_csvup/customer_csvup.tpl');

    }

    // 顧客情報CSV 全件ダウンロード
    public function customer_csvdown()
    {

        // 件数(max500件)を取得。とりあえず制限をかけておきます
        $tmp_offset   = 0;
        $tmp_per_page = 500;

        // 売上データの取得
        $this->load->model('Customer', 'cm', TRUE);
        $query = $this->cm->get_download_query($tmp_per_page, $tmp_offset, '0');

        // 作成したヘルパーを読み込む
        $this->load->helper(array('download', 'csvdata'));

        // ヘルパーに追加した関数を呼び出し、CSVデータ取得
        $get_dl_csv = csv_from_result($query);

        $file_name = 'dlcsv_customer_' . date('YmdHis') . '.csv';
        force_download($file_name, $get_dl_csv);

        $this->smarty->assign('dl_mess', "<br><font color=blue>>> CSVダウンロードが完了しました。</font>");

        $this->view('data_csvup/customer_csvup.tpl');

    }

    // 入金データCSVのアップロード処理TOP
    public function receive()
    {

        // バリデーション・チェック
        $this->_set_validation();

        $this->view('data_csvup/receive_csvup.tpl');

    }

    // 入金データのCSV取込
    public function receive_csvup()
    {

        print_r($_FILES['upfile']);
        print("<br><br>");
        exit;

        // パラメータを正しい構造で受け取った時のみ実行
        if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error'])) {

            try {

                /* ファイルアップロードエラーチェック */
                switch ($_FILES['upfile']['error']) {
                    case UPLOAD_ERR_OK:         // =0
                        // エラー無し
                        break;
                    case UPLOAD_ERR_NO_FILE:    // =4
                        // ファイル未選択
                        throw new RuntimeException('File is not selected');
                    case UPLOAD_ERR_INI_SIZE:   // =1
                    case UPLOAD_ERR_FORM_SIZE:  // =2
                        // 許可サイズを超過
                        throw new RuntimeException('File is too large');
                    default:
                        throw new RuntimeException('Unknown error');
                }

                $tmp_name = $_FILES['upfile']['tmp_name'];
                $detect_order = 'ASCII,JIS,UTF-8,CP51932,SJIS-win';
                setlocale(LC_ALL, 'ja_JP.UTF-8');

                /* 文字コードを変換してファイルを置換 */
                $buffer = file_get_contents($tmp_name);
                if (!$encoding = mb_detect_encoding($buffer, $detect_order, true)) {
                    // 文字コードの自動判定に失敗
                    unset($buffer);
                    throw new RuntimeException('Character set detection failed');
                }
                file_put_contents($tmp_name, mb_convert_encoding($buffer, 'UTF-8', $encoding));
                unset($buffer);

                /* トランザクション処理 */            // トランザクション・START
                $this->db->trans_strict(FALSE);                                         // StrictモードをOFF
                $this->db->trans_start();                                               // trans_begin

//              $db = DbManager::getConnection();
//              $db->beginTransaction();

                try {
                    $cnt = 0;
                    $fp = fopen($tmp_name, 'rb');
                    while ($row = fgetcsv($fp, 256, " ")) {                             // デリミタ：「半角スペース」で判定
                        if ($cnt == 0) {
                            // 1行目はタイトルのためスキップ
                            $cnt = 1;
                            continue;
                        }
                        if ($row === array(null)) {
                            // 空行はスキップ
                            continue;
                        }



                        print_r($row);
                        print("<br>");




//                      if (count($row) !== 10) {
//                          // カラム数が異なる無効なフォーマット
//                          throw new RuntimeException('Invalid column detected');
//                      }
//                      if ($row["0"] == "" || $row["1"] == "3" | $row["4"] == "") {
//                          // 必須項目チェック
//                          throw new RuntimeException($cnt . 'row :: Invalid column not SPACE');
//                      } else {

//                          $params["values"]['dm_flg' ]          = 2 ;                             // ステータス
//                          $params["values"]['dm_server' ]       = $row["1"] ;                     // サーバ (123,A,B)
//                          $params["values"]['dm_serverid' ]     = $row["0"] ;                     // サーバ番号
//                          $params["values"]['dm_domain_mng' ]   = $row["6"] ;                     // ドメイン管理会社
//                          $params["values"]['dm_domain' ]       = $row["4"] ;                     // ドメイン
//                          $params["values"]['dm_url' ]          = "http://" . $row["4"] . "/" ;   // URL
//                          $params["values"]['dm_keyword' ]      = $row["3"] ;                     // キーワード
//                          $params["values"]['dm_tpl_no' ]       = $row["7"] ;                     // テンプレート番号
//                          $params["values"]['dm_tpl_color' ]    = $row["8"] ;                     // テンプレート色
//                          $params["values"]['dm_pr' ]           = "0" ;                           // PR
//                          $params["values"]['dm_ftp_folder' ]   = "WinSCP" ;                      // FTPフォルダ
//                          $params["values"]['dm_in_link1' ]     = $row["5"] ;                     // 被リンクページ１
//                          $params["values"]['dm_client_url1' ]  = $row["13"] ;                    // toクライアントサイト１
//                          $params["values"]['dm_linkup_date' ]  = $row["14"] ;                    // リンク貼り日
//                          $params["values"]['dm_linkdel_date' ] = $row["12"] ;                    // リンク集削除
//                          $params["values"]['dm_in_link2' ]     = $row["10"] ;                    // 被リンクページ２
//                          $params["values"]['dm_authori_url2' ] = $row["11"] ;                    // toオーソリティサイト２
//                          $params["values"]['dm_in_link3' ]     = $row["15"] ;                    // 被リンクページ３
//                          $params["values"]['dm_authori_url3' ] = $row["16"] ;                    // toオーソリティサイト３
//                          $params["values"]['dm_comment' ]      = $row["17"] ;                    // 備考
//                          $params["values"]['dm_reg_date'  ]    = date("Y-m-d H:i:s") ;
//                          $params["values"]['dm_mod_date'  ]    = date("Y-m-d H:i:s") ;

//                          $strError = $db->insert( 'dm_master' , $params["values"] ) ;
//                          // $result = Lib_Domain_management::insert($params);
//                          $executed = $cnt;
//                      }

                        $executed = $cnt;
                        $cnt++;

                    }

                    if (!feof($fp)) {
                        // ファイルポインタが終端に達していなければエラー
                        throw new RuntimeException('CSV parsing error');
                    }
                    fclose($fp);
//                  $db->commit();

                    // トランザクション・COMMIT
                    $this->db->trans_complete();                                            // trans_rollback & trans_commit
                    if ($this->db->trans_status() === FALSE)
                    {
                        log_message('error', 'CLIENT::[Data_csvup -> receive_csvup()]：入金データ 読み込み処理 トランザクションエラー');
                    }


                } catch (Exception $e) {
                    fclose($fp);

//                  $db->rollBack();
                    // トランザクション・COMMIT
                    $this->db->trans_complete();                                            // trans_rollback & trans_commit
                    if ($this->db->trans_status() === FALSE)
                    {
                        log_message('error', 'CLIENT::[Data_csvup -> receive_csvup()]：入金データ 読み込み処理 トランザクションエラー');
                    }

                    throw $e;
                }

                /* 結果メッセージをセット */
                if (isset($executed)) {
                    // 1回以上実行された
                    $up_mess01 = array('green', $cnt . ' row :: Import successful');
                } else {
                    // 1回も実行されなかった
                    $up_mess01 = array('black', 'There were nothing to import');
                }

            } catch (Exception $e) {

                /* エラーメッセージをセット */
                $up_mess01 = array('red', $e->getMessage());

            }
        }


        // バリデーション・チェック
        $this->_set_validation();                                            // バリデーション設定
        //$this->form_validation->run();

        $this->smarty->assign('up_mess01', $up_mess01);

        $this->view('data_csvup/receive_csvup.tpl');

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {

        $rule_set = array(
        );

        $this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み

    }

}

