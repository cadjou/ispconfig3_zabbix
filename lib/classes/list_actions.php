<?php


class list_actions extends listform_actions
{
    protected $records_loop;

    protected $sortKeys;

    public function onLoad()
    {
        global $app, $conf, $list_def_file;

        $app->uses('tpl,listform,tform');

        //* Clear session variable that is used when lists are embedded with the listview plugin
        $_SESSION['s']['form']['return_to'] = '';

        // Load list definition
        $app->listform->loadListDef($list_def_file);

        if (!is_file('templates/' . $app->listform->listDef["name"] . '_list.htm')) {
            $app->uses('listform_tpl_generator');
            $app->listform_tpl_generator->buildHTML($app->listform->listDef);
        }

        $app->tpl->newTemplate("listpage.tpl.htm");
        $app->tpl->setInclude('content_tpl', 'templates/' . $app->listform->listDef["name"] . '_list.htm');

        //* Manipulate order by for sorting / Every list has a stored value
        //* Against notice error
        if (!isset($_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order'])) {
            $_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order'] = '';
        }

        $php_sort = false;

        if (!empty($_GET['orderby'])) {
            $order = str_replace('tbl_col_', '', $_GET['orderby']);

            //* Check the css class submited value
            if (preg_match("/^[a-z\_]{1,}$/", $order)) {

                if (isset($app->listform->listDef['phpsort']) && is_array($app->listform->listDef['phpsort']) && in_array($order, $app->listform->listDef['phpsort'])) {
                    $php_sort = true;
                } else {
                    // prepend correct table
                    $prepend_table = $app->listform->listDef['table'];
                    if (trim($app->listform->listDef['additional_tables']) != '' && is_array($app->listform->listDef['item']) && count($app->listform->listDef['item']) > 0) {
                        foreach ($app->listform->listDef['item'] as $field) {
                            if ($field['field'] == $order && $field['table'] != '') {
                                $prepend_table = $field['table'];
                                break;
                            }
                        }
                    }
                    $order = $prepend_table . '.' . $order;
                }

                if ($_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order'] == $order) {
                    $_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order'] = $order . ' DESC';
                } else {
                    $_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order'] = $order;
                }
                $_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order_in_php'] = $php_sort;
            }
        }

        // If a manuel oder by like customers isset the sorting will be infront
        if (!empty($_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order']) && !$_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order_in_php']) {
            if (empty($this->SQLOrderBy)) {
                $this->SQLOrderBy = "ORDER BY " . $_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order'];
            } else {
                $this->SQLOrderBy = str_replace("ORDER BY ", "ORDER BY " . $_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order'] . ', ', $this->SQLOrderBy);
            }
        }

        if ($_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order_in_php']) $php_sort = true;

        // Getting Datasets from DB
        $records = $app->db->queryAllRecords($this->getQueryString($php_sort));

        $csrf_token = $app->auth->csrf_token_get($app->listform->listDef['name']);
        $_csrf_id = $csrf_token['csrf_id'];
        $_csrf_key = $csrf_token['csrf_key'];

        $this->DataRowColor = "#FFFFFF";
        $records_new = array();
        if (is_array($records)) {
            $this->idx_key = $app->listform->listDef["table_idx"];
            foreach ($records as $key => $rec) {
                $records_new[$key] = $this->prepareDataRow($rec);
                $records_new[$key]['csrf_id'] = $_csrf_id;
                $records_new[$key]['csrf_key'] = $_csrf_key;
            }
        }

        if (!empty($_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order']) && $_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order_in_php']) {
            $order_by = $_SESSION['search'][$_SESSION['s']['module']['name'] . $app->listform->listDef["name"] . $app->listform->listDef['table']]['order'];
            $order_dir = 'ASC';
            if (substr($order_by, -5) === ' DESC') {
                $order_by = substr($order_by, 0, -5);
                $order_dir = 'DESC';
            }
            $this->sortKeys = array($order_by => $order_dir);
            uasort($records_new, array($this, '_sort'));
        }
        if ($php_sort) {
            $records_new = array_slice($records_new, $app->listform->getPagingValue('offset'), $app->listform->getPagingValue('records_per_page'));
        }

        if (is_array($records_new) && count($records_new) > 0) $app->tpl->setLoop('records', $records_new);

        $this->records_loop = $records_new;
        $this->onShow();
    }
    function  onShow()
    {
        global $app;
        $tmp = [];
        if (is_array($this->records_loop) && count($this->records_loop) > 0){
            //print_r($app->db->queryAllRecords('SELECT client_id FROM zabbix_client WHERE 1'));
            $data_client = [];
            foreach ($app->db->queryAllRecords('SELECT client_id FROM zabbix_client WHERE 1') as $id=>$item){
                $data_client[$item['client_id']] = $item['client_id'];
            }
            foreach ($this->records_loop as $id=>$item) {
                $item['ready'] = isset($data_client[$item['client_id']]) ? $app->lng('yes_txt') : $app->lng('no_txt');
                $tmp[$id] = $item;
            }
            $app->tpl->setLoop('records', $tmp);
        }

        parent::onShow();
    }
}