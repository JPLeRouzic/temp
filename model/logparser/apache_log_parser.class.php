<?php

class apache_log_parser {
    
    /**
     * Apache access log file
     * @var String
     * @access private
     */
    private $access_log = "";
    
    /**
     * Apache error log file
     * @var String
     * @access private
     */
    private $error_log = "";
    
    /**
     * @var String
     * @access private
     */
    private $content = "";
    
    /**
     * @var Array
     * @access private
     */
    private $data = array();
    
    /**
     * @var Array
     * @access private
     */
    private $rows = array();
    
    /**
     * @var Array
     * @access private
     */
    private $jplr = array();
    
    /**
     * @var Array
     * @access private
     */
    private $stats = array();
    
    /**
     * @var Array
     * @access private
     */
    private $statstmp = array();
    
    /**
     * @var Array
     * @access private
     */
    private $out = array();
    
     /**
     * @var Array
     * @access private
     */
    private $tags_tab = array();
    
    /**
     * @param String $access_log
     * @access public
     */
    public function __construct($path, $log_access, $log_error) {
        $this->access_log = $path . $log_access;
        $this->error_log = $path . $log_error;
        $this->content = file_get_contents($this->access_log);
        if(!$this->content) {
            echo "Can not open file: '" . $this->access_log . "' <br>Check the file name!";
            return;
        }
        // parse the Apache access log file and keep only the rows that interest us
        $this->parse_data1();
        // parse those rows of the access log file and keep only rows with HTTP errors
        $this->parse_data2();
        // Fill in some structures 
        $this->paralyse_log();
    }
    
    /**
     * Parse Apache log data and keep only rows of days that interest us
     * @access private
     */
    private function parse_data1() {
        $input = explode("\n", $this->content);
        $array_size = sizeof($input);
        $todate = getdate();
        $currentday = $todate['mday'];
        $nbrows = 0;
        for($i = $array_size;
        $i >= 1;
        ) {
            $i --;
            $back = explode(" ", $input[$i]);
            if(!isset($back['3'])) {
                continue;
            }
            // splitting date from date + time
            $day_time = explode(":", $back['3']);
            $day1 = $day_time[0];
            $day2 = substr($day1, 1);
            $day = explode("/", $day2);
            //           echo '<br>day: ' . $day[0] . ', current day: ' . $currentday;
            // We are only interested since 7 days back in the logs
            if($day <($currentday - 7)) {
                continue;
            } else {
                array_push($this->rows, $input[$i]);
                $nbrows ++;
            }
        }
    }
    
    /**
     * Parse rows data and keep only HTTP errors
     * @access private
     */
    private function parse_data2() {
        $this->stats['rows'] = 0;
        $input = $this->rows;
        $array_size = sizeof($input);
//        $todate = getdate();
//        $currentday = $todate['yday'];
        for($i = 0;
        $i < $array_size;
        $i ++ ) {
            $this->stats['rows'] ++;
            $refeerer0 = explode("+", $input[$i]);
            $back = explode(" ", $input[$i]);
            if(!isset($back['8'])) {
                //new line in file / empty line
                $back8 = "";
            } else {
                $back8 = $back['8'];
            }
            if(!isset($back['10'])) {
                $back10 = "";
            } else {
                $back10 = $back['10'];
            }
            if(!isset($refeerer0[2])) {
                $refeer = "";
            } else {
                $refeer = $refeerer0[2];
            }
            // splitting date from date + time
            $day_time = explode(":", $back['3']);
            $day1 = $day_time[0];
            $day = substr($day1, 1);
            $output = array('IP_client' =>$back['0'],
                            'date' =>$day,
                            'HTTP_code' =>$back['5'],
                            'loc_url' =>$back['6'],
                            'type' =>$back8,
                            'refeerer' =>$back10,
                            'search_engine' =>$refeer);
            $this->jplr[$i] = $output;
            if(//these are not errors
            $back8 == '200' || // OK
            $back8 == '206' || // Partial Content
            $back8 == '301' || // Moved Permanently (redirect permanently)
            $back8 == '302' || // Found (in cache / or redirect after POST or so)
            $back8 == '304' // Not Modified (in cache)
           ) {
                continue;
            } else {
                $this->data[] = $output;
            }
        }
    }
    
