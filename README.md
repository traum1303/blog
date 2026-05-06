How to instal

1. docker compose -f docker-compose.yml -p blog up -d
2. docker exec -it blog-app-1 bash -c "composer install"
3. docker exec -it blog-app-1 bash -c "console migrate"
4. docker exec -it blog-app-1 bash -c "console seed"

open http://localhost:8080/
