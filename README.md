# bigcsv
突破PHP内存限制，从MySQL导出数据生成csv文件

# Example

```php
$db_config = [
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => '12345',
    'dbname' => 'erp',
    'port' => 3306,
    'charset' => 'utf8'
];
$csv_header = ["订单号", "状态"];
$format = ["order_status" => function($str){
    return $str == 1 ? "完成" : "取消";
}];
$sql = 'select order_number, order_status from erp_ordersale';

$obj = \bigcsv\BigCSV::getinstance($db_config);
$obj->download($sql, $csv_header, $format);
```
