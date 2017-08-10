<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group_tag extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * クライアントSEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_gt_seq($seq_no)
    {


    	$set_where["gt_seq"] = $seq_no;

    	$this->db->order_by('gt_seq', 'DESC');
    	$query = $this->db->get_where('tb_group_tag', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * クライアントSEQから登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_gt_clseq($cl_seq, $type)
    {


    	$set_where["gt_type"]   = $type;
    	$set_where["gt_cl_seq"] = $cl_seq;

    	$this->db->order_by('gt_seq', 'DESC');
    	$query = $this->db->get_where('tb_group_tag', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * name から登録情報を取得する
     *
     * @param    int
     * @return   array()
     */
    public function get_gt_name($tg_name, $cl_seq, $type)
    {


    	$set_where["gt_name"]   = $tg_name;
    	$set_where["gt_type"]   = $type;
    	$set_where["gt_cl_seq"] = $cl_seq;

//     	$this->db->order_by('gt_seq', 'DESC');
    	$query = $this->db->get_where('tb_group_tag', $set_where);

    	$get_data = $query->result('array');

    	return $get_data;

    }

    /**
     * グループタグ情報リストの取得
     *
     * @param    array() : 検索項目値
     * @param    int     : 1ページ当たりの表示件数(LIMIT値)
     * @param    int     : オフセット値(ページ番号)
     * @return   array()
     */
    public function get_gtlist($get_post, $tmp_per_page, $tmp_offset=0)
    {

    	// 各SQL項目へセット
    	// WHERE
    	$set_select_like["gt_name"] = $get_post['gt_name'];

    	// ORDER BY
//     	if ($get_post['orderid'] == 'ASC')
//     	{
//     		$set_orderby["gt_seq"] = $get_post['orderid'];
//     	}else {
//     		$set_orderby["gt_seq"] = 'DESC';
//     	}
    	$set_orderby = 'DESC';

    	$_set_item['gt_type']  = $get_post['gt_type'];
    	$_set_item['gt_clseq'] = $get_post['gt_cl_seq'];

    	// 対象クアカウントメンバーの取得
    	$gt_list = $this->_select_gtlist($set_select_like, $set_orderby, $tmp_per_page, $tmp_offset, $_set_item);

    	return $gt_list;

    }

    /**
     * グループタグ情報リストの取得
     *
     * @param    array() : WHERE句項目
     * @param    array() : ORDER BY句項目
     * @param    int     : 1ページ当たりの表示件数
     * @param    int     : オフセット値(ページ番号)
     * @param    int
     * @return   array()
     */
    public function _select_gtlist($set_select_like, $set_orderby, $tmp_per_page, $tmp_offset=0, $set_item)
    {

    	$sql = 'SELECT
    			  gt_seq,
    			  gt_name,
    			  gt_type,
    			  gt_domain_cnt,
    			  gt_keyword_cnt,
    			  gt_cl_seq
    			FROM tb_group_tag WHERE gt_cl_seq = ' . $set_item['gt_clseq'] . ' AND gt_type = ' . $set_item['gt_type'];

    	// WHERE文 作成
    	foreach ($set_select_like as $key => $val)
    	{
    		if (isset($val) && $val != '')
    		{
    			$sql .= ' AND ' . $key . ' LIKE \'%' . $this->db->escape_like_str($val) . '%\'';
    		}
    	}

    	// ORDER BY文 作成
    	$sql .= ' ORDER BY gt_seq ' .  $set_orderby;
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

    	// 対象全件数を取得
    	$query = $this->db->query($sql);
    	$gt_countall = $query->num_rows();

    	// LIMIT ＆ OFFSET 値をセット
    	$sql .= ' LIMIT ' . $tmp_per_page . ' OFFSET ' . $tmp_offset;

    	// クエリー実行
    	$query = $this->db->query($sql);
    	$gt_list = $query->result('array');







    	// グループ設定されたキーワード数をカウントする

    	// ** 該当KW を検索
    	$sql2 = 'SELECT
	              kw_seq,
	              kw_cl_seq,
	              kw_status,
	              kw_url,
	              kw_domain,
                  kw_rootdomain,
                  kw_keyword,
                  kw_matchtype,
                  kw_searchengine,
                  kw_device,
                  kw_location_id,
                  kw_location_name,
                  kw_maxposition,
                  kw_trytimes,
                  kw_group,
                  kw_tag,
    			  T2.wt_seq,
        		  T2.wt_ac_seq,
        		  T3.gt_seq,
				  T3.gt_name
        		FROM `tb_keyword` LEFT JOIN `tb_watchlist` as T2 on ((`kw_seq` = `wt_kw_seq`) AND (`wt_ac_seq` = ' . $_SESSION['c_memSeq'] . '))
        		                  LEFT JOIN `tb_group_tag` as T3 on (`kw_group` = `gt_name`)
        		WHERE `kw_old_seq` is NULL'
    	;

    	$sql2 .= ' AND `kw_cl_seq`  = ' . $set_item['gt_clseq'];
    	$sql2 .= ' AND `gt_cl_seq`  = ' . $set_item['gt_clseq'];

    	// WHERE文 作成
    	if (!empty($free_kw))
    	{
    		$free_word = str_replace("　", " ", $free_kw);
    		$array = explode(" ", $free_word);

    		$sql2 .= ' AND ';
    		for($i = 0; $i < count($array); $i++){
    			$sql2 .= '( kw_keyword LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql2 .= ' OR kw_url LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql2 .= ' OR kw_group LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\'';
    			$sql2 .= ' OR kw_tag LIKE \'%' . $this->db->escape_like_str($array[$i]) . '%\')';

    			if ($i < count($array) -1){
    				$sql2 .= " AND ";						// 絞り込み
    				//$sql2 .= " OR ";						// 部分一致
    			}
    		}
    	}

    	// WHERE文(group) 作成
    	if (!empty($gt_list))
    	{
    		$tmp_firstitem = FALSE;
    		foreach ($gt_list as $key => $val)
    		{
    			if (empty($val['kw_rootdomain']))
    			{
    				$_tmp_rootdomain = $val['wt_kw_rootdomain'];
    			} else {
    				$_tmp_rootdomain = $val['kw_rootdomain'];
    			}

    			if ($tmp_firstitem === FALSE)
    			{
    				$sql2 .= ' AND ( kw_rootdomain = \'' . $_tmp_rootdomain . '\'';
    				$tmp_firstitem = TRUE;
    			} else {
    				$sql2 .= ' OR kw_rootdomain = \'' . $_tmp_rootdomain . '\'';
    			}
    		}
    		$sql2 .= ' ) ';

    		// 対象全件数を取得
    		$query = $this->db->query($sql2);
    		$kw_countall = $query->num_rows();

    		// GROUP BY文 作成
    		$sql2 .= ' GROUP BY kw_url, kw_domain, kw_rootdomain, kw_keyword, kw_matchtype, kw_location_id';
    		//$sql2 .= ' GROUP BY kw_rootdomain, kw_keyword, kw_matchtype, kw_location_id';

    		// ORDER BY文 作成
    		//$sql2 .= ' ORDER BY T3.rd_seq ' .  $set_orderby;
    		$sql2 .= ' ORDER BY T3.gt_seq ' .  $set_orderby . " , kw_seq DESC";


    		//         	    					print("一覧：：<br>");
    		//         	    					print($sql2);
    		//         	    					print("<br><br>");


    		// クエリー実行
    		$query = $this->db->query($sql2);
    		$kw_list = $query->result('array');

    		//         	    		        print_r($kw_list);
    		//         	    		        var_dump($kw_list);
    		//         	    		        print("<br><br>");

    	} else {
    		$kw_list = array();
    		$kw_countall = 0;
    	}


    	return array($kw_list, $gt_countall, $kw_countall);

    }

    /**
     * グループ＆タグ登録
     *
     * @param    array()
     * @return   int
     */
    public function insert_group_tag($setData)
    {

    	// データ追加
    	$query = $this->db->insert('tb_group_tag', $setData);
    	$_last_sql = $this->db->last_query();

    	// 挿入した ID 番号を取得
    	$row_id = $this->db->insert_id();

    	return $row_id;

    	// ログ書き込み
    	$set_data['lg_func']      = 'insert_group_tag';
    	$set_data['lg_detail']    = 'tg_seq = ' . $row_id . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * グループ＆タグ情報の更新
     *
     * @param    array()
     * @return   int
     */
    public function update_gt_cnt($setdata, $type)
    {

    	// UPDATE
    	$where = array(
    					'gt_seq'    => $setdata['gt_seq'],
    			 		'gt_cl_seq' => $setdata['gt_cl_seq'],
		    			'gt_name'   => $setdata['gt_name'],
    	 );

    	$result = $this->db->update('tb_group_tag', $setdata, $where);
    	$_last_sql = $this->db->last_query();

    	return;

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_gt_cnt';
    	$set_data['lg_detail'] = 'gt_name = ' . $setdata['gt_name'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * グループ＆タグ情報の更新
     *
     * @param    array()
     * @return   int
     */
    public function update_gt_name($setdata, $type)
    {

    	// UPDATE
    	$sql = 'UPDATE `tb_group_tag` SET `gt_name`= \''
    			. $setdata['gt_name'] . '\''
    			. ' WHERE `gt_cl_seq`= ' . $setdata['gt_cl_seq']
    			. ' AND `gt_name`= \'' . $setdata['old_gt_name'] . '\''
    			. ' AND `gt_type`= ' . $type
    	;
//     	$sql = 'UPDATE `tb_group_tag` SET `gt_name`= \''
//     			. $setdata['gt_name'] . '\',`gt_memo`= \'' . $setdata['gt_memo'] . '\''
//     			. ' WHERE `gt_cl_seq`= ' . $setdata['gt_cl_seq']
//     			. ' AND `gt_name`= \'' . $setdata['old_gt_name'] . '\''
//     			. ' AND `gt_type`= ' . $type
//     	;

    	$query = $this->db->query($sql);
    	$_last_sql = $this->db->last_query();

    	return;

    	// ログ書き込み
    	$set_data['lg_func']   = 'update_gt_name';
    	$set_data['lg_detail'] = 'gt_name = ' . $setdata['gt_name'] . ' <= ' . $_last_sql;
    	$this->insert_log($set_data);

    }

    /**
     * グループ＆タグ情報の削除
     *
     * @param    array()
     * @return   int
     */
    public function delete_group_tag($setdata)
    {

    	$where = array(
		    			'gt_cl_seq' => $setdata['gt_cl_seq'],
		    			'gt_name'   => $setdata['old_gt_name'],
    	 );

    	$result = $this->db->delete('tb_group_tag', $where);
    	$_last_sql = $this->db->last_query();

    	// ログ書き込み
    	$set_data['lg_func']   = 'delete_group_tag';
    	$set_data['lg_detail'] = $_last_sql;
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

    	$setData['lg_type'] = 'Group_tag.php';
    	$setData['lg_ip']   = $this->input->ip_address();

    	// データ追加
    	$query = $this->db->insert('tb_log', $setData);

    	//     	// 挿入した ID 番号を取得
    	//     	$row_id = $this->db->insert_id();
    	//     	return $row_id;
    }

}