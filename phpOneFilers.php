<?php
/*
* presenting thumbnails flow from pictures located in directory
* @author: k.gumulak
*/


    if (!isset($_SESSION)) {
      session_start();
    }
    $sz = 'szer';
    if (isset($_REQUEST[$sz])) {
       $_SESSION[$sz] = $_REQUEST[$sz];
    }else{
        $szer = '800';
        $_SESSION[$sz] = '800';
    }

echo '<!DOCTYPE html><html><head><title>Simple-Dir-Gallery [SDG]</title>';
echo '<style>

BODY{padding:10px;background-color:ghostwhite;color:silver;font-family:verdana;}
#menux{display:block;top:0px;padding:40xp;background-color:silver;width:100%;text-align:center;}
#menux a{margin-left:10px; margin-right:10px; }
#left{border:1px black solid:width:100px;height:100px;position:absolute;left:0px, top:0px;}
#right{border:1px black solid:width:100px;height:100px;position:absolute;right:0px, top:0px;}
</style>';
echo '</head><body>';
echo '<div id="menux">';
$current = (!isset($_REQUEST['cur'])?0:$_REQUEST['cur']);
// menu
echo '<a href="index.php?'.$sz.'=500&cur='.$current.'">500</a>';
echo '<a href="index.php?'.$sz.'=800&cur='.$current.'">800</a>';
echo '<a href="index.php?'.$sz.'=1000&cur='.$current.'">1000</a>';
echo '<a href="index.php?'.$sz.'=1200&cur='.$current.'">1200</a>';
echo '<a href="index.php?'.$sz.'=1400&cur='.$current.'">1400</a>';
echo '</div>';

$file_ext = array("jpg","bmp","png");
$r = scanDir::scan(".", $file_ext);
////////////////////////////////////////////////////
// engine dialog
if(isset($_REQUEST['cur'])){
echo DRAW_ME_AND_LEFT_AND_RIGHT($r, $_REQUEST['cur'], $_SESSION[$sz]);
}else{
echo DRAW_ME_AND_LEFT_AND_RIGHT($r, 0, $_SESSION[$sz]);
}
echo '</body></html>';
////////////////////////////////////////////////////////////////
// functions 
////////////////////////////////////////////////////////////////
function DRAW_ME_AND_LEFT_AND_RIGHT($array, $MeIndex, $szer){
  $max = count($array);
  // key jest liczba indexu dla MeName
  $key = $MeIndex;
  //$key = array_search($MeName, $array);
  $f='';
  //$f.='wszystkich elementow: '.$max."\n";
 $f.='<table border="0" cellpadding="10" width="95%" align="center"><tr>';
if($key>0){
    $l = $key-1;
    $f.='<td valign="top" style="width:100px;">PREV<div id="left"><a href="index.php?cur='.$l.'&szer='.$szer.'" target="_self"><img src="'.$array[$l].'" style="border:3px black solid;height:100px;width:100px;" /></a></div></td>'."\n";
  }else{
    $f.='<td valign="top" style="width:100px;">START</td>'."\n";
  } 
 $f.='<td valign="top" width="90%" align="center"><a href="'.$array[$key].'" target="_blind"><img src="'.$array[$key].'" style="border:1px silver solid;width:'.$szer.'px" /></a></td>'."\n";
 
  if($key<$max-1){
    $p = $key+1;
    $f.='<td valign="top" style="width:100px;">NEXT<div id="right"><a href="index.php?cur='.$p.'&szer='.$szer.'" target="_self"><img src="'.$array[$p].'" style="border:3px black solid;height:100px;width:100px;" /></a></div></td>'."\n";
  }else{
    $f.='<td valign="top" style="width:100px;">END</td>'."\n";
  }
 $f.='</tr>';
 $f.='<tr><td colspan="3">'.DRAW_WHOLE_ARRAY_AS_LINKS($array, $szer).'</td></tr>';
$f.='</table>'; 
  return $f;
}
///////////////////////////////////////////////////////////////
function DRAW_WHOLE_ARRAY_AS_LINKS($array, $szer){
$f='';
foreach($array as $k=>$v){
$f.='&nbsp;<a href="index.php?cur='.$k.'&szer='.$szer.'">[&nbsp;'.($k+1).'&nbsp;]</a>&nbsp; ';
}
return $f;
}
////////////////////////////////////////////////////////////
// scanning dir
class scanDir {
    static private $directories, $files, $ext_filter, $recursive;
// ----------------------------------------------------------------------------------------------
    // scan(dirpath::string|array, extensions::string|array, recursive::true|false)
    static public function scan(){
        // Initialize defaults
        self::$recursive = false;
        self::$directories = array();
        self::$files = array();
        self::$ext_filter = false;

        // Check we have minimum parameters
        if(!$args = func_get_args()){
            die("Must provide a path string or array of path strings");
        }
        if(gettype($args[0]) != "string" && gettype($args[0]) != "array"){
            die("Must provide a path string or array of path strings");
        }

        // Check if recursive scan | default action: no sub-directories
        if(isset($args[2]) && $args[2] == true){self::$recursive = true;}

        // Was a filter on file extensions included? | default action: return all file types
        if(isset($args[1])){
            if(gettype($args[1]) == "array"){self::$ext_filter = array_map('strtolower', $args[1]);}
            else
            if(gettype($args[1]) == "string"){self::$ext_filter[] = strtolower($args[1]);}
        }

        // Grab path(s)
        self::verifyPaths($args[0]);
        return self::$files;
    }

    static private function verifyPaths($paths){
        $path_errors = array();
        if(gettype($paths) == "string"){$paths = array($paths);}

        foreach($paths as $path){
            if(is_dir($path)){
                self::$directories[] = $path;
                $dirContents = self::find_contents($path);
            } else {
                $path_errors[] = $path;
            }
        }

        if($path_errors){echo "The following directories do not exists<br />";die(var_dump($path_errors));}
    }

    // This is how we scan directories
    static private function find_contents($dir){
        $result = array();
        $root = scandir($dir);
        foreach($root as $value){
            if($value === '.' || $value === '..') {continue;}
            if(is_file($dir.DIRECTORY_SEPARATOR.$value)){
                if(!self::$ext_filter || in_array(strtolower(pathinfo($dir.DIRECTORY_SEPARATOR.$value, PATHINFO_EXTENSION)), self::$ext_filter)){
                    self::$files[] = $result[] = $dir.DIRECTORY_SEPARATOR.$value;
                }
                continue;
            }
            if(self::$recursive){
                foreach(self::find_contents($dir.DIRECTORY_SEPARATOR.$value) as $value) {
                    self::$files[] = $result[] = $value;
                }
            }
        }
        // Return required for recursive search
        return $result;
    }
}


?>
