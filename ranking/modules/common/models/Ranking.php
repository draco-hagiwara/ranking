<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ranking extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 順位データを取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_ranking_kw($kw_seq, $start_date)
    {

    	$sql = 'SELECT
                  rk_seq,
                  rk_cl_seq,
                  rk_kw_seq,
    			  rk_se_seq,
    			  rk_position
    			FROM tb_ranking
    			WHERE
    			     rk_kw_seq = ' . $kw_seq . '
    			     AND rk_getdate = \'' . $start_date . '\'
	     		';



//     	print($sql);
//     	print("<br><br>");



    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }


    /**
     * キーワードSEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_kw_seq($seq_no, $start_date, $end_date)
    {

    	$sql = 'SELECT
                  rk_seq,
                  rk_cl_seq,
                  rk_kw_seq,
    			  rk_se_seq,
    			  rk_getdate,
    			  rk_position
    			FROM tb_ranking
    			WHERE
    			     rk_kw_seq = ' . $seq_no . '
    			     AND rk_getdate BETWEEN  \'' . $end_date . '\' AND \'' . $start_date . '\'
    			     ORDER BY rk_seq ASC
	    ';
    				//ORDER BY rk_seq ASC



//        	print($sql);
//        	print("<br><br>");



    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 旧キーワードSEQから順位取得対象SEQを取得する
     *
     * @param    date
     * @return   array()
     */
    public function get_kw_old_seq($today_date, $old_seq=NULL)
    {

    	$sql = 'SELECT
                  rk_seq,
                  rk_cl_seq,
                  rk_kw_seq,
    			  rk_se_seq,
    			  rk_getdate,
    			  rk_position
    			FROM tb_ranking
    			WHERE
    			     rk_getdate = \'' . $today_date . '\'

	    ';

    	if ($old_seq == NULL)
    	{
    		$sql .= ' AND rk_kw_old_seq is NULL ';
    	} else {
    		$sql .= ' AND ' . $old_seq;
    	}

    	$sql .= ' ORDER BY rk_seq ASC';

    	//        	print($sql);
    	//        	print("<br><br>");



    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * キーワードSEQから旧SEQを検索し対象となる全ての登録情報を取得する
     *
     * @param    date
     * @return   array()
     */
    public function get_top_rankingdata($kw_seq, $today_date)
    {

    	$sql = 'SELECT
                  rk_seq,
                  rk_cl_seq,
                  rk_kw_seq,
    			  rk_se_seq,
    			  rk_getdate,
    			  rk_result_id,
    			  rk_position
    			FROM tb_ranking
    			WHERE
    			     rk_getdate = \'' . $today_date . '\'
    			     AND (rk_kw_seq = ' . $kw_seq . ' OR rk_kw_old_seq = '. $kw_seq . ')
    			     ORDER BY rk_position ASC LIMIT 1
	    ';

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * 該当ランキング情報の取得 (取得日付)
     *
     * @param    date    : 取得日付
     * @param    int     : クライアントSEQ
     * @param    int     : 取得単位 (0:全件, 1:一部, 2:個別)
     * @return   array()
     */
    public function get_rk_getdatelist($rk_getdate, $client_no, $kind, $kw_seq=NULL)
    {

    	$sql = 'SELECT
                  rk_seq,
                  rk_cl_seq,
                  rk_kw_seq,
    			  rk_kw_old_seq,
    			  rk_se_seq,
    			  rk_result_id,
    			  rk_se_seq_re,
    			  rk_result_id_re,
    			  rk_position,
    			  rk_position_org,
    			  rk_ranking_url,
    			  rk_ranking_title,
    			  rk_getdate
    			FROM tb_ranking
    			WHERE
    			     rk_getdate = \'' . $rk_getdate . '\'
    			     AND rk_cl_seq = ' . $client_no
	    ;

	    // WHERE文 作成
	    if ($kind === 1)
	    {
	    	// 一部データ
	    	$sql .= ' AND (rk_position = 9999 OR rk_position = 90009)';
	    } elseif ($kind === 2) {
	    	// 個別データ
	    	$sql .= ' AND rk_kw_seq = ' . $kw_seq;
	    } else {

	    }

   		$sql .= ' ORDER BY rk_seq ASC';

    	// 対象全件数を取得
    	$query = $this->db->query($sql);
    	$kw_countall = $query->num_rows();

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$rank_list = $query->result('array');

    	return $rank_list;

    }

    /**
     * 順位データ登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_ranking($setData)
    {

    	// データ追加
    	$query = $this->db->insert('tb_ranking', $setData);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_ranking';
    	$set_data['lg_detail']    = 'rk_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }


    /**
     * 順位データの更新
     *
     * @param    array()
     * @return   int
     */
    public function update_ranking($setdata)
    {

    	// UPDATE
    	$where = array(
    			'rk_seq' => $setdata['rk_seq']
    	);

    	$result = $this->db->update('tb_ranking', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	return $result;

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_ranking';
    	$set_data['lg_detail'] = 'rk_seq = ' . $setdata['rk_seq'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * 順位データの更新
     *
     * @param    array()
     * @return   int
     */
    public function update_ranking_kwseq($setdata, $kw_seq)
    {

    	// UPDATE
    	$where = array(
    			'rk_kw_seq' => $kw_seq
    	);

    	$result = $this->db->update('tb_ranking', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_ranking_kwseq';
    	$set_data['lg_detail'] = 'rk_kw_seq = ' . $kw_seq . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $result;

    }

    /**
     * 順位データ情報削除
     *
     * @param    array()
     * @return   int
     */
    public function delete_ranking($kw_seq, $cl_seq)
    {

    	$set_where["rk_kw_seq"] = $kw_seq;
    	$set_where["rk_cl_seq"] = $cl_seq;

    	$result = $this->db->delete('tb_ranking', $set_where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'delete_ranking';
    	$set_data['lg_detail'] = $_last_sql;
    	$this->insert_log($set_data);

    	return $result;

    }























//     /**
//      * SEQから登録情報を取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_sd_seq($seq_no)
//     {

//     	$set_where["sd_seq"] = $seq_no;

//     	$query = $this->db->get_where('tb_search_data', $set_where);

//     	$get_data = $query->result('array');

//     	return $get_data;

//     }

//     /**
//      * 順位データを取得する
//      *
//      * @param    int
//      * @return   bool
//      */
//     public function get_seo_rank($result_id, $rootdomain)
//     {

//     	$sql = 'SELECT
//                   se_seq,
//                   se_result_id,
//                   se_position,
//     			  se_url,
//     			  se_title,
//     			  se_getdate
//     			FROM tb_serps
//     			WHERE
//     			     se_result_id = ' . $result_id . '
//     			     AND (se_domain = ' . $rootdomain . ' OR se_domain like \'% ' . $rootdomain . ')
//     			     ORDER BY se_seq ASC
// 	     		';

//     	// クエリー実行
//     	$query = $this->db->query($sql);
//     	$get_data = $query->result('array');

//     	return $get_data;

//     }

//     /**
//      * 検索結果のデータを登録
//      *
// 	 * @param    array()
//      * @return   int
//      */
//     public function insert_seach_data($get_serach_data)
//     {

//     	// データ追加
//     	$query = $this->db->insert('tb_serps', $get_serach_data);
//     	$_last_sql = $this->db->last_query();

//     	// 挿入した ID 番号を取得
//     	$row_id = $this->db->insert_id();

//     	return $row_id;

//     	// ログ書き込み
//     	$set_data['lg_func']   = 'insert_seach_data';
//     	$set_data['lg_detail'] = 'sd_seq = ' . $row_id . ' <= ' . $_last_sql;
//     	$this->insert_log($set_data);

//     }























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

    	$setData['lg_type'] = 'Ranking.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);
    }

}