import '#inc/greet.js'
import { CronJob } from 'cron'
import { updateServerStatus } from '#inc/server-status.js'
import { indexerInit, runIndexer, checkDaemon } from '#inc/sphinx.js'
import { listen } from '#inc/broadcast.js'
import log from '#inc/logger.js'
import { updateSmilies, watchSmilies } from '#inc/smilies.js'

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

listen()