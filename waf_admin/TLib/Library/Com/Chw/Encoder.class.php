<?php
namespace Com\Chw;

class Encoder{

    var $key = 'w8q3fa7j';
    var $Keys = array("0x12", "0x34", "0x56", "0x78", "0x90", "0xAB", "0xCD", "0xEF");

    function iv() {
        $iv = "";
        foreach ($this->Keys as $v) {
            $arr = str_split($v, 2);
            $iv .= chr(hexdec($arr[1]));
        }
        return $iv;
    }

    function encrypt($str) {
        //加密，返回值使用base64重编码
        $key = $this->key;
        $size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
        $str = $this->pkcs5Pad($str, $size);
        return (base64_encode(mcrypt_encrypt(MCRYPT_DES, $key, $str, 'cbc', $this->iv())));
    }

    function decrypt($str) {
        //解密 输入值是base64重编码过的
        $strBin = base64_decode($str);
        $str = mcrypt_decrypt(MCRYPT_DES, $this->key, $strBin, 'cbc', $this->iv());
        $str = $this->pkcs5Unpad($str);
        return $str;
    }

    function pkcs5Unpad($text) {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, -1 * $pad);
    }

    function pkcs5Pad($dat, $blocksize) {
        $block = mcrypt_get_block_size('des', 'cbc');
        $len = strlen($dat);
        $padding = $block - ($len % $block);
        $dat .= str_repeat(chr($padding), $padding);
        return $dat;
    }
	
	private static $mInstace=null;
	
	public static function getInstance()  
    {  
        if (!(self::$mInstace instanceof self))  
        {  
            self::$mInstace = new self();  
        }  
        return self::$mInstace;  
    }  
	
	public static function encodeCookie($data)
	{
	    return self::getInstance()->encrypt($data);
    }
	
	public static function decodeCookie($data)
	{
	    return self::getInstance()->decrypt($data);
    }
	
	public static function encodeData($data,$key)
	{
	    $pThis=self::getInstance();
	    $pThis->key=$key;
	    return $pThis->encrypt($data);
    }
	
	public static function decodeData($data,$key)
	{
	    $pThis=self::getInstance();
	    $pThis->key=$key;
	    return $pThis->decrypt($data);
    }

}