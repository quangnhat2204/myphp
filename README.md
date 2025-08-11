# Laravel CRUD Project

A simple CRUD (Create, Read, Update, Delete) project built with PHP and Laravel. This project manages two main resources: Users and Posts.

## About The Project

This project serves as an example of how to build a modern web application with Laravel, including setting up a development environment using Docker.

### Built With

- **Backend**: PHP 8.4.11, Laravel 12.0
- **Dependency Management**: Composer
- **ORM**: Eloquent ORM
- **Containerization**: Docker, Docker-compose

---

## Getting Started

You can run this project in two ways: directly on your local machine or using Docker (recommended).

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM (optional, for frontend asset customization)
- Docker and Docker Compose (for the Docker-based setup)

---

## Installation and Setup

Clone the repository:
```bash
git clone https://github.com/quangnhat2204/myphp.git
cd myphp
```

### Option 1: Local Machine Setup (Without Docker)

1.  **Install dependencies:**
    ```bash
    composer install
    ```

2.  **Create the environment file:**
    Copy the `.env.example` file to `.env`.
    ```bash
    cp .env.example .env
    ```

3.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```

4.  **Configure your database in the `.env` file:**
    Open the `.env` file and update the database connection details:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=myphp
    DB_USERNAME=root
    DB_PASSWORD=
    ```
5.  **Run the database migrations:**
    This command will create the necessary tables in your database.
    ```bash
    php artisan migrate
    ```

6.  **Start the development server:**
    ```bash
    php artisan serve
    ```
    The application will be available at `http://localhost:8000`.


### Option 2: Docker Compose Setup (Recommended)

This is the easiest way to get the project running without installing PHP or MySQL directly on your machine.

1.  **Install dependencies:**
    ```bash
    composer install
    ```
2.  **Create the environment file:**
    Copy the `.env.example` file to `.env`. The provided `.env` is pre-configured to work with Docker.
    ```bash
    cp .env.example .env
    ```
3.  **Build and start the containers:**
    This command will automatically build the images and start the PHP, Nginx, and MySQL containers.
    ```bash
    docker-compose up -d --build
    ```
4.  **Access the application:**
    Once the containers are up and running, the application will be accessible at:
    [http://localhost:8000](http://localhost:8000)

    **Note:** The startup script is configured to automatically run `php artisan migrate` for you.


---

## Running Tests

To run the test suite (Unit and Feature tests), you can use the following command (if using Docker):

```bash
docker-compose exec app php artisan test
```

Or, if you are running the project on your local machine:

```bash
php artisan test
```



#
#
# Trả lời câu hỏi còn nợ:
### Câu trả lời lúc interview:
Dùng lock database transaction để xử lý asynchronous lúc users claim giftcodes
-> Điểm yếu: bị tải lên database (quá nhiều connections đợi lock)

### Câu trả lời cuối cùng:
Tích hợp queue vào logic xử lý để chuyển thời gian đợi ở db về server. Như vậy sẽ không phải tốn connections với transaction của db. Nhưng scale sẽ khó khăn, vì là queue local nên chỉ có thể scale vertical.

Nếu muốn scale thành nhiều instances thì bắt buộc phải lock hoặc tập trung tại một nơi nào đó. Ở đây em suy nghĩ tới rpc queue (RabbitMQ,...), sẽ đảm bảo được thứ tự thực thi, số lượng connections database không bị quá tải và có thể chạy đồng thời nhiều instances. Đánh đổi là implementation sẽ phức tạp hơn.

Một cố gắng nữa là em sẽ dùng redis để cache available codes, sau khi user đợi xong lock sẽ đọc vào cache trước để tìm code, nếu không thấy sẽ skip qua user đó và thực thi tiếp theo. Điều này sẽ giảm được số lượng đọc và lock database. Trường hợp bị miss cache (code đã claim nhưng remove ra khỏi cache bị lỗi), user search và thấy code bình thường nhưng tới db sẽ bị reject.