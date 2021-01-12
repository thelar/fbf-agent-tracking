<?php


class Fbf_Agent_Tracking_Api
{

    private $version;
    private $plugin;


    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('parse_request', array($this, 'endpoint'), 0);
    }

    public function endpoint()
    {
        global $wp;

        $endpoint_vars = $wp->query_vars;

        // if endpoint
        if ($wp->request == 'api/v2/agent_tracking') {
            // Your own function to process end pint
            $this->processEndPointXML($_REQUEST);
            exit;
        }
    }

    public function processEndPointXML($request)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fbf_agent_tracking';
        //$query = 'SELECT * FROM ' . $table_name . ' WHERE timestamp >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY AND timestamp < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY';
        $query = 'SELECT * FROM ' . $table_name . ' WHERE timestamp >= curdate() - INTERVAL DAYOFWEEK(curdate())+7 DAY';
        //var_dump($query);
        $xml = <<<XML
<?xml version='1.0' standalone='yes'?>
<root>
</root>
XML;
        $output = new SimpleXMLElement($xml);
        $results = $wpdb->get_results($query);
        if($results){
            foreach($results as $result){
                $row = $output->addChild('row');
                $row->addChild('datetime', $result->timestamp);
                $row->addChild('sales_id', $result->sales_id);
                $row->addChild('sales_login', $result->sales_login);
                $row->addChild('customer_id', $result->customer_id);
                $row->addChild('customer_login', $result->customer_login);
                $row->addChild('action', $result->action);
                $row->addChild('order_id', $result->order_id);
            }
        }
        header('Content-Type: application/xml');
        print($output->asXML());
    }
}