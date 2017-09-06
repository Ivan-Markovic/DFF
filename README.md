# DFF
One of  Best Path traversal and PRL attack tools  by TS/SCI Security (year 2008). Also tool is included on BackTrack 4 and OWASP Phoenix/Tools Project.

-= DFF scanner
-= ivanm@security-net.biz


-= Description:

DFF (Default File & Folder) scanner is tool for finding path of predictable resource 
locations, that is common names of files and folders on web servers.
There is many options that can help in scanning like: detecting error pages, proxy 
usage, distionary attack, etc ...

DFF is writen in PHP and have two script files. The first one (dff.main.class.php) 
is main class with all logic and second one (dff.files.class.php) is extension
for files scanning. DFF scanner needs cURL library for working.


-= Help:

You need to include base classes in Yours script and than create object with
settings and methods that You need.

Example:

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


When You finish Yours script just call it trought browser and look at report.
