<?php
/*
* This script should reside in root of the server, and shows as subpages reached by left menu all the txt, html files 
* which are inside directories resides in $pgs
* @author: k.gumulak
*/
//#################################################################################
$currentHostRoot = 'http://' . $_SERVER[HTTP_HOST];
$pgs = array("kosz", "angular", "git", "docker", "java", "spring", "python", "selenium", "mongodb", "test", "phplearn", "testing", "sql", "nosql", "plsql", "selenium", "serenity", "build", "bootstrap", "rwd", "vms", "links");
$base = basename(__FILE__);
$file_ext = array("txt", "html"); // ,"jpg"
//$classFile        = '/home/axdesign/www/cms/class.php';
//echo $classFile;
//die();
//include($classFile);
$currentFile = 'http://' . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
//#################################################################################
if (isset($_REQUEST['dir'])) {
    $dirToOps = $_REQUEST['dir'];
} else {
    $dirToOps = 'git';
}
//#################################################################################
if (isset($_REQUEST['act']) && ($_REQUEST['act'] === 'ed')) {
    echo '<div id="edytor">' . show_edit_box($_REQUEST['page'], $dirToOps) . '</div>';
}
//#################################################################################
if (isset($_REQUEST['act']) && ($_REQUEST['act'] === 'upd')) {
//$myfile = fopen($_REQUEST['page'], "w") or die("Unable to open file!");
    if (strpos($_REQUEST['page'], '.html') !== false) {
        file_put_contents($_REQUEST['page'], removeslashes(stripslashes($_POST['xcont'])));
    } else {
        file_put_contents($_REQUEST['page'], removeslashes($_POST['xcont']));
    }
}
//#################################################################################
if (isset($_REQUEST['act']) && ($_REQUEST['act'] === 'addFile')) {
    //$urlCurrent  = $_SERVER['REQUEST_URI'];
    echo '<div id="edytor">' . show_add_box($_REQUEST['page'], $_REQUEST['dest'], $dirToOps) . '</div>';
}
//#################################################################################
if (isset($_REQUEST['act']) && ($_REQUEST['act'] === 'addFileDo')) {
    $dest = $_POST['dest'];
    $current = $_POST['textOfNewFile'];
    if (!createNewFile($dest, $current)) {
        echo "problem with saving";
    } else {
        echo "saved..";
    }
}

//###############################################################################
function pagesAsHierarchyList($a, $dirxx) {
// echo count($a);

    if (strContains($a[0], "/")) {
        $separ = "/";
    } else {
        $separ = "\\";
    }


    $f = '';
//         for($i=0; $i<sizeOf($a); $i++){
//           $f.= $i.'::'.$a[$i].'<BR>';
//           continue; 
//         }
//     return $f;
//     $a = array_unique($a);
    $a = getUniqeFromArray($a);
    //$a            = array_filter($a);    
    asort($a);
    $prev = explode($separ, $a[0]);
//     echo $prev[count($prev)-2] ;
    $prev_dirx = $prev[count($prev) - 2];
    $f .= "<div class='cyc'>" . strtoupper($prev_dirx) . "</div><BR>";
    for ($i = 0; $i < sizeOf($a); $i++) {
//        $f.       = $i.'::'.$a[$i].'<BR>';
//        continue; 
        if ($i > 0) {
            $prev = explode($separ, $a[$i - 1]);
            $prev_dirx = $prev[count($prev) - 2];
        }
        $curr = explode($separ, $a[$i]);
        $dirx = $curr[sizeOf($curr) - 2]; //
        //$f .= '['.$dirx .']['.$prev_dirx."]<BR>";
        if ($dirx != $prev_dirx) {
            $f .= "" . "<BR>";
            $f .= "<div class='cyc'>" . strtoupper($dirx) . "</div>";
        }
        $filx = $curr[sizeOf($curr) - 1]; //
        //$f .= $filx."<BR>";
        if (!empty($filx)) {
            $f .= '<a class="menuLink" href="' . basename(__FILE__) . '?page=' . $a[$i] . '&dir=' . $dirxx . '" style="border:1px silver solid;background-color:#ccffcc;margin:2px;display:block">' . $filx . '</a>'; // &nbsp;&nbsp;<a href="'.basename(__FILE__).'?act=ed&
        }
    }
    return $f;
}

