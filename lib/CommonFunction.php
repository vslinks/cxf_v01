<?php

/* 全局校验是否合法登录 */

/* * 检查所有请求输入* */
function checkRequestParma($inputStr, $maxLength = -1, $CheckName, $type = null, $isEnword = true, $isCheckNull = TRUE) {
    if ($isCheckNull) {
        if (!isset($inputStr) || empty($inputStr)) {
            myerror(StatusCode::msgFailStatus, format("{0}不能为空", $CheckName));
        }
        if ($isEnword) {
            if ($maxLength != -1) {
                if (mb_strlen($inputStr, 'utf8') > $maxLength) {
                    myerror(StatusCode::msgFailStatus, format("{0}不能大于{1}个字符", $CheckName, $maxLength));
                }
            }
        } else {
            if ($maxLength != -1) {
                if (abslength($inputStr) > $maxLength) {
                    myerror(StatusCode::msgFailStatus, format("{0}不能大于{1}个字符", $CheckName, $maxLength));
                }
            }
        }
    } else {
        if ($maxLength != -1) {
            if (mb_strlen($inputStr, 'utf8') > $maxLength) {
                myerror(StatusCode::msgFailStatus, format("{0}不能大于{1}个字符", $CheckName, $maxLength));
            }
        }
    }
    if (!empty($type) && $isCheckNull) {
        switch ($type) {
            case CheckType::Int:
                if (!filter_var($inputStr, FILTER_VALIDATE_INT)) {
                    myerror(StatusCode::msgFailStatus, format("{0}必须为整形", $CheckName));
                }
                break;
            case CheckType::Email:
                if (!filter_var($inputStr, FILTER_VALIDATE_EMAIL)) {
                    myerror(StatusCode::msgFailStatus, format("{0}不是标准的邮箱格式", $CheckName));
                }
                break;
            case CheckType::Idcard:
                if (!is_idcard($inputStr)) {
                    myerror(StatusCode::msgFailStatus, format("{0}不是标准的身份证号码", $CheckName));
                }
                break;
            case CheckType::Float:
                if (!filter_var($inputStr, FILTER_VALIDATE_FLOAT)) {
                    myerror(StatusCode::msgFailStatus, format("{0}不是小数类型", $CheckName));
                }
                break;
        }
    }
}


/*特殊校验输入*/
function is_bankCard($subject) {    //判断是否为银行卡号
    $pattern = '/^[1-9]\d{15,18}/';
    if (preg_match($pattern, $subject))
        return true;
    else
        return false;
}
function is_email($str){            //判断是否为email
    if (preg_match('/^[a-z0-9]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,8}([\.][a-z]{2,8})?$/i', $str)) {
        return true;
    }else{
        return false;
    }
}
function is_idCard($id) {
    $id = strtoupper($id);
    $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
    $arr_split = array();
    if (!preg_match($regx, $id)) {
        return FALSE;
    }
    if (15 == strlen($id)) { //检查15位
        $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
        @preg_match($regx, $id, $arr_split);
        //检查生日日期是否正确
        $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) {
            return FALSE;
        } else {
            return TRUE;
        }
    } else {           //检查18位
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $id, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) {  //检查生日日期是否正确
            return FALSE;
        } else {
            //检验18位身份证的校验码是否正确。
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $sign = 0;
            for ($i = 0; $i < 17; $i++) {
                $b = (int) $id{$i};
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $val_num = $arr_ch[$n];
            if ($val_num != substr($id, 17, 1)) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }   //教验身份证是否合法(兼容15位和18位)
}
function is_mobile($str){
    if (preg_match('/^1[3-9][0-9]{9}$/', $str)) {
        return true;
    }else{
        return false;
    }    
}
function is_naturalNumber($str){       //是否为自然数，从1开始
    if (preg_match('/^1[0-9]+$/', $str)) {
        return true;
    }else{
        return false;
    }    
}

function has_string($str){      //验证是否为非空字符串（非数字型字符串）
    if (strlen($str)>0 && !preg_match('/^[0-9]+$/', $str)) {
        return true;
    }else{
        return false;
    }
}
function has_number($str){      //验证是否为非空数字    
    if (strlen($str)>0 && preg_match('/^[0-9]+$/', $str)) {
        return true;
    }else{
        return false;
    }
}
function has_valueInArray($arr){           //检查数组里面是否存在空值
    foreach ($arr as $key => &$value) {
        if (empty($value)) {
            return false; 
        }
    }
    return true;
}

/***************/


/* * 格式化字符串，类似string.format* */

