<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../configs/env.php';
require_once PATH_MODEL . 'UserModel.php';

$users = new UserModel();
if ($users->countAdmins() > 0) {
    fwrite(STDERR, "Da co quan tri vien. Hay dang nhap admin de tao tai khoan tiep theo.\n");
    exit(1);
}

$prompt = static function (string $message): string {
    fwrite(STDOUT, $message);
    $value = fgets(STDIN);

    return $value === false ? '' : trim($value);
};
$fullName = $prompt('Ho va ten quan tri vien: ');
$email = $prompt('Email: ');
$password = $prompt('Mat khau (toi thieu 8 ky tu): ');
$confirmation = $prompt('Nhap lai mat khau: ');

if ($fullName === '' || mb_strlen($fullName) > 150) {
    fwrite(STDERR, "Ho ten khong hop le.\n");
    exit(1);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 191) {
    fwrite(STDERR, "Email khong hop le.\n");
    exit(1);
}
if (strlen($password) < 8 || $password !== $confirmation) {
    fwrite(STDERR, "Mat khau phai co it nhat 8 ky tu va khop nhau.\n");
    exit(1);
}
if ($users->findByEmail($email)) {
    fwrite(STDERR, "Email nay da duoc su dung.\n");
    exit(1);
}

$users->createAccount($fullName, $email, $password, '', 'admin');
fwrite(STDOUT, "Da tao quan tri vien. Hay dang nhap tai index.php?page=login.\n");
