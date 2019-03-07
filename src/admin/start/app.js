'use strict'

/*
|--------------------------------------------------------------------------
| Providers
|--------------------------------------------------------------------------
|
| Providers are building blocks for your Adonis app. Anytime you install
| a new Adonis specific package, chances are you will register the
| provider here.
|
*/
const providers = [
  '@adonisjs/framework/providers/AppProvider',
  '@adonisjs/framework/providers/ViewProvider',
  '@adonisjs/lucid/providers/LucidProvider',
  '@adonisjs/bodyparser/providers/BodyParserProvider',
  '@adonisjs/cors/providers/CorsProvider',
  '@adonisjs/shield/providers/ShieldProvider',
  '@adonisjs/session/providers/SessionProvider',
  '@adonisjs/auth/providers/AuthProvider',
  '@adonisjs/drive/providers/DriveProvider',
  '@adonisjs/mail/providers/MailProvider',
  '@adonisjs/redis/providers/RedisProvider',
  '@adonisjs/vow/providers/VowProvider',
  '@adonisjs/lucid-slugify/providers/SlugifyProvider',
  '@adonisjs/antl/providers/AntlProvider',
  '@adonisjs/validator/providers/ValidatorProvider',
  '@adonisjs/ally/providers/AllyProvider'
]

/*
|--------------------------------------------------------------------------
| Ace Providers
|--------------------------------------------------------------------------
|
| Ace providers are required only when running ace commands. For example
| Providers for migrations, tests etc.
|
*/
const aceProviders = [
  '@adonisjs/lucid/providers/MigrationsProvider'
]

/*
|--------------------------------------------------------------------------
| Aliases
|--------------------------------------------------------------------------
|
| Aliases are short unique names for IoC container bindings. You are free
| to create your own aliases.
|
| For example:
|   { Route: 'Adonis/Src/Route' }
|
*/
const aliases = {}

/*
|--------------------------------------------------------------------------
| Commands
|--------------------------------------------------------------------------
|
| Here you store ace commands for your package
|
*/
const commands = [
  'App/Commands/NuxtBuild',
  'App/Commands/Bbapi',
  'App/Commands/getBoatInfo',
  'App/Commands/getAvailabilities',
  'App/Commands/updateHistoricAvailabilities',
  'App/Commands/getBoatImages',
  'App/Commands/WP',
  'App/Commands/searchParams',
  'App/Commands/clientData',
  'App/Commands/SyncMmk',
  'App/Commands/CalculateMmkCounters',
  'App/Commands/FillMissingMmkData'
]

module.exports = {
  providers,
  aceProviders,
  aliases,
  commands
}