function format() {
    $args = func_get_args();
    if (count($args) == 0) {
        return;
    }
    if (count($args) == 1) {
        return $args[0];
    }
    $str = array_shift($args);
    $str = preg_replace_callback('/\\{(0|[1-9]\\d*)\\}/', create_function('$match', '$args = ' . var_export($args, true) . '; return isset($args[$match[1]]) ? $args[$match[1]] : $match[0];'), $str);
    return $str;
}

/* * 统计中英文混合字符串长度,utf8 下面是3个字节，gb2312 是2个字节 * */
//使用iconv_strlen($str, 'UTF-8');
//使用系统自带函数 
// function abslength($str) {
//     $len = strlen($str);
//     $i = 0;
//     while ($i < $len) {
//         if (preg_match("/^[" . chr(0xa1) . "-" . chr(0xff) . "]+$/", $str[$i])) {
//             $i+=2;
//         } else {
//             $i+=1;
//         }
//     }
//     return $i;
// }

/* * 获取当前时间,精确到毫秒级* */

function get_current_date() {
    $t = microtime(true);
    $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
    return date('Y-m-d H:i:s:' . $micro, $t);
}

/* * 将stdclass 转换为 array* */

function object_array($array) {
    if (is_object($array)) {
        $array = (array) $array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

/* * 将stdclass转换为实体类* */

function recast($className, stdClass &$object) {
    if (!class_exists($className))
        throw new InvalidArgumentException(sprintf('Inexistant class %s.', $className));

    $new = new $className();

    foreach ($object as $property => &$value) {
        $new->$property = &$value;
        unset($object->$property);
    }
    unset($value);
    $object = (unset) $object;
    return $new;
}



/* * 返回信息* */

function response_info($msg = '请求成功', $body = NULL) {
    $result = new result();
    $header = new header();
    $header->stats = StatusCode::msgSucessStatus;
    $header->msg = $msg;
    $result->header = $header;
    $result->type = 'json';

    if ($body != NULL) {
        $li = new body();
        $li->list = $body;
        $result->body = $li;
    }
    $returnMsg = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo $returnMsg;
    exit();
}



function get_dir($dir) {
    $dirArray[] = NULL;
    if (false != ($handle = opendir($dir))) {
        $i = 0;
        while (false !== ($file = readdir($handle))) {
            //去掉"“.”、“..”以及带“.xxx”后缀的文件
            if ($file != "." && $file != ".." && !strpos($file, ".")) {
                $dirArray[$i] = $file;
                $i++;
            }
        }
        //关闭句柄
        closedir($handle);
    }
    return $dirArray;
}

function get_file($dir) {
    $fileArray[] = NULL;
    if (false != ($handle = opendir($dir))) {
        $i = 0;
        while (false !== ($file = readdir($handle))) {
            //去掉"“.”、“..”以及带“.xxx”后缀的文件
            if ($file != "." && $file != ".." && strpos($file, ".")) {
                $fileArray[$i] = $dir . '/' . $file;
                if ($i == 100) {
                    break;
                }
                $i++;
            }
        }
        //关闭句柄
        closedir($handle);
    }
    return $fileArray;
}

function get_file_folder_list($dir) {
    $fileArray = NULL;
    if (false != ($handle = opendir($path))) {
        $i = 0;
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($path . $file)) {
                    $fileArray [$i] = $file;
                } else if (is_file($path . $file)) {
                    $fileArray [$i] = $file . "/";
                }
                $i ++;
            }
        }
        // 关闭句柄 
        closedir($handle);
    }
    return $fileArray;
}

function zip_dir($dir, $zipfilename, $Todo) {
    IF (!@Function_exists('gzcompress')) {
        Return 0;
    }
    @set_time_limit("0");

    openFile($dir, $zipfilename);

    $out = $this->filezip();

    Switch ($Todo) {
        Case "1":
            $this->DownLoad(__FILE__, $zipfilename, $out);
            Break;
        Case "2":
            $this->SaveFile(__FILE__, $zipfilename, $out);
            Break;
    }
}

function open_file($path, $zipName) {
    $temp_path = $path;
    $temp_zip_path = $zipName;
    IF ($handle = @opendir($path)) {
        While (false !== ($file = readdir($handle))) {
            IF ($file != '.' and $file != '..') {
                IF (ereg('\.', $file . @basename())) {
                    $fd = fopen($path . '/' . $file, "r");
                    $fileValue = @fread($fd, 1024000);
                    fclose($fd);
                    addFile($fileValue, $path . '/' . $file);
                } Else {
                    $this->openFile($path . '/' . $file, $zipName . '/' . $file);
                }
            }
        }
        $zipName = $temp_zip_path;
        $path = $temp_path;
        closedir($handle);
    }
}

