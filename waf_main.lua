--全局存取数据
local wafGlobalData={}

--写日志
function wafLog(logData,logFmt)
	local data=string.format(logData,logFmt)
	ngx.log(ngx.INFO,data)
end

--警告日志
function wafWarn(logData,logFmt)
	local data=string.format(logData,logFmt)
	ngx.log(ngx.WARN,data)
end

--判断table是否为空
function isTableEmpty(t)
    return t == nil or next(t) == nil
end

--从Redis获取
function getDataFromRedis(dataKey)
	local data=nil
	local redisLib = require('resty.redis')
	local redisHandle=redisLib.new()
	local ok,err=redisHandle.connect(redisHandle, '127.0.0.1', '6379')
	
	if not ok then
		wafWarn('redis error: %s', err)
		return data
	end
	
	redisHandle:select(0)
	
	local res,err=redisHandle:get(dataKey)
	redisHandle:close()
	
	wafLog('getRuleFromRedis dataKey=%s',dataKey)
	
	if not res then
		wafLog('redis error: %s', err)
	else
		data=res
	end
	
	return data
end

--从Redis获取分数
function getScoreFromRedis(dataKey)
	local data=0
	local redisLib = require('resty.redis')
	local redisHandle=redisLib.new()
	local ok,err=redisHandle.connect(redisHandle, '127.0.0.1', '6379')
	
	if not ok then
		wafWarn('redis error: %s', err)
		return data
	end
	
	redisHandle:select(1)
	
	local res,err=redisHandle:get(dataKey)
	redisHandle:close()
	
	wafLog('getScoreFromRedis dataKey=%s',dataKey)
	
	if not res then
		wafLog('redis error: %s', err)
	else
		data=tonumber(res)
	end
	
	if not data then
		data=0
	end
	
	return data
end

--设置Redis分数
function setScoreToRedis(dataKey,dataVal,timeOut)
	local redisLib = require('resty.redis')
	local redisHandle=redisLib.new()
	local ok,err=redisHandle.connect(redisHandle, '127.0.0.1', '6379')
	
	if not ok then
		wafWarn('redis error: %s', err)
		return
	end
	
	redisHandle:select(1)
	
	redisHandle:set(dataKey,dataVal)
	redisHandle:expire(dataKey,timeOut*1000)
	redisHandle:close()
	
	wafLog('setScoreToRedis dataKey=%s',dataKey)
end

--从全局缓存中获取
function getWafDataFromCacheDb(dataKey)
	local data={}
	local dbHandle=ngx.shared.waf_cache_db
	
	if not dbHandle then
		return data
	end
	
	--缓存中获取
	local dataVal=dbHandle:get(dataKey)
	
	--是否需要更新到缓存
	local bNeedUpdate=false
	
	--缓存中没有,从Redis获取
	if not dataVal then
		dataVal=getDataFromRedis(dataKey)
		bNeedUpdate=true
	end
	
	--都没有直接返回空table
	if not dataVal then
		return data
	end
	
	--更新到缓存
	if bNeedUpdate then
		dbHandle:set(dataKey,dataVal,600);
	end
	
	--反解析为table
	local jsonLib = require('cjson')	
	data=jsonLib.decode(dataVal)
	
	if not data then
		data={}
	end
	
	return data
end

--获取站点规则列表
function getWafSiteTable()
	local data=getWafDataFromCacheDb('waf_site')
	if not data then
		data={}
	end
	return data
end

--通过host查找站点ID
function getWafSiteIdByHost(host)
	local siteId=0
	
	local wafSiteTable=getWafSiteTable()
	
	for k,v in ipairs(wafSiteTable) do
				
		local id=tonumber(v.id)
		local httpHostType=v.http_host_type
		local httpHost=v.http_host
		
		if httpHostType=='string' then
			if httpHost==host then
				siteId=id
				return siteId
			end
		end
		
		if httpHostType=='regex' then
			if ngx.re.match(host,httpHost,'jio') then
				siteId=id
				return siteId
			end
		end
		
	end
	
	return siteId
