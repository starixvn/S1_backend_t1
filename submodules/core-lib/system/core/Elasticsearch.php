<?php
/**
 *   filename:   Elasticsearch.php
 *   author:     William Zhang
 *   e-mail:     zhangvy@gmail.com
 *   date:       2017-12-22
 *   @brief:     class for operations associated with Elasticsearch. You can choose send message to Elasticsearch.
 */
if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Elasticsearch Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category    Elasticsearch	
 */
class CI_Elasticsearch {
    protected $client = null;
    const HOSTS = [
        //'http://ftp:t1elkftp2017@elk.pureinfo.cc:9200',
        //'http://ftp:t1elkftp2017@logs.t1t.link:9200',
        'http://ftp:t1elkftp2017@13.112.224.75:9200'
    ];

	public function __construct() {
		// Note:  Do not log messages from this constructor.
        
        try {
            $this->client = Elasticsearch\ClientBuilder::create()   // Instantiate a new ClientBuilder
                ->setHosts(self::HOSTS)      // Set the hosts
                ->setRetries(0)
                ->build();              // Build the client object
            //->setConnectionPool('\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool', [])
        } catch (Exception $e) {
            log_message('error', 'CANNOT create Elasticsearch client!', $e);
            $this->client = null;
        }
	}
	// insertDocument {{{2
	/**
	 *  create a document in given index
	 *
	 *  @param  string index name
	 *  @param  string type
	 *  @return bool
	 */
	public function insertDocument($message = '', $idx = null, $type = null, $hostname = '', $id = null) {
        if(empty($this->client)){
            return null;
        }
        $offset = date("Z");
        $utcTime = time() - $offset;
        $today = date("Y-m-d", $utcTime);
        $timestamp = date("Y-m-d", $utcTime) . "T" . date("H:i:s", $utcTime);
        if (empty($idx)) {
            $idx = 'og_err_' . $today;
        } else {
            $idx = $idx . '_' . $today;
        }
        if (empty($type)) {
            $type = 'err_type';
        }
        if (empty($hostname)) {
            $hostname = gethostname();
        }
		$params = [
			'index'  => $idx,                                                                
			'type'   => $type,                                                                          
			'body' => [                                                                                      
				'hostname' => $hostname,                                                                         
				'message' => $message,                                                
				'@timestamp' => $timestamp,
            ]
		];                                                                                                   
        if (!empty($id)) {
			$params['id'] = $id;
        }
        try {
            $res = $this->client->index($params);                                                                     
            //log_message('error', 'log ES insertDocument res: ' . $res);
            return $res;
        } catch (Exception $e) {
            log_message('error', 'Elasticsearch connection failed!', $e);
        }

	} // insertDocument  }}}2
}
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of Elasticsearch.php
