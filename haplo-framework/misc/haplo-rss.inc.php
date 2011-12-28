<?php
    /****************************************************************************************/
    /* HaploRss - a class fetching and parsing RSS feeds                                   */
    /*                                                                                      */
    /* This file is part of the Haplo Framework, a simple PHP MVC framework                 */ 
    /*                                                                                      */
    /* Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot                        */
    /*                                                                                      */
    /* For the full copyright and license information, please view the LICENSE              */
    /* file that was distributed with this source code                                      */
    /****************************************************************************************/
   
    // RssObject, RssChannel and RssItem are support objects 
    // which represent the structure of the returned data
    class RssObject {
        public $channel;
        public $items = array();
      
        public function __construct() {
            $this->channel = new RssChannel();
        }
    }
   
    class RssChannel {
        public $title = '';
        public $description = '';
        public $link = '';
        public $date = '';
        public $generator = '';
        public $language = '';
    }
   
    class RssItem {
        public $title = '';
        public $description = '';
        public $link = '';
        public $date = '';
        public $author = '';
        public $categories = array();
    }
   
    // main class
    class HaploRss {
        protected $rssObject;
        protected $numItems;
        protected $outputEncoding;
        protected $inItem = false;
        protected $inChannel = false;
        protected $tag = '';
        protected $pointer = -1;
        protected $tempContent = '';
        protected $successful = false;
      
        // $numItems = -1 means whatever's in the feed
        // outputEncoding - defaults to UTF-8
        public function __construct($url, $numItems = -1, $outputEncoding = 'UTF-8') {
            $this->rssObject = new RssObject(); // this object holds the returned data
            $this->numItems = $numItems;
            $this->outputEncoding = $outputEncoding;

            // request feed
            if ($data = HaploHttp::get($url)) {
                $this->successful = $this->parse($data);
            }
        }
      
        // gets the returned feed data structure
        public function get_rss_object() {
            if ($this->successful) {
                return $this->rssObject;
            } else {
                return false;
            }
        }
      
        protected function parse($data) {
            // set up XML parser
            $parser = xml_parser_create();
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $this->outputEncoding);
            // set scope of handler functions to this class
            xml_set_object($parser, $this);
            // set handler functions
            xml_set_element_handler($parser, 'start_tag', 'close_tag');
            xml_set_character_data_handler($parser, 'tag_content');
            // parse the data, set flag to indicate success or failure
            $result = xml_parse($parser, $data);
            // free memory used
            xml_parser_free($parser);
         
            return $result;
        }
      
        // this function triggers each time the parser encounters a new tag
        protected function start_tag($parser, $name, $attributes) {
            if ($this->inItem || ($this->inChannel && $name != 'ITEM')) {
                $this->tag = $name;
            } else {
                switch ($name) {
                    case 'ITEM':
                        $this->inItem = true;
                        $this->pointer++;
                  
                        if ($this->numItems == -1 || $this->pointer < $this->numItems) {
                            $this->rssObject->items[] = new RssItem();
                        }
                        break;
                    case 'CHANNEL':
                        $this->inChannel = true;
                        break;
                }
            }
        }
      
        // this function triggers when the parser encounters a corresponding close tag
        protected function close_tag($parser, $name) {
            if ($name == 'ITEM') {
                if ($this->numItems == -1 || $this->pointer < $this->numItems) {
                    // if the feed contained a content element we'll override the description text with it as it's 
                    // likely to represent the entire article
                    if ($this->tempContent != '') {
                        $this->rssObject->items[$this->pointer]->description = $this->tempContent;
                        $this->tempContent = '';
                    }
                }
                $this->inItem = false;
            } elseif ($name == 'CHANNEL') {
                $this->inChannel = false;
            }
        }
      
        // this function triggers when the parser encounters content for the current tag
        protected function tag_content($parser, $data) {
            // is the parser looking at an item
            if ($this->inItem) {
                if ($this->numItems == -1 || $this->pointer < $this->numItems) {
                    switch ($this->tag) {
                        case 'TITLE':
                            $this->rssObject->items[$this->pointer]->title .= $data;
                            break;
                        case 'DESCRIPTION':
                            $this->rssObject->items[$this->pointer]->description .= $data;
                            break;
                        case 'CONTENT:ENCODED':
                            $this->tempContent .= $data;
                            break;
                        case 'LINK':
                            $this->rssObject->items[$this->pointer]->link .= $data;
                            break;
                        case 'PUBDATE':
                        case 'DC:DATE':
                            $this->rssObject->items[$this->pointer]->date .= $data;
                            break;
                        case 'AUTHOR':
                        case 'DC:CREATOR':
                            $this->rssObject->items[$this->pointer]->author .= $data;
                            break;
                        case 'CATEGORY':
                            if (trim($data) != '') {
                                $this->rssObject->items[$this->pointer]->categories[] = $data;
                            }
                            break;
                  
                    }
                }
            } elseif ($this->inChannel) { // is the parser looking at global channel data
                switch ($this->tag) {
                    case 'TITLE':
                        $this->rssObject->channel->title .= $data;
                        break;
                    case 'DESCRIPTION':
                        $this->rssObject->channel->description .= $data;
                        break;
                    case 'LINK':
                        $this->rssObject->channel->link .= $data;
                        break;
                    case 'PUBDATE':
                    case 'LASTBUILDDATE':
                        $this->rssObject->channel->date .= $data;
                        break;
                    case 'GENERATOR':
                        $this->rssObject->channel->generator .= $data;
                        break;
                    case 'LANGUAGE':
                        $this->rssObject->channel->language .= $data;
                        break;
                }
            }
        }
    }
?>