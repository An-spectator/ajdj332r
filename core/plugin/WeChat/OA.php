<?php
/*
 * @Author: 小小酥很酥
 * @Date: 2020-10-10 14:20:59
 * @LastEditTime: 2021-11-01 13:25:25
 * @Description:微信公众号接口类
 * @Copyright 2020 运城市盘石网络科技有限公司
 */
class OA
{
    public $cfg;
    public function __construct($config = [])
    {
        global $_L, $LF, $SID;
        $LF = $_L['form'];
        if (!$config) {
            $config    = LCMS::config(["name" => "wechat"]);
            $this->cfg = [
                "appid"     => $config['appid'],
                "appsecret" => $config['appsecret'],
                "thirdapi"  => $config['mode'] == "other" ? $config['access_api'] : "",
            ];
        } else {
            $this->cfg = [
                "appid"     => $config['appid'],
                "appsecret" => $config['appsecret'],
                "thirdapi"  => $config['thirdapi'],
            ];
        };
        $SID = "WX" . strtoupper(substr(md5($this->cfg['appid']), 8, 16)) . "-RID{$_L['ROOTID']}-";
        $this->cache();
    }
    /**
     * @description: 数据缓存读取与保存
     * @param string $type
     * @return {*}
     */
    public function cache($type = "get")
    {
        global $_L, $LF, $SID;
        if ($this->cfg['appid'] && $this->cfg['appsecret']) {
            $cname = md5($this->cfg['appid'] . $this->cfg['appsecret']);
        } else {
            return false;
        }
        switch ($type) {
            case 'save':
                LCMS::cache($cname, $this->cfg);
                break;
            case 'clear':
                LCMS::cache($cname, "clear");
                break;
            default:
                $arr = LCMS::cache($cname);
                if (is_array($arr)) {
                    $this->cfg = array_merge($arr, $this->cfg);
                }
                break;
        }
    }
    /**
     * @description: 获取全局access_token
     * @param {*}
     * @return {*}
     */
    public function access_token()
    {
        global $_L, $LF, $SID;
        $this->cache();
        if (!$this->cfg['access_token']['token'] || $this->cfg['access_token']['expires'] < time()) {
            if ($this->cfg['thirdapi']) {
                // 如果启用第三方接口
                $token = json_decode(HTTP::get($this->cfg['thirdapi'] . "accesstoken"), true);
                if ($token['code'] == "1" && $token['data']['access_token'] && $token['data']['expires_in']) {
                    $this->cfg['access_token'] = [
                        "token"   => $token['data']['access_token'],
                        "expires" => $token['data']['expires_in'],
                    ];
                    $this->cache("save");
                } else {
                    return $token;
                }
            } else {
                // 系统自处理
                $query = http_build_query([
                    "appid"      => $this->cfg['appid'],
                    "secret"     => $this->cfg['appsecret'],
                    "grant_type" => "client_credential",
                ]);
                $token = json_decode(HTTP::get("https://api.weixin.qq.com/cgi-bin/token?{$query}"), true);
                if (!$token['access_token']) {
                    return $token;
                } else {
                    $this->cfg['access_token'] = [
                        "token"   => $token['access_token'],
                        "expires" => time() + 3600,
                    ];
                    $this->cache("save");
                }
            }
        }
        return $this->cfg['access_token']['token'];
    }
    /**
     * @description: 使用code获取用户数据
     * @param string $code
     * @return array
     */
    private function getOpenidFromMp($code)
    {
        global $_L, $LF, $SID;
        $query = http_build_query([
            "appid"      => $this->cfg['appid'],
            "secret"     => $this->cfg['appsecret'],
            "code"       => $code,
            "grant_type" => "authorization_code",
        ]);
        $result = json_decode(HTTP::get("https://api.weixin.qq.com/sns/oauth2/access_token?{$query}"), true);
        return $result ?: [];
    }
    /**
     * @description: 微信登陆获取openid
     * @param bool $type
     * @return array
     */
    public function openid($type = false)
    {
        global $_L, $LF, $SID;
        $this->cache();
        $scope  = $type ? "snsapi_userinfo" : "snsapi_base";
        $openid = SESSION::get($SID . $scope);
        if ($openid['openid'] && $scope == "snsapi_base") {
            return $openid;
        } elseif ($openid['openid'] && $scope == "snsapi_userinfo" && $openid['expires_time'] > time()) {
            return $openid;
        } else {
            if ($LF['wechatoauthopenid']) {
                // 设置数据Session
                $userinfo = $this->user([
                    "openid" => $LF['wechatoauthopenid'],
                ]);
                if ($userinfo['openid']) {
                    SESSION::set("{$SID}snsapi_base", [
                        "openid" => $userinfo['openid'],
                    ]);
                    okinfo(url_clear($_L['url']['now'], "wechatoauthopenid"));
                }
            } else {
                if ($this->cfg['thirdapi']) {
                    // 如果启用第三方接口，跳转到第三方接口
                    $goback = urlencode($_L['url']['now']);
                    okinfo($this->cfg['thirdapi'] . "oauth&scope={$scope}&goback={$goback}&key=wechatoauthopenid");
                    exit();
                } else {
                    //使用系统API域名进行授权
                    if (stripos($_L['config']['web']['domain_api'], HTTP_HOST) === false) {
                        $goback = urlencode($_L['url']['now']);
                        okinfo("{$_L['config']['web']['domain_api']}app/index.php?rootid={$_L['ROOTID']}&n=wechat&c=index&a=oauth&scope={$scope}&goback={$goback}");
                        exit();
                    }
                    // 用户授权登陆，获取code
                    $code = $LF['code'];
                    if (!isset($code)) {
                        $query = http_build_query([
                            "appid"         => $this->cfg['appid'],
                            "redirect_uri"  => $_L['url']['now'],
                            "response_type" => "code",
                            "scope"         => $scope,
                        ]);
                        $this->header_nocache("https://open.weixin.qq.com/connect/oauth2/authorize?{$query}#wechat_redirect");
                        exit();
                    } else {
                        // 使用code获取用户数据
                        $openid = $this->getOpenidFromMp($code);
                        if ($openid['openid']) {
                            $this->user([
                                "do"     => "save",
                                "openid" => $openid['openid'],
                                "wechat" => [
                                    "openid" => $openid['openid'],
                                ],
                            ]);
                            $openid['expires_time'] = time() + 3600;
                            SESSION::set($SID . $scope, $openid);
                            okinfo(url_clear($_L['url']['now'], "code|state"));
                        }
                    }
                }
            }
        }
    }
    /**
     * @description: 获取微信用户的详细信息
     * @param array $para
     * @return array
     */
    public function userinfo($para = [])
    {
        global $_L, $LF, $SID;
        if ($para['type'] == "subscribe") {
            // 用户关注后获取用户信息
            $this->access_token();
            $query = http_build_query([
                "access_token" => $this->cfg['access_token']['token'],
                "openid"       => $para['openid'],
                "lang"         => "zh_CN",
            ]);
            $userinfo = json_decode(HTTP::get("https://api.weixin.qq.com/cgi-bin/user/info?{$query}"), true);
            if ($userinfo && !$userinfo['errcode']) {
                $userinfo = $this->user([
                    "do"     => "save",
                    "openid" => $userinfo['openid'],
                    "wechat" => $userinfo,
                ]);
            }
        } else {
            $userinfo = SESSION::get("{$SID}userinfo");
            if (!$userinfo['openid'] || $userinfo['errcode']) {
                if ($LF['wechatoauthopenid']) {
                    // 跳转获取用户信息
                    if ($this->cfg['thirdapi']) {
                        // 如果启用第三方接口
                        $user = json_decode(HTTP::get($this->cfg['thirdapi'] . "userinfo&openid={$LF['wechatoauthopenid']}"), true);
                        if ($user['code'] == 1 && $user['data']['openid']) {
                            $userinfo = $this->user([
                                "do"     => "save",
                                "openid" => $user['data']['openid'],
                                "wechat" => $user['data'],
                            ]);
                        } else {
                            return $user;
                        }
                    } else {
                        $userinfo = $this->user([
                            "openid" => $LF['wechatoauthopenid'],
                        ]);
                    }
                    if ($userinfo['openid']) {
                        SESSION::set("{$SID}userinfo", $userinfo);
                        okinfo(url_clear($_L['url']['now'], "wechatoauthopenid"));
                        exit();
                    }
                } else {
                    $openid = $this->openid(true);
                    $query  = http_build_query(array(
                        "access_token" => $openid['access_token'],
                        "openid"       => $openid['openid'],
                        "lang"         => "zh_CN",
                    ));
                    $userinfo = json_decode(HTTP::get("https://api.weixin.qq.com/sns/userinfo?{$query}"), true);
                    if ($userinfo && !$userinfo['errcode']) {
                        $userinfo = $this->user([
                            "do"     => "save",
                            "openid" => $userinfo['openid'],
                            "wechat" => $userinfo,
                        ]);
                        SESSION::set("{$SID}userinfo", $userinfo);
                    }
                }
            }
        }
        return $userinfo ?: [];
    }
    /**
     * @description: 用户数据的保存与读取
     * @param array $para
     * @return {*}
     */
    public function user($para = [])
    {
        global $_L, $LF, $SID;
        $userinfo = sql_get([
            "open_wechat_user",
            "openid = :openid AND lcms = :lcms",
            "", [
                ":openid" => $para['openid'],
                ":lcms"   => $_L['ROOTID'],
            ],
        ]);
        if ($para['do'] == "save") {
            $form = [
                "openid"          => $para['wechat']['openid'],
                "subscribe"       => $para['wechat']['subscribe'],
                "nickname"        => $para['wechat']['nickname'],
                "language"        => $para['wechat']['language'],
                "headimgurl"      => $para['wechat']['headimgurl'],
                "subscribe_time"  => $para['wechat']['subscribe_time'],
                "unionid"         => $para['wechat']['unionid'],
                "remark"          => $para['wechat']['remark'],
                "groupid"         => $para['wechat']['groupid'],
                "subscribe_scene" => $para['wechat']['subscribe_scene'],
                "qr_scene"        => $para['wechat']['qr_scene'],
                "qr_scene_str"    => $para['wechat']['qr_scene_str'],
                "location"        => $para['wechat']['location'],
                "activetime"      => $para['wechat']['activetime'],
                "parameter"       => $para['wechat']['parameter'],
            ];
            foreach ($form as $key => $val) {
                if ($val === false || $val === "" || $val === null) {
                    unset($form[$key]);
                }
            }
            if ($userinfo && $form) {
                sql_update([
                    "open_wechat_user",
                    $form,
                    "openid = :openid AND lcms = :lcms",
                    [
                        ":openid" => $para['openid'],
                        ":lcms"   => $_L['ROOTID'],
                    ],
                ]);
            } elseif ($form) {
                $form['lcms'] = $_L['ROOTID'];
                if ($form['openid']) {
                    $userinfo['id'] = sql_insert([
                        "open_wechat_user",
                        $form,
                    ]);

                }
            }
            $userinfo = $userinfo && $form ? array_merge($userinfo, $form) : false;
        }
        if ($para['openid']) {
            $userinfo = $userinfo ? $userinfo : sql_get([
                "open_wechat_user",
                "openid = :openid AND lcms = :lcms",
                "", [
                    ":openid" => $para['openid'],
                    ":lcms"   => $_L['ROOTID'],
                ],
            ]);
            return $userinfo;
        }
    }
    /**
     * @description: 获取全局jsapi_ticket
     * @param {*}
     * @return {*}
     */
    public function jsapi_ticket()
    {
        global $_L, $LF, $SID;
        $this->cache();
        if (!$this->cfg['jsapi_ticket']['ticket'] || $this->cfg['jsapi_ticket']['expires'] < time()) {
            if ($this->cfg['thirdapi']) {
                // 如果启用第三方接口
                $token = json_decode(HTTP::get($this->cfg['thirdapi'] . "jsapiticket"), true);
                if ($token['code'] == "1" && $token['data']['ticket'] && $token['data']['expires_in']) {
                    $this->cfg['jsapi_ticket'] = [
                        "ticket"  => $token['data']['ticket'],
                        "expires" => $token['data']['expires_in'],
                    ];
                    $this->cache("save");
                } else {
                    return $token;
                }
            } else {
                $this->access_token();
                $query = http_build_query([
                    "access_token" => $this->cfg['access_token']['token'],
                    "type"         => "jsapi",
                ]);
                $ticket = json_decode(HTTP::get("https://api.weixin.qq.com/cgi-bin/ticket/getticket?{$query}"), true);
                if (!$ticket['ticket']) {
                    return $ticket;
                } else {
                    $this->cfg['jsapi_ticket'] = [
                        "ticket"  => $ticket['ticket'],
                        "expires" => time() + 7000,
                    ];
                    $this->cache("save");
                }
            }
        }
        return $this->cfg['jsapi_ticket']['ticket'];
    }
    /**
     * @description: 获取前台JSSDK签名
     * @param string $url
     * @return array
     */
    public function signpackage($url = "")
    {
        global $_L, $LF, $SID;
        $this->jsapi_ticket();
        $url         = $url ? $url : $_L['url']['now'];
        $nonceStr    = randstr(16);
        $timestamp   = time();
        $query       = implode("&", ["jsapi_ticket=" . $this->cfg['jsapi_ticket']['ticket'], "noncestr={$nonceStr}", "timestamp={$timestamp}", "url={$url}"]);
        $signPackage = [
            "appId"     => $this->cfg['appid'],
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => sha1($query),
            "rawString" => $query,
        ];
        return $signPackage;
    }
    /**
     * @description: 发送模板消息
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
     * @param array $para
     * @return array
     */
    public function send_tpl($para = [])
    {
        global $_L, $LF, $SID;
        $this->access_token();
        $result = HTTP::post("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$this->cfg['access_token']['token']}", json_encode_ex($para));
        $result = json_decode($result, true);
        return $result ?: [];
    }
    /**
     * @description: 发送客服消息
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     * @param array $para
     * @return array
     */
    public function send_custom($para = [])
    {
        global $_L, $LF, $SID;
        $this->access_token();
        $result = HTTP::post("https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$this->cfg['access_token']['token']}", json_encode_ex($para));
        $result = json_decode($result, true);
        return $result ?: [];
    }
    /**
     * @description: 一次性订阅消息
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1500374289_66bvB
     * @param array $para
     * @return {*}
     */
    public function send_once($para = [])
    {
        global $_L, $LF, $SID;
        $this->access_token();
        $reserved = SESSION::get("{$SID}reserved");
        if (!$LF['reserved']) {
            $reserved = randstr(32);
            SESSION::set("{$SID}reserved", $reserved);
            $query = http_build_query([
                "action"       => "get_confirm",
                "appid"        => $this->cfg['appid'],
                "scene"        => 1,
                "template_id"  => $para['template_id'],
                "redirect_url" => $para['redirect_url'],
                "reserved"     => $reserved,
            ]);
            okinfo("https://mp.weixin.qq.com/mp/subscribemsg?{$query}#wechat_redirect");
        } elseif ($LF['reserved'] == $reserved) {
            $result = HTTP::post("https://api.weixin.qq.com/cgi-bin/message/template/subscribe?access_token={$this->cfg['access_token']['token']}", json_encode_ex([
                "touser"      => $para['touser'],
                "template_id" => $para['template_id'],
                "miniprogram" => $para['miniprogram'],
                "scene"       => $LF['scene'],
                "title"       => $para['title'],
                "data"        => $para['data'],
            ]));
            return $result ?: [];
        }
    }
    /**
     * @description: 添加模板消息
     * @param string $tpl
     * @return array
     */
    public function add_tpl($tpl)
    {
        global $_L, $LF, $SID;
        $this->access_token();
        $result = HTTP::post("https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token={$this->cfg['access_token']['token']}", json_encode_ex([
            "template_id_short" => $tpl,
        ]));
        $result = json_decode($result, true);
        return $result;
    }
    /**
     * @description: 删除模板消息
     * @param string $tplid
     * @return array
     */
    public function del_tpl($tplid)
    {
        global $_L, $LF, $SID;
        $this->access_token();
        $result = HTTP::post("https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token={$this->cfg['access_token']['token']}", json_encode_ex([
            "template_id" => $tplid,
        ]));
        $result = json_decode($result, true);
        return $result ?: [];
    }
    /**
     * @description: 设置、获取公众号菜单
     * @param array $para
     * @return array
     */
    public function menu($para = [])
    {
        global $_L, $LF, $SID;
        $this->access_token();
        if (is_array($para)) {
            $result = HTTP::post("https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$this->cfg['access_token']['token']}", json_encode_ex($para));
        } elseif ($para == "get") {
            $result = HTTP::get("https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$this->cfg['access_token']['token']}");
        }
        $result = json_decode($result, true);
        return $result ?: [];
    }
    /**
     * @description: 素材上传
     * @param array $para
     * @return array
     */
    public function material($para = [])
    {
        global $_L, $LF, $SID;
        $this->access_token();
        $file     = path_absolute($para['file']);
        $fileinfo = pathinfo($file);
        $size     = filesize($file);
        $mime     = [
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'mp3'  => 'audio/mp3',
            'wma'  => 'audio/x-ms-wma',
        ];
        if ($mime) {
            clearstatcache();
            $media = new \CURLFile($file, $mime[$fileinfo['extension']], $fileinfo['basename']);
            if ($para['temp']) {
                //临时素材
                $result = HTTP::post("https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$this->cfg['access_token']['token']}&type={$para['type']}", [
                    "media" => $media,
                ]);
            } else {
                //永久素材
                $result = HTTP::post("https://api.weixin.qq.com/cgi-bin/material/add_material?access_token={$this->cfg['access_token']['token']}&type={$para['type']}", [
                    "media" => $media,
                ]);
            }
            return json_decode($result, true);
        } else {
            return ["errcode" => 403, "errmsg" => "不支持的文件格式"];
        }
    }
    /**
     * @description: 关键词操作
     * @param array $para
     * @return bool
     */
    public function reply($para = [])
    {
        global $_L, $LF, $SID;
        switch ($para['do']) {
            case 'del':
                if ($para['name']) {
                    $words = sql_get([
                        "open_wechat_reply_words",
                        "name = :name AND app = :app AND lcms = :lcms",
                        "", [
                            ":name" => $para['name'],
                            ":app"  => L_NAME,
                            ":lcms" => $_L['ROOTID'],
                        ],
                    ]);
                    foreach ([
                        "reply", "reply_words", "reply_contents",
                    ] as $table) {
                        $name = $table == "reply" ? "id" : "rid";
                        sql_delete([
                            "open_wechat_{$table}",
                            "{$name} = :rid",
                            [
                                ":rid" => $words['rid'],
                            ],
                        ]);
                    }
                    return true;
                } else {
                    return false;
                }
                break;
            case 'delall':
                foreach ([
                    "reply", "reply_words", "reply_contents",
                ] as $table) {
                    sql_delete([
                        "open_wechat_{$table}",
                        "app = :app AND lcms = :lcms",
                        [
                            ":app"  => L_NAME,
                            ":lcms" => $_L['ROOTID'],
                        ],
                    ]);
                }
                return true;
                break;
            default:
                if ($para['name'] && $para['class'] && $para['func']) {
                    $words = sql_get([
                        "open_wechat_reply_words",
                        "name = :name AND app = :app AND lcms = :lcms",
                        "", [
                            ":name" => $para['name'],
                            ":app"  => L_NAME,
                            ":lcms" => $_L['ROOTID'],
                        ],
                    ]);
                    if ($words) {
                        sql_update(["open_wechat_reply_contents", [
                            "parameter" => arr2sql([
                                "open" => [
                                    "class" => $para['class'],
                                    "func"  => $para['func'],
                                ],
                            ]),
                        ], "rid = :rid", [
                            ":rid" => $words['rid'],
                        ]]);
                        return $words['rid'];
                    } else {
                        $insert_id = sql_insert([
                            "open_wechat_reply",
                            [
                                "type"     => "2",
                                "app"      => L_NAME,
                                "order_no" => "999999",
                                "lcms"     => $_L['ROOTID'],
                            ],
                        ]);
                        if ($insert_id) {
                            sql_insert([
                                "open_wechat_reply_words",
                                [
                                    "rid"  => $insert_id,
                                    "name" => $para['name'],
                                    "app"  => L_NAME,
                                    "type" => "1",
                                    "lcms" => $_L['ROOTID'],
                                ],
                            ]);
                            if ($para['type']) {
                                sql_insert([
                                    "open_wechat_reply_contents",
                                    [
                                        "rid"       => $insert_id,
                                        "type"      => $para['type'],
                                        "order_no"  => "999999",
                                        "parameter" => arr2sql($para[$para['type']]),
                                    ],
                                ]);
                            }
                            return $insert_id;
                        } else {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
                break;
        }
    }
    /**
     * @description: 获取所有已关注用户OPENID
     * @param array $para
     * @return array
     */
    public function get_all_openid($para = [])
    {
        global $_L, $LF, $SID;
        $this->access_token();
        $url    = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$this->cfg['access_token']['token']}";
        $url    = $para['next_openid'] ? $url . "&next_openid=" . $para['next_openid'] : $url;
        $result = json_decode(HTTP::get($url), true);
        if ($result['total'] && ($result['total'] == $result['count'] || (($para['page'] - 1) * 10000 + $result['count']) == $result['total'])) {
            unset($result['next_openid']);
        }
        return $result ?: [];
    }
    /**
     * @description: 获取当前在线客服列表
     * @param {*}
     * @return array|bool
     */
    public function get_custom_online()
    {
        global $_L, $LF, $SID;
        $this->access_token();
        $result = json_decode(HTTP::get("https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token={$this->cfg['access_token']['token']}"), true);
        return $result['kf_online_list'] ?: [];
    }
    /**
     * @description: 获取永久素材总数
     * @param {*}
     * @return {*}
     */
    public function get_material_count()
    {
        global $_L, $LF, $SID;
        $this->access_token();
        $result = json_decode(HTTP::get("https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token={$this->cfg['access_token']['token']}"), true);
        return $result ?: [];
    }
    /**
     * @description: 获取永久素材列表
     * @param array $para
     * @return array
     */
    public function get_material_list($para = [])
    {
        global $_L, $LF, $SID;
        $this->access_token();
        $query = json_encode([
            "type"   => $para['type'] ? $para['type'] : "image",
            "offset" => $para['offset'] ? $para['offset'] : "0",
            "count"  => $para['count'] ? $para['count'] : "20",
        ]);
        $result = json_decode(HTTP::post("https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token={$this->cfg['access_token']['token']}", $query), true);
        return $result ?: [];
    }
    /**
     * @description: 无缓存跳转
     * @param string $url
     * @return {*}
     */
    public function header_nocache($url)
    {
        global $_L, $LF, $SID;
        header('Expires: 0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cahe, must-revalidate');
        header('Cache-Control: post-chedk=0, pre-check=0', false);
        header('Pragma: no-cache');
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $url");
    }
    /**
     * @description: 数组转xml
     * @param array $arr
     * @return string
     */
    public function arr2xml($arr)
    {
        global $_L, $LF, $SID;
        if (!is_array($arr) || count($arr) == 0) {
            return false;
        } else {
            $xml = "<xml>";
            foreach ($arr as $key => $val) {
                if (is_numeric($val)) {
                    $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
                } else {
                    $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
                }
            }
            $xml .= "</xml>";
            return $xml;
        }
    }
    /**
     * @description: 打开信息页面
     * @param array $page
     * @return {*}
     */
    public function page_msg($page = [])
    {
        global $_L, $LF, $SID;
        if ($this->cfg['thirdapi'] && stripos($this->cfg['thirdapi'], "/app/index.php?rootid=") !== false) {
            $url = $this->cfg['thirdapi'] . "msg&c=page&body=";
        } else {
            $url = "{$_L['config']['web']['domain_api']}app/index.php?rootid={$_L['ROOTID']}&n=wechat&c=page&a=msg&body=";
        }
        okinfo($url . urlencode(base64_encode(json_encode([
            "icon"  => $page['icon'] ?: "success",
            "title" => $page['title'],
            "desc"  => $page['desc'],
            "info"  => $page['info'],
        ]))));
        exit;
    }
    /**
     * @description: 打开投诉页面
     * @param string $callback
     * @return {*}
     */
    public function page_tousu($callback = "")
    {
        global $_L, $LF, $SID;
        if ($this->cfg['thirdapi'] && stripos($this->cfg['thirdapi'], "/app/index.php?rootid=") !== false) {
            $url = $this->cfg['thirdapi'] . "tousu&c=page&callback=";
        } else {
            $url = "{$_L['config']['web']['domain_api']}app/index.php?rootid={$_L['ROOTID']}&n=wechat&c=page&a=tousu&callback=";
        }
        okinfo($url . urlencode($callback));
        exit;
    }
}