    /**
     * parse the log file
     * @access private
     */
    private function paralyse_log() {
        $type = array();
        $detail = array();
        $size_of_data = sizeof($this->data);
        for($dataIndex = 0;
        $dataIndex < $size_of_data;
        $dataIndex ++ ) {
            if(isset($type[$this->data[$dataIndex]['type']])) {
                $type[$this->data[$dataIndex]['type']] ++;
            } else {
                $type[$this->data[$dataIndex]['type']] = 1;
            }
            if(isset($detail[$this->data[$dataIndex]['loc_url']])) {
                $detail[$this->data[$dataIndex]['loc_url']] ++;
            } else {
                $detail[$this->data[$dataIndex]['loc_url']] = 1;
                $this->stats['content_type'][$this->data[$dataIndex]['loc_url']] = $this->data[$dataIndex]['type'];
            }
        }
        // statistics + types
        $this->stats['analyzed_rows'] = sizeof($this->data);
        $key = array_keys($type);
        $this->stats['rowtypeCount'] = sizeof($key);
        for($x = 0;
        $x < $this->stats['rowtypeCount'];
        $x ++ ) {
            $temp = array('name' =>$key[$x],
                          'value' =>$type[$key[$x]],
                          'pro' => round(($type[$key[$x]]* 100 / $this->stats['analyzed_rows']),
                          4). ' %');
            $this->stats['rowtype'][] = $temp;
            $this->statstmp = $temp;
        }
        $this->stats['errCount'] = sizeof($detail);
        array_multisort($detail, SORT_DESC, SORT_NUMERIC);
        $this->stats['err'] = $detail;
    }
    
    /**
     * @access private
     * @return String $out
     */
    private function table1() {
        $search_engine_tab = array();
        $refeerer_tab = array();
        $dataSize1 = sizeof($this->jplr);
        $out2 = "";
        $this->out = $out2 . '<br>dataSize: ' . $dataSize1;
        $url_tab = array();
        $date_tab = array();
        $detail_tab = array();
        
        /*
         * Fill in the tables
         * URL should begin with /News/
         * but not /News/alsresearchforum/
         * nor /News/system/ and neither /News/themes/
         */
        for($dataIndex = 0;
        $dataIndex < $dataSize1;
        $dataIndex ++ ) {
            $url007 = $this->jplr[$dataIndex]['loc_url'];
            $prest01 = strpos($url007, '/News/2');
            $prest02 = strpos($url007, '/News/tag');
            if(($prest01 !== false)||($prest02 !== false)) {
                $url_tab[$dataIndex] = $this->jplr[$dataIndex]['IP_client'];
                $date_tab[$dataIndex] = $this->jplr[$dataIndex]['date'];
                if($prest02 !== false) {
                    $this->tags_tab[$dataIndex] = $url007;
                } else {
                    $detail_tab[$dataIndex] = $url007;
                }
                $search_engine_tab[$dataIndex] = $this->jplr[$dataIndex]['search_engine'];
                $refeerer_tab[$dataIndex] = $this->jplr[$dataIndex]['refeerer'];
            }
        }
        //
        ///////////////////
        // client IP and number of hits
        $url_tab1 = array_count_values($url_tab);
        arsort($url_tab1);
        //Limit the number of events to the 20 most significants
        $url_tab2 = $this->no_more_20($url_tab1);
        $this->url_count_keys = array_keys($url_tab2);
        $this->url_count_values = array_values($url_tab2);
        //
        // Access per day
        $date_tab1 = array_count_values($date_tab);
        //	arsort($date_tab1) ; to order by increasing values
        $this->date_count_keys = array_keys($date_tab1);
        $this->date_count_values = array_values($date_tab1);
        //
        //
        // Search engines
        $search_engine_tab2 = array_count_values($search_engine_tab);
        arsort($search_engine_tab2);
        //Limit the number of events to the 20 most significants
        $search_engine_tab1 = $this->no_more_20($search_engine_tab2);
        $this->search_engine_count_keys = array_keys($search_engine_tab1);
        $this->search_engine_count_values = array_values($search_engine_tab1);
        //
        // Refeerers
        $refeerer_tab1 = array_count_values($refeerer_tab);
        arsort($refeerer_tab1);
        //Limit the number of events to the 20 most significants
        $refeerer_tab2 = $this->no_more_20($refeerer_tab1);
        $this->refeerer_count_keys = array_keys($refeerer_tab2);
        $this->refeerer_count_values = array_values($refeerer_tab2);
        //
        // Tags targeted
        $tags_tab1 = array_count_values($this->tags_tab);
        arsort($tags_tab1);
        //Limit the number of events to the 20 most significants
        $tags_tab2 = $this->no_more_20($tags_tab1);
        $this->tags_count_keys = array_keys($tags_tab2);
        $this->tags_count_values = array_values($tags_tab2);
        //
        // Local URLs targeted
        $detail_tab1 = array_count_values($detail_tab);
        arsort($detail_tab1);
        //Limit the number of events to the 20 most significants
        $detail_tab2 = $this->no_more_20($detail_tab1);
        $this->detail_count_keys = array_keys($detail_tab2);
        $this->detail_count_values = array_values($detail_tab2);
    }
    
