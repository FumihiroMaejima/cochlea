FROM node:16.14-alpine

ENV NODE_ENV=production
ENV HOST 0.0.0.0

EXPOSE 3000

WORKDIR /frontend

ADD ../next/package.json ./
RUN yarn install

COPY ../next .
RUN yarn build

# # yarn install
# COPY ./next/package.json ./
# RUN yarn install
#
# # build
# COPY ./next/ ./
# RUN yarn build


CMD ["yarn", "start"]
