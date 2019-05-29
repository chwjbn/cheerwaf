# Cheerwaf
## 设计背景
在一些大型信息展示的站点中，每天会面对大量的恶意漏洞盲扫、爬虫采集，往往在这样的站点场景中，流量很大，很难区分哪些是真实流量，哪些是采集垃圾流量，因此需要一个灵活规则的过滤器来进行筛选和拦截，笔者当时所在的云财经就是这么一个场景，每天的独立访问用户几十万，其中有效行为访问用户不到40%，针对这种场景开发了基于灵活自定义规则的web应用防火墙
## 架构
cheerwaf是基于openresty设计的web应用防火墙，技术架构如下：
![image](https://raw.githubusercontent.com/chwjbn/cheerwaf/master/Doc/arc.png)
- 防火墙逻辑部分采用openresty
- 防火墙规则读取采用二级缓存设计，nginx内存+redis
- waf的管理端采用thinkphp编写，规则保存在mysql，然后发布到redis
- waf规则过滤主要通过access_by_lua_file实现
- waf支持放行、拦截、行为可信加分、行为可疑加分
- waf支持的行为粒度单次会话、访客(cookie)、注册用户(cookie)、IP地址
- waf规则触发条件支持http头各字段、cookie字段、行为粒度的打分、行为粒度的单位时间访问次数
- waf规则内置IP段抓取防护
- waf规则内置伪搜索引擎蜘蛛防护
## 产品截图
![image](https://raw.githubusercontent.com/chwjbn/cheerwaf/master/Doc/w1.png)
![image](https://raw.githubusercontent.com/chwjbn/cheerwaf/master/Doc/w2.png)
![image](https://raw.githubusercontent.com/chwjbn/cheerwaf/master/Doc/w3.png)
![image](https://raw.githubusercontent.com/chwjbn/cheerwaf/master/Doc/w4.png)
## 开源地址
- https://github.com/chwjbn/cheerwaf
- 交流QQ：541601334
## 支持
![image](https://raw.githubusercontent.com/chwjbn/cheerwaf/master/Doc/zan.png)
