<?php

/**
 * User: sebra
 * Date: 16.09.16
 * Time: 11:29
 */
class Curl_base
{
    /** @var array $default_params Parameters for every curl connection */
    protected $default_params = array(
        "protocol" => "https",
        "use_htaccess_login" => false,
        "htaccess_login" => array(
            "username" => null,
            "password" => null,
        ),
    );

    /** @var array $error */
    public $error;
    public $cache_handler = null;
    public $cache_time = 60 * 5; # 5min

    public function __construct() {

    }

    /**
     * @param string $url
     * @param array  $params connection parameters (login, htaccess, ...)
     * @param array  $get_params
     * @param array  $post_params
     *
     * @return Curl_response
     */
    public function get_data(string $url, array $params = array(), array $get_params = [], array $post_params = []): Curl_response {
        if ($this->cache_handler) {
            $cached_response = $this->get_cached_request($url, $params, $get_params, $post_params);
            if ($cached_response)
                return $cached_response;
        }
        
        $params = $this->params = array_merge($this->default_params, $params);
        if (!empty($get_params)) {
            foreach ($get_params as $k => $v)
                $get_params_vars[] = urlencode($k) . "=" . urlencode($v);
            $get_params_url = "?" . implode("&", $get_params_vars);
        } else $get_params_url = "";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . $get_params_url);
        if (!empty($post_params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_params));
//        curl_setopt($ch, CURLOPT_POSTFIELDS,
//          http_build_query(array('postvar1' => 'value1')));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($params['use_htaccess_login']) {
            $username = $params['htaccess_login']['username'];
            $password = $params['htaccess_login']['password'];
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        $output = curl_exec($ch);
        if (!$output)
            $this->error = array(
                "url" => $url,
                "error" => curl_error($ch),
                "errno" => curl_errno($ch),
                'http_status' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            );
        else
            $this->error = [
                "url" => $url,
                'http_status' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            ];
        $response = new Curl_response($output, $url, curl_getinfo($ch, CURLINFO_HTTP_CODE), curl_errno($ch), curl_error($ch));
        curl_close($ch);
        if ($this->cache_handler)
            $this->set_cached_request($url, $params, $get_params, $post_params, $response);
        return $response;
    }


    /**
     * @param array $url_segments (only segments after :// )
     *
     * @return string implode segments with '/' as glue
     */
    public function url_segments_to_url($url_segments): string {
        if (is_string($url_segments))
            $url_segments = [$url_segments];
        if (empty($url_segments) || !is_array($url_segments))
            return "";
        return implode("/", $url_segments);
    }


    private function get_cached_request(string $url, array $params = array(), array $get_params = [], array $post_params = []) {
        if ($this->cache_handler) {
            $cache_key = json_encode([$url, $get_params, $post_params]);
            return $this->cache_handler->get($cache_key);
        }
    }

    private function set_cached_request(string $url, array $params = array(), array $get_params = [], array $post_params = [], Curl_response $response) {
        if ($this->cache_handler) {
            $cache_key = json_encode([$url, $get_params, $post_params]);
            return $this->cache_handler->save($cache_key, $response, $this->cache_time);
        }
    }
}

class Curl_lib extends Curl_base
{
    /** @var array $api_params Parameters for every api curl connection */
    public $api_params = array(
        "use_htaccess_login" => true,
        "htaccess_login" => array(
            "username" => "api",
            "password" => "Vfe3ALkOhRHysX",
        ),
    );
    /** @var string $api_server set by constructor (hostname, like localhost or back.entwicklung.spar-mit.com */
    protected $api_server;

    /** @var string $api_path */
    protected $api_path = "/public/api/";
    protected $api_path_cw = "/public/api_cw/";
    protected $search_path = "/search/search/get";
    protected $distance_path = "/search/search/getdistance";
    protected $location_path = "/search/search/getlocations";
    protected $getOptions_path = "/search/search/getAllOptions";

    public function __construct() {
        parent::__construct();
        $this->CI = &ci();
        $api_config = ci()->config->item('api');
        if (isset($api_config['api_server']))
            $this->api_server = $api_config['api_server'];
        if (isset($api_config['api_params']) && is_array($api_config['api_params'])) {
            $this->api_params = array_merge($this->api_params, $api_config['api_params']);
        }
        $protocol = ci()->config->item('protocol');
        if ($protocol)
            $this->api_params['protocol'] = $protocol;
        if (($_SERVER['SERVER_NAME'] == "localhost" && is_a(ci(), "Media"))) {
            $this->api_server = "backend.sparmit.local";
            $this->api_params['use_htaccess_login'] = true;
            $this->api_params['htaccess_login']['username'] = "entwicklung";
            $this->api_params['htaccess_login']['password'] = "smr";
        }
        $this->api_params = array_merge($this->default_params, $this->api_params);
    }

