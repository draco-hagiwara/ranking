<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 受注案件作成用クラス
 */
class Lib_project
{


	/**
	 * 該当月の契約更新対象受注案件を取得する
	 *
	 * @return array()
	 */
	public static function get_renew_data()
	{

		$CI =& get_instance();

		// 当月の1～末日を計算
		$date = new DateTime();
		$project_date['str'] = $date->modify('first day of this months')->format('Y-m-d');
		$project_date['end'] = $date->modify('last day of this months')->format('Y-m-d');

		$CI->load->model('Project', 'pj', TRUE);
		$get_data = $CI->pj->get_renew_data($project_date, $_SESSION['c_memGrp'], 'seorank');

		return $get_data;

	}

}