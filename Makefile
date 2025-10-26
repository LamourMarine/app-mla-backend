.PHONY: dev prod stop-dev stop-prod

dev:
	sudo docker compose up -d --build

prod:
	sudo docker compose -f docker-compose.prod.yml up -d --build

stop-dev:
	sudo docker compose down

stop-prod:
	sudo docker compose -f docker-compose.prod.yml down

logs-dev:
	sudo docker compose logs -f app

logs-prod:
	sudo docker compose -f docker-compose.prod.yml logs -f app