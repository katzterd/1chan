import Importer from 'mysql-import'
import SQL from 'sql-template-strings'
import mysql from 'mysql2/promise'
import log from '#inc/logger.js'
import ProgressBar from 'progress'
import checkEnv from '#inc/check-env.js'

checkEnv(["SQL_HOST", "SQL_PORT", "SQL_USER", "SQL_PASSWORD", "SQL_NAME"])

const creds = {
	host: process.env.SQL_HOST,
	port: process.env.SQL_PORT,
	user: process.env.SQL_USER,
	password: process.env.SQL_PASSWORD,
	database: process.env.SQL_NAME
}

let connection = null
const sqlConnection = async () => {
	if (connection) return connection
	try {
		connection = await mysql.createConnection(creds)
		return connection
	}
	catch (e) {
		log.fatal(`Не удалось подключиться к базе данных`)
	}
}

export async function checkDB() {
	let succ = true
	const conn = await sqlConnection()
	for (let table of ['1chan_category', '1chan_comment', '1chan_post']) {
		const results = await conn.query(SQL`SHOW TABLES LIKE ${table}`)
		if (results[0].length) {
			log.info(`Таблица "${table}" существует`)
		}
		else {
			log.err(`Таблицы "${table}" не существует`)
			succ = false
		}
	}
	return succ
}

export function dumpSQL() {
	return new Promise(async resolve => {
		const importer = new Importer(creds)
		const bar = new ProgressBar('Импорт SQL [:bar] :percent', {
			complete: '=',
			incomplete: ' ',
			width: 20,
			total: 100
		})
		importer.onProgress(progress => {
			const percent = Math.floor(progress.bytes_processed / progress.total_bytes * 10000) / 100
			bar.tick(percent)
		})
		importer.import('dump.sql').then(() => {
			log.succ('Таблицы импортированы')
			resolve('OK')
		})
		.catch(e => {
			console.error(e)
			log.fatal('Ошибка при импорте SQL')
		})
	})
}