//#################################################################################
function page_as_link($array, $dirToOps) {
    echo count($array);
    $array = array_unique($array);
    asort($array);
    $link = '';
    $i = 0;
    foreach ($array as $k) {

        //$link .= $k.'<BR>';
        //continue;

        $nice = preg_replace('/[0-9]+/', '', $k);
        $nice = str_replace("/", "&nbsp;&gt;&gt;&nbsp;", $nice);
        $lastDot = strrpos($nice, ".");
        $nice = substr($nice, 0, $lastDot);
        $fOnly = substr($k, strrpos($k, "/") + 1);
//    if (strpos($k, '.jpg') !== false) {
//        $link .= '<a class="menuLink_picture" href="'.basename(__FILE__).'?page='.$k.'&dir='.$dirToOps.'">'.$nice.'</a>&nbsp;&nbsp;<a href="'.basename(__FILE__).'?act=ed&page='.$k.'&dir='.$dirToOps.'">[e]</a><BR>'; //
//    }else{
//        $link .= '<a class="menuLink" href="'.basename(__FILE__).'?page='.$k.'&dir='.$dirToOps.'" style="border:1px silver solid;background-color:#ccffcc;margin:2px;display:block">'.$fOnly.' == '.$nice.'</a>'; // &nbsp;&nbsp;<a href="'.basename(__FILE__).'?act=ed&page='.$k.'&dir='.$dirToOps.'">[e]</a>
//    }
//$link .= $nice.'<BR>';

        $link .= '<a class="menuLink" href="' . basename(__FILE__) . '?page=' . $k . '&dir=' . $dirToOps . '" style="border:1px silver solid;background-color:#ccffcc;margin:2px;display:block">' . $i . '::' . $fOnly . ' == ' . $nice . '</a>'; // &nbsp;&nbsp;<a href="'.basename(__FILE__).'?act=ed&page='.$k.'&dir='.$dirToOps.'">[e]</a>
        $i++;
    }
    return $link;
}

//###############################################################################
function getLinkPageOnly($link) {
    $lastPos = strrpos($link, "?");
    return substr($link, 0, $lastPos);
}

//###############################################################################
function linkToBreads($link) {
    $fnd1 = strpos($link, "?");
    $fnd2 = strpos($link, ".", $fnd1);
    $long = strlen($link) - $fnd1 - (strlen($link) - $fnd2);
    $txt = substr($link, $fnd1, $long);
    $rp1 = array("?page=", "/");
    $rp2 = array("", "][",);
    return strtoupper('[' . str_replace($rp1, $rp2, $txt) . ']');
}

//###############################################################################
function show_edit_box($fileCont, $dir) {
    $f = '<form enctype="multipart/form-data" method="POST" action="' . basename(__FILE__) . '?act=upd&page=' . $fileCont . '&dir=' . $dir . '">' . "\n";
    $f .= '<textarea name="xcont" style="width:780;height:280px;wrap:off;">';

    $lines = file($fileCont);
    foreach ($lines as $line_num => $line) {
        $f .= $line;
    }
    $f .= '</textarea>' . "\n";
    $urlSkip = basename(__FILE__) . '?page=' . $fileCont . '&dir=' . $dir;
    $f .= '<br><input type="submit" value="update">&nbsp;&nbsp;<input type="button" value="skip update" onclick="location.href=\'' . $urlSkip . '\';">';

    $f .= '</form>' . "\n";
    return $f;
}

//###############################################################################
function show_add_box($fileCont, $destination, $dir) {

    $f = '<form enctype="multipart/form-data" method="POST" action="' . basename(__FILE__) . '?act=addFileDo&page=' . $fileCont . '&dir=' . $dir . '">' . "\n";
    $f .= '<textarea name="textOfNewFile" style="width:780;height:280px;wrap:off;">';
    $f .= '</textarea>' . "\n";
    $urlSkip = basename(__FILE__) . '?page=' . $fileCont . '&dir=' . $dir;
    $f .= '<br><input type="hidden" name="dir" value="' . $_REQUEST['dir'] . '"><input type="hidden" name="dest" value="' . $destination . '"><input type="submit" value="add-file">&nbsp;&nbsp;<input type="button" value="skip update" onclick="location.href=\'' . $urlSkip . '\';">';

    $f .= '</form>' . "\n";
    return $f;
}

//###############################################################################
function removeslashes($string) {
    $string = implode("", explode("\\", $string));
    return stripslashes(trim($string));
}

//###############################################################################
function createNewFile($path, $fileContent) {
    $firstLine = explode("\n", $fileContent);
    $fl = $firstLine[0];
    $fh = fopen($fl, 'a');
    fwrite($fh, $fileContent);
    return fclose($fh);
}

