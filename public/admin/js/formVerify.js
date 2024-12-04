/**
 * 后台表单自定义验证规则
 */
layui.use(function(){
    var form = layui.form;
    form.verify({
        // 验证用户名
        username: function (value, elem) {
            // 为空不检测
            if (!value) return;
            if (value.length < 3) {
                return '用户名长度不能小于3个字符';
            }
            if (!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)) {
                return '用户名不能有特殊字符';
            }
            if (/(^_)|(__)|(_+$)/.test(value)) {
                return '用户名首尾不能出现下划线';
            }
            if (/^\d+$/.test(value)) {
                return '用户名不能全为数字';
            }
        },
        // 验证密码
        password: function(value, elem) {
            // 为空不检测
            if (!value) return;
            if (!/^[\S]{6,16}$/.test(value)) {
                return '密码必须为 6 到 16 位的非空字符';
            }
        },
    });
});