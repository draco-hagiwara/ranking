<?php
/*
 * application/helpers/csvdata_helper.php
 */

/*
 * 順位レポート画面よりCSVダウンロード処理
 */
if ( ! function_exists('csv_report_result'))
{
    function csv_report_result($query, $delim = ",", $newline = "\n", $enclosure = '"')
    {

        if ( ! is_object($query) OR ! method_exists($query, 'list_fields'))
        {
            show_error('You must submit a valid result object');
        }

        $out = '';

        // First generate the headings from the table column names
        foreach ($query->list_fields() as $name)
        {
            $out .= $enclosure . str_replace($enclosure, $enclosure . $enclosure, $name) . $enclosure . $delim;
        }

        $out = rtrim($out);
        // 行の末尾のカンマを削除
        $out = rtrim($out, $delim);
        $out .= $newline;

        // Next blast through the result array and build out the rows
        foreach ($query->result_array() as $row)
        {
        	foreach ($row as $key => $item)
            {

            	// 変換処理
            	switch ($key)
            	{
            		case "kw_searchengine":
            			if ($item == 0) {
            				$item = "Google";
            			} else {
            				$item = "Yahoo!";
            			}
            			break;

            		case "kw_device":
            			if ($item == 0) {
            				$item = "pc";
            			} else {
            				$item = "mobile";
            			}
            			break;

            		case "kw_matchtype":
            			if ($item == 0) {
            				$item = "完全一致";
            			} elseif ($item == 1) {
            				$item = "前方一致";
            			} elseif ($item == 2) {
            				$item = "ドメイン一致";
            			} else {
            				$item = "ルートドメイン一致";
            			}
            			break;

            		// その他
            		default:
            			$item = $item;

            	}

                $out .= $enclosure . str_replace($enclosure, $enclosure . $enclosure, $item) . $enclosure . $delim;
            }

            $out = rtrim($out);

            // 行の末尾のカンマを削除
            $out = rtrim($out, $delim);
            $out .= $newline;
        }

        // 最終行に改行を削除
        $out = rtrim($out, $newline);
        return $out;
    }
}

if ( ! function_exists('csv_from_resultxxxxxxxxxxxxxx'))
{
	function csv_from_resultxxxxxxxxxxxxxx($query, $delim = ",", $newline = "\n", $enclosure = '"')
	{

		if ( ! is_object($query) OR ! method_exists($query, 'list_fields'))
		{
			show_error('You must submit a valid result object');
		}

		$out = '';

		// First generate the headings from the table column names
		foreach ($query->list_fields() as $name)
		{
			$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
		}

		$out = rtrim($out);
		// 行の末尾のカンマを削除
		$out = rtrim($out, $delim);
		$out .= $newline;

		// Next blast through the result array and build out the rows
		foreach ($query->result_array() as $row)
		{
			foreach ($row as $item)
			{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure.$delim;
			}
			$out = rtrim($out);
			// 行の末尾のカンマを削除
			$out = rtrim($out, $delim);
			$out .= $newline;
		}

		// 最終行に改行を削除
		$out = rtrim($out, $newline);
		return $out;
	}
}

/*
 * 順位データ画面よりCSVダウンロード処理
 */
if ( ! function_exists('csv_toplist_result'))
{
	function csv_toplist_result($query, $delim = ",", $newline = "\n", $enclosure = '"')
	{

		if ( ! is_object($query) OR ! method_exists($query, 'list_fields'))
		{
			show_error('You must submit a valid result object');
		}

		$out = '';

		// First generate the headings from the table column names
		foreach ($query->list_fields() as $name)
		{
			if ($name != "ウォッチリストseq" && $name != "アカウントseq")
			{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
			}
		}

		$out = rtrim($out);
		// 行の末尾のカンマを削除
		$out = rtrim($out, $delim);
		$out .= $newline;

		// Next blast through the result array and build out the rows
		foreach ($query->result_array() as $row)
		{
			foreach ($row as $key => $item)
			{

				// 変換処理
				switch ($key)
				{
					case "ステータス(U)":
						if ($item == 0) {
							$item = "無効";
						} else {
							$item = "有効";
						}
						break;

					case "URL一致方式(U)":
						if ($item == 0) {
							$item = "完全一致";
						} elseif ($item == 1) {
							$item = "前方一致";
						} elseif ($item == 2) {
							$item = "ドメイン一致";
						} else {
							$item = "ルートドメイン一致";
						}
						break;

					// その他
					default:
						$item = $item;

				}

				if ($key != "ウォッチリストseq" && $key != "アカウントseq")
				{
					$out .= $enclosure . str_replace($enclosure, $enclosure . $enclosure, $item) . $enclosure . $delim;
				}
			}
			$out = rtrim($out);
			// 行の末尾のカンマを削除
			$out = rtrim($out, $delim);
			$out .= $newline;
		}

		// 最終行に改行を削除
		$out = rtrim($out, $newline);
		return $out;
	}
}

/*
 * 順位データ画面よりCSVダウンロード処理
 */
if ( ! function_exists('csv_toplist_resultxxxx'))
{
	function csv_toplist_resultxxxx($query, $delim = ",", $newline = "\n", $enclosure = '"')
	{

		if ( ! is_object($query) OR ! method_exists($query, 'list_fields'))
		{
			show_error('You must submit a valid result object');
		}

		$out = '';

		// First generate the headings from the table column names
		foreach ($query->list_fields() as $name)
		{
			if ($name != "ウォッチリストseq" && $name != "アカウントseq")
			{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
			}
		}

		$out = rtrim($out);
		// 行の末尾のカンマを削除
		$out = rtrim($out, $delim);
		$out .= $newline;

		// Next blast through the result array and build out the rows
		foreach ($query->result_array() as $row)
		{
			foreach ($row as $key => $item)
			{

				// 変換処理
				switch ($key)
				{
					case "ステータス":
						if ($item == 0) {
							$item = "無効";
						} else {
							$item = "有効";
						}
						break;

					case "URL一致方式":
						if ($item == 0) {
							$item = "完全一致";
						} elseif ($item == 1) {
							$item = "前方一致";
						} elseif ($item == 2) {
							$item = "ドメイン一致";
						} else {
							$item = "ルートドメイン一致";
						}
						break;

					case "検索エンジン選択":
						if ($item == 0) {
							$item = "Google";
						} else {
							$item = "Yahoo!";
						}
						break;

					case "デバイス選択":
						if ($item == 0) {
							$item = "ＰＣ版";
						} else {
							$item = "モバイル版";
						}
						break;

					// その他
					default:
						$item = $item;

				}


				if ($key != "ウォッチリストseq" && $key != "アカウントseq")
				{
					$out .= $enclosure . str_replace($enclosure, $enclosure . $enclosure, $item) . $enclosure . $delim;
				}
			}
			$out = rtrim($out);
			// 行の末尾のカンマを削除
			$out = rtrim($out, $delim);
			$out .= $newline;
		}

		// 最終行に改行を削除
		$out = rtrim($out, $newline);
		return $out;
	}
}