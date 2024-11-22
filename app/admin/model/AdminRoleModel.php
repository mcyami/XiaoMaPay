<?php

namespace app\admin\model;

/**
 * @property integer $id ID(主键)
 * @property string $admin_id 管理员id
 * @property string $role_id 角色id
 */
class AdminRoleModel extends BaseModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'xm_admin_roles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';


    public $timestamps = false;

}
