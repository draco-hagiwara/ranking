<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Client extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * クライアントSEQから登録情報を取得する
     *
     * @param    int
     * @return   bool
     */
    public function get_cl_seq($seq_no)
    {

    	$set_where["cl_seq"] = $seq_no;

    	$query = $this->db->get_where('mt_client', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * クライアントメンバーの取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_clientlist($get_post, $tmp_per_page, $tmp_offset=0)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select["cl_status"]  = $get_post['cl_status'];
    	$set_select["cl_company"] = $get_post['cl_company'];

    	// ORDER BY
    	if ($get_post['orderid'] == 'ASC')
    	{
    		$set_orderby["cl_seq"] = $get_post['orderid'];
    	}else {
    		$set_orderby["cl_seq"] = 'DESC';
    	}

    	// 対象クアカウントメンバーの取得
    	$client_list = $this->_select_clientlist($set_select, $set_orderby, $tmp_per_page, $tmp_offset);

    	return $client_list;

    }

    /**
     * 対象クライアントメンバーの取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function _select_clientlist($set_select, $set_orderby, $tmp_per_page, $tmp_offset=0)
    {

    	$sql = 'SELECT
    			  cl_seq,
    			  cl_status,
    			  cl_company,
    			  cl_person01,
    			  cl_person02,
    			  cl_tel01,
    			  cl_tel02,
    			  cl_mail,
    			  cl_mailsub
    			FROM mt_client WHERE cl_delflg = 0 ';

    	// WHERE文 作成
    	if ($set_select["cl_status"] != '')
    	{
    		$sql .= ' AND cl_status = ' . $set_select["cl_status"];
    	}
    	if ($set_select["cl_company"] != '')
    	{
    		$sql .= ' AND cl_company LIKE \'%' . $this->db->escape_like_str($set_select['cl_company']) . '%\'';
    	}

    	// ORDER BY文 作成
    	$tmp_firstitem = FALSE;
    	foreach ($set_orderby as $key => $val)
    	{
    		if (isset($val))
    		{
    			if ($tmp_firstitem == FALSE)
    			{
    				$sql .= ' ORDER BY ' . $key . ' ' . $val;
    				$tmp_firstitem = TRUE;
    			} else {
    				$sql .= ' , ' . $key . ' ' . $val;
    			}
    		}
    	}

    	// 対象全件数を取得
    	$query = $this->db->query($sql);
    	$client_countall = $query->num_rows();

    	// LIMIT ＆ OFFSET 値をセット
    	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$client_list = $query->result('array');

    	return array($client_list, $client_countall);
    }



    /**
     * クライアント新規会員登録
     *
     * @param    array()
     * @param    bool : パスワード設定有無(空PWは危険なので一応初期登録でも入れておく)
     * @return   int
     */
    public function insert_client($setData)
    {

    	// パスワード変換
    	$_hash_pw = password_hash($setData["cl_pw"], PASSWORD_DEFAULT);
    	$setData["cl_pw"] = $_hash_pw;

    	// データ追加
    	$query = $this->db->insert('mt_client', $setData);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_client';
    	$set_data['lg_detail']    = 'cl_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }


    /**
     * 1レコード更新
     *
     * @param    array()
     * @return   bool
     */
    public function update_client($setdata, $pw = FALSE)
    {

    	// パスワード更新有無
    	if ($pw == TRUE)
    	{
    		$_hash_pw = password_hash($setdata["cl_pw"], PASSWORD_DEFAULT);
    		$setdata["cl_pw"] = $_hash_pw;
    	} else {
    		unset($setdata["cl_pw"]) ;
    	}

    	// ステータスの判定
    	if ($setdata["cl_status"] == 9)
    	{
    		$setdata["cl_delflg"] = 1;
    	}

    	$where = array(
    			'cl_seq' => $setdata['cl_seq']
    	);

    	$result = $this->db->update('mt_client', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']      = 'update_client';
    	$set_data['lg_detail']    = 'cl_seq = ' . $setdata['cl_seq'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    	return $result;
    }

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

    	$setData['lg_type']      = 'client.php';
    	$setData['lg_ip'] = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);
    }

}