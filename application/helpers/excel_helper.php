<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
* Excel library for Code Igniter applications
* Author: Derek Allard, Dark Horse Consulting, www.darkhorse.to, April 2006
*/

function to_excel($query, $filename='exceloutput')
{
     $headers = ''; // just creating the var for field headers to append to below
     $data = ''; // just creating the var for field data to append to below

     $obj =& get_instance();

     $fields = $query->list_fields();
     if ($query->num_rows() == 0) {
          echo '<p>The table appears to have no data.</p>';
     } else {
          foreach ($fields as $field) {
             $headers .= humanize($field) . ",";
          }

          foreach ($query->result() as $row) {
               $line = '';
               foreach($row as $value) {
                    if ((!isset($value)) OR ($value == "")) {
                         $value = ",";
                    } else {
                         $value = str_replace('"', '""', $value);
                         $value = '"' . $value . '"' . ",";
                    }
                    $line .= $value;
               }
               $data .= trim($line)."\n";
          }

          $data = str_replace("\r","",$data);

          header("Content-type: application/x-msdownload");
          header("Content-Length: " . strlen("$headers\n$data"));
          header("Content-Disposition: attachment; filename=$filename.csv");
          echo "$headers\n$data";
     }
}

/* End: excel_pi.php */