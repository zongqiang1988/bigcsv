<?php

namespace Bigcsv;

class BigCSV
{
    private static $instance;

    private function __construct($db_config)
    {
        $this->db_config = $db_config;
    }

    public static function getinstance($db_config)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($db_config);
        }
        return self::$instance;
    }

    /**
     * @param $query 要执行的查询语句
     * @param array $header 要导出的csv首行
     * @param array $formart_arr 可以用匿名函数格式化字段
     */
    public function download($query, $header = [], $formart_arr = [])
    {

        ini_set("max_execution_time", 0);

        $con = mysqli_connect(
            $this->db_config['host'],
            $this->db_config['user'],
            $this->db_config['password'],
            $this->db_config['dbname'],
            $this->db_config['port']
        );

        if (mysqli_connect_errno($con)) {
            throw new Exception("Database connection error");
        }

        $con->set_charset($this->db_config['charset']);

        /*
         * 可选。一个常量。可以是下列值中的任意一个：
         * MYSQLI_USE_RESULT（如果需要检索大量数据，请使用这个，结果集不返回给PHP）
         * MYSQLI_STORE_RESULT（默认）
         */
        $resource = mysqli_query($con, $query, MYSQLI_USE_RESULT);

        $this->header($header);

        while ($res = mysqli_fetch_assoc($resource)) {

            $nums = count($res);
            $str = '';

            foreach ($res as $k => $v) {

                if (isset($formart_arr[$k])) {
                    $v = $formart_arr[$k]($v);
                }

                if (--$nums == 0) {
                    $str .= '"' . str_replace("\"", "\"\"", mb_convert_encoding($v, 'GB18030', 'utf-8')) . "\"\r\n";
                } else {
                    $str .= '"' . str_replace("\"", "\"\"", mb_convert_encoding($v, 'GB18030', 'utf-8')) . '",';
                }
            }

            echo $str;
            flush();
        }

        return;
    }

    /**
     * @param $header
     */
    private function header($header)
    {

        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:attachment;filename=" . date('Y-m-d-H-i-s', time()) . '.csv');
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        if (!empty($header)) {
            $str = '';
            foreach ($header as $v) {
                $str .= '"' . str_replace("\"", "\"\"", mb_convert_encoding($v, 'gb2312', 'utf-8')) . '",';
            }
            $str = substr($str, 0, -1);
            $str .= "\r\n";
            echo $str;
        }
    }

}
