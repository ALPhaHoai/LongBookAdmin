<?php
/**
 * Created by Long
 * Date: 11/19/2018
 * Time: 3:38 PM
 */

/**
 * My curl
 * config: url, method, post_field, referer, cookies, cookie_file, headers, agent
 */
function my_curl($config_array){
    if(isset($config_array['url'])) {
        $url = $config_array['url'];
        if(isset($config_array['ipv6'])) {
            if($config_array['ipv6'] === "normal"){
                $ipv6 = "http://www.ipv6proxy.net/go.php?u=";
                $url = $ipv6 . urlencode($url);
            } else if ($config_array['ipv6'] === "hard"){
                //using proxy
            }
        }
    } else return null;

    $useragent = isset($config_array['agent'])?$config_array['agent']:NULL;
    if((!$useragent || $useragent=="" || $useragent == NULL)){
        $useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0";
    }
    $referer = isset($config_array['referer'])?$config_array['referer']:NULL;

    $headers = isset($config_array['headers'])?$config_array['headers']:array();
    $headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
    $headers[] = "Accept-Language: en-us,en;q=0.5";
    $headers[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $headers[] = "Keep-Alive: 115";
    $headers[] = "Connection: keep-alive";

    $cookies = isset($config_array['cookies'])?$config_array['cookies']:NULL;
    $cookie_file = isset($config_array['cookie_file'])?$config_array['cookie_file']:NULL;

    $method = isset($config_array['method'])?$config_array['method']:"GET";
    $post_field = isset($config_array['post_field'])?$config_array['post_field']:NULL;


    $encoding = isset($config_array['encoding'])?$config_array['encoding']:NULL;
    $sslverify = isset($config_array['sslverify'])?$config_array['sslverify']:NULL;
    $nobody = isset($config_array['nobody'])?$config_array['nobody']:NULL;



    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);
    if($useragent!= null){curl_setopt($curl, CURLOPT_USERAGENT, $useragent);}
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    /**
     * 3 type cookies
     * 1. xincha=vietnam;gacon=5
     * 2. array("xincha=vietnam", "gacon=5", "hahaha=6);
     * 3. array(
     * array("key"=>"xincha", "value"=>vietnam),
     * array("key"=>"gacon", "value"=>5),
     *  );
     * */
    if(is_array($cookies)){
        if(count($cookies) > 0){
            $str_cookies = "";
            foreach ($cookies as $cookie){
                if(is_array($cookie)){
                    if(isset($cookie['key']) && isset($cookie['value'])){
                        //type cookie 3
                        $str_cookies .= trim($cookie['key']) . "=" . trim($cookie['value']) . ";";
                    } else {
                        echo "Invalid cookies";
                    }
                }
                else $str_cookies .= trim($cookie)  . ";";//type cookie 2
            }
            curl_setopt($curl, CURLOPT_COOKIE, str_replace('\\"','"',$str_cookies));
        } else {
            echo "Invalid cookies";
        }
    } else curl_setopt($curl, CURLOPT_COOKIE, str_replace('\\"','"',$cookies));//type cookie 1
    if($cookie_file != null) {
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file);
        //set the cookie the site has for certain features, this is optional
        curl_setopt($curl,CURLOPT_COOKIEFILE, $cookie_file);
    }
    if($referer!=null){curl_setopt($curl, CURLOPT_REFERER, $referer);}
    else if(isset($_SERVER['REQUEST_URI'])) curl_setopt($curl, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);

    if($encoding!=null){curl_setopt($curl, CURLOPT_ENCODING, $encoding);}
    if(strcasecmp($method, "POST") == 0){
        curl_setopt($curl, CURLOPT_POST, 1);
        if(is_array($post_field)) {curl_setopt($curl, CURLOPT_POSTFIELDS, $post_field);}
    }

    if(isset($config_array['timeout']) && is_numeric($config_array['timeout'])){
        curl_setopt($curl, CURLOPT_TIMEOUT, $config_array['timeout']);//timeout in seconds
    }

    if($nobody){curl_setopt($curl, CURLOPT_NOBODY, 1);}
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    //if($ipv6){curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);}
    if($sslverify){
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    }
    $html = curl_exec($curl);

    $err     = curl_errno( $curl );
    $errmsg  = curl_error( $curl );
    $result  = curl_getinfo( $curl );

    $header_content = substr($html, 0, $result['header_size']);
    $body_content = trim(str_replace($header_content, '', $html));
    $pattern = "#Set-Cookie:\\s+(?<cookie>[^=]+=[^;]+)#m";
    preg_match_all($pattern, $header_content, $matches);
    $cookiesOut = implode("; ", $matches['cookie']);
    $result['err_no']   = $err;
    $result['err_msg']  = $errmsg;
    $result['headers']  = $header_content;
    $result['content'] = $body_content;
    $result['cookies'] = $cookiesOut;

    curl_close($curl);
    return $result;
}