end

--通过站点ID查找规则列表
function getWafRuleListBySiteId(siteId)
	local dataKey=string.format('waf_site_node_%d',siteId)
	local dataList=getWafDataFromCacheDb(dataKey)
	
	if not dataList then
		dataList={}
	end
	
	return dataList
end

--通过站点ID查找规则判断逻辑列表
function getWafLogicListBySiteId(siteId)
	local dataKey=string.format('waf_site_logic_%d',siteId)
	local dataList=getWafDataFromCacheDb(dataKey)
	
	if not dataList then
		dataList={}
	end
	
	return dataList
end

--获取客户端IP
function getClientIp()
	local header = ngx.req.get_headers()
	local data=header['X-Real-IP']
	
	if not data then
		data=header['X_FORWARDED_FOR']
	end
	
	if not data then
		data=ngx.var.remote_addr
	end
	
	if not data then
		data='0.0.0.0'
	end
	
	return data
end

--生成wafsid
function genWafSid()
	local data=getClientIp()
	local timeStr=os.date("%Y%m%d%H%M%S")
	local randNum=math.random()
	data=data..timeStr..randNum
	data=ngx.md5(data)
	data='wafsid_'..data
	return data
end

--获取环境变量
function getEnvData(withScore)
	local data={}
	
	local httpHeader = ngx.req.get_headers()
	
	data.s_http_ip=getClientIp()
	if not data.s_http_ip then
		data.s_http_ip='0.0.0.0'
	end
	
	data.s_http_header_method=ngx.var.request_method
	if not data.s_http_header_method then
		data.s_http_header_method=''
	end
	
	data.s_http_header_host=ngx.var.host
	if not data.s_http_header_host then
		data.s_http_header_host=''
	end
	
	data.s_http_header_useragent=ngx.var.http_user_agent
	if not data.s_http_header_useragent then
		data.s_http_header_useragent=''
	end
	
	data.s_http_header_url=ngx.var.request_uri
	if not data.s_http_header_url then
		data.s_http_header_url=''
	end
	
	data.s_http_header_referer=ngx.var.http_referer
	if not data.s_http_header_referer then
		data.s_http_header_referer=''
	end
	
	data.s_http_header_cookie=ngx.var.http_cookie
	if not data.s_http_header_cookie then
		data.s_http_header_cookie=''
	end
	
	data.s_http_header_x_requested_with=httpHeader['X-Requested-With']
	if not data.s_http_header_x_requested_with then
		data.s_http_header_x_requested_with=''
	end
	
	data.s_cookie_wafsid=ngx.var.cookie_ycj_wafsid
	if not data.s_cookie_wafsid then
		data.s_cookie_wafsid=genWafSid()
		ngx.header["Set-Cookie"] = 'ycj_wafsid='..data.s_cookie_wafsid..'; Path=/; domain=.yuncaijing.com; Expires=' .. ngx.cookie_time(ngx.time() + 2592000)
	end
	
	data.s_cookie_uuid=ngx.var.cookie_ycj_uuid
	if not data.s_cookie_uuid then
		data.s_cookie_uuid=''
	end
	
	data.s_cookie_uid=ngx.var.cookie_ycj_main_token
	if not data.s_cookie_uid then
		data.s_cookie_uid=''
	end
	
	if string.len(data.s_cookie_uid)>45 then
		data.s_cookie_uid=string.sub(data.s_cookie_uid,10,41)
	end
	
	data.s_cookie_token=ngx.var.cookie_ycj_main_token
	if not data.s_cookie_token then
		data.s_cookie_token=''
	end
	
	if not withScore then
		return data
	end
	
	--当前访问白加分
	data.d_score_session_white=wafGlobalData.session_white
	if not data.d_score_session_white then
		data.d_score_session_white=0
		wafGlobalData.session_white=data.d_score_session_white
	end
	
	--当前访问黑加分
	data.d_score_session_black=wafGlobalData.session_black
	if not data.d_score_session_black then
		data.d_score_session_black=0
		wafGlobalData.session_black=data.d_score_session_black
	end
	
	data.d_score_uuid_white=0
	if string.len(data.s_cookie_uuid)>0 then
		data.d_score_uuid_white=getScoreFromRedis('scorewhite_uuid_'..data.s_cookie_uuid)
	end
	
	data.d_score_uuid_black=0
	if string.len(data.s_cookie_uuid)>0 then
		data.d_score_uuid_black=getScoreFromRedis('scoreblack_uuid_'..data.s_cookie_uuid)
	end
	
	data.d_score_uid_white=0
	if string.len(data.s_cookie_uid)>0 then
		data.d_score_uid_white=getScoreFromRedis('scorewhite_uid_'..data.s_cookie_uid)
	end
	
	data.d_score_uid_black=0
	if string.len(data.s_cookie_uid)>0 then
		data.d_score_uid_black=getScoreFromRedis('scoreblack_uid_'..data.s_cookie_uid)
	end
	
	data.d_score_ip_white=0
	if string.len(data.s_http_ip)>0 then
		data.d_score_ip_white=getScoreFromRedis('scorewhite_ip_'..data.s_http_ip)
	end
	
	data.d_score_ip_black=0
	if string.len(data.s_http_ip)>0 then
		data.d_score_ip_black=getScoreFromRedis('scoreblack_ip_'..data.s_http_ip)
	end
	
	return data
