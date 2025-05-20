import '#inc/greet.js'
import { CronJob } from 'cron'
import { updateServerStatus } from '#inc/server-status.js'
import { indexerInit, runIndexer, checkDaemon } from '#inc/sphinx.js'
import log from '#inc/logger.js'
import { updateSmilies, watchSmilies } from '#inc/smilies.js'
import checkEnv from '#inc/check-env.js'
import fastify from "fastify"

checkEnv(['SRV_HOST', 'SRV_PORT', 'SRV_LOCAL_HOST'])

// Запуск индексатора
await indexerInit()
const searchdStatus = await checkDaemon()
const IndexerJob = new CronJob(process.env.INDEXER_SCHEDULE, runIndexer, null, true)
log.succ('Индексатор запущен')

// Запуск проверялки статуса серверов
const ServerStatusJob = new CronJob(process.env.SERVER_STATUS_SCHEDULE, updateServerStatus, null, true)
log.succ('Сервис проверки статуса серверов запущен')

// Проверка смайликов
await updateSmilies()
watchSmilies()

const app = fastify()
// Подключение плагина Socket.IO
app.register(import('#inc/broadcast.js'))
if (process.env?.TG_ENABLE == true) {
	// Подключение плагина Telegram
	app.register(import('#inc/telegram.js'))
}
// Запуск сервера
app.listen({ host: process.env.SRV_HOST, port: process.env.SRV_PORT })
log.succ(`Сервер Node.JS @ ${process.env.SRV_HOST}:${process.env.SRV_PORT} принимает сообщения с локального адреса ${process.env.SRV_LOCAL_HOST}`)
