<?php

/* 
	DFF Scaner v 1.1 [php5] [ 5/12/2007 8:19 PM ]
	- Files class [files scan]
	
	Ivan Markovic < ivan[dot]markovic[at]netsec[dot]rs >
	http://security-net.biz
    
    About: Dictionary scanner for common files on web locations.
           - extended from main class

    Usage:
    -------------------------------------------------------------------
        require_once 'dff.files.class.php';

        // Create object
        $dff = new dffFiles();
        // Chose url to scan
        $dff->url = 'http://www.security-net.biz/';
        // Chose first letters
        $dff->names_by_letter = array('w','a','t','b');
        // Custom names
        $dff->custom_names = array('admin', 'blog', 'forum', 'crm');
        // Use dictionary file, select mode
        $dff->use_dic_file = 'MERGE_CUSTOM';
        // Path od dictionary file
        $dff->dic_file = 'dic.txt';
        // cURL
            // Use proxy
            $dff->curl_proxing = '';
            // Follow redirection
    		$dff->curl_follow = 'YES';
    		// Nobody
    		$dff->curl_nobody = 'YES';
    	    // Set user agent
    		$dff->curl_useragent = '';
    		// Set reffer
    		$dff->curl_reffer = '';
        // Chose level of in_deep
        $dff->in_deep = 1;
        // Dislay as fonded pages that are similar to custom 404
        $dff->display_similiar = 0;
        // Set custom 404, leave empty for discover
        $dff->c404 = '';
        // Display message with mommentary url
        $dff->trying = 0;
        // FILE scan
            // Chose first letters
            $dff->file_names_by_letter = array('w','a','t','b');
            // Custom names
            $dff->file_custom_names = array('admin', 'blog', 'forum', 'crm');
            // Use dictionary file, select mode
            $dff->file_use_dic_file = 'MERGE_CUSTOM';
            // Path od dictionary file
            $dff->file_dic_file = 'dic_file.txt';
        // Custom extensions
        $dff->file_extensions = array('.bak','.dat','.txt');    
        // Scan
        $dff->scan_it();
    -------------------------------------------------------------------   
    
    Comment: Please be free to send remarks and ideas. 
    
    
    Legal staff:
     
     Use it at your own risk. I will not be responsible 
     for any damages done that might result from using 
     this script. It is written for educations purpose.
     
    
    Thanks people from: http://sla.ckers.org/forum/
    
    
    #  [License]
	#
	#  This program is free software; you can redistribute it and/or modify
	#  it under the terms of the GNU General Public License as published by
	#  the Free Software Foundation; either version 3 of the License, or
	#  any later version.
	#
	#  This program is distributed in the hope that it will be useful,
	#  but WITHOUT ANY WARRANTY; without even the implied warranty of
	#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	#  GNU General Public License for more details.
	#
	#  You should have received a copy of the GNU General Public License
	#  along with this program; if not, write to the Free Software
	#  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	#
	#  See 'LICENSE' for more information.

*/

// Include main class
require_once 'dff.main.class.php'; 

class dffFiles extends dff {
    
    protected $default_names_temp = array();    // Temp buff for default names
    
    var $file_names_by_letter = array();	    // Scan by letter
    var $file_custom_names = array();           // Custom names of folders to scan
    var $file_use_dic_file = 'NO';              // Use dic file (NO,YES,MERGE_CUSTOM,MERGE_DEFAULT)
    var $file_dic_file = '';                    // Dictionary file
    var $file_dic_file_delimiter = ',';         // Delimiter (',','\n',...) 
    
    var $file_extensions = array(
                                 '.dat','.phps','.php~','.php','.reg','.txt','.bak','.bak~','.php.bak','.db','.rdf','.pl',
                                 '.cgi','.class','.sql','.sh','.bat','.exe','.dll','.mdb','.xls','.doc','.bin','.zip','.tar.gz',
                                 '.inc','.ini','.conf','.pcf','.xls','.pwd','.inf','.pgp','.skr','.asa','.batch','.cron','.ica',
                                 '.cfg','.tpl','.old','.new','.'
                                 );
    
    function dffFiles() {
        parent::__construct();
    }
    
    public function scan_it() {
        
        // Make files array
        function array_make($val, $file_extensions) {
            
            $default_names_temp = array();
            
            foreach($file_extensions as $f_key => $f_val) {
                $val_temp = $val . $f_val;
                array_push($default_names_temp,$val_temp);    
            }
            
            return $default_names_temp;
            
        }
        
        // Scan for folders
        parent::scan_it();
        
        
        // Prepare scan function for files scan
        $this->use_dic_file = $this->file_use_dic_file;
        $this->dic_file = $this->file_dic_file;
        $this->dic_file_delimiter = $this->file_dic_file_delimiter;
        $this->custom_names = $this->file_custom_names;
        
        
        // Prepare names
        parent::prepare_names();
        
        
        // Create names of files
        $default_names_temp = '';
        foreach ($this->default_names as $d_key => $d_val) {
            
            if ($this->names_by_letter != '') {
                if (in_array($d_val[0],$this->file_names_by_letter)) {
                    $default_names_temp .= "," . implode(",",array_make($d_val, $this->file_extensions));
                }
            } else {
                $default_names_temp .= "," . implode(",",array_make($d_val, $this->file_extensions));
            }
        }
        
        
        // Prepare scan function
        $this->default_names = explode(",",$default_names_temp);
        $this->found_in_levels = $this->founded;
        
        
        // Scan it
        echo '<hr color="blue" /> Switch to files scanning ... <hr color="blue" />';
        parent::scan_levels();
        echo '<hr color="green" /><strong> Job done. </strong> Time: ' . date("l dS of F Y h:i:s A") . '<br /><hr color="green" />';
        
    }
    
    
}