end

--显示拦截页面
function readShowPage(pageFile)

	local data=''
	
	local info = debug.getinfo(1, "S")
	local path = info.source
	path = string.sub(path, 2, -1)
	path = string.match(path, "^.*/")
	path=path..pageFile
	
	local file = io.input(path)
	
	repeat
		local line = io.read()
		if nil == line then
			break
		end
		data=data..line
	until(false)
	
	io.close(file)
	
	return data

end

function rejectAccess(ip,code,rule_id)

	local pageData=''
	
	--100024:ip被封
	if code=='100024' then
		pageData=readShowPage('ip_block.html')
	else
		pageData=readShowPage('session_block.html')
	end
	
	if not pageData then
		pageData='Access Blocked'
	end
	
	pageData=string.gsub(pageData,'{$waf_client_ip}',ip)
	pageData=string.gsub(pageData,'{$waf_code}',code)
	pageData=string.gsub(pageData,'{$waf_rule_id}',rule_id)
	
	ngx.status=406
	ngx.send_headers()
	ngx.say(pageData)
	ngx.exit(0)
	
end

function exitWithBadRequest()
	ngx.status=400
	ngx.send_headers()
	ngx.say('Bad Request')
	ngx.exit(0)
end

--当前逻辑判断
function currentLogicJudge(current_logic_key,current_logic_type,current_logic_value,envData)
	local bRet=false
	
	local envValue=envData[current_logic_key]
	
	--环境变量中不存在此判断项目
	if not envValue then
		return bRet
	end
	
	--数据类型
	local keyPrefix=string.sub(current_logic_key,1,2)
	
	if current_logic_type=='eq' then
		if envValue==current_logic_value then
			return true
		end
	end
	
	if current_logic_type=='lt' then
		if envValue<current_logic_value then
			return true
		end
	end
	
	if current_logic_type=='gt' then
		if envValue>current_logic_value then
			return true
		end
	end
	
	if current_logic_type=='lte' then
		if envValue<=current_logic_value then
			return true
		end
	end
	
	if current_logic_type=='gte' then
		if envValue>=current_logic_value then
			return true
		end
	end
	
	if current_logic_type=='neq' then
		if envValue~=current_logic_value then
			return true
		end
	end
	
	if current_logic_type=='regex' and keyPrefix=='s_' then
		if ngx.re.match(envValue,current_logic_value,'jio') then
			wafLog('currentLogicJudge regex=true')
			return true
		end
	end
		
	return bRet