    /**
     * @param array $url_segments
     * @param array $params connection parameters (login, htaccess, ...)
     * @param array $get_params
     * @param array $post_params
     *
     * @return string
     */
    public function get_api_data($url_segments, $params = [], $get_params = [], $post_params = []) {
        $path = $this->url_segments_to_url($url_segments);
        $params = array_merge($this->api_params, $params);
        $protocol = $params['protocol'];

        $api_server = (@$params['api_server']) ?: $this->api_server;
        $api_path = (@$params['api_path']) ?: $this->api_path;
        $url = $protocol . "://" . $api_server . $api_path . $path;
        return $this->get_data($url, $params, $get_params, $post_params);
    }

    /**
     * @param array|string $url_segments
     * @param array        $params connection parameters (login, htaccess, ...)
     * @param array        $get_params
     * @param array        $post_params
     *
     * @return string
     */
    public function get_api_cw_data($url_segments, $params = [], $get_params = [], $post_params = []) {
        $params = array_merge($params, ['api_path' => '/public/api_cw/']);
        return $this->get_api_data($url_segments, $params, $get_params, $post_params);
    }

    /**
     * @param array|string $url_segments
     * @param array        $params connection parameters (login, htaccess, ...)
     * @param array        $get_params
     * @param array        $post_params
     *
     * @return string
     */
    public function get_search_data($url_segments, $params = [], $get_params = [], $post_params = []) {
        $path = $this->url_segments_to_url($url_segments);
        $params = array_merge($this->api_params, $params);
        $protocol = $params['protocol'];
        $api_server = $this->api_server;
        $search_path = $this->search_path;
        $url = $protocol . "://" . $api_server . $search_path . $path;
        return $this->get_data($url, $params, $get_params, $post_params);
    }

    /**
     * @param array $url_segments
     * @param array $params connection parameters (login, htaccess, ...)
     * @param array $get_params
     * @param array $post_params
     *
     * @return string
     */
    public function get_distance_data($url_segments, $params = [], $get_params = [], $post_params = []) {
        $path = $this->url_segments_to_url($url_segments);
        $params = array_merge($this->api_params, $params);
        $protocol = $params['protocol'];
        $api_server = $this->api_server;
        $search_path = $this->distance_path;
        $url = $protocol . "://" . $api_server . $search_path . $path;
        return $this->get_data($url, $params, $get_params, $post_params);
    }


    /* @param
     * @return json with range pakets
     */
    public function get_range_data($url_segments, $params = [], $get_params = [], $post_params = []) {
        $path = $this->url_segments_to_url($url_segments);
        $params = array_merge($this->api_params, $params);
        $protocol = $params['protocol'];
        $api_server = $this->api_server;
        $search_path = $this->location_path;
        $url = $protocol . "://" . $api_server . $search_path . $path;
        return $this->get_data($url, $params, $get_params, $post_params);
    }

    /*
     * which options? ->rename get_search_options
     * @params
     * return json with package information
     */
    public function get_options_data($url_segments, $params = [], $get_params = [], $post_params = []) {
        $path = $this->url_segments_to_url($url_segments);
        $params = array_merge($this->api_params, $params);
        $protocol = $params['protocol'];
        $api_server = $this->api_server;
        $search_path = $this->getOptions_path;
        $url = $protocol . "://" . $api_server . $search_path . $path;
        return $this->get_data($url, $params, $get_params, $post_params);
    }

}

class Curl_response implements Serializable
{
    private $plain_response;
    private $decoded;
    public $url;
    public $http_status;
    public $error_no;
    public $error_msg;

    public function __construct($response, $url, $http_status, $error_no, $error_msg) {
        $this->plain_response = $response;
        $this->url = $url;
        $this->http_status = $http_status;
        $this->error_no = $error_no;
        $this->error_msg = $error_msg;
    }

    public function __toString() {
        return $this->plain();
    }

    public function plain() {
        if ($this->http_status == 404)
            return '';
        return $this->plain_response;
    }

    public function decoded() {
        if (!$this->decoded)
            $this->decoded = json_decode($this->plain_response);
        return $this->decoded;
    }


    /**
     * String representation of object
     * @link  http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize() {
        return serialize(get_object_vars($this));
    }

    /**
     * Constructs the object
     * @link  http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     * @since 5.1.0
     */
    public function unserialize($data) {
        if (method_exists('stdClass', 'unserialize'))
            return stdClass::unserialize($data);
        else {
            $values = unserialize($data);
            foreach ($values as $key => $value) {
                $this->$key = $value;
            }
        }
    }

}