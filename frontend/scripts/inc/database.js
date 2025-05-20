import Importer from 'mysql-import'
import SQL from 'sql-template-strings'
import mysql from 'mysql2/promise'
import log from '#inc/logger.js'
import ProgressBar from 'progress'
import checkEnv from '#inc/check-env.js'

checkEnv(["MARIADB_HOST", "MARIADB_PORT", "MARIADB_USER", "MARIADB_PASSWORD", "MARIADB_DATABASE"])

const creds = {
	host: process.env.MARIADB_HOST,
	port: process.env.MARIADB_PORT,
	user: process.env.MARIADB_USER,
	password: process.env.MARIADB_PASSWORD,
	database: process.env.MARIADB_DATABASE
}

export const connectionPool = await createAndVerifyPool({
	...creds,
	waitForConnections: true,
	connectionLimit: 10,
	queueLimit: 0
})

async function createAndVerifyPool(config) {
	const pool = mysql.createPool(config)
	try {
		// Test the connection
		const connection = await pool.getConnection()
		await connection.ping() // Simple query to verify connectivity
		connection.release()
		log.succ('✅ Успешное подключение к БД');
		return pool
	} catch (err) {
		await pool.end() // Close the pool if credentials are bad
		lo.fatal('❌ Ошибка подключения к БД:');
	}
}

export async function checkDB() {
	let succ = true
	for (let table of ['1chan_category', '1chan_comment', '1chan_post']) {
		const results = await connectionPool.query(SQL`SHOW TABLES LIKE ${table}`)
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