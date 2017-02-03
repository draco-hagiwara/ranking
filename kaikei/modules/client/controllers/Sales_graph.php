<?php

class Sales_graph extends MY_Controller
{

	/*
	 *  売上データのグラフ表示
	 *
	 *    > 月次売上グラフ
	 *    > 担当営業別売上グラフ
	 */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

    }

    // グラフ 初期表示
    public function index()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');

    }

    // 売上データグラフ表示：月次売上
    public function monthly()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');

    	$this->_set_validation();

    	// 売上データの取得
    	$this->load->model('Sales', 'sa', TRUE);

    	$date = new DateTime();
    	$_x_data = $date->modify('-11 months')->format('Y/m');

    	$_start_date = $date->modify('first day of this months')->format("Y-m-d 00:00:00");
    	$_end_day    = $date->modify('last day of this months' )->format("Y-m-d 23:59:59");

    	$_y_data = $this->sa->get_sales_monthly($_start_date, $_end_day);

    	for ($cnt = 0; $cnt < 11; $cnt++)
    	{
    		$date_str = new DateTime($_start_date);
    		$_start_date = $date_str->modify('+1 months')->format('Y-m-d 00:00:00');

    		$_end_day = $date_str->modify('last day of this months')->format('Y-m-d 23:59:59');

    		$_y_data .=  ',' . $this->sa->get_sales_monthly($_start_date, $_end_day);

    		$_x_data .= ',' . $date_str->format('Y/m');
    	}

	// グラフ用データ
	$this->smarty->assign('x_data',  ',,,,,,,,,,,,');						// 目盛を表示したくないため。臨時処置！
	$this->smarty->assign('y_data',  $_y_data);

	$_tbl_x_data1 = explode(",", $_x_data);
	$_tbl_y_data1 = explode(",", $_y_data);

	$this->smarty->assign('tbl_x_data',  $_tbl_x_data1);
	$this->smarty->assign('tbl_y_data',  $_tbl_y_data1);

    	$this->view('sales_graph/monthly.tpl');

    }

    // 売上データグラフ表示：担当営業別売上
    public function salesman()
    {

    	// セッションデータをクリア
    	$this->load->library('lib_auth');
    	$this->lib_auth->delete_session('client');

    	$this->_set_validation();

    	$this->load->model('Account', 'ac', TRUE);
    	$this->load->model('Sales',     'sa', TRUE);

    	// 対象企業（ラベンダー=2）の選択
    	$this->config->load('config_comm');
    	$opt_cl_seq = $this->config->item('PROJECT_CL_SEQ');

    	// 対象営業担当者(ac_seq)を抽出
    	$salesman_list = $this->ac->get_salesman($opt_cl_seq, 'seorank');		// 「ラベンダー」固定 : ac_cl_seq = 2

	// 担当営業毎に売上データの取得
    	foreach ($salesman_list as $key => $val)
    	{

    		$sales_name[$key] = $val['ac_name01'] . " " . $val['ac_name02'] ;

	    	$date = new DateTime();
	    	$_x_data = $date->modify('-11 months')->format('Y/m');

	    	$_start_date = $date->modify('first day of this months')->format("Y-m-d 00:00:00");
	    	$_end_day    = $date->modify('last day of this months' )->format("Y-m-d 23:59:59");

	    	$_y_data[$key] = $this->sa->get_sales_salesman($val['ac_seq'], $_start_date, $_end_day);

	    	for ($cnt = 0; $cnt < 11; $cnt++)
	    	{
	    		$date_str = new DateTime($_start_date);
	    		$_start_date = $date_str->modify('+1 months')->format('Y-m-d 00:00:00');

	    		$_end_day = $date_str->modify('last day of this months')->format('Y-m-d 23:59:59');

	    		$_y_data[$key] .=  ',' . $this->sa->get_sales_salesman($val['ac_seq'], $_start_date, $_end_day);

	    		$_x_data .= ',' . $date_str->format('Y/m');
	    	}
    	}

    	// グラフ用データ
    	$this->smarty->assign('x_data',  ',,,,,,,,,,,,');						// 目盛を表示したくないため
    	$this->smarty->assign('y_data',  $_y_data);

    	// 営業名を表示
	$this->smarty->assign("salse_name",  $sales_name);

	// グラフカラーの設定（仮）
	$line_color_r = $this->config->item('SALES_GRAPH_R');
	$line_color_g = $this->config->item('SALES_GRAPH_G');
	$line_color_b = $this->config->item('SALES_GRAPH_B');

	$this->smarty->assign("line_color_r",     $line_color_r);
	$this->smarty->assign("line_color_g",     $line_color_g);
	$this->smarty->assign("line_color_b",     $line_color_b);

// 	$line_color = array("0" => 10, "1" => 120, "2" => 220);
// 	$this->smarty->assign("line_color",     $line_color);

	// テーブルデータの整形
	$_tbl_x_data = explode(",", $_x_data);
	$this->smarty->assign('tbl_x_data',  $_tbl_x_data);

	foreach ($_y_data as $key => $value)
	{
	    	$_tbl_y_data[$key] = explode(",", $value);
	}
	$this->smarty->assign('tbl_y_data',  $_tbl_y_data);

    	$this->view('sales_graph/salesman.tpl');

    }

    // フォーム・バリデーションチェック
    private function _set_validation()
    {
    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}