    /**
     * @access private
     * @return String $out
     */
    private function Nb_access() {
        /////////////////// IP table
        
        /* List of single users
         $this->out = '<br/><br/><b>List of single users</b>';
         $dataSize2 = sizeof($this->url_count_keys);
         $this->out .= '<br><table>';
         $this->out .= '<tr><td><b><b>IPs</b></td><td><b>Nd of hits</td></tr>';
         for ($dataIndex = 0; $dataIndex < $dataSize2; $dataIndex++) {
         $this->out .= '<tr><td>' . $this->url_count_keys[$dataIndex] . '</td><td>' . $this->url_count_values[$dataIndex] . '</td></tr>';
         }
         $this->out .= '</table>';
         */
        //
        /////////////////// Number of access per day
        // Number of access per day
        //        $out = '<br/><br/><b>Number of access per day</b>' ;
        $dataSize3 = sizeof($this->date_count_keys);
        $this->out .= '<br><table><br>Number of access per day';
        $this->out .= '<tr><td><b><b>Day</b></td><td><b>Count</td></tr>';
        for($dataIndex = 0;
        $dataIndex < $dataSize3;
        $dataIndex ++ ) {
            $this->out .= '<tr><td>' . $this->date_count_keys[$dataIndex] . '</td><td>' . $this->date_count_values[$dataIndex] . '</td></tr>';
        }
        $this->out .= '</table>';
        //
        $this->out .= '</table>';
        /////////////////// list of refeerers
        $dataSize5 = sizeof($this->refeerer_count_keys);
        $this->out .= '<br><b>List of refeerers</b><br><table>';
        $this->out .= '<tr><td><b>Referer</b></td><td><b>Count</td></tr>';
        for($dataIndex = 0;
        $dataIndex < $dataSize5;
        $dataIndex ++ ) {
            $this->out .= '<tr><td>' . $this->refeerer_count_keys[$dataIndex] . '</td><td>' . $this->refeerer_count_values[$dataIndex] . '</td></tr>';
        }
        //
        $this->out .= '</table>';
        /////////////////// list of search engines
        $dataSize6 = sizeof($this->search_engine_count_keys);
        $this->out .= '<br><b>List of search engines</b><br><table>';
        $this->out .= '<tr><td><b>Referer</b></td><td><b>Count</td></tr>';
        for($dataIndex = 0;
        $dataIndex < $dataSize6;
        $dataIndex ++ ) {
            $this->out .= '<tr><td>' . $this->search_engine_count_keys[$dataIndex] . '</td><td>' . $this->search_engine_count_values[$dataIndex] . '</td></tr>';
        }
        $this->out .= '</table>';
        //
        /////////////////// tags count
        $dataSize4 = sizeof($this->tags_count_keys);
        $this->out .= '<br><table>';
        $this->out .= '<tr><td><b>Tags</b></td><td><b>Count</td></tr>';
        for($dataIndex = 0;
        $dataIndex < $dataSize4;
        $dataIndex ++ ) {
            $this->out .= '<tr><td>' . $this->tags_count_keys[$dataIndex] . '</td><td>' . $this->tags_count_values[$dataIndex] . '</td></tr>';
        }
        $this->out .= '</table>';
        //
        /////////////////// page count
        $dataSize7 = sizeof($this->detail_count_keys);
        $this->out .= '<br><table>';
        $this->out .= '<tr><td><b>Page</b></td><td><b>Count</td></tr>';
        for($dataIndex = 0;
        $dataIndex < $dataSize7;
        $dataIndex ++ ) {
            $this->out .= '<tr><td>' . $this->detail_count_keys[$dataIndex] . '</td><td>' . $this->detail_count_values[$dataIndex] . '</td></tr>';
        }
    }
    
