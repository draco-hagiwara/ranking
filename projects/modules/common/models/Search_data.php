<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search_data extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * SEQから登録情報を取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_sd_seq($seq_no)
    {

    	$set_where["sd_seq"] = $seq_no;

    	$query = $this->db->get_where('tb_search_data', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 検索結果のデータを登録
     *
	 * @param    array()
	 * @param    char
	 * @param    array()
     * @return   int
     */
    public function insert_seach_data($get_serach_data, $err_mess, $pj_list, $engine)
    {

    	$this->config->load('config_status');
    	$opt_search_st = $this->config->item('SEARCH_DATA_STATUS');
    	$opt_rank_flg  = $this->config->item('SEARCH_RANKING_FLG');

    	$time = time();

    	if ($err_mess == "success")
    	{
    		$setData["sd_status"]      = $opt_search_st['success'];
    		$setData["sd_status_memo"] = $err_mess;
    	} else {
    		$setData["sd_status"]      = $opt_search_st['error'];
    		$setData["sd_status_memo"] = $err_mess;
    	}

    	$setData["sd_pj_seq"]      = $pj_list['pj_seq'];
    	$setData["sd_pj_keyword"]  = $pj_list['pj_keyword'];
    	$setData["sd_pj_url"]      = $pj_list['pj_url'];
    	$setData["sd_engine"]      = $engine;
    	$setData["sd_target"]      = $pj_list['pj_target'];
    	$setData["sd_url_match"]   = $pj_list['pj_url_match'];
    	$setData["sd_location_id"] = $pj_list['pj_location_id'];
    	$setData["sd_seach_ymd"]   = date("Y-m-d", $time);
    	$setData["sd_rankflg"]     = $opt_rank_flg['no-write'];
    	$setData["sd_ranking"]     = $pj_list['sd_ranking'];



//     	print_r($get_serach_data);


    	$sql = 'INSERT INTO `tb_search_data` (
		    			`sd_status`,
		    			`sd_status_memo`,
		    			`sd_pj_seq`,
		    			`sd_pj_keyword`,
		    			`sd_pj_url`,
		    			`sd_engine`,
		    			`sd_target`,
    					`sd_url_match`,
		    			`sd_location_id`,
    					`sd_seach_ymd`,
		    			`sd_rankflg`,
		    			`sd_ranking`,
    					`sd_data`
		    			)
    			VALUES ('
    					. $setData["sd_status"] . ','
    					. "'" . $setData["sd_status_memo"] . "',"
    					. $setData["sd_pj_seq"] . ','
    					. "'" . $setData["sd_pj_keyword"] . "',"
    					. "'" . $setData["sd_pj_url"] . "',"
    					. "'" . $setData["sd_engine"] . "',"
    					. $setData["sd_target"] . ','
    					. $setData["sd_url_match"] . ','
    					. $setData["sd_location_id"] . ','
    					. "'" . $setData["sd_seach_ymd"] . "',"
    					. $setData["sd_rankflg"] . ','
    					. $setData["sd_ranking"] . ','
    					. "'" . str_replace("'", "%27", $get_serach_data)					// 「'」 を エンコーディングして "%27" に置き換えた！
    					. "');"
    	;

    	// データ追加
    	$query = $this->db->query($sql);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']   = 'insert_seach_data';
    	$set_data['lg_detail'] = 'sd_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }























//     /**
//      * 会社一覧を取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_cl_company()
//     {

//     	$sql = 'SELECT
//     			  cl_seq,
//     			  cl_status,
//     			  cl_company
//     			FROM mt_client WHERE cl_delflg = 0 ORDER BY cl_seq DESC';

//     	// クエリー実行
//     	$query = $this->db->query($sql);
//     	$get_data = $query->result('array');

//     	return $get_data;

//     }

//     /**
//      * クライアントメンバーの取得
//      *
//      * @param    array() : 検索項目値
//      * @param    int     : 1ページ当たりの表示件数(LIMIT値)
//      * @param    int     : オフセット値(ページ番号)
//      * @return   array()
//      */
//     public function get_clientlist($get_post, $tmp_per_page, $tmp_offset=0)
//     {

//     	// 各SQL項目へセット
//     	// WHERE
//     	$set_select["cl_status"]  = $get_post['cl_status'];
//     	$set_select["cl_company"] = $get_post['cl_company'];

//     	// ORDER BY
//     	if ($get_post['orderid'] == 'ASC')
//     	{
//     		$set_orderby["cl_seq"] = $get_post['orderid'];
//     	}else {
//     		$set_orderby["cl_seq"] = 'DESC';
//     	}

//     	// 対象クアカウントメンバーの取得
//     	$client_list = $this->_select_clientlist($set_select, $set_orderby, $tmp_per_page, $tmp_offset);

//     	return $client_list;

//     }

//     /**
//      * 対象クライアントメンバーの取得
//      *
//      * @param    array() : WHERE句項目
//      * @param    array() : ORDER BY句項目
//      * @param    int     : 1ページ当たりの表示件数
//      * @param    int     : オフセット値(ページ番号)
//      * @return   array()
//      */
//     public function _select_clientlist($set_select, $set_orderby, $tmp_per_page, $tmp_offset=0)
//     {

//     	$sql = 'SELECT
//     			  cl_seq,
//     			  cl_status,
//     			  cl_company,
//     			  cl_person01,
//     			  cl_person02,
//     			  cl_tel01,
//     			  cl_tel02,
//     			  cl_mail,
//     			  cl_mailsub
//     			FROM mt_client WHERE cl_delflg = 0 ';

//     	// WHERE文 作成
//     	if ($set_select["cl_status"] != '')
//     	{
//     		$sql .= ' AND cl_status = ' . $set_select["cl_status"];
//     	}
//     	if ($set_select["cl_company"] != '')
//     	{
//     		$sql .= ' AND cl_company LIKE \'%' . $this->db->escape_like_str($set_select['cl_company']) . '%\'';
//     	}

//     	// ORDER BY文 作成
//     	$tmp_firstitem = FALSE;
//     	foreach ($set_orderby as $key => $val)
//     	{
//     		if (isset($val))
//     		{
//     			if ($tmp_firstitem == FALSE)
//     			{
//     				$sql .= ' ORDER BY ' . $key . ' ' . $val;
//     				$tmp_firstitem = TRUE;
//     			} else {
//     				$sql .= ' , ' . $key . ' ' . $val;
//     			}
//     		}
//     	}

//     	// 対象全件数を取得
//     	$query = $this->db->query($sql);
//     	$client_countall = $query->num_rows();

//     	// LIMIT ＆ OFFSET 値をセット
//     	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

//     	// クエリー実行
//     	$query = $this->db->query($sql);
//     	$client_list = $query->result('array');

//     	return array($client_list, $client_countall);
//     }


//     /**
//      * 重複データのチェック：ログインID
//      *
//      * @param    char
//      * @param    bool         :: FALSE => 新規登録時。 TRUE => 更新時使用。
//      * @return   bool
//      */
//     public function check_loginid($cl_id, $seq = FALSE)
//     {

//     	if ($seq == FALSE)
//     	{
//     		$sql = 'SELECT cl_id FROM `mt_client` '
//     				. 'WHERE `cl_id` = ? ';

//     		$values = array(
//     						$cl_id,
//     		);
//     	} else {
//     		$sql = 'SELECT cl_id FROM `mt_client` '
//         			. 'WHERE `cl_seq` != ? AND `cl_id` = ? ';

//         	$values = array(
//         					$seq,
//         					$cl_id,
//         	);
//     	}

//         $query = $this->db->query($sql, $values);

//         if ($query->num_rows() > 0) {
//         	return TRUE;
//         } else {
//         	return FALSE;
//         }
//     }

//     /**
//      * クライアント新規会員登録
//      *
//      * @param    array()
//      * @param    bool : パスワード設定有無(空PWは危険なので一応初期登録でも入れておく)
//      * @return   int
//      */
//     public function insert_client($setData)
//     {

//     	// パスワード変換
//    		$_hash_pw = password_hash($setData["cl_pw"], PASSWORD_DEFAULT);
//    		$setData["cl_pw"] = $_hash_pw;

//     	// データ追加
//     	$query = $this->db->insert('mt_client', $setData);
//     	$_last_sql = $this->db->last_query();

//     	// 挿入した ID 番号を取得
//     	$row_id = $this->db->insert_id();

//     	return $row_id;

//     	// ログ書き込み
//     	$set_data['lg_func']      = 'insert_client';
//     	$set_data['lg_detail']    = 'cl_seq = ' . $row_id . ' <= ' . $_last_sql;
//     	$this->insert_log($set_data);

//     }

//     /**
//      * 1レコード更新
//      *
//      * @param    array()
//      * @return   bool
//      */
//     public function update_client($setdata, $pw = FALSE)
//     {

//     	// パスワード更新有無
//     	if ($pw == TRUE)
//     	{
//     		$_hash_pw = password_hash($setdata["cl_pw"], PASSWORD_DEFAULT);
//     		$setdata["cl_pw"] = $_hash_pw;
//     	} else {
//     		unset($setdata["cl_pw"]) ;
//     	}

//     	// ステータスの判定
//     	if ($setdata["cl_status"] == 9)
//     	{
//     		$setdata["cl_delflg"] = 1;
//     	}

//     	$where = array(
//     					'cl_seq' => $setdata['cl_seq']
//     	);

//     	$result = $this->db->update('mt_client', $setdata, $where);
//     	$_last_sql = $this->db->last_query();

//     	// ログ書き込み
//     	$set_data['lg_func']      = 'update_client';
//     	$set_data['lg_detail']    = 'cl_seq = ' . $setdata['cl_seq'] . ' <= ' . $_last_sql;
//     	$this->insert_log($set_data);

//     	return $result;
//     }

//     /**
//      * ログイン日時の更新
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function update_Logindate($cl_seq, $user_type=2)
//     {

//     	$time = time();
//     	$setData = array(
//     			'cl_lastlogin' => date("Y-m-d H:i:s", $time)
//     	);
//     	$where = array(
//     			'cl_seq' => $cl_seq
//     	);
//     	$result = $this->db->update('mt_client', $setData, $where);
//     	$_last_sql = $this->db->last_query();

//     	// ログ書き込み
//     	$set_data['lg_func']      = 'update_Logindate';
//     	$set_data['lg_detail']    = 'cl_seq = ' . $cl_seq . ' <= ' . $_last_sql;
//     	$this->insert_log($set_data);

//     	return $result;
//     }













//     /**
//      * クライアントIDから登録情報を取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_cl_id($cl_id)
//     {

//     	$set_where["cl_id"]    = $cl_id;

//     	$query = $this->db->get_where('mb_client', $set_where);

//     	$get_data = $query->result('array');

//     	return $get_data;

//     }

//     /**
//      * クライアントサイトIDから登録情報を取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_cl_siteid($cl_siteid)
//     {

//     	$set_where["cl_siteid"]    = $cl_siteid;

//     	$query = $this->db->get_where('mb_client', $set_where);

//     	$get_data = $query->result('array');

//     	return $get_data;

//     }

//     /**
//      * クライアントSEQから営業＆編集者情報を取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_clac_seq($cl_seq, $cl_id)
//     {

//     	if (isset($cl_seq))
//     	{
//     		$set_where["cl_seq"]    = $cl_seq;
//     	} else {
//     		$set_where["cl_id"]    = $cl_id;
//     	}

//     	$query = $this->db->get_where('vw_a_clientlist', $set_where);

//     	$get_data = $query->result('array');

//     	return $get_data;

//     }


//     /**
//      * 重複データのチェック：サイトID(URL名)
//      *
//      * @param    char
//      * @param    bool         :: FALSE => 新規登録時。 TRUE => 更新時使用。
//      * @return   bool
//      */
//     public function check_siteid($seq = FALSE, $cl_siteid)
//     {

//     	if ($seq == FALSE)
//     	{
//     		$sql = 'SELECT * FROM `mb_client` '
//     				. 'WHERE `cl_siteid` = ? ';

//     		$values = array(
//     				$cl_siteid,
//     		);
//     	} else {
// 	    	$sql = 'SELECT * FROM `mb_client` '
// 	    			. 'WHERE `cl_seq` != ? AND `cl_siteid` = ? ';

// 	    	$values = array(
// 	    			$seq,
// 	    			$cl_siteid,
// 	    	);
//     	}

//     	$query = $this->db->query($sql, $values);

//     	if ($query->num_rows() > 0) {
//     		return TRUE;
//     	} else {
//     		return FALSE;
//     	}
//     }



//     /**
//      * 重複データのチェック：メールアドレス
//      *
//      * @param    int
//      * @param    varchar
//      * @return   bool
//      */
//     public function check_mailaddr($seq, $mail)
//     {

//     	$sql = 'SELECT * FROM `mb_client` '
//     			. 'WHERE `cl_seq` != ? AND `cl_mail` = ? ';

//     	$values = array(
//     			$seq,
//     			$mail,
//     	);

//     	$query = $this->db->query($sql, $values);

//     	if ($query->num_rows() > 0) {
//     		return TRUE;
//     	} else {
//     		return FALSE;
//     	}
//     }

//     /**
//      * クライアントSEQから登録情報を取得する
//      *
//      * @param    int
//      * @return   int
//      */
//     public function check_statusno($seq_no)
//     {

//     	$sql = 'SELECT cl_status FROM `mb_client` '
//     			. 'WHERE `cl_seq` = ' . $seq_no;

//     	$query = $this->db->query($sql);

//     	$get_data = $query->result('array');

//     	return $get_data;

//     }




//     /**
//      * ログインIDによる更新
//      *
//      * @param    array()
//      * @return   bool
//      */
//     public function update_client_id($setData, $user_type=2)
//     {

//     	$where = array(
//     			'cl_id' => $setData['cl_id']
//     	);

//     	$result = $this->db->update('mb_client', $setData, $where);

// //     	// ログ書き込み
// //     	$set_data['lg_user_type'] = "";
// //     	$set_data['lg_type']      = 'client_update';
// //     	$set_data['lg_func']      = 'update_client_id';
// //     	$set_data['lg_detail']    = 'cl_id = ' . $setData['cl_id'];
// //     	$this->insert_log($set_data);

//     	return $result;
//     }

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

    	$setData['lg_type'] = 'Search_data.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);
    }

}