/**
 * 得到所有文件夹下面文件
 * * */
function add_file_to_zip($path, $zip) {
    $handler = opendir($path); //打开当前文件夹由$path指定。
    while (($filename = readdir($handler)) !== false) {
        if ($filename != "." && $filename != "..") {//文件夹文件名字为'.'和‘..’，不要对他们进行操作
            if (is_dir($path . "/" . $filename)) {// 如果读取的某个对象是文件夹，则递归
                addFileToZip($path . "/" . $filename, $zip);
            } else { //将文件加入zip对象
                $zip->addFile($path . "/" . $filename);
            }
        }
    }
    @closedir($path);
}

/**
 * 生成zip压缩文件
 * * */
function new_zip($files = array(), $destination = '', $overwrite = false) {
    //if the zip file already exists and overwrite is false, return false
    if (file_exists($destination) && !$overwrite) {
        return false;
    }
    //vars
    $valid_files = array();
    //if files were passed in...
    if (is_array($files)) {
        //cycle through each file
        foreach ($files as $file) {
            //make sure the file exists
            if (file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if (count($valid_files)) {
        //create the archive
        $zip = new ZipArchive();
        if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        //add the files
        foreach ($valid_files as $file) {
            $zip->addFile($file, $file);
        }
        //debug
        //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
        //close the zip -- done!
        $zip->close();
        //check to make sure the file exists
        return file_exists($destination);
    } else {
        return false;
    }
}

/**
 * 创建压缩zip文件
 * input:$sourceDir（要压缩的文件夹，如：e:/abc）
 * input:$outDir (输出的压缩包完整路径,如: e:/out/test.zip)
 * input:$isDownLoad (是否立即下载, 可选参数,false 不下载)
 * */
function create_zip($sourceDir, $outDir, $isDownLoad = false) {
    $zip = new ZipArchive();
    if ($zip->open($outDir, ZipArchive::OVERWRITE) === TRUE) {
        addFileToZip($sourceDir, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
        $zip->close(); //关闭处理的zip文件
    }
    if (!empty($outDir) && $isDownLoad) {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . basename($outDir)); //文件名   
        header("Content-Type: application/zip"); //zip格式的   
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件    
        header('Content-Length: ' . filesize($outDir)); //告诉浏览器，文件大小   
        @readfile($outDir);
    }
}

/**
 * 压缩文件
 * * */
function create_zips($outPath, $arr, $sourceDir) {
    $zip = new ZipArchive();
    if ($zip->open($outPath, ZipArchive::OVERWRITE)) {
        foreach ($arr as $key => $value) {
            if (!empty($value) && is_array($value)) {
                $zip->addEmptyDir($key);
                foreach ($value as $file) {
                    $download_file = file_get_contents($sourceDir . $key . '/' . $file);
                    #add it to the zip
                    $zip->addFromString($key . '/' . basename($file), $download_file);

                    //$zip->addFile($sourceDir.'/'.$file,$key.'/'.basename($file));
                }
            } else {
                $download_file = file_get_contents($sourceDir . $file);
                #add it to the zip
                $zip->addFromString(basename($file), $download_file);

                //   $zip->addFile($sourceDir.'/'.$value);//假设加入的文件名是image.txt，在当前路径下
            }
        }
        $zip->close();
    }
}

function create_zip_from_many($outPath, $rr) {
    $zip = new ZipArchive();
    if ($zip->open($outPath, ZipArchive::OVERWRITE)) {
        foreach ($rr as $key => $val) {
            if (!empty($val) && is_array($val)) {
                foreach ($val as $ckey => $cval) {
                    if (!empty($cval) && is_array($cval)) {
                        foreach ($cval as $cckey => $ccval) {
                            if (!empty($ccval) && is_array($ccval)) {
                                $zip->addEmptyDir($cckey);
                                foreach ($ccval as $file) {
                                    $download_file = file_get_contents($sourceDir . $key . '/' . $file);
                                    #add it to the zip
                                    $zip->addFromString($cckey . '/' . basename($file), $download_file);

                                    //$zip->addFile($sourceDir.'/'.$file,$key.'/'.basename($file));
                                }
                            } else {
                                $download_file = file_get_contents($sourceDir . $ccval);
                                #add it to the zip
                                $zip->addFromString(basename($ccval), $download_file);

                                //   $zip->addFile($sourceDir.'/'.$value);//假设加入的文件名是image.txt，在当前路径下
                            }
                        }
                    } else {
                        $path = $cval['path'];
                    }
                }
            }
        }
        $zip->close();
    }
}

/**
 * 压缩文件
 * * */
function create_zip_one($outPath, $rr) {
    $zip = new ZipArchive();
    if ($zip->open($outPath, ZipArchive::OVERWRITE)) {
        foreach ($rr as $key => $val) {
            $floder = basename($rr['csvpath'], '.csv');
            $zip->addEmptyDir($floder);
            if (!empty($val) && is_array($val)) {
                foreach ($val as $ckey => $cval) {
                    if (!empty($cval) && is_array($cval)) {
                        foreach ($cval as $cckey => $ccval) {
                            $download_file = file_get_contents($ccval);
                            $zip->addFromString($floder . '/' . basename($ccval), $download_file);
                        }
                    }
                }
            } else {
                $download_file = file_get_contents($val);
                $zip->addFromString(basename($val), $download_file);
            }
        }
        $zip->close();
    }
}

/**
 * 读取本地文件夹和文件按目录结构转换为array
 * * */
function dir_to_array($dir) {
    $result = array();
    $cdir = scandir($dir);
    foreach ($cdir as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
            } else {
                $result[] = $value;
            }
        }
    }
    return $result;
}




/*
 * 递归创建文件夹
 * parma: 文件夹路径
 * 
 */

function create_folders($dir) {
    return is_dir($dir) or ( create_folders(dirname($dir)) and mkdir($dir, 0777));
}

/* escape解码 */

function phpescape($str) {
    preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/", $str, $newstr);
    $ar = $newstr[0];
    foreach ($ar as $k => $v) {
        if (ord($ar[$k]) >= 127) {
            $tmpString = bin2hex(iconv("GBK", "ucs-2", $v));
            if (!eregi("WIN", PHP_OS)) {
                $tmpString = substr($tmpString, 2, 2) . substr($tmpString, 0, 2);
            }
            $reString.="%u" . $tmpString;
        } else {
            $reString.= rawurlencode($v);
        }
    }
    return $reString;
}

/* 取stdclass值 */

function get_stdclass_name($base_class, $property) {
    if (!empty($base_class) && property_exists($base_class, $property)) {
        return $base_class->$property;
    }
    return null;
}




/*
 * 抓取淘宝属性
 */

function get_properties_by_cid($cid) {
    $arr = array(
        'method' => 'taobao.itemprops.get',
        'app_key' => GET_APP_KEY,
        'timestamp' => date('Y-m-d H:i:s'),
        'format' => 'json',
        'v' => '2.0',
        'sign_method' => 'md5',
        'cid' => '1512',
        'fields' => 'pid,name,prop_values',
        'cid' => $cid
    );
    ksort($arr);
    $str = GET_APP_SCRIPT;
    $js = '';
    foreach ($arr as $key => $val) {
        $js.= format($key . $val);
    }
    $js = $str . $js . $str;
    $sign = strtoupper(md5($js));
    $pp = getStr($arr);
    //return curlPostStr(TB_URL, format("{0}&sign={1}", $pp, $sign), 60, FALSE);
    // die(curlPostStr(TB_URL, format("{0}&sign={1}", $pp, $sign)));
    return json_decode(curlPostStr(TB_URL, format("{0}&sign={1}", $pp, $sign), 60, FALSE));
}

/* 调用curl读取返回内容 */

function curl_post_str($url, $data, $timeout = 30, $CA = true) {
    $cacert = getcwd() . '/cacert.pem'; //CA根证书  
    $SSL = substr($url, 0, 8) == "https://" ? true : false;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout - 2);
    if ($SSL && $CA) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书  
        curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配  
    } else if ($SSL && !$CA) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书  
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名  
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //避免data数据过长问题  
    curl_setopt($ch, CURLOPT_POST, true);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //data with URLEncode  

    $ret = curl_exec($ch);
    //var_dump(curl_error($ch));  //查看报错信息  

    curl_close($ch);
    return $ret;
}

/* 返回字符串 */

function get_str($array, $Separator = '&') {
    $str = '';
    $yb = '';
    foreach ($array as $key => $val) {
        $yb .= format($key . "=" . $val . "&");
    }
    return substr($yb, 0, strlen($yb) - 1);
}



/* 截取两个字符串之间的字符
 */

function get_string_between($str, $from, $to) {
    $sub = substr($str, strpos($str, $from) + strlen($from), strlen($str));
    return substr($sub, 0, strpos($sub, $to));
}

/* 获取唯一字符串id
 */

function get_unique_str() {
    return md5(uniqid('', TRUE));
}






function dump($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

/**
 * 上传图片到upyun返回路径
 * @param string|array $imgeFile
 * @return string
 */
function upyunUpload($imgeFile){
    require __EXT__ . 'upyun.php';
    return $upyunFile;
}