    /**
     * @access private
     * @return String $out
     *
     * Output errors from $this->stats[]
     */
    private function error_output() {
        ///////////////////
        $this->out .= '<table>';
        // List of errors types
        $this->out .= '<br/><b>Parsed ' . $this->stats['rows'] . ' lines</b>. Found <b>' . $this->stats['analyzed_rows'] . '</b> errors with <b>' . $this->stats['rowtypeCount'] . '</b> type(s)';
        $this->out .= '<br><table>';

        foreach($this->statstmp as $val) {
            if(!isset($val['name'])) {
                continue;
            }
            $this->out .= '<tr><td>' . $val['name'] . '</td><td>' . $val['value'] . '</td><td>' . $val['pro'] . '</td></tr>';
        }
        $this->out .= '</table>';
        // List of common errors
        $this->out .= '<br/><b>List of the 20 most common errors</b> ( over ' . $this->stats['errCount'] . ' different errors ).';
        $this->out .= '<br><table>';
        $this->out .= '<tr><td><b>Rank</b></td><td><b>count</b></td><td><b>type</b></td><td><b>Error</b></td></tr>';
        $keys = array_keys($this->stats['err']);
        $keycount = count($keys);
        if($keycount > 20) {
            $keycount = 20;
            
            /* limit to 20 errors shown */
        }
        for($y = 0;
        $y < $keycount;
        $y ++ ) {
            if(trim($this->stats['err'][$keys[$y]])== '') {
                continue;
            }
            $this->out .= '<tr><td>' .($y + 1);
            // rank
            $this->out .= '</td><td>' . $this->stats['err'][$keys[$y]];
            // count
            if(isset($this->stats['content_type'][$keys[$y]])) {
                $this->out .= '</td><td>' . $this->stats['content_type'][$keys[$y]];
                // type
            }
            $this->out .= '</td><td>' . htmlentities($keys[$y]). '</td></tr>' . "\n";
            // error
        }
        $this->out .= '</table>';
        return $this->out;
    }
    
    /**
     * 40 last lines of log_error
     *
     * @access private
     * @return String $out
     *
     * Output errors from $this->stats[]
     */
    private function error_500() {
        $this->content = file_get_contents($this->error_log);
        if(!$this->content) {
            echo "Can not open file: '" . $this->access_log . "' <br>Check the name of error log file!";
            return;
        }
        $input = explode("\n", $this->content);
        $input_size = sizeof($input);
        $thisout = '<br>Error log file size:<br>' . $input_size;

        for($i = ($input_size - 1); $i >= 1; $i--)
            {
            $thisout .= '<br>' . $input[$i];
	    if($i < ($input_size - 40)) {
		break ;
		}
            }
        return '<br>Error log file content:<br>' . $thisout;
    }
    
    /**
     * @access public
     * @return String $out
     */
    public function output() {
        $out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . '<html xmlns="https://www.w3.org/1999/xhtml"><head><title>apache log parser</title>
		<style type="text/css">
		* {
			font-family: verdana;
			font-size: 11px;
			background-color: #EFEFEF;
			color: #101010;
			border:0;
			margin:0;
		}
		body {
			padding:20px;
		}
		table {
			/* border:1px solid #000000; */
			cell-spacing:none;
		}
		td {
			border-bottom:1px solid #000000;
			border-right:1px solid #000000;
			border-top:1px solid #000000;
		}

		tr > td {
			border-left:1px solid #000000;
		}
		</style></head><body>' . $this->table1(). $this->Nb_access(). $this->error_output(). $this->error_500(). '</body></html>';
        return $out;
    }
    
    /*
     * Publish only the 20 most significant events
     */
    private function no_more_20($refeerer_tab1) {
        $refeerer_tab = array_slice($refeerer_tab1, 0, 20);
        return $refeerer_tab;
    }
}