end

--判断是否匹配规则
function matchRuleLogic(logicList,currentLogicId,envData)
	
	local bRet=false
	
	local currentLogicData=nil
	
	for logicKey,logicItem in ipairs(logicList) do
		
		if not logicItem.id then
			break
		end
		
		local logicId=tonumber(logicItem.id)
		
		--最右侧逻辑
		if currentLogicId<=0 then
			if logicItem.rule_logic_type=='2' then
				currentLogicData=logicItem
				currentLogicId=logicId
				break
			end	
		else	
			if currentLogicId==logicId then
				currentLogicData=logicItem
				currentLogicId=logicId
				break
			end	
		end
		
	end
	
	if not currentLogicData then
		return bret
	end
	
	local current_logic_key=currentLogicData.current_logic_key
	if not current_logic_key then
		return bRet
	end
	
	local current_logic_type=currentLogicData.current_logic_type
	if not current_logic_type then
		return bret
	end
	
	local current_logic_value=currentLogicData.current_logic_value
	if not current_logic_value then
		return bret
	end
	
	local left_logic_type=currentLogicData.left_logic_type
	if not left_logic_type then
		return bRet
	end
	
	--数字类型的数据转换
	local keyPrefix=string.sub(current_logic_key,1,2)
	if keyPrefix=='d_' then
		current_logic_value=tonumber(current_logic_value)
	end
	
	local left_logic_id=0
	if not currentLogicData.left_logic_id then
		left_logic_id=0
	else
		left_logic_id=tonumber(currentLogicData.left_logic_id)
	end
	
	local currentResult=currentLogicJudge(current_logic_key,current_logic_type,current_logic_value,envData)
	local leftLogicResult=true

	if left_logic_id>0 then
		leftLogicResult=matchRuleLogic(logicList,left_logic_id,envData)
	end
	
	if left_logic_type=='and' then
		bRet=leftLogicResult and currentResult
	end
	
	if left_logic_type=='or' then
		bRet=leftLogicResult or currentResult
	end
	
	if left_logic_type=='andnot' then
		bRet=leftLogicResult and (not currentResult)
	end
	
	if left_logic_type=='ornot' then
		bRet=leftLogicResult or (not currentResult)
	end
	
	if bRet then
		wafWarn('match logicId=%d',currentLogicId)
	end
		
	return bRet
end

