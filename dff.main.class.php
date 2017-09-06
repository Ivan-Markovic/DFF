<?php

/* 
	DFF Scaner v 1.1 [php5] [ 5/12/2007 7:53 PM ]
	- Main class [folder scan]
	
	Ivan Markovic <ivanm@security-net.biz>
	http://security-net.biz
    
    About: Dictionary scanner for common folders on web locations.
    
    To do:
    
        - better recognizer

    Usage:
    -------------------------------------------------------------------
        require_once 'dff.main.class.php';

        // Create object
        $dff = new dff();
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


class dff {
    
    protected $default_names = array();         // Default names of folders to scan
    protected $found = array();                 // Found in first level
    protected $found_in_levels = array();       // Found in levels
    
    var $url;                                   // Url to scan
    var $names_by_letter = array();	            // Scan by letter, only apply to first level
    var $custom_names = array();                // Custom names of folders to scan
    var $use_dic_file = 'NO';                   // Use dic file (NO,YES,MERGE_CUSTOM,MERGE_DEFAULT)
    var $dic_file = '';                         // Dictionary file
    var $dic_file_delimiter = ',';              // Delimiter (',','\n',...)
    var $in_deep = 0;                           // Level of deep scan
    var $trying = 0;                            // Print mommentary url
    var $c404 = '';                             // Buffer for custom 404
    var $display_similiar = 0;                  // Switch for displaying message and push into array of similar 404 page
    
    var $founded = array();                     // Founded
    
    // Curl settings
    
    var $curl_proxing = '';                     // Adress of proxy
    var $curl_follow = 'YES';                   // Follow redirections (YES, NO);
    
    var $curl_nobody = 'YES';                   // Get body or not (YES, NO),
                                               
    var $curl_useragent = '';                   // Set user agent
    var $curl_reffer = '';                      // Set reffer
    
    
    function dff() {
        
        // Check cURL
        if (!extension_loaded('curl')) {
            die ("This class can not work without CURL PHP module!");
        }
        
        // System
        set_time_limit(0); 
        
        // Default names
        $this->default_names = array(
                                     'sub','domen','domain','user','admin','clients','cms','config','configuration','adm',
							         'database','administrator','db','social','wsw','forum','forums','groups','lab','secret',
							         'secure','support','test','wiki','svn', 'ftp','www','archive','old','new','download',
							         'downloads','archives','arhiva','arh','blog','blogs','chat','billing','mail','docs',
							         'lists','help','helpdesk','intranet','private','resources','res','crm','login','demo',
							         'manual','faq','sec','testing','site','services','testing-services', 'backand','tests',
							         'file','files','syscp','ensim','demos','vb','nl','en','be','uk','sr','_','__','___','@',
							         '01','02','03','0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h',
							         'i','j','k','l','m','n','o','p','q','r','t','u','v','w','x','y','z','phpmyadmin','database',
							         'mirror','proxy','dev','mp3','images','webcam','vpn','frontpage','stats','webstats','bin',
							         'plesk-stat','intranet','backup','backups','mysql','sql','data','csv','msn','webmail','doc',
							         'vmware','netware','novell','mambo','joomla','phpnuke','squirellmail','mail','auth',
							         'webadmin','groupwise','portal','remote','admin_','default','register','_vti_cnf','_vti',
							         'maildir','winnt','ws_ftp','desktop','wap','share','torrent','torrents',
							         'webalizer','bash_history','shell','polly','.cobalt','bash','gateway','terminal','nt','xp',
							         'lan','wan','eth0','spam','smtp','hr','sharepoint','oracle','peoplesoft','siebel','erp',
							         'customer','customers','squid','checkpoint','foundry','wireless','outside','inside','snort',
							         'outlook','lotus','voip','aa','aaa','aaaa','cc','ccc','cccc','001','0001','00001','11',
							         '111','1111','_1','_11','area51','_area51','UFO','confidential','007','777','classified',
							         'mediafiles','_backup','plugins','plug','uni','photoshop','dreamweaver','flash','swf','fla',
							         'myfiles','mydownloads','myuploads','newtorents','movies','video','pager','allusers',
							         'databases','db1','db2','db3','db01','db02','db03','musicfiles','wmv','porn','girls',
							         'camserver','javaserver','trial','warez','0day','night','love','sex','letters','documents',
							         'intra','corporate','corp','shares','stocks','stock','stockphoto','getty','designs','psd',
							         'psds','gifs','img','oscommerce','hacks','hacked','hacker','cracker','crackers','1337',
							         'leet','phorum','phpbb','bbcode','phpboard','ebay','e-bay','bay','powerseller','seller',
							         'zero','one','two','zeroday','spider','spidered','spiders','ai','neo','matrix','quake',
							         'doom','devil','bsd','tracker','urchin','google','torch','dev1','dev1','stager','stage',
							         'stage1','prelive','live','server1','server2','backupserver','serverbackup','classes',
							         'java','oop','devlnull','bhistory','wikipedia','wiki','pre_','_pre','_db','_database',
							         '_old','_stored','_bak','personal','_personal','_pers','pers','write','country','lookup',
							         'scripts','googleearth','truncate','possix','filehunter','dll','make','makefile','p2p',
							         'spamcop','server','dat','index','list','log','logs','back','users','table','robots',
							         'login','shop','store','web','install','installer','sys','vpn','demo','servlet','sendmail',
							         'mod','upload','uploads','auth','ttawlogin','login','sign','perform','clients','isapi',
							         'register','delete','del','desktop','winnt','ws_ftp','eudora','slapd','sites','client',
							         'wand','sysprep','htpasswd','htgroup','secring','passlist','passes','pw','trillian',
							         'master','shell','cobalt','agent','polly','mainframe','nodes','tor','exec','default','pgp',
							         'crypt','help','terminal','.htpasswd','.htaccess','htaccess','book','books' 
							         );
							         
							         
    }
    
    // Process headers, recognizer, etc ...
    private function process_headers($buffer,$df_val) {
        
        if (stripos($buffer,'Not Found') === false and $this->curl_nobody == 'YES') {
            
            echo '<strong>' . htmlspecialchars($df_val) . '</strong><hr color="red" />';
          	flush();
          	
          	array_push($this->found,$df_val."/");
          	
          	// Buffer for all founded
            array_push($this->founded,$df_val."/");
            
        } elseif ($this->curl_nobody == 'NO') {
            
            // Check if page without headers similiar as custom founded 404
            if ($this->woh($df_val) == $this->c404) {
                
                if ($this->display_similiar == 1) {
                    echo '<strong>' . htmlspecialchars($df_val) . '</strong> | page is similiar to custom/default 404<hr color="red" />';
              	    flush();
              	
              	    array_push($this->found,$df_val."/");
              	
              	    // Buffer for all founded
                    array_push($this->founded,$df_val."/"); 
                    
                }  
                 
            } elseif (stripos($buffer,'Not Found') === false) {
                
                echo '<strong>' . htmlspecialchars($df_val) . '</strong><hr color="red" />';
          	    flush();
          	
          	    array_push($this->found,$df_val."/");
          	
          	    // Buffer for all founded
                array_push($this->founded,$df_val."/"); 
            }
            
        }
    }
    
    // Prepare names
    public function prepare_names() {
        
        // If dic file in use
		if (file_exists($this->dic_file) && $this->use_dic_file != 'NO') {
		    
		    $dic_file_buff = file_get_contents($this->dic_file);
		    
		    switch ($this->use_dic_file) {
		        
		        // Only from dic file
		        case 'YES': 
		        default:
		            $this->custom_names = explode($this->dic_file_delimiter,$dic_file_buff);
		            break;
		        
		        // From dic file and custom names    
		        case 'MERGE_CUSTOM': 
		            $this->custom_names = array_merge($this->custom_names,explode($this->dic_file_delimiter,$dic_file_buff));
		            break;
		        
		        // From dic file and default names   
		        case 'MERGE_DEFAULT': 
		            $this->custom_names = array_merge($this->default_names,explode($this->dic_file_delimiter,$dic_file_buff));
		            break;
		            
		    }           
		    
		} else {
		    
		    if (!file_exists($this->dic_file) && $this->use_dic_file != 'NO') die("Dictionary file doesn`t exists!");
		    
		}
							  
		// If we have custom names then overide default names
        if (!empty($this->custom_names)) $this->default_names = $this->custom_names;
        // Remove duplicates
        $this->default_names = array_unique($this->default_names);
        
    }
    
    // Scan it
    public function scan_it() {
        
        // Check host
        function check_host($url) {
            
            $urlz = explode("/", $url);
            $urlz_tmp = $urlz[2];
            
            $urlz_tmp_ip = ip2long($urlz_tmp);
            
            if($urlz_tmp_ip != -1 and $urlz_tmp_ip !== FALSE) {
                    if ($urlz_tmp == gethostbyaddr($urlz_tmp)) {
                        return 0;
                    } else {
                        return 1;                
                    }
            } else {
                if ($urlz_tmp == gethostbyname($urlz_tmp)) {
                    return 0;
                } else {
                    return 1;                
                }
            }

        }
        
        // Die if url empty
        if ($this->url == '') die("Url can`t be empty!");
        // If host doesn`t exists
        if (check_host($this->url) == 0) die("Host doesn`t exists!");
        
        // Push main url to founded
        array_push($this->founded,$this->url);
        
        // Make final names array
        $this->prepare_names();
        
        echo '<h1> DFF scaner v 1.1</h1>Developer: Ivan Markovic <br /> Site: http://security-net.biz <hr color="green" />
             Started at: '. date("l dS of F Y h:i:s A") . '<br /><hr color="green" />';
        
        // Check for custom error page
        if ($this->c404 == '') {
            $this->check_404();
        } else {
            $this->c404 = md5($this->c404);
        }
        
        // Scan first level
        $this->scan_level_zero();
        
        // For in_deep scan
        while ($this->in_deep > 0) {
            
            // Switch founded buffers 
            $this->found_in_levels = $this->found;
            $this->found = array();
            
            // Call scan function
            $this->scan_levels(); 
            $this->in_deep--; 
        }
        
        echo '<hr color="green" /><strong> Job done. </strong> Time: ' . date("l dS of F Y h:i:s A") . '<br /><hr color="green" />';
        flush();
    }
    
    // Scan first level
    private function scan_level_zero() {
        
        $buffer = '';
            
        foreach ($this->default_names as $df_key => $df_val) {
            
        	if (!empty($this->names_by_letter)) { // If test by letter
        		
        	    sort($this->default_names);
        		
    			if (in_array($df_val[0],$this->names_by_letter)) {
    			    
    			      if ($this->trying == 1) {
        		         echo "Trying: $this->url$df_val<hr color='black'>";
        		         flush();
        		      }
    			    
    				  $curl_handle = curl_init();
    				  curl_setopt($curl_handle,CURLOPT_URL,$this->url.$df_val);
    				  
    				  // If we use proxy
    				  if ($this->curl_proxing != '') {
    				      curl_setopt($curl_handle, CURLOPT_PROXY, $this->curl_proxing);  
    				  }
    				  
    				  // Follow redirection
    				  if ($this->curl_follow == 'YES') {
    				      curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);  
    				  } else {
    				      curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 0);
    				  }
    				  
    				  // Nobody
    				  if ($this->curl_nobody == 'YES') {
    				      curl_setopt($curl_handle, CURLOPT_NOBODY, 1);  
    				  } else {
    				      curl_setopt($curl_handle, CURLOPT_NOBODY, 0);
    				  }
    				  
    				  // Set user agent
    				  if ($this->curl_useragent != '') {
    				      curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->curl_useragent);  
    				  }
    				  
    				  // Set reffer
    				  if ($this->curl_reffer != '') {
    				      curl_setopt($curl_handle, CURLOPT_REFERER, $this->curl_reffer);  
    				  }
    				  
    				  curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
    				  curl_setopt($curl_handle, CURLOPT_HEADER, 1);
    				  $buffer = curl_exec($curl_handle);
    				  curl_close($curl_handle);
    				  
    				  $this->process_headers($buffer,$this->url.$df_val);
    			}
        	} else {
        	    
        	    if ($this->trying == 1) {
    		        echo "Trying: $this->url$df_val<hr color='black'>";
    		        flush();
    		    }
        	    
                $curl_handle = curl_init();
        		curl_setopt($curl_handle,CURLOPT_URL,$this->url.$df_val);
        		
        		// If we use proxy
			    if ($this->curl_proxing != '') {
			        curl_setopt($curl_handle, CURLOPT_PROXY, $this->curl_proxing);  
			    }
			    
			    // Follow redirection
			    if ($this->curl_follow == 'YES') {
			        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);  
			    } else {
			        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 0);
			    }
			  
			    // Nobody
			    if ($this->curl_nobody == 'YES') {
			        curl_setopt($curl_handle, CURLOPT_NOBODY, 1);  
			    } else {
			        curl_setopt($curl_handle, CURLOPT_NOBODY, 0);
			    }
			  
			    // Set user agent
			    if ($this->curl_useragent != '') {
			        curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->curl_useragent);  
			    }
			  
			    // Set reffer
			    if ($this->curl_reffer != '') {
			        curl_setopt($curl_handle, CURLOPT_REFERER, $this->curl_reffer);  
			    }
    				  
        		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
        		curl_setopt($curl_handle, CURLOPT_HEADER, 1);
        		$buffer = curl_exec($curl_handle);
        		curl_close($curl_handle);
        		
        		$this->process_headers($buffer,$this->url.$df_val);
        		
        	} // else
	    
        } // foreach  
        
    } // function scan_level_zero
    
    // Scan all levels
    public function scan_levels() {
        
        foreach ($this->found_in_levels as $f_key => $f_val) {
        
            foreach ($this->default_names as $df_key => $df_val) {
                
                $buffer = '';
                
                $curl_handle = curl_init();
        		curl_setopt($curl_handle,CURLOPT_URL,$f_val.$df_val);
        		
        		if ($this->trying == 1) {
        		    echo "Trying: $f_val$df_val<hr color='black'>";
        		    flush();
        		}
        		
        		// If we use proxy
			    if ($this->curl_proxing != '') {
			        curl_setopt($curl_handle, CURLOPT_PROXY, $this->curl_proxing);  
			    }
			    
			    // Follow redirection
			    if ($this->curl_follow == 'YES') {
			        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);  
			    } else {
			        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 0);
			    }
			  
			    // Nobody
			    if ($this->curl_nobody == 'YES') {
			        curl_setopt($curl_handle, CURLOPT_NOBODY, 1);  
			    } else {
			        curl_setopt($curl_handle, CURLOPT_NOBODY, 0);
			    }
			  
			    // Set user agent
			    if ($this->curl_useragent != '') {
			        curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->curl_useragent);  
			    }
			  
			    // Set reffer
			    if ($this->curl_reffer != '') {
			        curl_setopt($curl_handle, CURLOPT_REFERER, $this->curl_reffer);  
			    }
			    
        		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
        		curl_setopt($curl_handle, CURLOPT_HEADER, 1);
        		$buffer = curl_exec($curl_handle);
        		curl_close($curl_handle);
        		
        		$this->process_headers($buffer,$f_val.$df_val);
                
            }
        }
            
    } // function scan_levels
    
    
    // Check custom error page
    private function check_404() {
        
        $randval = md5("some_rand_name".time());
    
        $curl_handle = curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$this->url.$randval);
		
		// If we use proxy
	    if ($this->curl_proxing != '') {
	        curl_setopt($curl_handle, CURLOPT_PROXY, $this->curl_proxing);  
	    }
	    
	    // Follow redirection
	    curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);  
	  
	    // Nobody
	    curl_setopt($curl_handle, CURLOPT_NOBODY, 0);
	  
	    // Set user agent
	    if ($this->curl_useragent != '') {
	        curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->curl_useragent);  
	    }
	  
	    // Set reffer
	    if ($this->curl_reffer != '') {
	        curl_setopt($curl_handle, CURLOPT_REFERER, $this->curl_reffer);  
	    }
			  
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl_handle, CURLOPT_HEADER, 0);
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		
		$this->c404 = md5($buffer);
		
    }
    
    // Return page buffer without headers
    private function woh($url) {
        
        $curl_handle = curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$url);
		
		// If we use proxy
	    if ($this->curl_proxing != '') {
	        curl_setopt($curl_handle, CURLOPT_PROXY, $this->curl_proxing);  
	    }
	    
	    // Follow redirection
	    curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);  
	  
	    // Nobody
	    curl_setopt($curl_handle, CURLOPT_NOBODY, 0);
	  
	    // Set user agent
	    if ($this->curl_useragent != '') {
	        curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->curl_useragent);  
	    }
	  
	    // Set reffer
	    if ($this->curl_reffer != '') {
	        curl_setopt($curl_handle, CURLOPT_REFERER, $this->curl_reffer);  
	    }
			  
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl_handle, CURLOPT_HEADER, 0);
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);
		
		return md5($buffer);
		
    }
}
