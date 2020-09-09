<?php
/*
 * @Author: 小小酥很酥
 * @Date: 2020-08-01 18:52:16
 * @LastEditTime: 2020-09-09 17:42:12
 * @Description: 全局设置
 * @Copyright 2020 运城市盘石网络科技有限公司
 */
defined('IN_LCMS') or exit('No permission');
load::sys_class('adminbase');
class admin extends adminbase
{
    public function __construct()
    {
        global $_L;
        parent::__construct();
    }
    public function doindex()
    {
        global $_L;
        switch ($_L['form']['action']) {
            case 'save':
                LCMS::config(array(
                    "do"   => "save",
                    "type" => "sys",
                    "cate" => "admin",
                    "lcms" => true,
                ));
                ajaxout(1, "保存成功");
                break;
            default:
                $config = LCMS::config(array(
                    "type" => "sys",
                    "cate" => "admin",
                    "lcms" => true,
                ));
                $config['oauth_code'] = $config['oauth_code'] ? $config['oauth_code'] : strtoupper(md5(HTTP_HOST)) . randstr(32);
                $form                 = array(
                    ["layui" => "title", "title" => "基础信息"],
                    ["layui" => "input", "title" => "系统名称",
                        "name"   => "LC[title]",
                        "value"  => $config['title'],
                        "verify" => "required",
                    ],
                    ["layui" => "radio", "title" => "后台协议",
                        "name"   => "LC[https]",
                        "value"  => $config['https'] ? $config['https'] : "0",
                        "radio"  => [
                            ["title" => "https://", "value" => "1"],
                            ["title" => "http://", "value" => "0"],
                        ],
                        "tips"   => "如果使用了cdn半程加密，会用到",
                    ],
                    ["layui"      => "input", "title" => "后台域名",
                        "name"        => "LC[domain]",
                        "value"       => $config['domain'],
                        "placeholder" => "不填任意域名可访问后台",
                        "tips"        => "不填任意域名可打开后台，填写后任意域名访问后台，均自动跳转到填写的域名",
                    ],
                    ["layui" => "input", "title" => "开发者",
                        "name"   => "LC[developer]",
                        "value"  => $config['developer'],
                        "verify" => "required"],
                    ["layui" => "upload",
                        "title"  => "后台LOGO",
                        "name"   => "LC[logo]",
                        "value"  => $config['logo'],
                    ],
                    ["layui" => "title", "title" => "通知公告"],
                    ["layui" => "editor", "title" => "后台公告",
                        "name"   => "LC[gonggao]",
                        "value"  => $config['gonggao'],
                    ],
                    ["layui" => "btn", "title" => "立即保存"],
                );
                require LCMS::template("own/admin_index");
                break;
        }
    }
    public function dosafe()
    {
        global $_L;
        switch ($_L['form']['action']) {
            case 'save':
                if ($_L['form']['LC']['dir'] != $_L['config']['admin']['dir']) {
                    if (!getdirpower(PATH_WEB)) {
                        unset($_L['form']['LC']['dir']);
                        ajaxout(1, "根目录没有写权限", "reload");
                    } else {
                        $change = true;
                    }
                }
                LCMS::config(array(
                    "do"   => "save",
                    "type" => "sys",
                    "cate" => "admin",
                    "lcms" => true,
                ));
                if ($change) {
                    ajaxout(1, "保存成功", "{$_L['url']['own_form']}change&olddir={$_L['config']['admin']['dir']}&newdir={$_L['form']['LC']['dir']}");
                } else {
                    ajaxout(1, "保存成功", "reload");
                }
                break;
            default:
                $config = LCMS::config(array(
                    "type" => "sys",
                    "cate" => "admin",
                    "lcms" => true,
                ));
                $form = [
                    ["layui" => "title", "title" => "登陆安全"],
                    ["layui" => "input", "title" => "后台目录", "name" => "LC[dir]", "value" => $config['dir'] ? $config['dir'] : "admin", "tips" => "建议修改后台目录提高安全性", "verify" => "required"],
                    ["layui" => "slider", "title" => "自动登出", "name" => "LC[sessiontime]", "value" => $config['sessiontime'], "tips" => "指定时间无操作自动登出，0为不自动登出", "min" => "0", "max" => "60", "step" => "5", "settips" => "分钟"],
                    ["layui" => "radio", "title" => "登陆限制", "name" => "LC[login_limit]", "value" => $config['login_limit'] != null ? $config['login_limit'] : "0", "tips" => "是否限制一个账号可同时在多个设备登陆", "radio" => [
                        ["title" => "单设备", "value" => "0"],
                        ["title" => "多设备", "value" => "1"],
                    ]],
                    ["layui" => "radio", "title" => "登陆验证码", "name" => "LC[login_code][type]", "value" => $config['login_code']['type'] != null ? $config['login_code']['type'] : "0", "radio" => [
                        ["title" => "普通验证码", "value" => "0", "tab" => "login_code"],
                        ["title" => "Luosimao人机验证", "value" => "luosimao", "tab" => "login_code_luosimao"],
                    ]],
                    ["layui" => "input", "title" => "使用域名", "name" => "LC[login_code][domain]", "value" => $config['login_code']['domain'], "cname" => "hidden login_code_luosimao login_code_tencent", "placeholder" => "请填写主域名！", "tips" => "请填写主域名！"],
                    ["layui" => "input", "title" => "site_key", "name" => "LC[login_code][luosimao][site_key]", "value" => $config['login_code']['luosimao']['site_key'], "cname" => "hidden login_code_luosimao"],
                    ["layui" => "input", "title" => "api_key", "name" => "LC[login_code][luosimao][api_key]", "value" => $config['login_code']['luosimao']['api_key'], "cname" => "hidden login_code_luosimao"],
                    ["layui" => "title", "title" => "其它安全"],
                    ["layui" => "input", "title" => "上传大小", "name" => "LC[attsize]", "value" => $config['attsize'], "tips" => "限制上传文件的大小，单位KB", "verify" => "required"],
                    ["layui" => "tags", "title" => "格式白名单", "name" => "LC[mimelist]", "value" => $config['mimelist'], "tips" => "允许上传白名单里的文件格式", "verify" => "required"],
                    ["layui" => "title", "title" => "性能优化"],
                    ["layui" => "on", "title" => "SESSION", "name" => "LC[session_type]", "value" => $config['session_type'] ? $config['session_type'] : "0", "text" => "Redis存储|文件存储", "tips" => "SESSION的存储方式", "url" => "{$_L['url']['own_form']}checkredis"],
                    ["layui" => "btn", "title" => "立即保存"],
                ];
                require LCMS::template("own/admin_safe");
                break;
        }
    }
    public function dochange()
    {
        global $_L;
        if (LCMS::SUPER()) {
            if ($_L['form']['olddir'] && $_L['form']['newdir'] && is_dir(PATH_WEB . $_L['form']['olddir']) && !is_dir(PATH_WEB . $_L['form']['newdir']) && movedir(PATH_WEB . $_L['form']['olddir'], PATH_WEB . $_L['form']['newdir'])) {
                echo '<script type="text/javascript">top.location.href = "' . $_L['url']['site'] . $_L['form']['newdir'] . '";</script>';
            } else {
                LCMS::X(500, "发生致命错误，您需要在FTP中手动修改后台目录");
            }
        } else {
            LCMS::X(403, "您没有权限修改后台目录");
        }
    }
    public function doplugin()
    {
        global $_L;
        switch ($_L['form']['action']) {
            case 'save':
                LCMS::config(array(
                    "do"   => "save",
                    "type" => "sys",
                    "cate" => "plugin",
                ));
                ajaxout(1, "保存成功");
                break;
            case 'test_eamil':
                $email = $_L['form']['LC']['email'];
                if ($email) {
                    load::sys_class("email");
                    $r = EMAIL::send([
                        "to"       => $email['from'],
                        "toname"   => "发送测试",
                        "subject"  => "邮件发送测试",
                        "body"     => "恭喜您！邮件服务配置成功！",
                        "fromname" => $email['fromname'],
                        "from"     => $email['from'],
                        "pass"     => $email['pass'],
                        "smtp"     => $email['smtp'],
                        "ssl"      => $email['ssl'],
                        "port"     => $email['port'],
                    ]);
                }
                if ($r['code'] == "1") {
                    ajaxout(1, "邮件服务配置成功");
                } else {
                    ajaxout(0, "邮件服务配置失败！{$r['msg']}");
                }
                break;
            default:
                $plugin = LCMS::config(array(
                    "type" => "sys",
                    "cate" => "plugin",
                ));
                $email = array(
                    array("layui" => "title", "title" => "邮箱配置"),
                    array("layui" => "input", "title" => "发件人", "name" => "LC[email][fromname]", "value" => $plugin['email']['fromname']),
                    array("layui" => "input", "title" => "邮箱账号", "name" => "LC[email][from]", "value" => $plugin['email']['from']),
                    array("layui" => "input", "title" => "SMTP密码", "name" => "LC[email][pass]", "value" => $plugin['email']['pass'], "type" => "password"),
                    array("layui" => "input", "title" => "SMTP服务器", "name" => "LC[email][smtp]", "value" => $plugin['email']['smtp']),
                    array("layui" => "on", "title" => "TLS/SSL", "name" => "LC[email][ssl]", "value" => $plugin['email']['ssl'], "text" => "SSL|TLS"),
                    array("layui" => "input", "title" => "端口", "name" => "LC[email][port]", "value" => $plugin['email']['port'], "tips" => "一般情况下<br>SSL端口为465，TLS端口为25"),

                );
                $alisms = array(
                    array("layui" => "title", "title" => "阿里云短信配置"),
                    array("layui" => "des", "title" => "阿里云短信开通地址&nbsp;&nbsp;<a href='https://www.aliyun.com/product/sms?userCode=kabw9nx2&tag=share_component&share_source=copy_link' target='_blank'>[点击访问] https://www.aliyun.com/product/sms</a>"),
                    array("layui" => "input", "title" => "AccessKey ID", "name" => "LC[alisms][id]", "value" => $plugin['alisms']['id']),
                    array("layui" => "input", "title" => "Access Key Secret", "name" => "LC[alisms][secret]", "value" => $plugin['alisms']['secret']),
                    array("layui" => "input", "title" => "短信签名", "name" => "LC[alisms][sign]", "value" => $plugin['alisms']['sign']),
                    array("layui" => "btn", "title" => "立即保存"),
                );
                require LCMS::template("own/admin_plugin");
                break;
        }
    }
    public function dopayment()
    {
        global $_L;
        load::sys_class('table');
        switch ($_L['form']['action']) {
            case 'payment-list':
                $data = table::data("payment", "lcms = '{$_L['ROOTID']}'", "id DESC");
                table::out($data);
                break;
            case 'payment-edit':
                load::sys_class('pays');
                $payment        = LCMS::form(array("table" => "payment", "do" => "get", "id" => $_L['form']['id']));
                $payment_config = PAYS::payment_config();
                foreach ($payment_config as $key => $val) {
                    $val                       = json_decode($val, true);
                    $payment_list['payment'][] = [
                        "title" => $val['info']['title'],
                        "value" => $val['info']['name'],
                        "tab"   => "payment_{$val['info']['name']}",
                    ];
                    foreach ($val['form'] as $val2) {
                        if (!empty($payment[$key][$val2['value']])) {
                            $val2['value'] = $payment[$key][$val2['value']];
                        } else {
                            $val2['value'] = $val2['layui'] == "radio" ? "0" : "";
                        }
                        $payment_list['form'][$key][] = $val2;
                    }
                }
                $form['base'][] = ["layui" => "input", "title" => "支付名称", "name" => "LC[title]", "value" => $payment['title'], "tips" => "支付名称，仅作分辨使用", "placeholder" => "支付名称，仅作分辨使用", "verify" => "required"];
                $form['base'][] = ["layui" => "radio", "title" => "支付方式", "name" => "LC[payment]", "value" => $payment['payment'], "verify" => "required", "radio" => $payment_list['payment']];
                $form           = $form + $payment_list['form'];
                $form['last']   = [["layui" => "btn", "fluid" => true, "title" => "立即保存"]];
                require LCMS::template("own/payment-edit");
                break;
            case 'payment-save':
                foreach (["wechat" => "mch_id", "wechat_agent" => "sub_mch_id"] as $payment => $mch_id) {
                    if ($_L['form']['LC'][$payment][$mch_id] && $_L['form']['LC'][$payment]['apiclient_cert'] && $_L['form']['LC'][$payment]['apiclient_key']) {
                        $dir  = PATH_CORE_PLUGIN . "payment/{$payment}/cert/";
                        $file = $dir . md5($_L['form']['LC'][$payment][$mch_id]);
                        file_put_contents("{$file}_cert.pem", $_L['form']['LC'][$payment]['apiclient_cert']);
                        file_put_contents("{$file}_key.pem", $_L['form']['LC'][$payment]['apiclient_key']);
                    }
                }
                $_L['form']['LC']['lcms'] = $_L['ROOTID'];
                LCMS::form(array("table" => "payment", "key" => "parameter"));
                if (sql_error()) {
                    ajaxout(0, "保存失败：" . sql_error());
                } else {
                    ajaxout(1, "保存成功", "close");
                };
                break;
            case 'payment-list-del':
                if (table::del("payment")) {
                    ajaxout(1, "删除成功");
                } else {
                    ajaxout(0, "删除失败");
                }
                break;
            default:
                $table = array(
                    "url"     => $_L['url']['own_form'] . "payment&action=payment-list",
                    "cols"    => array(
                        array("checkbox" => "checkbox", "width" => 80),
                        array("title" => "ID", "field" => "id", "width" => 80, "align" => "center"),
                        array("title" => "支付名称", "field" => "title", "width" => 200),
                        array("title" => "支付方式", "field" => "payment", "width" => 200),
                        array("title" => "操作", "field" => "do", "toolbar" => array(
                            array("title" => "编辑", "event" => "iframe", "url" => $_L['url']['own_form'] . "payment&action=payment-edit", "color" => "default"),
                            array("title" => "删除", "event" => "ajax", "url" => $_L['url']['own_form'] . "payment&action=payment-list-del", "color" => "danger", "tips" => "确认删除？"),
                        )),
                    ),
                    "toolbar" => array(
                        array("title" => "添加支付", "event" => "iframe", "url" => $_L['url']['own_form'] . "payment&action=payment-edit", "color" => "default"),
                        array("title" => "批量删除", "event" => "ajax", "url" => $_L['url']['own_form'] . "payment&action=payment-list-del", "color" => "danger", "tips" => "确认删除？"),
                    ),
                );
                require LCMS::template("own/payment-list");
                break;
        }
    }
    public function doclear()
    {
        global $_L;
        switch ($_L['form']['action']) {
            case 'save':
                if ($_L['form']['tpl']) {
                    deldir(PATH_CACHE . "tpl");
                }
                if ($_L['form']['static']) {
                    deldir(PATH_CACHE . "static");
                }
                if ($_L['form']['cfg']) {
                    deldir(PATH_CACHE . "cfg");
                }
                ajaxout(1, "清除成功", "close");
                break;
            default:
                $form = [
                    ["layui"   => "checkbox", "title" => "缓存类型",
                        "checkbox" => [
                            ["title" => "页面缓存", "name" => "tpl", "value" => "1"],
                            ["title" => "CSS/JS缓存", "name" => "static", "value" => "0"],
                            ["title" => "临时配置", "name" => "cfg", "value" => "0"],
                        ],
                    ],
                    ["layui" => "btn", "title" => "立即清除"],
                ];
                require LCMS::template("own/clear");
                break;
        }
    }
    public function docheckredis()
    {
        global $_L;
        if ($_L['form']['checked'] == "1") {
            load::plugin("Redis/rds");
            $redis = new RDS();
            $redis->$do->setex("LCMSREDISTEST", 60, "success");
            if ($redis->$do->get('LCMSREDISTEST') == "success") {
                ajaxout(1);
            } else {
                ajaxout(0, "Redis未成功开启");
            }
        } else {
            ajaxout(1);
        }
    }
}
