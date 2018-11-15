<?php
namespace Com\Chw;

class RedisLib extends \Redis
{

    private static $_instanceList = array();

    public static function getInstance($configName = 'REDIS_DEFAULT')
    {
        if ( !(self::$_instanceList[$configName] instanceof self))
        {
            self::$_instanceList[$configName] = new self($configName);
        }

        self::$_instanceList[$configName]->select(self::RC($configName, 'REDIS_DB'));//默认连接配置的redis 数据库
        return self::$_instanceList[$configName];
    }


    static function RC($configName, $configVal)
    {
        $val = sprintf('%s.%s', $configName, $configVal);

        return C($val);
    }

    function __clone()
    {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }

    function __construct($configName)
    {
        $this->connect(self::RC($configName, 'REDIS_HOST'), self::RC($configName, 'REDIS_PORT'));

        $password = self::RC($configName, 'REDIS_PASSWORD');
        if ($password)
        {
            $this->auth($password);
        }
    }

    function is_unserialize($data)
    {
        $re = unserialize($data);
        if ( !$re)
        {
            return $data;
        }
        else
        {
            return $re;
        }
    }
}