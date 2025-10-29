<?php
// Запрет прямого доступа к файлу
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
    exit;
}

// Установка заголовков
header('Content-Type: application/json; charset=utf-8');

// Получение данных из формы
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Валидация данных
$errors = [];

if (empty($name)) {
    $errors[] = 'Пожалуйста, укажите ваше имя';
}

if (empty($phone)) {
    $errors[] = 'Пожалуйста, укажите номер телефона';
}

if (empty($email)) {
    $errors[] = 'Пожалуйста, укажите ваш email';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Пожалуйста, укажите корректный email';
}

// Если есть ошибки, возвращаем их
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit;
}

// Настройка email
$to = 'Bess-vadim96@mail.ru';
$subject = 'Новая заявка с сайта';

// Формирование тела письма
$email_body = "Новая заявка с сайта\n\n";
$email_body .= "Имя: " . $name . "\n";
$email_body .= "Телефон: " . $phone . "\n";
$email_body .= "Email: " . $email . "\n";
$email_body .= "Сообщение: " . ($message ? $message : 'Не указано') . "\n";
$email_body .= "\n---\n";
$email_body .= "Дата отправки: " . date('d.m.Y H:i:s') . "\n";
$email_body .= "IP адрес: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Настройка заголовков письма
$headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=utf-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Отправка письма
$mail_sent = @mail($to, $subject, $email_body, $headers);

if ($mail_sent) {
    echo json_encode([
        'success' => true,
        'message' => 'Спасибо! Ваша заявка успешно отправлена. Мы свяжемся с вами в ближайшее время.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'К сожалению, произошла ошибка при отправке сообщения. Пожалуйста, попробуйте позже или свяжитесь с нами по телефону.'
    ]);
}
?>
