# BeyondChats – Full-Stack Assignment Submission

For now script is not tested due to API unavilablity,
Backend Scraping and CRUD APIs work and Frontend Works

---

## 📌 Project How to start
- cd backend
- docker compose up --build 
- docker compose exec app php artisan migrate                            
- docker compose exec app php artisan scrape:beyondchats (to initiate scraper)

- cd frontend
- npm install
- npm run dev

---

The scraper fetches the main blog listing page:
https://beyondchats.com/blogs
It scans all anchor tags containing /blogs/page/{n}/
Using a regular expression, it extracts page numbers
The maximum page number is selected as the starting point

---

## 📁 Repository Structure

├── backend/ # Laravel API + scraping command
├── node-worker/ # Node.js AI automation script
├── frontend/ # React frontend (Vite)
├── docker-compose.yml
└── README.md

## 🔌 API Overview

Get all articles
GET /api/articles

Get single article (with AI version)
GET /api/articles/{id}

Store / update AI article
POST /api/articles/{id}/ai
