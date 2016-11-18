<?php

class Graph extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($_SESSION['c_login'] == TRUE)
        {
            $this->smarty->assign('login_chk', TRUE);
            $this->smarty->assign('mem_Seq',   $_SESSION['c_memSeq']);
            $this->smarty->assign('mem_Type',  $_SESSION['c_memType']);
            $this->smarty->assign('mem_Grp',   $_SESSION['c_memGrp']);
            $this->smarty->assign('mem_Name',  $_SESSION['c_memName']);
        } else {
            $this->smarty->assign('login_chk', FALSE);
            $this->smarty->assign('mem_Seq',   "");
            $this->smarty->assign('mem_Type',  "");
            $this->smarty->assign('mem_Grp',   "");
            $this->smarty->assign('mem_Name',  "");

            redirect('/login/');
        }

    }

    // グラフ 初期表示
    public function index()
    {

    	// セッションデータをクリア
    	$this->load->model('comm_auth', 'comm_auth', TRUE);
		$this->comm_auth->delete_session('client');

    	$this->_set_validation();


    	// グラフデータ (逆順)
    	$date = new DateTime();
    	$_end_day = date('t');														// 今月の末日を取得
    	//$_x_data = $date->format('d');												// X軸データ (当日からスタート)
    	$_x_data = $date->modify('-' . $_end_day+1 . ' days')->format('d');
    	for ($cnt = $_end_day-1; $cnt >= 1; $cnt--)
    	{
    		$_x_data .= ',' . $date->modify('+1 days')->format('d');

    		//print($_x_data);
    		//print("<br>");
    	}

    	$_tbl_date = explode(",", $_x_data);

    	// テストデータ
    	$_set_data1 = mt_rand(1, 50);
    	$_set_data2 = mt_rand(1, 100);
    	for ($cnt = $_end_day-1; $cnt >= 1; $cnt--)
    	{
    		$_set_data1 .= ',' . mt_rand(1, 50);
    		$_set_data2 .= ',' . mt_rand(1, 100);
    	}

    	$_tbl_data1 = explode(",", $_set_data1);
    	$_tbl_data2 = explode(",", $_set_data2);


    	print("DATA1 : ");
    	print($_set_data1);
    	print("<br>");
    	print("DATA2 : ");
    	print($_set_data2);
    	print("<br>");

    	$this->smarty->assign('x_data',  $_x_data);
    	$this->smarty->assign('set_data1',  $_set_data1);
    	$this->smarty->assign('set_data2',  $_set_data2);

    	$this->smarty->assign('tbl_date',  $_tbl_date);
    	$this->smarty->assign('tbl_data1',  $_tbl_data1);
    	$this->smarty->assign('tbl_data2',  $_tbl_data2);

        $this->view('graph/index.tpl');

    }

    // グラフ 初期表示
    public function graph_print()
    {

    	// セッションデータをクリア
    	$this->load->model('comm_auth', 'comm_auth', TRUE);
    	$this->comm_auth->delete_session('client');

    	$this->_set_validation();


    	// グラフデータ (逆順)
    	$date = new DateTime();
    	$_end_day = date('t');														// 今月の末日を取得
    	//$_x_data = $date->format('d');												// X軸データ (当日からスタート)
    	$_x_data = $date->modify('-' . $_end_day+1 . ' days')->format('d');
    	for ($cnt = $_end_day-1; $cnt >= 1; $cnt--)
    	{
    		$_x_data .= ',' . $date->modify('+1 days')->format('d');

    		//print($_x_data);
    		//print("<br>");
    	}

    	$_tbl_date = explode(",", $_x_data);

    	// テストデータ
    	$_set_data1 = mt_rand(1, 50);
    	$_set_data2 = mt_rand(1, 100);
    	for ($cnt = $_end_day-1; $cnt >= 1; $cnt--)
    	{
    		$_set_data1 .= ',' . mt_rand(1, 50);
    		$_set_data2 .= ',' . mt_rand(1, 100);
    	}

    	$_tbl_data1 = explode(",", $_set_data1);
    	$_tbl_data2 = explode(",", $_set_data2);

    	$this->smarty->assign('x_data',  $_x_data);
    	$this->smarty->assign('set_data1',  $_set_data1);
    	$this->smarty->assign('set_data2',  $_set_data2);

    	$this->smarty->assign('tbl_date',  $_tbl_date);
    	$this->smarty->assign('tbl_data1',  $_tbl_data1);
    	$this->smarty->assign('tbl_data2',  $_tbl_data2);

    	$this->view('graph/graph_print.tpl');

    }

    // ＰＤＦ作成
    public function createpdf()
    {

    	$date = new DateTime();
    	$_end_day = date('t');														// 今月の末日を取得


    	// テストデータ
    	$_set_data1 = mt_rand(1, 50);
    	$_set_data2 = mt_rand(1, 100);
    	for ($cnt = $_end_day-1; $cnt >= 1; $cnt--)
    	{
    		$_set_data1 .= ',' . mt_rand(1, 50);
    		$_set_data2 .= ',' . mt_rand(1, 100);
    	}

    	$_tbl_data1 = explode(",", $_set_data1);
    	$_tbl_data2 = explode(",", $_set_data2);







    	// 雛形PDFのパス取得
    	$this->load->helper('path');
    	$list_path = '../public/images/pdf/receipt_list.pdf';
    	$pdflist_path = set_realpath($list_path);

    	// インストールパスを取得 :: /home/cs/www/cs.com.dev
    	$list_path = '../';
    	$base_path = set_realpath($list_path);

    	// PDFライブラリ呼出
    	$this->load->library('pdf');
    	$this->pdf->pdf_receiptlist($pdf_list, $pdflist_path, $base_path);







    	$this->view('graph/index.tpl');

    }

    // ＰＤＦ作成
    public function pdf_test()
    {

    	$this->_set_validation();

    	$this->view('graph/pdf_test.tpl');

    }

    public function pdf_javascript()
    {

    	$date = new DateTime();
    	$_end_day = date('t');														// 今月の末日を取得


    	// テストデータ
    	$_set_data1 = mt_rand(1, 50);
    	$_set_data2 = mt_rand(1, 100);
    	for ($cnt = $_end_day-1; $cnt >= 1; $cnt--)
    	{
    		$_set_data1 .= ',' . mt_rand(1, 50);
    		$_set_data2 .= ',' . mt_rand(1, 100);
    	}

    	$_tbl_data1 = explode(",", $_set_data1);
    	$_tbl_data2 = explode(",", $_set_data2);



    	// 雛形PDFのパス取得
    	$this->load->helper('path');
    	$list_path = '../public/images/pdf/receipt_list.pdf';
    	$pdflist_path = set_realpath($list_path);

    	// インストールパスを取得 :: /home/cs/www/cs.com.dev
    	$list_path = '../';
    	$base_path = set_realpath($list_path);

    	// PDFライブラリ呼出
    	$this->load->library('pdf');
    	$this->pdf->pdf_javascript($_tbl_data1, $pdflist_path, $base_path);



    	$this->view('graph/pdf_test.tpl');

    }

    public function pdf_font()
    {

    	$date = new DateTime();
    	$_end_day = date('t');														// 今月の末日を取得


    	// テストデータ
    	$_set_data1 = mt_rand(1, 50);
    	$_set_data2 = mt_rand(1, 100);
    	for ($cnt = $_end_day-1; $cnt >= 1; $cnt--)
    	{
    		$_set_data1 .= ',' . mt_rand(1, 50);
    		$_set_data2 .= ',' . mt_rand(1, 100);
    	}

    	$_tbl_data1 = explode(",", $_set_data1);
    	$_tbl_data2 = explode(",", $_set_data2);



    	// 雛形PDFのパス取得
    	$this->load->helper('path');
    	$list_path = '../public/images/pdf/receipt_list.pdf';
    	$pdflist_path = set_realpath($list_path);

    	// インストールパスを取得 :: /home/cs/www/cs.com.dev
    	$list_path = '../';
    	$base_path = set_realpath($list_path);

    	// PDFライブラリ呼出
    	$this->load->library('pdf');
    	$this->pdf->pdf_font($_tbl_data1, $pdflist_path, $base_path);



    	$this->view('graph/pdf_test.tpl');

    }

    public function pdf_invoice()
    {

    	$date = new DateTime();
    	$_end_day = date('t');														// 今月の末日を取得


    	// テストデータ
    	$_set_data1 = mt_rand(1, 50);
    	$_set_data2 = mt_rand(1, 100);
    	for ($cnt = $_end_day-1; $cnt >= 1; $cnt--)
    	{
    		$_set_data1 .= ',' . mt_rand(1, 50);
    		$_set_data2 .= ',' . mt_rand(1, 100);
    	}

    	$_tbl_data1 = explode(",", $_set_data1);
    	$_tbl_data2 = explode(",", $_set_data2);



    	// 雛形PDFのパス取得
    	$this->load->helper('path');
    	$list_path = '../public/images/pdf/receipt_list.pdf';
    	$pdflist_path = set_realpath($list_path);

    	// インストールパスを取得 :: /home/cs/www/cs.com.dev
    	$list_path = '../';
    	$base_path = set_realpath($list_path);

    	// PDFライブラリ呼出
    	$this->load->library('pdf');
    	$this->pdf->pdf_invoice($_tbl_data1, $pdflist_path, $base_path);



    	$this->view('graph/pdf_test.tpl');

    }

    public function pdf_demo()
    {

    	$date = new DateTime();
    	$_end_day = date('t');														// 今月の末日を取得


    	// テストデータ
    	$_set_data1 = mt_rand(1, 50);
    	$_set_data2 = mt_rand(1, 100);
    	for ($cnt = $_end_day-1; $cnt >= 1; $cnt--)
    	{
    		$_set_data1 .= ',' . mt_rand(1, 50);
    		$_set_data2 .= ',' . mt_rand(1, 100);
    	}

    	$_tbl_data1 = explode(",", $_set_data1);
    	$_tbl_data2 = explode(",", $_set_data2);



    	// 雛形PDFのパス取得
    	$this->load->helper('path');
    	$list_path = '../public/images/pdf/receipt_list.pdf';
    	$pdflist_path = set_realpath($list_path);

    	// インストールパスを取得 :: /home/cs/www/cs.com.dev
    	$list_path = '../';
    	$base_path = set_realpath($list_path);

    	// PDFライブラリ呼出
    	$this->load->library('pdf');
    	$this->pdf->pdf_demo($_tbl_data1, $pdflist_path, $base_path);



    	$this->view('graph/pdf_test.tpl');

    }

    public function pdf_html()
    {

    	$date = new DateTime();
    	$_end_day = date('t');														// 今月の末日を取得


    	// テストデータ
    	$_set_data1 = mt_rand(1, 50);
    	$_set_data2 = mt_rand(1, 100);
    	for ($cnt = $_end_day-1; $cnt >= 1; $cnt--)
    	{
    		$_set_data1 .= ',' . mt_rand(1, 50);
    		$_set_data2 .= ',' . mt_rand(1, 100);
    	}

    	$_tbl_data1 = explode(",", $_set_data1);
    	$_tbl_data2 = explode(",", $_set_data2);



    	// 雛形PDFのパス取得
    	$this->load->helper('path');
    	$list_path = '../public/images/pdf/receipt_list.pdf';
    	$pdflist_path = set_realpath($list_path);

    	// インストールパスを取得 :: /home/cs/www/cs.com.dev
    	$list_path = '../';
    	$base_path = set_realpath($list_path);

    	// PDFライブラリ呼出
    	$this->load->library('pdf');
    	$this->pdf->pdf_html($_tbl_data1, $pdflist_path, $base_path);



    	$this->view('graph/pdf_test.tpl');

    }
    // フォーム・バリデーションチェック
    private function _set_validation()
    {
    	$rule_set = array(
    	);

    	$this->load->library('form_validation', $rule_set);                     // バリデーションクラス読み込み
    }

}
