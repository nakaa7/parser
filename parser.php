<?php

class Parser
{

    
    public $search = [
        'google', 'yandex', 'mail', 'Bing'
    ];
    public $file;
    private $ip;
    private $url;
    private $date;
    private $code;
    private $trafic;
    private $userAgent;


    public function __construct($filename)
    {
        $this->file = $this->getFile($filename); 
    }

    public function getFile($filename)
    {

        if (file_exists($filename)) {
            return file($filename);
        } else {
            die("Файл не найден");
        }
    }

    public function parserFile()
    {

        foreach ($this->file as $str) {

            $arr_log = preg_split('/"|-\s-\s/', $str);

            preg_match('/[0-9]{3}/', $arr_log[3], $matches_code);
            preg_match('/\d{2}\/[a-zA-Z]+\/\d{4}:\d{2}:\d{2}:\d{2}/', $arr_log[1], $matches_date);

            $this->ip[] = $arr_log[0];
            $this->url[] = $arr_log[4];
            $this->userAgent[] = $arr_log[6];
            $this->trafic[] = trim($arr_log[0]) . ' ' . $arr_log[6];
            
            $this->code[] = $matches_code[0];
            $this->date[] = $matches_date[0];
        }
        
        return $this->getJson();
    }

    public function countHit()
    {
        return count($this->url);
    }

    public function uniqueUrl()
    {
        return count(array_unique($this->url));
    }

    public function countTraffic()
    {
        return count(array_unique($this->trafic));
    }

    public function countSearch()
    {
        foreach ($this->url as $url) {
            foreach ($this->search as $search) {
                if (preg_match("/$search/", $url)) {
                    $search_all[$search] += 1;
                }
            }
        }
        return $search_all;
    }

    public function countCode()
    {
        foreach ($this->code as $code) {
            $codes[$code] += 1;
        }
        
        return $codes;
    }

    public function getJson()
    {
        $arr = [
            'views' => $this->countHit(),
            'urls' => $this->uniqueUrl(),
            'traffic' => $this->countTraffic(),
            'crawlers' => $this->countSearch(),
            'statusCodes' => $this->countCode()
        ];

        return json_encode($arr, JSON_PRETTY_PRINT);
    }

}

if($_SERVER['argv'][1]){
    $filename = trim($_SERVER['argv'][1]);   
}else{
    $filename = 'acess_log';
}

$parser = new Parser($filename);
echo $parser->parserFile();