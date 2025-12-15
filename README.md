# Symfony + React.js Challenge

A modern web application built with Symfony 7.x (PHP 8.4), React 19 + TypeScript, and Tailwind CSS v4.

## Prerequisites

- Docker
- Docker Compose

## Quick Start

1. **Clone the repository**
   ```bash
   git clone https://github.com/gadius/symfony-reactjs-challenge.git
   ```

2. **Start the application**
   ```bash
   docker-compose up -d
   ```

3. **Access the application**
   - Backend API: http://localhost:8080/api/sum
   - Frontend: http://localhost:5173

4. **Install frontend dependencies**
   ```bash
   cd frontend
   npm install
   npm run dev
   ```

5. **Run backend tests**
   ```bash
   docker-compose exec php php bin/phpunit
   ```
   
   For detailed test output:
   ```bash
   docker-compose exec php php bin/phpunit --testdox
   ```

## API Endpoints

### POST /api/sum
Calculate the sum of two numbers.

**Request Body:**
```json
{
  "a": 5,
  "b": 3
}
```

**Response:**
```json
{
  "sum": 8
}
```

## Project Structure

```
code/
├── frontend/                 # React frontend application
│   ├── src/
│   │   ├── components/       # Reusable React components
│   │   │   ├── NumberInput.tsx
│   │   │   ├── ResultDisplay.tsx
│   │   │   ├── ErrorDisplay.tsx
│   │   │   └── CalculateButton.tsx
│   │   ├── App.tsx           # Main application component
│   │   └── main.tsx          # Application entry point
│   ├── package.json          # Frontend dependencies
│   └── vite.config.ts        # Vite configuration
├── nginx/
│   └── default.conf          # Nginx configuration
├── src/
│   └── Controller/
│       └── SumController.php # API endpoint controller
├── tests/
│   └── Controller/
│       └── SumControllerTest.php  # PHPUnit tests
├── Dockerfile                # PHP-FPM setup
├── docker-compose.yml        # Orchestrates Nginx + PHP-FPM
└── composer.json             # Backend dependencies
```

## Technologies

### Backend
- **Framework:** Symfony 7.x
- **Runtime:** PHP 8.4-FPM
- **Validation:** Symfony Validator Component
- **Testing:** PHPUnit 12.5
- **CORS:** NelmioCorsBundle

### Frontend
- **Framework:** React 19.2.0
- **Language:** TypeScript 5.9
- **Build Tool:** Vite 6
- **Styling:** Tailwind CSS v4
- **HTTP Client:** Native Fetch API

### Infrastructure
- **Web Server:** Nginx (Alpine)
- **Containerization:** Docker & Docker Compose
- **Architecture:** Nginx + PHP-FPM microservices