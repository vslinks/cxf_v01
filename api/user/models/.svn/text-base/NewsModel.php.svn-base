<?php
namespace api\user;

class NewsModel extends Model {

    function __construct($id = null, $title = null, $content = null) {

        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->table = 'News';
    }


    static $init_valid_array = array("id" => array('int', '', 'YES', ''), "title" => array('varchar', '255', 'YES', ''), "content" => array('varchar', '255', 'YES', ''));
    public $id;
    public $title;
    public $content;
}    