<?php

namespace app\admin\model;

use app\admin\cache\RuleCache;

/**
 * @property integer $id ID(主键)
 * @property integer $user_type 用户类型 1:A端后台用户 2:B端用户 3:C端用户
 * @property integer $user_id 操作用户id
 * @property string $user_name 操作用户名称
 * @property string $user_agent 浏览器UserAgent
 * @property integer $client_ip 客户端IP
 * @property integer $category 分类id
 * @property string $controller 控制器
 * @property string $action 操作
 * @property string $action_name 操作名称
 * @property integer $related_id 关联记录id
 * @property string $before_data 修改前数据
 * @property string $after_data 修改后数据
 * @property string $msg 操作描述
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 */
class LogModel extends BaseModel {
    // 表名称
    protected $table = 'xm_log';

    // 主键
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    // 操作用户类型，如果修改了要与配置项CATE_LOG_USER同步
    const OP_USER_TYPE_ADMIN = 1; // A端后台用户
    const OP_USER_TYPE_CUSTOMER = 2; // B端用户
    const OP_USER_TYPE_CLIENT = 3; // C端用户

    // 操作类型，如果修改了要与配置项CATE_LOG_TYPE同步
    // 日志开关在日志配置中操作
    const OP_TYPE_ACCOUNT = 1; // 后台账户
    const OP_TYPE_ADMIN = 2; // 管理员操作
    const OP_TYPE_ROLE = 3; // 角色操作
    const OP_TYPE_RULE = 4; // 菜单操作
    const OP_TYPE_CONFIG = 5; // 配置操作
    const OP_TYPE_ARTICLE = 6; // 文章操作
    const OP_TYPE_ATTACHMENT = 7; // 附件操作
    const OP_TYPE_PAY_METHOD = 8; // 支付方式
    const OP_TYPE_PAY_CHANNEL = 9; // 支付通道

    /**
     * 保存日志
     * @param int $user_type 用户类型
     * @param int $opType 操作类型
     * @param int $relatedId 关联记录id
     * @param string $beforeData 修改前数据
     * @param string $afterData 修改后数据
     * @param string $msg 操作描述
     * @return int
     */
    public static function saveLog($user_type, $opType, $relatedId = 0, $beforeData = '', $afterData = '', $msg = '') {
        // LOG_SWITCH 为日志开关，如果关闭则不记录日志
        if (!C('LOG_SWITCH', null, 0)) { // 默认关闭
            return 0;
        }
        // 子开关
        $log_switch = C('LOG_SWITCH_' . $opType, null, 0); // 默认关闭
        if (!$log_switch) {
            return 0;
        }
        $log = new LogModel;
        $request = request();
        $log->controller = $request->controller;
        $log->action = $request->action;
        $log->user_agent = $request->header('User-Agent');
        $log->client_ip = ip2long($request->getRealIp(true));
        $log->category = $opType;
        $log->user_type = $user_type;
        if ($user_type == self::OP_USER_TYPE_ADMIN) {
            $log->user_id = session('admin.id');
            $log->user_name = session('admin.username');
        }
        $log->related_id = $relatedId;
        $log->before_data = is_array($beforeData) ? json_encode($beforeData, JSON_UNESCAPED_UNICODE) : $beforeData;
        $log->after_data = is_array($afterData) ? json_encode($afterData, JSON_UNESCAPED_UNICODE) : $afterData;
        $log->msg = $msg;
        // 操作名称，从Rule中匹配获取
        $actions = RuleCache::getSystemRuleTitle();
        if ($actions) {
            $action_key = $request->controller . '@' . $request->action;
            $log->action_name = $actions[$action_key] ?? '';
        }
        $log->save();
        return $log->id;
    }

}
