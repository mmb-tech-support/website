FROM nginx:stable-alpine

# Основной конфиг Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Конфиги виртуальных хостов
COPY conf.d /etc/nginx/conf.d

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