--响应规则动作
function doAction(ruleData,envData)
	local nRet=0
	
	local action_type=ruleData.action_type
	if not action_type then
		return nRet
	end
	
	local action_target=ruleData.action_target
	if not action_target then
		return nRet
	end
	
	local action_value=ruleData.action_value
	if not action_value  then
		return nRet
	end
	
	--放行
	if action_type=='white' then
		if action_target=='session' then
			nRet=11
			return nRet
		end
		
		if action_target=='uuid' then
			local dataKey='white_uuid_'..envData.s_cookie_uuid
			if string.len(envData.s_cookie_uuid)>1 then
				setScoreToRedis(dataKey,100,3600*24*2)
			end
			
			nRet=12
			return nRet
		end
		
		if action_target=='uid' then
			local dataKey='white_uid_'..envData.s_cookie_uid
			if string.len(envData.s_cookie_uid)>1 then
				setScoreToRedis(dataKey,100,3600*24*7)
			end
			
			nRet=13
			return nRet
		end
		
		if action_target=='ip' then
			local dataKey='white_ip_'..envData.s_http_ip
			setScoreToRedis(dataKey,100,3600*24)
			nRet=14
			return nRet
		end
	end
	
	--拦截
	if action_type=='black' then
		if action_target=='session' then
			nRet=21
			return nRet
		end
		
		if action_target=='uuid' then
			local dataKey='black_uuid_'..envData.s_cookie_uuid
			if string.len(envData.s_cookie_uuid)>1 then
				setScoreToRedis(dataKey,100,3600*24*2)
			end
			
			nRet=22
			return nRet
		end
		
		if action_target=='uid' then
			local dataKey='black_uid_'..envData.s_cookie_uid
			if string.len(envData.s_cookie_uid)>1 then
				setScoreToRedis(dataKey,100,3600*24*7)
			end
			
			nRet=23
			return nRet
		end
		
		if action_target=='ip' then
			local dataKey='black_ip_'..envData.s_http_ip
			setScoreToRedis(dataKey,100,3600*24)
			nRet=24
			return nRet
		end
	end
	
	local action_value_score=tonumber(action_value)
	
	--可信加分
	if action_type=='white_score' then
		if action_target=='session' then
			wafGlobalData.session_white=(wafGlobalData.session_white or 0)+action_value_score
			nRet=0
			return nRet
		end
		
		if action_target=='uuid' then
			local dataKey='scorewhite_uuid_'..envData.s_cookie_uuid
			local dataV=envData.d_score_uuid_white+action_value_score
			if string.len(envData.s_cookie_uuid)>1 then
				setScoreToRedis(dataKey,dataV,3600*24*2)
			end
			
			nRet=0
			return nRet
		end
		
		if action_target=='uid' then
			local dataKey='scorewhite_uid_'..envData.s_cookie_uid
			local dataV=envData.d_score_uid_white+action_value_score
			if string.len(envData.s_cookie_uid)>1 then
				setScoreToRedis(dataKey,dataV,3600*24*7)
			end
			
			nRet=0
			return nRet
		end
		
		if action_target=='ip' then
			local dataKey='scorewhite_ip_'..envData.s_http_ip
			local dataV=envData.d_score_ip_white+action_value_score
			setScoreToRedis(dataKey,dataV,3600*24)
			nRet=0
			return nRet
		end
	end
	
	--可疑加分
	if action_type=='black_score' then
		if action_target=='session' then
			wafGlobalData.session_black=(wafGlobalData.session_black or 0)+action_value_score
			nRet=0
			return nRet
		end
		
		if action_target=='uuid' then
			local dataKey='scoreblack_uuid_'..envData.s_cookie_uuid
			local dataV=envData.d_score_uuid_black+action_value_score
			if string.len(envData.s_cookie_uuid)>1 then
				setScoreToRedis(dataKey,dataV,3600*24*2)
			end
			
			nRet=0
			return nRet
		end
		
		if action_target=='uid' then
			local dataKey='scoreblack_uid_'..envData.s_cookie_uid
			local dataV=envData.d_score_uid_black+action_value_score
			if string.len(envData.s_cookie_uid)>1 then
				setScoreToRedis(dataKey,dataV,3600*24*7)
			end
			
			nRet=0
			return nRet
		end
		
		if action_target=='ip' then
			local dataKey='scoreblack_ip_'..envData.s_http_ip
			local dataV=envData.d_score_ip_black+action_value_score
			setScoreToRedis(dataKey,dataV,3600*24)
			nRet=0
			return nRet
		end
	end
	
	return nRet
end

--内置放白规则
function checkSpWhiteAccess()
	local nRet=0
	local envData=getEnvData(false)
	local dataKey=''
	
	local uid=envData.s_cookie_uid;
	
	if string.len(uid)>0 then
		nRet=1001
		return nRet
	end
	
	local url=envData.s_http_header_url
	
	--验证码显示页面
	if url=='/waf_auth/check_code_page.html' then
		nRet=1002
		return nRet
	end
	
	--验证码校验页面
	if url=='/waf_auth/check_code_task.html' then
		nRet=1003
		return nRet
	end
	
	return nRet
end

