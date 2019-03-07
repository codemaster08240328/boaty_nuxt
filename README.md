# SailChecker

https://sailchecker.com/

## Project Description
Frontend Stack: Vue + Nuxt, Wordpress
Backend Stack: ADONIS

This is search engine for Yacht Charter Platform.
Yacht Charter is a passport to the unrivalled riches of our planet; SailChecker is your guide to a collection of carefully selected Charter Yachts, Catamarans, and Motor Yachts; Skippered, Crewed and Bareboat, worldwide.

Our story begins attempting to navigate the bewildering range of yachts, charter, and terminology and a deep culture of high service standard. We are driven by a shared understanding of needing the careful blend of search technology with consistently high levels of customer service to realise the perfect sailing trip with total Peace of Mind.

Our mission is to create your perfect sailing cruise, designed around your tastes, your dreams, and the way you choose to explore, play and relax. By routinely meeting with our partners, inspecting bases, and sailing on their yachts, my team and I can guide you to a tailored match for the kind of experience you have always imagined.

Our Yacht Charter has no standard packages, we have no standard customers. Our collection is curated from bareboat yachts no more than 6 years, and crewed yachts of distinction. We look forward to creating your sailing dreams.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites
Node.js
docker
adonis

```
$ npm i -g @adonisjs/cli
```

### Installing

#### Using Docker

```
$ docker-compose up
```

#### Locally Using NPM

- Frontend
Rename .env.example to .env
```
$ cd /project/src/frontend
$ yarn
$ yarn dev
```

- Backend
Rename .env.example to .env
Database config setting
```
$ cd /project/src/admin
$ yarn
$ adonis migration:run
$ adonis serve --dev
```

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

