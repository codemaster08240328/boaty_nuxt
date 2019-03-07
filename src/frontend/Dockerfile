FROM node:8.10.0

# Install node modules
RUN npm install -g nodemon

# Create app directory
RUN mkdir -p /usr/src/app/frontend
WORKDIR /usr/src/app/frontend

ONBUILD RUN npm install

ENV HOST 0.0.0.0
# Install app dependencies
CMD [ "npm", "run", "buildStart"]
