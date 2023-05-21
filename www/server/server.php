<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'user', 'password');
$channel = $connection->channel();

$channel->queue_declare('rpc_queue', false, false, false, false);



function connect_to_db()
{
    $user = 'MYSQL_USER';
    $pass = 'MYSQL_PASSWORD';
    $host = 'mysql_8';
    $db = 'api_weather_db';

    try
    {
        $dbh = new PDO('mysql:host='.$host.';dbname='.$db, $user, $pass);
    } 
    catch (PDOException $e) 
    {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
    
    return $dbh;
}

function form_picture($city_id)
{
    $dbh = connect_to_db();
    
    $row = $dbh->query('SELECT `latitude`, `longitude`, `name` FROM `tt_cities` WHERE `id`='.$city_id)->fetch();

    $latitude = $row['latitude'];
    $longitude = $row['longitude'];
    $name = $row['name'];

    $res_arr_weather = CallWeatherAPI($latitude, $longitude);

    if ($res_arr_weather[0])
    {
        $obj_weather1 = json_decode($res_arr_weather[1]);
        $obj_weather2 = $obj_weather1->{'hourly'};
        $arr_time = $obj_weather2->{'time'};
        $arr_temp = $obj_weather2->{'temperature_2m'};
        //print_r($obj_weather->{'hourly'});

        //echo '<pre>';
        //print_r($arr_time);
        //print_r($arr_temp);

        foreach($arr_temp as $k1 => $v1)
        {
            if (substr($arr_time[$k1],-5) == '05:00')   $arr_temp_night[] = $v1;
            if (substr($arr_time[$k1],-5) == '16:00')
            {
                $arr_temp_day[] = $v1;
                $arr_time_final[] = substr($arr_time[$k1],2,8);
            }
        }
        //print_r($arr_temp_night);
        //print_r($arr_temp_day);
        //print_r($arr_time_final);
        //echo '</pre>';

        return DrawGraph($arr_time_final, $arr_temp_night, $arr_temp_day, $name);
    }
    else
    {
        return DrawErrorImage('Ошибка при обращении к сервису open-meteo.com.');
    }
}





/*---------------------------------------------------*/
echo " [x] Awaiting RPC requests\n";

$callback = function ($req) {
    $n = intval($req->body);
    
    echo ' [.] form_picture(', $n, ")\n";

    $msg = new AMQPMessage((string) form_picture($n), array('correlation_id' => $req->get('correlation_id')));

    $req->delivery_info['channel']->basic_publish($msg, '', $req->get('reply_to'));
    $req->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

while ($channel->is_open())
{
    $channel->wait();
}

$channel->close();
$connection->close();
/*---------------------------------------------------*/






function DrawGraph($arr_time_final, $arr_temp_night, $arr_temp_day, $name)
{
        $chart_options = array(
            'width' => 700,
            'height' => 200,
            'chart' => array(
                    'type' => 'line',
                    'data' => array(
                            'labels' => $arr_time_final,
                            'datasets' => Array(
                                    '0' => array(
                                            'label' => 'Ночная темп.',
                                            'data' => $arr_temp_night
                                        ),
                                    '1' => array(
                                            'label' => 'Дневная темп.',
                                            'data' => $arr_temp_day
                                        ),
                                )
                        ),
                    'options' => Array(
                        'title' => Array(
                            'display' => true,
                            'text' => $name
                        )
                )
            )
        );
        $res_array = CallGraphAPI($chart_options);
        
        if ($res_array[0])
        {
            //header ('Content-Type: image/png');
            return $res_array[1];
        }
        else
        {
            return DrawErrorImage('Сервис quickchart.io недоступен.');
        }
}


function DrawErrorImage($string)
{
    //header('Content-Type: image/png');
    // Create the image
    $im = imagecreatetruecolor(600, 30);

    // Create some colors
    $white = imagecolorallocate($im, 255, 255, 255);
    $red = imagecolorallocate($im, 255, 0, 0);

    imagefilledrectangle($im, 0, 0, 599, 29, $white);
    
    $font = 'arial.ttf';
    
    imagettftext($im, 20, 0, 11, 21, $red, $font, $string);
    
    $res = imagepng($im);
    imagedestroy($im);
    
    return $res;
}

function CallGraphAPI($chart_options)
{
    $url = 'https://quickchart.io/chart';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($chart_options));
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

    $response = curl_exec($curl);
    $data = $response;

    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    switch ($httpCode)
    {
        case 200:   $error_status = "200: Success";                                                         return array(true, $data);
        case 400:   $error_status = "400: Bad Request";                                                     break;
        case 404:   $error_status = "404: Not found";                                                       break;
        case 409:   $error_status = "409: The request could not be completed";                              break;
        case 500:   $error_status = "500: servers replied with an error.";                                  break;
        case 502:   $error_status = "502: servers may be down or being upgraded. Try some time later.";     break;
        case 503:   $error_status = "503: service unavailable. Try some time later.";                       break;
        default:    $error_status = "Undocumented error: " . $httpCode . " : " . curl_error($curl);         break;
    }
    curl_close($curl);
    return array(false, $error_status);
}



function CallWeatherAPI($latitude, $longitude)
{
    $url = 'https://api.open-meteo.com/v1/forecast?latitude='.$latitude.'&longitude='.$longitude.'&hourly=temperature_2m&timezone=Asia%2FSingapore&forecast_days=16';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($curl);
    $data = $response;

    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    switch ($httpCode)
    {
        case 200:   $error_status = "200: Success";                                                         return array(true, $data);
        case 400:   $error_status = "400: Bad Request";                                                     break;
        case 404:   $error_status = "404: Not found";                                                       break;
        case 409:   $error_status = "409: The request could not be completed";                              break;
        case 500:   $error_status = "500: servers replied with an error.";                                  break;
        case 502:   $error_status = "502: servers may be down or being upgraded. Try some time later.";     break;
        case 503:   $error_status = "503: service unavailable. Try some time later.";                       break;
        default:    $error_status = "Undocumented error: " . $httpCode . " : " . curl_error($curl);         break;
    }
    curl_close($curl);
    return array(false, $error_status);
}
?>