--检查白
function checkWhiteAccess()
	local nRet=0
	local envData=getEnvData(false)
	local dataKey=''
	
	dataKey='white_uuid_'..envData.s_cookie_uuid
	if string.len(envData.s_cookie_uuid)>1 then
		local dataV=getScoreFromRedis(dataKey)
		if dataV>0 then
		nRet=12
		return nRet
		end
	end
	
	dataKey='white_uid_'..envData.s_cookie_uid
	if string.len(envData.s_cookie_uid)>1 then
		local dataV=getScoreFromRedis(dataKey)
		if dataV>0 then
		nRet=13
		return nRet
		end
	end
	
	dataKey='white_ip_'..envData.s_http_ip
	if string.len(envData.s_http_ip)>1 then
		local dataV=getScoreFromRedis(dataKey)
		if dataV>0 then
		nRet=14
		return nRet
		end
	end
	
	return nRet
end

--检查黑
function checkBlackAccess()
	local nRet=0
	local envData=getEnvData(false)
	local dataKey='';
	
	dataKey='black_uuid_'..envData.s_cookie_uuid
	if string.len(envData.s_cookie_uuid)>1 then
		local dataV=getScoreFromRedis(dataKey)
		if dataV>0 then
		nRet=22
		return nRet
		end
	end
	
	dataKey='black_uid_'..envData.s_cookie_uid
	if string.len(envData.s_cookie_uid)>1 then
		local dataV=getScoreFromRedis(dataKey)
		if dataV>0 then
		nRet=23
		return nRet
		end
	end
	
	dataKey='black_ip_'..envData.s_http_ip
	if string.len(envData.s_http_ip)>1 then
		local dataV=getScoreFromRedis(dataKey)
		if dataV>0 then
		nRet=24
		return nRet
		end
	end
	
	return nRet
end

--检查访问
function checkAccessTask()
	local httpHeaders = ngx.req.get_headers()
	
	local httpHost=httpHeaders['host']
	if not httpHost then
		exitWithBadRequest()
		return
	end
	
	--获取当前站点对应的站点规则ID
	local siteId=getWafSiteIdByHost(httpHost)
	
	if not siteId then
		return
	end
	
	--获取当前站点规则ID对应的规则动作
	local ruleList=getWafRuleListBySiteId(siteId)

	if not ruleList then
		return
	end
	
	if isTableEmpty(ruleList) then
		return
	end
	
	--获取当前站点规则ID对应的规则逻辑
	local logicList=getWafLogicListBySiteId(siteId)
	
	if not logicList then
		return
	end
	
	if isTableEmpty(logicList) then
		return
	end
	
	
	for ruleKey,ruleItem in ipairs(ruleList) do
		local ruleLogicList={}
		for logicKey,logicItem in ipairs(logicList) do
			if ruleItem.id==logicItem.rule_node_id then
				table.insert(ruleLogicList,logicItem)
			end
		end
		
		local envData=getEnvData(true)
		if matchRuleLogic(ruleLogicList,0,envData) then
			local nRet=doAction(ruleItem,envData)
			
			if nRet>20 then
				wafWarn('checkAccessTask match ruleId='..ruleItem.id)
				rejectAccess(envData.s_http_ip,string.format('2000%d',nRet),ruleItem.id)
			end
			
			--退出后面的规则检测
			if nRet>0 then
				wafWarn('checkAccessTask match ruleId='..ruleItem.id)
				break
			end
		end
		
	end
end

function checkAccess()
	
	ngx.header["Server"]='CheerWaf'
	
	local envData=getEnvData(false)
	
	--内置放行
	local checkSpWhiteAccessFlag=checkSpWhiteAccess()
	if checkSpWhiteAccessFlag>0 then
		return
	end
	
	--命中放行
	local checkWhiteAccessFlag=checkWhiteAccess()
	if checkWhiteAccessFlag>0 then
		return
	end
	
	--命中拦截
	local checkBlackAccessFlag=checkBlackAccess()
	if checkBlackAccessFlag>0 then
		rejectAccess(envData.s_http_ip,string.format('1000%d',checkBlackAccessFlag),'a')
		return
	end
	

	checkAccessTask()
end

wafLog('====================================================================================================')
checkAccess()