//###############################################################################
function strContains($fullText, $patern) {
    if (strpos($fullText, $patern) !== false) {
        return true;
    } else {
        return false;
    }
}

//###############################################################################
function getUniqeFromArray($a) {
    $n = array();
//echo count($a)."\n\n";
    for ($i = 0; $i < count($a); $i++) {
        if (!in_array($a[$i], $n)) {
            // echo $i ." :: ". $a[$i]."\n";
            array_push($n, $a[$i]);
        }
    }
    return $n;
}

//###############################################################################
function getPageLoc($partOfTheUrl) {
    $leftPat = "page=";
    $posLeft = strpos($partOfTheUrl, $leftPat) + strlen($leftPat);
    $posRight = strrpos($partOfTheUrl, "/") + 1;
    return substr($partOfTheUrl, $posLeft, $posRight - $posLeft);
}

//###############################################################################
function page_display($file_location) {
    $file = '';
    if (!isset($file_location)) {
        return '<h2>Click links on left...</h2>';
    }
    if (strpos($file_location, '.html') !== false) { // if file is html
        $file .= '<a href="' . basename(__FILE__) . '?act=ed&page=' . $_REQUEST['page'] . '&dir=' . $_REQUEST['dir'] . '">[e]</a><hR>';
        $file .= file_get_contents($file_location);
    } else if (strpos($file_location, '.jpg') !== false) {
        $file .= '<a href="' . $file_location . '" target="_blank"><img src="' . $file_location . '" style="width:600px;"/></a>'; //'<iframe src="'.$file_location.'" style="width:800px;height:600px"></iframe>';
    } else { // file is txt
        $file .= '<a href="' . basename(__FILE__) . '?act=ed&page=' . $_REQUEST['page'] . '&dir=' . $_REQUEST['dir'] . '">[e]</a><hR>';
        $lines = file($file_location);
        foreach ($lines as $line_num => $line) {
            //$file .= "Line #<b>{$line_num}</b> : " .
            // $bodytag = str_replace(" ", "&nbsp;&nbsp;", htmlspecialchars($line));
            // $body= preg_replace('/[ ]{2,}|[\s]/', '&nbsp', trim(htmlspecialchars($line)));                
            $body = trim(preg_replace('!\t+!', '&nbsp;&nbsp;', htmlspecialchars($line)));
            $file .= $body . "<br />\n";
        }
    }
    return $file;
}

echo '<!DOCTYPE html><html lang="en-US">';
echo '<head><title>TXT-CMS</title>';
echo '<meta charset="utf-8">';
echo '<style>
BODY{font-family:courier;}
a.menuLink{text-decoration:none;font-family:Verdana;font-size:12px; color:black}
a.menuLink_picture{text-decoration:none;font-family:Verdana;font-size:12px; color:red; font-size:bold}
#menux{height: 550px; width:450px;overflow-y: auto;resize: both;overflow:auto;position:-webkit-sticky;position:sticky;top:0;background-color:ghostwhite;border:2px silver inset}
#headBelk{position:-webkit-sticky;position:sticky;top:0;background-color:ghostwhite;padding-top:10px;padding-bottom:10px;padding-left:220px}
#selector{position:-webkit-sticky;position:sticky;top:0;float:right;}
#edytor{resize: both;overflow:auto;position: absolute;left:50%;margin-left:-400px;background-color:silver;border:1px red solid;padding:20px;top:100px;width:800px;height:310px;}
@media only screen and (max-width: 760px) {
#contx{font-family:courier;font-size: 3em;}  
a.menuLink{text-decoration:none;font-family:Verdana;font-size: 3em;}
}
</style>';
echo '</head><body>';

echo '<div id="headBelk">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . $base . '">TXT-CMS</a> OF <a href="' . $base . '">' . linkToBreads($currentFile) . '</a>&nbsp;&nbsp;';
//echo '<a href="'.$base.'?act=addFile&dest='.getPageLoc($_SERVER['REQUEST_URI']).'&dir='.$dirToOps.'">[ADD-FILE-HERE]</a>';
echo '</div>';
echo '<div id="selector">';
// #################################################
echo '<form  method="POST" action="' . $base . '">';
echo '<select  onchange="this.form.submit()" name="dir">';

