<?php
/*
 * @Author: 小小酥很酥
 * @Date: 2020-08-01 18:52:16
 * @LastEditTime: 2020-08-09 16:48:25
 * @Description: 用户管理
 * @Copyright 2020 运城市盘石网络科技有限公司
 */
defined('IN_LCMS') or exit('No permission');
load::sys_class('adminbase');
load::sys_class('table');
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
        if ($_L['LCMSADMIN']['lcms'] != "0") {
            LCMS::X(403, "没有权限，禁止访问");
        }
        $table = array(
            "url"     => $_L['url']['own_form'] . "ajax&action=admin-list",
            "cols"    => [
                ["checkbox" => "checkbox", "width" => 80],
                ["title" => "ID", "field" => "id", "width" => 80, "align" => "center"],
                ["title" => "帐号", "field" => "name", "minWidth" => 90],
                ["title" => "用户名", "field" => "title", "width" => 150],
                ["title" => "邮箱", "field" => "email", "width" => 120],
                ["title" => "手机号", "field" => "mobile", "width" => 120],
                ["title" => "用户权限", "field" => "type", "width" => 120],
                ["title" => "上级用户", "field" => "lcms", "width" => 120],
                ["title" => "最后登录时间", "field" => "logintime", "width" => 180],
                ["title" => "最后登录IP", "field" => "ip", "width" => 150],
                ["title" => "到期时间", "field" => "lasttime", "width" => 180],
                ["title" => "账号状态", "field" => "status", "width" => 100, "align" => "center"],
                ["title" => "操作", "field" => "do", "width" => 110, "align" => "center", "toolbar" => [
                    ["title" => "编辑", "event" => "iframe", "url" => $_L['url']['own_form'] . "iframe&action=admin-edit", "color" => "default"],
                    ["title" => "删除", "event" => "ajax", "url" => $_L['url']['own_form'] . "ajax&action=admin-list-del", "color" => "danger", "tips" => "确认删除？"],
                ]],
            ],
            "toolbar" => [
                ["title" => "添加用户", "event" => "iframe", "url" => $_L['url']['own_form'] . "iframe&action=admin-edit", "color" => "default"],
                ["title" => "批量删除", "event" => "ajax", "url" => $_L['url']['own_form'] . "ajax&action=admin-list-del", "color" => "danger", "tips" => "确认删除？"],
            ],
            "search"  => [
                ["title" => "账号/用户名/邮箱/手机", "name" => "name"],
            ],
        );
        require LCMS::template("own/admin-list");
    }
    public function dolevel()
    {
        global $_L;
        if ($_L['LCMSADMIN']['lcms'] != "0") {
            LCMS::X(403, "没有权限，禁止访问");
        }
        $table = array(
            "url"     => $_L['url']['own_form'] . "ajax&action=admin-level-list",
            "cols"    => array(
                array("title" => "ID", "field" => "id", "width" => 80, "align" => "center"),
                array("title" => "权限名", "field" => "name", "width" => 200, "edit" => "text"),
                array("title" => "添加人", "field" => "uid", "width" => 300),
                array("title" => "操作", "field" => "do", "minWidth" => 120, "toolbar" => array(
                    array("title" => "编辑", "event" => "iframe", "url" => $_L['url']['own_form'] . "iframe&action=admin-level-edit", "color" => "default"),
                    array("title" => "删除", "event" => "ajax", "url" => $_L['url']['own_form'] . "ajax&action=admin-level-list-del", "color" => "danger", "tips" => "确认删除？"),
                )),
            ),
            "toolbar" => array(
                array("title" => "添加权限", "event" => "iframe", "url" => $_L['url']['own_form'] . "iframe&action=admin-level-edit", "color" => "default"),
            ),
        );
        require LCMS::template("own/admin-list");
    }
    public function doajax()
    {
        global $_L;
        $form = $_L['form']['LC'];
        switch ($_L['form']['action']) {
            case 'admin-list':
                $where     = $form['name'] ? " AND (name LIKE '%{$form['name']}%' OR title LIKE '%{$form['name']}%' OR email LIKE '%{$form['name']}%' OR mobile LIKE '%{$form['name']}%')" : "";
                $data      = LCMS::SUPER() ? TABLE::data("admin", "id != 0" . $where, "id ASC") : TABLE::data("admin", "(lcms = '{$_L[LCMSADMIN][id]}' OR id = '{$_L[LCMSADMIN][id]}')" . $where, "id ASC");
                $adminlist = sql_getall(["admin"]);
                $levellist = sql_getall(["admin_level"]);
                foreach ($data['data'] as $key => $val) {
                    $data['data'][$key]['type']   = $data['data'][$key]['type'] == "lcms" ? "超级权限" : $data['data'][$key]['type'];
                    $checked                      = $data['data'][$key]['status'] ? "checked" : "";
                    $data['data'][$key]['status'] = "<input type='checkbox' data-url='{$_L['url']['own_form']}ajax&action=admin-list-status&id={$data['data'][$key]['id']}' lay-skin='switch' lay-text='启用|禁用' {$checked}>";
                    foreach ($adminlist as $info) {
                        if ($info['id'] == $val['lcms']) {
                            $data['data'][$key]['lcms'] = $info['title'] . " - [" . $info['name'] . "]";
                        } elseif ($val['lcms'] == "0") {
                            $data['data'][$key]['lcms'] = "超级管理员";
                        }
                        continue;
                    }
                    foreach ($levellist as $info) {
                        if ($info['id'] == $val['type']) {
                            $data['data'][$key]['type'] = $info['name'] . " - [ID" . $info['id'] . "]";
                        } elseif ($val['type'] == "lcms") {
                        }
                        continue;
                    }
                }
                TABLE::out($data);
                break;
            case 'admin-list-save':
                sql_update(["admin", [
                    $form['name'] => $form['value'],
                ], "id = '{$form[id]}'"]);
                if (sql_error()) {
                    ajaxout(0, "保存失败");
                } else {
                    ajaxout(1, "保存成功");
                }
                break;
            case 'admin-list-del':
                if ($form['id'] == $_L['LCMSADMIN']['id']) {
                    ajaxout(0, "禁止删除");
                    exit;
                } elseif ($form[0]['id']) {
                    foreach ($form as $key => $val) {
                        if ($val['id'] == $_L['LCMSADMIN']['id']) {
                            ajaxout(0, "禁止删除");
                            exit;
                        }
                    }
                }
                if (TABLE::del("admin")) {
                    ajaxout(1, "删除成功");
                } else {
                    ajaxout(0, "删除失败");
                }
                break;
            case 'admin-list-status':
                if ($_L['form']['id'] == $_L['LCMSADMIN']['id']) {
                    ajaxout(0, "禁止修改");
                    exit;
                }
                sql_update(["admin", [
                    "status" => $form['value'] ? "1" : "0",
                ], "id = '{$_L['form']['id']}'"]);
                if (sql_error()) {
                    ajaxout(0, "保存失败");
                } else {
                    ajaxout(1, "保存成功");
                }
                break;
            case 'admin-level-list':
                if (LCMS::SUPER()) {
                    $data = table::data("admin_level", "", "id ASC");
                } else {
                    $data = table::data("admin_level", "uid = '{$_L[LCMSADMIN][id]}'", "id ASC");
                }
                $adminlist = sql_getall(["admin"]);
                foreach ($data['data'] as $key => $val) {
                    foreach ($adminlist as $info) {
                        if ($info['id'] == $val['uid']) {
                            $data['data'][$key]['uid'] = $info['title'] . " - [" . $info['name'] . "]";
                            continue;
                        }
                    }
                }
                table::out($data);
                break;
            case 'admin-level-list-save':
                sql_update(["admin_level", [
                    $form['name'] => $form['value'],
                ], "id = '{$form[id]}'"]);
                if (sql_error()) {
                    ajaxout(0, "保存失败");
                } else {
                    ajaxout(1, "保存成功");
                }
                break;
            case 'admin-level-list-del':
                $adminlist = sql_getall(["admin", "type = '{$form[id]}'"]);
                if ($adminlist) {
                    ajaxout(0, "有用户使用此权限");
                } else {
                    if (table::del("admin_level")) {
                        ajaxout(1, "删除成功");
                    } else {
                        ajaxout(0, "删除失败");
                    }
                }
                break;
            case 'admin-level':
                $admininfo = $_L['LCMSADMIN'];
                $adminall  = LCMS::SUPER() ? sql_getall(["admin", "lcms = '0'", "id ASC"]) : sql_getall(["admin", "id = '{$admininfo['id']}'", "id ASC"]);
                foreach ($adminall as $key => $val) {
                    $val['title'] .= " - [{$val['name']}]";
                    $val['title'] .= $val['lasttime'] > "0000-00-00 00:00:00" && $val['lasttime'] < datenow() ? " - 已到期" : "";
                    $adminarr = [
                        "value" => $val['type'] == "lcms" ? "0" : $val['id'],
                        "title" => $val['title'],
                    ];
                    $children = sql_getall(["admin_level", "uid = '{$val['id']}'", "id ASC"]);
                    foreach ($children as $key => $val) {
                        $adminarr['children'][] = [
                            "value" => $val['id'],
                            "title" => $val['name'] . " - [ID" . $val['id'] . "]",
                        ];
                    }
                    $adminlist[] = $adminarr;
                }
                echo json_encode($adminlist);
                break;
        }
    }
    public function doiframe()
    {
        global $_L;
        if ($_L['LCMSADMIN']['lcms'] != "0") {
            LCMS::X(403, "没有权限，禁止访问");
        }
        switch ($_L['form']['action']) {
            case 'admin-edit':
                $admin = LCMS::form([
                    "do"    => "get",
                    "table" => "admin",
                    "id"    => $_L['form']['id'],
                ]);
                $form['base'] = [
                    ["layui"      => "input", "title" => "账号",
                        "name"        => "LC[name]",
                        "value"       => $admin['name'],
                        "placeholder" => "帐号用来登录，不能重复",
                        "verify"      => "required|name",
                        "disabled"    => $admin['name'] && $admin['type'] != "lcms" ? "1" : "",
                    ],
                    ["layui"      => "input", "title" => "用户名",
                        "name"        => "LC[title]",
                        "value"       => $admin['title'],
                        "placeholder" => "用户名字只做显示",
                        "verify"      => "required",
                    ],
                    ["layui"      => "input", "title" => "密码",
                        "name"        => "LC[pass]",
                        "value"       => $admin['pass'],
                        "placeholder" => "请输入用户密码",
                        "verify"      => "required",
                        "type"        => "password",
                    ],
                    ["layui"      => "input", "title" => "重复密码",
                        "name"        => "repass",
                        "value"       => $admin['pass'],
                        "placeholder" => "请再次输入用户密码",
                        "verify"      => "required|pass",
                        "type"        => "password",
                    ],
                    ["layui" => "input", "title" => "邮箱",
                        "name"   => "LC[email]",
                        "value"  => $admin['email'],
                        "type"   => "email",
                    ],
                    ["layui" => "input", "title" => "手机号",
                        "name"   => "LC[mobile]",
                        "value"  => $admin['mobile'],
                        "type"   => "phone",
                    ],
                    ["layui" => "radio", "title" => "账号状态",
                        "name"   => "LC[status]",
                        "value"  => $admin['status'] != null ? $admin['status'] : 0,
                        "radio"  => [
                            ["title" => "启用", "value" => 1],
                            ["title" => "禁用", "value" => 0],
                        ],
                    ],
                ];
                $form['level'] = [
                    ["layui" => "title", "title" => "权限设置"],
                    ["layui" => "selectN", "title" => "用户权限",
                        "name"   => "admin_level",
                        "value"  => "{$admin['lcms']}/{$admin['type']}",
                        "tips"   => "先新建管理员权限再选择",
                        "verify" => "required",
                        "url"    => "{$_L['url']['own_form']}ajax&action=admin-level",
                    ],
                    ["layui" => "date", "title" => "到期时间",
                        "name"   => "LC[lasttime]",
                        "value"  => $admin['lasttime'],
                        "tips"   => "到期不能登录",
                        "min"    => datenow(),
                        "max"    => LCMS::SUPER() ? "" : ($_L['LCMSADMIN']['lasttime'] ? $_L['LCMSADMIN']['lasttime'] : ""),
                    ],
                ];
                require LCMS::template("own/iframe/admin-edit");
                break;
            case 'admin-check-name':
                $admininfo = sql_get(["admin", "name = ':name' OR email = ':name' OR mobile = ':name'", "id DESC", [
                    ":name" => $_L['form']['name'],
                ]]);
                if ($admininfo) {
                    ajaxout(0, "账号已存在");
                };
                break;
            case 'admin-save':
                if ($_L['form']['LC']['id'] == $_L['LCMSADMIN']['id']) {
                    unset($_L['form']['LC']['status']);
                }
                if ($_L['form']['LC']['oldpass'] != $_L['form']['LC']['pass']) {
                    $_L['form']['LC']['pass'] = md5($_L['form']['LC']['pass']);
                }
                if ($_L['form']['LC']['id']) {
                    if (!LCMS::SUPER()) {
                        unset($_L['form']['LC']['name']);
                    }
                    $where = " AND id NOT IN({$_L['form']['LC']['id']})";
                }
                if ($_L['form']['LC']['name']) {
                    $admininfo = sql_get(["admin", "(name = ':name' OR email = ':name' OR mobile = ':name'){$where}", "id DESC", [
                        ":name" => $_L['form']['LC']['name'],
                    ]]);
                    if ($admininfo) {
                        ajaxout(0, "账号已存在");
                    };
                }
                if ($_L['form']['LC']['email']) {
                    $admininfo = sql_get(["admin", "(name = ':email' OR email = ':email' OR mobile = ':email'){$where}", "id DESC", [
                        ":email" => $_L['form']['LC']['email'],
                    ]]);
                    if ($admininfo) {
                        ajaxout(0, "邮箱地址已存在");
                    };
                }
                if ($_L['form']['LC']['mobile']) {
                    $admininfo = sql_get(["admin", "(name = ':mobile' OR email = ':mobile' OR mobile = ':mobile'){$where}", "id DESC", [
                        ":mobile" => $_L['form']['LC']['mobile'],
                    ]]);
                    if ($admininfo) {
                        ajaxout(0, "手机号已存在");
                    };
                }
                unset($_L['form']['LC']['oldpass']);
                if ($_L['form']['admin_level']) {
                    $level = explode("/", $_L['form']['admin_level']);
                    if (!$level[1]) {
                        ajaxout(0, "请设置用户权限");
                    } else {
                        $_L['form']['LC']['lcms'] = $level[0];
                        $_L['form']['LC']['type'] = $level[1];
                    }
                }
                LCMS::form(["table" => "admin"]);
                if (sql_error()) {
                    ajaxout(0, "保存失败", "", sql_error());
                } else {
                    ajaxout(1, "保存成功", "close");
                }
                break;
            case 'admin-level-edit':
                $level = $_L['form']['id'] ? LCMS::form(array(
                    "do"    => "get",
                    "table" => "admin_level",
                    "id"    => $_L['form']['id'],
                )) : array();
                ksort($level['sys']);
                ksort($level['open']);
                $appall = level::appall();
                foreach ($appall as $type => $val) {
                    foreach ($val as $name => $info) {
                        if (!empty($info['class'])) {
                            $level[$type][$name]['title'] = $info['info']['title'];
                            foreach ($info['class'] as $class => $val) {
                                if (!empty($val['level'])) {
                                    $level[$type][$name]['class'][$class]['title'] = $val['title'];
                                    foreach ($val['level'] as $key => $val) {
                                        if ($info['power'][$class][$key] != "no") {
                                            $level[$type][$name]['class'][$class]['select'][] = array(
                                                "value" => $key,
                                                "title" => $val['title'],
                                            );
                                        } else {
                                            $hide[$type][$name][$class][$key] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                foreach ($level as $type => $list) {
                    foreach ($list as $name => $li) {
                        foreach ($li['class'] as $class => $val) {
                            if (empty($val['select'])) {
                                unset($level[$type][$name]['class'][$class]);
                                if (empty($level[$type][$name]['class'])) {
                                    unset($level[$type][$name]);
                                }
                            }
                        }
                    }
                }
                $form = [
                    ["layui"      => "input", "title" => "权限名",
                        "name"        => "LC[name]",
                        "value"       => $level['name'],
                        "placehplder" => "请输入权限名",
                        "verify"      => "required",
                    ],
                    ["layui"   => "select", "title" => "所属用户",
                        "name"     => "LC[uid]",
                        "value"    => $level['uid'] ? $level['uid'] : $_L['LCMSADMIN']['id'],
                        "verify"   => "required",
                        "option"   => $this->get_adminall(true),
                        "disabled" => LCMS::SUPER() ? "" : true,
                    ],
                ];
                $hide = base64_encode(json_encode($hide));
                require LCMS::template("own/iframe/admin-level-edit");
                break;
            case 'admin-level-save':
                if ($_L['form']['level']) {
                    $level = json_decode(base64_decode($_L['form']['level']), true);
                    if (is_array($level)) {
                        $_L['form']['LC'] = array_merge_recursive($level, $_L['form']['LC']);
                    }
                }
                LCMS::form([
                    "table" => "admin_level",
                    "unset" => true,
                ]);
                if (sql_error()) {
                    ajaxout(0, sql_error());
                } else {
                    ajaxout(1, "保存成功", "close");
                }
                break;
        }
    }
    public function doprofile()
    {
        global $_L;
        switch ($_L['form']['action']) {
            case 'admin-edit':
                $admin = LCMS::form([
                    "do"    => "get",
                    "table" => "admin",
                    "id"    => $_L['LCMSADMIN']['id'],
                ]);
                $form['base'] = [
                    ["layui"      => "input", "title" => "账号",
                        "name"        => "LC[name]",
                        "value"       => $admin['name'],
                        "placeholder" => "帐号用来登录，不能重复",
                        "verify"      => "required|name",
                        "disabled"    => $admin['name'] && $admin['type'] != "lcms" ? "1" : "",
                    ],
                    ["layui"      => "input", "title" => "用户名",
                        "name"        => "LC[title]",
                        "value"       => $admin['title'],
                        "placeholder" => "用户名字只做显示",
                        "verify"      => "required",
                    ],
                    ["layui"      => "input", "title" => "密码",
                        "name"        => "LC[pass]",
                        "value"       => $admin['pass'],
                        "placeholder" => "请输入用户密码",
                        "verify"      => "required",
                        "type"        => "password",
                    ],
                    ["layui"      => "input", "title" => "重复密码",
                        "name"        => "repass",
                        "value"       => $admin['pass'],
                        "placeholder" => "请再次输入用户密码",
                        "verify"      => "required|pass",
                        "type"        => "password",
                    ],
                    ["layui"   => "input", "title" => "邮箱",
                        "name"     => "email",
                        "value"    => $admin['email'],
                        "type"     => "email",
                        "disabled" => $admin['type'] != "lcms" ? "1" : "",
                    ],
                    ["layui"   => "input", "title" => "手机号",
                        "name"     => "mobile",
                        "value"    => $admin['mobile'],
                        "type"     => "phone",
                        "disabled" => $admin['type'] != "lcms" ? "1" : "",
                    ],
                ];
                require LCMS::template("own/iframe/admin-edit");
                break;
            case 'admin-save':
                if ($_L['form']['LC']['oldpass'] != $_L['form']['LC']['pass']) {
                    $_L['form']['LC']['pass'] = md5($_L['form']['LC']['pass']);
                }
                if ($_L['form']['LC']['id'] && !LCMS::SUPER()) {
                    unset($_L['form']['LC']['name']);
                }
                unset($_L['form']['LC']['oldpass']);
                unset($_L['form']['LC']['email']);
                unset($_L['form']['LC']['mobile']);
                $_L['LCMSADMIN']['title'] = $_L['form']['LC']['title'];
                SESSION::set("LCMSADMIN", $_L['LCMSADMIN']);
                LCMS::form(["table" => "admin"]);
                if (sql_error()) {
                    ajaxout(0, "保存失败", "", sql_error());
                } else {
                    ajaxout(1, "保存成功", "close");
                }
                break;
        }
    }
    public function doconfig()
    {
        global $_L;
        if ($_L['LCMSADMIN']['lcms'] != "0") {
            LCMS::X(403, "没有权限，禁止访问");
        }
        switch ($_L['form']['action']) {
            case 'save':
                LCMS::config([
                    "do"   => "save",
                    "type" => "sys",
                    "cate" => "admin",
                ]);
                ajaxout(1, "保存成功");
                break;
            default:
                $config = LCMS::config([
                    "type" => "sys",
                    "cate" => "admin",
                ]);
                $form = [
                    ["layui"   => "input", "title" => "登陆地址",
                        "name"     => "login_url",
                        "value"    => "{$_L['url']['admin']}index.php?n=login&rootid={$_L['ROOTID']}",
                        "disabled" => true,
                    ],
                    ["layui"   => "input", "title" => "注册地址",
                        "name"     => "login_url",
                        "value"    => "{$_L['url']['admin']}index.php?n=login&c=reg&rootid={$_L['ROOTID']}",
                        "disabled" => true,
                    ],
                    ["layui" => "radio", "title" => "用户注册",
                        "name"   => "LC[reg][on]",
                        "value"  => $config['reg']['on'] ? $config['reg']['on'] : "0",
                        "radio"  => [
                            ["title" => "关闭", "value" => "0", "tab" => "tab0"],
                            ["title" => "账号验证", "value" => "justuser", "tab" => "tab_justuser"],
                            ["title" => "邮箱验证", "value" => "email", "tab" => "tab_email"],
                            ["title" => "手机号验证", "value" => "mobile", "tab" => "tab_mobile"],
                        ],
                    ],
                    ["layui" => "radio", "title" => "注册审核",
                        "name"   => "LC[reg][status]",
                        "value"  => $config['reg']['status'] ? $config['reg']['status'] : "0",
                        "radio"  => [
                            ["title" => "手动审核", "value" => "0"],
                            ["title" => "自动审核", "value" => "1"],
                        ],
                    ],
                    ["layui" => "input", "title" => "短信ID",
                        "name"   => "LC[reg][sms_tplcode]",
                        "value"  => $config['reg']['sms_tplcode'],
                        "cname"  => "hidden tab_mobile",
                        "tips"   => "请先到全局设置中配置短信插件",
                    ],
                    ["layui" => "select", "title" => "默认上级",
                        "name"   => "LC[reg][lcms]",
                        "value"  => $config['reg']['lcms'],
                        "verify" => "required",
                        "option" => $this->get_adminall(),
                    ],
                    ["layui" => "select", "title" => "默认权限",
                        "name"   => "LC[reg][level]",
                        "value"  => $config['reg']['level'],
                        "verify" => "required",
                        "option" => $this->get_levelall(),
                    ],
                    ["layui"   => "checkbox", "title" => "注册字段",
                        "checkbox" => [
                            ["title" => "用户名",
                                "name"   => "LC[reg][input_title]",
                                "value"  => $config['reg']['input_title']],
                        ],
                    ],
                    ["layui" => "btn", "title" => "立即保存"],
                ];
                require LCMS::template("own/admin-config");
                break;
        }
    }
    private function get_adminall($type = false)
    {
        global $_L;
        if (LCMS::SUPER()) {
            $list[] = [
                "value" => $type ? $_L['LCMSADMIN']['id'] : "0",
                "title" => "{$_L['LCMSADMIN']['title']} - [{$_L['LCMSADMIN']['name']}]",
            ];
            $admin = sql_getall(["admin", "lcms = '0' AND type != 'lcms'", "id ASC"]);
        } else {
            $admin = sql_getall(["admin", "id = '{$_L['LCMSADMIN']['id']}'", "id DESC"]);
        }
        foreach ($admin as $key => $val) {
            $val['title'] .= " - [{$val['name']}]";
            $val['title'] .= $val['lasttime'] > "0000-00-00 00:00:00" && $val['lasttime'] < datenow() ? " - 已到期" : "";
            $list[] = [
                "value" => $val['id'],
                "title" => $val['title'],
            ];
        }
        return $list;
    }private function get_levelall()
    {
        global $_L;
        if (LCMS::SUPER()) {
            $level = sql_getall(["admin_level", "", "id DESC"]);
        } else {
            $level = sql_getall(["admin_level", "uid = '{$_L['LCMSADMIN']['id']}'", "id DESC"]);
        }
        foreach ($level as $key => $val) {
            $list[] = [
                "value" => $val['id'],
                "title" => $val['name'] . " - [ID:{$val['id']}]",
            ];
        }
        return $list;
    }
}
