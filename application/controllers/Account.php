<?php
/**
 * User: sbraun
 * Date: 16.01.18
 * Time: 18:29
 */

class Account extends MY_Controller
{
    public $default_helpers = ['url_helper'];
    public $default_libraries = [];
    public $auth_ldap_status = false;
    public $no_overhead = true;
    public $libraries = ['oauth_lib', 'session'];

    public function __construct() {
        parent::__construct();
//        $this->load->library('oauth_lib');
//        $this->load->library('session');

    }

    public function index($param1 = null) {
//        var_dump($param1);
//        redirect('account/login_form');
        return $this->login_form();
    }

    public function login_form() {
        $data = [
            'form_url' => site_url('account/login_form'),
            'google_url' => site_url('account/call_google'),
        ];
        $input = $this->input->get_post('login');
        if ($input) {
            if (isset($input['google']) && $input['google'] > 0) {
                redirect($data['google_url']);
                die;
            }

        }
        $body = ci()->get_view('login', $data);
        if (ci()->is_ajax_request()) {
            $data_ajax['html']['append']['body'] = trim($body);
            $this->show_ajax_message($data_ajax);
        } else {
            ci()->get_view('layout', ['body' => trim($body)], FALSE);
        }
    }

    public function login() {
//        $server_name = $_SERVER['SERVER_NAME'];
//        var_dump($server_name);
//        var_dump($this->config->item('base_url'));
//        var_dump(site_url());
        $_SESSION['calls'] = (int) @$_SESSION['calls'] + 1;
        echo "<pre>";
        echo "<a href='" . site_url('Account/call_google') . '\' target="_blank"> Call Google</a>' . "\n";
//        \League\OAuth2\Client\Token\AccessToken
        /** @var \League\OAuth2\Client\Token\AccessToken $token */
        $token = @$_SESSION['qlu']['google']['token_obj'];
        if (!is_object($token) || $token->hasExpired())
            var_dump("token is expired");
        else
            var_dump("token is valid");
        var_dump($_SESSION);
//        var_dump($this->session->userdata('qlu'));
        echo "</pre>";
    }

    public function oauth2_redirect() {
        if (1 || $_SERVER['HTTP_REFERER'] == "https://accounts.google.de/accounts/SetSID") {
            $state = $this->input->get_post('state');
            $code = $this->input->get_post('code');
            $authuser = $this->input->get_post('authuser');
            $session_state = $this->input->get_post('session_state');
            $prompt = $this->input->get_post('prompt');
            $_SESSION['qlu']['google']['GET'] = [
              'state' => $state,
              'code' => $code,
              'authuser' => $authuser,
              'session_state' => $session_state,
              'prompt' => $prompt,
            ];
            $_SESSION['oauth2state'] = $state;
        }
        /** @var Oauth_lib $oauth_lib */
        $oauth_lib = $this->oauth_lib;
        $oauth_lib->config = $this->config->item('oauth')['google'];
        $r = $oauth_lib->google_auth();
        if ($r['token'] && $r['token']->hasExpired() == false) {
            redirect(site_url('account/login'));
            die;
        }
        echo "<pre>";
        var_dump($_SESSION);
//        var_dump($_SESSION);
//        var_dump($_SERVER, $_REQUEST);
        echo "</pre>";

    }

    public function call_google() {
        /** @var Oauth_lib $oauth_lib */
        $oauth_lib = $this->oauth_lib;
//        $oauth_lib->config = $this->config->item('oauth')['demo'];
//        $r = $oauth_lib->basic();
        $oauth_lib->config = $this->config->item('oauth')['google'];

//        if (!@$_SESSION['qlu']['google']['token']) {}
        $r = $oauth_lib->google_auth();
        var_dump($r);
    }

    public function google_authentication_request() {
        $url = "https://accounts.google.com/o/oauth2/v2/auth?
 client_id=424911365001.apps.googleusercontent.com&
 response_type=code&
 scope=openid%20email&
 redirect_uri=https://oauth2-login-demo.example.com/code&
 state=security_token%3D138r5719ru3e1%26url%3Dhttps://oauth2-login-demo.example.com/myHome&
 login_hint=jsmith@example.com&
 openid.realm=example.com&
 nonce=0394852-3190485-2490358&
 hd=example.com";
    }

    public function test_url() {
        $oauth_lib_config = $this->config->item('oauth')['google'];
//        var_dump($_SESSION['qlu']['google']);
        var_dump($oauth_lib_config);
        var_dump($oauth_lib_config['clientSecret']);
//        die;
        $code = $_SESSION['qlu']['google']['GET']['code'];

        $this->load->library('curl_lib');
        /** @var Curl_lib $curl_lib */
        $curl_lib = $this->curl_lib;
        $url = "https://accounts.google.com/o/oauth2/token";
        $post_data = [
            'client_id' => $oauth_lib_config['clientId'],
            'client_secret' => $oauth_lib_config['clientSecret'],
            'code' => $code,
            'redirect_uri' => $oauth_lib_config['redirectUri'],
            'grant_type' => 'authorization_code',
        ];
        $response = $curl_lib->get_data($url, [], [], $post_data);


        echo "<pre>";
        var_dump($response);
        if ($curl_lib->error)
            var_dump($curl_lib->error);
        echo "</pre>";
    }

    public function twitter() {
        $this->load->library('curl_lib');
        $host = "api.twitter.com";
        $uri = "/oauth/request_token";
        /** @var Curl_lib $curl_lib */
        $curl_lib = $this->curl_lib;
        $response = $curl_lib->get_data($host . $uri, [], [], $post_data);

    }

    public function to_string_test () {
        $k = new Klasse();
        $k->a = "A";
        $k->b = "B";

        print_r(json_decode((string)$k));
        print_r(json_decode($k));
    }

}

class Klasse implements JsonSerializable, Serializable {
//class Klasse implements Serializable {
    /**
     * String representation of object
     * @link  http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize() {
        return __LINE__;
        // TODO: Implement serialize() method.
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
    public function unserialize($serialized) {
        // TODO: Implement unserialize() method.
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize() {
        return __LINE__;
        // TODO: Implement jsonSerialize() method.
    }

    public function __toString() {
        return (string) __LINE__;
    }
}