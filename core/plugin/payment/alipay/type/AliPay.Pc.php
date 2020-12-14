<?php
load::plugin("payment/alipay/type/lib/AliPay.Api");
class AliPayPc
{
    public function Order($config, $order)
    {
        $input = [
            'method'      => 'alipay.trade.page.pay',
            'app_id'      => $config->get['appid'],
            'format'      => $config->get['format'],
            'charset'     => $config->get['charset'],
            'sign_type'   => $config->get['sign_type'],
            'timestamp'   => $config->get['timestamp'],
            'version'     => $config->get['version'],
            'notify_url'  => $config->get['notify_url'],
            'return_url'  => $config->get['return_url'],
            'biz_content' => json_encode([
                'out_trade_no' => $order['order_no'],
                'product_code' => "FAST_INSTANT_TRADE_PAY",
                'total_amount' => $order['pay'],
                'subject'      => $order['body'],
            ] + ($order['fenqi'] > 0 ? ['extend_params' => ["hb_fq_num" => $order['fenqi'], "hb_fq_seller_percent" => "100"]] : [])),
        ];
        $input = AliPayApi::unifiedOrder($config, $input);
        return $this->buildRequestJson($config, $input);
    }
    protected function buildRequestJson($config, $input)
    {
        foreach ($input as $key => $val) {
            $arr[$key] = str_replace("'", "&apos;", $val);
        }
        $result = [
            "url"  => $config->get['gatewayurl'] . "?charset=" . $config->get['charset'],
            "data" => $arr,
        ];
        return $result;
    }
}
