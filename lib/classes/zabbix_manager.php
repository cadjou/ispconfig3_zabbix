<?php


class zabbix_manager
{

    private const api_zabbix_api = 'api_jsonrpc.php';

    /**
     * @example https://zabbix.domain.com
     * @var string
     */
    private $connexion_server;

    /**
     * @example Admin
     * @var string
     */
    private $connexion_user;

    /**
     * @example My-Pa$$W0rd
     * @var string
     */
    private $connexion_password;

    /**
     * @var string
     */
    private $secret;

    protected $messages = [];

    private $shared = null;

    public function __construct($host,$user,$password)
    {
        $this->connexion_server = 'https://' . rtrim($host,'/');
        $this->connexion_user = $user;
        $this->connexion_password = $password;
    }

    public function checkConnexion()
    {
        if ($this->connexion()){
            return $this->shared ? $this->shared : true;
        }
        return false;
    }

    public function requestGet($method,$params,$items)
    {
        $this->connexion();
        $response = $this->curl($method, $params);

        if (isset($response[0])) return null;

        $response = $response[0];

        $items = is_array($items) ? $items : [$items];
        $return = [];
        $tmp = null;
        foreach ($items as $item) {
            $tab_items = explode(':',$item);
            $tmp = $response;
            foreach ($tab_items as $tab_item) {
                if (isset($tmp->$tab_item)){
                    $tmp = $tmp->$tab_item;
                } elseif (isset($tmp[$tab_item])) {
                    $tmp = $tmp[$tab_item];
                }else{
                    $tmp = null;
                    break;
                }
            }
            $return[$item] = $tmp;
        }
        return count($items) > 1 ? $return : $tmp;
    }

    public function requestChange($method,$params,$return)
    {
        $this->connexion();
        $response = $this->curl($method, $params);
        return isset($response->$return,$response->$return[0]) ? $response->$return[0] : null;
    }

    protected function connexion()
    {
        if ($this->secret) return true;

        if (!$this->connexion_server) return false;

        $method = 'user.login';
        $params = [
            'user' => $this->connexion_user,
            'password' => $this->connexion_password,
        ];
        $this->secret = $this->curl($method, $params, 0);

        return !!$this->secret;
    }

    protected function disconnection()
    {
        $method = 'user.logout';
        $params = [];
        $this->curl($method, $params);
    }

    protected function _response_get($response,$param){
        return isset($response[0],$response[0]->$param) ? $response[0]->$param : null;
    }

    protected function _response_modified($response, $param){
        return isset($response->$param,$response->$param[0]) ? $response->$param[0] : null;
    }

    protected function curl(String $method,Array $params,Int $id = 1)
    {
        if ($id and empty($this->secret)) {
            $this->add_messages('Authentification error','danger');
            return false;
        }
        $post_fields = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => $id,
        ];
        if ($id) {
            $post_fields['auth'] = $this->secret;
        }

        //echo nl2br(print_r(json_encode($post_fields), true)) . '<br>';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->connexion_server . '/' . self::api_zabbix_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json-rpc']);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->add_messages('Curl request error : ' . curl_error($ch),'danger');
            return false;
        }
        curl_close($ch);

        $result_json = json_decode($result);

        //echo nl2br(print_r($post_fields, true)) . '<br>';
        //echo nl2br(print_r($result, true)) . '<br>';

        if (empty($result) or json_last_error() <> 0) {
            $this->add_messages('Server no return json data : Url Server = ' . $this->connexion_server . '/' . self::api_zabbix_api . ' it\'s correct ?','danger');
            return false;
        }
        if (isset($result_json->shared)){
            $this->shared = $result_json->shared;
        }
        if (isset($result_json->error)){
            $this->add_messages($result_json->error->message . ' => ' . $result_json->error->data,'danger');
            return false;
        }
        return $result_json->result;
    }

    protected function add_messages($message,$type = 'info')
    {
        $table_messages['message'] = $message;
        $table_messages['type'] = $type;
        $this->messages[] = $table_messages;
    }

    public function get_messages($type='')
    {
        $return = [];
        if ($type)
        {
            foreach($this->messages as $message){
                if ($message['type'] == $type) {
                    $return[] = $message;
                }
            }
            return $return;
        }
        return $this->messages;
    }

    public function get_messages_html($class=['div' => 'alert alert-%type%','ul'=>'list-group','li'=>'list-group-item alert-%type%'])
    {
        if (!$this->has_messages()) return '';

        //$return = '<ul class="list-group">';
        $messages = $return = [];
        foreach ($this->messages as $message) {
            $class_li = isset($class['li']) ? 'class="' . $class['li'] . '"' : '';
            $messages[$message['type']][] = '<li ' . $class_li . '>' . $message['message'] . '</li>';
        }
        foreach ($messages as $type => $message) {
            $class_div = isset($class['div']) ? 'class="' . $class['div'] . '"' : '';
            $class_ul = isset($class['ul']) ? 'class="' . $class['ul'] . '"' : '';
            $return[] = str_replace( '%type%',$type,'<div ' . $class_div . '><ul ' . $class_ul . '>' . implode("\n",$message) . '</ul></div>');
        }
        return '<div>' . implode("\n",$return) . '</div>';
    }

    protected function has_messages(){
        return count($this->messages);
    }

    public function __destruct()
    {
        $this->disconnection();
    }
}