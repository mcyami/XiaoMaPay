<?php

namespace app\common\model;

/**
 * @property integer $id ID(主键)
 * @property integer $category 分类id
 * @property string $title 标题
 * @property string $thumb 缩略图
 * @property string $content 内容
 * @property string $role_id 角色id
 * @property integer $status 状态 1正常 0禁用
 * @property integer $sort 排序
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 */
class ArticleModel extends BaseModel {
    // 表名称
    protected $table = 'xm_article';

    // 主键
    protected $primaryKey = 'id';

    // 时间戳存储格式
    protected $dateFormat = 'U';

    const ARTICLE_STATUS_ENABLE = 1; // 启用
    const ARTICLE_STATUS_DISABLE = 0; // 禁用

}
