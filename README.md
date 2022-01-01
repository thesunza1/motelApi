# Api quản lý nhà trọ
## Sử dụng framework Laravel 8
### Cách chạy api.
1. Sao Chép file .env.example sang .env
2. Chạy: 
```bash
composer install
```
 Lưu ý: bạn phải tạo sẳn database và điền  tên database đó vào file .env
3. Chạy trong cmd: 
```bash
php artisan key:generate
```
4. Chạy tạo cơ sở dữ liệu.
```bash
php artisan migrate
```
5. Chạy tạo dữ liệu mẫu.
```bash 
php artisan db:seed
```
6. Chạy server localhost.
```bash 
php artisan serve
```


