# 部署方式
### 环境准备
#### 1.安装openresty

windows环境
```
下载解压:
32 位 Windows：https://openresty.org/download/openresty-1.15.8.1-win32.zip
64 位 Windows: https://openresty.org/download/openresty-1.15.8.1-win64.zip
```
Centos下

```
sudo yum install yum-utils
sudo yum-config-manager --add-repo https://openresty.org/package/centos/openresty.repo
sudo yum install openresty
```
其他linux环境参考:http://openresty.org/cn/linux-packages.html
#### 2.安装mysql/redis
比较简单，不详细展开

#### 3.安装PHP环境
要求在php5.6以上，装好mysql/redis扩展，比较简单，不详细展开，php环境主要是用来管理防火墙规则，参考thinkphp3.2环境配置

#### 4.配置规则管理平台
按thinkphp配置好，修改waf_admin\Application\Common\Conf\db.php到实际的mysql和redis配置信息，数据库初始化文件Doc\db_waf.sql,初始化完成后登陆管理平台配置规则，初始账号密码cheergo/admin

#### 5.配置nginx
##### 配置nginx.conf
http节点加入共享缓存配置
```
lua_shared_dict waf_cache_db 256m;
```
##### 配置你要做安全防护的站点

```
#后端服务器upstream，根据实际情况配置
upstream www_ycj {
	keepalive 15;
	server 172.25.10.123:80 weight=10 max_fails=3 fail_timeout=30s;
	server 172.25.10.124:80 weight=10 max_fails=3 fail_timeout=30s;
	server 172.25.10.125:80 weight=3 max_fails=3 fail_timeout=30s;
	server 172.25.10.126:80 weight=10 max_fails=3 fail_timeout=30s;
}

#waf_api是waf_admin所在的php服务器
upstream waf_api {
	keepalive 15;
	server 172.25.10.102:80;
}

server {
	    listen 80;
	    #www.xxx.com是你的站点域名,支持多域名，正则表达式配置
		server_name www.xxx.com;   
		
		error_log  /data/logs/nginx_error_xxx.com.log warn;
		
        #waf验证码反向代理到
		location /waf_auth/ {
			include proxy.conf;
			proxy_pass http://waf_api;
			break;
		}

		
		location / {
		     default_type text/html;
		     #下面修改成waf_main.lua的实际路径
			 access_by_lua_file /usr/local/openresty/lua/waf_main.lua;
			 include proxy.conf;
			 proxy_pass http://www_ycj;
		}
}
```



