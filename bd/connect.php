<?php

class db {
    public $time_load_page; # время выполнения скрипта
    public $memory_start; # потребление памяти при выполнении скрипта
    public $memory_pek_start; # пиковое потребление памяти при выполнении скрипта
    public $error_connect;

    const DB_HOST = "localhost";
    const DB_USER = "root";
    const DB_PASS = "root";
    const DB_NAME = "game_curs";

    private $counter_mysql = 0; # Количество запросов
    private $timer_mysql = 0; # Общее время запросов
    private $mysql_query_desc; # Список запросов
    private $connect;

    # Соединение с БД и запуск класса
    public function __construct() {
        $this->connect = mysqli_connect(self::DB_HOST, self::DB_USER, self::DB_PASS, self::DB_NAME) or die("База данных ушла в себя");
        # Установка языка записи в БД
        mysqli_query($this->connect, "SET NAMES utf8") or die("Невозможно найти БД");
    }
                
    # Считаются запросы
    # @query:String - mysql запрос
    public function q($query)
    {
        $this->mysql_query_desc[] = $query; # Добавляется запрос в список запросов
        $this->counter_mysql++; # Увеличивается количество запросов
        # Считается время выполнения запроса
        $start = microtime(true);
        if(!($result = mysqli_query($this->connect, $query))){
            $this->error_connect = mysqli_error($this->connect);
        }
        $this->timer_mysql += microtime(true)-$start;
        # Возвращается выполненый mysql запрос
        return $result;
    }

    public function getLastId(){
        return mysqli_insert_id($this->connect);
    }

    public function err(){
        $err = $this->error_connect;
        unset($this->error_connect);
        return $err;
    }

    # Выводится отладочная инфа
    public function debug()
    {
        $txt = "";
        $txt .= "Скрипт выполнен за ".$this->time_load_page." сек.<br>\n";
        $txt .= "Количество запросов к БД: ".$this->counter_mysql."<br>\n";
        $txt .= "Время запросов к БД: ".round($this->timer_mysql,4)."<br>\n";
        $txt .= "Список запросов<br>\n<div>";
        $len = sizeof($this->mysql_query_desc);
        for ($i = 0; $i < $len; $i++) {
            $txt .= "[".($i+1)."] ".$this->mysql_query_desc[$i]."<br>\n";
        }
        $txt .= "</div>";
        if ( function_exists('memory_get_usage') ) {
            $type = "Kb";
            $num = round($this->memory_start/1024, 2);
            if ($num > 1024) {
                $type = "Mb";
                $num = round($this->memory_start/1024/1024, 2);
            }
            $txt .= "Потребление памяти: ".$num.$type." <br>\n";
        }
                    
        return $txt;
    }
}