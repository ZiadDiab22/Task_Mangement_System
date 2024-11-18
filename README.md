# Task Management System

## Overview

This is a Laravel-based API for a task management system. It provides endpoints for user authentication, task creation, retrieval, updating, deletion, and completion.

## Features

- User registration and login
- Task CRUD operations (create, read, update, delete)
- Task completion functionality
- Filtering tasks

## API Endpoints

### Authentication
- POST /api/register - Register a new user
- POST /api/login - Login existing user

### Tasks
- GET /api/showTasks - Get all user tasks
- POST /api/addTask - Create new task - This is done by sending the task information in the request body (title is required , due_date and description are optional)
- POST /api/editTask - Update existing task - This is done by sending id and information to be modified in the request body.
- GET /api/deleteTask/{id} - Delete task
- GET /api/completeTask/{id} - Mark task as complete
- POST /api/filterTasks - Filter tasks by status - This is done by sending a completed:1 or 0 to show the completed or uncompleted tasks, and tasks are ordered in ascending by due_date.

- ## Setup and Run project
- Clone the repository - git clone https://github.com/ZiadDiab22/Task_Mangement_System.git
- Navigate to the project directory and set up the environment - cp .env.example .env - php artisan key:generate
- Install Composer dependencies - composer install
- Install npm dependencies - npm install
- Run migrations - php artisan migrate
- Start the development server - php artisan serve
- Open Postman and import collection to test the api's
