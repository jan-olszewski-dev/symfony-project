FROM node

ARG WORK_DIR=/var/www/html

WORKDIR ${WORK_DIR}

COPY app/package*.json .
RUN npm install --quiet
COPY app/. .
