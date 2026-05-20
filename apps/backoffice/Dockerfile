FROM node:20-alpine

WORKDIR /app

COPY package*.json ./

RUN npm install

EXPOSE 7032

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "7032"]
