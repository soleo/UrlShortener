<?php

namespace Soleo\UrlShortener;

class MongoConnection implements ConnectionInterface
{

    private $client;
    private $DB;

    public function __construct($config)
    {
        $options = array("connectTimeoutMS" => 30000);
        $this->client = new \MongoDB\Client($config, $options);
        $this->DB = $this->client->selectDatabase("url_shortener");
    }

    public function lookup($slug, $update = false)
    {
        $c_db = $this->DB;
        $c_table = $c_db->selectCollection("url_table");
        $c_table->createIndex(array('shorturl' => 1 ), array('unique' => true));
        $urls = $c_table->findOne(array('shorturl' => $slug), array('longurl', '_id'));
        if ($urls) {
            if ($update) {
                $c_table->createIndex(array('hits' => 1 ));
                $c_table->update(array("_id" => $urls['_id']), array('$inc' => array("hits" => 1)));
                $days = floor(time()/24/3600);
                $c_table->update(array("_id" => $urls['_id']), array('$inc' => array("hits_d.{$days}" => 1)));
            }
            return $urls['longurl'];
        } else {
            return false;
        }
    }

    public function reverseLookup($longUrl)
    {
        // If stored, return the slug, otherwise, return false
        /*
         * mongodb
         * {
         *   shorturl
         *   longurl
         * }
         */

        $c_db = $this->DB;

        // check if url already exists
        $c_table = $c_db->selectCollection("url_table");
        $c_table->createIndex(array('longurl' => 1 ), array('unique' => true));
        $c_table->createIndex(array('shorturl' => 1 ), array('unique' => true));
        $shorturl = $c_table->findOne(array('longurl' => $longUrl), array('shorturl'));
        if ($shorturl && $shorturl['shorturl']) {
            return $shorturl['shorturl'];
        } else {
            return false;
        }
    }

    public function addNewRecord($longUrl, $slug)
    {
        $c_db = $this->DB;
        $c_table = $c_db->selectCollection("url_table");

        $c_table->insert(array("shorturl" => $slug, "longurl" => $longUrl));
        return $slug;
    }

    public function getIncrementUid()
    {
        // generate increment unique id
        $collection_name = 'increment_id';
        $unique_field =  'section';
        $data_field = 'url_id';

        $c_inc = $this->DB->selectCollection($collection_name);
        $c_inc->createIndex(array($unique_field => 1), array('unique' => true));
        $ret = $this->DB->command(array(
                "findandmodify" => "increment_id",
                "query" => array($unique_field => $data_field),
                "update" => array('$inc' => array($data_field => 1)),
                "upsert" => true
                ));
        if ($ret && $ret['value'] && $ret['value'][$data_field]) {
            return $ret['value'][$data_field];
        }
    }
}
