<?php


class zabbix_trend extends zabbix_manager
{
    /**
     * @var int
     */
    protected $refresh = 0;

    /**
     * @var string
     */
    protected $trend_start_date;

    /**
     * @var string
     */
    protected $trend_end_date;

    /**
     * @var string
     */
    protected $start_date = '-7 day';

    /**
     * @var string
     */
    protected $end_date = 'now';

    /**
     * @var string
     */
    protected $format = 'd-m-Y H:i';

    /**
     * @var string
     */
    protected $format_html = 'dd-mm-yyyy hh:ii';

    /**
     * @var int
     */
    protected $start_id;

    /**
     * @var array
     */
    protected $monitor_list = [];


    /**
     * @var array
     */
    private $data_db = [];

    public function __construct($config){
        parent::__construct($config['server'],$config['user'],$config['pwd']);

        $this->trend_start_date = $this->initDate($this->start_date);
        $this->trend_end_date = $this->initDate($this->end_date);

        $this->validateVar('refresh','int');
        $this->validateVar('trend_start_date','date');
        $this->validateVar('trend_end_date','date');
        $this->validateVar('start_id','int');

        if ($this->start_id){
            $this->monitor_list[$this->start_id] = $this->start_id;
        }
    }

    public function getRefreshOption(){
        global $app;
        $refresh_values = [
            '0' => '- ' . $app->lng('No Refresh') . ' -',
            '1' => '1 ' . $app->lng('minutes'),
            '5' => '5 ' . $app->lng('minutes'),
            '10' => '10 ' . $app->lng('minutes'),
            '15' => '15 ' . $app->lng('minutes'),
            '30' => '30 ' . $app->lng('minutes'),
            '60' => '60 ' . $app->lng('minutes')
        ];
        $refresh_option = '';
        foreach($refresh_values as $key => $val) {
            if($key == $this->refresh) {
                $refresh_option .= "<option value='$key' SELECTED>$val</option>";
            } else {
                $refresh_option .= "<option value='$key'>$val</option>";
            }
        }
        return $refresh_option;
    }

    public function getTrendStartDate()
    {
        return $this->trend_start_date;
    }

    public function getTrendEndDate()
    {
        return $this->trend_end_date;
    }

    public function getMonitor(){

        if (!$this->data_db) $this->getDataDB();
        $return = [];
        foreach ($this->data_db as $data)
        {
            $checked = isset($this->monitor_list[$data['monitor_id']]) ? 'checked=""' : '';
            $return[] = '<input class="check_monitor" name="monitor_' . $data['monitor_id'] . '" id="monitor_' . $data['monitor_id'] . '" value="' . $data['monitor_id'] . '" type="checkbox" ' . $checked . '> <label for="monitor_' . $data['monitor_id'] . '" class="inlineLabel"> ' . $data['domain'] . '</label>';

        }
        return implode(' ',$return);
    }

    protected function getDataDB(){
        global $app;
        $sql = 'SELECT monitor.*, web_domain.domain FROM monitor, web_domain WHERE monitor.domain_id = web_domain.domain_id AND ' . $app->tform_base->getAuthSQL('r','monitor');
        foreach ($app->db->queryAllRecords($sql) as $data)
        {
            $zp = !empty($data['object']) ? unserialize($data['object']) : null;
            $needed = [];
            if ($zp instanceof zabbix_monitor){
                $needed['monitor_id'] = $data['monitor_id'];
                $needed['domain'] = $data['domain'];
                $needed['ItemTrendId'] = $zp->getItemTrendId();
                $this->data_db[$data['monitor_id']] = $needed;
            }
        }
    }

    public function getDataTrend(){
        $this->connexion();
        $itemTrendId = [];
        foreach($this->monitor_list as $id){
            if (isset($this->data_db[$id],$this->data_db[$id]['ItemTrendId']))
            {
                $itemTrendId[$id] = $this->data_db[$id]['ItemTrendId'];
            }
        }
        $flip = array_flip($itemTrendId);
        $datasets = [];
        foreach($this->get_host_webitems_history(array_values($itemTrendId)) as $id=>$data){
            $datasets = $data;
        }

        $return = [
            'type' => 'line',
            'data' => [
                'datasets' => [$datasets]
            ],
            'options' => [
                'title' => [
                    'text' => 'Chart.js Time Scale'
                ],
                'scales' => [
                    'xAxes' => [[
                        'type' => 'time',
                        'time' => [
                            'parser' => 'timeFormat',
                            'tooltipFormat' => $this->format_html
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => 'Date'
                        ]
                    ]],
                    'yAxes' => [[
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => 'Time (s)'
                        ]
                    ]]
                ],
            ]
        ];

        $this->disconnection();

        return json_encode($datasets, JSON_NUMERIC_CHECK);
    }

    protected function get_host_webitems_history($id,$start='-7 day',$end='now')
    {
        $method = 'history.get';
        $params = [
            'output' => 'extend',
            'itemids' => $id,
            'sortfield' => 'clock',
            'sortorder' => 'DESC',
            'time_from' => strtotime($start),
            'time_till' => strtotime($end),
            //'limit' => 10,
            'history' => 0
        ];
        $response = $this->curl($method, $params);
        $history = [];
        foreach($response as $id=>$trend){
            //echo nl2br(print_r(json_encode($trend), true)) . '<br>';
            $history[$trend->itemid][$id]['x'] = date($this->format,$trend->clock);
            $history[$trend->itemid][$id]['y'] = $trend->value;
        }
        return $history;
    }

    protected function initDate($time)
    {
        $date = new DateTime($time);
        return $date->format($this->format);
    }

    protected function validateVar($var,$type)
    {
        global $app;
        $value = isset($_GET[$var]) ? $_GET[$var] : $this->$var;
        if ($type == 'int') $value = $app->functions->intval($value);
        elseif ($type == 'date' and !$this->validateDate($value)) $value = null;
        $this->$var = $value;
    }

    protected function validateDate($date) {
        $d = DateTime::createFromFormat($this->format, $date);
        return $d && $d->format($this->format) == $date;
    }

}