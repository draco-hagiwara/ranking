<?php

class System extends MY_Controller
{

    /*
     *  システム関連
     *
     *    > DB & System バックアップ
     *    > セッション情報削除
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->library('lib_auth');
        $this->lib_auth->check_session();

    }

    // 初期表示
    public function index()
    {

        $this->view('system/index.tpl');

    }

    // DB & System バックアップ
    public function backup()
    {

        // sh に記述
        $arg = 1;                                                    // 判定用に引数を渡す

        // DBのバックアップ
        $app_path = "/var/www/ranking/backup/";
        $strCommand = $app_path . 'backup4mysql.sh  "'.$arg.'"';
        exec( $strCommand );

        // システムのバックアップ (手動データ作成)
        $app_path = "/var/www/ranking/backup/";
        $strCommand = $app_path . 'backup4pg.sh  "'.$arg.'"';
        exec( $strCommand );

        $this->view('system/index.tpl');

    }

    // セッション情報削除 (一か月前)
    public function sess_destroy()
    {

        // 一か月前のセッションを削除
        $now_time = time();
        $del_time = strtotime('-1 hour' , $now_time);

        $this->load->model('Ci_sessions', 'sess', TRUE);
        $this->sess->destroy_session($del_time);

        $this->view('system/index.tpl');

    }

    // グラフの キャッシュ (Memcached) を削除する
    public function memcached_delete()
    {

        $this->load->driver('cache', array('adapter' => 'memcached'));

        if ($this->cache->is_supported('memcached'))
        {
            $this->cache->delete('key_salesman');                               // 月次売上グラフ：全体
            $this->cache->delete('key_monthly');                                // 月次売上グラフ：担当者別
            $this->cache->delete('key_accounting');                             // 月次売上グラフ：課金方式別
        }

        $this->view('system/index.tpl');

    }

}
