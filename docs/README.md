# Giới thiệu

Project này là module SAAS dùng để quản lí các customer trong hệ thống. Các customer sẽ có 2 loại tài khoản là free user hoặc là premium user.

Tài khoản premium user có thể được nâng cấp lên từ free user bằng cách subscribe 1 plan thông qua cổng thanh toán `Stripe`.

# Tính năng

- Quản lí customer trong hệ thống
- Customer có thể tạo tài khoản, đăng nhập, sử dụng free hoặc nâng lên premium bằng các subscribe premium plan
- Giao diện Subscription cho customer và trang quản lí cho admin
- Gửi email khi đăng kí, quên mật khẩu, subscription, cancel subscription, thanh toán thất bại

# Installing

## Setup Docker
Để chạy được saas trên môi trường develop thì cần sử dụng docker, các file cấu hình đã được cài đặt sẵn trong thư mục dockers.

Để biết thêm về docker thì xem thêm ở đây [documentation](https://docs.docker.com/install/)

Nếu máy đã có docker thì làm những bước bên dưới để cài đặt:

1. Mở terminal ở thư mục gốc của dự án, truy cập vào thư mục docker
   
        cd docker

2. Build containers

        docker-compose build

3. Sau khi docker build xong container, thì start docker bằng lệnh:

        docker-compose up

Build container chỉ làm 1 lần khi mới setup dự án, còn ở các lần sau thì chỉ cần chạy lệnh `docker-compose up`

## Setup Laravel

1. Truy cập vào trong container vào bằng lệnh:

        docker exec -ti saas_app bash

2. Cài đặt packages

        composer install

3. Copy file .env.example -> file .env

4. Generate APP_KEY

        php artisan key:generate

5. Migrate and seed databases

        php artisan migrate --seed

## Setup reCAPTCHA v3

Thêm RECAPTCHAV3_SITEKEY và RECAPTCHAV3_SECRET vào file .env. (Có thể tạo ra ở [đây](https://www.google.com/recaptcha/admin/site/523218037))

Link tham khảo:

- Package Github: [laravel-recaptchav3](https://github.com/josiasmontag/laravel-recaptchav3)
- Lấy SITEKEY và SECRET: [Site Key, Secret Key Google reCAPTCHA](https://www.magetop.com/blog/lay-site-key-secret-key-google-recaptcha/)

Nếu không sử dụng reCAPTCHA thì remove package `josiasmontag/laravel-recaptchav3` trong file compose.json. 

Và tìm kiếm từ khóa `'g-recaptcha-response' => 'recaptchav3:login,0.5'` và xóa đi 

## Setup Mail

- Đăng kí tài 1 khoản email server và điền các tham số cấu hình trong file .env

        MAIL_MAILER=smtp
        MAIL_HOST=mailhog
        MAIL_PORT=1025
        MAIL_USERNAME=null
        MAIL_PASSWORD=null
        MAIL_ENCRYPTION=null
        MAIL_FROM_ADDRESS="hello@example.com"
        MAIL_FROM_NAME="${APP_NAME}"

## Setup Stripe

- Tham khảo file setup-stripe.docx để biết cách setup

# Other Documents
## Database

Các thông tin về database nằm trong thư mục databases

Nếu muốn xem diagram của database thì có thể mở file Diagram.png hoặc mở file Diagram.mwb bằng workbench trong thư mục Databases.

File PHP saas - DB specifications.docx miêu tả chi tiết các thông tin của các table

## Code Flow

Mở file flow-drawio bằng trang web https://app.diagrams.net/ để có thể chỉnh sửa, hoặc mở file PHP - Saas - Flow.drawio.png để xem dưới dạng ảnh

## Api

Project có build sẵn 1 bộ api để dùng cho fronted framework như: angular, react, vue...

Các Api này được đặt trong thư mục modules/Api

Document của các api này có thể tham khảo ở đường link dưới hoặc import file PHP-SAAS.postman_collection.json vào postman để testing.
https://documenter.getpostman.com/view/10447658/2s83ziP41k

