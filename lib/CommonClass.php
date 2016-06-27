<?php





/* * 要检查的输入参数* */

class InputCheckName {
    const UserName = '用户名';
    const PassWord = '用户密码';
    const CurrentPage = '当前页码';
    const GoodsId = '商品ID';
    const UserId = '用户ID';
    const SuplierId = '供货商ID';
    const ClassId = '商品小类';
    const ItemId = '商品ID';
    const OldPassWord = '旧密码';
    const NewPassWord = '新密码';
    const UserNick = '用户昵称';
    const Mobile = '电话号码';
    const Email = '电子邮箱';
    const Address = '用户地址';
    const QQ = 'QQ号码';
    const CategoryId = '类目ID';
    const SortType = '排序类型';
    const Humen = '人气';
    const Sales = '销量';
    const IsNew = '新款';
    const Price = '价格';
    const Search = '搜索内容';
    const NoticeId = '公告ID';

}

/**
 * 检查类型
 * * */
class CheckType {

    const Email = 'Email';
    const Idcard = 'IdCard';
    const Int = 'Int';
    const Float = 'Float';

}

/*
 * 查询类型
 *  */

class QueryType {

    const In = 'In';
    const NotIn = 'NotIn';
    const Eq = 'Eq';
    const Range = 'Range';
    const Maybe = 'Maybe'; //等于Or
    const Like = 'Like';
    const Is = 'Is';
    const PlusThan = 'PlusThan'; //大于
    const LessThan = 'LessThan'; //小于

}

/*
 * 广告商品
 */

class AdverGoods {

    const NewGoods = 1;  //新款
    const VisitGoods = 2; //爆款
    const SpecialMoney = 3; // 特价商品
    const ClearGoods = 4;  //清仓商品
    const RecommandGoods = 5; //特推商品

}

class SearchTitle {

    const HumenHigh = 1;
    const HumenLow = 2;

}

/* 事物处理 */

class TranType {
    const delete = 1;
    const update = 2;
    const create = 3;
}
/*流量卡类型
 */
class CardType{
    const jichu=1;
    const biaozhun=2;
    const zhuanye=3;
    const haohua=4;
    const zungui=5;
    const zhizun=6;
}
/*充值资金类型*/
class PayMoneyType{
    const BuyCard=1; //购买流量卡
    const Alipay=2;  //支付宝充值
    const CaifuTong=3;  //财付通充值
    const Kami=4;  //卡密充值
}



class Common {
    /* 获取购买流量卡，明细 */

    static function GetByCardRemark($type) {
        $remark = "";
        switch ($type) {
            CASE CardType::jichu:
                $remark = format("购买基础版流量卡，获得{0}个流量点.", JICHU_POINT);
                break;
            CASE CardType::biaozhun:
                $remark = format("购买标准流量卡，获得{0}个流量点.", BIAOZHUN_POINT);
                break;
            CASE CardType::zhuanye:
                $remark = format("购买专业版流量卡，获得{0}个流量点.", ZHUANYE_POINT);
                break;
            CASE CardType::haohua:
                $remark = format("购买豪华版流量卡，获得{0}个流量点.", HAOHUA_POINT);
                break;
            CASE CardType::zungui:
                $remark = format("购买尊贵版流量卡，获得{0}个流量点.", ZUNGUI_POINT);
                break;
            CASE CardType::zhizun:
                $remark = format("购买至尊版流量卡，获得{0}个流量点.", ZHIZUN_POINT);
                break;
        }
        return $remark;
    }

    static function GetPointByCardType($type) {
        
    }

    /*** 获取当前时间,精确到毫秒级* */

    static function getCurrentDate() {
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        return date('Y-m-d H:i:s:' . $micro, $t);
    }

    static function getUserId() {
        $userID = _get("userId");
        if (empty($userID)) {
            return "请重新登录后操作.";
        } else {
            $userDalDal = new UserDal();
            $whereDal = array(QueryType::Eq => array("UserID" => $userID));
            $userDalList = $userDalDal->QueryByWhere($whereDal, NULL, NULL, array("UserID", "UserName", "PassWord", "Email", "RealName", "CONVERT(varchar(50), Birthday, 23) as Birthday", "IDCard", "Mobile", "QQ", "CONVERT(varchar(100), RegDate, 20) as RegDate", "CONVERT(varchar(100), LastLoginTime, 20) as LastLoginTime", "LastLoginIP", "Convert(decimal(18,2),Money) as Money", "Convert(decimal(18,2),Point) as Point", "shopName", "TotalVisitNum", "TotalClickNum"));
            if (!empty($userDalList) && is_array($userDalList)) {
                return $userDalList;
            } else {
                return "请重新登录后操作.";
            }
        }
    }

    /* 记录日志 */

    static function WriteLog($value) {
        $path = BASE_PATH . "log/" . date('ymd', time()) . ".txt";
        $dirname = dirname($path);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
        $myfile = fopen($path, "a") or die("Unable to open file!");
        fwrite($myfile, date('y-m-d h:i:s', time()) . ":" . $value . "\r\n");
        fclose($myfile);
    }

    /* 获取请求参数 */

    static function Request() {
        $arr = array();
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'PUT':
                do_something_with_put($request);
                break;
            case 'POST':
                $data = file_get_contents('php://input', 'r');
                $arr = json_decode($data, TRUE);
                break;
            case 'GET':
                parse_str($_SERVER["QUERY_STRING"], $arr);
                break;
            case 'HEAD':
                do_something_with_head($request);
                break;
            case 'DELETE':
                do_something_with_delete($request);
                break;
            case 'OPTIONS':
                do_something_with_options($request);
                break;
            default:
                handle_error($request);
                break;
        }
        return $arr;
    }

}
class Cookie {
   /**
     * 解密已经加密了的cookie
     * 
     * @param string $encryptedText
     * @return string
     */
    private static function _decrypt($encryptedText)
    {
        //$key = Config::get('secret_key');
        $key= SECRET_KEY;
        $cryptText = base64_decode($encryptedText);
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $decryptText = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $cryptText, MCRYPT_MODE_ECB, $iv);
        return trim($decryptText);
    }
 
    /**
     * 加密cookie
     *
     * @param string $plainText
     * @return string
     */
    private static function _encrypt($plainText)
    {
        //$key = Config::get('secret_key');
        $key=$key= SECRET_KEY;
        $ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $encryptText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $plainText, MCRYPT_MODE_ECB, $iv);
        return trim(base64_encode($encryptText));
    }
     
    /**
     * 删除cookie
     * 
     * @param array $args
     * @return boolean
     */
    public static function del($args)
    {
        $name = $args['name'];
        $domain = isset($args['domain']) ? $args['domain'] : null;
        return isset($_COOKIE[$name]) ? setcookie($name, '', time() - 86400, '/', $domain) : true;
    }
     
    /**
     * 得到指定cookie的值
     * 
     * @param string $name
     */
    public static function get($name)
    {
        return isset($_COOKIE[$name]) ? self::_decrypt($_COOKIE[$name]) : null;
    }
     
    /**
     * 设置cookie
     *
     * @param array $args
     * @return boolean
     */
    public static function set($args,$isEncrypt=FALSE)
    {
        $name = $args['name'];
        $value= $isEncrypt ? self::_encrypt($args['value']):$args['value'];
        $expire = isset($args['expire']) ? $args['expire'] : null;
        $path = isset($args['path']) ? $args['path'] : '/';
        $domain = isset($args['domain']) ? $args['domain'] : null;
        $secure = isset($args['secure']) ? $args['secure'] : 0;
        return setcookie($name, $value, $expire, $path, $domain, $secure);
    }
}