$selected = "";
echo '<option>== SELECT PAGE ==</option>' . "\n";
foreach ($pgs as $pg) {
    if ($pg === $_REQUEST['dir']) {
        $selected = "selected";
    } else {
        $selected = "";
    }
    echo '<option value="' . $pg . '" ' . $selected . '>' . strtoupper($pg) . '</option>' . "\n";
}
echo '</select></form>';
// ##############################################################################
echo '</div>';
echo '<table border="0" cellspacing="5" cellpadding="5"><tr>';
echo '<td valign="top" style="width:250px;"><div id="menux">';
//echo page_as_link(scanDir::scan($dirToOps, $file_ext, true), $dirToOps);
echo pagesAsHierarchyList(scanDir::scan($dirToOps, $file_ext, true), $dirToOps);
echo '<img src="none.gif" style="width:200px;height:0px;border:0px white solid;"/>';
echo '</div></td>';
echo '<td valign="top" style="width:800px;" id="contx">';
echo page_display($_REQUEST['page']);
echo '<HR>';
//echo pagesAsHierarchyList(scanDir::scan($dirToOps, $file_ext, true), $dirToOps);//page_as_link(scanDir::scan($dirToOps, $file_ext, true), $dirToOps);
echo '</td></tr></table>';
echo '</body></html>';

// ##############################################################################
class scanDir {

    static private $directories, $files, $ext_filter, $recursive;

// ----------------------------------------------------------------------------------------------
    // scan(dirpath::string|array, extensions::string|array, recursive::true|false)
    static public function scan() {
        // Initialize defaults
        self::$recursive = false;
        self::$directories = array();
        self::$files = array();
        self::$ext_filter = false;

        // Check we have minimum parameters
        if (!$args = func_get_args()) {
            die("Must provide a path string or array of path strings");
        }
        if (gettype($args[0]) != "string" && gettype($args[0]) != "array") {
            die("Must provide a path string or array of path strings");
        }

        // Check if recursive scan | default action: no sub-directories
        if (isset($args[2]) && $args[2] == true) {
            self::$recursive = true;
        }

        // Was a filter on file extensions included? | default action: return all file types
        if (isset($args[1])) {
            if (gettype($args[1]) == "array") {
                self::$ext_filter = array_map('strtolower', $args[1]);
            } else
            if (gettype($args[1]) == "string") {
                self::$ext_filter[] = strtolower($args[1]);
            }
        }

        // Grab path(s)
        self::verifyPaths($args[0]);
        return self::$files;
    }

    static private function verifyPaths($paths) {
        $path_errors = array();
        if (gettype($paths) == "string") {
            $paths = array($paths);
        }

        foreach ($paths as $path) {
            if (is_dir($path)) {
                self::$directories[] = $path;
                $dirContents = self::find_contents($path);
            } else {
                $path_errors[] = $path;
            }
        }

        if ($path_errors) {
            echo "The following directories do not exists<br />";
            die(var_dump($path_errors));
        }
    }

    // This is how we scan directories
    static private function find_contents($dir) {
        $result = array();
        $root = scandir($dir);
        foreach ($root as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_file($dir . DIRECTORY_SEPARATOR . $value)) {
                if (!self::$ext_filter || in_array(strtolower(pathinfo($dir . DIRECTORY_SEPARATOR . $value, PATHINFO_EXTENSION)), self::$ext_filter)) {
                    self::$files[] = $result[] = $dir . DIRECTORY_SEPARATOR . $value;
                }
                continue;
            }
            if (self::$recursive) {
                foreach (self::find_contents($dir . DIRECTORY_SEPARATOR . $value) as $value) {
                    self::$files[] = $result[] = $value;
                }
            }
        }
        // Return required for recursive search
        return $result;
    }

}

/*

  //Scan a single directory for all files, no sub-directories
  $files = scanDir::scan('D:\Websites\temp');

  //Scan multiple directories for all files, no sub-dirs
  $dirs = array(
  'D:\folder';
  'D:\folder2';
  'C:\Other';
  );
  $files = scanDir::scan($dirs);

  // Scan multiple directories for files with provided file extension,
  // no sub-dirs
  $files = scanDir::scan($dirs, "jpg");
  //or with an array of extensions
  $file_ext = array(
  "jpg",
  "bmp",
  "png"
  );
  $files = scanDir::scan($dirs, $file_ext);

  // Scan multiple directories for files with any extension,
  // include files in recursive sub-folders
  $files = scanDir::scan($dirs, false, true);

  // Multiple dirs, with specified extensions, include sub-dir files
  $files = scanDir::scan($dirs, $file_ext, true);


 */
?>
