# 📈 Trading Assignment – Full Stack Application

This project is a simple trading system consisting of a **Laravel backend API** and a **Vue 3 frontend SPA**.  
Both applications run independently and communicate via REST APIs and real-time WebSocket events.


# 📁 Project Structure

```
assignment/
│
├── backend/                  # Laravel 11 API (Auth, Orders, Matching, Events)
│   ├── app/
│   ├── routes/
│   ├── database/
│   ├── config/
│   └── ...
│
└── trading-frontend/         # Vue 3 + Vite + Pinia + Tailwind SPA
    ├── src/
    ├── package.json
    ├── vite.config.js
    └── ...
  
``` 

## 🧩 Tech Stack
### Backend
-   Laravel 11
-   PHP 8.2+ (Required)    
-   MySQL 8+    
-   Laravel Sanctum (API authentication)    
-   Laravel Broadcasting (Reverb / Pusher compatible)    
-   Events & Queues
-   BCMath Extension (required for precise calculations)

### Frontend
-   Vue 3 (Composition API)    
-   Vite    
-   Pinia (State Management)    
-   Tailwind CSS 
-   Axios  
-   Laravel Echo (Realtime updates)

## ⚙️ System Requirements

Make sure your system has:

-   PHP **>= 8.2**    
-   Composer **>= 2.x**   
-   Node.js **>= 18** (Recommended: Node 20)    
-   NPM **>= 9**    
-   MySQL **>= 8.0**    
-   BCMath PHP extension enabled

Check BCMath : 
`php -m | grep bcmath` 


## ⚙️ Backend Setup (Laravel)

### 1️⃣ Go to backend directory

`cd backend` 

### 2️⃣ Install dependencies

`composer install` 

### 3️⃣ Environment setup

`cp .env.example .env `

`php artisan key:generate`

Update `.env` with DB and broadcasting config:

`DB_DATABASE=assignment`

`DB_USERNAME=root`

`DB_PASSWORD=*****`

`BROADCAST_CONNECTION=reverb`

`QUEUE_CONNECTION=database` 

### 4️⃣ Run migrations

`php artisan migrate` 

### 5️⃣ Start Laravel server

`php artisan serve --port=8000` 

API will be available at:

`http://127.0.0.1:8000` 

## 📡 Realtime Broadcasting (Local)

This project uses **Laravel Reverb / Pusher‑compatible broadcasting**.
#### Start Reverb (Laravel 11)
`php artisan reverb:start` 

Events broadcast on:
`private-user.{id}` 

Broadcasted Event:
-   `OrderMatched`

## 🎨 Frontend Setup (Vue 3)

### 1️⃣ Go to frontend directory

`cd trading-frontend` 

----------

### 2️⃣ Install Node.js (if not installed)

> Node **v18+** required

Check version:

`node -v
 npm -v` 

If not installed:

`sudo apt install nodejs npm` 

(Or install via **nvm** – recommended)

----------

### 3️⃣ Install frontend dependencies

`npm install` 

This installs:

-   Vue 3    
-   Vite    
-   Pinia    
-   Axios    
-   Laravel Echo    
-   Pusher JS    
-   Tailwind CSS (if configured)  

----------

### 4️⃣ Create environment file

Create `.env` inside `trading-frontend/`

`touch .env` 

Add:

`VITE_API_BASE_URL=http://127.0.0.1:8000`

`VITE_PUSHER_APP_KEY=local`

`VITE_PUSHER_APP_CLUSTER=mt1` 

`VITE_PUSHER_HOST=127.0.0.1`

`VITE_PUSHER_PORT=6001`

----------

### 5️⃣ Verify `main.js`

Ensure `src/main.js` looks like this:

`import { createApp } from 'vue';`

`import { createPinia } from 'pinia';`

`import App from './App.vue';`

`import './index.css';`

`const app = createApp(App);`

`app.use(createPinia());`

`app.mount('#app');`


----------

### 6️⃣ Start development server

`npm run dev` 

Output:

`Local: http://localhost:5173/` 

----------

### 7️⃣ Open in browser

`http://localhost:5173` 

----------

### 8️⃣ Build for production (optional)

`npm run build` 

Build output:

`trading-frontend/dist/` 

----------

### 9️⃣ Common Issues & Fixes

#### ❌ Blank page

-   Check browser console
    
-   Ensure `App.vue` has a `<template>`
    
-   Ensure API URL is correct
    

#### ❌ Pinia not found

`npm install pinia` 

#### ❌ Tailwind not working

Ensure `index.css` contains:

`@tailwind base;`

`@tailwind components;`

`@tailwind utilities;` 

----------

### 🔁 Realtime Setup (Echo)

Frontend listens to:

`private-user.{userId}` 

Backend fires:

`OrderMatched` 

No extra setup required in frontend once Echo is configured.

----------

## ✅ Frontend Ready

✔ Vue running  
✔ API connected  
✔ Realtime events supported




## 📊 Features Implemented

-   ✅ User authentication (Login / Logout)    
-   ✅ Wallet (USD + Assets)    
-   ✅ Place Buy/Sell Orders    
-   ✅ Cancel Open Orders    
-   ✅ Order Matching Engine (Exact match)    
-   ✅ Trade settlement    
-   ✅ Realtime updates (Orders, Wallet)    
-   ✅ Symbol filtering (BTC / ETH)




