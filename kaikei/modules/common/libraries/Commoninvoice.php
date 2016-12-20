<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 請求書作成用クラス
 */
class Commoninvoice
{

	/**
	 * 請求書番号：発行
	 *
	 * @param  array()
	 * @return int
	 */
	public static function issue_num($_issue_num)
	{

		$set_number  = $_issue_num['issue_num']
		    		 . $_issue_num['client_no']									// クライアントNO
		    		 . '-' . $_issue_num['customer_no']							// 顧客NO
		    		 . '-' . $_issue_num['issue_class']							// 一括発行=1,個別発行=2
		    		 . $_issue_num['issue_accounting']							// 「通常（固定or成果）:A」/「前受取:B」/「赤伝票:C」
		    		 . $_issue_num['issue_suffix']								// 枝番
		    		 . '-' . $_issue_num['issue_yymm'];							// 発行年月

		return $set_number;

	}

    /**
     * 消費税計算
     *
     * @param  int
     * @param  int
     * @param  int
     * @param  int
     * @return int
     */
	public static function cal_tax($_subtotal, $_issue_tax)
	{

		$zeigaku = $_subtotal * $_issue_tax['zeiritsu'];

		if ($_issue_tax['zeinuki'] == 0)										// 税抜計算
		{
			// 端数計算
			switch( $_issue_tax['hasuu'] )
			{
				case 0:

					// 切り上げ
					$tax_total = $zeigaku + 0.9;
					break;

				case 1:

					// 切り捨て
					break;

				case 2:
				default:

					// 四捨五入
					$tax_total = $zeigaku + 0.5;
					break;
			}

			$result = floor( $tax_total );

		} else {																// 税込計算

			$result = 0;

		}

		return $result;

	}

}