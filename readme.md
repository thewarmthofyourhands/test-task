### Local START
запускать из основной паки
````
docker compose up -d
docker compose exec api php ./bin/console db.migrations.migrate
docker compose exec api php ./bin/console minio.buckets.init
docker compose exec api php public/index.php start
````

### Local Tests
````
docker compose exec api ./vendor/phpunit/phpunit/phpunit ./tests/
````

### FRONT
Перед использованием нужно миграции и тесты запустить, они подготовят окружение  
http://localhost/api-doc.html -- api doc  
http://localhost/tickets.html -- список заявок в админке (токен автоматом подставляется)  
http://localhost/widget.html -- виджет  
http://localhost/ -- пример встроенное виджета  

### RPS Tests
Если пк сильный, то можно рейты повысить. Основной летенси формируется из-за бд и haproxy.  
Если сделать пустую апи ручку, то есть голый фреймворк и парсинг параметров, то 18 тысяч rps  

Чистый запрос пустышка
````
import http from "k6/http";

export const options = {
  discardResponseBodies: true,
  scenarios: {
    rps: {
      executor: "constant-arrival-rate",
      rate: 14000,
      timeUnit: "1s",
      duration: "3s",
      preAllocatedVUs: 300,
      maxVUs: 5000,
    },
  },
  thresholds: { dropped_iterations: ["count==0"],
    http_req_failed: ["rate<0.001"],
  },
};

export default function () {
  const res = http.get("http://127.0.0.1/api/welcome", {
    headers: { Authorization: "token12345" },
  });
}
K6
````

С запросами в бд
````
docker run --rm -i --ulimit nofile=100000:100000 --network=host grafana/k6 run - <<'K6'
import http from "k6/http";

export const options = {
  discardResponseBodies: true,
  scenarios: {
    rps: {
      executor: "constant-arrival-rate",
      rate: 4000,
      timeUnit: "1s",
      duration: "3s",
      preAllocatedVUs: 300,
      maxVUs: 5000,
    },
  },
  thresholds: { dropped_iterations: ["count==0"],
    http_req_failed: ["rate<0.001"],
  },
};

export default function () {
  const res = http.get("http://127.0.0.1/api/tickets", {
    headers: { Authorization: "token12345" },
  });
}
K6
````
