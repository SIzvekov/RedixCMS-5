<?
$err_code['status'] = false;
$err_code['message'] = "Заявка не отправлена!";
$check_name = false;
$check_phone = false;
$check_cap = false;
$phone = "";
$this->kapcha_field_name = isset($_POST['code1']) ? 'code1' : 'code';

if (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "callback") {
    /*проаерка имени*/
    if (isset($_POST['name']) && !empty($_POST['name'])) {
        $name = trim($_POST['name']);
        $check_name = true;
    } else {
        $err_code['name_status'] = "Введите имя";
    }
    /*проверка телефона*/
    $phone = preg_replace('/\D*/', '', $_POST['phone']);
    if (isset($_POST['phone']) && !empty($_POST['phone'])) {
        $phone = $_POST['phone'];
        $check_phone = true;
    } else {
        $err_code['phone_status'] = "Введите правильный телефонный номер";
    }
    /*проверка капчи*/
    if ($this->form_check_kapcha()) {
        $check_cap = true;
    } else {
        $err_code['capcha_status'] = 'Введите правильный код с картинки';
    }
    if ($check_cap && $check_phone && $check_name) {
        //$to  = 'tolstousova.t@gmail.com';
        $to = 'limuzin-ural@mail.ru';

        $subj = "Заказ обратного звонка";
        /* сообщение */
        $message = '<html>
                    <head>
                     <title>' . $subj . '</title>
                    </head>
                    <body>
                    <p>' . $subj . '</p>
                    <table>
                     <tr>
                    <td>Имя: </td><td>' . $name . '</td>
                     </tr>
                     <tr>
                    <td>Телефон: </td><td>' . $phone . '</td>
                     </tr>
                    </table>
                    </body>
                    </html>
                    ';

        /* Для отправки HTML-почты вы можете установить шапку Content-type. */
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        /* дополнительные шапки */
        $headers .= "From: <site@limo66.ru>\r\n";

        /* и теперь отправим из */
        mail($to, $subj, $message, $headers);

        $err_code['status'] = true;
        die(json_encode(array('status' => true, 'message' => "Заявка отправлена!")));
    }
    if ($err_code['status'] == false) {
        die(json_encode($err_code));
    }

}elseif (isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "addZ") {
    
    /*проаерка имени*/
    if (isset($_POST['name']) && !empty($_POST['name'])) {
        $name = trim($_POST['name']);
        $check_name = true;
    } else {
        $err_code['name_status'] = "Введите имя";
    }
    
    $phone = preg_replace('/\D*/', '', $_POST['phone']);
    //preg_replace('/\+[7]\([0-9]{3}\)[0-9]{3}.[0-9]{2}.[0-9]{2}/is',$_POST['phone'], $phone);
    
    /*проверка телефона*/
    if (isset($_POST['phone']) && !empty($_POST['phone'])) {
        $phone = $_POST['phone'];
        $check_phone = true;
    } else {
        $err_code['phone_status'] = "Введите правильный телефонный номер";
    }
    
    /*проверка капчи*/
    if ($this->form_check_kapcha()) {
        $check_cap = true;
    } else {
        $err_code['capcha_status'] = 'Введите правильный код с картинки';
    }

    if ($check_cap && $check_phone && $check_name) {
        $extra = "";
        if(isset($_POST['from']) || isset($_POST['to']) || isset($_POST['order_date']) || isset($_POST['from_location']) || isset($_POST['to_location'])) {
            $extra = "
            <tr>
            <td>Дата заказа</td>
            <td>{$_POST['order_date']}</td>
            </tr>
            <tr>
            <td>С</td>
            <td>{$_POST['from']}</td>
            </tr>
            <tr>
            <td>По</td>
            <td>{$_POST['to']}</td>
            </tr>
            <tr>
            <td>Адрес начала поездки</td>
            <td>{$_POST['from_location']}</td>
            </tr>
            <tr>
            <td>Адрес окончания поездки</td>
            <td>{$_POST['to_location']}</td>
            </tr>
            <tr>
            <td>Стоимость аренды:</td>
            <td>{$_POST['price']}</td>
            </tr>
            <tr>
            <td>Стоимость аренды со скидкой:</td>
            <td>{$_POST['discount_price']}</td>
            </tr>
            ";

        }
        
        //$to  = 'tolstousova.t@gmail.com';
        $to = 'limuzin-ural@mail.ru';

        $subj = "Заказ лимузина: " . $_POST['lim_name'];
        /* сообщение */
        $message = '<html>
                    <head>
                     <title>' . $subj . '</title>
                    </head>
                    <body>
                    <p>' . $subj . '</p>
                    <table>
                     <tr>
                    <td>Имя: </td><td>' . $name . '</td>
                     </tr>
                     <tr>
                    <td>Телефон: </td><td>' . $phone . '</td>
                     </tr>
                     '. $extra . '
                    </table>
                    </body>
                    </html>
                    ';

        /* Для отправки HTML-почты вы можете установить шапку Content-type. */
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        /* дополнительные шапки */
        $headers .= "From: <site@limo66.ru>\r\n";

        /* и теперь отправим из */
        mail($to, $subj, $message, $headers);

        $err_code['status'] = true;
        die(json_encode(array('status' => true, 'message' => "Заявка отправлена!")));
    }
    if ($err_code['status'] == false) die(json_encode($err_code));
} else {
    die(json_encode(array('status' => false, 'message' => "Заявка не отправлена!")));
}
