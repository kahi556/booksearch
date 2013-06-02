<?php
    class BookInfo{
        public $ISBN;
        public $ASIN;
        public $Title;
        public $Author;
        public $Publisher;
        public $PublicationDate;
        public $DetailPageURL;

        /**
         * コンストラクタ
         * 
         */
        public function __construct() {
            $this->ISBN = "";
            $this->ASIN = "";
            $this->Title = "";
            $this->Author = "";
            $this->Publisher = "";
            $this->PublicationDate = "";
            $this->DetailPageURL = "";
        }
    }
?>