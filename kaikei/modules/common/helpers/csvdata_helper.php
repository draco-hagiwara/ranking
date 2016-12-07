<?php
/*
 * application/helpers/csvdata_helper.php
 */

if ( ! function_exists('csv_from_result'))
{
    function csv_from_result($query, $delim = ",", $newline = "\n", $enclosure = '"')
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