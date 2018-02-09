--
-- Created by IntelliJ IDEA.
-- User: qitmac000378
-- Date: 17/5/5
-- Time: 下午5:42
-- To change this template use File | Settings | File Templates.
--

local _M = {}
_M.redis = {}
-- l-qchatdb1.vc.cn5.qunar.com  不能用host,解析不了
_M.redis.host = '<?qtalk redis>'
_M.redis.port = '6379'
_M.redis.subpool = 2
_M.redis.passwd = '27594e8a-877e-11e5-bd52-6bced77c06ee'

return _M
