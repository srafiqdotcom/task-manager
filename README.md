

---

# 📝 Task Manager API

A simple RESTful Task Manager API built with **SlimPHP**, **PostgreSQL**, and **Doctrine ORM**, with support for **API key authentication**, **pagination**, and **OpenAPI documentation**.

---

## 🚀 Quick Start

### Prerequisites

- [Docker](https://www.docker.com/products/docker-desktop/)
- [Docker Compose](https://docs.docker.com/compose/)

### 🛠️ Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/task-manager.git
   cd task-manager
   ```

2. **Start the services**
   ```bash
   docker-compose up -d --build
   ```

3. **Run database migrations**
   ```bash
   docker exec -it slimphp_app vendor/bin/phinx migrate
   ```

4. ✅ **Visit the app**
    - API: [http://localhost:8080](http://localhost:8080)
    - Swagger Docs: [http://localhost:8080/docs/ui](http://localhost:8080/docs/ui)

---

## 🔐 Authentication

All endpoints (except `/docs`, `/docs/ui`, `/healthcheck`, and `/`) require an API key via the `X-API-Key` header.

**Sample API Key:**
```bash
X-API-Key: 427bb67d-07f8-4220-a11a-7dfb181e9bdc
```

---

## 📘 API Endpoints

| Method | Endpoint      | Description            |
|--------|---------------|------------------------|
| GET    | /tasks        | List all tasks         |
| GET    | /tasks/{id}   | Get task by ID         |
| POST   | /tasks        | Create a new task      |
| PUT    | /tasks/{id}   | Update a task          |
| DELETE | /tasks/{id}   | Delete a task          |

---

## 🔧 Environment Variables

These are preconfigured in `docker-compose.yml`:

```env
DB_HOST=db
DB_PORT=5432
DB_DATABASE=slim_tasks
DB_USERNAME=postgres
DB_PASSWORD=password
API_KEYS=427bb67d-07f8-4220-a11a-7dfb181e9bdc,ea7853e9-0a36-4b21-beac-860fc94d9680
```

---

## 🧪 Testing the API (with curl)



### ✅ List all tasks

```bash
curl -X GET http://localhost:8080/tasks \
  -H "X-API-Key: 427bb67d-07f8-4220-a11a-7dfb181e9bdc"
```

### 📄 Get a specific task

```bash
curl -X GET http://localhost:8080/tasks/1 \
  -H "X-API-Key: 427bb67d-07f8-4220-a11a-7dfb181e9bdc"
```

### ➕ Create a new task

```bash
curl -X POST http://localhost:8080/tasks \
  -H "Content-Type: application/json" \
  -H "X-API-Key: 427bb67d-07f8-4220-a11a-7dfb181e9bdc" \
  -d '{
    "title": "Learn Docker",
    "description": "Study Docker and Docker Compose",
    "completed": false
  }'
```

### ✏️ Update a task

```bash
curl -X PUT http://localhost:8080/tasks/1 \
  -H "Content-Type: application/json" \
  -H "X-API-Key: 427bb67d-07f8-4220-a11a-7dfb181e9bdc" \
  -d '{
    "completed": true
  }'
```

### ❌ Delete a task

```bash
curl -X DELETE http://localhost:8080/tasks/3 \
  -H "X-API-Key: 427bb67d-07f8-4220-a11a-7dfb181e9bdc"
```

---

Let me know if you want the same examples with `httpie`, `Postman`, or formatted for Swagger UI.

## 🐞 Troubleshooting

- Check logs:
  ```bash
  docker-compose logs app
  ```

- Restart containers:
  ```bash
  docker-compose down -v
  docker-compose up -d --build
  ```

- Re-run migrations if needed:
  ```bash
  docker exec -it slimphp_app vendor/bin/phinx migrate
  ```

---

## 📂 Project Structure

```
.
├── app/                    # DI container, middleware, routes
├── src/                    # Domain, Application, Infrastructure
├── db/                     # Migrations, seeds
├── public/                 # Public assets, index.php
├── vendor/                 # Composer dependencies
├── openapi.yaml            # API specification
├── docker/                 # Docker-specific configs
├── docker-compose.yml
└── Dockerfile
```



Let me know if you want it saved as a file or added to your repo automatically.